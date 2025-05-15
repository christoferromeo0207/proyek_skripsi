<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data kategori yang ingin ditambahkan
        $categories = [
            ['name' => 'Bank', 'slug' => 'bank', 'color' => 'red'],
            ['name' => 'Klinik', 'slug' => 'klinik', 'color' => 'green'],
            ['name' => 'Rumah Sakit', 'slug' => 'rumah-sakit', 'color' => 'blue'],
            ['name' => 'Asuransi', 'slug' => 'asuransi', 'color' => 'yellow'],
            ['name' => 'Laboratorium', 'slug' => 'laboratorium', 'color' => 'purple'],
            ['name' => 'Perusahaan', 'slug' => 'perusahaan', 'color' => 'orange'],
            ['name' => 'Agen Asuransi', 'slug' => 'agen-asuransi', 'color' => 'pink'],
            ['name' => 'Hotel', 'slug' => 'hotel', 'color' => 'grey'],
            ['name' => 'Komunitas', 'slug' => 'komunitas', 'color' => 'brown']
        ];

        // Menambahkan data kategori jika belum ada
        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => $category['slug']], 
                ['name' => $category['name'], 'color' => $category['color']] 
            );
        }
    }
}
