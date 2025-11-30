<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $categoryData = [
            [
                'category_name' => 'Running',
            ],
            [
                'category_name' => 'Casual',
            ],
            [
                'category_name' => 'Sneakers',
            ],
            [
                'category_name' => 'Oxford',
            ],
            [
                'category_name' => 'Loafers',
            ],
            [
                'category_name' => 'Basketball',
            ]
        ];

        foreach ($categoryData as $key => $val) {
            Category::create($val);
        }
    }
}
