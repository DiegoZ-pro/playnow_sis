<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    /**
     * Mostrar página de checkout
     */
    public function index()
    {
        // Verificar autenticación manualmente
        if (!Auth::guard('customer')->check()) {
            return redirect()->route('shop.login')
                ->with('error', 'Debes iniciar sesión para continuar con la compra')
                ->with('url.intended', route('shop.checkout.index'));
        }

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('shop.home')
                ->with('error', 'Tu carrito está vacío');
        }

        // Calcular totales
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += floatval($item['price']) * intval($item['quantity']);
        }

        $shippingCost = $subtotal >= 500 ? 0 : 30;
        $total = $subtotal + $shippingCost;

        $customer = Auth::guard('customer')->user();

        return view('shop.cart.checkout', compact('cart', 'subtotal', 'shippingCost', 'total', 'customer'));
    }

    /**
     * Procesar el pedido
     */
    public function process(Request $request)
    {
        // Verificar autenticación
        if (!Auth::guard('customer')->check()) {
            return redirect()->route('shop.login')
                ->with('error', 'Debes iniciar sesión para continuar');
        }

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('shop.home')
                ->with('error', 'Tu carrito está vacío');
        }

        // Validar datos
        $validated = $request->validate([
            'shipping_address' => ['required', 'string', 'max:500'],
            'shipping_city' => ['required', 'string', 'max:100'],
            'shipping_phone' => ['required', 'string', 'max:20'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        // Calcular totales
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += floatval($item['price']) * intval($item['quantity']);
        }

        $shippingCost = $subtotal >= 500 ? 0 : 30;
        $total = $subtotal + $shippingCost;

        try {
            DB::beginTransaction();

            // Crear dirección de envío completa
            $fullAddress = $validated['shipping_address'] . ', ' . 
                          $validated['shipping_city'] . ' - Tel: ' . 
                          $validated['shipping_phone'];

            // Crear orden
            $order = Order::create([
                'customer_id' => Auth::guard('customer')->id(),
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'status' => 'pending',
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'total' => $total,
                'shipping_address' => $fullAddress,
                'notes' => $validated['notes'],
                'whatsapp_sent' => false,
            ]);

            // Crear detalles del pedido
            foreach ($cart as $item) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_variant_id' => $item['variant_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => floatval($item['price']) * intval($item['quantity']),
                ]);

                // Reducir stock
                $variant = \App\Models\ProductVariant::find($item['variant_id']);
                if ($variant) {
                    $variant->decrement('stock', $item['quantity']);
                }
            }

            DB::commit();

            // Limpiar carrito
            session()->forget('cart');

            return redirect()->route('shop.checkout.confirmation', $order->id)
                ->with('success', '¡Pedido realizado exitosamente!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error procesando pedido: ' . $e->getMessage());
            return back()
                ->with('error', 'Error al procesar el pedido. Por favor intenta de nuevo.')
                ->withInput();
        }
    }

    /**
     * Página de confirmación
     */
    public function confirmation($orderId)
    {
        if (!Auth::guard('customer')->check()) {
            return redirect()->route('shop.login');
        }

        $order = Order::with([
            'details.productVariant.product.images', 
            'details.productVariant.size', 
            'details.productVariant.color',
            'customer'
        ])
        ->where('customer_id', Auth::guard('customer')->id())
        ->findOrFail($orderId);

        return view('shop.cart.confirmation', compact('order'));
    }
}