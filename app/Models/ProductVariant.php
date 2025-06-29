<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'size',
        'price',
        'stock',
        'color',
        'type'  // Tambahkan field baru
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class, 'product_variant_id');
    }

    // Helper method untuk mendapatkan nama variant
    public function getVariantName()
    {
        if ($this->type && !$this->size && !$this->color) {
            return $this->type;
        }
        
        $parts = [];
        if ($this->size) $parts[] = $this->size;
        if ($this->color) $parts[] = $this->color;
        if ($this->type) $parts[] = $this->type;
        
        return !empty($parts) ? implode(' - ', $parts) : 'Default';
    }

    // Helper method untuk cek apakah variant tersedia
    public function isAvailable()
    {
        return $this->stock > 0;
    }
}