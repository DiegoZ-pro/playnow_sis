<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_id',
        'order_id',
        'sale_number',
        'sale_type',
        'payment_method',
        'subtotal',
        'discount',
        'total',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Relación con usuario (vendedor)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con cliente
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Relación con pedido online (si aplica)
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relación con detalles de la venta
     */
    public function details()
    {
        return $this->hasMany(SaleDetail::class);
    }

    /**
     * Scope para ventas online
     */
    public function scopeOnline($query)
    {
        return $query->where('sale_type', 'online');
    }

    /**
     * Scope para ventas físicas
     */
    public function scopePhysical($query)
    {
        return $query->where('sale_type', 'physical');
    }

    /**
     * Scope para ventas de hoy
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope para ventas del mes actual
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', date('m'))
                     ->whereYear('created_at', date('Y'));
    }

    /**
     * Scope para ventas del año actual
     */
    public function scopeThisYear($query)
    {
        return $query->whereYear('created_at', date('Y'));
    }

    /**
     * Generar número de venta único
     */
    public static function generateSaleNumber(): string
    {
        $prefix = 'VTA-';
        $date = date('Ymd');
        $random = strtoupper(substr(md5(uniqid()), 0, 6));
        
        return $prefix . $date . '-' . $random;
    }
}