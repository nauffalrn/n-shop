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
        'color'
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
        return $this->size . ' - ' . $this->color;
    }

    // Helper method untuk cek apakah variant tersedia
    public function isAvailable()
    {
        return $this->stock > 0;
    }
}