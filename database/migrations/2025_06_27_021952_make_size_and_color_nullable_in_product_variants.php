<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class MakeSizeAndColorNullableInProductVariants extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE product_variants MODIFY size VARCHAR(255) NULL');
        DB::statement('ALTER TABLE product_variants MODIFY color VARCHAR(255) NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE product_variants MODIFY size VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE product_variants MODIFY color VARCHAR(255) NOT NULL');
    }
}
