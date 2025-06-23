<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'product_variant_id',
        'amount',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function variant(){
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    // Helper method untuk mendapatkan harga final
    public function getFinalPrice()
    {
        if ($this->variant) {
            return $this->variant->price;
        }
        return $this->product->price;
    }

    public function getTotalPrice()
    {
        return $this->getFinalPrice() * $this->amount;
    }
}
