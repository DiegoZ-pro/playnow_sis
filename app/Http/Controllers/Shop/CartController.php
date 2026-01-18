<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Display cart page
     */
    public function index(Request $request)
    {
        $cart = $this->cartService->getCart();
        
        // Si es una petición AJAX, devolver JSON
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($cart);
        }
        
        // Si no, devolver vista
        return view('shop.cart.index', compact('cart'));
    }

    /**
     * Add item to cart (AJAX)
     */
    public function add(Request $request)
    {
        try {
            $request->validate([
                'variant_id' => 'required|exists:product_variants,id',
                'quantity' => 'required|integer|min:1',
            ]);

            $cart = $this->cartService->addItem(
                $request->variant_id,
                $request->quantity
            );

            return response()->json([
                'success' => true,
                'message' => 'Producto agregado al carrito',
                'cart' => $cart,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Update item quantity (AJAX)
     */
    public function update(Request $request, $cartId)
    {
        try {
            $request->validate([
                'quantity' => 'required|integer|min:1',
            ]);

            $cart = $this->cartService->updateQuantity($cartId, $request->quantity);

            return response()->json([
                'success' => true,
                'message' => 'Cantidad actualizada',
                'cart' => $cart,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Remove item from cart (AJAX)
     */
    public function remove($cartId)
    {
        try {
            $cart = $this->cartService->removeItem($cartId);

            return response()->json([
                'success' => true,
                'message' => 'Producto eliminado del carrito',
                'cart' => $cart,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Clear cart (AJAX)
     */
    public function clear()
    {
        try {
            $cart = $this->cartService->clearCart();

            return response()->json([
                'success' => true,
                'message' => 'Carrito vaciado',
                'cart' => $cart,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get cart count (AJAX - for header badge)
     */
    public function count()
    {
        $count = $this->cartService->getCartCount();
        
        return response()->json([
            'success' => true,
            'count' => $count,
        ]);
    }
}