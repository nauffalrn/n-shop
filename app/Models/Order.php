<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'is_paid',
        'payment_status',
        'rejection_reason',
        'payment_receipt',
        'promo_id',
        'discount',
        'address_id',
        'shipping_cost',
        'shipping_address'
    ];

    const PAYMENT_PENDING = 'pending';
    const PAYMENT_AWAITING_CONFIRMATION = 'awaiting_confirmation';
    const PAYMENT_CONFIRMED = 'confirmed';
    const PAYMENT_REJECTED = 'rejected';

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function transactions(){
        return $this->hasMany(Transaction::class);
    }

    public function promo()
    {
        return $this->belongsTo(Promo::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    // Tambahkan method untuk menghitung total setelah diskon dan dengan ongkir
    public function getTotalAfterDiscount()
    {
        $total = $this->transactions->sum(function($transaction) {
            return $transaction->product->price * $transaction->amount;
        });
        
        return $total - $this->discount + $this->shipping_cost;
    }
    
    // Ambil data alamat pengiriman dari JSON
    public function getShippingAddressAttribute($value)
    {
        return $value ? json_decode($value) : null;
    }
    
    // Helper method untuk memeriksa status pembayaran
    public function isPending()
    {
        return $this->payment_status === self::PAYMENT_PENDING;
    }
    
    public function isAwaitingConfirmation()
    {
        return $this->payment_status === self::PAYMENT_AWAITING_CONFIRMATION;
    }
    
    public function isConfirmed()
    {
        return $this->payment_status === self::PAYMENT_CONFIRMED;
    }
    
    public function isRejected()
    {
        return $this->payment_status === self::PAYMENT_REJECTED;
    }
}
