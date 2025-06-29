<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;

class UpdateOrderStatusSeeder extends Seeder
{
    public function run()
    {
        // Update order yang sudah dibayar
        Order::where('is_paid', true)->update([
            'payment_status' => Order::PAYMENT_CONFIRMED
        ]);
        
        // Update order yang belum dibayar tapi sudah upload bukti
        Order::where('is_paid', false)
             ->whereNotNull('payment_receipt')
             ->update([
                'payment_status' => Order::PAYMENT_AWAITING_CONFIRMATION
             ]);
        
        // Update order yang belum dibayar dan belum upload bukti
        Order::where('is_paid', false)
             ->whereNull('payment_receipt')
             ->update([
                'payment_status' => Order::PAYMENT_PENDING
             ]);
    }
}