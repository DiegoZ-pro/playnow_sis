<?php

namespace App\Http\Controllers\Sistema;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Size;
use App\Models\Color;
use App\Services\InventoryService;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InventoryController extends Controller
{
    protected $inventoryService;
    protected $imageService;

    public function __construct(InventoryService $inventoryService, ImageService $imageService)
    {
        $this->inventoryService = $inventoryService;
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of the products.
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand', 'images']);

        // Filtros
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('brand')) {
            $query->where('brand_id', $request->brand);
        }

        $products = $query->latest()->paginate(10);
        $categories = Category::all();
        $brands = Brand::all();

        return view('sistema.inventory.index', compact('products', 'categories', 'brands'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = Category::all();
        $brands = Brand::all();
        $sizes = Size::all();
        $colors = Color::all();

        return view('sistema.inventory.create', compact('categories', 'brands', 'sizes', 'colors'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validación
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'category_id' => 'required|exists:categories,id',
                'brand_id' => 'required|exists:brands,id',
                'base_price' => 'required|numeric|min:0',
                'images.*' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            ]);

            DB::beginTransaction();

            // Crear producto
            $product = Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'category_id' => $request->category_id,
                'brand_id' => $request->brand_id,
                'base_price' => $request->base_price,
                'active' => $request->has('active'),
                'is_featured' => $request->has('is_featured'),
            ]);

            // Procesar imágenes
            if ($request->hasFile('images')) {
                $this->processImages($request->file('images'), $product->id);
            }

            // Crear variantes si se proporcionaron
            if ($request->has('variants') && is_array($request->variants)) {
                foreach ($request->variants as $variantData) {
                    // Asegurarse de tener los datos necesarios
                    if (isset($variantData['size_id']) && isset($variantData['color_id'])) {
                        // Llamar a createVariant con ($productId, $data)
                        $this->inventoryService->createVariant(
                            $product->id,
                            [
                                'size_id' => $variantData['size_id'],
                                'color_id' => $variantData['color_id'],
                                'stock' => $variantData['stock'] ?? 0,
                                'price' => $variantData['price'] ?? null,
                            ]
                        );
                    }
                }
            }

            DB::commit();

            // REDIRECCIÓN CON MENSAJE DE ÉXITO (NO JSON)
            return redirect()
                ->route('sistema.inventory.show', $product->id)
                ->with('success', 'Producto creado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            
            // REDIRECCIÓN CON ERROR (NO JSON)
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al crear producto: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified product.
     */
    public function show($id)
    {
        $product = Product::with(['category', 'brand', 'images' => function($query) {
            $query->orderBy('order')->orderBy('created_at');
        }, 'variants.size', 'variants.color'])->findOrFail($id);

        $sizes = Size::all();
        $colors = Color::all();

        return view('sistema.inventory.show', compact('product', 'sizes', 'colors'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit($id)
    {
        $product = Product::with(['images', 'variants'])->findOrFail($id);
        $categories = Category::all();
        $brands = Brand::all();

        return view('sistema.inventory.edit', compact('product', 'categories', 'brands'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);

            // Validación
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'category_id' => 'required|exists:categories,id',
                'brand_id' => 'required|exists:brands,id',
                'base_price' => 'required|numeric|min:0',
            ]);

            $product->update([
                'name' => $request->name,
                'description' => $request->description,
                'category_id' => $request->category_id,
                'brand_id' => $request->brand_id,
                'base_price' => $request->base_price,
                'active' => $request->has('active'),
                'is_featured' => $request->has('is_featured'),
            ]);

            return redirect()
                ->route('sistema.inventory.show', $product->id)
                ->with('success', 'Producto actualizado exitosamente');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar producto: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            $productName = $product->name;
            
            // Las imágenes se eliminarán automáticamente por el modelo (cascade)
            $product->delete();

            // SI LA PETICIÓN ESPERA JSON (AJAX)
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Producto \"{$productName}\" eliminado exitosamente",
                ]);
            }

            // SI ES UNA PETICIÓN NORMAL (FORM)
            return redirect()
                ->route('sistema.inventory.index')
                ->with('success', "Producto \"{$productName}\" eliminado exitosamente");

        } catch (\Exception $e) {
            // SI LA PETICIÓN ESPERA JSON (AJAX)
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar producto: ' . $e->getMessage(),
                ], 500);
            }

            // SI ES UNA PETICIÓN NORMAL (FORM)
            return redirect()
                ->back()
                ->with('error', 'Error al eliminar producto: ' . $e->getMessage());
        }
    }

    /**
     * Add variant to product
     */
    public function addVariant(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'size_id' => 'required|exists:sizes,id',
                'color_id' => 'required|exists:colors,id',
                'stock' => 'required|integer|min:0',
                'price' => 'nullable|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Llamar a createVariant con ($productId, $data)
            $variant = $this->inventoryService->createVariant($id, [
                'size_id' => $request->size_id,
                'color_id' => $request->color_id,
                'stock' => $request->stock,
                'price' => $request->price,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Variante agregada exitosamente',
                'variant' => $variant,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Adjust stock for a variant
     */
    public function adjustStock(Request $request)
    {
        try {
            $request->validate([
                'variant_id' => 'required|exists:product_variants,id',
                'quantity' => 'required|integer',
                'type' => 'required|in:entrada,salida,ajuste',
                'reason' => 'required|string|max:255',
            ]);

            $this->inventoryService->adjustStock(
                $request->variant_id,
                $request->quantity,
                $request->type,
                $request->reason,
                auth()->id()
            );

            return response()->json([
                'success' => true,
                'message' => 'Stock ajustado exitosamente',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload images for product
     */
    public function uploadImages(Request $request, $id)
    {
        try {
            $request->validate([
                'images' => 'required|array|max:5',
                'images.*' => 'image|mimes:jpeg,jpg,png,webp|max:5120', // 5MB
            ]);

            $product = Product::findOrFail($id);
            
            // Verificar que no exceda el límite de 5 imágenes
            $currentImagesCount = $product->images()->count();
            $newImagesCount = count($request->file('images'));
            
            if (($currentImagesCount + $newImagesCount) > 5) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes agregar más de 5 imágenes por producto',
                ], 400);
            }

            $uploadedImages = $this->processImages($request->file('images'), $id);

            return response()->json([
                'success' => true,
                'message' => 'Imágenes subidas exitosamente',
                'images' => $uploadedImages,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al subir imágenes: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete image
     */
    public function deleteImage($imageId)
    {
        try {
            $image = ProductImage::findOrFail($imageId);
            $image->delete(); // El modelo se encarga de eliminar el archivo físico

            return response()->json([
                'success' => true,
                'message' => 'Imagen eliminada exitosamente',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar imagen: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Set primary image
     */
    public function setPrimaryImage(Request $request, $imageId)
    {
        try {
            $image = ProductImage::findOrFail($imageId);
            
            // Quitar is_primary de todas las imágenes del producto
            ProductImage::where('product_id', $image->product_id)
                ->update(['is_primary' => false]);
            
            // Marcar esta como principal
            $image->update(['is_primary' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Imagen principal actualizada',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Process and save images
     */
    private function processImages(array $images, int $productId)
    {
        $uploadedImages = [];
        $order = ProductImage::where('product_id', $productId)->max('order') ?? -1;

        foreach ($images as $image) {
            // Validar imagen
            $errors = $this->imageService->validateImage($image);
            if (!empty($errors)) {
                continue; // Saltar imágenes inválidas
            }

            // Subir y procesar
            $path = $this->imageService->uploadProductImage($image, $productId);
            
            $order++;
            
            // Crear registro en BD
            $productImage = ProductImage::create([
                'product_id' => $productId,
                'image_url' => $path,
                'order' => $order,
                'is_primary' => $order == 0, // Primera imagen es principal
            ]);

            $uploadedImages[] = $productImage;
        }

        return $uploadedImages;
    }
}