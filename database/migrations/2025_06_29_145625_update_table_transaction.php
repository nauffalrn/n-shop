<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Tambahkan kolom payment_status yang lebih fleksibel
            // pending: belum upload bukti, awaiting_confirmation: sudah upload menunggu konfirmasi
            // confirmed: sudah dikonfirmasi, rejected: ditolak
            $table->string('payment_status')->default('pending')->after('is_paid');
            $table->text('rejection_reason')->nullable()->after('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_status', 'rejection_reason']);
        });
    }
};
