<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Shoes;
use App\Models\Category;
use App\Models\ShoesVariant;
use App\Models\VariantImages;
use App\Models\Address;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Review;
use App\Models\Cart;
use App\Models\Reviews;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 0. Penyiapan File Gambar Dummy
        $sourcePath = public_path('assets/upload/testing/sepatu1.webp');

        if (File::exists($sourcePath)) {
            Storage::makeDirectory('public/profile-pictures');
            Storage::makeDirectory('public/variant-shoes');
            Storage::makeDirectory('public/proof-of-delivery');

            File::copy($sourcePath, storage_path('app/public/profile-pictures/sepatu1.webp'));
            File::copy($sourcePath, storage_path('app/public/variant-shoes/sepatu1.webp'));
            $this->command->info('Gambar dummy berhasil disalin ke Storage!');
        } else {
            $this->command->warn('PERINGATAN: File sepatu1.webp tidak ditemukan. Gambar akan broken.');
        }

        // 1. Seed Categories (6 Data)
        $categories = ['Running', 'Casual', 'Sneakers', 'Oxford', 'Loafers', 'Basketball'];
        foreach ($categories as $name) {
            Category::create(['category_name' => $name]);
        }

        // 2. Seed Shoes (20 Data Master - Menggunakan data Anda)
        $shoeNames = [
            ['Nike Air Zoom Pegasus 40', 1, 'Nike'],
            ['Adidas Ultraboost 23', 1, 'Adidas'],
            ['NB Fresh Foam 1080v13', 1, 'New Balance'],
            ['ASICS Gel-Nimbus 25', 1, 'ASICS'],
            ['Puma Velocity Nitro 2', 1, 'Puma'],
            ['Converse Chuck Taylor', 2, 'Converse'],
            ['Vans Old Skool Classic', 2, 'Vans'],
            ['Adidas Stan Smith', 2, 'Adidas'],
            ['Skechers Go Walk 6', 2, 'Skechers'],
            ['Nike Air Force 1', 3, 'Nike'],
            ['Yeezy Boost 350 V2', 3, 'Adidas'],
            ['Jordan 1 Retro High', 3, 'Nike'],
            ['NB 574 Core', 3, 'New Balance'],
            ['Clarks Tilden Cap', 4, 'Clarks'],
            ['Bostonian Maynor Cap', 4, 'Bostonian'],
            ['Florsheim Medfield', 4, 'Florsheim'],
            ['Cole Haan Penny Loafer', 5, 'Cole Haan'],
            ['Sperry Authentic', 5, 'Sperry'],
            ['Nike LeBron 21', 6, 'Nike'],
            ['Adidas Harden Vol. 7', 6, 'Adidas']
        ];

        foreach ($shoeNames as $shoe) {
            Shoes::create([
                'name' => $shoe[0],
                'slug' => Str::slug($shoe[0]) . '-' . Str::random(5),
                'category_id' => $shoe[1],
                'brand_name' => $shoe[2],
                'description' => 'Deskripsi dummy untuk ' . $shoe[0] . '. Sepatu berkualitas tinggi untuk kenyamanan maksimal.',
                'is_active' => true,
            ]);
        }

        // 3. Seed Users (Admin, 10 Driver, 50 Customers)
        User::create([
            'name' => "Admin ShoeCycle",
            'role' => 'admin',
            'email' => "admin@gmail.com",
            'password' => Hash::make('12345678'),
            'email_verified_at' => now()
        ]);

        for ($i = 1; $i <= 10; $i++) {
            User::create([
                'name' => "Kurir " . $i,
                'role' => 'driver',
                'email' => "driver{$i}@gmail.com",
                'password' => Hash::make('12345678'),
                'profile_picture' => 'profile-pictures/sepatu1.webp',
                'email_verified_at' => now(),
            ]);
        }

        for ($i = 1; $i <= 50; $i++) {
            $customer = User::create([
                'name' => "Customer " . $i,
                'role' => 'customer',
                'email' => "customer{$i}@gmail.com",
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
            ]);

            // Setiap customer punya 1-2 alamat
            for ($a = 0; $a < rand(1, 2); $a++) {
                Address::create([
                    'user_id' => $customer->id,
                    'recipient_name' => $customer->name,
                    'phone_number' => '0812' . rand(10000000, 99999999),
                    'label' => ['Home', 'Office'][rand(0, 1)],
                    'full_address' => "Jl. Testing No. " . rand(1, 100) . ", Jawa Timur",
                    'district' => 'Magersari',
                    'village' => 'Gedongan',
                    'is_primary' => $a == 0,
                    'latitude' => -7.47 + (rand(-100, 100) / 10000),
                    'longitude' => 112.43 + (rand(-100, 100) / 10000),
                ]);
            }
        }

        // 4. Seed Shoes Variants (100 Varian - rata-rata 5 per sepatu)
        $colors = ['Black', 'White', 'Red', 'Navy', 'Grey'];
        $sizes = [39, 40, 41, 42, 43, 44];

        for ($i = 0; $i < 100; $i++) {
            $shoe = Shoes::inRandomOrder()->first();
            $color = $colors[array_rand($colors)];
            $size = $sizes[array_rand($sizes)];

            $variant = ShoesVariant::create([
                'shoe_id' => $shoe->id,
                'color' => $color,
                'color_code' => '#000000',
                'size' => $size,
                'price' => rand(500, 2500) * 1000,
                'stock' => rand(10, 50),
                'sku' => strtoupper(substr($shoe->name, 0, 3)) . "-$size-" . Str::random(5),
                'is_available' => true,
            ]);

            VariantImages::create([
                'shoe_variant_id' => $variant->id,
                'image_path' => 'variant-shoes/sepatu1.webp',
                'is_primary' => true,
                'order' => 1,
            ]);
        }

        // 5. Seed Transactions (50 Data Transaksi)
        $statuses = ['pending', 'settlement', 'expire', 'cancel'];
        $t_statuses = ['pending', 'processing', 'shipping', 'delivered'];

        for ($i = 1; $i <= 50; $i++) {
            $customer = User::where('role', 'customer')->inRandomOrder()->first();
            $address = Address::where('user_id', $customer->id)->first();
            $driver = User::where('role', 'driver')->inRandomOrder()->first();

            $pay_status = $statuses[array_rand($statuses)];
            $trans_status = ($pay_status == 'settlement') ? $t_statuses[array_rand($t_statuses)] : 'pending';

            $transaction = Transaction::create([
                'customer_id' => $customer->id,
                'address_id' => $address->id,
                'courier_id' => ($trans_status != 'pending') ? $driver->id : null,
                'invoice' => 'INV-' . date('Ymd') . '-' . Str::upper(Str::random(6)),
                'subtotal' => 0, // Akan diupdate
                'shipping_cost' => 15000,
                'admin_fee' => 2500,
                'total_price' => 0, // Akan diupdate
                'payment_status' => $pay_status,
                'transaction_status' => $trans_status,
                'snap_token' => Str::random(32),
                'proof_of_delivery' => ($trans_status == 'delivered') ? 'variant-shoes/sepatu1.webp' : null,
            ]);

            // Isi Detail Transaksi (1-3 item per transaksi)
            $subtotal = 0;
            $items = ShoesVariant::inRandomOrder()->take(rand(1, 3))->get();
            foreach ($items as $item) {
                $qty = rand(1, 2);
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'variant_id' => $item->id,
                    'qty' => $qty,
                    'price' => $item->price,
                ]);
                $subtotal += ($item->price * $qty);
            }

            $transaction->update([
                'subtotal' => $subtotal,
                'total_price' => $subtotal + 15000 + 2500
            ]);

            // 6. Seed Reviews (Hanya untuk yang statusnya delivered)
            if ($trans_status == 'delivered') {
                Reviews::create([
                    'user_id' => $customer->id,
                    'shoe_id' => $items->first()->shoe_id,
                    'transaction_id' => $transaction->id,
                    'rating' => rand(4, 5),
                    'comment' => 'Sepatunya sangat nyaman dan pengiriman cepat!',
                ]);
            }
        }

        // 7. Seed Cart (Beberapa item di keranjang customer)
        for ($i = 0; $i < 20; $i++) {
            $cust = User::where('role', 'customer')->inRandomOrder()->first();
            $var = ShoesVariant::inRandomOrder()->first();

            Cart::updateOrCreate(
                ['user_id' => $cust->id, 'shoes_variant_id' => $var->id],
                ['quantity' => rand(1, 2)]
            );
        }

        $this->command->info('Seeding 50+ data berhasil diselesaikan!');
    }
}
