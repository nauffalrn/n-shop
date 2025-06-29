<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Gunakan SQL langsung untuk menghindari kebutuhan akan doctrine/dbal
        DB::statement('ALTER TABLE products MODIFY weight DECIMAL(10,3) NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Kembali ke tipe data sebelumnya (float atau double)
        DB::statement('ALTER TABLE products MODIFY weight FLOAT NULL');
    }
};
