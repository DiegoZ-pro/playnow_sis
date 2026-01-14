<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'hex_code',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Relación con variantes de productos
     */
    public function productVariants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Scope para colores activos
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}