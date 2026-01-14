<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'value',
        'order',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Relación con categoría
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relación con variantes de productos
     */
    public function productVariants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Scope para tallas activas
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope para ordenar tallas
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}