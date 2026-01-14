<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'city',
    ];

    /**
     * Relación con pedidos
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Relación con ventas
     */
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Obtener total de compras del cliente
     */
    public function getTotalPurchasesAttribute(): float
    {
        return $this->sales()->sum('total');
    }

    /**
     * Obtener cantidad de compras del cliente
     */
    public function getPurchasesCountAttribute(): int
    {
        return $this->sales()->count();
    }
}