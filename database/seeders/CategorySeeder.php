<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Atasan', 'slug' => Str::slug('Atasan')],
            ['name' => 'Bawahan', 'slug' => Str::slug('Bawahan')],
            ['name' => 'Outerwear', 'slug' => Str::slug('Outerwear')],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
