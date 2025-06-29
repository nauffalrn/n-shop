<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'description',
        'type',
        'value',
        'max_uses',
        'used_count',
        'min_purchase',
        'max_discount',
        'is_active',
        'starts_at',
        'expires_at'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function isValid($totalPrice = 0)
    {
        $now = now();
        
        // Cek jika kode aktif
        if (!$this->is_active) {
            return false;
        }
        
        // Cek periode berlaku
        if ($this->starts_at && $this->starts_at->gt($now)) {
            return false;
        }
        
        if ($this->expires_at && $this->expires_at->lt($now)) {
            return false;
        }
        
        // Cek jumlah penggunaan
        if ($this->max_uses > 0 && $this->used_count >= $this->max_uses) {
            return false;
        }
        
        // Cek minimum pembelian
        if ($this->min_purchase > 0 && $totalPrice < $this->min_purchase) {
            return false;
        }
        
        return true;
    }
    
    public function calculateDiscount($totalPrice)
    {
        if (!$this->isValid($totalPrice)) {
            return 0;
        }
        
        $discount = 0;
        
        if ($this->type === 'percentage') {
            $discount = $totalPrice * ($this->value / 100);
            
            // Terapkan maksimum diskon jika dikonfigurasi
            if ($this->max_discount && $discount > $this->max_discount) {
                $discount = $this->max_discount;
            }
        } else {
            $discount = $this->value;
            
            // Pastikan diskon tidak melebihi total harga
            if ($discount > $totalPrice) {
                $discount = $totalPrice;
            }
        }
        
        return $discount;
    }

    public function getDiscountDescription()
    {
        if ($this->type === 'percentage') {
            $desc = "{$this->value}% diskon";
            if ($this->max_discount) {
                $desc .= " (maks. Rp " . number_format($this->max_discount, 0, ',', '.') . ")";
            }
            return $desc;
        } else {
            return "Potongan Rp " . number_format($this->value, 0, ',', '.');
        }
    }
}
