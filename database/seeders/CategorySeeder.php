<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            'Fashion Pria',
            'Fashion Wanita', 
            'Elektronik',
            'Mainan & Hobi'
        ];

        foreach ($categories as $category) {
            Category::create(['name' => $category]);
        }
    }
}