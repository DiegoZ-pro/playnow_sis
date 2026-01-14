<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'description',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Relación con productos
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Relación con tallas
     */
    public function sizes()
    {
        return $this->hasMany(Size::class);
    }

    /**
     * Scope para categorías activas
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}