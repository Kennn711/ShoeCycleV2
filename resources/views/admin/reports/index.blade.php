@extends('layouts/backend/index')

@section('title', 'ShoeCycle | Laporan Penjualan')
@section('breadcrumb', 'Laporan Penjualan')

@section('backend-content')
    <div class="space-y-8 p-4">

        {{-- 1. Branding Header & Filter Section --}}
        <div class="flex flex-col gap-6">
            {{-- Branding Row --}}
            <div class="flex items-center gap-5" data-aos="fade-right">
                <div class="w-20 h-20 rounded-2xl overflow-hidden shadow-xl">
                    <img src="{{ asset('assets/upload/logo/logo.png') }}" alt="ShoeCycle Logo" class="w-full h-full rounded-2xl object-contain">
                </div>
                <div>
                    <h1 class="text-2xl font-black text-slate-800 tracking-tight">Laporan Penjualan</h1>
                    <p class="text-sm text-slate-500 font-medium flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                        Rekapitulasi Data Transaksi ShoeCycle
                    </p>
                </div>
            </div>

            {{-- Filter & Action Card --}}
            <div class="bg-white p-5 rounded-[2.5rem] shadow-sm border border-slate-100" data-aos="fade-up">
                <form action="{{ route('report.index') }}" method="GET" class="flex flex-col lg:flex-row items-center gap-4">
                    <div class="grid grid-cols-2 gap-4 w-full lg:w-auto">
                        {{-- Month Selector --}}
                        <div class="relative w-full lg:w-52">
                            <i class="fa-solid fa-calendar-days absolute left-4 top-1/2 -translate-y-1/2 text-blue-500 text-sm"></i>
                            <select name="month" class="select select-bordered w-full pl-11 rounded-2xl bg-slate-50 border-none font-bold text-slate-700 focus:ring-2 focus:ring-blue-500 transition-all">
                                @foreach (range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Year Selector --}}
                        <div class="relative w-full lg:w-40">
                            <i class="fa-solid fa-clock-rotate-left absolute left-4 top-1/2 -translate-y-1/2 text-blue-500 text-sm"></i>
                            <select name="year" class="select select-bordered w-full pl-11 rounded-2xl bg-slate-50 border-none font-bold text-slate-700 focus:ring-2 focus:ring-blue-500 transition-all">
                                @for ($y = date('Y'); $y >= 2024; $y--)
                                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex items-center gap-3 w-full lg:w-auto lg:ml-auto">
                        <button type="submit" class="btn bg-blue-500 hover:bg-blue-600 text-white border-none rounded-2xl px-8 flex-1 lg:flex-none shadow-lg shadow-blue-100 normal-case h-12">
                            <i class="fa-solid fa-arrows-rotate mr-2"></i> Terapkan Filter
                        </button>

                        <a href="{{ route('report.export-pdf', ['month' => $month, 'year' => $year]) }}" class="btn bg-slate-800 hover:bg-black text-white border-none rounded-2xl px-8 flex-1 lg:flex-none shadow-lg shadow-slate-200 normal-case h-12">
                            <i class="fa-solid fa-file-pdf mr-2 text-red-400"></i> Unduh Laporan
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- 2. Statistik Utama --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6" data-aos="fade-up">
            <div class="bg-linear-to-br from-blue-600 to-blue-800 p-8 rounded-[2.5rem] text-white shadow-2xl relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-2xl group-hover:bg-white/20 transition-all"></div>
                <p class="text-blue-100 text-[10px] font-black uppercase tracking-[0.2em] mb-2">Total Pendapatan</p>
                <h2 class="text-3xl font-black">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h2>
                <div class="mt-6 flex items-center gap-2">
                    <span class="bg-white/20 px-3 py-1 rounded-full text-[10px] font-bold tracking-wider">Lunas (Settlement)</span>
                </div>
            </div>

            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm relative group overflow-hidden">
                <div class="absolute right-8 top-8 opacity-10 group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-truck-fast text-5xl text-blue-600"></i>
                </div>
                <p class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] mb-2">Volume Penjualan</p>
                <h2 class="text-3xl font-black text-slate-800">{{ $reports->count() }} <span class="text-sm font-medium text-slate-400">Unit</span></h2>
                <p class="text-green-500 text-[10px] font-black mt-6 flex items-center gap-1.5 uppercase">
                    <i class="fa-solid fa-check-circle"></i> Data Terverifikasi
                </p>
            </div>

            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm relative group overflow-hidden">
                <div class="absolute right-8 top-8 opacity-10 group-hover:scale-110 transition-transform text-black">
                    <i class="fa-solid fa-chart-pie text-5xl text-blue-600 text-black"></i>
                </div>
                <p class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] mb-2 text-black">Rata-rata Order</p>
                <h2 class="text-3xl font-black text-slate-800 text-black">
                    Rp {{ number_format($reports->avg('total_price') ?? 0, 0, ',', '.') }}
                </h2>
                <p class="text-slate-400 text-[10px] font-black mt-6 uppercase tracking-wider italic text-black">Nilai rata-rata keranjang</p>
            </div>
        </div>

        {{-- 3. Chart & Table Section --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Daily Trend Chart --}}
            <div class="lg:col-span-2 bg-white p-8 rounded-[3rem] shadow-sm border border-slate-100 flex flex-col min-h-[450px]" data-aos="fade-right">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                    <h4 class="font-black text-slate-800 uppercase text-xs tracking-[0.15em]">Visualisasi Tren Harian</h4>
                </div>
                {{-- FIX: Menghindari chart rusak dengan tinggi kontainer tetap --}}
                <div class="relative flex-1 w-full max-h-[320px]">
                    <canvas id="reportChart"></canvas>
                </div>
            </div>

            {{-- Summary List --}}
            <div class="bg-white p-8 rounded-[3rem] shadow-sm border border-slate-100" data-aos="fade-left">
                <h4 class="font-black text-slate-800 uppercase text-xs tracking-[0.15em] mb-8">Rincian Transaksi</h4>
                <div class="space-y-6 overflow-y-auto max-h-[300px] pr-2 no-scrollbar text-black">
                    @forelse($reports as $trx)
                        <div class="flex items-center justify-between group">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center font-black text-[10px] text-blue-600 border border-slate-100 group-hover:bg-blue-600 group-hover:text-white transition-all">
                                    {{ strtoupper(substr($trx->customer->name, 0, 2)) }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-xs font-black text-slate-800 truncate w-24">{{ $trx->customer->name }}</span>
                                    <span class="text-[9px] font-bold text-slate-400">{{ $trx->created_at->format('d M') }} • #{{ $trx->invoice }}</span>
                                </div>
                            </div>
                            <span class="text-xs font-black text-slate-700">Rp {{ number_format($trx->total_price, 0, ',', '.') }}</span>
                        </div>
                    @empty
                        <div class="text-center py-10">
                            <i class="fa-solid fa-folder-open text-slate-200 text-4xl mb-2"></i>
                            <p class="text-[10px] font-bold text-slate-400 uppercase">Belum ada data</p>
                        </div>
                    @endforelse
                </div>
                <div class="mt-8 pt-6 border-t border-dashed border-slate-200">
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-black text-slate-400 uppercase">Akumulasi Bulan Ini</span>
                        <span class="text-lg font-black text-blue-600">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('styles')
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Memastikan chart tidak memanjang vertikal secara tidak wajar */
        canvas#reportChart {
            max-height: 320px !important;
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
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(37, 99, 235, 0.25)');
        gradient.addColorStop(1, 'rgba(37, 99, 235, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    label: 'Penjualan',
                    data: @json($chartData),
                    borderColor: '#2563eb',
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#2563eb',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 3,
                    borderWidth: 4
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
                        backgroundColor: '#1e293b',
                        padding: 12,
                        titleFont: {
                            size: 10,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13,
                            weight: 'bold'
                        },
                        callbacks: {
                            label: (context) => ' Rp ' + new Intl.NumberFormat('id-ID').format(context.raw)
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(226, 232, 240, 0.5)',
                            drawBorder: false
                        },
                        ticks: {
                            stepSize: 5000000,
                            callback: v => v >= 1000000 ? (v / 1000000) + ' Jt' : v,
                            font: {
                                weight: 'bold',
                                size: 10
                            },
                            color: '#94a3b8'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                weight: 'bold',
                                size: 10
                            },
                            color: '#94a3b8'
                        }
                    }
                }
            }
        });
    </script>
@endpush
