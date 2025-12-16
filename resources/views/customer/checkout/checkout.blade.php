@extends('layouts/frontend/index')
@section('title', 'Checkout | ShoeCycle')

@push('styles')
    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        #map-container {
            height: 400px;
            width: 100%;
            border-radius: 1rem;
            z-index: 1;
        }

        .leaflet-bottom {
            z-index: 0 !important;
        }

        /* Fix layer issue */
    </style>
@endpush

@section('frontend-content')
    <section class="py-10 bg-slate-50 min-h-screen">
        <div class="container mx-auto px-4">

            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 font-heading mb-6">Checkout Pengiriman</h1>

            <form action="#" method="POST" id="checkout-form">
                @csrf
                {{-- Hidden Inputs untuk Data Pengiriman --}}
                <input type="hidden" name="latitude" id="input-lat">
                <input type="hidden" name="longitude" id="input-lng">
                <input type="hidden" name="distance" id="input-distance">
                <input type="hidden" name="shipping_cost" id="input-shipping-cost">

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                    {{-- LEFT COLUMN --}}
                    <div class="lg:col-span-8 space-y-6">

                        {{-- 1. ALAMAT & PINPOINT --}}
                        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <i class="fas fa-map-marked-alt text-blue-600"></i> Alamat & Titik Pengiriman
                            </h2>

                            @if ($address)
                                <div class="flex flex-col gap-4">
                                    {{-- Info Alamat Database --}}
                                    <div class="p-4 border border-blue-100 bg-blue-50/50 rounded-xl relative">
                                        {{-- Label Alamat --}}
                                        <div class="absolute top-4 right-4">
                                            <span class="badge badge-outline badge-primary text-xs uppercase">{{ $address->label }}</span>
                                            @if ($address->is_primary)
                                                <span class="badge badge-primary badge-xs ml-1">Utama</span>
                                            @endif
                                        </div>

                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="font-bold text-gray-900">{{ $address->recipient_name }}</span>
                                            <span class="text-gray-500 text-sm">| {{ $address->phone_number }}</span>
                                        </div>

                                        <p class="text-gray-600 text-sm leading-relaxed mt-2">
                                            {{ $address->full_address }}<br>
                                            Kec. {{ $address->district }}, {{ $address->village }}<br>
                                            @if ($address->courier_note)
                                                <span class="text-xs text-orange-600 italic"><i class="fas fa-sticky-note mr-1"></i>Catatan: {{ $address->courier_note }}</span>
                                            @endif
                                        </p>
                                    </div>

                                    {{-- Pinpoint Status --}}
                                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-4 border border-gray-200 rounded-xl bg-white">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center text-orange-600">
                                                <i class="fas fa-location-dot text-lg"></i>
                                            </div>
                                            <div>
                                                @if ($address->latitude && $address->longitude)
                                                    <p class="text-sm font-bold text-green-600" id="pin-status-text">Lokasi Tersimpan</p>
                                                    <p class="text-xs text-gray-500" id="pin-coords-text">{{ number_format($address->latitude, 5) }}, {{ number_format($address->longitude, 5) }}</p>
                                                @else
                                                    <p class="text-sm font-bold text-red-500" id="pin-status-text">Lokasi Belum Diatur</p>
                                                    <p class="text-xs text-gray-500" id="pin-coords-text">Wajib set titik peta untuk hitung ongkir</p>
                                                @endif
                                            </div>
                                        </div>
                                        <button type="button" onclick="openMapModal()" class="btn btn-outline btn-sm border-gray-300 hover:border-blue-600 hover:bg-blue-50 hover:text-blue-600 normal-case w-full sm:w-auto">
                                            <i class="fas fa-map-pin mr-1"></i> {{ $address->latitude ? 'Ubah' : 'Atur' }} Pinpoint
                                        </button>
                                    </div>
                                </div>
                            @else
                                {{-- Empty State --}}
                                <div class="text-center py-6 border-2 border-dashed border-gray-200 rounded-xl">
                                    <p class="text-gray-500 text-sm mb-3">Kamu belum mengatur alamat pengiriman.</p>
                                    {{-- Link ke halaman tambah alamat --}}
                                    <a href="#" class="btn btn-primary btn-sm rounded-lg text-white">
                                        <i class="fas fa-plus mr-1"></i> Tambah Alamat Baru
                                    </a>
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

                                <div class="flex gap-4 mb-6 last:mb-0 border-b last:border-b-0 border-gray-100 pb-6 last:pb-0">
                                    {{-- Image --}}
                                    <div class="w-20 h-20 bg-gray-50 rounded-xl border border-gray-200 overflow-hidden flex-shrink-0">
                                        <img src="{{ $imageUrl }}" class="w-full h-full object-contain mix-blend-multiply p-1">
                                    </div>

                                    {{-- Detail --}}
                                    <div class="flex-1">
                                        <h3 class="font-bold text-gray-900 text-sm md:text-base mb-1">{{ $item->variant->shoe->name }}</h3>
                                        <p class="text-xs text-gray-500 mb-2">
                                            {{ $item->variant->color }} • Size {{ $item->variant->size }} • {{ $item->quantity }}x
                                        </p>
                                        <div class="font-bold text-blue-600">Rp {{ number_format($item->variant->price, 0, ',', '.') }}</div>

                                        {{-- Catatan --}}
                                        <div class="mt-3">
                                            <input type="text" name="note[{{ $item->id }}]" class="input input-sm input-bordered w-full md:w-2/3 text-xs focus:border-blue-500 rounded-lg placeholder-gray-400 bg-gray-50 focus:bg-white transition-colors" placeholder="Catatan (Opsional)...">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- 3. INFO PENGIRIMAN LOKAL --}}
                        <div class="bg-blue-50 p-4 rounded-xl border border-blue-100 flex items-start gap-3">
                            <i class="fas fa-info-circle text-blue-600 mt-1"></i>
                            <div>
                                <h4 class="font-bold text-blue-900 text-sm">Pengiriman Lokal ShoeCycle</h4>
                                <p class="text-xs text-blue-700 mt-1">
                                    Kami menggunakan kurir internal untuk area Kabupaten & Kota Mojokerto.
                                    Biaya dihitung berdasarkan jarak dari toko kami.
                                </p>
                            </div>
                        </div>

                    </div>

                    {{-- RIGHT COLUMN --}}
                    <div class="lg:col-span-4">
                        <div class="sticky top-24 space-y-4">

                            {{-- Payment Summary --}}
                            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-lg shadow-blue-100/50">
                                <h3 class="font-bold text-lg text-gray-900 mb-4">Ringkasan Pembayaran</h3>

                                <div class="space-y-3 mb-6 border-b border-gray-100 pb-4 text-sm">
                                    <div class="flex justify-between text-gray-600">
                                        <span>Total Harga ({{ $cartItems->count() }} Barang)</span>
                                        <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between text-gray-600">
                                        <span>Biaya Layanan</span>
                                        <span>Rp {{ number_format($adminFee, 0, ',', '.') }}</span>
                                    </div>

                                    {{-- Kalkulasi Jarak & Ongkir --}}
                                    <div class="flex justify-between text-gray-900 font-medium bg-gray-50 p-2 rounded-lg">
                                        <span class="flex items-center gap-1"><i class="fas fa-route text-gray-400"></i> Jarak</span>
                                        <span id="distance-display">- km</span>
                                    </div>
                                    <div class="flex justify-between text-gray-600">
                                        <span>Ongkos Kirim</span>
                                        <span id="shipping-cost-display" class="font-bold">-</span>
                                    </div>
                                </div>

                                <div class="flex justify-between items-center mb-6">
                                    <span class="font-bold text-lg text-gray-900">Total Tagihan</span>
                                    <span class="font-bold text-xl text-blue-600" id="grand-total-display">-</span>
                                </div>

                                <div class="bg-blue-50 p-4 rounded-xl flex items-center gap-4 mb-6 border border-blue-100 shadow-sm">

                                    {{-- mix-blend-multiply: Opsional, agar background putih pada gambar (jika ada) menyatu dengan biru. --}}
                                    <img src="{{ asset('assets/upload/logo/midtrans.svg') }}" class="h-20 w-auto object-contain opacity-80 mix-blend-multiply">

                                    {{-- TEKS --}}
                                    <div class="flex flex-col">
                                        <span class="font-bold text-blue-900 text-sm">Pembayaran via Midtrans</span>
                                        <span class="text-xs text-blue-700">Transaksi aman & terverifikasi otomatis</span>
                                    </div>

                                </div>

                                <button type="button" id="btn-pay" class="btn btn-primary w-full rounded-xl text-white font-bold h-12 shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 hover:-translate-y-0.5 transition-all" disabled>
                                    Atur Lokasi Dulu
                                </button>
                            </div>

                        </div>
                    </div>

                </div>
            </form>
        </div>
    </section>

    {{-- MODAL PETA --}}
    <dialog id="map_modal" class="modal modal-bottom sm:modal-middle backdrop-blur-sm">
        <div class="modal-box w-11/12 max-w-3xl bg-white p-0 rounded-2xl overflow-hidden shadow-2xl">
            {{-- Header Modal --}}
            <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-white z-10 relative">
                <h3 class="font-bold text-lg">Tentukan Titik Pengiriman</h3>
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost">✕</button>
                </form>
            </div>

            {{-- Map Container --}}
            <div class="relative">
                <div id="map-container"></div>

                {{-- Floating Info on Map --}}
                <div class="absolute bottom-6 left-4 right-4 bg-white/90 backdrop-blur-sm p-4 rounded-xl shadow-lg border border-gray-200 z-[400] flex flex-col sm:flex-row justify-between items-center gap-3">
                    <div class="text-sm">
                        <p class="font-bold text-gray-900">Geser marker merah ke rumahmu</p>
                        <p class="text-xs text-gray-500" id="modal-distance-info">Jarak dari toko: - km</p>
                    </div>
                    <button type="button" onclick="confirmLocation()" class="btn btn-primary btn-sm rounded-lg text-white shadow-lg">
                        Simpan Lokasi Ini
                    </button>
                </div>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>

