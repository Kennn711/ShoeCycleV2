<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Shoes;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // 1. Kategori
        $categories = Category::withCount('shoes')->get();

        // 2. New Arrivals (Logic Harga Diperbaiki)
        $newArrivals = Shoes::with(['category', 'variants.images' => function ($q) {
            $q->where('is_primary', true);
        }])
            ->where('is_active', true)
            ->latest()
            ->take(8)
            ->get()
            ->map(function ($shoe) {
                $prices = $shoe->variants->pluck('price');

                if ($prices->isEmpty()) {
                    $shoe->price_display = 'Stok Habis';
                } else {
                    $min = $prices->min();
                    $max = $prices->max();

                    if ($min == $max) {
                        // KASUS 1: Harga Tunggal (Sama semua atau cuma 1 varian)
                        // Output: Rp 1.500.000
                        $shoe->price_display = 'Rp ' . number_format($min, 0, ',', '.');
                    } else {
                        // KASUS 2: Range Harga
                        // Output: Rp 1.500.000 - Rp 2.000.000
                        // Perhatikan penambahan ' - Rp ' di tengah
                        $shoe->price_display = 'Rp ' . number_format($min, 0, ',', '.') . ' - Rp ' . number_format($max, 0, ',', '.');
                    }
                }
                return $shoe;
            });

        // 3. Brand
        $brands = Shoes::select('brand_name')->distinct()->pluck('brand_name');

        return view('landing-page', compact('categories', 'newArrivals', 'brands'));
    }

    public function detailShoes($slug)
    {
        // 1. Ambil data sepatu berdasarkan Slug
        // Eager load: Kategori, Varian, dan Gambar Varian
        $shoe = Shoes::with(['category', 'variants.images' => function ($q) {
            $q->orderBy('order', 'asc');
        }])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // 2. Siapkan Data Varian untuk Frontend (Grouping)
        // Kita butuh list warna unik dan ukuran unik untuk ditampilkan di tombol
        $uniqueColors = $shoe->variants->unique('color')->values();

        // Kita juga butuh mapping data lengkap (Stok, Harga, Gambar) per kombinasi Warna & Ukuran
        // Format: [ "Hitam_42" => { price: 500000, stock: 5, images: [...] } ]
        $variantMap = [];
        $availableSizesPerColor = [];

        foreach ($shoe->variants as $variant) {
            $key = $variant->color . '_' . $variant->size; // Key kombinasi

            // Simpan data detail per varian
            $variantMap[$key] = [
                'id' => $variant->id,
                'price' => $variant->price,
                'stock' => $variant->stock,
                'sku' => $variant->sku,
                'formatted_price' => 'Rp ' . number_format($variant->price, 0, ',', '.'),
                'is_available' => $variant->is_available && $variant->stock > 0,
                'images' => $variant->images->map(function ($img) {
                    return asset('storage/' . $img->image_path);
                })
            ];

            // Grouping Size berdasarkan Warna (agar user tahu size mana yang ready untuk warna tertentu)
            $availableSizesPerColor[$variant->color][] = $variant->size;
        }

        // Ambil range harga awal untuk tampilan default
        $minPrice = $shoe->variants->min('price');
        $maxPrice = $shoe->variants->max('price');
        $priceRange = ($minPrice == $maxPrice)
            ? 'Rp ' . number_format($minPrice, 0, ',', '.')
            : 'Rp ' . number_format($minPrice, 0, ',', '.') . ' - Rp ' . number_format($maxPrice, 0, ',', '.');

        // Ambil gambar default (dari varian pertama) untuk tampilan awal
        $defaultImages = $shoe->variants->first() ? $shoe->variants->first()->images : collect([]);

        return view('customer.detail-shoes', compact(
            'shoe',
            'uniqueColors',
            'variantMap',
            'priceRange',
            'availableSizesPerColor',
            'defaultImages'
        ));
    }

    public function allCategory()
    {
        // Eager loading untuk performa agar tidak lambat (N+1 query)
        $categories = Category::with(['shoes' => function ($query) {
            $query->where('is_active', true);
        }, 'shoes', 'shoes.variants.images'])->get();

        return view('customer.category', compact('categories'));
    }

    public function shoesCollection(Request $request)
    {
        // 1. Query dasar: hanya sepatu aktif dengan eager loading
        $query = Shoes::with(['category', 'variants.images'])->where('is_active', true);

        // 2. Logika Search (Nama Sepatu atau Brand)
        if ($request->has('q') && $request->q != '') {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%')
                    ->orWhere('brand_name', 'like', '%' . $request->q . '%');
            });
        }

        // 3. Logika Filter Kategori
        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }

        // 4. Ambil data dengan Pagination (misal: 12 produk per halaman)
        $shoes = $query->latest()->paginate(12);

        // 5. Ambil semua kategori untuk ditampilkan di sidebar filter
        $categories = Category::all();

        return view('customer.shoes-collection', compact('shoes', 'categories'));
    }
}
