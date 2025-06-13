<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create(['name' => 'APPAREL']);
        Category::create(['name' => 'KIDS APPAREL']);
        Category::create(['name' => 'LINEN']);
        Category::create(['name' => 'OTHERS']);
    }
}
