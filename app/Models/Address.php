<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'address',
        'city',
        'district', // Tambahkan field district
        'province',
        'postal_code',
        'country'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    // Helper method untuk menampilkan kota/kabupaten
    public function getCityOrDistrict()
    {
        return $this->city ?: $this->district;
    }
}
