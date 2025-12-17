@extends('layouts/frontend/index')
@section('title', 'Checkout | ShoeCycle')

@push('styles')
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

        /* GARIS PENGHUBUNG (LINE) */
        .stepper-item::after {
            content: '';
            position: absolute;
            top: 15px;
            /* Tepat di tengah vertikal circle (30px / 2) */
            left: 50%;
            /* Mulai dari tengah item ini */
            width: 100%;
            /* Menjangkau sampai tengah item berikutnya */
            height: 3px;
            background-color: #e5e7eb;
            /* Gray-200 (Default) */
            z-index: 0;
            /* Di belakang lingkaran */
            transition: all 0.3s ease;
        }

        /* Hilangkan garis pada item terakhir */
        .stepper-item:last-child::after {
            content: none;
        }

        /* LINGKARAN ANGKA (CIRCLE) */
        .stepper-circle {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: white;
            /* WAJIB ADA: Untuk menutupi garis di belakangnya */
            border: 2px solid #d1d5db;
            /* Gray-300 */
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            font-size: 14px;
            color: #9ca3af;
            /* Gray-400 */
            position: relative;
            z-index: 10;
            /* Di depan garis */
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

        /* --- STATE COLORS --- */

        /* 1. Item Aktif (Sedang dipilih) */
        .stepper-item.active .stepper-circle {
            border-color: #3b82f6;
            /* Blue-500 */
            background-color: #3b82f6;
            color: white;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
            /* Efek glow biru muda */
        }

        .stepper-item.active .stepper-title {
            color: #3b82f6;
        }

        /* 2. Item Completed (Sudah lewat) */
        .stepper-item.completed .stepper-circle {
            border-color: #3b82f6;
            /* Blue-500 */
            background-color: #3b82f6;
            color: white;
        }

        .stepper-item.completed .stepper-title {
            color: #3b82f6;
        }

        /* 3. Garis Completed (Garis setelah item yang selesai jadi biru) */
        .stepper-item.completed::after {
            background-color: #3b82f6;
            /* Blue-500 */
        }

        /* Fix: Jika item aktif tapi belum completed, garis ke depannya tetap abu-abu */
        .stepper-item.active::after {
            background-color: #e5e7eb;
        }

        /* Kecuali jika item aktif ITU JUGA item yang completed (kasus step 1 ke 2) - Logic CSS Override */
        .stepper-item.completed.active::after {
            background-color: #3b82f6;
        }
    </style>
@endpush

@section('frontend-content')
    <section class="py-10 bg-slate-50 min-h-screen">
        <div class="container mx-auto px-4">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 font-heading mb-6">Checkout</h1>

            <form action="#" method="POST" id="checkout-form">
                @csrf
                <input type="hidden" name="address_id" id="input-address-id" value="{{ $address->id ?? '' }}">
                <input type="hidden" name="shipping_cost" id="input-shipping-cost">

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    {{-- LEFT COLUMN --}}
                    <div class="lg:col-span-8 space-y-6">

                        {{-- 1. ALAMAT PENGIRIMAN --}}
                        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                            <div class="flex justify-between items-start mb-4">
                                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                    <i class="fas fa-map-marker-alt text-blue-600"></i> Alamat Pengiriman
                                </h2>
                                @if ($address)
                                    <button type="button" onclick="openAddressListModal()" class="btn btn-sm btn-ghost text-blue-600 hover:bg-blue-50 normal-case font-bold">
                                        Ganti Alamat
                                    </button>
                                @endif
                            </div>

                            @if ($address)
                                {{-- Card Alamat Terpilih (Blue Theme) --}}
                                <div class="p-5 border border-blue-200 bg-blue-50/40 rounded-xl relative hover:border-blue-300 transition-colors">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="badge badge-sm bg-blue-100 text-blue-700 border-none font-bold">{{ $address->label }}</span>
                                        @if ($address->is_primary)
                                            <span class="badge badge-xs badge-primary">Utama</span>
                                        @endif
                                    </div>
                                    <div class="flex flex-col md:flex-row md:items-center gap-1 md:gap-3">
                                        <p class="font-bold text-gray-900 text-lg">{{ $address->recipient_name }}</p>
                                        <p class="text-gray-500 hidden md:block">|</p>
                                        <p class="text-gray-600 font-medium">{{ $address->phone_number }}</p>
                                    </div>
                                    <p class="text-gray-600 text-sm mt-2 leading-relaxed">
                                        {{ $address->full_address }}<br>
                                        Kec. {{ $address->district }}, {{ $address->village }}
                                    </p>
                                    @if ($address->courier_note)
                                        <p class="text-xs text-orange-600 mt-1 italic flex items-center gap-1">
                                            <i class="fas fa-sticky-note"></i> "{{ $address->courier_note }}"
                                        </p>
                                    @endif

                                    @if ($address->latitude)
                                        <div class="flex items-center gap-2 mt-4 text-xs text-blue-700 font-bold bg-blue-100 w-fit px-3 py-1 rounded-full">
                                            <i class="fas fa-map-pin"></i> Pinpoint Terpasang
                                        </div>
                                    @else
                                        <div class="flex items-center gap-2 mt-4 text-xs text-red-500 font-bold bg-red-50 w-fit px-3 py-1 rounded-full border border-red-100">
                                            <i class="fas fa-exclamation-triangle"></i> Pinpoint Belum Diatur
                                        </div>
                                    @endif
                                </div>
                            @else
                                {{-- Empty State --}}
                                <div class="text-center py-10 border-2 border-dashed border-gray-300 rounded-xl bg-gray-50">
                                    <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-map-marked-alt text-2xl"></i>
                                    </div>
                                    <p class="text-gray-600 font-medium mb-4">Kamu belum mengatur alamat pengiriman.</p>
                                    <button type="button" onclick="openAddAddressModal()" class="btn btn-primary rounded-lg text-white shadow-lg shadow-blue-500/30">
                                        <i class="fas fa-plus mr-1"></i> Tambah Alamat Baru
                                    </button>
                                </div>
                            @endif
                        </div>

                        {{-- 2. DETAIL PESANAN --}}
                        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                            <h2 class="text-lg font-bold text-gray-900 mb-4">Detail Pesanan</h2>

                            @foreach ($cartItems as $item)
                                @php
                                    $image = $item->variant->images->first();
                                    $imageUrl = $image ? asset('storage/' . $image->image_path) : asset('assets/upload/testing/dummy.jpg');
                                @endphp
                                <div class="flex gap-4 mb-6 border-b border-gray-50 pb-4 last:border-0 last:pb-0 last:mb-0">
                                    <div class="w-20 h-20 bg-gray-50 rounded-xl border border-gray-200 overflow-hidden flex-shrink-0">
                                        <img src="{{ $imageUrl }}" class="w-full h-full object-contain mix-blend-multiply p-1">
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-bold text-gray-900 text-sm md:text-base">{{ $item->variant->shoe->name }}</h3>
                                        <p class="text-xs text-gray-500 mt-1">
                                            <span class="bg-gray-100 px-2 py-0.5 rounded text-gray-600 font-medium">{{ $item->variant->color }}</span>
                                            <span class="bg-gray-100 px-2 py-0.5 rounded text-gray-600 font-medium ml-1">Size {{ $item->variant->size }}</span>
                                        </p>
                                        <div class="flex justify-between items-end mt-2">
                                            <div class="text-sm text-gray-500">{{ $item->quantity }} x Rp {{ number_format($item->variant->price, 0, ',', '.') }}</div>
                                            <div class="font-bold text-blue-600">Rp {{ number_format($item->variant->price * $item->quantity, 0, ',', '.') }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            {{-- Pilihan Pengiriman --}}
                            <div class="mt-6 pt-4 border-t border-dashed border-gray-200">
                                <div class="flex justify-between items-center p-3 bg-blue-50/50 rounded-lg border border-blue-100">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                            <i class="fas fa-shipping-fast"></i>
                                        </div>
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
                            {{-- Summary --}}
                            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-lg shadow-blue-100/50">
                                <h3 class="font-bold text-lg text-gray-900 mb-4">Ringkasan Belanja</h3>
                                <div class="space-y-3 mb-6 border-b border-gray-100 pb-4 text-sm">
                                    <div class="flex justify-between text-gray-600">
                                        <span>Total Harga ({{ $cartItems->count() }} Sepatu)</span>
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

                                {{-- Midtrans Logo (Updated Big) --}}
                                <div class="bg-blue-50 p-4 rounded-xl flex items-center gap-4 mb-6 border border-blue-100 shadow-sm">
                                    <img src="{{ asset('assets/upload/logo/midtrans.svg') }}" class="h-12 w-auto object-contain opacity-80 mix-blend-multiply">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-blue-900 text-xs uppercase tracking-wide">Secure Payment</span>
                                        <span class="text-[10px] text-blue-700">Powered by Midtrans Gateway</span>
                                    </div>
                                </div>

                                <button type="button" id="btn-pay" class="btn btn-primary w-full text-white rounded-xl font-bold shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 transition-all hover:-translate-y-1" disabled>
                                    Bayar Sekarang
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

    {{-- MODAL 1: DAFTAR ALAMAT --}}
    <dialog id="address_list_modal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-white">
            <div class="flex justify-between items-center mb-4 pb-2 border-b border-gray-100">
                <h3 class="font-bold text-lg text-gray-900">Pilih Alamat Pengiriman</h3>
                <form method="dialog"><button class="btn btn-sm btn-circle btn-ghost text-gray-500">✕</button></form>
            </div>

            <div class="mb-4">
                <button onclick="openAddAddressModal()" class="btn btn-outline btn-primary w-full border-dashed border-2 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-600 normal-case gap-2">
                    <i class="fas fa-plus"></i> Tambah Alamat Baru
                </button>
            </div>

            <div class="space-y-3 max-h-[400px] overflow-y-auto pr-1">
                @foreach ($userAddresses as $addr)
                    <div class="p-4 border rounded-xl cursor-pointer transition-all relative group {{ $addr->id == ($address->id ?? 0) ? 'border-blue-500 bg-blue-50 ring-1 ring-blue-500' : 'border-gray-200 hover:border-blue-300' }}" onclick="selectAddress({{ $addr->id }})">

                        <div class="flex justify-between items-start mb-1">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold px-2 py-0.5 rounded bg-gray-100 text-gray-600">{{ $addr->label }}</span>
                                @if ($addr->is_primary)
                                    <span class="text-[10px] font-bold text-blue-600 bg-blue-100 px-2 py-0.5 rounded">UTAMA</span>
                                @endif
                            </div>
                            @if ($addr->id == ($address->id ?? 0))
                                <i class="fas fa-check-circle text-blue-600 text-lg"></i>
                            @endif
                        </div>

                        <p class="font-bold text-gray-900 text-sm mt-2">{{ $addr->recipient_name }} <span class="font-normal text-gray-500">({{ $addr->phone_number }})</span></p>
                        <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                            {{ $addr->full_address }}, {{ $addr->district }}, {{ $addr->village }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>

    {{-- MODAL 2: WIZARD TAMBAH ALAMAT (BLUE THEME) --}}
    <dialog id="add_address_modal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box w-11/12 max-w-4xl bg-white p-0 h-[650px] flex flex-col rounded-2xl overflow-hidden">

            {{-- Header & Stepper --}}
            <div class="bg-white px-6 pt-6 pb-2 border-b border-gray-100 z-20 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <button onclick="prevStep()" id="btn-back-step" class="btn btn-sm btn-circle btn-ghost text-gray-500 invisible hover:bg-gray-100"><i class="fas fa-arrow-left"></i></button>
                    <h3 class="font-bold text-lg text-gray-800">Tambah Alamat Baru</h3>
                    <form method="dialog"><button class="btn btn-sm btn-circle btn-ghost text-gray-500 hover:bg-gray-100">✕</button></form>
                </div>

                {{-- NEW BLUE STEPPER --}}
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

            {{-- Content Area --}}
            <div class="flex-1 overflow-y-auto relative bg-white">

                {{-- STEP 1: SEARCH LOCATION --}}
                <div id="step-content-1" class="p-6 h-full flex flex-col justify-center items-center">
                    <div class="w-full max-w-md text-center">
                        <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-map-search text-2xl"></i>
                        </div>
                        <h4 class="font-bold text-xl text-gray-900 mb-4">Tentukan Lokasi Pengiriman</h4>

                        <div class="space-y-3">
                            <button onclick="useCurrentLocation()" class="btn btn-ghost w-full justify-start gap-3 text-gray-700 normal-case border border-gray-200 hover:border-blue-500 hover:bg-blue-50 hover:text-blue-600 rounded-xl">
                                <i class="fa-solid fa-location-crosshairs text-blue-500 text-[1rem]"></i> Gunakan Lokasi Saat Ini
                            </button>
                            <div class="divider text-xs text-gray-400">Atau</div>
                            <button onclick="goToStep(2)" class="btn btn-primary btn-outline w-full rounded-xl normal-case">
                                Set Pinpoint Manual di Peta
                            </button>
                        </div>
                    </div>
                </div>

                {{-- STEP 2: PINPOINT MAP --}}
                <div id="step-content-2" class="hidden h-full relative flex flex-col">
                    <div class="absolute top-4 left-12 right-4 z-[400] px-2">
                        <div class="bg-white/90 backdrop-blur shadow-md rounded-lg p-3 flex items-center gap-3 border border-gray-200">
                            <i class="fas fa-info-circle text-blue-600 flex-shrink-0"></i>
                            <p class="text-xs text-gray-700 leading-tight">
                                Geser peta hingga ujung bawah pin <span class="text-red-500 font-bold"><i class="fas fa-map-marker-alt"></i> MERAH</span> berada tepat di lokasi tujuan.
                            </p>
                        </div>
                    </div>

                    <div id="map-container" class="flex-1"></div>

                    <div class="absolute top-1/2 left-1/2 z-[300] pointer-events-none">

                        {{-- Ikon Pin (Digeser ke atas 100% tingginya) --}}
                        <div class="absolute -translate-x-1/2 -translate-y-full pb-1">
                            {{-- pb-1 memberikan sedikit jarak agar ujung tajam tidak tertutup shadow --}}
                            <i class="fas fa-map-marker-alt text-red-500 text-4xl drop-shadow-md"></i>
                        </div>

                        {{-- Bayangan Pin (Tetap di titik tengah / ground) --}}
                        <div class="absolute -translate-x-1/2 -translate-y-1/2">
                            <div class="w-4 h-1 bg-black/20 rounded-[100%] blur-[2px]"></div>
                        </div>

                    </div>

                    {{-- Bottom Sheet (Tetap sama, tapi pastikan layout flex nya benar) --}}
                    <div class="bg-white p-4 border-t border-gray-100 shadow-[0_-5px_20px_rgba(0,0,0,0.05)] z-[400]">
                        <div class="flex items-center justify-between gap-4">

                            {{-- BAGIAN KIRI: Info Koordinat --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-500 font-bold uppercase tracking-wide mb-0.5">
                                    <i class="fas fa-crosshairs mr-1"></i> Koordinat Terpilih
                                </p>
                                <p class="text-xs text-gray-400 font-mono mt-0.5" id="map-coords-preview">
                                    Geser pin di peta
                                </p>
                            </div>

                            {{-- BAGIAN KANAN: Tombol --}}
                            <button onclick="goToStep(3)" class="btn btn-primary text-white font-bold rounded-xl shadow-lg shadow-blue-500/30 normal-case px-6 shrink-0">
                                Pilih Lokasi Ini <i class="fas fa-arrow-right ml-1"></i>
                            </button>

                        </div>
                    </div>
                </div>

                {{-- STEP 3: FORM DETAILS --}}
                <div id="step-content-3" class="hidden h-full flex flex-col">

                    {{-- Scrollable Content --}}
                    <div class="flex-1 overflow-y-auto p-6 md:px-8">

                        {{-- Success Badge --}}
                        <div class="flex items-center gap-3 mb-6 bg-blue-50 p-4 rounded-xl border border-blue-100">
                            <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-blue-600 shadow-sm flex-shrink-0">
                                <i class="fas fa-check"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-900 text-sm">Pinpoint Berhasil!</h4>
                                <p class="text-xs text-gray-600 mt-0.5">Silakan lengkapi detail alamat di bawah ini.</p>
                            </div>
                            <button type="button" onclick="goToStep(2)" class="btn btn-xs btn-ghost text-blue-600 font-bold hover:bg-blue-100">
                                Ubah Pinpoint
                            </button>
                        </div>

                        <form id="add-address-form" class="space-y-5">
                            <input type="hidden" name="latitude" id="form-lat">
                            <input type="hidden" name="longitude" id="form-lng">

                            {{-- Label Alamat --}}
                            <div class="form-control w-full">
                                <label class="label pt-0 pb-2"><span class="label-text font-bold text-gray-700">Simpan Sebagai</span></label>
                                <div class="flex flex-wrap gap-2 w-full">
                                    @foreach (['Home' => 'Rumah', 'Office' => 'Kantor', 'Apartment' => 'Apartemen', 'Boarding House' => 'Kos', 'Other' => 'Lainnya'] as $val => $label)
                                        <label class="cursor-pointer border border-gray-200 rounded-lg px-4 py-2 hover:border-blue-500 has-[:checked]:bg-blue-50 has-[:checked]:border-blue-500 has-[:checked]:text-blue-700 transition-all flex items-center gap-2 flex-grow sm:flex-grow-0 justify-center">
                                            <input type="radio" name="label" value="{{ $val }}" class="radio radio-primary radio-xs" {{ $val == 'Home' ? 'checked' : '' }}>
                                            <span class="text-sm font-medium">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Alamat Lengkap --}}
                            <div class="form-control w-full group">
                                <label class="label pb-1"><span class="label-text font-bold text-gray-700">Alamat Lengkap <span class="text-red-500">*</span></span></label>
                                <textarea name="full_address" class="textarea textarea-bordered w-full rounded-xl focus:border-blue-500 text-base leading-relaxed" rows="3" placeholder="Nama Jalan, Nomor Rumah, RT/RW, Blok..."></textarea>
                                {{-- Error Message Container --}}
                                <span class="error-text text-xs text-red-500 mt-1 hidden">Alamat wajib diisi minimal 10 karakter.</span>
                            </div>

                            {{-- Penerima & Kontak --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
                                <div class="form-control w-full group">
                                    <label class="label pb-1"><span class="label-text font-bold text-gray-700">Nama Penerima <span class="text-red-500">*</span></span></label>
                                    <input type="text" name="recipient_name" class="input input-bordered w-full rounded-xl focus:border-blue-500" value="{{ Auth::user()->name }}">
                                    <span class="error-text text-xs text-red-500 mt-1 hidden">Nama penerima wajib diisi.</span>
                                </div>
                                <div class="form-control w-full group">
                                    <label class="label pb-1"><span class="label-text font-bold text-gray-700">Nomor HP <span class="text-red-500">*</span></span></label>
                                    <input type="text" name="phone_number" class="input input-bordered w-full rounded-xl focus:border-blue-500" value="{{ Auth::user()->phone ?? '' }}" placeholder="08xxxxxxxxxx">
                                    <span class="error-text text-xs text-red-500 mt-1 hidden">Format nomor HP tidak valid (10-15 angka).</span>
                                </div>
                            </div>

                            {{-- Wilayah --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
                                <div class="form-control w-full group">
                                    <label class="label pb-1"><span class="label-text font-bold text-gray-700">Kecamatan <span class="text-red-500">*</span></span></label>
                                    <input type="text" name="district" class="input input-bordered w-full rounded-xl focus:border-blue-500" placeholder="Contoh: Magersari">
                                    <span class="error-text text-xs text-red-500 mt-1 hidden">Kecamatan wajib diisi.</span>
                                </div>
                                <div class="form-control w-full group">
                                    <label class="label pb-1"><span class="label-text font-bold text-gray-700">Desa / Kelurahan <span class="text-red-500">*</span></span></label>
                                    <input type="text" name="village" class="input input-bordered w-full rounded-xl focus:border-blue-500" placeholder="Contoh: Meri">
                                    <span class="error-text text-xs text-red-500 mt-1 hidden">Desa wajib diisi.</span>
                                </div>
                            </div>

                            {{-- Catatan --}}
                            <div class="form-control w-full">
                                <label class="label pb-1">
                                    <span class="label-text font-bold text-gray-700">Catatan Kurir <span class="font-normal text-gray-400 text-xs ml-1">(Opsional)</span></span>
                                </label>
                                <input type="text" name="courier_note" class="input input-bordered w-full rounded-xl focus:border-blue-500" placeholder="Warna pagar, titip di pos satpam, dll.">
                            </div>

                            {{-- Primary Toggle --}}
                            <div class="form-control w-full bg-gray-50 p-3 rounded-xl border border-gray-100">
                                <label class="label cursor-pointer justify-start gap-3 p-0">
                                    <input type="checkbox" name="is_primary" class="checkbox checkbox-primary checkbox-sm rounded" checked>
                                    <span class="label-text text-gray-700 font-medium">Jadikan Alamat Utama</span>
                                </label>
                            </div>
                        </form>

                        {{-- Spacer agar form tidak tertutup footer --}}
                        <div class="h-4"></div>
                    </div>

                    {{-- Sticky Modal Footer --}}
                    <div class="p-4 border-t border-gray-100 bg-white z-10 w-full">
                        <button type="button" id="btn-save-address" onclick="submitNewAddress()" class="btn btn-primary w-full text-white font-bold rounded-xl shadow-lg shadow-blue-500/30 text-lg normal-case h-12 
        disabled:bg-blue-400 disabled:border-blue-400 disabled:text-white disabled:opacity-50 disabled:shadow-none transition-all duration-200" disabled>
                            Simpan Alamat & Gunakan
                        </button>
                    </div>

                </div>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop bg-gray-900/50"><button>close</button></form>
    </dialog>

@endsection

@push('scripts')
    {{-- Leaflet JS --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        $(document).ready(function() {

            // --- 1. KONFIGURASI & VARIABEL GLOBAL ---
            const STORE_LAT = {{ $storeConfig['lat'] }};
            const STORE_LNG = {{ $storeConfig['lng'] }};
            const BASE_COST = {{ $storeConfig['base_shipping_cost'] }};
            const COST_KM = {{ $storeConfig['cost_per_km'] }};
            const SUBTOTAL = {{ $subtotal }};
            const ADMIN_FEE = {{ $adminFee }};

            // State Variables
            let map = null;
            let marker = null;
            let selectedLat = STORE_LAT;
            let selectedLng = STORE_LNG;
            let currentStep = 1;

            // Setup CSRF Token untuk AJAX jQuery
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // --- 2. INIT HALAMAN ---
            // Cek apakah user sudah punya alamat dengan koordinat saat halaman dimuat
            @if ($address && $address->latitude)
                calculateShipping({{ $address->latitude }}, {{ $address->longitude }});
            @endif

            // --- 3. MODAL HANDLERS (jQuery) ---

            // Fungsi Buka Modal List Alamat
            window.openAddressListModal = function() {
                // DaisyUI menggunakan native <dialog>, kita akses elemen native via [0]
                $('#address_list_modal')[0].showModal();
            };

            // Fungsi Buka Modal Tambah Alamat (Wizard)
            window.openAddAddressModal = function() {
                $('#address_list_modal')[0].close();
                $('#add_address_modal')[0].showModal();
                goToStep(1); // Reset ke langkah 1
            };

            // Tombol Close Modal (Standard DaisyUI pattern, handled by form method="dialog")
            // Kita tambahkan listener extra jika butuh reset form saat close
            $('#add_address_modal').on('close', function() {
                // Optional: Reset form jika ditutup
                // $('#add-address-form')[0].reset();
            });


            // --- 4. STEPPER LOGIC (jQuery) ---

            window.goToStep = function(step) {
                currentStep = step;

                // 1. Toggle Content Visibility
                // Sembunyikan semua id yang berawalan step-content-
                $('[id^="step-content-"]').addClass('hidden');
                // Tampilkan step yang aktif
                $('#step-content-' + step).removeClass('hidden');

                // 2. Update Stepper Indicator UI
                updateStepperUI(step);

                // 3. Logic Khusus per Step
                if (step === 2) {
                    // Beri jeda sedikit agar modal render penuh dulu sebelum init map
                    setTimeout(function() {
                        initMap();
                    }, 300);
                }

                // 4. Atur tombol Back
                if (step > 1) {
                    $('#btn-back-step').removeClass('invisible');
                } else {
                    $('#btn-back-step').addClass('invisible');
                }
            };

            window.prevStep = function() {
                if (currentStep > 1) {
                    goToStep(currentStep - 1);
                }
            };

            function updateStepperUI(step) {
                // Loop 1 sampai 3
                for (let i = 1; i <= 3; i++) {
                    let $el = $('#step-indicator-' + i);

                    // Reset class
                    $el.removeClass('active completed');

                    if (i < step) {
                        $el.addClass('completed'); // Step sebelumnya sudah selesai
                    } else if (i === step) {
                        $el.addClass('active'); // Step saat ini
                    }
                }
            }


            // --- 5. MAP LOGIC (Leaflet + jQuery) ---

            function initMap() {
                if (map) {
                    map.invalidateSize(); // Fix jika peta abu-abu sebagian
                    return;
                }

                // Init Leaflet
                map = L.map('map-container').setView([selectedLat, selectedLng], 14);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap'
                }).addTo(map);

                // Buat Custom Icon (Agar marker diam di tengah layar)
                // Disini kita pakai pendekatan event 'move' map daripada draggable marker
                // agar UX nya seperti aplikasi ojek online (Pin diam, Peta gerak)

                // Event: Saat peta digeser
                map.on('move', function() {
                    let center = map.getCenter();
                    selectedLat = center.lat;
                    selectedLng = center.lng;

                    // Update UI Teks Koordinat via jQuery
                    $('#map-coords-preview').text(selectedLat.toFixed(5) + ', ' + selectedLng.toFixed(5));
                });
            }

            // Fungsi Gunakan Lokasi Saat Ini (Geolocation API)
            window.useCurrentLocation = function() {
                if (navigator.geolocation) {
                    // Ubah tombol jadi loading state
                    let $btn = $(event.currentTarget); // Ambil tombol yang diklik
                    let originalText = $btn.html();
                    $btn.html('<span class="loading loading-spinner loading-xs"></span> Mencari Lokasi...').prop('disabled', true);

                    navigator.geolocation.getCurrentPosition(
                        (pos) => {
                            selectedLat = pos.coords.latitude;
                            selectedLng = pos.coords.longitude;

                            // Kembalikan tombol
                            $btn.html(originalText).prop('disabled', false);

                            // Pindah ke Step 2
                            goToStep(2);

                            // Set peta ke lokasi baru (delay dikit nunggu modal)
                            setTimeout(() => {
                                if (map) {
                                    map.setView([selectedLat, selectedLng], 16);
                                }
                            }, 500);
                        },
                        (err) => {
                            alert('Gagal mengambil lokasi: ' + err.message);
                            $btn.html(originalText).prop('disabled', false);
                        }, {
                            enableHighAccuracy: true
                        } // Opsi akurasi tinggi
                    );
                } else {
                    alert('Browser anda tidak mendukung Geolocation.');
                }
            };


            // ==========================================
            // 1. LIVE VALIDATION LOGIC
            // ==========================================

            // Definisi Aturan Validasi
            const validationRules = {
                recipient_name: {
                    required: true,
                    min: 3,
                    msg: "Nama minimal 3 karakter."
                },
                phone_number: {
                    required: true,
                    regex: /^[0-9]{10,15}$/,
                    msg: "Nomor HP harus angka (10-15 digit)."
                },
                full_address: {
                    required: true,
                    min: 10,
                    msg: "Alamat terlalu pendek (min. 10 karakter)."
                },
                district: {
                    required: true,
                    min: 3,
                    msg: "Kecamatan wajib diisi."
                },
                village: {
                    required: true,
                    min: 3,
                    msg: "Desa/Kelurahan wajib diisi."
                }
            };

            // Fungsi Validasi Satu Input
            function validateField($input) {
                let name = $input.attr('name');
                let val = $input.val().trim();
                let rule = validationRules[name];
                let isValid = true;
                let errorMsg = "";

                if (!rule) return true; // Skip jika tidak ada rule (misal: catatan/label)

                // Cek Rules
                if (rule.required && val === "") {
                    isValid = false;
                    errorMsg = "Wajib diisi.";
                } else if (rule.min && val.length < rule.min) {
                    isValid = false;
                    errorMsg = rule.msg;
                } else if (rule.regex && !rule.regex.test(val)) {
                    isValid = false;
                    errorMsg = rule.msg;
                }

                // Update UI Input
                let $errorText = $input.siblings('.error-text');

                if (!isValid) {
                    // STATE ERROR: Merah
                    $input.addClass('input-error').removeClass('focus:border-blue-500');

                    // Buat element error message jika belum ada
                    if ($errorText.length === 0) {
                        $input.after('<span class="error-text text-xs text-red-500 mt-1 block"></span>');
                        $errorText = $input.siblings('.error-text');
                    }
                    $errorText.text(errorMsg).removeClass('hidden');
                } else {
                    // STATE SUCCESS: Kembali ke Default (Tidak Hijau)
                    $input.removeClass('input-error').addClass('focus:border-blue-500');
                    // Sembunyikan pesan error
                    if ($errorText.length > 0) $errorText.addClass('hidden');
                }

                return isValid;
            }

            // Fungsi Cek Seluruh Form untuk Mengaktifkan Tombol
            function checkFormValidity() {
                let allValid = true;

                $('#add-address-form input, #add-address-form textarea').each(function() {
                    let name = $(this).attr('name');
                    if (validationRules[name]) {
                        let val = $(this).val().trim();
                        let rule = validationRules[name];

                        // Cek logic saja tanpa ubah UI (agar tidak merah semua saat belum disentuh)
                        if (rule.required && val === "") allValid = false;
                        else if (rule.min && val.length < rule.min) allValid = false;
                        else if (rule.regex && !rule.regex.test(val)) allValid = false;
                    }
                });

                // Update Tombol Submit State
                if (allValid) {
                    $('#btn-save-address').prop('disabled', false);
                } else {
                    $('#btn-save-address').prop('disabled', true);
                }
            }

            // --- Event Listeners Validasi ---

            // 1. Saat mengetik (Realtime feedback)
            $('#add-address-form input, #add-address-form textarea').on('input', function() {
                validateField($(this));
                checkFormValidity();
            });

            // 2. Saat pindah kolom (Blur)
            $('#add-address-form input, #add-address-form textarea').on('blur', function() {
                validateField($(this));
                checkFormValidity();
            });

            // 3. Reset Form & Validasi saat modal dibuka
            window.resetValidationForm = function() {
                $('#add-address-form')[0].reset();

                // Reset style input ke default
                $('#add-address-form input, #add-address-form textarea')
                    .removeClass('input-error')
                    .addClass('focus:border-blue-500');

                // Sembunyikan semua error text
                $('.error-text').addClass('hidden');

                // Matikan tombol lagi
                $('#btn-save-address').prop('disabled', true);
            }

            // Override fungsi buka modal agar melakukan reset
            let originalOpenModal = window.openAddAddressModal;
            window.openAddAddressModal = function() {
                originalOpenModal();
                if (window.resetValidationForm) window.resetValidationForm();
            };


            // ==========================================
            // 2. AJAX SUBMIT ADDRESS
            // ==========================================

            window.submitNewAddress = function() {
                // 1. Set Hidden Input Koordinat
                $('#form-lat').val(selectedLat);
                $('#form-lng').val(selectedLng);

                // 2. Siapkan Data Form
                let formElement = $('#add-address-form')[0];
                let formData = new FormData(formElement);

                // 3. Setup UI Button (Loading)
                let $btnSubmit = $('#btn-save-address');
                let originalText = $btnSubmit.text();

                $btnSubmit.prop('disabled', true).html('<span class="loading loading-spinner loading-sm"></span> Menyimpan...');

                // 4. Kirim Request AJAX
                $.ajax({
                    url: "{{ route('address.store') }}",
                    type: "POST",
                    data: formData,
                    processData: false, // Wajib false untuk FormData
                    contentType: false, // Wajib false untuk FormData
                    success: function(response) {
                        if (response.status === 'success') {
                            // Sukses: Tutup Modal & Reload
                            $('#add_address_modal')[0].close();
                            // alert('Alamat berhasil disimpan!'); // Opsional
                            window.location.reload();
                        }
                    },
                    error: function(xhr) {
                        // Error: Kembalikan tombol
                        $btnSubmit.prop('disabled', false).text(originalText);

                        if (xhr.status === 422) {
                            // Error Validasi Laravel (Server-side)
                            let errors = xhr.responseJSON.errors;

                            // Loop error dan tampilkan di bawah input masing-masing
                            $.each(errors, function(key, value) {
                                let $input = $(`[name="${key}"]`);
                                if ($input.length > 0) {
                                    $input.addClass('input-error').removeClass('focus:border-blue-500');

                                    let $errText = $input.siblings('.error-text');
                                    if ($errText.length === 0) {
                                        $input.after('<span class="error-text text-xs text-red-500 mt-1 block"></span>');
                                        $errText = $input.siblings('.error-text');
                                    }
                                    $errText.text(value[0]).removeClass('hidden');
                                }
                            });

                            // Alert umum
                            alert('Mohon periksa kembali inputan Anda yang berwarna merah.');

                        } else {
                            // Error Server Lain (500, dll)
                            let msg = 'Terjadi kesalahan sistem.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            }
                            alert(msg);
                        }
                    }
                });
            };


            // --- 7. KALKULASI ONGKIR & TOTAL ---

            window.calculateShipping = function(lat, lng) {
                // Haversine Formula (Logic Matematika Murni)
                const R = 6371;
                const dLat = (lat - STORE_LAT) * Math.PI / 180;
                const dLon = (lng - STORE_LNG) * Math.PI / 180;
                const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                    Math.cos(STORE_LAT * Math.PI / 180) * Math.cos(lat * Math.PI / 180) * Math.sin(dLon / 2) * Math.sin(dLon / 2);
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                const distanceKm = R * c;

                // Logic Biaya (Bisa disesuaikan)
                // Contoh: Base 5000 + (2500 per km jika > 1km)
                let cost = BASE_COST;
                if (distanceKm > 1) {
                    cost += Math.ceil(distanceKm) * COST_KM;
                }

                // Pembulatan ke atas kelipatan 500 (Biar rapi uangnya)
                cost = Math.ceil(cost / 500) * 500;

                // Update UI dengan jQuery
                $('#shipping-cost-label').text(formatRupiah(cost));
                $('#summary-shipping-cost').text(formatRupiah(cost));
                $('#grand-total-display').text(formatRupiah(SUBTOTAL + ADMIN_FEE + cost));

                // Update Input Hidden untuk Form Checkout
                $('#input-shipping-cost').val(cost);

                // Aktifkan Tombol Bayar
                $('#btn-pay').prop('disabled', false);
            };

            // Helper Format Rupiah
            function formatRupiah(num) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(num);
            }

            // Fungsi Pilih Alamat (dari List)
            window.selectAddress = function(id) {
                // Disini kita bisa buat AJAX untuk set 'is_primary' lalu reload
                // Atau cukup reload page dengan query param ?address_id=ID

                // Simulasi:
                // window.location.href = "{{ route('checkout.index') }}?address_id=" + id;
                alert('Memilih alamat ID: ' + id + ' (Fitur ganti alamat perlu implementasi Backend)');
            };

            // --- 8. PROSES PEMBAYARAN (CHECKOUT SUBMIT) ---
            $('#btn-pay').on('click', function() {
                let $btn = $(this);
                $btn.prop('disabled', true).text('Memproses Pembayaran...');

                // Submit Form Checkout (Create Transaction)
                // $('#checkout-form').submit(); // Uncomment untuk submit normal form

                // Atau AJAX:
                alert('Redirect ke Midtrans Snap...');
            });

        }); // End Document Ready
    </script>
@endpush
