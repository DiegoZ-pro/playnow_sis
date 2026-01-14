<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'size_id',
        'color_id',
        'sku',
        'price',
        'stock',
        'low_stock_threshold',
        'active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'active' => 'boolean',
    ];

    /**
     * Relación con producto
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relación con talla
     */
    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    /**
     * Relación con color
     */
    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    /**
     * Relación con detalles de pedidos
     */
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    /**
     * Relación con detalles de ventas
     */
    public function saleDetails()
    {
        return $this->hasMany(SaleDetail::class);
    }

    /**
     * Relación con movimientos de stock
     */
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Scope para variantes activas
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope para variantes con stock
     */
    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    /**
     * Scope para variantes con stock bajo
     */
    public function scopeLowStock($query)
    {
        return $query->whereRaw('stock <= low_stock_threshold')->where('stock', '>', 0);
    }

    /**
     * Verificar si hay stock disponible
     */
    public function hasStock(int $quantity = 1): bool
    {
        return $this->stock >= $quantity;
    }

    /**
     * Verificar si el stock está bajo
     */
    public function isLowStock(): bool
    {
        return $this->stock <= $this->low_stock_threshold && $this->stock > 0;
    }

    /**
     * Obtener nombre completo de la variante
     */
    public function getFullNameAttribute(): string
    {
        return $this->product->name . ' - ' . $this->size->value . ' - ' . $this->color->name;
    }
}