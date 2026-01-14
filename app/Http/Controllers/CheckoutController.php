<?php

namespace App\Http\Controllers;

use Midtrans\Notification;
use App\Models\Cart;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // 1. Cek apakah ada transaksi pending yang belum dibayar
        $pendingTransaction = Transaction::where('customer_id', $user->id)
            ->where('payment_status', 'pending')
            ->latest()
            ->first();

        // 2. Ambil data keranjang
        $selectedItemIds = $request->query('items');
        $query = Cart::with(['variant.shoe', 'variant.images'])->where('user_id', $user->id);

        if ($selectedItemIds) {
            $idsArray = explode(',', $selectedItemIds);
            $query->whereIn('id', $idsArray);
        }
        $cartItems = $query->get();

        // 3. JIKA keranjang kosong DAN TIDAK ADA transaksi pending, baru redirect ke cart
        // Ini mencegah halaman "Not Found" atau hilang saat refresh setelah konfirmasi
        if ($cartItems->isEmpty() && !$pendingTransaction) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja kosong.');
        }

        $subtotal = $cartItems->sum(fn($item) => $item->quantity * $item->variant->price);
        $adminFee = 1000;
        $userAddresses = $user->addresses()->orderBy('is_primary', 'desc')->get();
        $address = $user->primaryAddress ?? $userAddresses->first();
        $storeConfig = ['lat' => -7.472613, 'lng' => 112.433912, 'base_shipping_cost' => 5000, 'cost_per_km' => 2500];

        return view('customer.checkout.checkout', compact(
            'cartItems',
            'subtotal',
            'adminFee',
            'address',
            'userAddresses',
            'user',
            'storeConfig',
            'pendingTransaction'
        ));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = Auth::user();
            // Ambil item keranjang beserta relasi variannya
            $cartItems = Cart::with('variant')->where('user_id', $user->id)->get();

            if ($cartItems->isEmpty()) {
                return response()->json(['message' => 'Keranjang Anda kosong.'], 422);
            }

            // 1. Buat Header Transaksi
            $invoice = 'INV-' . strtoupper(bin2hex(random_bytes(3))) . '-' . time();

            $transaction = Transaction::create([
                'customer_id'        => $user->id,
                'address_id'         => $request->address_id,
                'invoice'            => $invoice,
                'subtotal'           => $request->subtotal,
                'shipping_cost'      => $request->shipping_cost,
                'admin_fee'          => $request->admin_fee,
                'total_price'        => $request->total_price,
                'payment_status'     => 'pending',
                'transaction_status' => 'pending',
            ]);

            // 2. Simpan Detail & Catatan
            foreach ($cartItems as $item) {
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'variant_id'     => $item->variant_id ?? $item->shoes_variant_id,
                    'qty'            => $item->quantity,
                    'price'          => $item->variant->price,
                    'notes'          => $request->notes[$item->id] ?? null,
                ]);
            }

            // 3. Konfigurasi Midtrans
            Config::$serverKey = config('midtrans.server_key');
            Config::$isProduction = config('midtrans.is_production');
            Config::$isSanitized = true;
            Config::$is3ds = true;

            $params = [
                'transaction_details' => [
                    'order_id'     => $invoice,
                    'gross_amount' => (int) $request->total_price,
                ],
                'callbacks' => [
                    'finish' => route('my-order.index'),
                ],
                'customer_details' => [
                    'first_name' => $user->name,
                    'email'      => $user->email,
                    'phone'      => $user->phone ?? '',
                ],
                'expiry' => [
                    'unit'     => 'hours',
                    'duration' => 24
                ]
            ];

            $snapToken = Snap::getSnapToken($params);
            $transaction->update(['snap_token' => $snapToken]);

            // 4. Hapus Keranjang
            Cart::where('user_id', $user->id)->delete();

            DB::commit();

            return response()->json([
                'status'     => 'success',
                'snap_token' => $snapToken,
                'expiry'     => date('c', strtotime('+24 hours'))
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            // Kembalikan pesan error asli agar mudah didebug
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function cancel($id)
    {
        $transaction = Transaction::where('id', $id)
            ->where('customer_id', Auth::id())
            ->where('payment_status', 'pending')
            ->first();

        if (!$transaction) {
            return response()->json(['status' => 'error', 'message' => 'Transaksi tidak ditemukan.'], 404);
        }

        DB::beginTransaction();
        try {
            $details = TransactionDetail::where('transaction_id', $transaction->id)->get();

            foreach ($details as $detail) {
                // 1. Cari dulu apakah item ini sudah ada di keranjang user
                // Catatan: Pastikan kolom di tabel carts adalah 'user_id' (sesuai error log Anda)
                $cartItem = Cart::where('user_id', Auth::id())
                    ->where('shoes_variant_id', $detail->variant_id)
                    ->first();

                if ($cartItem) {
                    // 2. Jika sudah ada, TAMBAHKAN quantity-nya
                    $cartItem->quantity += $detail->qty;
                    $cartItem->save();
                } else {
                    // 3. Jika belum ada, BUAT BARU dengan quantity dari detail transaksi
                    Cart::create([
                        'user_id' => Auth::id(),
                        'shoes_variant_id' => $detail->variant_id,
                        'quantity' => $detail->qty
                    ]);
                }
            }

            // 4. Update status transaksi
            $transaction->update([
                'payment_status' => 'cancel',
                'transaction_status' => 'failed'
            ]);

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Pesanan dibatalkan, barang kembali ke keranjang.']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function callback(Request $request)
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');

        try {
            $notif = new Notification();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid Notification'], 400);
        }

        $transaction_status = $notif->transaction_status;
        $payment_type = $notif->payment_type;
        $order_id = $notif->order_id; // Ini adalah nomor Invoice kita
        $fraud_status = $notif->fraud_status;

        // Cari transaksi di database berdasarkan Invoice
        $transaction = Transaction::where('invoice', $order_id)->first();

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        // Update Status Berdasarkan Logic Midtrans
        if ($transaction_status == 'settlement') {
            $transaction->update([
                'payment_status' => 'settlement',
                'payment_type' => $payment_type,
                'transaction_status' => 'processing' // Langsung masuk ke status diproses
            ]);
        } else if ($transaction_status == 'pending') {
            $transaction->update(['payment_status' => 'pending']);
        } else if ($transaction_status == 'deny' || $transaction_status == 'expire' || $transaction_status == 'cancel') {
            $transaction->update(['payment_status' => $transaction_status]);
        }

        return response()->json(['message' => 'Callback Success']);
    }
}
