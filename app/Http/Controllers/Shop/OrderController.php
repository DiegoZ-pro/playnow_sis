<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Services\CartService;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Crear pedido desde el carrito
     */
    public function create(OrderRequest $request)
    {
        try {
            $order = $this->cartService->createOrder(
                $request->input('customer'),
                $request->input('shipping_address'),
                $request->input('notes')
            );

            return response()->json([
                'success' => true,
                'message' => 'Pedido creado exitosamente',
                'order' => $order,
                'redirect' => route('order.confirmation', $order->order_number),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el pedido: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Mostrar confirmación de pedido
     */
    public function confirmation($orderNumber)
    {
        $order = Order::with([
            'details.productVariant.product',
            'details.productVariant.size',
            'details.productVariant.color',
            'customer'
        ])
        ->where('order_number', $orderNumber)
        ->firstOrFail();

        return view('web.order.confirmation', compact('order'));
    }

    /**
     * Enviar pedido por WhatsApp
     */
    public function sendWhatsApp($orderNumber)
    {
        $order = Order::with([
            'details.productVariant.product',
            'customer'
        ])
        ->where('order_number', $orderNumber)
        ->firstOrFail();

        // Generar mensaje de WhatsApp
        $message = $this->generateWhatsAppMessage($order);

        // Obtener número de WhatsApp de la configuración
        $whatsappNumber = config('app.whatsapp_phone_number', '');

        // Generar enlace de WhatsApp
        $whatsappUrl = "https://wa.me/{$whatsappNumber}?text=" . urlencode($message);

        // Marcar como enviado
        $order->update(['whatsapp_sent' => true]);

        return response()->json([
            'success' => true,
            'whatsapp_url' => $whatsappUrl,
        ]);
    }

    /**
     * Generar mensaje de WhatsApp para el pedido
     */
    private function generateWhatsAppMessage(Order $order): string
    {
        $message = "🛍️ *Nuevo Pedido PLAY NOW*\n\n";
        $message .= "📋 *Pedido:* {$order->order_number}\n";
        $message .= "👤 *Cliente:* {$order->customer->name}\n";
        $message .= "📱 *Teléfono:* {$order->customer->phone}\n";
        
        if ($order->customer->email) {
            $message .= "✉️ *Email:* {$order->customer->email}\n";
        }
        
        $message .= "\n📦 *Productos:*\n";
        
        foreach ($order->details as $detail) {
            $product = $detail->productVariant->product;
            $size = $detail->productVariant->size->value;
            $color = $detail->productVariant->color->name;
            
            $message .= "• {$product->name}\n";
            $message .= "  Talla: {$size} | Color: {$color}\n";
            $message .= "  Cantidad: {$detail->quantity} x $" . number_format($detail->unit_price, 0, ',', '.') . "\n";
        }
        
        $message .= "\n💰 *Total:* $" . number_format($order->total, 0, ',', '.') . "\n";
        
        if ($order->shipping_address) {
            $message .= "\n📍 *Dirección de envío:*\n{$order->shipping_address}\n";
        }
        
        if ($order->notes) {
            $message .= "\n📝 *Notas:*\n{$order->notes}\n";
        }

        return $message;
    }
}