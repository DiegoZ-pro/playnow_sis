<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'image_path',
        'order',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    /**
     * Relación con producto
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope para ordenar imágenes
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Obtener URL completa de la imagen
     */
    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->image_path);
    }
}