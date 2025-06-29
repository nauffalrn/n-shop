<?php

namespace App\Services;

class ShippingService
{
    // Origin selalu dari Jawa Barat
    protected $origin = 'Jawa Barat';
    
    // Data ongkir dari Jawa Barat ke provinsi lain
    protected $domesticRates = [
        'Aceh' => 35000,
        'Sumatera Utara' => 30000,
        'Sumatera Barat' => 25000,
        'Riau' => 25000,
        'Jambi' => 25000,
        'Sumatera Selatan' => 20000,
        'Bengkulu' => 25000,
        'Lampung' => 20000,
        'Kepulauan Bangka Belitung' => 25000,
        'Kepulauan Riau' => 30000,
        'DKI Jakarta' => 10000,
        'Jawa Barat' => 5000, // Dalam provinsi yang sama
        'Jawa Tengah' => 15000,
        'DI Yogyakarta' => 15000,
        'Jawa Timur' => 15000,
        'Banten' => 10000,
        'Bali' => 20000,
        'Nusa Tenggara Barat' => 25000,
        'Nusa Tenggara Timur' => 30000,
        'Kalimantan Barat' => 35000,
        'Kalimantan Tengah' => 35000,
        'Kalimantan Selatan' => 30000,
        'Kalimantan Timur' => 35000,
        'Kalimantan Utara' => 40000,
        'Sulawesi Utara' => 40000,
        'Sulawesi Tengah' => 35000,
        'Sulawesi Selatan' => 30000,
        'Sulawesi Tenggara' => 35000,
        'Gorontalo' => 40000,
        'Sulawesi Barat' => 35000,
        'Maluku' => 45000,
        'Maluku Utara' => 50000,
        'Papua Barat' => 60000,
        'Papua' => 60000,
    ];
    
    // Ongkir ke luar negeri flat 100k
    protected $internationalRate = 100000;
    
    /**
     * Hitung ongkir berdasarkan provinsi, negara tujuan, dan berat
     */
    public function calculate(?string $province, string $country, float $weight = 1): int
    {
        // Jika negara bukan Indonesia, gunakan tarif internasional
        if ($country !== 'Indonesia') {
            return $this->calculateInternationalRate($weight);
        }
        
        // Jika provinsi tidak ada dalam daftar, gunakan tarif default
        if (!isset($this->domesticRates[$province])) {
            return $this->calculateDefaultRate($weight);
        }
        
        // Hitung berdasarkan tarif provinsi dan berat
        $baseRate = $this->domesticRates[$province];
        return $this->calculateFinalRate($baseRate, $weight);
    }
    
    /**
     * Hitung tarif internasional berdasarkan berat
     */
    protected function calculateInternationalRate(float $weight): int
    {
        // Berat <= 1kg, gunakan tarif dasar
        if ($weight <= 1) {
            return $this->internationalRate;
        }
        
        // Untuk setiap kg tambahan, tambah 50% dari tarif dasar
        $additionalWeight = ceil($weight - 1);
        $additionalCost = $additionalWeight * ($this->internationalRate * 0.5);
        
        return (int) ($this->internationalRate + $additionalCost);
    }
    
    /**
     * Hitung tarif default untuk provinsi yang tidak terdaftar
     */
    protected function calculateDefaultRate(float $weight): int
    {
        // Default menggunakan tarif ke Jawa Tengah
        $baseRate = $this->domesticRates['Jawa Tengah'] ?? 15000;
        return $this->calculateFinalRate($baseRate, $weight);
    }
    
    /**
     * Hitung tarif final berdasarkan tarif dasar dan berat
     */
    protected function calculateFinalRate(int $baseRate, float $weight): int
    {
        // Jika berat <= 1kg, gunakan tarif dasar
        if ($weight <= 1) {
            return $baseRate;
        }
        
        // Untuk setiap kg tambahan, tambah 50% dari tarif dasar
        $additionalWeight = ceil($weight - 1);
        $additionalCost = $additionalWeight * ($baseRate * 0.5);
        
        return (int) ($baseRate + $additionalCost);
    }
}