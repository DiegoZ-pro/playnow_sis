<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Mostrar página de checkout
     */
    public function index()
    {
        // Verificar autenticación manualmente
        if (!Auth::guard('customer')->check()) {
            // ✅ Pasar URL de checkout como parámetro GET
            return redirect()->route('shop.login', ['redirect' => route('shop.checkout.index')])
                ->with('info', 'Inicia sesión para continuar con tu compra');
        }

        // ✅ CORREGIDO: Usar CartService en lugar de session
        $cartData = $this->cartService->getCart();

        if (empty($cartData['items']) || $cartData['count'] == 0) {
            return redirect()->route('shop.cart.index')
                ->with('error', 'Tu carrito está vacío');
        }

        // Preparar datos para la vista
        // La vista espera $cart como array de items directamente
        $cart = $cartData['items'];
        $subtotal = $cartData['total'];
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
        Log::info('=== CHECKOUT PROCESS INICIADO ===');
        Log::info('Request data:', $request->all());
        
        // Verificar autenticación
        if (!Auth::guard('customer')->check()) {
            Log::warning('Usuario no autenticado en process');
            return redirect()->route('shop.login', ['redirect' => route('shop.checkout.index')])
                ->with('error', 'Debes iniciar sesión para continuar');
        }

        Log::info('Usuario autenticado: ' . Auth::guard('customer')->id());

        // ✅ Usar CartService
        $cartData = $this->cartService->getCart();
        Log::info('Cart data:', $cartData);

        if (empty($cartData['items']) || $cartData['count'] == 0) {
            Log::warning('Carrito vacío en process');
            return redirect()->route('shop.cart.index')
                ->with('error', 'Tu carrito está vacío');
        }

        Log::info('Validando formulario...');
        
        // Validar datos
        $validated = $request->validate([
            'shipping_address' => ['required', 'string', 'max:500'],
            'shipping_city' => ['required', 'string', 'max:100'],
            'shipping_phone' => ['required', 'string', 'max:20'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'shipping_address.required' => 'La dirección de envío es obligatoria',
            'shipping_city.required' => 'La ciudad es obligatoria',
            'shipping_phone.required' => 'El teléfono es obligatorio',
        ]);
        
        Log::info('Validación exitosa. Datos:', $validated);

        // Calcular totales
        $subtotal = $cartData['total'];
        $shippingCost = $subtotal >= 500 ? 0 : 30;
        $total = $subtotal + $shippingCost;

        try {
            // Validar stock antes de procesar
            $this->cartService->validateCartStock();

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
                'notes' => $validated['notes'] ?? null,
                'whatsapp_sent' => false,
            ]);

            // Crear detalles del pedido
            foreach ($cartData['items'] as $item) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_variant_id' => $item['variant_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            DB::commit();

            Log::info('Orden creada exitosamente: ' . $order->id);

            // ✅ Limpiar carrito usando CartService
            $this->cartService->clearCart();
            
            Log::info('Carrito limpiado. Redirigiendo a confirmación...');

            return redirect()->route('shop.checkout.confirmation', $order->id)
                ->with('success', '¡Pedido realizado exitosamente!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error procesando pedido: ' . $e->getMessage());
            
            return back()
                ->with('error', 'Error al procesar el pedido: ' . $e->getMessage())
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