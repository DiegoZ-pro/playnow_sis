<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;

class HomeController extends Controller
{
    /**
     * Mostrar página principal
     */
    public function index()
    {
        // Productos destacados
        $featuredProducts = Product::with(['images', 'brand', 'category', 'variants'])
            ->active()
            ->featured()
            ->limit(8)
            ->get();

        // Productos nuevos
        $newProducts = Product::with(['images', 'brand', 'category', 'variants'])
            ->active()
            ->latest()
            ->limit(8)
            ->get();

        // Categorías
        $categories = Category::active()->get();

        // Marcas
        $brands = Brand::active()->get();

        return view('web.home', compact('featuredProducts', 'newProducts', 'categories', 'brands'));
    }
}