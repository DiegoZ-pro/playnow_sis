<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'image_url',
        'order',
        'is_primary',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'order' => 'integer',
        'is_primary' => 'boolean',
    ];

    /**
     * Get the product that owns the image.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the full URL of the image.
     */
    public function getFullUrlAttribute()
    {
        return asset('storage/' . $this->image_url);
    }

    /**
     * Delete the image file from storage.
     */
    public function deleteFile()
    {
        if (Storage::disk('public')->exists($this->image_url)) {
            Storage::disk('public')->delete($this->image_url);
        }
    }

    /**
     * Boot method to handle model events.
     */
    protected static function boot()
    {
        parent::boot();

        // Cuando se elimina la imagen, eliminar el archivo físico
        static::deleting(function ($image) {
            $image->deleteFile();
        });
    }
}