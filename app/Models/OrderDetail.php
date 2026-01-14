<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_variant_id',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /**
     * Relación con pedido
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relación con variante de producto
     */
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}