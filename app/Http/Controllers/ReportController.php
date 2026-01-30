<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

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

    public function exportPdf(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));

        $reports = Transaction::with(['customer'])
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->where('payment_status', 'settlement')
            ->orderBy('created_at', 'asc')
            ->get();

        $totalRevenue = $reports->sum('total_price');
        $monthName = date('F', mktime(0, 0, 0, $month, 1));

        // Data yang dikirim ke view PDF
        $data = [
            'reports' => $reports,
            'totalRevenue' => $totalRevenue,
            'monthName' => $monthName,
            'year' => $year,
            'dateGenerated' => now()->format('d/m/Y H:i')
        ];

        // Load view khusus PDF dan atur kertas ke A4
        $pdf = Pdf::loadView('admin.reports.export-pdf', $data)->setPaper('a4', 'portrait');

        return $pdf->download("Laporan-Penjualan-ShoeCycle-{$monthName}-{$year}.pdf");
    }
}
