@extends('layouts/backend/index')
@section('title', 'ShoeCycle | Daftar Pengiriman')
@section('breadcrumb', 'Daftar Pengiriman')

@section('backend-content')
    <div class="space-y-6 max-w-lg mx-auto">
        {{-- Header Kurir & Stats --}}
        <div class="bg-gradient-to-r from-blue-600 to-blue-400 rounded-2xl p-6 text-white shadow-lg shadow-blue-100">
            <p class="text-sm opacity-80 uppercase tracking-widest font-bold">Halo, {{ auth()->user()->name }}!</p>
            <h2 class="text-2xl font-bold mt-1">Daftar Pengiriman</h2>
            <div class="mt-4 flex gap-4">
                <div class="bg-white/20 px-3 py-2 rounded-xl backdrop-blur-sm border border-white/10 flex-1 text-center">
                    <p class="text-[10px] uppercase font-bold opacity-80 leading-none mb-1">Perlu Dikirim</p>
                    <p class="text-xl font-bold">{{ $transactions->where('transaction_status', 'processing')->count() }}</p>
                </div>
                <div class="bg-white/20 px-3 py-2 rounded-xl backdrop-blur-sm border border-white/10 flex-1 text-center">
                    <p class="text-[10px] uppercase font-bold opacity-80 leading-none mb-1">Dikirim</p>
                    <p class="text-xl font-bold">{{ $transactions->where('transaction_status', 'shipping')->count() }}</p>
                </div>
                <div class="bg-white/20 px-3 py-2 rounded-xl backdrop-blur-sm border border-white/10 flex-1 text-center text-green-100">
                    <p class="text-[10px] uppercase font-bold opacity-80 leading-none mb-1">Selesai</p>
                    <p class="text-xl font-bold">{{ $transactions->where('transaction_status', 'delivered')->count() }}</p>
                </div>
            </div>
        </div>

        {{-- NAVIGATION FILTER TABS --}}
        <div class="flex overflow-x-auto gap-2 no-scrollbar pb-1">
            <button onclick="filterStatus('all')" class="filter-btn btn btn-sm rounded-full border-none bg-blue-600 text-white px-5 shadow-md shadow-blue-100" data-filter="all">Semua</button>
            <button onclick="filterStatus('processing')" class="filter-btn btn btn-sm rounded-full border-none bg-white text-gray-600 px-5 shadow-sm" data-filter="processing">Perlu Dikirim</button>
            <button onclick="filterStatus('shipping')" class="filter-btn btn btn-sm rounded-full border-none bg-white text-gray-600 px-5 shadow-sm" data-filter="shipping">Sedang Jalan</button>
            <button onclick="filterStatus('delivered')" class="filter-btn btn btn-sm rounded-full border-none bg-white text-gray-600 px-5 shadow-sm" data-filter="delivered">Selesai</button>
        </div>

        {{-- DAFTAR PESANAN --}}
        <div class="space-y-4" id="delivery-container">
            @forelse ($transactions as $trx)
                {{-- Tambahkan class 'delivery-card' dan atribut 'data-status' --}}
                <div class="delivery-card bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden" data-status="{{ $trx->transaction_status }}">

                    {{-- Status Bar Logic --}}
                    @php
                        $statusBg = 'bg-gray-50';
                        $badgeClass = 'bg-gray-100 text-gray-600';
                        $statusLabel = 'Menunggu Pickup';

                        if ($trx->transaction_status == 'shipping') {
                            $statusBg = 'bg-blue-50';
                            $badgeClass = 'bg-blue-100 text-blue-700';
                            $statusLabel = 'Sedang Dikirim';
                        } elseif ($trx->transaction_status == 'delivered') {
                            $statusBg = 'bg-green-50';
                            $badgeClass = 'bg-green-100 text-green-700';
                            $statusLabel = 'Selesai Terkirim';
                        }
                    @endphp

                    <div class="px-4 py-2 border-b border-gray-50 flex justify-between items-center {{ $statusBg }}">
                        <span class="text-[10px] font-bold font-mono text-gray-500">{{ $trx->invoice }}</span>
                        <span class="badge badge-sm font-bold border-none {{ $badgeClass }}">
                            {{ $statusLabel }}
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
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $trx->address->phone_number) }}" class="btn btn-circle btn-sm bg-green-500 border-none text-white hover:bg-green-600">
                                <i class="fab fa-whatsapp text-lg"></i>
                            </a>
                        </div>

                        {{-- Info Alamat --}}
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-red-50 rounded-full flex items-center justify-center text-red-500 shrink-0">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs text-gray-400 uppercase font-bold tracking-tighter leading-none mb-1">Alamat</p>
                                <p class="text-xs text-gray-600 leading-relaxed">{{ $trx->address->full_address }}</p>
                            </div>
                        </div>

                        {{-- Aksi Driver --}}
                        <div class="pt-2">
                            @if ($trx->transaction_status == 'processing')
                                <button onclick="updateStatus({{ $trx->id }}, 'shipping')" class="btn btn-block bg-blue-500 hover:bg-blue-600 text-white border-none rounded-xl h-12 shadow-lg shadow-blue-100">
                                    <i class="fas fa-motorcycle mr-2"></i> Mulai Kirim Sekarang
                                </button>
                            @elseif($trx->transaction_status == 'shipping')
                                <button onclick="updateStatus({{ $trx->id }}, 'delivered', '{{ $trx->invoice }}', '{{ $trx->address->recipient_name }}')" class="btn btn-block bg-green-500 hover:bg-green-600 text-white border-none rounded-xl h-12 shadow-lg shadow-green-100">
                                    <i class="fas fa-check-circle text-sm mr-2"></i> Selesaikan Pengiriman
                                </button>
                            @else
                                {{-- Jika Selesai, tampilkan Bukti Pengiriman (Opsional) --}}
                                <div class="bg-green-50 p-3 rounded-xl border border-green-100 flex items-center gap-3">
                                    <i class="fas fa-check-double text-green-600"></i>
                                    <span class="text-xs text-green-700 font-bold uppercase">Pesanan Telah Sampai</span>
                                </div>
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

    {{-- MODAL UPLOAD BUKTI PENGIRIMAN --}}
    <dialog id="modal_proof" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-white p-0 overflow-hidden max-w-md">
            <div class="p-4 border-b border-gray-200 bg-gray-50 flex justify-between items-start">
                <div>
                    <h3 class="font-bold text-gray-900">Selesaikan Pengiriman</h3>
                    <div class="flex flex-col mt-0.5">
                        <span id="display-invoice-proof" class="text-[10px] font-mono font-bold text-blue-600 uppercase tracking-wider">INV-XXXXXXXX</span>
                        <span id="display-customer-proof" class="text-xs text-gray-500 font-medium italic">Nama Pelanggan</span>
                    </div>
                </div>
                <button class="btn btn-sm btn-circle btn-ghost" onclick="closeProofModal()">✕</button>
            </div>

            <form id="form-proof" class="p-6 space-y-4">
                <input type="hidden" id="proof-trx-id">

                <div class="space-y-2">
                    <label class="label"><span class="label-text font-bold">Foto Bukti Penerimaan</span></label>

                    {{-- Dropzone / Camera Trigger --}}
                    <div class="relative group">
                        <input type="file" id="proof-image" accept="image/*" capture="environment" class="hidden" onchange="previewImage(this)">
                        <div onclick="document.getElementById('proof-image').click()" class="border-2 border-dashed border-gray-300 rounded-2xl p-8 text-center hover:border-blue-400 transition-colors cursor-pointer bg-gray-50 overflow-hidden min-h-[200px] flex flex-col items-center justify-center">

                            <div id="placeholder-upload">
                                <i class="fas fa-camera text-4xl text-gray-400 mb-2"></i>
                                <p class="text-xs text-gray-500 font-medium">Klik untuk Ambil Foto / Pilih File</p>
                            </div>

                            <img id="preview-img" class="absolute inset-0 w-full h-full object-cover hidden">
                        </div>
                    </div>
                </div>

                <div class="pt-4 flex gap-2">
                    <button type="submit" id="btn-submit-proof" class="btn bg-blue-500 flex-1 text-white shadow-lg shadow-blue-100" disabled>
                        Simpan & Selesai
                    </button>
                </div>
            </form>
        </div>
    </dialog>
