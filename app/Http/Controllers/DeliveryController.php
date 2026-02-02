<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Pest\Support\Str;

class DeliveryController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with(['customer', 'address', 'details.variant.shoe'])
            ->where('courier_id', Auth::id()) // Pastikan hanya milik kurir ini
            ->whereIn('transaction_status', ['processing', 'shipping', 'delivered']) // Hanya ambil yang aktif
            ->orderBy('transaction_status', 'desc') // Biarkan 'shipping' di atas
            ->latest()
            ->get();

        return view('driver.delivery.delivery', compact('transactions'));
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $transaction = Transaction::findOrFail($id);

            if ($transaction->courier_id != Auth::id()) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak!'], 403);
            }

            $newStatus = $request->transaction_status;
            $updateData = ['transaction_status' => $newStatus];

            // Set timestamp berdasarkan status baru
            switch ($newStatus) {
                case 'shipping':
                    if (!$transaction->shipped_at) {
                        $updateData['shipped_at'] = now();
                    }
                    break;
                case 'delivered':
                    $updateData['delivered_at'] = now();
                    break;
            }

            // Jika ada upload gambar (status delivered)
            if ($request->hasFile('proof_of_delivery')) {
                $image = $request->file('proof_of_delivery');

                $extension = $image->getClientOriginalExtension();
                $filename = 'proof_' . time() . '_' . Str::random(10) . '.' . $extension;

                $path = $image->storeAs('delivery-proofs', $filename, 'public');

                $updateData['proof_of_delivery'] = $path;
            }

            $transaction->update($updateData);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
