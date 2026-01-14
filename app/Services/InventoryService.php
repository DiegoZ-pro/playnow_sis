<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InventoryService
{
    /**
     * Crear un nuevo producto con sus variantes
     */
    public function createProduct(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            // Crear producto principal
            $product = Product::create([
                'category_id' => $data['category_id'],
                'brand_id' => $data['brand_id'],
                'name' => $data['name'],
                'slug' => Str::slug($data['name']),
                'description' => $data['description'] ?? null,
                'base_price' => $data['base_price'],
                'featured' => $data['featured'] ?? false,
                'active' => $data['active'] ?? true,
            ]);

            // Crear variantes si se proporcionan
            if (isset($data['variants']) && is_array($data['variants'])) {
                foreach ($data['variants'] as $variant) {
                    $this->createVariant($product->id, $variant);
                }
            }

            return $product->load(['category', 'brand', 'variants']);
        });
    }

    /**
     * Actualizar un producto existente
     */
    public function updateProduct(int $productId, array $data): Product
    {
        $product = Product::findOrFail($productId);

        $product->update([
            'category_id' => $data['category_id'] ?? $product->category_id,
            'brand_id' => $data['brand_id'] ?? $product->brand_id,
            'name' => $data['name'] ?? $product->name,
            'slug' => isset($data['name']) ? Str::slug($data['name']) : $product->slug,
            'description' => $data['description'] ?? $product->description,
            'base_price' => $data['base_price'] ?? $product->base_price,
            'featured' => $data['featured'] ?? $product->featured,
            'active' => $data['active'] ?? $product->active,
        ]);

        return $product->fresh(['category', 'brand', 'variants']);
    }

    /**
     * Crear una variante de producto
     */
    public function createVariant(int $productId, array $data): ProductVariant
    {
        $product = Product::findOrFail($productId);

        // Generar SKU único
        $sku = $this->generateSKU($product, $data['size_id'], $data['color_id']);

        return ProductVariant::create([
            'product_id' => $productId,
            'size_id' => $data['size_id'],
            'color_id' => $data['color_id'],
            'sku' => $sku,
            'price' => $data['price'] ?? $product->base_price,
            'stock' => $data['stock'] ?? 0,
            'low_stock_threshold' => $data['low_stock_threshold'] ?? 5,
            'active' => $data['active'] ?? true,
        ]);
    }

    /**
     * Actualizar una variante de producto
     */
    public function updateVariant(int $variantId, array $data): ProductVariant
    {
        $variant = ProductVariant::findOrFail($variantId);

        $variant->update([
            'price' => $data['price'] ?? $variant->price,
            'stock' => $data['stock'] ?? $variant->stock,
            'low_stock_threshold' => $data['low_stock_threshold'] ?? $variant->low_stock_threshold,
            'active' => $data['active'] ?? $variant->active,
        ]);

        return $variant->fresh(['product', 'size', 'color']);
    }

    /**
     * Ajustar stock de una variante
     */
    public function adjustStock(int $variantId, int $quantity, string $type, ?int $userId = null, ?string $reference = null, ?string $notes = null): StockMovement
    {
        return DB::transaction(function () use ($variantId, $quantity, $type, $userId, $reference, $notes) {
            $variant = ProductVariant::lockForUpdate()->findOrFail($variantId);
            
            $previousStock = $variant->stock;
            $newStock = $previousStock;

            switch ($type) {
                case 'entrada':
                    $newStock = $previousStock + abs($quantity);
                    break;
                case 'salida':
                    $newStock = $previousStock - abs($quantity);
                    if ($newStock < 0) {
                        throw new \Exception('Stock insuficiente');
                    }
                    break;
                case 'ajuste':
                    $newStock = $quantity;
                    break;
            }

            // Actualizar stock
            $variant->update(['stock' => $newStock]);

            // Registrar movimiento
            $movement = StockMovement::create([
                'product_variant_id' => $variantId,
                'user_id' => $userId,
                'type' => $type,
                'quantity' => abs($quantity),
                'previous_stock' => $previousStock,
                'new_stock' => $newStock,
                'reference' => $reference,
                'notes' => $notes,
            ]);

            return $movement;
        });
    }

    /**
     * Reducir stock (para ventas)
     */
    public function reduceStock(int $variantId, int $quantity, ?int $userId = null, ?string $reference = null): StockMovement
    {
        return $this->adjustStock($variantId, $quantity, 'salida', $userId, $reference, 'Reducción por venta');
    }

    /**
     * Aumentar stock (para entradas)
     */
    public function increaseStock(int $variantId, int $quantity, ?int $userId = null, ?string $reference = null, ?string $notes = null): StockMovement
    {
        return $this->adjustStock($variantId, $quantity, 'entrada', $userId, $reference, $notes ?? 'Entrada de stock');
    }

    /**
     * Obtener productos con stock bajo
     */
    public function getLowStockProducts()
    {
        return ProductVariant::with(['product', 'size', 'color'])
            ->lowStock()
            ->active()
            ->get();
    }

    /**
     * Verificar disponibilidad de stock
     */
    public function checkStockAvailability(int $variantId, int $quantity): bool
    {
        $variant = ProductVariant::find($variantId);
        
        if (!$variant) {
            return false;
        }

        return $variant->hasStock($quantity);
    }

    /**
     * Generar SKU único
     */
    private function generateSKU(Product $product, int $sizeId, int $colorId): string
    {
        $prefix = strtoupper(substr($product->category->slug, 0, 3));
        $brandCode = strtoupper(substr($product->brand->slug, 0, 3));
        $productCode = str_pad($product->id, 4, '0', STR_PAD_LEFT);
        
        return $prefix . '-' . $brandCode . '-' . $productCode . '-' . $sizeId . '-' . $colorId;
    }

    /**
     * Eliminar producto (soft delete)
     */
    public function deleteProduct(int $productId): bool
    {
        $product = Product::findOrFail($productId);
        
        // Desactivar producto y sus variantes
        $product->update(['active' => false]);
        $product->variants()->update(['active' => false]);
        
        return true;
    }

    /**
     * Agregar variante a un producto
     */
    public function addVariant(int $productId, int $sizeId, int $colorId, int $stock = 0, ?float $price = null): ProductVariant
    {
        $product = Product::findOrFail($productId);
        
        // Verificar si ya existe la combinación
        $existing = ProductVariant::where('product_id', $productId)
            ->where('size_id', $sizeId)
            ->where('color_id', $colorId)
            ->first();
            
        if ($existing) {
            throw new \Exception('Ya existe una variante con esta combinación de talla y color');
        }
        
        // Generar SKU
        $sku = $this->generateSKU($product, $sizeId, $colorId);
        
        // Crear variante
        $variant = ProductVariant::create([
            'product_id' => $productId,
            'size_id' => $sizeId,
            'color_id' => $colorId,
            'sku' => $sku,
            'stock' => $stock,
            'price' => $price,
            'active' => true,
        ]);
        
        return $variant;
    }
}