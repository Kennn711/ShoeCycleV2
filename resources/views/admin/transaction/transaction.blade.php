@extends('layouts/backend/index')

@section('title', 'ShoeCycle | Transaksi')
@section('breadcrumb', 'Tabel > Transaksi')

@section('backend-content')
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-900">Daftar Transaksi</h2>
            <div class="flex gap-2">
                <button class="btn btn-sm bg-green-500 hover:bg-green-600 text-white border-none">
                    <i class="fas fa-file-excel mr-1"></i> Export Excel
                </button>
            </div>
        </div>

        {{-- Filter & Search Controls --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <form action="{{ route('transaction.index') }}" method="GET">
                <div class="flex flex-col lg:flex-row gap-2 justify-between">
                    {{-- Bagian Filter Kiri --}}
                    <div class="flex flex-wrap gap-4">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-gray-600">Status Bayar</span>
                            <select name="payment_status" class="select select-sm select-bordered w-full max-w-xs bg-white text-black" onchange="this.form.submit()">
                                <option value="">Semua</option>
                                <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="settlement" {{ request('payment_status') == 'settlement' ? 'selected' : '' }}>Lunas (Settlement)</option>
                                <option value="expire" {{ request('payment_status') == 'expire' ? 'selected' : '' }}>Expired</option>
                                <option value="cancel" {{ request('payment_status') == 'cancel' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-gray-600">Status Kirim</span>
                            <select name="transaction_status" class="select select-sm select-bordered w-full max-w-xs bg-white text-black" onchange="this.form.submit()">
                                <option value="">Semua</option>
                                <option value="pending" {{ request('transaction_status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                                <option value="processing" {{ request('transaction_status') == 'processing' ? 'selected' : '' }}>Diproses</option>
                                <option value="shipping" {{ request('transaction_status') == 'shipping' ? 'selected' : '' }}>Dikirim</option>
                                <option value="delivered" {{ request('transaction_status') == 'delivered' ? 'selected' : '' }}>Selesai</option>
                            </select>
                        </div>
                    </div>

                    {{-- Bagian Search Kanan --}}
                    <div class="flex items-center gap-2 w-full lg:w-auto">
                        <label class="input input-bordered input-md flex items-center gap-2 bg-white w-full lg:w-80">
                            <i class="fas fa-search text-gray-400"></i>
                            <input type="text" name="q" class="grow text-black min-w-0" placeholder="Cari No. Invoice atau Pelanggan..." value="{{ request('q') }}" />
                        </label>
                        <button type="submit" class="btn btn-md bg-blue-500 hover:bg-blue-600 text-white border-none">
                            Cari
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Tabel Transaksi --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 border-b border-gray-100">
                            <th class="w-16">No</th>
                            <th>Invoice & Tanggal</th>
                            <th>Pelanggan</th>
                            <th>Total Tagihan</th>
                            <th>Detail Pesanan</th>
                            <th>Bukti Pengiriman</th>
                            <th class="text-center">Status Bayar</th>
                            <th class="text-center">Status Transaksi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-black">
                        @forelse ($transactions as $index => $trx)
                            <tr class="hover:bg-blue-50/30 transition-colors">
                                <td>{{ $transactions->firstItem() + $index }}</td>
                                <td>
                                    <div class="font-bold text-blue-600">{{ $trx->invoice }}</div>
                                    <div class="text-xs text-gray-500">{{ $trx->created_at->format('d M Y, H:i') }}</div>
                                </td>

                                <td>
                                    <div class="font-medium text-gray-900">{{ $trx->customer->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $trx->customer->email }}</div>
                                </td>

                                <td class="font-bold text-gray-900">
                                    Rp {{ number_format($trx->total_price, 0, ',', '.') }}
                                </td>

                                <td class="font-bold text-gray-900">
                                    <button class="btn btn-sm bg-blue-400 text-blue-100 border-none hover:bg-blue-500 tooltip" data-tip="Lihat Detail Pesanan" onclick="showTrxDetail({{ $trx->id }})">
                                        <i class="fa-solid fa-eye text-sm"></i>
                                    </button>
                                </td>

                                <td class="text-center">
                                    @if ($trx->proof_of_delivery)
                                        <button class="btn btn-sm bg-blue-400 text-white border-none hover:bg-blue-500 tooltip" data-tip="Lihat Bukti" onclick="showProofModal('{{ $trx->proof_of_delivery }}', '{{ $trx->invoice }}')">
                                            <i class="fa-solid fa-image"></i>
                                        </button>
                                    @else
                                        <button class="btn btn-sm bg-gray-100 text-gray-400 border-none cursor-not-allowed" disabled>
                                            <i class="fa-solid fa-image"></i>
                                        </button>
                                    @endif
                                </td>

                                <td class="text-center">
                                    @php
                                        $pStatus = [
                                            'pending' => 'bg-orange-100 text-orange-700',
                                            'settlement' => 'bg-green-100 text-green-700',
                                            'expire' => 'bg-red-100 text-red-700',
                                            'cancel' => 'bg-gray-100 text-gray-700',
                                        ];
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-[11px] font-bold uppercase {{ $pStatus[$trx->payment_status] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ $trx->payment_status == 'settlement' ? 'LUNAS' : $trx->payment_status }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    @php
                                        $tStatus = [
                                            'pending' => 'bg-gray-100 text-gray-600',
                                            'processing' => 'bg-blue-100 text-blue-700',
                                            'shipping' => 'bg-purple-100 text-purple-700',
                                            'delivered' => 'bg-green-100 text-green-700',
                                        ];
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-[11px] font-bold uppercase {{ $tStatus[$trx->transaction_status] ?? 'bg-gray-100 text-gray-600' }}">
                                        @if ($trx->transaction_status == 'processing' && empty($trx->courier_id))
                                            Diproses
                                        @endif
                                        @if ($trx->transaction_status == 'processing' && !empty($trx->courier_id))
                                            Ditugaskan kepada Kurir
                                        @endif
                                        @if ($trx->transaction_status == 'shipping')
                                            Dikirim
                                        @endif
                                        @if ($trx->transaction_status == 'delivered')
                                            Selesai
                                        @endif
                                    </span>
                                </td>

                                <td>
                                    @if (empty($trx->courier_id))
                                        <button class="btn btn-sm bg-yellow-400 tooltip" data-tip="Tugaskan Kurir" onclick="openStatusModal({{ $trx->id }}, '{{ $trx->invoice }}', '{{ $trx->transaction_status }}')" ...>
                                            <i class="fa-solid fa-truck-fast text-sm text-gray-100"></i>
                                        </button>
                                    @endif

                                    @if ($trx->transaction_status == 'processing' && !empty($trx->courier_id))
                                        <button class="btn btn-sm bg-gray-400" disabled>
                                            <i class="fa-solid fa-user-clock text-sm text-white"></i>
                                        </button>
                                    @endif

                                    @if (!empty($trx->courier_id && $trx->transaction_status == 'shipping'))
                                        <button class="btn btn-sm bg-gray-400" disabled>
                                            <i class="fa-solid fa-user-clock text-sm text-white"></i>
                                        </button>
                                    @endif

                                    @if (!empty($trx->courier_id && $trx->transaction_status == 'delivered'))
                                        <button class="btn btn-sm bg-green-100 tooltip" data-tip="Selesai" disabled>
                                            <i class="fa-solid fa-square-check text-sm text-green-700"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-20">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                            <i class="fa-solid fa-inbox text-5xl text-gray-200"></i>
                                        </div>
                                        <p class="text-gray-500 font-medium italic">
                                            Tidak ada data transaksi yang ditemukan.
                                        </p>
                                        <p class="text-xs text-gray-400 mt-1">Coba sesuaikan filter atau kata kunci pencarian Anda.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="mt-6 flex justify-between items-center">
            <div class="text-sm text-gray-500">
                Menampilkan {{ $transactions->firstItem() }} - {{ $transactions->lastItem() }} dari {{ $transactions->total() }} transaksi
            </div>
            <div>
                {{ $transactions->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

    {{-- MODAL PLACEHOLDERS --}}
    <dialog id="modal_trx_detail" class="modal">
        <div class="modal-box bg-white max-w-4xl p-0 overflow-hidden">
            {{-- Header --}}
            <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="font-bold text-lg text-gray-900">Detail Transaksi</h3>
                <button class="btn btn-sm btn-circle btn-ghost" onclick="document.getElementById('modal_trx_detail').close()">✕</button>
            </div>
            <div class="p-6 text-black" id="trx-detail-content">
                {{-- Data via AJAX --}}
                <div class="flex justify-center py-10"><span class="loading loading-spinner loading-lg text-blue-500"></span></div>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>

    {{-- MODAL UPDATE STATUS & ASSIGN DRIVER --}}
    <dialog id="modal_status_update" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-white p-0 overflow-hidden max-w-md border border-gray-100">
            <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-gray-900">Penugasan Kurir</h3>
                    {{-- DISPLAY INVOICE DINAMIS --}}
                    <p class="text-xs text-blue-600 font-mono font-bold" id="display-invoice">INV-0000000000</p>
                </div>
                <button class="btn btn-sm btn-circle btn-ghost" onclick="document.getElementById('modal_status_update').close()">✕</button>
            </div>

            <form id="form-update-status" class="p-6 space-y-4">
                @csrf
                <input type="hidden" id="status-trx-id">

                <div class="form-control">
                    <label class="label"><span class="label-text font-bold text-gray-700">Pilih Driver / Kurir</span></label>
                    <select id="select-courier" class="select select-bordered w-full bg-white text-black focus:border-blue-400" required>
                        <option value="" disabled selected>Memuat daftar driver...</option>
                    </select>
                </div>

                <div class="bg-blue-50 p-3 rounded-lg border border-blue-100 mb-2">
                    <p class="text-xs text-blue-800 leading-relaxed">
                        <i class="fas fa-info-circle mr-1"></i>
                        Kurir yang dipilih akan bertanggung jawab mengirimkan pesanan ini sampai ke tangan pelanggan.
                    </p>
                </div>

                <div class="pt-4">
                    <button type="submit" id="btn-text-update" class="btn bg-blue-500 hover:bg-blue-600 w-full text-white border-none shadow-lg shadow-blue-100">
                        Konfirmasi Penugasan
                    </button>
                </div>
            </form>
        </div>
    </dialog>

    {{-- MODAL LIHAT BUKTI PENGIRIMAN --}}
    <dialog id="modal_proof_view" class="modal">
        <div class="modal-box bg-white p-0 overflow-hidden max-w-lg">
            {{-- Header dengan Invoice --}}
            <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <div>
                    <h3 class="font-bold text-gray-900 leading-none">Bukti Pengiriman</h3>
                    {{-- ID BARU: proof-invoice-display --}}
                    <p id="proof-invoice-display" class="text-[11px] text-blue-600 font-mono font-bold mt-1 uppercase tracking-wider"></p>
                </div>
                <button class="btn btn-sm btn-circle btn-ghost" onclick="document.getElementById('modal_proof_view').close()">✕</button>
            </div>

            <div class="p-4 flex flex-col items-center">
                <img id="proof-img-display" src="" class="w-full rounded-xl shadow-sm border border-gray-200 object-contain max-h-[70vh]">
                <div class="mt-4 w-full">
                    <a id="btn-download-proof" href="" download class="btn btn-sm btn-block bg-blue-500 hover:bg-blue-600 text-white border-none">
                        <i class="fas fa-download mr-2"></i> Simpan Gambar
                    </a>
                </div>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>
@endsection

@push('styles')
    <style>
        /* Sama seperti style pagination varian Anda sebelumnya */
        nav[role="navigation"] {
            background-color: transparent !important;
        }

        nav[role="navigation"] a {
            background-color: #ffffff !important;
            color: #374151 !important;
            border-color: #e5e7eb !important;
        }

        nav[role="navigation"] a:hover {
            background-color: #f3f4f6 !important;
        }

        nav[role="navigation"] span[aria-disabled="true"] span,
        nav[role="navigation"] span[aria-disabled="true"] {
            background-color: #ffffff !important;
            color: #d1d5db !important;
            border-color: #e5e7eb !important;
            cursor: not-allowed;
        }

        nav[role="navigation"] span[aria-current="page"]>span {
            background-color: #3b82f6 !important;
            color: #ffffff !important;
            border-color: #3b82f6 !important;
        }

        nav[role="navigation"] svg {
            width: 20px;
            height: 20px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Fungsi Format Rupiah
        const formatRupiah = (num) => {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(num);
        };

        function showTrxDetail(id) {
            const modal = document.getElementById('modal_trx_detail');
            const container = document.getElementById('trx-detail-content');

            container.innerHTML = '<div class="flex justify-center py-10"><span class="loading loading-spinner loading-lg text-blue-500"></span></div>';
            modal.showModal();

            fetch(`/transaction/show/${id}`)
                .then(response => response.json())
                .then(data => {
                    let itemsHtml = '';
                    data.details.forEach(item => {
                        const img = item.variant.images.length > 0 ?
                            `/storage/${item.variant.images[0].image_path}` :
                            '/assets/images/dummy-shoe.jpg';

                        itemsHtml += `
                <tr class="text-xs border-b border-gray-100">
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-gray-50 rounded-lg overflow-hidden border border-gray-100 shrink-0">
                                <img src="${img}" class="w-full h-full object-contain p-1">
                            </div>
                            <div>
                                <div class="font-bold text-gray-900">${item.variant.shoe.name}</div>
                                <div class="text-[10px] text-gray-400 uppercase">Warna: ${item.variant.color} | Size: ${item.variant.size}</div>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">${item.qty}</td>
                    <td class="text-right">${formatRupiah(item.price)}</td>
                    <td class="text-right font-bold text-gray-900">${formatRupiah(item.price * item.qty)}</td>
                </tr>`;
                    });

                    // Logika Tampilan Driver
                    let driverHtml = '';
                    if (data.courier) {
                        driverHtml = `
                    <div class="flex items-center gap-3 bg-blue-50 p-3 rounded-xl border border-blue-100">
                        <div class="avatar">
                            <div class="w-10 rounded-full ring ring-blue-200 ring-offset-base-100 ring-offset-2">
                                <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(data.courier.name)}&background=0D8ABC&color=fff" />
                            </div>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-blue-600 uppercase leading-none mb-1">Kurir</p>
                            <p class="text-sm font-bold text-gray-900">${data.courier.name}</p>
                        </div>
                    </div>
                `;
                    } else {
                        driverHtml = `
                    <div class="flex items-center gap-3 bg-gray-50 p-3 rounded-xl border border-gray-200 opacity-60">
                        <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-400">
                            <i class="fas fa-user-slash"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-500 uppercase leading-none mb-1">Driver</p>
                            <p class="text-xs italic text-gray-400">Belum ditugaskan</p>
                        </div>
                    </div>
                `;
                    }

                    container.innerHTML = `
            <div class="space-y-8">
                {{-- Grid Informasi Atas --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div>
                        <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Pelanggan</h4>
                        <p class="text-sm font-bold text-gray-900">${data.customer.name}</p>
                        <p class="text-xs text-gray-500">${data.customer.email}</p>
                        <p class="text-xs text-gray-500">${data.customer.phone || '-'}</p>
                    </div>
                    <div>
                        <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Alamat Pengiriman</h4>
                        <p class="text-xs font-bold text-gray-800">${data.address.recipient_name}</p>
                        <p class="text-xs text-gray-600 leading-relaxed mt-1">
                            ${data.address.full_address}, <br>
                            Kec. ${data.address.district}, ${data.address.village}
                        </p>
                    </div>
                    <div>
                        <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Pembayaran</h4>
                        <span class="badge badge-outline font-bold text-[10px] uppercase mb-2">${data.payment_type || 'Belum Memilih'}</span>
                        <p class="text-[10px] text-gray-400">Status Bayar:</p>
                        <p class="text-[10px] font-bold text-blue-600 uppercase">${data.payment_status}</p>
                    </div>
                    <div>
                        <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Informasi Kurir</h4>
                        ${driverHtml}
                    </div>
                </div>

                {{-- Tabel Item --}}
                <div>
                    <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">Item Pesanan</h4>
                    <div class="overflow-x-auto border border-gray-100 rounded-xl">
                        <table class="table w-full">
                            <thead class="bg-gray-50">
                                <tr class="text-gray-500 text-[10px] uppercase tracking-wider border-b border-gray-100">
                                    <th>Produk</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-right">Harga</th>
                                    <th class="text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${itemsHtml}
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Rincian Biaya --}}
                <div class="flex justify-end">
                    <div class="w-full md:w-64 space-y-2 pt-4">
                        <div class="flex justify-between text-xs text-gray-500">
                            <span>Subtotal</span>
                            <span>${formatRupiah(data.subtotal)}</span>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500">
                            <span>Ongkos Kirim</span>
                            <span>${formatRupiah(data.shipping_cost)}</span>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500">
                            <span>Biaya Admin</span>
                            <span>${formatRupiah(data.admin_fee)}</span>
                        </div>
                        <div class="flex justify-between text-sm font-bold text-gray-900 border-t border-gray-200 pt-2">
                            <span>Total Tagihan</span>
                            <span class="text-blue-600">${formatRupiah(data.total_price)}</span>
                        </div>
                    </div>
                </div>
            </div>`;
                })
                .catch(error => {
                    container.innerHTML = `<div class="text-center py-10 text-red-500">Gagal mengambil data: ${error.message}</div>`;
                });
        }

        function showProofModal(path, invoice) { // Tambahkan parameter invoice
            const modal = document.getElementById('modal_proof_view');
            const img = document.getElementById('proof-img-display');
            const invoiceLabel = document.getElementById('proof-invoice-display'); // Ambil elemen invoice
            const btnDownload = document.getElementById('btn-download-proof');

            const fullPath = `/storage/${path}`;

            // Set Data
            img.src = fullPath;
            btnDownload.href = fullPath;
            invoiceLabel.innerText = invoice; // Masukkan teks invoice ke label

            modal.showModal();
        }

        // 1. Fungsi Membuka Modal Status dengan Invoice Dinamis
        function openStatusModal(id, invoice, currentStatus) {
            const modal = document.getElementById('modal_status_update');
            const selectCourier = document.getElementById('select-courier');

            // Set data ke form
            document.getElementById('status-trx-id').value = id;
            // Tampilkan invoice di modal
            document.getElementById('display-invoice').innerText = invoice;

            modal.showModal();

            // Reset dan Ambil daftar driver
            selectCourier.innerHTML = '<option value="" disabled selected>Memuat daftar driver...</option>';

            fetch('/transaction/get-courier')
                .then(res => res.json())
                .then(drivers => {
                    let options = '<option value="" disabled selected>-- Pilih Driver --</option>';
                    drivers.forEach(driver => {
                        options += `<option value="${driver.id}">${driver.name}</option>`;
                    });
                    selectCourier.innerHTML = options;
                })
                .catch(err => {
                    selectCourier.innerHTML = '<option value="" disabled>Gagal memuat driver</option>';
                });
        }

        // 2. Handle Submit Form Update
        document.getElementById('form-update-status').addEventListener('submit', function(e) {
            e.preventDefault();

            const id = document.getElementById('status-trx-id').value;
            const courier_id = document.getElementById('select-courier').value;
            const btnSubmit = document.getElementById('btn-text-update');

            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<span class="loading loading-spinner loading-sm"></span> Menyimpan...';

            fetch(`/transaction/update-status/${id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify({
                        courier_id: courier_id,
                        // Saat admin menugaskan kurir, status otomatis berubah ke 'processing' atau 'shipping'
                        transaction_status: 'processing'
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert('Gagal: ' + data.message);
                        btnSubmit.disabled = false;
                        btnSubmit.innerText = 'Konfirmasi Penugasan';
                    }
                })
                .catch(err => {
                    alert('Terjadi kesalahan koneksi.');
                    btnSubmit.disabled = false;
                    btnSubmit.innerText = 'Konfirmasi Penugasan';
                });
        });
    </script>
@endpush
