<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeliveryController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with(['customer', 'address', 'details.variant.shoe'])
            ->where('courier_id', Auth::id()) // Pastikan hanya milik kurir ini
            ->whereIn('transaction_status', ['processing', 'shipping']) // Hanya ambil yang aktif
            ->orderBy('transaction_status', 'desc') // Biarkan 'shipping' di atas
            ->latest()
            ->get();

        return view('driver.delivery.delivery', compact('transactions'));
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $transaction = Transaction::findOrFail($id);

            // Security Check: Pastikan driver tidak mengubah pesanan orang lain
            if ($transaction->courier_id != Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak. Ini bukan tugas Anda.'
                ], 403);
            }

            // Update status
            $transaction->update([
                'transaction_status' => $request->transaction_status
            ]);

            $message = $request->transaction_status == 'shipping'
                ? 'Pesanan mulai dikirim!'
                : 'Pesanan telah selesai dikirim!';

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
