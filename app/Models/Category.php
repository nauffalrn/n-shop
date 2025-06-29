<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    // Relasi one-to-many dengan Product (tetap ada untuk backward compatibility)
    public function singleProducts()
    {
        return $this->hasMany(Product::class);
    }

    // Relasi many-to-many dengan Product (tambahan baru)
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_category');
    }

    // Helper method untuk menghitung jumlah produk
    public function getProductsCountAttribute()
    {
        return $this->products()->count();
    }
}