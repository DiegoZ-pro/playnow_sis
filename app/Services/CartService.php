<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\ProductVariant;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CartService
{
    /**
     * Get cart identifier (session_id or customer_id)
     */
    private function getCartIdentifier()
    {
        if (auth('customer')->check()) {
            return ['customer_id' => auth('customer')->id()];
        }
        
        // Para invitados, usar session ID
        if (!Session::has('cart_session_id')) {
            Session::put('cart_session_id', Session::getId());
        }
        
        return ['session_id' => Session::get('cart_session_id')];
    }

    /**
     * Get all cart items
     */
    public function getCart()
    {
        $identifier = $this->getCartIdentifier();
        
        $cartItems = Cart::with(['productVariant.product.category', 'productVariant.product.brand', 
                                 'productVariant.product.images', 'productVariant.size', 'productVariant.color'])
            ->where($identifier)
            ->get();
        
        $items = $cartItems->map(function ($cart) {
            $variant = $cart->productVariant;
            $product = $variant->product;
            $price = $variant->price ?? $product->base_price;
            
            return [
                'cart_id' => $cart->id,
                'variant_id' => $variant->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'category' => $product->category->name,
                'brand' => $product->brand->name,
                'size' => $variant->size->name,
                'color' => $variant->color->name,
                'sku' => $variant->sku,
                'price' => $price,
                'quantity' => $cart->quantity,
                'stock' => $variant->stock,
                'subtotal' => $price * $cart->quantity,
                'image' => $product->images->first()?->image_url ?? '/images/no-image.jpg',
            ];
        });
        
        $total = $items->sum('subtotal');
        $count = $items->sum('quantity');
        
        return [
            'items' => $items,
            'total' => $total,
            'count' => $count,
        ];
    }

    /**
     * Add item to cart
     */
    public function addItem(int $variantId, int $quantity = 1)
    {
        $variant = ProductVariant::with('product')->findOrFail($variantId);
        
        // Validar stock disponible
        if (!$variant->active) {
            throw new \Exception('Producto no disponible');
        }
        
        if ($variant->stock < $quantity) {
            throw new \Exception('Stock insuficiente. Solo hay ' . $variant->stock . ' unidades disponibles');
        }
        
        $identifier = $this->getCartIdentifier();
        
        // Buscar si ya existe en el carrito
        $cartItem = Cart::where($identifier)
            ->where('product_variant_id', $variantId)
            ->first();
        
        if ($cartItem) {
            // Actualizar cantidad
            $newQuantity = $cartItem->quantity + $quantity;
            
            if ($variant->stock < $newQuantity) {
                throw new \Exception('No hay suficiente stock. Máximo disponible: ' . $variant->stock);
            }
            
            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            // Crear nuevo item
            Cart::create([
                ...$identifier,
                'product_variant_id' => $variantId,
                'quantity' => $quantity,
            ]);
        }
        
        return $this->getCart();
    }

    /**
     * Update item quantity
     */
    public function updateQuantity(int $cartId, int $quantity)
    {
        if ($quantity < 1) {
            throw new \Exception('La cantidad debe ser al menos 1');
        }
        
        $identifier = $this->getCartIdentifier();
        
        $cartItem = Cart::where($identifier)
            ->where('id', $cartId)
            ->firstOrFail();
        
        $variant = ProductVariant::findOrFail($cartItem->product_variant_id);
        
        // Validar stock
        if ($variant->stock < $quantity) {
            throw new \Exception('Stock insuficiente. Solo hay ' . $variant->stock . ' unidades disponibles');
        }
        
        $cartItem->update(['quantity' => $quantity]);
        
        return $this->getCart();
    }

    /**
     * Remove item from cart
     */
    public function removeItem(int $cartId)
    {
        $identifier = $this->getCartIdentifier();
        
        Cart::where($identifier)
            ->where('id', $cartId)
            ->delete();
        
        return $this->getCart();
    }

    /**
     * Clear cart
     */
    public function clearCart()
    {
        $identifier = $this->getCartIdentifier();
        
        Cart::where($identifier)->delete();
        
        return ['items' => [], 'total' => 0, 'count' => 0];
    }

    /**
     * Get cart count (for header badge)
     */
    public function getCartCount(): int
    {
        $identifier = $this->getCartIdentifier();
        
        return Cart::where($identifier)->sum('quantity');
    }

    /**
     * Validate stock before checkout
     */
    public function validateCartStock()
    {
        $cart = $this->getCart();
        
        foreach ($cart['items'] as $item) {
            $variant = ProductVariant::find($item['variant_id']);
            
            if (!$variant->active) {
                throw new \Exception("El producto '{$item['product_name']}' ya no está disponible");
            }
            
            if ($variant->stock < $item['quantity']) {
                throw new \Exception("Stock insuficiente para '{$item['product_name']}'. Solo quedan {$variant->stock} unidades");
            }
        }
        
        return true;
    }

    /**
     * Merge guest cart to customer cart after login
     */
    public function mergeGuestCart(int $customerId)
    {
        if (!Session::has('cart_session_id')) {
            return;
        }
        
        $sessionId = Session::get('cart_session_id');
        
        DB::transaction(function () use ($sessionId, $customerId) {
            $guestCart = Cart::where('session_id', $sessionId)->get();
            
            foreach ($guestCart as $guestItem) {
                $customerItem = Cart::where('customer_id', $customerId)
                    ->where('product_variant_id', $guestItem->product_variant_id)
                    ->first();
                
                if ($customerItem) {
                    // Sumar cantidades
                    $customerItem->update([
                        'quantity' => $customerItem->quantity + $guestItem->quantity
                    ]);
                    $guestItem->delete();
                } else {
                    // Transferir item al cliente
                    $guestItem->update([
                        'session_id' => null,
                        'customer_id' => $customerId,
                    ]);
                }
            }
        });
        
        Session::forget('cart_session_id');
    }

    /**
     * Clean old guest carts (run daily via scheduler)
     */
    public static function cleanOldGuestCarts(int $days = 7)
    {
        Cart::whereNotNull('session_id')
            ->whereNull('customer_id')
            ->where('created_at', '<', now()->subDays($days))
            ->delete();
    }
}