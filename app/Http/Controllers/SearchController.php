<?php

namespace App\Http\Controllers;

use App\Models\Shoes;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function searchGlobal(Request $request)
    {
        $query = $request->get('q');
        $user = auth()->user();
        $results = [];

        if (!$query) return response()->json([]);

        // LOGIKA UNTUK ROLE: ADMIN
        if ($user->role === 'admin') {
            // 1. Cari Sepatu (Produk)
            $shoes = Shoes::where('name', 'LIKE', "%$query%")->take(3)->get();
            foreach ($shoes as $item) {
                $results[] = [
                    'category' => 'Produk',
                    'name'     => $item->name,
                    'url'      => route('shoes.index', ['search' => $item->name]),
                    'image'    => asset('storage/' . $item->image),
                    'price'    => 'Rp ' . number_format($item->price, 0, ',', '.')
                ];
            }

            // 2. Cari Transaksi Berdasarkan Invoice
            $transactions = Transaction::where('invoice', 'LIKE', "%$query%")->take(3)->get();
            foreach ($transactions as $item) {
                $results[] = [
                    'category' => 'Transaksi',
                    'name'     => '#' . $item->invoice,
                    'url'      => route('transaction.index', ['search' => $item->invoice]),
                    'image'    => 'https://ui-avatars.com/api/?name=TR&background=6366f1&color=fff',
                    'price'    => 'Status: ' . strtoupper($item->transaction_status)
                ];
            }

            // 3. Cari Driver Berdasarkan Nama
            $drivers = User::where('role', 'driver')->where('name', 'LIKE', "%$query%")->take(2)->get();
            foreach ($drivers as $item) {
                $results[] = [
                    'category' => 'Driver',
                    'name'     => $item->name,
                    'url'      => route('driver.index', ['search' => $item->name]),
                    'image'    => $item->profile_picture ? asset('storage/' . $item->profile_picture) : 'https://ui-avatars.com/api/?name=' . urlencode($item->name),
                    'price'    => $item->email
                ];
            }
        }

        // LOGIKA UNTUK ROLE: DRIVER
        else if ($user->role === 'driver') {
            // Driver hanya bisa mencari tugas yang ditugaskan ke dia
            $tasks = Transaction::with('customer')
                ->where('courier_id', $user->id)
                ->where(function ($q) use ($query) {
                    $q->where('invoice', 'LIKE', "%$query%")
                        ->orWhereHas('customer', function ($c) use ($query) {
                            $c->where('name', 'LIKE', "%$query%");
                        });
                })
                ->take(5)->get();

            foreach ($tasks as $item) {
                $results[] = [
                    'category' => 'Tugas Pengiriman',
                    'name'     => $item->customer->name,
                    'url'      => route('dashboard-driver', ['focus' => $item->invoice]),
                    'image'    => 'https://ui-avatars.com/api/?name=' . urlencode($item->customer->name),
                    'price'    => 'Invoice: #' . $item->invoice
                ];
            }
        }

        return response()->json($results);
    }
}
