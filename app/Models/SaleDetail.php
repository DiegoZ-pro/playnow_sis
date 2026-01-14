<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
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
     * Relación con venta
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Relación con variante de producto
     */
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}