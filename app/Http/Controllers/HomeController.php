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
            ->withAvg('reviews as average_rating', 'rating')
            // 2. Hitung Jumlah Ulasan
            ->withCount('reviews')
            // 3. Hitung Total Terjual (Berdasarkan transaksi lunas)
            ->withSum(['transactionDetails as total_sold' => function ($q) {
                $q->whereHas('transaction', function ($sq) {
                    $sq->whereIn('payment_status', ['settlement', 'capture']);
                });
            }], 'qty')
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
                        $shoe->price_display = 'Rp ' . number_format($min, 0, ',', '.');
                    } else {
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
        // Tambahkan eager load 'reviews.user' agar bisa menampilkan nama pengulas
        $shoe = Shoes::with(['category', 'variants.images' => function ($q) {
            $q->orderBy('order', 'asc');
        }, 'reviews.user']) // Relasi ke tabel reviews dan user
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // 2. Hitung Statistik Rating
        // Mengambil rata-rata kolom 'rating' dari tabel reviews
        $avgRating = round($shoe->reviews()->avg('rating'), 1) ?: 0;
        $totalReviews = $shoe->reviews()->count();

        // 3. Siapkan Data Varian (Tetap seperti kode Anda sebelumnya)
        $uniqueColors = $shoe->variants->unique('color')->values();
        $variantMap = [];
        $availableSizesPerColor = [];

        foreach ($shoe->variants as $variant) {
            $key = $variant->color . '_' . $variant->size;
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
            $availableSizesPerColor[$variant->color][] = $variant->size;
        }

        $minPrice = $shoe->variants->min('price');
        $maxPrice = $shoe->variants->max('price');
        $priceRange = ($minPrice == $maxPrice)
            ? 'Rp ' . number_format($minPrice, 0, ',', '.')
            : 'Rp ' . number_format($minPrice, 0, ',', '.') . ' - Rp ' . number_format($maxPrice, 0, ',', '.');

        $defaultImages = $shoe->variants->first() ? $shoe->variants->first()->images : collect([]);

        return view('customer.detail-shoes', compact(
            'shoe',
            'uniqueColors',
            'variantMap',
            'priceRange',
            'availableSizesPerColor',
            'defaultImages',
            'avgRating',    // Data baru
            'totalReviews'  // Data baru
        ));
    }

    public function allCategory()
    {
        $categories = Category::with(['shoes' => function ($query) {
            $query->where('is_active', true)
                // Load Gambar agar tidak hilang
                ->with(['variants.images' => function ($q) {
                    $q->orderBy('order', 'asc');
                }])
                ->withAvg('reviews as average_rating', 'rating')
                ->withCount('reviews')
                ->withSum(['transactionDetails as total_sold' => function ($q) {
                    $q->whereHas('transaction', function ($sq) {
                        $sq->whereIn('payment_status', ['settlement', 'capture']);
                    });
                }], 'qty');
        }])->get();

        return view('customer.category', compact('categories'));
    }

    public function shoesCollection(Request $request)
    {
        $query = Shoes::with(['category', 'variants.images'])->where('is_active', true)
            ->withAvg('reviews as average_rating', 'rating')
            ->withCount('reviews')
            ->withSum(['transactionDetails as total_sold' => function ($q) {
                $q->whereHas('transaction', function ($sq) {
                    $sq->whereIn('payment_status', ['settlement', 'capture']);
                });
            }], 'qty');

        if ($request->has('q') && $request->q != '') {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%')
                    ->orWhere('brand_name', 'like', '%' . $request->q . '%');
            });
        }

        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }

        $shoes = $query->latest()->paginate(12)->through(function ($shoe) {
            $prices = $shoe->variants->pluck('price');
            if ($prices->isEmpty()) {
                $shoe->price_display = 'Stok Habis';
            } else {
                $min = $prices->min();
                $max = $prices->max();
                $shoe->price_display = ($min == $max)
                    ? 'Rp ' . number_format($min, 0, ',', '.')
                    : 'Rp ' . number_format($min, 0, ',', '.') . ' - Rp ' . number_format($max, 0, ',', '.');
            }
            return $shoe;
        });

        $categories = Category::all();

        return view('customer.shoes-collection', compact('shoes', 'categories'));
    }
}
