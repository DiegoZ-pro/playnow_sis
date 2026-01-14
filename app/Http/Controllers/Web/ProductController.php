<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Size;
use App\Models\Color;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Mostrar catálogo de productos
     */
    public function index(Request $request)
    {
        $query = Product::with(['images', 'brand', 'category', 'variants'])
            ->active();

        // Filtro por categoría
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filtro por marca
        if ($request->filled('brand')) {
            $query->whereHas('brand', function ($q) use ($request) {
                $q->where('slug', $request->brand);
            });
        }

        // Filtro por rango de precio
        if ($request->filled('price_min')) {
            $query->where('base_price', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $query->where('base_price', '<=', $request->price_max);
        }

        // Filtro por talla
        if ($request->filled('size')) {
            $query->whereHas('variants.size', function ($q) use ($request) {
                $q->where('value', $request->size);
            });
        }

        // Filtro por color
        if ($request->filled('color')) {
            $query->whereHas('variants.color', function ($q) use ($request) {
                $q->where('name', $request->color);
            });
        }

        // Búsqueda por nombre
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Ordenamiento
        $sort = $request->input('sort', 'newest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('base_price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('base_price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            default:
                $query->latest();
        }

        $products = $query->paginate(12);

        // Datos para filtros
        $categories = Category::active()->get();
        $brands = Brand::active()->get();
        $sizes = Size::active()->ordered()->get()->unique('value');
        $colors = Color::active()->get();

        return view('web.products.index', compact('products', 'categories', 'brands', 'sizes', 'colors'));
    }

    /**
     * Mostrar productos por categoría
     */
    public function byCategory($categorySlug, Request $request)
    {
        $category = Category::where('slug', $categorySlug)
            ->where('active', true)
            ->firstOrFail();

        $request->merge(['category' => $categorySlug]);

        return $this->index($request);
    }

    /**
     * Mostrar detalle de producto
     */
    public function show($slug)
    {
        $product = Product::with([
            'images',
            'brand',
            'category',
            'variants.size',
            'variants.color',
            'variants' => function ($query) {
                $query->where('active', true)
                      ->where('stock', '>', 0);
            }
        ])
        ->where('slug', $slug)
        ->where('active', true)
        ->firstOrFail();

        // Productos relacionados (misma categoría)
        $relatedProducts = Product::with(['images', 'brand', 'variants'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->active()
            ->inRandomOrder()
            ->limit(4)
            ->get();

        // Agrupar variantes por talla y color
        $availableSizes = $product->variants->pluck('size')->unique('id');
        $availableColors = $product->variants->pluck('color')->unique('id');

        return view('web.products.show', compact('product', 'relatedProducts', 'availableSizes', 'availableColors'));
    }

    /**
     * API: Obtener variantes disponibles según filtros
     */
    public function getVariants(Request $request, $productId)
    {
        $query = Product::findOrFail($productId)
            ->variants()
            ->with(['size', 'color'])
            ->where('active', true)
            ->where('stock', '>', 0);

        if ($request->filled('size_id')) {
            $query->where('size_id', $request->size_id);
        }

        if ($request->filled('color_id')) {
            $query->where('color_id', $request->color_id);
        }

        $variants = $query->get();

        return response()->json([
            'success' => true,
            'variants' => $variants,
        ]);
    }
}