<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Mostrar página de favoritos
     */
    public function index()
    {
        $customer = Auth::guard('customer')->user();
        
        $favorites = $customer->favoriteProducts()
            ->with(['brand', 'category', 'images' => function($query) {
                $query->where('is_primary', true)->orWhere('order', 0);
            }])
            ->paginate(12);

        return view('shop.favorites.index', compact('favorites'));
    }

    /**
     * Agregar/quitar de favoritos (AJAX)
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $customer = Auth::guard('customer')->user();
        $isAdded = $customer->toggleFavorite($request->product_id);

        return response()->json([
            'success' => true,
            'is_favorite' => $isAdded,
            'message' => $isAdded ? 'Agregado a favoritos' : 'Eliminado de favoritos',
            'favorites_count' => $customer->favorites()->count(),
        ]);
    }

    /**
     * Eliminar de favoritos
     */
    public function remove($productId)
    {
        $customer = Auth::guard('customer')->user();
        $customer->removeFavorite($productId);

        return back()->with('success', 'Producto eliminado de favoritos');
    }
}