@endsection

@push('scripts')
    {{-- Leaflet JS --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        // --- 1. KONFIGURASI DARI CONTROLLER ---
        // Ambil data konfigurasi yang dikirim dari controller
        const STORE_LAT = {{ $storeConfig['lat'] }};
        const STORE_LNG = {{ $storeConfig['lng'] }};
        const BASE_SHIPPING_COST = {{ $storeConfig['base_shipping_cost'] }};
        const COST_PER_KM = {{ $storeConfig['cost_per_km'] }};

        // Data transaksi
        const subtotal = {{ $subtotal }};
        const adminFee = {{ $adminFee }};

        let map, marker, userLat, userLng, currentDistance, currentCost;

        // --- 2. INISIALISASI PETA ---
        function initMap() {
            if (map) return; // Jangan init ulang

            map = L.map('map-container').setView([STORE_LAT, STORE_LNG], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            // Marker Toko (Fixed)
            const storeIcon = L.icon({
                iconUrl: 'https://cdn-icons-png.flaticon.com/512/4481/4481197.png',
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                popupAnchor: [0, -32]
            });
            L.marker([STORE_LAT, STORE_LNG], {
                icon: storeIcon
            }).addTo(map).bindPopup("<b>Toko ShoeCycle</b>").openPopup();

            // Marker User (Draggable)
            // Jika user sudah punya lat/lng tersimpan, gunakan itu. Jika tidak, geser dikit dari toko.
            const startLat = userLat ? userLat : (STORE_LAT - 0.005);
            const startLng = userLng ? userLng : (STORE_LNG + 0.005);

            marker = L.marker([startLat, startLng], {
                draggable: true
            }).addTo(map);

            // Event saat marker digeser
            marker.on('dragend', function(e) {
                const position = marker.getLatLng();
                updateCalculation(position.lat, position.lng);
            });

            // Hitung awal jika membuka map
            updateCalculation(startLat, startLng);
        }

        // --- 3. HITUNG JARAK (HAVERSINE) & ONGKIR ---
        function updateCalculation(lat, lng) {
            userLat = lat;
            userLng = lng;

            const R = 6371;
            const dLat = deg2rad(lat - STORE_LAT);
            const dLon = deg2rad(lng - STORE_LNG);
            const a =
                Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(deg2rad(STORE_LAT)) * Math.cos(deg2rad(lat)) * Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            const distance = R * c;

            currentDistance = distance.toFixed(2);

            let cost = 0;
            if (currentDistance > 1) {
                cost = BASE_SHIPPING_COST + (Math.ceil(currentDistance) * COST_PER_KM);
            } else {
                cost = BASE_SHIPPING_COST;
            }

            // Pembulatan ke 500 terdekat
            currentCost = Math.ceil(cost / 500) * 500;

            document.getElementById('modal-distance-info').innerText = `Jarak dari toko: ${currentDistance} km (Est. Rp ${new Intl.NumberFormat('id-ID').format(currentCost)})`;
        }

        function deg2rad(deg) {
            return deg * (Math.PI / 180);
        }

        // --- 4. MODAL CONTROLS ---
        function openMapModal() {
            document.getElementById('map_modal').showModal();
            setTimeout(() => {
                initMap();
                map.invalidateSize();
            }, 200);
        }

        function confirmLocation() {
            // Update Input Hidden
            document.getElementById('input-lat').value = userLat;
            document.getElementById('input-lng').value = userLng;
            document.getElementById('input-distance').value = currentDistance;
            document.getElementById('input-shipping-cost').value = currentCost;

            // Update UI Utama
            document.getElementById('pin-status-text').innerText = "Lokasi terkunci";
            document.getElementById('pin-status-text').classList.remove('text-red-500');
            document.getElementById('pin-status-text').classList.add('text-green-600');
            document.getElementById('pin-coords-text').innerText = `${userLat.toFixed(5)}, ${userLng.toFixed(5)}`;

            document.getElementById('distance-display').innerText = `${currentDistance} km`;
            document.getElementById('shipping-cost-display').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(currentCost);

            // Update Grand Total
            const grandTotal = subtotal + adminFee + currentCost;
            document.getElementById('grand-total-display').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(grandTotal);

            // Enable Tombol Bayar
            const btnPay = document.getElementById('btn-pay');
            btnPay.disabled = false;
            btnPay.innerText = "Bayar Sekarang";
            btnPay.classList.remove('btn-primary', 'shadow-blue-500/30');
            btnPay.classList.add('btn-success', 'text-white', 'shadow-green-500/30');

            document.getElementById('map_modal').close();
        }

        // --- AUTO CALCULATE JIKA ALAMAT SUDAH ADA ---
        $(document).ready(function() {
            @if ($address && $address->latitude && $address->longitude)
                // Set data awal dari database
                userLat = {{ $address->latitude }};
                userLng = {{ $address->longitude }};

                // Hitung tanpa membuka map
                updateCalculation(userLat, userLng);
                confirmLocation();
            @endif
        });

        // Logic Tombol Bayar
        $('#btn-pay').click(function() {
            alert(`Siap proses ke backend! \nTotal Bayar: ${document.getElementById('grand-total-display').innerText}`);
            // $(this).prop('disabled', true).text('Memproses...');
            // $('#checkout-form').submit(); // Aktifkan ini nanti kalau route store sudah siap
        });
    </script>
@endpush
