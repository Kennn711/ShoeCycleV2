<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));

        $reports = Transaction::with(['customer'])
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->where('payment_status', 'settlement')
            ->orderBy('created_at', 'desc')
            ->get();

        $totalRevenue = $reports->sum('total_price');
        $daysInMonth = Carbon::create($year, $month)->daysInMonth;
        $salesPerDay = Transaction::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->where('payment_status', 'settlement')
            ->select(
                DB::raw('DAY(created_at) as day'),
                DB::raw('SUM(total_price) as total')
            )
            ->groupBy('day')
            ->pluck('total', 'day');

        $chartLabels = [];
        $chartData = [];

        for ($i = 1; $i <= $daysInMonth; $i++) {
            $chartLabels[] = 'Tgl ' . $i;
            $chartData[] = (int) ($salesPerDay[$i] ?? 0);
        }

        return view('admin.reports.index', compact(
            'reports',
            'totalRevenue',
            'month',
            'year',
            'chartLabels',
            'chartData'
        ));
    }
}
