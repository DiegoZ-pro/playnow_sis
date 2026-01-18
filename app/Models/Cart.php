<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'session_id',
        'customer_id',
        'product_variant_id',
        'quantity',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
    ];

    /**
     * Get the customer that owns the cart item.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the product variant in the cart.
     */
    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    /**
     * Get the product through the variant.
     */
    public function product()
    {
        return $this->productVariant->product();
    }

    /**
     * Scope to get cart items by session ID.
     */
    public function scopeBySession($query, string $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    /**
     * Scope to get cart items by customer ID.
     */
    public function scopeByCustomer($query, int $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    /**
     * Get the subtotal for this cart item.
     */
    public function getSubtotalAttribute(): float
    {
        $price = $this->productVariant->price ?? $this->productVariant->product->base_price;
        return $price * $this->quantity;
    }
}