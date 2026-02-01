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
                <div class="delivery-card bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden" data-status="{{ $trx->transaction_status }}">

                    @php
                        $statusInfo = [
                            'processing' => ['bg' => 'bg-gray-100', 'badge' => 'bg-gray-100 text-gray-600', 'label' => 'Siap Pickup'],
                            'shipping' => ['bg' => 'bg-blue-50', 'badge' => 'bg-blue-100 text-blue-700', 'label' => 'Sedang Dikirim'],
                            'delivered' => ['bg' => 'bg-green-50', 'badge' => 'bg-green-100 text-green-700', 'label' => 'Selesai'],
                        ];
                        $curr = $statusInfo[$trx->transaction_status] ?? $statusInfo['processing'];
                    @endphp

                    <div class="px-4 py-2 border-b border-gray-50 flex justify-between items-center {{ $curr['bg'] }}">
                        <span class="text-[10px] font-bold font-mono text-gray-500">{{ $trx->invoice }}</span>
                        <span class="badge badge-sm font-bold border-none {{ $curr['badge'] }}">
                            {{ $curr['label'] }}
                        </span>
                    </div>

                    <div class="p-4 space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-blue-50 rounded-full flex items-center justify-center text-blue-600 shrink-0"><i class="fas fa-user"></i></div>
                            <div class="min-w-0 flex-1">
                                <p class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter leading-none mb-1">Penerima</p>
                                <h4 class="font-bold text-gray-900 truncate">{{ $trx->address->recipient_name }}</h4>
                                <p class="text-sm font-bold text-blue-600">{{ $trx->address->phone_number }}</p>
                            </div>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $trx->address->phone_number) }}" target="_blank" class="btn btn-circle btn-sm bg-green-500 border-none text-white hover:bg-green-600">
                                <i class="fab fa-whatsapp text-lg"></i>
                            </a>
                        </div>

                        <div class="flex items-start gap-3">
                            {{-- Ikon Alamat --}}
                            <div class="w-10 h-10 bg-red-50 rounded-full flex items-center justify-center text-red-500 shrink-0">
                                <i class="fas fa-map-marker-alt text-sm"></i>
                            </div>

                            {{-- Teks Alamat --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-[10px] text-gray-400 uppercase font-bold leading-none mb-1">Alamat Pengiriman</p>
                                <p class="text-xs text-gray-600 leading-relaxed">{{ $trx->address->full_address }}</p>
                            </div>

                            {{-- Tombol Navigasi Google Maps (Pojok Kanan Atas Alamat) --}}
                            <a href="https://www.google.com/maps/dir/?api=1&destination={{ $trx->address->latitude }},{{ $trx->address->longitude }}" target="_blank" class="btn btn-circle btn-sm bg-blue-500 border-none text-white hover:bg-blue-600 shadow-sm shrink-0">
                                <i class="fa-solid fa-diamond-turn-right text-sm"></i>
                            </a>
                        </div>

                        <div class="flex items-start gap-3 pt-3 border-t border-dashed border-gray-100">
                            <div class="w-10 h-10 bg-amber-50 rounded-full flex items-center justify-center text-amber-500 shrink-0"><i class="fas fa-box text-sm"></i></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider leading-none mb-2">Isi Paket ({{ $trx->details->sum('qty') }} Item)</p>
                                <div class="space-y-2">
                                    @foreach ($trx->details as $detail)
                                        <div class="flex justify-between items-center gap-2 bg-slate-50 p-2 rounded-xl border border-gray-100">
                                            <div class="min-w-0">
                                                <p class="text-xs font-bold text-gray-800 truncate">{{ $detail->variant->shoe->name }}</p>
                                                <p class="text-[10px] text-gray-500">Size {{ $detail->variant->size }} | {{ $detail->variant->color }}</p>
                                            </div>
                                            <div class="bg-white border border-gray-200 px-2 py-1 rounded-lg text-[10px] font-black text-blue-600">{{ $detail->qty }}x</div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="pt-2">
                            @if ($trx->transaction_status == 'processing')
                                <button onclick="openActionModal(@js($trx->load('details.variant.shoe', 'address')), 'pickup')" class="btn btn-block bg-blue-500 hover:bg-blue-600 text-white border-none rounded-xl h-12 shadow-lg">
                                    <i class="fas fa-motorcycle mr-2 text-sm"></i> Kirim Paket
                                </button>
                            @elseif($trx->transaction_status == 'shipping')
                                <button onclick="openActionModal(@js($trx->load('details.variant.shoe', 'address')), 'deliver')" class="btn btn-block bg-blue-400 hover:bg-blue-500 text-white border-none rounded-xl h-12 shadow-lg">
                                    Selesaikan Pengiriman
                                </button>
                            @else
                                <div class="bg-green-200 p-3 rounded-xl border border-green-100 flex items-center justify-center gap-3">
                                    <i class="fas fa-check-double text-green-600"></i>
                                    <span class="text-xs text-green-700 font-extrabold uppercase">Terkirim</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-20 opacity-40">
                    <i class="fas fa-box-open text-6xl mb-3"></i>
                    <p class="font-bold">Belum ada pengiriman</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- DAISYUI DYNAMIC ACTION MODAL --}}
    <dialog id="modal_delivery_action" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-white p-0 overflow-hidden max-w-md">
            <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-start">
                <div>
                    <h3 class="font-bold text-gray-900" id="mdl-title">Konfirmasi</h3>
                    <div class="flex flex-col mt-0.5">
                        <span id="mdl-invoice" class="text-[10px] font-mono font-bold text-blue-600 uppercase tracking-wider"></span>
                        <span id="mdl-customer" class="text-xs text-gray-500 font-medium italic leading-none mt-1"></span>
                    </div>
                </div>
                <button class="btn btn-sm btn-circle btn-ghost" onclick="document.getElementById('modal_delivery_action').close()">✕</button>
            </div>

            <form id="form-delivery-action">
                @csrf
                <input type="hidden" id="mdl-trx-id">
                <input type="hidden" id="mdl-next-status">

                <div class="p-6 space-y-5 max-h-[65vh] overflow-y-auto no-scrollbar">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">Isi Paket & Spesifikasi</p>
                        <div id="mdl-items-list" class="space-y-2"></div>
                    </div>

                    <div class="bg-slate-50 p-4 rounded-xl border border-gray-100">
                        <p class="text-[10px] font-extrabold text-blue-600 uppercase tracking-widest mb-1">Tujuan Pengiriman</p>
                        <p id="mdl-address" class="text-xs text-gray-600 leading-relaxed"></p>
                    </div>

                    <div id="mdl-proof-section" class="hidden space-y-2">
                        <label class="label p-0"><span class="label-text font-bold text-gray-700">Foto Bukti Penerimaan</span></label>
                        <div class="relative w-full">
                            <input type="file" id="mdl-proof-image" accept="image/*" capture="environment" class="hidden" onchange="previewActionImage(this)">
                            <div id="mdl-placeholder" onclick="document.getElementById('mdl-proof-image').click()" class="border-2 border-dashed border-gray-300 rounded-2xl p-8 text-center bg-gray-50 cursor-pointer hover:border-blue-400 transition-all flex flex-col items-center justify-center min-h-[160px]">
                                <i class="fas fa-camera text-xl text-blue-500 mb-2"></i>
                                <p class="text-xs text-gray-500 font-bold uppercase">Ambil Foto Bukti</p>
                            </div>
                            <div id="mdl-preview-container" class="hidden relative rounded-2xl overflow-hidden border border-gray-200">
                                <img id="mdl-preview-img" src="" class="w-full h-auto block mx-auto max-h-[450px] object-contain">
                                <button type="button" onclick="removePreviewImage()" class="absolute top-3 right-3 btn btn-circle btn-xs bg-red-500 text-white border-none shadow-lg"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-4 border-t border-gray-100 bg-gray-50">
                    <button type="submit" id="mdl-btn-submit" class="btn bg-blue-500 btn-block text-white shadow-lg rounded-xl">Konfirmasi</button>
                </div>
            </form>
        </div>
    </dialog>
