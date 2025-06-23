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
        Schema::table('products', function (Blueprint $table) {
            // Hapus column image lama, ganti dengan images (JSON)
            $table->dropColumn('image');
            $table->json('images')->after('description'); // multiple images
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null')->after('images');
            $table->decimal('weight', 8, 2)->nullable()->after('stock');
            $table->decimal('rating', 2, 1)->default(0)->after('weight');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn(['images', 'category_id', 'weight', 'rating']);
            $table->string('image')->after('description'); // restore image column
        });
    }
};
