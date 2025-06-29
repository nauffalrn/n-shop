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
            $table->unsignedBigInteger('address_id')->nullable()->after('discount');
            $table->foreign('address_id')->references('id')->on('addresses')->nullOnDelete();
            $table->decimal('shipping_cost', 10, 2)->default(0)->after('address_id');
            $table->text('shipping_address')->nullable()->after('shipping_cost');
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
            $table->dropForeign(['address_id']);
            $table->dropColumn(['address_id', 'shipping_cost', 'shipping_address']);
        });
    }
};
