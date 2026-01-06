@extends('layouts/frontend/index')
@section('title', 'Pesanan Saya | ShoeCycle')

@section('frontend-content')
    {{-- Breadcrumb --}}
    <div class="bg-gray-50 border-b border-gray-100 py-4">
        <div class="container mx-auto px-4">
            <div class="text-sm breadcrumbs text-gray-500">
                <ul>
                    <li><a href="{{ route('landing-page') }}">Beranda</a></li>
                    <li class="font-bold text-gray-900">Pesanan Saya</li>
                </ul>
            </div>
        </div>
    </div>

    <section class="py-10 bg-slate-50 min-h-screen">
        <div class="container mx-auto px-4 max-w-5xl">
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 font-heading">Riwayat Pesanan</h1>
                    <p class="text-sm text-gray-500 mt-1">Pantau status pengiriman dan riwayat belanja kamu.</p>
                </div>
                {{-- Pencarian Invoice --}}
                <div class="relative w-full md:w-72">
                    <input type="text" placeholder="Cari No. Invoice..." class="input input-bordered w-full pl-10 rounded-xl bg-white border-gray-200 focus:border-blue-500">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>

            {{-- FILTER TABS (Minimalist Style) --}}
            <div class="flex overflow-x-auto gap-2 mb-8 no-scrollbar pb-2">
                <button class="btn btn-sm px-6 rounded-full border-none bg-blue-600 text-white hover:bg-blue-700 shadow-md shadow-blue-200">Semua</button>
                <button class="btn btn-sm px-6 rounded-full border-none bg-white text-gray-600 hover:bg-blue-50 hover:text-blue-600 shadow-sm">Belum Dibayar</button>
                <button class="btn btn-sm px-6 rounded-full border-none bg-white text-gray-600 hover:bg-blue-50 hover:text-blue-600 shadow-sm">Diproses</button>
                <button class="btn btn-sm px-6 rounded-full border-none bg-white text-gray-600 hover:bg-blue-50 hover:text-blue-600 shadow-sm">Dikirim</button>
                <button class="btn btn-sm px-6 rounded-full border-none bg-white text-gray-600 hover:bg-blue-50 hover:text-blue-600 shadow-sm">Selesai</button>
            </div>

            {{-- DAFTAR TRANSAKSI --}}
            <div class="space-y-6">
                @forelse ($transactions as $transaction)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:border-blue-200 transition-all group">
                        {{-- Card Header --}}
                        <div class="p-4 md:px-6 border-b border-gray-50 flex flex-wrap justify-between items-center gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 text-lg">
                                    <i class="fas fa-shopping-bag"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Belanja • {{ $transaction->created_at->format('d M Y') }}</p>
                                    <p class="text-sm font-bold text-gray-900 group-hover:text-blue-600 transition-colors">{{ $transaction->invoice }}</p>
                                </div>
                            </div>

                            {{-- Status Badge Logic --}}
                            @php
                                $statusClasses = [
                                    'pending' => 'bg-orange-100 text-orange-600',
                                    'settlement' => 'bg-green-100 text-green-600',
                                    'expire' => 'bg-red-100 text-red-600',
                                    'cancel' => 'bg-gray-100 text-gray-600',
                                ];
                                $shipClasses = [
                                    'pending' => 'bg-gray-100 text-gray-500',
                                    'processing' => 'bg-blue-100 text-blue-600',
                                    'shipping' => 'bg-indigo-100 text-indigo-600',
                                    'delivered' => 'bg-green-100 text-green-600',
                                ];
                            @endphp

                            <div class="flex gap-2">
                                <span class="badge badge-sm font-bold border-none {{ $statusClasses[$transaction->payment_status] ?? 'bg-gray-100' }}">
                                    {{ strtoupper($transaction->payment_status == 'settlement' ? 'Lunas' : $transaction->payment_status) }}
                                </span>
                                <span class="badge badge-sm font-bold border-none {{ $shipClasses[$transaction->transaction_status] ?? 'bg-gray-100' }}">
                                    {{ ucfirst($transaction->transaction_status) }}
                                </span>
                            </div>
                        </div>

                        {{-- Card Body (Produk Pertama) --}}
                        <div class="p-4 md:p-6 flex flex-col md:flex-row gap-6">
                            @php $firstItem = $transaction->details->first(); @endphp
                            <div class="w-20 h-20 bg-gray-50 rounded-xl border border-gray-200 overflow-hidden shrink-0">
                                <img src="{{ asset('storage/' . $firstItem->variant->images->first()->image_path) }}" class="w-full h-full object-contain mix-blend-multiply p-1">
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-bold text-gray-900 truncate">{{ $firstItem->variant->shoe->name }}</h4>
                                <p class="text-xs text-gray-500 mt-1">
                                    Size {{ $firstItem->variant->size }} • {{ $firstItem->qty }} Barang
                                </p>
                                @if ($transaction->details->count() > 1)
                                    <p class="text-[11px] text-blue-600 mt-2 font-medium">+{{ $transaction->details->count() - 1 }} produk lainnya</p>
                                @endif
                            </div>

                            <div class="md:text-right flex flex-col justify-center">
                                <p class="text-xs text-gray-400">Total Belanja</p>
                                <p class="text-lg font-bold text-gray-900">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</p>
                            </div>
                        </div>

                        {{-- Card Footer (Actions) --}}
                        <div class="p-4 md:px-6 bg-gray-50/50 flex flex-col sm:flex-row justify-between items-center gap-4 border-t border-gray-50">

                            {{-- Info Kurir (Hanya jika sedang dikirim/selesai) --}}
                            @if ($transaction->courier_id)
                                <div class="flex items-center gap-3">
                                    <div class="avatar">
                                        <div class="w-8 rounded-full ring ring-blue-500 ring-offset-base-100 ring-offset-2">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($transaction->courier->name) }}&background=0D8ABC&color=fff" />
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold text-blue-600 uppercase leading-none">Kurir Lokal</p>
                                        <p class="text-xs font-bold text-gray-800">{{ $transaction->courier->name }}</p>
                                    </div>
                                </div>
                            @else
                                <div class="text-xs text-gray-400 italic">Menunggu penugasan kurir...</div>
                            @endif

                            <div class="flex gap-3 w-full sm:w-auto">
                                {{-- Tombol Khusus Belum Bayar --}}
                                @if ($transaction->payment_status == 'pending')
                                    <button onclick="window.snap.pay('{{ $transaction->snap_token }}')" class="btn btn-sm btn-primary text-white rounded-lg px-6 grow sm:grow-0">Bayar Sekarang</button>
                                @endif

                                {{-- Tombol Beri Rating (Jika Selesai) --}}
                                @if ($transaction->transaction_status == 'delivered')
                                    <button class="btn btn-sm btn-outline btn-primary rounded-lg px-6 grow sm:grow-0 group-hover:bg-blue-600 group-hover:text-white transition-all">
                                        <i class="fas fa-star mr-1"></i> Beri Rating
                                    </button>
                                @endif

                                <a href="#" class="btn btn-sm btn-ghost text-gray-500 text-xs normal-case font-bold">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                @empty
                    {{-- Empty State --}}
                    <div class="text-center py-20 bg-white rounded-3xl border-2 border-dashed border-gray-200">
                        <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-box-open text-4xl text-gray-300"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">Belum ada pesanan</h3>
                        <p class="text-sm text-gray-500 max-w-xs mx-auto mt-2">Sepertinya kamu belum melakukan transaksi apapun. Yuk, cari sepatu impianmu!</p>
                        <a href="{{ route('landing-page') }}" class="btn btn-primary mt-6 rounded-xl text-white px-8">Mulai Belanja</a>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    {{-- Midtrans Snap JS --}}
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>

    <script>
        // Logika Tab Switch (Simple)
        $(document).on('click', '.btn-sm', function() {
            $('.btn-sm').removeClass('bg-blue-600 text-white shadow-md shadow-blue-200').addClass('bg-white text-gray-600 shadow-sm');
            $(this).removeClass('bg-white text-gray-600 shadow-sm').addClass('bg-blue-600 text-white shadow-md shadow-blue-200');
            // Catatan: Disini Anda bisa tambahkan filter AJAX atau menyembunyikan card berdasarkan status.
        });
    </script>
@endpush
