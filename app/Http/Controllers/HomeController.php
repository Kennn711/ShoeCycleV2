<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Shoes;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // 1. Ambil Kategori (dengan jumlah produknya)
        $categories = Category::withCount('shoes')->get();

        // 2. Ambil Sepatu Terbaru (Limit 8)
        // Kita eager load 'variants' dan 'variants.images' untuk menampilkan harga & foto
        $newArrivals = Shoes::with(['category', 'variants.images' => function ($q) {
            $q->where('is_primary', true); // Ambil hanya gambar utama
        }])
            ->where('is_active', true)
            ->latest()
            ->take(8)
            ->get();

        // 3. Ambil Brand Unik untuk logo section
        $brands = Shoes::select('brand_name')->distinct()->pluck('brand_name');

        return view('landing-page', compact('categories', 'newArrivals', 'brands'));
    }
}
