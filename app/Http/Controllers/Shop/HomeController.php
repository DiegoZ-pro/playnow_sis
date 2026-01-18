<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display home page
     */
    public function index()
    {
        // Productos destacados (featured)
        $featuredProducts = Product::with(['images', 'category', 'brand', 'variants'])
            ->where('active', true)
            ->where('is_featured', true)
            ->take(8)
            ->get();
        
        // Productos nuevos (últimos 8)
        $newProducts = Product::with(['images', 'category', 'brand', 'variants'])
            ->where('active', true)
            ->latest()
            ->take(8)
            ->get();
        
        // Categorías principales
        $categories = Category::withCount('products')
            ->having('products_count', '>', 0)
            ->get();
        
        // Marcas principales
        $brands = Brand::withCount('products')
            ->having('products_count', '>', 0)
            ->get();
        
        return view('shop.home', compact('featuredProducts', 'newProducts', 'categories', 'brands'));
    }

    /**
     * Global search
     */
    public function search(Request $request)
    {
        $query = $request->input('q');
        
        if (empty($query)) {
            return redirect()->route('shop.products.index');
        }
        
        $products = Product::with(['images', 'category', 'brand', 'variants'])
            ->where('active', true)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhereHas('category', function($q) use ($query) {
                      $q->where('name', 'like', "%{$query}%");
                  })
                  ->orWhereHas('brand', function($q) use ($query) {
                      $q->where('name', 'like', "%{$query}%");
                  });
            })
            ->paginate(12);
        
        return view('shop.products.index', compact('products', 'query'));
    }
}