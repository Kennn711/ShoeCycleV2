<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Shoes;
use App\Models\ShoesVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        // Ambil data keranjang user yang sedang login
        // Eager loading: Variant -> Shoe (untuk nama/brand) -> Images (untuk foto)
        $cartItems = Cart::with(['variant.shoe', 'variant.images'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        // Hitung Grand Total Awal
        $grandTotal = $cartItems->sum(function ($item) {
            return $item->quantity * $item->variant->price;
        });

        return view('customer.cart.cart', compact('cartItems', 'grandTotal'));
    }

    public function updateQty(Request $request, $id)
    {
        $cart = Cart::where('user_id', Auth::id())->where('id', $id)->first();

        if (!$cart) {
            return response()->json(['status' => 'error', 'message' => 'Item tidak ditemukan'], 404);
        }

        // Validasi Stok
        if ($request->qty > $cart->variant->stock) {
            return response()->json([
                'status' => 'error',
                'message' => 'Stok maksimal tercapai',
                'reset_qty' => $cart->variant->stock
            ], 400);
        }

        if ($request->qty < 1) {
            return response()->json(['status' => 'error', 'message' => 'Minimal pembelian 1'], 400);
        }

        $cart->quantity = $request->qty;
        $cart->save();

        // Hitung ulang total belanja user
        $newGrandTotal = Cart::where('user_id', Auth::id())
            ->get()
            ->sum(function ($item) {
                return $item->quantity * $item->variant->price;
            });

        return response()->json([
            'status' => 'success',
            'grand_total' => 'Rp ' . number_format($newGrandTotal, 0, ',', '.')
        ]);
    }

    public function addToCart(Request $request)
    {
        // 1. Validasi Input dari AJAX
        $request->validate([
            'shoe_slug' => 'required',
            'color' => 'required',
            'size' => 'required',
            'qty' => 'required|integer|min:1'
        ]);

        // 2. Cari Data Sepatu (Parent)
        $shoe = Shoes::where('slug', $request->shoe_slug)->firstOrFail();

        // 3. Cari Variant Spesifik (Berdasarkan Warna & Size pilihan user)
        $variant = ShoesVariant::where('shoe_id', $shoe->id)
            ->where('color', $request->color)
            ->where('size', $request->size)
            ->first();

        // Jika varian tidak ditemukan (misal user memanipulasi HTML)
        if (!$variant) {
            return response()->json(['status' => 'error', 'message' => 'Varian produk tidak ditemukan.'], 404);
        }

        // 4. Cek Stok Database vs Request
        if ($variant->stock < $request->qty) {
            return response()->json(['status' => 'error', 'message' => 'Stok tidak mencukupi.'], 400);
        }

        // 5. Simpan / Update Keranjang
        // Cek apakah user ini sudah punya varian ini di keranjang?
        $cart = Cart::where('user_id', Auth::id())
            ->where('shoes_variant_id', $variant->id)
            ->first();

        if ($cart) {
            // Jika sudah ada, tambahkan quantity-nya
            $newQty = $cart->quantity + $request->qty;

            // Cek stok lagi agar tidak overstock saat dijumlah
            if ($newQty > $variant->stock) {
                return response()->json(['status' => 'error', 'message' => 'Total barang di keranjang melebihi stok tersedia.'], 400);
            }

            $cart->quantity = $newQty;
            $cart->save();
        } else {
            // Jika belum ada, buat baru
            Cart::create([
                'user_id' => Auth::id(),
                'shoes_variant_id' => $variant->id,
                'quantity' => $request->qty
            ]);
        }

        // 6. Return response sukses + update badge keranjang
        return response()->json([
            'status' => 'success',
            'message' => 'Produk berhasil masuk keranjang!',
            'cart_count' => Cart::where('user_id', Auth::id())->count()
        ]);
    }


    public function destroy($id)
    {
        $cart = Cart::where('user_id', Auth::id())->where('id', $id)->first();
        if ($cart) {
            $cart->delete();
            return response()->json(['status' => 'success', 'message' => 'Item dihapus']);
        }
        return response()->json(['status' => 'error', 'message' => 'Gagal menghapus'], 400);
    }
}
