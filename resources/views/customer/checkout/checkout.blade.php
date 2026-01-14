@extends('layouts/frontend/index')
@section('title', 'Checkout | ShoeCycle')

@push('styles')
    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <style>
        #map-container {
            height: 100%;
            width: 100%;
            z-index: 1;
            min-height: 400px;
        }

        .modal-box {
            max-height: 90vh;
        }

        /* --- FIXED STEPPER CSS --- */
        .stepper-wrapper {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin-bottom: 20px;
            width: 100%;
        }

        .stepper-item {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
        }

        .stepper-item::after {
            content: '';
            position: absolute;
            top: 15px;
            left: 50%;
            width: 100%;
            height: 3px;
            background-color: #e5e7eb;
            z-index: 0;
            transition: all 0.3s ease;
        }

        .stepper-item:last-child::after {
            content: none;
        }

        .stepper-circle {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: white;
            border: 2px solid #d1d5db;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            font-size: 14px;
            color: #9ca3af;
            position: relative;
            z-index: 10;
            transition: all 0.3s ease;
        }

        .stepper-title {
            margin-top: 5px;
            font-size: 12px;
            color: #9ca3af;
            font-weight: 600;
            position: relative;
            z-index: 10;
        }

        .stepper-item.active .stepper-circle {
            border-color: #3b82f6;
            background-color: #3b82f6;
            color: white;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
        }

        .stepper-item.active .stepper-title {
            color: #3b82f6;
        }

        .stepper-item.completed .stepper-circle {
            border-color: #3b82f6;
            background-color: #3b82f6;
            color: white;
        }

        .stepper-item.completed::after {
            background-color: #3b82f6;
        }

        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .loading-spinner {
            color: white !important;
        }
    </style>
@endpush

