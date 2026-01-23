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
                {{-- Search --}}
                <div class="relative w-full md:w-72">
                    <input type="text" id="search-invoice" placeholder="Cari No. Invoice..." class="input input-bordered w-full pl-10 rounded-xl bg-white border-gray-200 focus:border-blue-500">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>

            {{-- FILTER TABS (Minimalist Style) --}}
            <div class="flex overflow-x-auto gap-2 mb-8 no-scrollbar pb-2">
                <button class="tab-filter btn btn-sm px-6 rounded-full border-none bg-blue-600 text-white shadow-md shadow-blue-200" data-filter="all">Semua</button>
                <button class="tab-filter btn btn-sm px-6 rounded-full border-none bg-white text-gray-600 shadow-sm" data-filter="pending">Belum Dibayar</button>
                <button class="tab-filter btn btn-sm px-6 rounded-full border-none bg-white text-gray-600 shadow-sm" data-filter="processing">Diproses</button>
                <button class="tab-filter btn btn-sm px-6 rounded-full border-none bg-white text-gray-600 shadow-sm" data-filter="shipping">Dikirim</button>
                <button class="tab-filter btn btn-sm px-6 rounded-full border-none bg-white text-gray-600 shadow-sm" data-filter="delivered">Selesai</button>
            </div>

            {{-- DAFTAR TRANSAKSI --}}
            <div class="space-y-6">
                @forelse ($transactions as $transaction)
                    <div class="order-card bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:border-blue-200 transition-all group" data-status="{{ $transaction->transaction_status }}" data-invoice="{{ $transaction->invoice }}">
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
                                    @if ($transaction->transaction_status == 'processing')
                                        Diproses
                                    @endif
                                    @if ($transaction->transaction_status == 'shipping')
                                        Dikirim
                                    @endif
                                    @if ($transaction->transaction_status == 'delivered')
                                        Selesai
                                    @endif
                                    @if ($transaction->payment_status == 'cancel' && $transaction->transaction_status == 'failed')
                                        Dibatalkan
                                    @endif
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
                            @endif

                            @if (empty($transaction->courier_id) && $transaction->payment_status == 'cancel')
                                <div class="text-xs text-gray-400 italic">Pesanan dibatalkan oleh Anda</div>
                            @endif

                            @if (empty($transaction->courier_id))
                                <div class="text-xs text-gray-400 italic">Pesanan sedang diproses oleh Admin</div>
                            @endif

                            {{-- Cari bagian ini dan tambahkan id pada pembungkusnya --}}
                            <div class="flex gap-3 w-full sm:w-auto" id="btn-container-{{ $transaction->id }}">
                                {{-- Tombol Bayar --}}
                                @if ($transaction->payment_status == 'pending')
                                    <button onclick="window.snap.pay('{{ $transaction->snap_token }}')" class="btn btn-sm btn-primary text-white rounded-lg px-6 grow sm:grow-0">Bayar Sekarang</button>
                                @endif

                                @if ($transaction->transaction_status == 'delivered')
                                    @php
                                        $orderDetails = $transaction->details;

                                        // Ambil ID Sepatu (model) yang sudah diulas user (Global atau per Transaksi)
                                        $reviewedShoeIds = \App\Models\Reviews::where('user_id', Auth::id())->pluck('shoe_id')->toArray();

                                        // Filter: Ambil item, tapi buat unik berdasarkan shoe_id
                                        // Jadi jika ada 2 varian sepatu yang sama, hanya akan muncul 1 kali di list
                                        $unreviewedItems = $orderDetails
                                            ->filter(function ($item) use ($reviewedShoeIds) {
                                                return !in_array($item->variant->shoe_id, $reviewedShoeIds);
                                            })
                                            ->unique(function ($item) {
                                                return $item->variant->shoe_id; // KUNCI: Unik berdasarkan model sepatu
                                            });

                                        $unreviewedCount = $unreviewedItems->count();
                                    @endphp

                                    @if ($unreviewedCount > 0)
                                        <button type="button" class="btn-open-rating btn btn-sm btn-primary px-6 text-white" data-invoice="{{ $transaction->invoice }}" data-tid="{{ $transaction->id }}" data-items="{{ json_encode($unreviewedItems->values()->load(['variant.shoe.category', 'variant.images'])) }}">
                                            <i class="fas fa-star mr-1"></i> Beri Ulasan
                                        </button>
                                    @else
                                        <div class="flex items-center gap-1 text-green-600 font-bold text-xs uppercase px-4 select-none">
                                            <i class="fas fa-check-circle"></i> Selesai Diulas
                                        </div>
                                    @endif
                                @endif

                                {{-- Tombol Lihat Detail (FIXED: Menggunakan data-detail) --}}
                                <button type="button" class="btn-show-detail btn btn-sm btn-primary text-white text-xs normal-case font-bold px-6" data-detail="{{ json_encode($transaction->load(['details.variant.shoe', 'address', 'courier'])) }}">
                                    Lihat Detail
                                </button>
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

    {{-- MODAL 1: DETAIL PESANAN --}}
    <dialog id="order_detail_modal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-white p-0 max-w-2xl overflow-hidden">
            {{-- Header --}}
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-slate-50">
                <div>
                    <h3 class="font-bold text-lg text-gray-900" id="mdl-invoice">-</h3>
                    <p class="text-xs text-gray-500" id="mdl-date">-</p>
                </div>
                <form method="dialog"><button class="btn btn-sm btn-circle btn-ghost">✕</button></form>
            </div>

            <div class="p-6 space-y-6 max-h-[75vh] overflow-y-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Alamat --}}
                    <div>
                        <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Info Pengiriman</h4>
                        <p class="text-sm font-bold text-gray-900" id="mdl-recipient">-</p>
                        <p class="text-xs text-gray-600 mt-1" id="mdl-phone">-</p>
                        <p class="text-xs text-gray-500 mt-1 leading-relaxed" id="mdl-address">-</p>
                    </div>
                    {{-- Status --}}
                    <div>
                        <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Status & Pembayaran</h4>
                        <div id="mdl-status-badges" class="flex flex-wrap gap-2 items-start"></div>
                    </div>
                </div>

                {{-- INFO KURIR & BUKTI (Akan muncul jika sudah ada Driver) --}}
                <div id="mdl-delivery-info" class="hidden pt-4 border-t border-gray-100">
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Kurir Pengirim</h4>
                            <div class="flex items-center gap-3 bg-blue-50 p-3 rounded-xl border border-blue-100">
                                <img id="mdl-courier-avatar" src="" class="w-10 h-10 rounded-full">
                                <div>
                                    <p class="text-sm font-bold text-gray-900" id="mdl-courier-name">-</p>
                                    <p class="text-[10px] text-blue-600 font-bold uppercase">Driver Lokal</p>
                                </div>
                            </div>
                        </div>
                        <div id="mdl-proof-section" class="flex-1 hidden">
                            <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Bukti Sampai</h4>
                            <div class="relative group cursor-pointer" onclick="openProofViewer(this.querySelector('img').src)">
                                <img id="mdl-proof-img" src="" class="w-full h-16 object-cover rounded-xl border border-gray-200">
                                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 bg-black/20 rounded-xl transition-all">
                                    <i class="fas fa-expand text-white text-xs"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tabel Barang --}}
                <div class="pt-4 border-t border-gray-100">
                    <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">Daftar Barang</h4>
                    <div class="overflow-x-auto border border-gray-100 rounded-xl">
                        <table class="table w-full">
                            <thead class="bg-slate-50">
                                <tr class="text-gray-600 text-[10px] uppercase">
                                    <th>Produk</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="mdl-items-body"></tbody>
                        </table>
                    </div>
                </div>

                {{-- Rincian Biaya --}}
                <div class="bg-slate-50 p-4 rounded-xl space-y-2">
                    <div class="flex justify-between text-xs text-gray-600"><span>Subtotal Produk</span><span id="mdl-subtotal">-</span></div>
                    <div class="flex justify-between text-xs text-gray-600"><span>Ongkos Kirim</span><span id="mdl-shipping">-</span></div>
                    <div class="flex justify-between text-xs text-gray-600"><span>Biaya Admin</span><span id="mdl-admin">-</span></div>
                    <div class="flex justify-between text-sm font-bold text-gray-900 pt-2 border-t border-gray-200"><span>Total</span><span class="text-blue-600" id="mdl-total">-</span></div>
                </div>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop bg-gray-900/50"><button>close</button></form>
    </dialog>

    {{-- MODAL 2: IMAGE VIEWER (Lightbox) --}}
    <dialog id="proof_image_viewer" class="modal bg-black/80 backdrop-blur-sm z-[100]">
        <div class="modal-box p-0 max-w-3xl bg-transparent shadow-none relative">
            <form method="dialog"><button class="btn btn-sm btn-circle absolute right-4 top-4 bg-white/20 text-white border-none">✕</button></form>
            <img id="mdl-proof-full-img" src="" class="w-full h-auto rounded-2xl shadow-2xl object-contain max-h-[85vh]">
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>

    {{-- MODAL 3: RATING & ULASAN --}}
    <dialog id="rating_modal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-white p-0 max-w-xl overflow-hidden">
            {{-- Header --}}
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-slate-50">
                <div>
                    <h3 class="font-bold text-lg text-gray-900">Beri Ulasan Produk</h3>
                    <p class="text-xs text-gray-500" id="mdl-rating-invoice">-</p>
                </div>
                <form method="dialog"><button class="btn btn-sm btn-circle btn-ghost">✕</button></form>
            </div>

            <form id="form-multiple-rating" action="{{ route('reviews.store') }}" method="POST">
                @csrf
                <input type="hidden" name="transaction_id" id="mdl-rating-transaction-id">

                <div class="p-6 space-y-8 max-h-[60vh] overflow-y-auto" id="rating-items-container">
                    {{-- Item akan di-generate via JavaScript --}}
                </div>

                <div class="p-4 bg-gray-50 border-t border-gray-100">
                    <button type="submit" id="btn-submit-rating" class="btn btn-primary w-full text-white font-bold rounded-xl shadow-lg">
                        Kirim Semua Ulasan
                    </button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop bg-gray-900/50"><button>close</button></form>
    </dialog>
