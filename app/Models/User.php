<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'role_id',
        'name',
        'email',
        'password',
        'active',
        'last_login',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login' => 'datetime',
        'active' => 'boolean',
        'password' => 'hashed',
    ];

    /**
     * Relación con rol
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Relación con ventas
     */
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Relación con movimientos de stock
     */
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Verificar si el usuario tiene un rol específico
     */
    public function hasRole(string $role): bool
    {
        return $this->role->slug === $role;
    }

    /**
     * Verificar si el usuario tiene un permiso específico
     */
    public function can($ability, $arguments = [])
    {
        if ($this->role) {
            return $this->role->hasPermission($ability);
        }
        return parent::can($ability, $arguments);
    }

    /**
     * Verificar si el usuario está activo
     */
    public function isActive(): bool
    {
        return $this->active;
    }
}