@section('frontend-content')
    <section class="py-10 bg-slate-50 min-h-screen">
        <div class="container mx-auto px-4">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 font-heading mb-6">Checkout</h1>

            <form action="#" method="POST" id="checkout-form">
                @csrf
                {{-- Input Hidden Utama (Menggunakan Null Safe Operator) --}}
                <input type="hidden" name="address_id" id="input-address-id" value="{{ $address?->id }}">
                <input type="hidden" name="shipping_cost" id="input-shipping-cost">

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                    {{-- LEFT COLUMN --}}
                    <div class="lg:col-span-8 space-y-6">

                        {{-- 1. ALAMAT PENGIRIMAN --}}
                        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm" id="address-section">
                            <div class="flex justify-between items-start mb-4">
                                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                    <i class="fas fa-map-marker-alt text-blue-600"></i> Alamat Pengiriman
                                </h2>
                                @if ($userAddresses->count() > 0)
                                    <button type="button" onclick="openAddressListModal()" class="btn btn-sm btn-ghost text-blue-600 hover:bg-blue-50 normal-case font-bold">
                                        Ganti Alamat
                                    </button>
                                @endif
                            </div>

                            {{-- Area Kartu Alamat Aktif --}}
                            <div id="active-address-card" class="{{ $address ? '' : 'hidden' }}">
                                <div class="p-5 border border-blue-200 bg-blue-50/40 rounded-xl relative hover:border-blue-300 transition-colors">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span id="disp-label" class="badge badge-sm bg-blue-100 text-blue-700 border-none font-bold uppercase">
                                            @php $label = $address?->label; @endphp
                                            @if ($label == 'Home')
                                                Rumah
                                            @elseif ($label == 'Office')
                                                Kantor
                                            @elseif ($label == 'Apartment')
                                                Apartemen
                                            @elseif ($label == 'Boarding House')
                                                Tempat Kos
                                            @elseif ($label == 'Other')
                                                Lainnya
                                            @else
                                                Alamat
                                            @endif
                                        </span>
                                        <span id="disp-primary" class="badge badge-xs badge-primary {{ $address?->is_primary ? '' : 'hidden' }}">Utama</span>
                                    </div>
                                    <div class="flex flex-col md:flex-row md:items-center gap-1 md:gap-3">
                                        <p class="font-bold text-gray-900 text-lg" id="disp-recipient">{{ $address?->recipient_name }}</p>
                                        <p class="text-gray-500 hidden md:block">|</p>
                                        <p class="text-gray-600 font-medium" id="disp-phone">{{ $address?->phone_number }}</p>
                                    </div>
                                    <p class="text-gray-600 text-sm mt-2 leading-relaxed" id="disp-full-address">
                                        {{ $address?->full_address }}<br>
                                        @if ($address)
                                            Kec. {{ $address->district }}, {{ $address->village }}
                                        @endif
                                    </p>

                                    <div id="status-pinpoint-ok" class="flex items-center gap-2 mt-4 text-xs text-blue-700 font-bold bg-blue-100 w-fit px-3 py-1 rounded-full {{ $address && $address->latitude ? '' : 'hidden' }}">
                                        <i class="fas fa-map-pin"></i> Pinpoint Terpasang
                                    </div>
                                    <div id="status-pinpoint-fail" class="flex items-center gap-2 mt-4 text-xs text-red-500 font-bold bg-red-50 w-fit px-3 py-1 rounded-full border border-red-100 {{ $address && !$address->latitude ? '' : 'hidden' }}">
                                        <i class="fas fa-exclamation-triangle"></i> Pinpoint Belum Diatur
                                    </div>
                                </div>
                            </div>

                            {{-- Empty State (Muncul jika $address null) --}}
                            <div id="address-empty-state" class="text-center py-10 border-2 border-dashed border-gray-300 rounded-xl bg-gray-50 {{ $address ? 'hidden' : '' }}">
                                <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-map-marked-alt text-2xl"></i>
                                </div>
                                <p class="text-gray-600 font-medium mb-4">Kamu belum mengatur alamat pengiriman.</p>
                                <button type="button" onclick="openAddAddressModal()" class="btn btn-primary rounded-lg text-white shadow-lg shadow-blue-500/30">
                                    <i class="fas fa-plus mr-1"></i> Tambah Alamat Baru
                                </button>
                            </div>
                        </div>

                        {{-- 2. DETAIL PESANAN --}}
                        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                            <h2 class="text-lg font-bold text-gray-900 mb-4">Detail Pesanan</h2>

                            @foreach ($cartItems as $item)
                                @php
                                    $image = $item->variant->images->first();
                                    $imageUrl = $image ? asset('storage/' . $image->image_path) : asset('assets/upload/testing/dummy.jpg');
                                @endphp
                                <div class="flex gap-4 mb-6 border-b border-gray-50 pb-6 last:border-0 last:pb-0 last:mb-0">
                                    <div class="w-20 h-20 bg-gray-50 rounded-xl border border-gray-200 overflow-hidden flex-shrink-0">
                                        <img src="{{ $imageUrl }}" class="w-full h-full object-contain mix-blend-multiply p-1">
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex justify-between items-start">
                                            <h3 class="font-bold text-gray-900 text-sm md:text-base">{{ $item->variant->shoe->name }}</h3>
                                            <div class="font-bold text-gray-900 text-sm">Rp {{ number_format($item->variant->price * $item->quantity, 0, ',', '.') }}</div>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">
                                            <span class="bg-gray-100 px-2 py-0.5 rounded text-gray-600 font-medium">{{ $item->variant->color }}</span>
                                            <span class="bg-gray-100 px-2 py-0.5 rounded text-gray-600 font-medium ml-1">Size {{ $item->variant->size }}</span>
                                            <span class="ml-2 font-bold">{{ $item->quantity }}x</span>
                                        </p>

                                        <div class="mt-3 flex items-center gap-2 group">
                                            <i class="fas fa-comment-dots text-gray-400 text-xs group-focus-within:text-blue-500 transition-colors"></i>
                                            <input type="text" name="notes[{{ $item->id }}]" class="w-full text-xs border-0 border-b border-gray-100 focus:border-blue-500 focus:ring-0 placeholder:text-gray-500 py-1 transition-all" placeholder="Kasih catatan untuk barang ini (opsional)...">
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <div class="mt-6 pt-4 border-t border-dashed border-gray-200">
                                <div class="flex justify-between items-center p-3 bg-blue-50/50 rounded-lg border border-blue-100">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600"><i class="fas fa-shipping-fast"></i></div>
                                        <div>
                                            <p class="font-bold text-gray-900 text-sm">ShoeCycle Express (Lokal)</p>
                                            <p class="text-xs text-gray-500">Estimasi tiba hari ini / besok</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-bold text-blue-600 text-lg" id="shipping-cost-label">Rp -</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT COLUMN --}}
                    <div class="lg:col-span-4">
                        <div class="sticky top-24 space-y-4">
                            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-lg shadow-blue-100/50">
                                <h3 class="font-bold text-lg text-gray-900 mb-4">Ringkasan Belanja</h3>
                                <div class="space-y-3 mb-6 border-b border-gray-100 pb-4 text-sm">
                                    <div class="flex justify-between text-gray-600">
                                        <span>Total Harga ({{ $cartItems->count() }} Barang)</span>
                                        <span class="font-medium">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between text-gray-600">
                                        <span>Total Ongkos Kirim</span>
                                        <span class="font-medium" id="summary-shipping-cost">Rp -</span>
                                    </div>
                                    <div class="flex justify-between text-gray-600">
                                        <span>Biaya Admin</span>
                                        <span class="font-medium">Rp {{ number_format($adminFee, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center mb-6">
                                    <span class="font-bold text-lg text-gray-900">Total Tagihan</span>
                                    <span class="font-bold text-xl text-blue-600" id="grand-total-display">-</span>
                                </div>

                                <div class="bg-blue-50 p-4 rounded-xl flex items-center gap-4 mb-6 border border-blue-100 shadow-sm">
                                    <img src="{{ asset('assets/upload/logo/midtrans.svg') }}" class="h-12 w-auto object-contain opacity-80 mix-blend-multiply">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-blue-900 text-xs uppercase tracking-wide">Pembayaran Aman</span>
                                        <span class="text-[10px] text-blue-700">Midtrans Payment Gateway</span>
                                    </div>
                                </div>

                                <div id="payment-actions">
                                    <button type="button" id="btn-confirm" onclick="confirmOrder()" @if (!$address) disabled @endif class="btn w-full text-white font-bold rounded-xl h-12 transition-all 
        {{ !$address ? 'bg-blue-400 border-blue-400 cursor-not-allowed hover:bg-blue-400' : 'btn-primary' }}">
                                        @if (!$address)
                                            <i class="fas fa-exclamation-circle mr-2"></i> Pilih Alamat Dahulu
                                        @else
                                            Konfirmasi Pesanan
                                        @endif
                                    </button>

                                    <div id="pay-now-container" class="hidden space-y-3">
                                        <div class="bg-orange-50 border border-orange-100 p-3 rounded-xl text-center">
                                            <p class="text-[10px] text-orange-600 font-bold uppercase">Batas Waktu Pembayaran</p>
                                            <div id="countdown-timer" class="text-xl font-mono font-bold text-orange-700">23:59:59</div>
                                        </div>
                                        <div class="flex gap-2">
                                            <button type="button" onclick="cancelOrder('{{ $pendingTransaction->id ?? '' }}')" class="btn btn-ghost border-gray-200 flex-1 rounded-xl text-white bg-red-400 hover:bg-red-500 font-bold">Batal</button>
                                            <button type="button" id="btn-pay-snap" class="btn btn-success flex-[2] text-white font-bold rounded-xl shadow-lg">Bayar Sekarang</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

    {{-- MODAL 1: DAFTAR ALAMAT --}}
    <dialog id="address_list_modal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-white p-0 overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-bold text-lg text-gray-900">Pilih Alamat Pengiriman</h3>
                <form method="dialog"><button class="btn btn-sm btn-circle btn-ghost text-gray-500">✕</button></form>
            </div>
            <div class="p-4">
                <button onclick="openAddAddressModal()" class="btn btn-outline btn-primary w-full border-dashed border-2 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-600 normal-case gap-2 mb-4">
                    <i class="fas fa-plus"></i> Tambah Alamat Baru
                </button>
                <div class="space-y-3 max-h-[400px] overflow-y-auto pr-1">
                    @foreach ($userAddresses as $addr)
                        {{-- Tambahkan id unik 'addr-item-{id}' dan class 'address-item' --}}
                        <div id="addr-item-{{ $addr->id }}" class="address-item p-4 border rounded-xl cursor-pointer transition-all relative group {{ $addr->id == ($address->id ?? 0) ? 'border-blue-500 bg-blue-50 ring-1 ring-blue-500' : 'border-gray-200 hover:border-blue-300' }}" onclick="selectAddress({{ $addr->id }})">

                            <div class="flex justify-between items-start mb-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold px-2 py-0.5 rounded bg-gray-100 text-gray-600 uppercase">{{ $addr->label }}</span>
                                    @if ($addr->is_primary)
                                        <span class="text-[10px] font-bold text-blue-600 bg-blue-100 px-2 py-0.5 rounded">UTAMA</span>
                                    @endif
                                </div>
                                {{-- Tambahkan class 'check-icon' dan sembunyikan jika tidak aktif menggunakan 'hidden' --}}
                                <i class="fas fa-check-circle text-blue-600 text-lg check-icon {{ $addr->id == ($address->id ?? 0) ? '' : 'hidden' }}"></i>
                            </div>

                            <p class="font-bold text-gray-900 text-sm mt-2">{{ $addr->recipient_name }} <span class="font-normal text-gray-500">({{ $addr->phone_number }})</span></p>
                            <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                                {{ $addr->full_address }}, Kec. {{ $addr->district }}, {{ $addr->village }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop bg-gray-900/50"><button>close</button></form>
    </dialog>

    {{-- MODAL 2: TAMBAH ALAMAT WIZARD --}}
    <dialog id="add_address_modal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box w-11/12 max-w-4xl bg-white p-0 h-[650px] flex flex-col rounded-2xl overflow-hidden">
            {{-- Header --}}
            <div class="bg-white px-6 pt-6 pb-2 border-b border-gray-100 z-20 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <button onclick="prevStep()" id="btn-back-step" class="btn btn-sm btn-circle btn-ghost text-gray-500 invisible hover:bg-gray-100"><i class="fas fa-arrow-left"></i></button>
                    <h3 class="font-bold text-lg text-gray-800">Tambah Alamat Baru</h3>
                    <form method="dialog"><button class="btn btn-sm btn-circle btn-ghost text-gray-500 hover:bg-gray-100">✕</button></form>
                </div>
                <div class="stepper-wrapper px-4 md:px-12">
                    <div class="stepper-item active" id="step-indicator-1">
                        <div class="stepper-circle">1</div>
                        <div class="stepper-title">Lokasi</div>
                    </div>
                    <div class="stepper-item" id="step-indicator-2">
                        <div class="stepper-circle">2</div>
                        <div class="stepper-title">Pinpoint</div>
                    </div>
                    <div class="stepper-item" id="step-indicator-3">
                        <div class="stepper-circle">3</div>
                        <div class="stepper-title">Detail</div>
                    </div>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto relative bg-white">
                {{-- STEP 1: LOKASI --}}
                <div id="step-content-1" class="p-6 h-full flex flex-col justify-center items-center">
                    <div class="w-full max-w-md text-center">
                        <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-map-search text-2xl"></i>
                        </div>
                        <h4 class="font-bold text-xl text-gray-900 mb-4">Tentukan Lokasi Pengiriman</h4>
                        <div class="space-y-3">
                            <button onclick="useCurrentLocation()" class="btn btn-ghost w-full justify-start gap-3 text-gray-700 normal-case border border-gray-200 hover:border-blue-500 hover:bg-blue-50 rounded-xl transition-all">
                                <i class="fa-solid fa-location-crosshairs text-blue-500"></i> Gunakan Lokasi Saat Ini
                            </button>
                            <div class="divider text-xs text-gray-400">Atau</div>
                            <button onclick="goToStep(2)" class="btn btn-primary btn-outline w-full rounded-xl normal-case">Set Pinpoint Manual di Peta</button>
                        </div>
                    </div>
                </div>

                {{-- STEP 2: PINPOINT --}}
                <div id="step-content-2" class="hidden h-full relative flex flex-col">
                    <div class="absolute top-4 left-12 right-4 z-[400] px-2">
                        <div class="bg-white/90 backdrop-blur shadow-md rounded-lg p-3 flex items-center gap-3 border border-gray-200">
                            <i class="fas fa-info-circle text-blue-600 flex-shrink-0"></i>
                            <p class="text-xs text-gray-700 leading-tight">Geser peta hingga ujung pin <span class="text-red-500 font-bold">MERAH</span> tepat di lokasi tujuan.</p>
                        </div>
                    </div>
                    <div id="map-container" class="flex-1"></div>

                    <div class="absolute top-1/2 left-1/2 z-[300] pointer-events-none">
                        <div class="absolute -translate-x-1/2 -translate-y-full pb-1"><i class="fas fa-map-marker-alt text-red-500 text-4xl drop-shadow-md"></i></div>
                        <div class="absolute -translate-x-1/2 -translate-y-1/2">
                            <div class="w-4 h-1 bg-black/20 rounded-[100%] blur-[2px]"></div>
                        </div>
                    </div>

                    <div class="bg-white p-4 border-t border-gray-100 shadow-[0_-5px_20px_rgba(0,0,0,0.05)] z-[400]">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-500 font-bold uppercase tracking-wide mb-0.5"><i class="fas fa-crosshairs mr-1"></i> Koordinat</p>
                                <p class="text-xs text-gray-400 font-mono mt-0.5" id="map-coords-preview">-</p>
                            </div>
                            <button onclick="goToStep(3)" class="btn btn-primary text-white font-bold rounded-xl shadow-lg normal-case px-6 shrink-0">
                                Pilih Lokasi Ini <i class="fas fa-arrow-right ml-1"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- STEP 3: DETAIL FORM --}}
                <div id="step-content-3" class="hidden h-full flex flex-col">
                    <div class="flex-1 overflow-y-auto p-6 md:px-8">
                        <div class="flex items-center gap-3 mb-6 bg-blue-50 p-4 rounded-xl border border-blue-100">
                            <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-blue-600 shadow-sm flex-shrink-0"><i class="fas fa-check"></i></div>
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-900 text-sm">Pinpoint Berhasil!</h4>
                                <p class="text-xs text-gray-600 mt-0.5">Sekarang lengkapi detail alamat agar kurir tidak nyasar.</p>
                            </div>
                            <button type="button" onclick="goToStep(2)" class="btn btn-xs btn-ghost text-blue-600 font-bold hover:bg-blue-100">Ubah Pin</button>
                        </div>

                        <form id="add-address-form" class="space-y-5">
                            <input type="hidden" name="latitude" id="form-lat">
                            <input type="hidden" name="longitude" id="form-lng">

                            <div class="form-control w-full">
                                <label class="label pt-0 pb-2"><span class="label-text font-bold text-gray-700">Simpan Sebagai</span></label>
                                <div class="flex flex-wrap gap-2 w-full">
                                    @foreach (['Home' => 'Rumah', 'Office' => 'Kantor', 'Apartment' => 'Apartemen', 'Boarding House' => 'Kos', 'Other' => 'Lainnya'] as $val => $label)
                                        <label class="cursor-pointer border border-gray-200 rounded-lg px-4 py-2 hover:border-blue-500 has-[:checked]:bg-blue-50 has-[:checked]:border-blue-500 has-[:checked]:text-blue-700 flex items-center gap-2 flex-grow sm:flex-grow-0 justify-center transition-all">
                                            <input type="radio" name="label" value="{{ $val }}" class="radio radio-primary radio-xs" {{ $val == 'Home' ? 'checked' : '' }}>
                                            <span class="text-sm font-medium">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="form-control w-full group">
                                <label class="label pb-1"><span class="label-text font-bold text-gray-700">Alamat Lengkap <span class="text-red-500">*</span></span></label>
                                <textarea name="full_address" class="textarea textarea-bordered w-full rounded-xl focus:border-blue-500 text-base leading-relaxed" rows="4" style="resize: none" placeholder="Nama Jalan, Nomor Rumah, RT/RW, Blok..."></textarea>
                                <span class="error-text text-xs text-red-500 mt-1 hidden"></span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
                                <div class="form-control group">
                                    <label class="label pb-1"><span class="label-text font-bold text-gray-700">Kecamatan <span class="text-red-500">*</span></span></label>
                                    <input type="text" name="district" class="input input-bordered rounded-xl focus:border-blue-500" placeholder="Magersari">
                                    <span class="error-text text-xs text-red-500 mt-1 hidden"></span>
                                </div>
                                <div class="form-control group">
                                    <label class="label pb-1"><span class="label-text font-bold text-gray-700">Desa / Kelurahan <span class="text-red-500">*</span></span></label>
                                    <input type="text" name="village" class="input input-bordered rounded-xl focus:border-blue-500" placeholder="Meri">
                                    <span class="error-text text-xs text-red-500 mt-1 hidden"></span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
                                <div class="form-control group">
                                    <label class="label pb-1"><span class="label-text font-bold text-gray-700">Nama Penerima <span class="text-red-500">*</span></span></label>
                                    <input type="text" name="recipient_name" class="input input-bordered rounded-xl focus:border-blue-500" value="{{ Auth::user()->name }}">
                                    <span class="error-text text-xs text-red-500 mt-1 hidden"></span>
                                </div>
                                <div class="form-control group">
                                    <label class="label pb-1"><span class="label-text font-bold text-gray-700">Nomor HP <span class="text-red-500">*</span></span></label>
                                    <input type="text" name="phone_number" class="input input-bordered rounded-xl focus:border-blue-500" value="{{ Auth::user()->phone ?? '' }}" placeholder="08xxxxxxxx">
                                    <span class="error-text text-xs text-red-500 mt-1 hidden"></span>
                                </div>
                            </div>

                            <div class="form-control w-full">
                                <label class="label pb-1"><span class="label-text font-bold text-gray-700">Catatan Kurir (Opsional)</span></label>
                                <textarea name="courier_note" class="textarea textarea-bordered w-full resize-none rounded-xl focus:border-blue-500" placeholder="Warna pagar, patokan, titip satpam, dll."></textarea>
                            </div>

                            <div class="form-control bg-gray-50 p-3 rounded-xl border border-gray-100">
                                <label class="label cursor-pointer justify-start gap-3 p-0">
                                    <input type="checkbox" name="is_primary" class="checkbox checkbox-primary checkbox-sm rounded" checked>
                                    <span class="label-text text-gray-700 font-medium">Jadikan Alamat Utama</span>
                                </label>
                            </div>
                        </form>
                    </div>

                    <div class="p-4 border-t border-gray-100 bg-white z-10 w-full">
                        <button type="button" id="btn-save-address" onclick="submitNewAddress()" class="btn btn-primary w-full text-white font-bold rounded-xl shadow-lg text-lg h-12 disabled:bg-blue-400 disabled:border-blue-400 disabled:text-white disabled:opacity-50 transition-all" disabled>Simpan & Gunakan</button>
                    </div>
                </div>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop bg-gray-900/50"><button>close</button></form>
    </dialog>

    {{-- MODAL 3: KONFIRMASI BATAL --}}
    <dialog id="modal_cancel_confirmation" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-white p-0 overflow-hidden max-w-md">
            <div class="p-6 text-center">
                <div class="w-20 h-20 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-exclamation-triangle text-3xl"></i>
                </div>
                <h3 class="font-bold text-xl text-gray-900">Batalkan Pesanan?</h3>
                <p class="text-gray-500 mt-2 leading-relaxed">
                    Apakah Anda yakin ingin membatalkan checkout ini? Barang akan dikembalikan ke keranjang Anda.
                </p>
            </div>
            <div class="flex border-t border-gray-100 bg-gray-50 p-4 gap-3">
                <form method="dialog" class="flex-1">
                    <button class="btn btn-ghost w-full rounded-xl font-bold text-gray-400">Tutup</button>
                </form>
                {{-- Button ini akan memicu AJAX --}}
                <button id="btn-execute-cancel" class="btn flex-1 text-white bg-red-400 hover:bg-red-500 font-bold rounded-xl shadow-lg">
                    Ya, Batalkan
                </button>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop bg-gray-900/50"><button>close</button></form>
    </dialog>
@endsection

@push('scripts')
    {{-- Leaflet JS --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    {{-- Midtrans Snap JS --}}
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>

    <script>
        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        // 1. DATA GLOBAL
        const userAddresses = @json($userAddresses);
        const STORE_LAT = {{ $storeConfig['lat'] }};
        const STORE_LNG = {{ $storeConfig['lng'] }};
        const BASE_COST = {{ $storeConfig['base_shipping_cost'] }};
        const COST_KM = {{ $storeConfig['cost_per_km'] }};
        const SUBTOTAL = {{ $subtotal }};
        const ADMIN_FEE = {{ $adminFee }};

        let map = null,
            marker = null,
            selectedLat = STORE_LAT,
            selectedLng = STORE_LNG,
            currentStep = 1;

        // 2. FUNGSI GLOBAL (UI & Navigasi)
        window.openAddressListModal = function() {
            $('#address_list_modal')[0].showModal();
        };

        window.openAddAddressModal = function() {
            if ($('#address_list_modal').length) $('#address_list_modal')[0].close();
            $('#add_address_modal')[0].showModal();
            window.goToStep(1);
        };

        window.goToStep = function(step) {
            currentStep = step;
            $('[id^="step-content-"]').addClass('hidden');
            $('#step-content-' + step).removeClass('hidden');

            for (let i = 1; i <= 3; i++) {
                let $el = $('#step-indicator-' + i);
                $el.removeClass('active completed');
                if (i < step) $el.addClass('completed');
                else if (i === step) $el.addClass('active');
            }

            if (step === 2) {
                setTimeout(() => {
                    window.initMap();
                    if (map) {
                        map.invalidateSize();
                    }
                }, 400);
            }
            if (step > 1) $('#btn-back-step').removeClass('invisible');
            else $('#btn-back-step').addClass('invisible');
        };

        window.prevStep = function() {
            if (currentStep > 1) window.goToStep(currentStep - 1);
        };

        window.initMap = function() {
            if (map) {
                map.invalidateSize();
                return;
            }
            map = L.map('map-container').setView([selectedLat, selectedLng], 14);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
            map.on('move', function() {
                let center = map.getCenter();
                selectedLat = center.lat;
                selectedLng = center.lng;
                $('#map-coords-preview').text(selectedLat.toFixed(6) + ', ' + selectedLng.toFixed(6));
            });
        };

        window.useCurrentLocation = function() {
            if (!navigator.geolocation) return alert('Browser tidak mendukung lokasi.');
            let $btn = $(event.currentTarget),
                originalText = $btn.html();
            $btn.html('<span class="loading loading-spinner loading-xs"></span> Mencari...').prop('disabled', true);
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    selectedLat = pos.coords.latitude;
                    selectedLng = pos.coords.longitude;
                    $btn.html(originalText).prop('disabled', false);
                    window.goToStep(2);
                    setTimeout(() => {
                        if (map) map.setView([selectedLat, selectedLng], 16);
                    }, 500);
                },
                (err) => {
                    alert('Gagal: ' + err.message);
                    $btn.html(originalText).prop('disabled', false);
                }
            );
        };

        // 3. LOGIKA HARGA & ONGKIR
        window.calculateShipping = function(lat, lng) {
            if (!lat || !lng) return window.resetShippingUI();

            const R = 6371;
            const dLat = (lat - STORE_LAT) * Math.PI / 180;
            const dLon = (lng - STORE_LNG) * Math.PI / 180;
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(STORE_LAT * Math.PI / 180) * Math.cos(lat * Math.PI / 180) * Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            const distanceKm = R * c;

            let cost = BASE_COST;
            if (distanceKm > 1) cost += Math.ceil(distanceKm) * COST_KM;
            cost = Math.ceil(cost / 500) * 500;

            // Gunakan jQuery .text() yang lebih aman daripada innerText manual
            $('#shipping-cost-label').text(window.formatRupiah(cost));
            $('#summary-shipping-cost').text(window.formatRupiah(cost));
            $('#grand-total-display').text(window.formatRupiah(SUBTOTAL + ADMIN_FEE + cost));

            $('#input-shipping-cost').val(cost);
            $('#btn-pay').prop('disabled', false).removeClass('bg-blue-400').addClass('btn-primary');
        };

        // Fungsi Reset UI jika Alamat tidak valid (PENTING untuk mencegah error null)
        window.resetShippingUI = function() {
            $('#shipping-cost-label').text('Rp -');
            $('#summary-shipping-cost').text('Rp -');
            $('#grand-total-display').text('-');
            $('#btn-pay').prop('disabled', true);
        };

        window.formatRupiah = function(num) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(num);
        };

        // 4. LOGIKA ALAMAT
        window.selectAddress = function(id) {
            const addr = userAddresses.find(a => a.id == id);
            if (!addr) return;

            $('.address-item').removeClass('border-blue-500 bg-blue-50 ring-1 ring-blue-500').addClass('border-gray-200');
            $('.address-item .check-icon').addClass('hidden');

            const $selectedItem = $(`#addr-item-${id}`);
            $selectedItem.addClass('border-blue-500 bg-blue-50 ring-1 ring-blue-500').removeClass('border-gray-200');
            $selectedItem.find('.check-icon').removeClass('hidden');

            $('#disp-label').text(addr.label);
            $('#disp-recipient').text(addr.recipient_name);
            $('#disp-phone').text(addr.phone_number);
            $('#disp-full-address').html(`${addr.full_address}<br>Kec. ${addr.district}, ${addr.village}`);

            if (addr.is_primary) $('#disp-primary').removeClass('hidden');
            else $('#disp-primary').addClass('hidden');

            $('#input-address-id').val(addr.id);

            if (addr.latitude && addr.longitude) {
                $('#status-pinpoint-ok').removeClass('hidden');
                $('#status-pinpoint-fail').addClass('hidden');
                window.calculateShipping(addr.latitude, addr.longitude);
            } else {
                $('#status-pinpoint-ok').addClass('hidden');
                $('#status-pinpoint-fail').removeClass('hidden');
                window.resetShippingUI();
                alert('Alamat ini belum memiliki titik koordinat.');
            }

            $('#active-address-card').removeClass('hidden');
            $('#address-empty-state').addClass('hidden');
            $('#address_list_modal')[0].close();
        };

        // 5. KONFIRMASI & PEMBAYARAN (SNAP POP-UP)
        window.confirmOrder = function() {
            let $btn = $('#btn-confirm');
            let address_id = $('#input-address-id').val();

            if (!address_id || address_id === "") {
                alert('Silakan pilih alamat pengiriman terlebih dahulu.');
                return;
            }

            // Ambil semua notes per item cart
            let notes = {};
            $('input[name^="notes"]').each(function() {
                let match = $(this).attr('name').match(/\d+/);
                if (match) notes[match[0]] = $(this).val();
            });

            let shipping = parseInt($('#input-shipping-cost').val()) || 0;

            // Loading State
            $btn.prop('disabled', true).html('<span class="loading loading-spinner loading-xs"></span> Memproses...');

            $.ajax({
                url: "{{ route('checkout.store') }}",
                type: "POST",
                data: {
                    address_id: address_id,
                    subtotal: SUBTOTAL,
                    shipping_cost: shipping,
                    admin_fee: ADMIN_FEE,
                    total_price: SUBTOTAL + ADMIN_FEE + shipping,
                    notes: notes
                },
                success: function(res) {
                    // 1. Ganti UI: Sembunyikan konfirmasi, tampilkan area pembayaran & timer
                    $btn.addClass('hidden');
                    if ($('#pay-now-container').length) {
                        $('#pay-now-container').removeClass('hidden').fadeIn();
                    }

                    // 2. Jalankan Timer Berdasarkan Waktu ISO dari Server (Anti-Loncat)
                    window.startTimer(res.expiry);

                    // 3. LOGIKA POP-UP SNAP
                    // Fungsi untuk memicu pop-up Midtrans
                    const triggerSnap = function() {
                        window.snap.pay(res.snap_token, {
                            onPending: function(result) {
                                /* Terpanggil saat user mendapatkan instruksi bayar (misal: Virtual Account) */
                                window.location.href = "/orders/status?order_id=" + result.order_id;
                            },
                            onError: function(result) {
                                /* Terpanggil saat terjadi kesalahan teknis */
                                alert("Pembayaran gagal! Silakan coba lagi.");
                                console.error(result);
                            },
                            onClose: function() {
                                Swal.fire({
                                    title: 'Pembayaran Belum Selesai',
                                    text: 'Jangan lupa selesaikan pembayaran Anda sebelum batas waktu berakhir agar pesanan tidak dibatalkan otomatis.',
                                    icon: 'warning',
                                    confirmButtonColor: '#3b82f6',
                                    confirmButtonText: 'Oke, Mengerti'
                                });
                            }
                        });
                    };

                    // Picu pop-up pertama kali secara otomatis (Opsional)
                    triggerSnap();

                    // Pasang logika yang sama ke tombol "Bayar Sekarang" jika user menutup pop-up secara tidak sengaja
                    $('#btn-pay-snap').off('click').on('click', function() {
                        triggerSnap();
                    });
                },
                error: function(xhr) {
                    $btn.prop('disabled', false).text('Konfirmasi Pesanan');
                    let errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan sistem.';
                    alert('Gagal membuat pesanan: ' + errorMsg);
                }
            });
        };

        window.startTimer = function(expiryTime) {
            if (!expiryTime) return;

            // Menghitung target waktu berdasarkan string ISO dari server
            let countDownDate = new Date(expiryTime).getTime();

            // Hapus interval lama jika ada (mencegah timer ganda)
            if (window.timerInterval) clearInterval(window.timerInterval);

            window.timerInterval = setInterval(function() {
                // Ambil waktu sekarang (berdasarkan timezone browser user)
                let now = new Date().getTime();

                // Jarak antara waktu sekarang dan waktu kadaluarsa
                let distance = countDownDate - now;

                // Logika kalkulasi jam, menit, detik
                let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                let seconds = Math.floor((distance % (1000 * 60)) / 1000);

                let timerText = (hours < 10 ? "0" + hours : hours) + ":" +
                    (minutes < 10 ? "0" + minutes : minutes) + ":" +
                    (seconds < 10 ? "0" + seconds : seconds);

                let timerEl = $('#countdown-timer');
                if (timerEl.length) {
                    timerEl.text(timerText);
                }

                // Jika waktu habis
                if (distance < 0) {
                    clearInterval(window.timerInterval);
                    if (timerEl.length) timerEl.text("EXPIRED");
                    alert('Waktu pembayaran telah habis.');
                    window.location.reload();
                }
            }, 1000);
        };

        window.cancelOrder = function(id) {
            if (!id || id === "") {
                window.location.href = "{{ route('cart.index') }}";
                return;
            }

            const cancelModal = document.getElementById('modal_cancel_confirmation');
            cancelModal.showModal();

            $('#btn-execute-cancel').off('click').on('click', function() {
                let $btn = $(this);
                const originalHtml = $btn.html();

                $btn.prop('disabled', true).html('<span class="loading loading-spinner loading-xs"></span>');

                $.ajax({
                    url: `/checkout/cancel/${id}`,
                    type: "POST",
                    data: {
                        _token: csrfToken, // Pastikan variabel ini sudah dideklarasikan di paling atas script
                        _method: "PUT"
                    },
                    success: function(res) {
                        // TUTUP MODAL DAISYUI DULU sebelum buka SweetAlert
                        cancelModal.close();

                        if (res.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil Batal',
                                text: res.message,
                                confirmButtonColor: '#3b82f6',
                            }).then(() => {
                                window.location.href = "{{ route('cart.index') }}";
                            });
                        }
                    },
                    error: function(xhr) {
                        // TUTUP MODAL DAISYUI agar backdrop hilang
                        cancelModal.close();

                        $btn.prop('disabled', false).html(originalHtml);

                        let errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan sistem (500).';

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Membatalkan',
                            text: errorMsg, // Akan memunculkan pesan error PHP jika 500
                            confirmButtonColor: '#ef4444',
                        });
                    }
                });
            });
        };

        window.submitNewAddress = function() {
            $('#form-lat').val(selectedLat);
            $('#form-lng').val(selectedLng);
            let formData = new FormData($('#add-address-form')[0]);
            let $btn = $('#btn-save-address');
            $btn.prop('disabled', true).html('<span class="loading loading-spinner loading-sm"></span>');

            $.ajax({
                url: "{{ route('address.store') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function() {
                    window.location.reload();
                },
                error: function() {
                    alert('Gagal menyimpan.');
                    $btn.prop('disabled', false).text('Simpan Alamat');
                }
            });
        };

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // --- LOGIKA RECOVERY SAAT REFRESH ---
            @if (isset($pendingTransaction) && $pendingTransaction->payment_status == 'pending')
                // 1. Sembunyikan tombol konfirmasi, tampilkan area bayar
                $('#btn-confirm').addClass('hidden');
                $('#pay-now-container').removeClass('hidden').show();

                // 2. Hitung sisa waktu (Created_at + 24 Jam)
                @php
                    $expiryTime = $pendingTransaction->created_at->addHours(24)->toIso8601String();
                @endphp

                // 3. Jalankan timer otomatis
                window.startTimer("{{ $expiryTime }}");

                // 4. Pasang fungsi Snap ke tombol bayar menggunakan token lama
                $('#btn-pay-snap').off('click').on('click', function() {
                    window.snap.pay("{{ $pendingTransaction->snap_token }}", {
                        onSuccess: function(result) {
                            window.location.href = "/orders/status";
                        },
                        onPending: function(result) {
                            window.location.reload();
                        },
                        onClose: function() {
                            Swal.fire({
                                title: 'Lanjutkan Pembayaran?',
                                text: 'Pesanan Anda masih berstatus pending. Segera selesaikan pembayaran ya!',
                                icon: 'info',
                                confirmButtonColor: '#3b82f6'
                            });
                        }
                    });
                });
            @endif

            const rules = {
                recipient_name: {
                    required: true,
                    min: 3
                },
                phone_number: {
                    required: true,
                    regex: /^[0-9]{10,15}$/
                },
                full_address: {
                    required: true,
                    min: 10
                },
                district: {
                    required: true
                },
                village: {
                    required: true
                }
            };

            function validate() {
                let valid = true;
                $('#add-address-form input, #add-address-form textarea').each(function() {
                    let name = $(this).attr('name'),
                        rule = rules[name],
                        val = $(this).val().trim();
                    if (rule) {
                        let ok = true;
                        if (rule.required && !val) ok = false;
                        if (rule.min && val.length < rule.min) ok = false;
                        if (rule.regex && !rule.regex.test(val)) ok = false;
                        if (!ok) valid = false;
                        if (!ok && val !== "") $(this).addClass('input-error');
                        else $(this).removeClass('input-error');
                    }
                });
                $('#btn-save-address').prop('disabled', !valid);
            }

            $('#add-address-form input, #add-address-form textarea').on('input blur', validate);

            @if ($address && $address->latitude)
                window.calculateShipping({{ $address->latitude }}, {{ $address->longitude }});
            @endif
        });
    </script>
@endpush
