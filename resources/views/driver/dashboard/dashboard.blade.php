@extends('layouts/backend/index')
@section('title', 'ShoeCycle | Driver Dashboard')
@section('breadcrumb', 'Dashboard > Driver')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        .driver-card {
            transition: all 0.3s ease;
        }

        .driver-card:hover {
            transform: translateY(-5px);
        }

        #delivery-map {
            height: 400px;
            width: 100%;
            border-radius: 1.5rem;
            z-index: 1;
        }
    </style>
@endpush

@section('backend-content')
    <div class="space-y-6 p-4 text-black">

        {{-- 1. Summary Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="driver-card bg-white p-6 rounded-3xl shadow-sm border border-slate-100 border-l-4 border-l-blue-600">
                <div class="flex items-center gap-4">
                    <div class="bg-blue-100 p-3 rounded-2xl text-blue-600"><i class="fa-solid fa-list-check text-xl"></i></div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest text-black">Tugas Hari Ini</p>
                        <h3 class="text-2xl font-black text-slate-800 text-black">{{ $today_tasks_count ?? 0 }}</h3>
                    </div>
                </div>
            </div>

            <div class="driver-card bg-white p-6 rounded-3xl shadow-sm border border-slate-100 border-l-4 border-l-green-500">
                <div class="flex items-center gap-4 text-black">
                    <div class="bg-green-100 p-3 rounded-2xl text-green-600"><i class="fa-solid fa-clipboard-check text-xl"></i></div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Selesai Hari Ini</p>
                        <h3 class="text-2xl font-black text-slate-800">{{ $completed_today ?? 0 }}</h3>
                    </div>
                </div>
            </div>

            <div class="driver-card bg-white p-6 rounded-3xl shadow-sm border border-slate-100 border-l-4 border-l-orange-500">
                <div class="flex items-center gap-4 text-black">
                    <div class="bg-orange-100 p-3 rounded-2xl text-orange-600"><i class="fa-solid fa-box-open text-xl"></i></div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Antrian Baru</p>
                        <h3 class="text-2xl font-black text-slate-800">{{ $new_orders ?? 0 }}</h3>
                    </div>
                </div>
            </div>

            <div class="driver-card bg-linear-to-br from-blue-600 to-blue-800 p-6 rounded-3xl shadow-xl text-white">
                <div class="flex items-center gap-4">
                    <div class="bg-white/20 p-3 rounded-2xl text-white"><i class="fa-solid fa-history text-xl"></i></div>
                    <div>
                        <p class="text-[10px] font-bold text-blue-100 uppercase tracking-widest">Total Selesai</p>
                        <h3 class="text-2xl font-black">{{ $total_history ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. Daftar Tugas di Samping Peta --}}
        <div class="bg-white p-6 rounded-4xl shadow-sm border border-slate-100 text-black">
            <h4 class="font-black text-slate-800 mb-6">Detail Tugas Hari Ini</h4>
            <div class="space-y-4 max-h-[350px] overflow-y-auto no-scrollbar">
                @forelse($today_tasks_list as $task)
                    <div class="p-4 rounded-2xl border transition-colors cursor-pointer group {{ $task->transaction_status == 'shipping' ? 'bg-blue-50 border-blue-200' : 'bg-slate-50 border-slate-100' }}" onclick="focusToLocation({{ $task->address->latitude }}, {{ $task->address->longitude }})">

                        <div class="flex justify-between items-center mb-2">
                            <span class="text-[10px] font-black uppercase {{ $task->transaction_status == 'shipping' ? 'text-blue-700' : 'text-orange-600' }}">
                                <i class="fa-solid {{ $task->transaction_status == 'shipping' ? 'fa-truck-fast' : 'fa-clock' }} mr-1"></i>
                                {{ $task->transaction_status }}
                            </span>
                            <span class="text-[9px] font-bold text-slate-400">#{{ $task->invoice }}</span>
                        </div>

                        <h5 class="font-bold text-sm text-slate-800 truncate">{{ $task->customer->name }}</h5>
                        <p class="text-[11px] text-slate-500 line-clamp-1 mt-0.5">{{ $task->address->full_address }}</p>
                    </div>
                @empty
                    <p class="text-center text-slate-400 py-10 text-xs font-bold uppercase tracking-widest">Tidak ada tugas</p>
                @endforelse
            </div>
        </div>

        {{-- 2. Map & Active Task Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Peta Rute Pengiriman --}}
            <div class="lg:col-span-2 bg-white p-4 rounded-4xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="flex justify-between items-center mb-4 px-2">
                    <h4 class="font-black text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-map-location-dot text-blue-500"></i> Peta Pengiriman Aktif
                    </h4>
                    <span class="badge badge-primary badge-sm font-bold uppercase p-3">Mode Live</span>
                </div>
                <div id="delivery-map"></div>
            </div>

            {{-- Daftar Tugas Cepat --}}
            <div class="bg-white p-6 rounded-4xl shadow-sm border border-slate-100">
                <h4 class="font-black text-slate-800 mb-6">Tugas Saat Ini</h4>
                <div class="space-y-4 max-h-[350px] overflow-y-auto no-scrollbar">
                    @forelse($today_tasks_list as $delivery)
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 hover:border-blue-300 transition-colors cursor-pointer group" onclick="focusToLocation({{ $delivery->address->latitude }}, {{ $delivery->address->longitude }})">
                            <div class="flex justify-between items-start mb-2">
                                <span class="text-[10px] font-black uppercase text-blue-600 bg-blue-50 px-2 py-1 rounded-md">#{{ $delivery->invoice }}</span>
                                <i class="fa-solid fa-chevron-right text-slate-300 group-hover:text-blue-500 transition-transform group-hover:translate-x-1"></i>
                            </div>
                            <h5 class="font-bold text-sm text-slate-800 truncate">{{ $delivery->address->recipient_name }}</h5>
                            <p class="text-[11px] text-slate-500 line-clamp-2 mt-1">{{ $delivery->address->full_address }}</p>
                            <div class="mt-3 pt-3 border-t border-slate-200 flex justify-between items-center">
                                <a href="https://www.google.com/maps?q={{ $delivery->address->latitude }},{{ $delivery->address->longitude }}" target="_blank" class="text-[10px] font-bold text-blue-500 hover:underline">
                                    <i class="fa-solid fa-location-arrow mr-1"></i> Buka Maps
                                </a>
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-tighter">{{ $delivery->address->district }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10">
                            <img src="{{ asset('assets/images/empty-task.svg') }}" class="w-24 mx-auto mb-4 opacity-20">
                            <p class="text-xs font-bold text-slate-400">Belum ada tugas aktif</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        let addressMap = null;
        // PERBAIKAN: Ubah $delivery_locations menjadi $today_tasks_list
        const locations = @json($today_tasks_list);

        function initMap() {
            const container = document.getElementById('delivery-map');
            if (!container) return;

            // Default ke Mojokerto
            addressMap = L.map('delivery-map', {
                zoomControl: false
            }).setView([-7.472, 112.438], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap'
            }).addTo(addressMap);

            L.control.zoom({
                position: 'bottomright'
            }).addTo(addressMap);

            // Tambahkan Marker untuk setiap tugas (Antrian & Shipping)
            locations.forEach(task => {
                if (task.address.latitude && task.address.longitude) {
                    // Berikan warna icon berbeda jika sudah 'shipping' (opsional)
                    const marker = L.marker([task.address.latitude, task.address.longitude]).addTo(addressMap);

                    marker.bindPopup(`
                    <div class="p-2 text-black">
                        <h6 class="font-black text-sm mb-1">${task.customer.name}</h6>
                        <p class="text-[10px] leading-tight text-slate-500 mb-2">${task.address.full_address}</p>
                        <span class="badge badge-sm uppercase font-bold ${task.transaction_status === 'shipping' ? 'badge-primary' : 'badge-warning'}">
                            ${task.transaction_status}
                        </span>
                    </div>
                `);
                }
            });

            // Supaya render peta sempurna (tidak abu-abu)
            setTimeout(() => {
                addressMap.invalidateSize();
            }, 500);
        }

        function focusToLocation(lat, lng) {
            if (addressMap) {
                addressMap.flyTo([lat, lng], 16, {
                    animate: true,
                    duration: 1.5
                });
            }
        }

        document.addEventListener('DOMContentLoaded', initMap);
    </script>
@endpush
