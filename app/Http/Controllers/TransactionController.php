<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;

class TransactionController extends Controller
{
    public function __construct()
    {
        // Set konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    public function index(Request $request)
    {
        $query = Transaction::with(['customer', 'details']);

        // 1. Filter Pencarian (Invoice atau Nama Pelanggan)
        if ($request->q) {
            $query->where(function ($q) use ($request) {
                $q->where('invoice', 'like', '%' . $request->q . '%')
                    ->orWhereHas('customer', function ($c) use ($request) {
                        $c->where('name', 'like', '%' . $request->q . '%');
                    });
            });
        }

        // 2. Filter Status Bayar
        if ($request->payment_status) {
            $query->where('payment_status', $request->payment_status);
        }

        // 3. Filter Status Transaksi
        if ($request->transaction_status) {
            $query->where('transaction_status', $request->transaction_status);
        }

        $transactions = $query->latest()->paginate(10);

        return view('admin.transaction.transaction', compact('transactions'));
    }

    public function show($id)
    {
        // Load semua relasi yang diperlukan
        $transaction = Transaction::with(['customer', 'address', 'details.variant.shoe', 'details.variant.images'])
            ->findOrFail($id);

        return response()->json($transaction);
    }

    // Ambil daftar user yang memiliki role kurir
    public function getCouriers()
    {
        // Sesuaikan dengan cara Anda membedakan user (misal kolom role)
        $couriers = User::where('role', 'driver')->get(['id', 'name']);
        return response()->json($couriers);
    }

    // Update Driver dan Status
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'courier_id' => 'required|exists:users,id',
        ]);

        $transaction = Transaction::findOrFail($id);
        $transaction->update([
            'courier_id' => $request->courier_id,
        ]);

        return response()->json(['success' => true, 'message' => 'Driver berhasil ditugaskan!']);
    }

    public function indexCustomer()
    {
        $transactions = Transaction::with(['details.variant.shoe', 'details.variant.images', 'courier'])
            ->where('customer_id', auth()->id())
            ->latest()
            ->get();

        return view('customer.transaction.myorder', compact('transactions'));
    }
}
