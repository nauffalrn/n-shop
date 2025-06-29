<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateProductCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Buat tabel pivot untuk relasi many-to-many
        Schema::create('product_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            // Kombinasi product_id dan category_id harus unik
            $table->unique(['product_id', 'category_id']);
        });
        
        // Salin data dari relasi lama ke tabel pivot baru
        // Ini memastikan data kategori yang sudah ada tidak hilang
        DB::statement('
            INSERT INTO product_category (product_id, category_id, created_at, updated_at) 
            SELECT id, category_id, NOW(), NOW() FROM products 
            WHERE category_id IS NOT NULL
        ');
        
        // TIDAK menghapus kolom category_id dari tabel products
        // Kita tetap mempertahankannya sebagai "kategori utama"
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_category');
    }
}
