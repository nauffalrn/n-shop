<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description', 
        'price',
        'stock',
        'weight',
        'images',
        'rating'
    ];

    protected $casts = [
        'images' => 'array',
        'weight' => 'decimal:3'
    ];

    // Relasi many-to-many dengan Category
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_category');
    }
    
    // Untuk backward compatibility, gunakan kategori pertama
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    
    // Method untuk mendapatkan kategori pertama (untuk breadcrumb atau tampilan lain)
    public function getMainCategory()
    {
        return $this->categories()->first();
    }

    // Relasi lainnya tetap sama
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    // Helper methods
    public function getMainImage()
    {
        return $this->images ? $this->images[0] : null;
    }

    public function hasVariants()
    {
        return $this->variants()->count() > 0;
    }

    public function getAverageRating()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    public function getTotalReviews()
    {
        return $this->reviews()->count();
    }
}
