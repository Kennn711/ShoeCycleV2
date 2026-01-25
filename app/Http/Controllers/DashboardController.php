<?php

namespace App\Http\Controllers;

use App\Models\Shoes;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function admin()
    {
        // 1. Statistik Utama
        $total_revenue = Transaction::where('payment_status', 'settlement')->sum('total_price');
        $total_orders = Transaction::count();
        $total_customers = User::where('role', 'customer')->count();
        $active_shoes = Shoes::where('is_active', true)->count();

        // 2. Logika Tren Penjualan 7 Hari Terakhir
        $salesData = Transaction::where('payment_status', 'settlement')
            ->where('created_at', '>=', Carbon::now()->subDays(6))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_price) as total')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total', 'date');

        $chartLabels = [];
        $chartData = [];

        // Loop 7 hari ke belakang agar urutan hari sinkron (Sen, Sel, ...)
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $chartLabels[] = Carbon::parse($date)->isoFormat('ddd'); // Format hari singkat
            $chartData[] = (int) ($salesData[$date] ?? 0); // Jika tidak ada penjualan, set 0
        }

        // 3. Logika Status Pesanan (Doughnut Chart)
        $statusCounts = Transaction::select('transaction_status', DB::raw('count(*) as total'))
            ->groupBy('transaction_status')
            ->pluck('total', 'transaction_status');

        $statusData = [
            $statusCounts->get('delivered') ?? 0,
            $statusCounts->get('processing') ?? 0,
            $statusCounts->get('shipping') ?? 0,
            $statusCounts->get('pending') ?? 0,
        ];

        // 4. Transaksi Terbaru
        $latest_transactions = Transaction::with('customer')->latest()->take(5)->get();

        return view('admin.dashboard.dashboard', compact(
            'total_revenue',
            'total_orders',
            'total_customers',
            'active_shoes',
            'chartLabels',
            'chartData',
            'statusData',
            'latest_transactions'
        ));
    }

    public function driver()
    {
        $driverId = auth()->id();
        // 1. Antrian Order: Lunas (settlement), Ditugaskan ke dia, tapi status masih Processing
        $data['new_orders'] = Transaction::where('payment_status', 'settlement')
            ->where('courier_id', $driverId)
            ->where('transaction_status', 'processing')
            ->count();

        // 2. Sedang Dikirim: Sudah mulai jalan (shipping)
        $data['active_delivery'] = Transaction::where('courier_id', $driverId)
            ->where('transaction_status', 'shipping')
            ->count();

        // 3. Selesai Hari Ini: Sudah sampai (delivered) & selesai di tanggal hari ini
        $data['today_tasks_count'] = Transaction::where('courier_id', $driverId)
            ->where('transaction_status', '!=', 'delivered')
            ->where('payment_status', 'settlement') // Pastikan sudah lunas
            ->count();

        // 4. Selesai Hari Ini (Pencapaian hari ini)
        $data['completed_today'] = Transaction::where('courier_id', $driverId)
            ->where('transaction_status', 'delivered')
            ->whereDate('updated_at', today())
            ->count();

        // 5. Total Riwayat (Lifetime)
        $data['total_history'] = Transaction::where('courier_id', $driverId)
            ->where('transaction_status', 'delivered')
            ->count();

        // 6. Daftar Tugas untuk di List & Peta (Semua yang bukan delivered)
        $data['today_tasks_list'] = Transaction::with('address', 'customer')
            ->where('courier_id', $driverId)
            ->where('transaction_status', '!=', 'delivered')
            ->where('payment_status', 'settlement')
            ->orderBy(DB::raw("CASE WHEN transaction_status = 'shipping' THEN 1 ELSE 2 END"))
            ->get();

        return view('driver.dashboard.dashboard', $data);
    }
}
