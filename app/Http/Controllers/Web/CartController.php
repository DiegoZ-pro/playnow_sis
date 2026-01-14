<?php

namespace App\Http\Controllers\Web;

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
     * Mostrar carrito
     */
    public function index()
    {
        $cart = $this->cartService->getCart();

        return view('web.cart.index', compact('cart'));
    }

    /**
     * Agregar producto al carrito
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        try {
            $cart = $this->cartService->addItem(
                $request->product_variant_id,
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
            ], 422);
        }
    }

    /**
     * Actualizar cantidad de item
     */
    public function update(Request $request, $key)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        try {
            $cart = $this->cartService->updateItem($key, $request->quantity);

            return response()->json([
                'success' => true,
                'message' => 'Carrito actualizado',
                'cart' => $cart,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Eliminar item del carrito
     */
    public function remove($key)
    {
        try {
            $cart = $this->cartService->removeItem($key);

            return response()->json([
                'success' => true,
                'message' => 'Producto eliminado del carrito',
                'cart' => $cart,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Vaciar carrito
     */
    public function clear()
    {
        $this->cartService->clearCart();

        return response()->json([
            'success' => true,
            'message' => 'Carrito vaciado',
        ]);
    }

    /**
     * API: Obtener cantidad de items en carrito
     */
    public function count()
    {
        return response()->json([
            'success' => true,
            'count' => $this->cartService->getItemsCount(),
        ]);
    }
}