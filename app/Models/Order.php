<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'order_number',
        'status',
        'subtotal',
        'shipping_cost',
        'total',
        'shipping_address',
        'notes',
        'whatsapp_sent',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total' => 'decimal:2',
        'whatsapp_sent' => 'boolean',
    ];

    /**
     * Relación con cliente
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Relación con detalles del pedido
     */
    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }

    /**
     * Relación con venta (si fue confirmado)
     */
    public function sale()
    {
        return $this->hasOne(Sale::class);
    }

    /**
     * Scope para pedidos pendientes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope para pedidos confirmados
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope para pedidos cancelados
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Generar número de pedido único
     */
    public static function generateOrderNumber(): string
    {
        $prefix = 'ORD-';
        $date = date('Ymd');
        $random = strtoupper(substr(md5(uniqid()), 0, 6));
        
        return $prefix . $date . '-' . $random;
    }

    /**
     * Verificar si el pedido puede ser cancelado
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }
}