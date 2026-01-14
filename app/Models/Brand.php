<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'logo',
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
     * Scope para marcas activas
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}