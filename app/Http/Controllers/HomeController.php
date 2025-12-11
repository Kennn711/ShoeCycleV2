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

    public function detailShoes($id)
    {
        $shoes = Shoes::with(['category', 'variants.images'])->findOrFail($id);

        return view('customer.detail-shoes', compact('shoes'));
    }
}