@endsection

@push('scripts')
    <script>
        function openActionModal(trx, type) {
            const modal = document.getElementById('modal_delivery_action');
            document.getElementById('mdl-trx-id').value = trx.id;
            document.getElementById('mdl-invoice').innerText = trx.invoice;
            document.getElementById('mdl-customer').innerText = "Penerima: " + trx.address.recipient_name;
            document.getElementById('mdl-address').innerText = trx.address.full_address;

            let itemsHtml = '';
            trx.details.forEach(item => {
                itemsHtml += `
                    <div class="flex items-center justify-between bg-white border border-gray-100 p-3 rounded-xl shadow-sm">
                        <div class="min-w-0 pr-2">
                            <p class="text-xs font-bold text-gray-800 truncate">${item.variant.shoe.name}</p>
                            <p class="text-[10px] text-gray-500 uppercase">Size ${item.variant.size} | ${item.variant.color}</p>
                        </div>
                        <div class="bg-blue-100 text-blue-700 px-2.5 py-1 rounded-lg text-xs font-black">${item.qty}x</div>
                    </div>`;
            });
            document.getElementById('mdl-items-list').innerHTML = itemsHtml;

            removePreviewImage();

            const proofSection = document.getElementById('mdl-proof-section');
            const submitBtn = document.getElementById('mdl-btn-submit');
            const statusInput = document.getElementById('mdl-next-status');

            if (type === 'pickup') {
                document.getElementById('mdl-title').innerText = "Konfirmasi Pickup";
                proofSection.classList.add('hidden');
                submitBtn.innerText = "Mulai Pengiriman";
                submitBtn.disabled = false;
                statusInput.value = 'shipping';
            } else {
                document.getElementById('mdl-title').innerText = "Selesaikan Pengiriman";
                proofSection.classList.remove('hidden');
                submitBtn.innerText = "Simpan & Selesai";
                submitBtn.disabled = true;
                statusInput.value = 'delivered';
            }
            modal.showModal();
        }

        document.getElementById('form-delivery-action').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('mdl-btn-submit');
            const id = document.getElementById('mdl-trx-id').value;
            const nextStatus = document.getElementById('mdl-next-status').value;
            const fileInput = document.getElementById('mdl-proof-image');

            const formData = new FormData();
            formData.append('transaction_status', nextStatus);
            if (nextStatus === 'delivered') formData.append('proof_of_delivery', fileInput.files[0]);

            // Native Loading State
            btn.disabled = true;
            btn.innerHTML = '<span class="loading loading-spinner loading-sm"></span> Memproses...';

            fetch(`/delivery/update-status/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) window.location.reload();
                    else {
                        alert(data.message);
                        btn.disabled = false;
                        btn.innerText = 'Konfirmasi';
                    }
                })
                .catch(() => {
                    alert('Kesalahan jaringan.');
                    btn.disabled = false;
                    btn.innerText = 'Konfirmasi';
                });
        });

        function previewActionImage(input) {
            const container = document.getElementById('mdl-preview-container');
            const preview = document.getElementById('mdl-preview-img');
            const placeholder = document.getElementById('mdl-placeholder');
            const btn = document.getElementById('mdl-btn-submit');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    preview.src = e.target.result;
                    container.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                    btn.disabled = false;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function removePreviewImage() {
            document.getElementById('mdl-proof-image').value = "";
            document.getElementById('mdl-preview-img').src = "";
            document.getElementById('mdl-preview-container').classList.add('hidden');
            document.getElementById('mdl-placeholder').classList.remove('hidden');
            if (document.getElementById('mdl-next-status').value === 'delivered') {
                document.getElementById('mdl-btn-submit').disabled = true;
            }
        }

        function filterStatus(status) {
            document.querySelectorAll('.filter-btn').forEach(btn => {
                if (btn.getAttribute('data-filter') === status) {
                    btn.classList.add('bg-blue-600', 'text-white');
                    btn.classList.remove('bg-white', 'text-gray-600');
                } else {
                    btn.classList.remove('bg-blue-600', 'text-white');
                    btn.classList.add('bg-white', 'text-gray-600');
                }
            });
            document.querySelectorAll('.delivery-card').forEach(card => {
                card.style.display = (status === 'all' || card.getAttribute('data-status') === status) ? 'block' : 'none';
            });
        }
    </script>
@endpush