@endsection
@push('scripts')
    <script>
        /**
         * 1. Tombol Utama (Buka Modal atau Langsung Update)
         */
        function updateStatus(id, newStatus, invoice = '', customerName = '') {
            if (newStatus === 'delivered') {
                document.getElementById('proof-trx-id').value = id;
                document.getElementById('display-invoice-proof').innerText = invoice;
                document.getElementById('display-customer-proof').innerText = "Penerima: " + customerName;

                // Tampilkan modal
                document.getElementById('modal_proof').showModal();
            } else {
                // Untuk status 'shipping' (Mulai Kirim), langsung proses tanpa modal
                if (confirm('Mulai kirim pesanan ini?')) {
                    processUpdate(id, newStatus);
                }
            }
        }

        function filterStatus(status) {
            // Animasi tombol filter
            const btns = document.querySelectorAll('.filter-btn');
            btns.forEach(btn => {
                if (btn.getAttribute('data-filter') === status) {
                    btn.classList.add('bg-blue-600', 'text-white', 'shadow-md');
                    btn.classList.remove('bg-white', 'text-gray-600');
                } else {
                    btn.classList.remove('bg-blue-600', 'text-white', 'shadow-md');
                    btn.classList.add('bg-white', 'text-gray-600');
                }
            });

            // Sembunyikan/Tampilkan Card
            const cards = document.querySelectorAll('.delivery-card');
            cards.forEach(card => {
                if (status === 'all' || card.getAttribute('data-status') === status) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function processUpdate(id, newStatus) {
            fetch(`/delivery/update-status/${id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        transaction_status: newStatus
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert('Gagal: ' + data.message);
                    }
                })
                .catch(err => alert('Terjadi kesalahan koneksi ke server.'));
        }

        document.getElementById('form-proof').addEventListener('submit', function(e) {
            e.preventDefault();

            const id = document.getElementById('proof-trx-id').value;
            const fileInput = document.getElementById('proof-image');
            const btn = document.getElementById('btn-submit-proof');

            // Kita gunakan FormData karena mengirimkan File/Gambar
            const formData = new FormData();
            formData.append('transaction_status', 'delivered');
            formData.append('proof_of_delivery', fileInput.files[0]);

            // Efek loading pada tombol
            btn.disabled = true;
            btn.innerHTML = '<span class="loading loading-spinner loading-xs"></span> Menyimpan...';

            fetch(`/delivery/update-status/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' // Jangan set Content-Type manual jika pakai FormData
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert('Gagal: ' + data.message);
                        btn.disabled = false;
                        btn.innerHTML = 'Simpan & Selesai';
                    }
                })
                .catch(err => {
                    alert('Kesalahan jaringan.');
                    btn.disabled = false;
                    btn.innerHTML = 'Simpan & Selesai';
                });
        });

        /**
         * 4. Fungsi Preview Gambar
         */
        function previewImage(input) {
            const preview = document.getElementById('preview-img');
            const placeholder = document.getElementById('placeholder-upload');
            const btnSubmit = document.getElementById('btn-submit-proof');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                    btnSubmit.disabled = false;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        /**
         * 5. Tutup Modal & Reset
         */
        function closeProofModal() {
            document.getElementById('modal_proof').close();
            document.getElementById('form-proof').reset();
            document.getElementById('preview-img').classList.add('hidden');
            document.getElementById('placeholder-upload').classList.remove('hidden');
            document.getElementById('btn-submit-proof').disabled = true;
        }
    </script>
@endpush
