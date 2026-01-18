<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'category_id',
        'brand_id',
        'base_price',
        'active',
        'is_featured',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'base_price' => 'decimal:2',
        'active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    /**
     * Boot method to handle model events.
     */
    protected static function boot()
    {
        parent::boot();

        // Generar slug automáticamente al crear
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = static::generateUniqueSlug($product->name);
            }
        });

        // Actualizar slug al cambiar nombre
        static::updating(function ($product) {
            if ($product->isDirty('name') && empty($product->slug)) {
                $product->slug = static::generateUniqueSlug($product->name);
            }
        });
    }

    /**
     * Generate a unique slug from name
     */
    protected static function generateUniqueSlug($name)
    {
        $slug = Str::slug($name);
        $count = static::where('slug', 'LIKE', "{$slug}%")->count();
        
        return $count ? "{$slug}-{$count}" : $slug;
    }

    /**
     * Get the category that owns the product.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the brand that owns the product.
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the variants for the product.
     */
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Get the images for the product.
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    /**
     * Get the primary image for the product.
     */
    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    /**
     * Get the first image or a default placeholder.
     */
    public function getImageUrlAttribute()
    {
        $image = $this->primaryImage ?? $this->images()->first();
        
        if ($image) {
            return asset('storage/' . $image->image_url);
        }
        
        return asset('images/no-image.png');
    }

    /**
     * Scope to get only active products.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope to get only featured products.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Get total stock across all variants.
     */
    public function getTotalStockAttribute()
    {
        return $this->variants()->sum('stock');
    }

    /**
     * Check if product is in stock.
     */
    public function isInStock()
    {
        return $this->total_stock > 0;
    }
}