@endsection

@push('scripts')
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    <script>
        let activeFilter = 'all';
        let searchTimeout;

        // Fungsi Format Rupiah
        window.formatRupiah = (num) => 'Rp ' + new Intl.NumberFormat('id-ID').format(num);

        /**
         * FUNGSI FILTER & SEARCH REAL-TIME
         */
        function applyFilters() {
            const searchQuery = $('#search-invoice').val().toLowerCase();
            $('.order-card').each(function() {
                const status = $(this).data('status');
                const invoice = $(this).data('invoice').toLowerCase();
                const matchTab = (activeFilter === 'all' || status === activeFilter);
                const matchSearch = invoice.includes(searchQuery);

                if (matchTab && matchSearch) $(this).fadeIn(200);
                else $(this).hide();
            });
        }

        /**
         * DOUBLE MODAL: PENAMPIL GAMBAR (LIGHTBOX)
         */
        window.openProofViewer = function(src) {
            document.getElementById('mdl-proof-full-img').src = src;
            document.getElementById('proof_image_viewer').showModal();
        };

        /**
         * FUNGSI MENAMPILKAN DETAIL PESANAN
         * (Dipanggil oleh listener tombol .btn-show-detail)
         */
        window.showOrderDetail = function(data) {
            const modal = $('#order_detail_modal')[0];
            $('#mdl-invoice').text(data.invoice);
            $('#mdl-date').text('Dipesan pada ' + new Date(data.created_at).toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'long',
                year: 'numeric'
            }));

            // Alamat
            const addr = data.address;
            $('#mdl-recipient').text(addr.recipient_name);
            $('#mdl-phone').text(addr.phone_number);
            $('#mdl-address').html(`${addr.full_address}<br>Kec. ${addr.district}, ${addr.village}`);

            // Status Badges
            $('#mdl-status-badges').html(`
                <span class="badge badge-outline font-bold text-[10px] uppercase text-black">${data.payment_type ? data.payment_type.replace(/_/g, ' ') : 'PENDING'}</span>
                <span class="badge badge-info text-white font-bold text-[10px] uppercase">${data.transaction_status}</span>
            `);

            // Logika Driver & Bukti Foto
            if (data.courier) {
                $('#mdl-delivery-info').removeClass('hidden');
                $('#mdl-courier-name').text(data.courier.name);
                $('#mdl-courier-avatar').attr('src', `https://ui-avatars.com/api/?name=${encodeURIComponent(data.courier.name)}&background=0D8ABC&color=fff`);

                const proof = data.proof_of_delivery || data.proof_of_payment;
                if (proof) {
                    $('#mdl-proof-section').removeClass('hidden');
                    $('#mdl-proof-img').attr('src', `/storage/${proof}`);
                } else {
                    $('#mdl-proof-section').addClass('hidden');
                }
            } else {
                $('#mdl-delivery-info').addClass('hidden');
            }

            // Tabel Barang di Modal Detail
            let tableHtml = '';
            data.details.forEach(item => {
                tableHtml += `
                <tr class="border-b border-gray-50 text-xs text-black">
                    <td><div class="font-bold text-gray-900">${item.variant.shoe.name}</div><div class="text-[10px] text-gray-400 uppercase">Size: ${item.variant.size} | ${item.variant.color}</div></td>
                    <td class="text-center">${item.qty}</td>
                    <td class="text-right font-bold">${window.formatRupiah(item.price * item.qty)}</td>
                </tr>`;
            });
            $('#mdl-items-body').html(tableHtml);

            // Rincian Biaya
            $('#mdl-subtotal').text(window.formatRupiah(data.subtotal));
            $('#mdl-shipping').text(window.formatRupiah(data.shipping_cost));
            $('#mdl-admin').text(window.formatRupiah(data.admin_fee));
            $('#mdl-total').text(window.formatRupiah(data.total_price));

            modal.showModal();
        };

        $(document).ready(function() {
            // 1. Tombol Filter Tab
            $(document).on('click', '.tab-filter', function() {
                $('.tab-filter').removeClass('bg-blue-600 text-white shadow-md shadow-blue-200').addClass('bg-white text-gray-600 shadow-sm');
                $(this).removeClass('bg-white text-gray-600 shadow-sm').addClass('bg-blue-600 text-white shadow-md shadow-blue-200');
                activeFilter = $(this).data('filter');
                applyFilters();
            });

            // 2. Input Pencarian
            $('#search-invoice').on('keyup', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => applyFilters(), 300);
            });

            $(document).on('click', '.btn-show-detail', function() {
                const data = $(this).data('detail');
                showOrderDetail(data);
            });

            $(document).on('click', '.btn-open-rating', function() {
                const invoice = $(this).data('invoice');
                const transactionId = $(this).data('tid');
                const items = $(this).data('items');

                const modal = document.getElementById('rating_modal');
                const container = document.getElementById('rating-items-container');

                $('#mdl-rating-invoice').text(invoice);
                $('#mdl-rating-transaction-id').val(transactionId);
                container.innerHTML = '';

                items.forEach((item, index) => {
                    const imagePath = (item.variant.images && item.variant.images.length > 0) ?
                        `/storage/${item.variant.images[0].image_path}` :
                        '/assets/upload/testing/sepatu1.webp';

                    const itemHtml = `
<div class="space-y-4 border-b border-gray-100 pb-6 last:border-0 last:pb-0 text-black">
    <div class="flex items-center gap-4">
        <img src="${imagePath}" class="w-14 h-14 object-contain bg-gray-50 rounded-xl border p-1">
        <div class="min-w-0">
            <h4 class="text-sm font-bold text-gray-900 truncate">${item.variant.shoe.name}</h4>
            {{-- PERBAIKAN: Gunakan pengecekan agar tidak error --}}
            <p class="text-[10px] text-gray-400 uppercase tracking-tight">
                Kategori: ${item.variant.shoe.category ? item.variant.shoe.category.category_name : 'Sepatu'}
            </p>
        </div>
    </div>
    
    <input type="hidden" name="reviews[${index}][shoe_id]" value="${item.variant.shoe_id}">

    <div class="space-y-3">
        <div class="flex items-center justify-between bg-slate-50 p-2 rounded-lg">
            <span class="text-xs font-bold text-gray-600 uppercase">Rating:</span>
            <div class="rating rating-sm">
                <input type="radio" name="reviews[${index}][rating]" value="1" class="mask mask-star-2 bg-orange-400" />
                <input type="radio" name="reviews[${index}][rating]" value="2" class="mask mask-star-2 bg-orange-400" />
                <input type="radio" name="reviews[${index}][rating]" value="3" class="mask mask-star-2 bg-orange-400" />
                <input type="radio" name="reviews[${index}][rating]" value="4" class="mask mask-star-2 bg-orange-400" />
                <input type="radio" name="reviews[${index}][rating]" value="5" class="mask mask-star-2 bg-orange-400" checked />
            </div>
        </div>
        <textarea name="reviews[${index}][comment]" class="textarea textarea-bordered w-full bg-white text-sm focus:border-blue-500 rounded-xl resize-none text-black" rows="2" placeholder="Bagaimana kualitas model sepatu ini?"></textarea>
    </div>
</div>`;
                    container.insertAdjacentHTML('beforeend', itemHtml);
                });

                modal.showModal();
            });

            // 5. Submit Rating via AJAX
            $(document).on('submit', '#form-multiple-rating', function(e) {
                e.preventDefault();
                const form = $(this);
                const btn = $('#btn-submit-rating');
                const originalText = btn.text();
                const transactionId = $('#mdl-rating-transaction-id').val();

                btn.prop('disabled', true).html('<span class="loading loading-spinner loading-xs"></span> Mengirim...');

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    success: function(res) {
                        if (res.success) {
                            document.getElementById('rating_modal').close();

                            Swal.fire({
                                icon: 'success',
                                title: 'Terima Kasih!',
                                text: 'Ulasanmu berhasil dikirim.',
                                showConfirmButton: false,
                                timer: 1500
                            });

                            // UPDATE UI INSTAN: Ganti tombol ulasan menjadi badge Selesai
                            const container = $(`#btn-container-${transactionId}`);
                            container.find('.btn-open-rating').replaceWith(`
                                <div class="flex items-center gap-1 text-green-600 font-bold text-xs uppercase px-4 select-none animate-in fade-in zoom-in duration-300">
                                    <i class="fas fa-check-circle"></i> Selesai Diulas
                                </div>
                            `);

                            btn.prop('disabled', false).text(originalText);
                        }
                    },
                    error: function() {
                        btn.prop('disabled', false).text(originalText);
                        Swal.fire('Error', 'Gagal mengirim ulasan.', 'error');
                    }
                });
            });

            // 6. Cek Redirect Midtrans
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('status_code') === '200') {
                Swal.fire({
                    title: 'Pembayaran Berhasil!',
                    text: 'Pesanan segera dikirim.',
                    icon: 'success',
                    confirmButtonColor: '#3b82f6'
                });
                window.history.pushState({}, document.title, window.location.pathname);
            }
        });
    </script>
@endpush
