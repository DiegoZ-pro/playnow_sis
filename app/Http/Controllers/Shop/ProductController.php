<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Mostrar catálogo de productos con filtros
     */
    public function index(Request $request)
    {
        // Query base
        $query = Product::with([
            'category', 
            'brand',
            'images' => function($q) {
                $q->where('is_primary', true)->orWhere('order', 0);
            }
        ])->where('active', true);

        // Filtro por búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filtro por categoría
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filtro por marca
        if ($request->filled('brand')) {
            $query->where('brand_id', $request->brand);
        }

        // Filtro por rango de precio
        if ($request->filled('min_price')) {
            $query->where('base_price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('base_price', '<=', $request->max_price);
        }

        // Ordenamiento
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'price_asc':
                $query->orderBy('base_price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('base_price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'latest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // Paginación
        $products = $query->paginate(12)->withQueryString();

        // Obtener categorías y marcas para filtros
        $categories = Category::where('active', true)->get();
        $brands = Brand::where('active', true)->get();

        return view('shop.products.index', compact(
            'products',
            'categories',
            'brands'
        ));
    }

    /**
     * Mostrar detalle de un producto específico
     */
    public function show($slug)
    {
        // Buscar producto por slug con relaciones
        $product = Product::with([
            'category',
            'brand',
            'images' => function($query) {
                $query->orderBy('order');
            },
            'variants' => function($query) {
                $query->where('active', true)
                      ->with(['size', 'color']);
            }
        ])
        ->where('slug', $slug)
        ->where('active', true)
        ->firstOrFail();

        // Obtener tallas únicas disponibles
        $availableSizes = $product->variants
            ->where('stock', '>', 0)
            ->pluck('size')
            ->unique('id')
            ->sortBy('order')
            ->values();

        // Obtener colores únicos disponibles
        $availableColors = $product->variants
            ->where('stock', '>', 0)
            ->pluck('color')
            ->unique('id')
            ->values();

        // Obtener productos relacionados (misma categoría)
        $relatedProducts = Product::with(['images' => function($query) {
                $query->where('is_primary', true)->orWhere('order', 0);
            }])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('active', true)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return view('shop.products.show', compact(
            'product',
            'availableSizes',
            'availableColors',
            'relatedProducts'
        ));
    }

    /**
     * Obtener información de una variante específica (AJAX)
     */
    public function getVariantInfo(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'size_id' => 'required|exists:sizes,id',
            'color_id' => 'required|exists:colors,id',
        ]);

        $variant = ProductVariant::with(['size', 'color'])
            ->where('product_id', $request->product_id)
            ->where('size_id', $request->size_id)
            ->where('color_id', $request->color_id)
            ->where('active', true)
            ->first();

        if (!$variant) {
            return response()->json([
                'success' => false,
                'message' => 'Variante no disponible'
            ], 404);
        }

        // Obtener producto para precio base si la variante no tiene precio
        $product = Product::find($request->product_id);

        return response()->json([
            'success' => true,
            'variant' => [
                'id' => $variant->id,
                'sku' => $variant->sku,
                'price' => $variant->price ?? $product->base_price,
                'stock' => $variant->stock,
                'available' => $variant->stock > 0,
                'size' => $variant->size->value,
                'color' => $variant->color->name,
            ]
        ]);
    }
}