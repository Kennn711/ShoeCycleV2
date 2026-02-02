<?php

namespace App\Http\Controllers;

use Midtrans\Notification;
use App\Models\Cart;
use App\Models\ShoesVariant;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        $storeConfig = ['lat' => -7.55139419143815, 'lng' => 112.48052086345787, 'base_shipping_cost' => 5000, 'cost_per_km' => 2500];
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
            $cartItems = Cart::with('variant')->where('user_id', $user->id)->get();

            if ($cartItems->isEmpty()) {
                return response()->json(['message' => 'Keranjang Anda kosong.'], 422);
            }

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

            foreach ($cartItems as $item) {
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'variant_id'     => $item->variant_id ?? $item->shoes_variant_id,
                    'qty'            => $item->quantity,
                    'price'          => $item->variant->price,
                    'notes'          => $request->notes[$item->id] ?? null,
                ]);
            }

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

            Cart::where('user_id', $user->id)->delete();

            DB::commit();

            return response()->json([
                'status'     => 'success',
                'snap_token' => $snapToken,
                'order_id'   => $transaction->id,
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
        try {
            $transaction = Transaction::with('details')->where('id', $id)
                ->where('customer_id', Auth::id())
                ->firstOrFail();

            // LOGIKA KEMBALIKAN BARANG KE KERANJANG
            foreach ($transaction->details as $detail) {
                $existingCart = Cart::where('user_id', Auth::id())
                    ->where('shoes_variant_id', $detail->variant_id)
                    ->first();

                if ($existingCart) {
                    $existingCart->increment('quantity', $detail->qty);
                } else {
                    Cart::create([
                        'user_id' => Auth::id(),
                        'shoes_variant_id' => $detail->variant_id,
                        'quantity' => $detail->qty,
                    ]);
                }
            }

            // UPDATE STATUS SESUAI PERMINTAAN
            $transaction->update([
                'payment_status' => 'cancel', // payment_status === cancel
                'transaction_status' => 'failed', // transaction_status === failed
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Pesanan dibatalkan. Barang telah kembali ke keranjang Anda.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
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
        $order_id = $notif->order_id;
        $fraud_status = $notif->fraud_status;

        // Cari transaksi di database berdasarkan Invoice
        $transaction = Transaction::with('details')->where('invoice', $order_id)->first();

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        // Update Status Berdasarkan Logic Midtrans
        if ($transaction_status == 'settlement') {
            DB::beginTransaction();
            try {
                // KURANGI STOK saat pembayaran berhasil
                foreach ($transaction->details as $detail) {
                    $variant = ShoesVariant::find($detail->variant_id);
                    if ($variant) {
                        $variant->decrement('stock', $detail->qty);
                    }
                }

                $transaction->update([
                    'payment_status' => 'settlement',
                    'payment_type' => $payment_type,
                    'transaction_status' => 'processing'
                ]);

                DB::commit();
                Log::info("Transaction {$order_id} settled. Stock decremented.");
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Failed to process settlement for {$order_id}: " . $e->getMessage());
            }
        } else if ($transaction_status == 'pending') {
            $transaction->update(['payment_status' => 'pending']);
        } else if ($transaction_status == 'deny') {
            $transaction->update([
                'payment_status' => 'deny',
                'transaction_status' => 'failed'
            ]);
        } else if ($transaction_status == 'expire' || $transaction_status == 'cancel') {
            DB::beginTransaction();
            try {
                foreach ($transaction->details as $detail) {
                    $existingCart = Cart::where('user_id', $transaction->customer_id)
                        ->where('shoes_variant_id', $detail->variant_id)
                        ->first();

                    if ($existingCart) {
                        $existingCart->increment('quantity', $detail->qty);
                    } else {
                        Cart::create([
                            'user_id' => $transaction->customer_id,
                            'shoes_variant_id' => $detail->variant_id,
                            'quantity' => $detail->qty,
                        ]);
                    }
                }

                $transaction->update([
                    'payment_status' => $transaction_status, // 'expire' atau 'cancel'
                    'transaction_status' => 'failed'
                ]);

                DB::commit();
                Log::info("Transaction {$order_id} {$transaction_status}. Items returned to cart for user {$transaction->customer_id}.");
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Failed to process {$transaction_status} callback for {$order_id}: " . $e->getMessage());
            }
        }

        return response()->json(['message' => 'Callback Success']);
    }
}
