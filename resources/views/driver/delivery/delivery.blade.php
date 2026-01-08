@extends('layouts/backend/index')
@section('title', 'ShoeCycle | Daftar Pengiriman')
@section('breadcrumb', 'Daftar Pengiriman')

@section('backend-content')
    <div class="space-y-6 max-w-lg mx-auto"> {{-- Max-width agar tetap rapi di desktop --}}

        {{-- Header Kurir --}}
        <div class="bg-gradient-to-r from-blue-600 to-blue-400 rounded-2xl p-6 text-white shadow-lg shadow-blue-100">
            <p class="text-sm opacity-80 uppercase tracking-widest font-bold">Halo, {{ auth()->user()->name }}!</p>
            <h2 class="text-2xl font-bold mt-1">Daftar Pengiriman</h2>
            <div class="mt-4 flex gap-4">
                <div class="bg-white/20 px-3 py-2 rounded-xl backdrop-blur-sm">
                    <p class="text-[10px] uppercase font-bold">Perlu Dikirim</p>
                    <p class="text-xl font-bold">{{ $transactions->where('transaction_status', 'processing')->count() }}</p>
                </div>
                <div class="bg-white/20 px-3 py-2 rounded-xl backdrop-blur-sm">
                    <p class="text-[10px] uppercase font-bold">Sedang Jalan</p>
                    <p class="text-xl font-bold">{{ $transactions->where('transaction_status', 'shipping')->count() }}</p>
                </div>
            </div>
        </div>

        {{-- Daftar Pesanan --}}
        <div class="space-y-4">
            @forelse ($transactions as $trx)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    {{-- Status Bar --}}
                    <div class="px-4 py-2 border-b border-gray-50 flex justify-between items-center {{ $trx->transaction_status == 'shipping' ? 'bg-indigo-50' : 'bg-orange-50' }}">
                        <span class="text-[10px] font-bold font-mono text-gray-500">{{ $trx->invoice }}</span>
                        <span class="badge badge-sm font-bold border-none {{ $trx->transaction_status == 'shipping' ? 'bg-indigo-100 text-indigo-700' : 'bg-orange-100 text-orange-700' }}">
                            {{ $trx->transaction_status == 'shipping' ? 'Sedang Dikirim' : 'Menunggu Pickup' }}
                        </span>
                    </div>

                    <div class="p-4 space-y-4">
                        {{-- Info Penerima --}}
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-blue-50 rounded-full flex items-center justify-center text-blue-600 shrink-0">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs text-gray-400 uppercase font-bold tracking-tighter leading-none mb-1">Penerima</p>
                                <h4 class="font-bold text-gray-900 truncate">{{ $trx->address->recipient_name }}</h4>
                                <p class="text-sm font-bold text-blue-600">{{ $trx->address->phone_number }}</p>
                            </div>
                            {{-- Tombol WA Langsung --}}
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $trx->address->phone_number) }}" target="_blank" class="btn btn-circle btn-sm bg-green-500 border-none text-white hover:bg-green-600">
                                <i class="fab fa-whatsapp text-lg"></i>
                            </a>
                        </div>

                        {{-- Info Alamat --}}
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-red-50 rounded-full flex items-center justify-center text-red-500 shrink-0">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs text-gray-400 uppercase font-bold tracking-tighter leading-none mb-1">Alamat Pengiriman</p>
                                <p class="text-xs text-gray-600 leading-relaxed">{{ $trx->address->full_address }}</p>
                                <p class="text-[10px] font-bold text-gray-400 mt-1 uppercase">Kec. {{ $trx->address->district }}, {{ $trx->address->village }}</p>
                            </div>
                            {{-- Tombol Google Maps --}}
                            @if ($trx->address->latitude && $trx->address->longitude)
                                <a href="https://www.google.com/maps/search/?api=1&query={{ $trx->address->latitude }},{{ $trx->address->longitude }}" target="_blank" class="btn btn-circle btn-sm bg-blue-500 border-none text-white hover:bg-blue-600">
                                    <i class="fas fa-directions"></i>
                                </a>
                            @endif
                        </div>

                        {{-- List Barang Ringkas --}}
                        <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                            <p class="text-[10px] font-bold text-gray-400 uppercase mb-2">Item Pesanan ({{ $trx->details->count() }})</p>
                            <ul class="space-y-1">
                                @foreach ($trx->details as $item)
                                    <li class="text-xs flex justify-between">
                                        <span class="text-gray-700 truncate mr-2">{{ $item->variant->shoe->name }} (Size: {{ $item->variant->size }})</span>
                                        <span class="font-bold text-gray-900">x{{ $item->qty }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        {{-- Aksi Driver --}}
                        <div class="pt-2">
                            @if ($trx->transaction_status == 'processing')
                                <button onclick="updateStatus({{ $trx->id }}, 'shipping')" class="btn btn-block bg-blue-500 hover:bg-blue-600 text-white border-none rounded-xl h-12 shadow-lg shadow-blue-100">
                                    <i class="fas fa-motorcycle mr-2"></i> Mulai Kirim Sekarang
                                </button>
                            @elseif($trx->transaction_status == 'shipping')
                                <button onclick="updateStatus({{ $trx->id }}, 'delivered')" class="btn btn-block bg-green-500 hover:bg-green-600 text-white border-none rounded-xl h-12 shadow-lg shadow-green-100">
                                    <i class="fas fa-check-circle mr-2"></i> Selesaikan Pengiriman
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-20 opacity-40">
                    <i class="fas fa-box-open text-6xl mb-3"></i>
                    <p class="font-bold">Belum ada daftar pengiriman.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function updateStatus(id, newStatus) {
            // Teks konfirmasi dinamis
            let confirmMsg = newStatus === 'shipping' ?
                'Konfirmasi: Anda akan mulai mengirim pesanan ini?' :
                'Konfirmasi: Pesanan sudah diterima oleh pelanggan?';

            if (!confirm(confirmMsg)) return;

            // Tampilkan loading (optional: ganti text tombol jadi loading)
            const btn = event.target;
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="loading loading-spinner loading-xs"></span> memproses...';

            fetch(`/delivery/update-status/${id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' // Penting untuk rute web.php
                    },
                    body: JSON.stringify({
                        transaction_status: newStatus
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Tampilkan notifikasi sukses jika perlu
                        window.location.reload();
                    } else {
                        alert(data.message);
                        btn.disabled = false;
                        btn.innerHTML = originalHtml;
                    }
                })
                .catch(err => {
                    alert('Gagal menghubungi server.');
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                });
        }
    </script>
@endpush
