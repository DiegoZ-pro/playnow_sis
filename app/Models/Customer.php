<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'city',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Relación con pedidos
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Relación con favoritos
     */
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Productos favoritos (relación directa)
     */
    public function favoriteProducts()
    {
        return $this->belongsToMany(Product::class, 'favorites')->withTimestamps();
    }

    /**
     * Verificar si un producto está en favoritos
     */
    public function hasFavorite($productId)
    {
        return $this->favorites()->where('product_id', $productId)->exists();
    }

    /**
     * Agregar a favoritos
     */
    public function addFavorite($productId)
    {
        if (!$this->hasFavorite($productId)) {
            return $this->favorites()->create(['product_id' => $productId]);
        }
        return false;
    }

    /**
     * Eliminar de favoritos
     */
    public function removeFavorite($productId)
    {
        return $this->favorites()->where('product_id', $productId)->delete();
    }

    /**
     * Toggle favorito (agregar si no existe, eliminar si existe)
     */
    public function toggleFavorite($productId)
    {
        if ($this->hasFavorite($productId)) {
            $this->removeFavorite($productId);
            return false; // Removido
        } else {
            $this->addFavorite($productId);
            return true; // Agregado
        }
    }

    /**
     * Obtener pedidos recientes
     */
    public function recentOrders($limit = 5)
    {
        return $this->orders()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Verificar si tiene pedidos
     */
    public function hasOrders()
    {
        return $this->orders()->count() > 0;
    }

    /**
     * Total gastado
     */
    public function totalSpent()
    {
        return $this->orders()
            ->where('status', '!=', 'cancelled')
            ->sum('total');
    }

    /**
     * Obtener nombre completo
     */
    public function getFullNameAttribute()
    {
        return $this->name;
    }

    /**
     * Obtener dirección completa
     */
    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->address,
            $this->city,
        ]);

        return implode(', ', $parts);
    }
}