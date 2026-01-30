@extends('layouts/backend/index')

@section('title', 'ShoeCycle | Laporan Penjualan')
@section('breadcrumb', 'Laporan Penjualan')

@section('backend-content')
    <div class="space-y-6 p-4">

        {{-- 1. Header & Filter --}}
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex flex-col md:flex-row justify-between items-center gap-4" data-aos="fade-down">
            <div>
                <h1 class="text-xl font-black text-slate-800">Laporan Penjualan</h1>
                <p class="text-sm text-slate-500 font-medium">Pantau performa bisnis ShoeCycle</p>
            </div>

            <form action="{{ route('report.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
                <select name="month" class="select select-bordered rounded-xl bg-slate-50 text-slate-700 font-bold border-none focus:ring-2 focus:ring-blue-500">
                    @foreach (range(1, 12) as $m)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                        </option>
                    @endforeach
                </select>

                <select name="year" class="select select-bordered rounded-xl bg-slate-50 text-slate-700 font-bold border-none focus:ring-2 focus:ring-blue-500">
                    @for ($y = date('Y'); $y >= 2024; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>

                <button type="submit" class="btn bg-blue-600 hover:bg-blue-700 text-white border-none rounded-xl px-6">
                    <i class="fa-solid fa-filter mr-2"></i> Terapkan
                </button>

                <button type="button" onclick="window.print()" class="btn btn-outline border-slate-200 hover:bg-slate-100 text-slate-600 rounded-xl">
                    <i class="fa-solid fa-print"></i>
                </button>
            </form>
        </div>

        {{-- 2. Statistik Ringkas --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6" data-aos="fade-up">
            <div class="bg-linear-to-br from-blue-600 to-blue-800 p-6 rounded-4xl text-white shadow-xl">
                <p class="text-blue-100 text-xs font-black uppercase tracking-widest">Total Omzet</p>
                <h2 class="text-3xl font-black mt-2">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h2>
                <div class="mt-4 py-2 px-3 bg-white/10 rounded-lg inline-flex items-center text-[10px] font-bold">
                    <i class="fa-solid fa-calendar-check mr-2"></i> Periode Terpilih
                </div>
            </div>

            <div class="bg-white p-6 rounded-4xl border border-slate-100 shadow-sm">
                <p class="text-slate-400 text-xs font-black uppercase tracking-widest">Total Transaksi</p>
                <h2 class="text-3xl font-black mt-2 text-slate-800">{{ $reports->count() }}</h2>
                <p class="text-green-500 text-xs font-bold mt-4 flex items-center gap-1">
                    <i class="fa-solid fa-circle-check"></i> Transaksi Settlement
                </p>
            </div>

            <div class="bg-white p-6 rounded-4xl border border-slate-100 shadow-sm text-black">
                <p class="text-slate-400 text-xs font-black uppercase tracking-widest">Rata-rata Keranjang</p>
                <h2 class="text-3xl font-black mt-2 text-slate-800 text-black">
                    Rp {{ number_format($reports->avg('total_price') ?? 0, 0, ',', '.') }}
                </h2>
                <p class="text-slate-400 text-xs font-bold mt-4">Berdasarkan data bulan ini</p>
            </div>
        </div>

        {{-- 3. Grafik Tren --}}
        <div class="bg-white p-8 rounded-4xl shadow-sm border border-slate-100" data-aos="fade-up">
            <h4 class="font-black text-slate-800 mb-6 flex items-center gap-2">
                <i class="fa-solid fa-chart-line text-blue-600"></i> Tren Penjualan Harian
            </h4>
            <div class="relative h-[300px] w-full">
                <canvas id="reportChart"></canvas>
            </div>
        </div>

        {{-- 4. Tabel Detail --}}
        <div class="bg-white rounded-4xl shadow-sm border border-slate-100 overflow-hidden text-black" data-aos="fade-up">
            <div class="p-8 border-b border-slate-50 flex justify-between items-center">
                <h4 class="font-black text-slate-800">Detail Rincian Transaksi</h4>
            </div>
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead class="bg-slate-50/50">
                        <tr class="text-black">
                            <th class="text-[10px] uppercase tracking-widest">Tgl Transaksi</th>
                            <th class="text-[10px] uppercase tracking-widest">Invoice</th>
                            <th class="text-[10px] uppercase tracking-widest">Pelanggan</th>
                            <th class="text-[10px] uppercase tracking-widest">Metode</th>
                            <th class="text-[10px] uppercase tracking-widest">Total Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $trx)
                            <tr>
                                <td class="text-sm font-medium text-slate-500">{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                                <td class="font-black text-blue-600">#{{ $trx->invoice }}</td>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="avatar placeholder">
                                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100">
                                                <span class="text-[10px] font-black">{{ strtoupper(substr($trx->customer->name, 0, 2)) }}</span>
                                            </div>
                                        </div>
                                        <span class="font-bold text-slate-700">{{ $trx->customer->name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-ghost font-bold text-[10px] uppercase">{{ $trx->payment_type ?? 'N/A' }}</span>
                                </td>
                                <td class="font-black text-slate-800 text-left">Rp {{ number_format($trx->total_price, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-20">
                                    <div class="flex flex-col items-center opacity-30">
                                        <i class="fa-solid fa-receipt text-5xl mb-4"></i>
                                        <p class="font-black uppercase tracking-widest">Tidak ada transaksi pada periode ini</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-slate-50">
                        <tr class="text-black">
                            <th colspan="4" class="text-right font-black uppercase text-xs">Total Akumulasi:</th>
                            <th class="text-right text-lg font-black text-blue-700">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        @media print {

            .sidebar,
            .navbar,
            form,
            button,
            .breadcrumb {
                display: none !important;
            }

            .bg-white {
                border: none !important;
                box-shadow: none !important;
            }

            .p-4 {
                padding: 0 !important;
            }

            canvas {
                max-width: 100% !important;
                height: auto !important;
            }
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true
        });

        const ctx = document.getElementById('reportChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(37, 99, 235, 0.2)');
        gradient.addColorStop(1, 'rgba(37, 99, 235, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartLabels), // Array tanggal 1-31
                datasets: [{
                    label: 'Penjualan',
                    data: @json($chartData), // Array total per tanggal
                    borderColor: '#2563eb',
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: (context) => ' Rp ' + new Intl.NumberFormat('id-ID').format(context.raw)
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 5000000,
                            callback: value => value >= 1000000 ? (value / 1000000) + ' Jt' : value,
                            font: {
                                weight: '600'
                            }
                        },
                        grid: {
                            color: '#f8fafc'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    </script>
@endpush
