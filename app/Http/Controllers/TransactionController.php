<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
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

    public function index()
    {
        return view('admin.transaction.transaction');
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
