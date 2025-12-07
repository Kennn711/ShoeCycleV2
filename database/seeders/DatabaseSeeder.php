<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Shoe;
use App\Models\Shoes;
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
        // Seed Categories
        $categoryData = [
            ['category_name' => 'Running'],
            ['category_name' => 'Casual'],
            ['category_name' => 'Sneakers'],
            ['category_name' => 'Oxford'],
            ['category_name' => 'Loafers'],
            ['category_name' => 'Basketball'],
        ];

        foreach ($categoryData as $val) {
            Category::create($val);
        }

        // Seed Shoes (20 data)
        $shoeData = [
            // Running Shoes (5)
            [
                'name' => 'Nike Air Zoom Pegasus 40',
                'slug' => 'nike-air-zoom-pegasus-40',
                'category_id' => 1,
                'brand_name' => 'Nike',
                'description' => 'Sepatu lari dengan teknologi Air Zoom untuk responsivitas maksimal. Cocok untuk pelari pemula hingga profesional dengan cushioning yang nyaman untuk jarak jauh.',
                'is_active' => true,
            ],
            [
                'name' => 'Adidas Ultraboost 23',
                'slug' => 'adidas-ultraboost-23',
                'category_id' => 1,
                'brand_name' => 'Adidas',
                'description' => 'Sepatu running premium dengan teknologi Boost yang memberikan energy return luar biasa. Didesain untuk performa maksimal dengan kenyamanan sepanjang hari.',
                'is_active' => true,
            ],
            [
                'name' => 'New Balance Fresh Foam 1080v13',
                'slug' => 'new-balance-fresh-foam-1080v13',
                'category_id' => 1,
                'brand_name' => 'New Balance',
                'description' => 'Kombinasi sempurna antara cushioning dan stabilitas untuk pelari jarak jauh. Fresh Foam memberikan langkah yang empuk namun responsif.',
                'is_active' => true,
            ],
            [
                'name' => 'ASICS Gel-Nimbus 25',
                'slug' => 'asics-gel-nimbus-25',
                'category_id' => 1,
                'brand_name' => 'ASICS',
                'description' => 'Sepatu lari dengan GEL technology untuk shock absorption superior. Ideal untuk pelari yang membutuhkan support ekstra dan kenyamanan maksimal.',
                'is_active' => true,
            ],
            [
                'name' => 'Puma Velocity Nitro 2',
                'slug' => 'puma-velocity-nitro-2',
                'category_id' => 1,
                'brand_name' => 'Puma',
                'description' => 'Sepatu running ringan dengan teknologi Nitro Foam yang responsif. Desain modern dengan performa tinggi untuk berbagai jenis latihan.',
                'is_active' => true,
            ],

            // Casual Shoes (4)
            [
                'name' => 'Converse Chuck Taylor All Star',
                'slug' => 'converse-chuck-taylor-all-star',
                'category_id' => 2,
                'brand_name' => 'Converse',
                'description' => 'Sepatu kasual ikonik dengan desain timeless. Sempurna untuk gaya santai sehari-hari dengan berbagai pilihan warna dan material.',
                'is_active' => true,
            ],
            [
                'name' => 'Vans Old Skool Classic',
                'slug' => 'vans-old-skool-classic',
                'category_id' => 2,
                'brand_name' => 'Vans',
                'description' => 'Sepatu skateboarding yang menjadi ikon fashion streetwear. Desain sederhana dengan stripe khas Vans yang mudah dipadukan dengan berbagai outfit.',
                'is_active' => true,
            ],
            [
                'name' => 'Adidas Stan Smith',
                'slug' => 'adidas-stan-smith',
                'category_id' => 2,
                'brand_name' => 'Adidas',
                'description' => 'Sepatu tenis klasik yang menjadi fashion staple. Desain minimalis dengan upper kulit premium untuk tampilan elegan kasual.',
                'is_active' => true,
            ],
            [
                'name' => 'Skechers Go Walk 6',
                'slug' => 'skechers-go-walk-6',
                'category_id' => 2,
                'brand_name' => 'Skechers',
                'description' => 'Sepatu walking super ringan dan nyaman untuk aktivitas sehari-hari. Teknologi Air-Cooled Goga Mat memberikan kenyamanan ekstra.',
                'is_active' => false,
            ],

            // Sneakers (4)
            [
                'name' => 'Nike Air Force 1',
                'slug' => 'nike-air-force-1',
                'category_id' => 3,
                'brand_name' => 'Nike',
                'description' => 'Sneakers legendaris dengan desain klasik yang tidak lekang oleh waktu. Air cushioning memberikan kenyamanan maksimal untuk pemakaian seharian.',
                'is_active' => true,
            ],
            [
                'name' => 'Adidas Yeezy Boost 350 V2',
                'slug' => 'adidas-yeezy-boost-350-v2',
                'category_id' => 3,
                'brand_name' => 'Adidas',
                'description' => 'Sneakers premium hasil kolaborasi dengan Kanye West. Desain futuristik dengan Boost technology untuk kenyamanan dan style statement.',
                'is_active' => true,
            ],
            [
                'name' => 'Jordan 1 Retro High OG',
                'slug' => 'jordan-1-retro-high-og',
                'category_id' => 3,
                'brand_name' => 'Nike',
                'description' => 'Sneakers ikonik Michael Jordan dengan desain high-top klasik. Koleksi wajib bagi sneakerhead dengan nilai investasi tinggi.',
                'is_active' => true,
            ],
            [
                'name' => 'New Balance 574 Core',
                'slug' => 'new-balance-574-core',
                'category_id' => 3,
                'brand_name' => 'New Balance',
                'description' => 'Sneakers retro dengan kombinasi suede dan mesh yang nyaman. Desain versatile yang cocok untuk berbagai kesempatan.',
                'is_active' => true,
            ],

            // Oxford Shoes (3)
            [
                'name' => 'Clarks Tilden Cap Oxford',
                'slug' => 'clarks-tilden-cap-oxford',
                'category_id' => 4,
                'brand_name' => 'Clarks',
                'description' => 'Sepatu formal Oxford dengan upper kulit premium. Desain cap-toe klasik yang sempurna untuk acara formal dan profesional.',
                'is_active' => true,
            ],
            [
                'name' => 'Bostonian Maynor Cap Toe',
                'slug' => 'bostonian-maynor-cap-toe',
                'category_id' => 4,
                'brand_name' => 'Bostonian',
                'description' => 'Oxford shoes dengan craftsmanship detail tinggi. Cushioned footbed memberikan kenyamanan sepanjang hari dalam suasana formal.',
                'is_active' => true,
            ],
            [
                'name' => 'Florsheim Medfield Oxford',
                'slug' => 'florsheim-medfield-oxford',
                'category_id' => 4,
                'brand_name' => 'Florsheim',
                'description' => 'Sepatu dress Oxford dengan desain elegan dan material berkualitas tinggi. Ideal untuk business professional dan acara formal.',
                'is_active' => true,
            ],

            // Loafers (2)
            [
                'name' => 'Cole Haan Pinch Penny Loafer',
                'slug' => 'cole-haan-pinch-penny-loafer',
                'category_id' => 5,
                'brand_name' => 'Cole Haan',
                'description' => 'Loafers klasik dengan desain penny strap yang timeless. Kombinasi gaya dan kenyamanan untuk smart casual look.',
                'is_active' => true,
            ],
            [
                'name' => 'Sperry Authentic Original',
                'slug' => 'sperry-authentic-original',
                'category_id' => 5,
                'brand_name' => 'Sperry',
                'description' => 'Boat shoes dengan desain nautical yang ikonik. Upper kulit dengan non-marking rubber outsole untuk grip maksimal.',
                'is_active' => true,
            ],

            // Basketball Shoes (2)
            [
                'name' => 'Nike LeBron 21',
                'slug' => 'nike-lebron-21',
                'category_id' => 6,
                'brand_name' => 'Nike',
                'description' => 'Sepatu basketball signature LeBron James dengan teknologi Zoom Air. Support dan responsivitas tinggi untuk performa optimal di lapangan.',
                'is_active' => true,
            ],
            [
                'name' => 'Adidas Harden Vol. 7',
                'slug' => 'adidas-harden-vol-7',
                'category_id' => 6,
                'brand_name' => 'Adidas',
                'description' => 'Sepatu basketball dengan Boost cushioning untuk energy return maksimal. Desain lightweight dengan traction pattern untuk quick movement.',
                'is_active' => true,
            ],
        ];

        foreach ($shoeData as $val) {
            Shoes::create($val);
        }
    }
}
