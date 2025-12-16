<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil ID items dari URL (?items=1,2,3)
        $selectedItemIds = $request->query('items');

        $query = Cart::with(['variant.shoe', 'variant.images'])
            ->where('user_id', Auth::id());

        // Jika ada filter item tertentu (dari checkbox cart), filter query-nya
        if ($selectedItemIds) {
            $idsArray = explode(',', $selectedItemIds);
            $query->whereIn('id', $idsArray);
        }

        $cartItems = $query->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Tidak ada barang yang dipilih.');
        }

        $subtotal = $cartItems->sum(function ($item) {
            return $item->quantity * $item->variant->price;
        });

        $adminFee = 1000;

        // 2. AMBIL ALAMAT USER (UPDATED)
        $user = Auth::user();

        // Cari alamat utama, jika tidak ada ambil alamat terakhir dibuat
        $address = $user->primaryAddress ?? $user->addresses()->latest()->first();

        // 3. Konfigurasi Toko (Tetap sama)
        $storeConfig = [
            'lat' => -7.472613,
            'lng' => 112.433912,
            'base_shipping_cost' => 5000,
            'cost_per_km' => 2500,
        ];

        return view('customer.checkout.checkout', compact(
            'cartItems',
            'subtotal',
            'adminFee',
            'address', // Ini sekarang objek model Address, bukan string/null
            'user',
            'storeConfig'
        ));
    }
}
