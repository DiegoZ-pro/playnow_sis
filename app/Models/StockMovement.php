<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_variant_id',
        'user_id',
        'type',
        'quantity',
        'previous_stock',
        'new_stock',
        'reference',
        'notes',
    ];

    /**
     * Relación con variante de producto
     */
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    /**
     * Relación con usuario
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para movimientos de entrada
     */
    public function scopeEntrada($query)
    {
        return $query->where('type', 'entrada');
    }

    /**
     * Scope para movimientos de salida
     */
    public function scopeSalida($query)
    {
        return $query->where('type', 'salida');
    }

    /**
     * Scope para movimientos de ajuste
     */
    public function scopeAjuste($query)
    {
        return $query->where('type', 'ajuste');
    }
}