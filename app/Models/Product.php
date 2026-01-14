<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'brand_id',
        'name',
        'slug',
        'description',
        'base_price',
        'featured',
        'active',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'featured' => 'boolean',
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
     * Relación con marca
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Relación con imágenes
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    /**
     * Relación con imagen principal
     */
    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    /**
     * Relación con variantes
     */
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Obtener variantes activas
     */
    public function activeVariants()
    {
        return $this->hasMany(ProductVariant::class)->where('active', true);
    }

    /**
     * Scope para productos activos
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope para productos destacados
     */
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    /**
     * Scope para filtrar por categoría
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope para filtrar por marca
     */
    public function scopeByBrand($query, $brandId)
    {
        return $query->where('brand_id', $brandId);
    }

    /**
     * Verificar si el producto tiene stock disponible
     */
    public function hasStock(): bool
    {
        return $this->variants()->where('stock', '>', 0)->exists();
    }

    /**
     * Obtener stock total del producto
     */
    public function getTotalStockAttribute(): int
    {
        return $this->variants()->sum('stock');
    }
}