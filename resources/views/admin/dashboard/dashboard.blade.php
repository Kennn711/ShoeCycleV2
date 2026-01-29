@extends('layouts/backend/index')
@section('title', 'ShoeCycle | Admin Dashboard')
@section('breadcrumb', 'Dashboard > Admin')

@push('styles')
    <style>
        .stat-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
        }
    </style>
@endpush

@section('backend-content')
    <div class="space-y-8 p-4">

        {{-- 1. Statistik Utama (Cards) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="stat-card bg-linear-to-br from-blue-600 to-blue-700 p-6 rounded-3xl shadow-xl text-white" data-aos="fade-up">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-blue-100 text-sm font-medium uppercase tracking-wider">Total Pendapatan</p>
                        <h3 class="text-2xl font-bold mt-1 text-white">Rp {{ number_format($total_revenue ?? 0, 0, ',', '.') }}</h3>
                    </div>
                    <div class="bg-white/20 p-3 rounded-2xl">
                        <i class="fa-solid fa-wallet text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm text-blue-100">
                    <i class="fa-solid fa-arrow-up mr-1"></i>
                    <span>12% dari bulan lalu</span>
                </div>
            </div>

            <div class="stat-card bg-white p-6 rounded-3xl shadow-sm border border-slate-100" data-aos="fade-up" data-aos-delay="100">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-slate-400 text-sm font-medium uppercase tracking-wider">Total Pesanan</p>
                        <h3 class="text-2xl font-bold mt-1 text-black">{{ $total_orders ?? 0 }}</h3>
                    </div>
                    <div class="bg-orange-50 p-3 rounded-2xl text-orange-500">
                        <i class="fa-solid fa-cart-shopping text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm text-green-500">
                    <i class="fa-solid fa-check-double mr-1"></i>
                    <span class="text-black">Update otomatis</span>
                </div>
            </div>

            <div class="stat-card bg-white p-6 rounded-3xl shadow-sm border border-slate-100" data-aos="fade-up" data-aos-delay="200">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-slate-400 text-sm font-medium uppercase tracking-wider">Pelanggan</p>
                        <h3 class="text-2xl font-bold mt-1 text-black">{{ $total_customers ?? 0 }}</h3>
                    </div>
                    <div class="bg-purple-50 p-3 rounded-2xl text-purple-500 ">
                        <i class="fa-solid fa-users text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm text-purple-500">
                    <i class="fa-solid fa-user-plus mr-1"></i>
                    <span class="text-black">Customer aktif</span>
                </div>
            </div>

            <div class="stat-card bg-white p-6 rounded-3xl shadow-sm border border-slate-100" data-aos="fade-up" data-aos-delay="300">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-slate-400 text-sm font-medium uppercase tracking-wider ">Produk Aktif</p>
                        <h3 class="text-2xl font-bold mt-1 text-black">{{ $active_shoes ?? 0 }}</h3>
                    </div>
                    <div class="bg-green-50 p-3 rounded-2xl text-green-500 ">
                        <i class="fa-solid fa-shoe-prints text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm text-green-600">
                    <i class="fa-solid fa-box mr-1"></i>
                    <span class="text-black">Tersedia di katalog</span>
                </div>
            </div>
        </div>

        {{-- 2. Charts Section --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white p-8 rounded-4xl shadow-sm border border-slate-100 flex flex-col" data-aos="fade-right">
                <div class="flex justify-between items-center mb-6">
                    <h4 class="font-bold text-black">Tren Penjualan Mingguan</h4>
                    <select class="select select-sm select-bordered rounded-xl text-black bg-white">
                        <option>7 Hari Terakhir</option>
                        <option>30 Hari Terakhir</option>
                    </select>
                </div>
                {{-- WRAPPER DENGAN TINGGI MAKSIMAL --}}
                <div class="relative flex-1 min-h-[350px] max-h-[350px] w-full">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            <div class="bg-white p-8 rounded-4xl shadow-sm border border-slate-100 flex flex-col" data-aos="fade-left">
                <h4 class="font-bold mb-6 text-black text-center">Status Pesanan</h4>
                {{-- WRAPPER DENGAN TINGGI MAKSIMAL --}}
                <div class="relative flex-1 min-h-[350px] max-h-[350px] w-full">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>

        {{-- 3. Tabel Aktivitas Terbaru --}}
        <div class="bg-white rounded-4xl shadow-sm border border-slate-100 overflow-hidden" data-aos="fade-up">
            <div class="p-8 border-b border-slate-50 flex justify-between items-center">
                <h4 class="font-bold text-black">Transaksi Terbaru</h4>
                <a href="{{ route('transaction.index') }}" class="btn btn-ghost btn-sm text-blue-600 normal-case">Lihat Semua <i class="fa-solid fa-arrow-right ml-2"></i></a>
            </div>
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full text-black">
                    <thead class="bg-slate-50/50">
                        <tr>
                            <th class=" uppercase text-[10px] tracking-widest text-black">Invoice</th>
                            <th class=" uppercase text-[10px] tracking-widest text-black">Pelanggan</th>
                            <th class=" uppercase text-[10px] tracking-widest text-black">Total</th>
                            <th class=" uppercase text-[10px] tracking-widest text-black">Status Bayar</th>
                            <th class=" uppercase text-[10px] tracking-widest text-black">Status Kirim</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($latest_transactions as $trx)
                            <tr>
                                <td class="font-bold">#{{ $trx->invoice }}</td>
                                <td>
                                    <div class="flex items-center gap-3">
                                        @php
                                            $words = explode(' ', $trx->customer->name);
                                            $initials = '';
                                            if (count($words) >= 2) {
                                                $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
                                            } else {
                                                $initials = strtoupper(substr($trx->customer->name, 0, 2));
                                            }
                                        @endphp

                                        <div class="avatar {{ $trx->customer->profile_picture ? '' : 'placeholder' }}">
                                            <div class="w-9 h-9 rounded-xl overflow-hidden border border-slate-100 {{ $trx->customer->profile_picture ? '' : 'bg-blue-100 text-blue-600 flex items-center justify-center' }}">
                                                @if ($trx->customer->profile_picture)
                                                    <img src="{{ asset('storage/' . $trx->customer->profile_picture) }}" alt="{{ $trx->customer->name }}" class="object-cover" />
                                                @else
                                                    <span class="font-bold text-xs uppercase tracking-tighter">{{ $initials }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="flex flex-col text-left">
                                            <span class="font-bold text-slate-800 text-sm leading-tight">{{ $trx->customer->name }}</span>
                                            <span class="text-[10px] text-slate-400 font-medium">{{ $trx->customer->email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="font-bold">Rp {{ number_format($trx->total_price, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge {{ $trx->payment_status == 'settlement' ? 'badge-success' : 'badge-warning' }} badge-sm font-bold text-white">
                                        {{ $trx->payment_status }}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full {{ $trx->transaction_status == 'delivered' ? 'bg-green-500' : 'bg-blue-500' }}"></span>
                                        <span class="text-sm">{{ ucfirst($trx->transaction_status) }}</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-10 text-slate-400">Belum ada transaksi hari ini</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // 1. Sales Trend Chart (Real Data)
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: @json($chartLabels), // Mengambil hari dari PHP
                datasets: [{
                    label: 'Penjualan',
                    data: @json($chartData), // Mengambil total rupiah dari PHP
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
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
                        callbacks: {
                            label: function(context) {
                                return 'Total: Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                            }
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
                            },
                            color: '#94a3b8'
                        }
                    }
                }
            }
        });

        // 2. Order Status Chart (Real Data)
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Delivered', 'Processing', 'Shipping', 'Pending'],
                datasets: [{
                    data: @json($statusData), // Mengambil jumlah status dari PHP
                    backgroundColor: ['#22c55e', '#3b82f6', '#f59e0b', '#ef4444'],
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 25,
                            font: {
                                weight: '600'
                            }
                        }
                    }
                },
                cutout: '75%'
            }
        });
    </script>
@endpush
