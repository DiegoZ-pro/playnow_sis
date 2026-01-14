<?php

namespace App\Http\Controllers\Sistema;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Services\InventoryService;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Size;
use App\Models\Color;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Listar productos
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand', 'variants']);

        // Filtros
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('active')) {
            $query->where('active', $request->active);
        }

        $products = $query->latest()->paginate(20);
        $categories = Category::active()->get();
        $brands = Brand::active()->get();

        return view('sistema.inventory.index', compact('products', 'categories', 'brands'));
    }

    /**
     * Mostrar formulario de crear producto
     */
    public function create()
    {
        $categories = Category::active()->get();
        $brands = Brand::active()->get();
        $colors = Color::active()->get();
        $sizes = Size::active()->ordered()->get();

        return view('sistema.inventory.create', compact('categories', 'brands', 'colors', 'sizes'));
    }

    /**
     * Guardar nuevo producto
     */
    public function store(ProductRequest $request)
    {
        try {
            $productData = $request->validated();
            $product = $this->inventoryService->createProduct($productData);

            // Crear variantes si se enviaron
            $variantsCreated = 0;
            $variantsErrors = [];
            
            if ($request->has('variants') && is_array($request->variants)) {
                foreach ($request->variants as $index => $variantData) {
                    if (!empty($variantData['size_id']) && !empty($variantData['color_id'])) {
                        try {
                            $this->inventoryService->addVariant(
                                $product->id,
                                $variantData['size_id'],
                                $variantData['color_id'],
                                $variantData['stock'] ?? 0,
                                $variantData['price'] ?? null
                            );
                            $variantsCreated++;
                        } catch (\Exception $e) {
                            // Guardar error pero continuar con las demás variantes
                            $variantsErrors[] = "Variante " . ($index + 1) . ": " . $e->getMessage();
                        }
                    }
                }
            }

            // Mensaje de éxito
            $message = 'Producto creado exitosamente';
            if ($variantsCreated > 0) {
                $message .= " con {$variantsCreated} variante(s)";
            }
            
            // Si hubo errores en variantes, agregarlos como warning
            if (!empty($variantsErrors)) {
                $message .= ". Advertencias: " . implode(', ', $variantsErrors);
            }

            return redirect()
                ->route('sistema.inventory.show', $product->id)
                ->with('success', $message);
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al crear el producto: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar detalle de producto
     */
    public function show($id)
    {
        $product = Product::with([
            'category',
            'brand',
            'images',
            'variants.size',
            'variants.color',
            'variants.stockMovements.user'
        ])->findOrFail($id);

        $sizes = Size::active()->ordered()->get();
        $colors = Color::active()->get();

        return view('sistema.inventory.show', compact('product', 'sizes', 'colors'));
    }

    /**
     * Mostrar formulario de editar producto
     */
    public function edit($id)
    {
        $product = Product::with('variants')->findOrFail($id);
        $categories = Category::active()->get();
        $brands = Brand::active()->get();
        $colors = Color::active()->get();
        $sizes = Size::active()->ordered()->get()->groupBy('category_id');

        return view('sistema.inventory.edit', compact('product', 'categories', 'brands', 'colors', 'sizes'));
    }

    /**
     * Actualizar producto
     */
    public function update(ProductRequest $request, $id)
    {
        try {
            $product = $this->inventoryService->updateProduct($id, $request->validated());

            return redirect()
                ->route('sistema.inventory.show', $product->id)
                ->with('success', 'Producto actualizado exitosamente');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al actualizar el producto: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar producto (desactivar)
     */
    public function destroy($id)
    {
        try {
            $this->inventoryService->deleteProduct($id);

            return redirect()
                ->route('sistema.inventory.index')
                ->with('success', 'Producto desactivado exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al desactivar el producto: ' . $e->getMessage());
        }
    }

    /**
     * Agregar variante a producto
     */
    public function addVariant(Request $request, $id)
    {
        $request->validate([
            'size_id' => 'required|exists:sizes,id',
            'color_id' => 'required|exists:colors,id',
            'price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
        ]);

        try {
            $variant = $this->inventoryService->createVariant($id, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Variante agregada exitosamente',
                'variant' => $variant->load(['size', 'color']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al agregar variante: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Actualizar variante
     */
    public function updateVariant(Request $request, $variantId)
    {
        $request->validate([
            'price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'active' => 'boolean',
        ]);

        try {
            $variant = $this->inventoryService->updateVariant($variantId, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Variante actualizada exitosamente',
                'variant' => $variant,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar variante: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Eliminar variante
     */
    public function deleteVariant($variantId)
    {
        try {
            $variant = ProductVariant::findOrFail($variantId);
            $variant->update(['active' => false]);

            return response()->json([
                'success' => true,
                'message' => 'Variante desactivada exitosamente',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al desactivar variante: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Ajustar stock de una variante
     */
    public function adjustStock(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer',
            'type' => 'required|in:entrada,salida,ajuste',
            'notes' => 'nullable|string',
        ]);

        try {
            $movement = $this->inventoryService->adjustStock(
                $request->variant_id,
                $request->quantity,
                $request->type,
                auth()->id(),
                null,
                $request->notes
            );

            return response()->json([
                'success' => true,
                'message' => 'Stock ajustado exitosamente',
                'movement' => $movement,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al ajustar stock: ' . $e->getMessage(),
            ], 422);
        }
    }
}