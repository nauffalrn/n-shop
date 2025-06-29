<?php

use Illuminate\Database\Migrations\Migration;
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
        // Gunakan SQL langsung untuk mengubah tipe kolom
        DB::statement('ALTER TABLE products MODIFY description TEXT');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Kembalikan ke VARCHAR jika rollback
        DB::statement('ALTER TABLE products MODIFY description VARCHAR(255)');
    }
};
