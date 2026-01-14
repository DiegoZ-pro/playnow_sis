<?php

namespace App\Services;

use App\Models\ProductVariant;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Customer;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class CartService
{
    private $sessionKey = 'shopping_cart';

    /**
     * Obtener carrito actual
     */
    public function getCart(): array
    {
        $cart = Session::get($this->sessionKey, []);
        
        // Cargar datos de productos
        $items = [];
        $subtotal = 0;

        foreach ($cart as $key => $item) {
            $variant = ProductVariant::with(['product', 'size', 'color'])
                ->find($item['product_variant_id']);

            if ($variant && $variant->active && $variant->hasStock($item['quantity'])) {
                $itemSubtotal = $variant->price * $item['quantity'];
                
                $items[] = [
                    'key' => $key,
                    'product_variant_id' => $variant->id,
                    'product' => $variant->product,
                    'size' => $variant->size,
                    'color' => $variant->color,
                    'sku' => $variant->sku,
                    'price' => $variant->price,
                    'quantity' => $item['quantity'],
                    'subtotal' => $itemSubtotal,
                    'available_stock' => $variant->stock,
                ];

                $subtotal += $itemSubtotal;
            } else {
                // Eliminar items inválidos
                unset($cart[$key]);
            }
        }

        // Actualizar sesión si se eliminaron items
        Session::put($this->sessionKey, $cart);

        return [
            'items' => $items,
            'subtotal' => $subtotal,
            'shipping' => 0, // Calculado después
            'total' => $subtotal,
            'items_count' => count($items),
        ];
    }

    /**
     * Agregar producto al carrito
     */
    public function addItem(int $productVariantId, int $quantity = 1): array
    {
        $variant = ProductVariant::with(['product'])->findOrFail($productVariantId);

        if (!$variant->active) {
            throw new \Exception('Este producto no está disponible');
        }

        $cart = Session::get($this->sessionKey, []);
        $key = 'item_' . $productVariantId;

        // Si ya existe, incrementar cantidad
        if (isset($cart[$key])) {
            $newQuantity = $cart[$key]['quantity'] + $quantity;
        } else {
            $newQuantity = $quantity;
        }

        // Verificar stock
        if (!$variant->hasStock($newQuantity)) {
            throw new \Exception('Stock insuficiente. Disponible: ' . $variant->stock);
        }

        $cart[$key] = [
            'product_variant_id' => $productVariantId,
            'quantity' => $newQuantity,
            'added_at' => now()->toDateTimeString(),
        ];

        Session::put($this->sessionKey, $cart);

        return $this->getCart();
    }

    /**
     * Actualizar cantidad de un item
     */
    public function updateItem(string $key, int $quantity): array
    {
        $cart = Session::get($this->sessionKey, []);

        if (!isset($cart[$key])) {
            throw new \Exception('Item no encontrado en el carrito');
        }

        if ($quantity <= 0) {
            return $this->removeItem($key);
        }

        // Verificar stock
        $variant = ProductVariant::find($cart[$key]['product_variant_id']);
        
        if (!$variant->hasStock($quantity)) {
            throw new \Exception('Stock insuficiente. Disponible: ' . $variant->stock);
        }

        $cart[$key]['quantity'] = $quantity;
        Session::put($this->sessionKey, $cart);

        return $this->getCart();
    }

    /**
     * Eliminar item del carrito
     */
    public function removeItem(string $key): array
    {
        $cart = Session::get($this->sessionKey, []);
        
        unset($cart[$key]);
        
        Session::put($this->sessionKey, $cart);

        return $this->getCart();
    }

    /**
     * Vaciar carrito
     */
    public function clearCart(): void
    {
        Session::forget($this->sessionKey);
    }

    /**
     * Crear pedido desde el carrito
     */
    public function createOrder(array $customerData, ?string $shippingAddress = null, ?string $notes = null): Order
    {
        return DB::transaction(function () use ($customerData, $shippingAddress, $notes) {
            $cart = $this->getCart();

            if (empty($cart['items'])) {
                throw new \Exception('El carrito está vacío');
            }

            // Validar stock nuevamente
            foreach ($cart['items'] as $item) {
                $variant = ProductVariant::find($item['product_variant_id']);
                if (!$variant->hasStock($item['quantity'])) {
                    throw new \Exception('Stock insuficiente para: ' . $item['product']->name);
                }
            }

            // Crear o buscar cliente
            $customer = $this->findOrCreateCustomer($customerData);

            // Calcular costos
            $subtotal = $cart['subtotal'];
            $shippingCost = $this->calculateShipping($shippingAddress);
            $total = $subtotal + $shippingCost;

            // Crear pedido
            $order = Order::create([
                'customer_id' => $customer->id,
                'order_number' => Order::generateOrderNumber(),
                'status' => 'pending',
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'total' => $total,
                'shipping_address' => $shippingAddress,
                'notes' => $notes,
            ]);

            // Crear detalles
            foreach ($cart['items'] as $item) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_variant_id' => $item['product_variant_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            // Limpiar carrito
            $this->clearCart();

            return $order->load(['details.productVariant.product', 'customer']);
        });
    }

    /**
     * Obtener cantidad de items en el carrito
     */
    public function getItemsCount(): int
    {
        $cart = Session::get($this->sessionKey, []);
        
        $count = 0;
        foreach ($cart as $item) {
            $count += $item['quantity'];
        }

        return $count;
    }

    /**
     * Buscar o crear cliente
     */
    private function findOrCreateCustomer(array $customerData): Customer
    {
        $customer = null;

        // Buscar por email
        if (isset($customerData['email'])) {
            $customer = Customer::where('email', $customerData['email'])->first();
        }

        // Buscar por teléfono si no se encontró por email
        if (!$customer && isset($customerData['phone'])) {
            $customer = Customer::where('phone', $customerData['phone'])->first();
        }

        // Crear si no existe
        if (!$customer) {
            $customer = Customer::create([
                'name' => $customerData['name'],
                'email' => $customerData['email'] ?? null,
                'phone' => $customerData['phone'] ?? null,
                'address' => $customerData['address'] ?? null,
                'city' => $customerData['city'] ?? null,
            ]);
        }

        return $customer;
    }

    /**
     * Calcular costo de envío
     */
    private function calculateShipping(?string $address): float
    {
        // Implementar lógica de cálculo de envío
        // Por ahora retornamos 0
        return 0;
    }
}