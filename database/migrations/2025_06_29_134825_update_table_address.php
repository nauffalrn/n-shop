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
        // Hanya tambahkan kolom district tanpa mengubah kolom city yang sudah ada
        Schema::table('addresses', function (Blueprint $table) {
            // Tambahkan kolom district (kabupaten)
            $table->string('district')->nullable()->after('city');
        });
        
        // Optional: Set existing city values to allow NULL using raw SQL query
        DB::statement('ALTER TABLE addresses MODIFY city VARCHAR(255) NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Hapus kolom district
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn('district');
        });
        
        // Optional: Set city back to NOT NULL
        DB::statement('ALTER TABLE addresses MODIFY city VARCHAR(255) NOT NULL');
    }
};
