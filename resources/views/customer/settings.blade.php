@extends('layouts/frontend/index')
@section('title', 'Pengaturan Akun | ShoeCycle')

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
    <div class="bg-[#f0f3f7] min-h-screen py-12">
        <div class="container mx-auto px-4 max-w-7xl">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                {{-- SIDEBAR KIRI --}}
                <aside class="lg:col-span-3 space-y-5 sticky top-24">
                    <div class="bg-white rounded-3xl border border-gray-200 p-6 shadow-sm">
                        <div class="flex items-center gap-4">
                            <div class="avatar">
                                <div class="w-14 rounded-4xl ring ring-blue-50 ring-offset-2">
                                    <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=2563eb&color=fff&bold=true' }}" />
                                </div>
                            </div>
                            <div class="min-w-0">
                                <h2 class="font-bold text-gray-900 truncate text-base">{{ $user->name }}</h2>
                                <p class="text-[11px] text-blue-600 font-black uppercase tracking-widest mt-0.5">{{ $user->email }}</p>
                            </div>
                        </div>

                        <div class="divider my-4 opacity-50"></div>

                        {{-- Status Saldo Dummy ala Tokopedia --}}
                        <div class="space-y-3">
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-gray-500 flex items-center gap-2"><i class="fa-solid fa-id-card text-blue-500"></i> Akun dibuat pada</span>
                                <span class="font-black text-gray-900">{{ $user->created_at->format('d F Y') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Navigation Menu --}}
                    <div class="bg-white rounded-3xl border border-gray-200 overflow-hidden shadow-sm text-black">
                        <ul class="menu w-full px-0 py-2 font-medium">
                            <li class="menu-title text-[11px] uppercase text-gray-400 py-4 px-6 tracking-[0.2em] flex flex-row items-center gap-2">
                                <i class="fa-solid fa-gear text-[10px]"></i>
                                <span>Pengaturan</span>
                            </li>

                            <li class="px-2">
                                <a href="#" class="py-3.5 px-4 rounded-xl hover:bg-slate-50 transition-all duration-200 flex items-center gap-3 w-full">
                                    <i class="fas fa-heart w-5 text-gray-400"></i>
                                    <span class="text-sm">Wishlist</span>
                                </a>
                            </li>

                            <li class="px-2 mt-1">
                                <button onclick="switchTab('biodata')" id="side-biodata" class="tab-side active bg-blue-50 text-blue-600 py-3.5 px-4 rounded-xl font-bold flex items-center gap-3 w-full transition-all duration-200">
                                    <i class="fas fa-user-cog w-5"></i>
                                    <span class="text-sm">Profil Akun</span>
                                </button>
                            </li>
                        </ul>
                    </div>
                </aside>

                <main class="lg:col-span-9">
                    <div class="bg-white rounded-4xl border border-gray-200 shadow-sm min-h-[700px] flex flex-col overflow-hidden">

                        <div class="flex border-b border-gray-100 px-8 bg-white overflow-x-auto no-scrollbar">
                            <button onclick="switchTab('biodata')" class="tab-item py-6 px-6 text-sm font-bold border-b-4 border-blue-600 text-blue-600 whitespace-nowrap transition-all duration-300" id="tab-biodata">Biodata Diri</button>
                            <button onclick="switchTab('alamat')" class="tab-item py-6 px-6 text-sm font-medium border-b-4 border-transparent text-gray-400 whitespace-nowrap transition-all duration-300" id="tab-alamat">Daftar Alamat</button>
                            <button onclick="switchTab('keamanan')" class="tab-item py-6 px-6 text-sm font-medium border-b-4 border-transparent text-gray-400 whitespace-nowrap transition-all duration-300" id="tab-keamanan">Keamanan</button>
                        </div>

                        <div class="p-8 md:p-12 flex-1">

                            {{-- 1. SECTION: BIODATA DIRI (EDITABLE) --}}
                            <div id="section-biodata" class="tab-pane space-y-12 animate-in fade-in slide-in-from-bottom-2 duration-500">
                                <form action="{{ route('settings.profile.update') }}" enctype="multipart/form-data" method="POST" class="grid grid-cols-1 md:grid-cols-12 gap-12">
                                    @csrf @method('PUT')
                                    {{-- Foto Profil --}}
                                    <div class="md:col-span-4 flex flex-col items-center">
                                        <div class="relative group">
                                            {{-- Container Gambar --}}
                                            <div class="w-52 h-52 rounded-[2.5rem] overflow-hidden border-[6px] border-slate-100 shadow-xl bg-slate-50 relative">
                                                <img id="image-preview" src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&size=256&background=2563eb&color=fff&bold=true' }}" class="w-full h-full object-cover transition-opacity duration-300">

                                                <button type="button" id="btn-remove-image" onclick="removeImagePreview()" class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300">
                                                    <div class="w-12 h-12 bg-red-500 text-white rounded-xl flex items-center justify-center shadow-lg transform hover:scale-110 active:scale-95">
                                                        <i class="fas fa-trash-can text-xl"></i>
                                                    </div>
                                                </button>
                                            </div>

                                            {{-- Tombol Kamera (Input Trigger) --}}
                                            <label for="profile-input" class="absolute -bottom-2 -right-2 w-14 h-14 bg-blue-600 text-white rounded-xl shadow-2xl flex items-center justify-center cursor-pointer hover:bg-blue-700 hover:scale-110 transition-all z-10 border-4 border-white">
                                                <i class="fas fa-camera text-xl"></i>
                                                <input type="file" id="profile-input" name="profile_picture" class="hidden" accept="image/*" onchange="handleImageSelect(this)">
                                            </label>
                                        </div>

                                        {{-- File Metadata --}}
                                        <div id="file-info" class="mt-6 text-center hidden animate-in zoom-in duration-300">
                                            <p id="file-name" class="text-xs font-bold text-gray-900 truncate max-w-[200px]"></p>
                                            <p id="file-size" class="text-[10px] text-blue-600 font-black uppercase tracking-widest mt-1"></p>
                                        </div>

                                        <div id="file-instruction" class="mt-6 text-center">
                                            <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest leading-relaxed">Maksimum: 100KB<br>Format: JPG, PNG, WEBP</p>
                                        </div>
                                    </div>

                                    {{-- Inputs --}}
                                    <div class="md:col-span-8 space-y-8 text-black">
                                        <div class="space-y-6">
                                            <h4 class="font-black text-gray-900 text-lg">Biodata Diri</h4>

                                            {{-- Cari bagian input Nama dan Email, tambahkan ID dan data-original --}}
                                            <div class="form-control w-full">
                                                <label class="label"><span class="label-text font-bold text-gray-700">Nama Lengkap</span></label>
                                                <input type="text" id="input-name" name="name" value="{{ $user->name }}" data-original="{{ $user->name }}" class="input input-bordered w-full rounded-xl bg-slate-50 focus:bg-white border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 transition-all">
                                            </div>

                                            <div class="form-control w-full">
                                                <label class="label"><span class="label-text font-bold text-gray-700">Alamat Email</span></label>
                                                <div class="relative">
                                                    <input type="email" id="input-email" name="email" value="{{ $user->email }}" data-original="{{ $user->email }}" class="input input-bordered w-full rounded-xl bg-slate-50 focus:bg-white border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 transition-all">
                                                </div>
                                                <span id="email-error-msg" class="text-[10px] text-red-500 font-bold mt-1 hidden"></span>
                                            </div>

                                            {{-- Bagian Tombol (Simpan & Hapus) --}}
                                            <div class="flex items-center justify-start gap-3 pt-1">
                                                <button type="submit" id="btn-save-biodata" class="btn bg-blue-400 hover:bg-blue-500 rounded-md text-white font-black normal-case h-10 border-none disabled:opacity-50" disabled>
                                                    <i class="fas fa-save text-xl"></i> Simpan
                                                </button>

                                                <button type="button" onclick="delete_account_modal.showModal()" class="btn bg-red-400 hover:bg-red-500 rounded-md text-white font-black normal-case h-10 border-none">
                                                    <i class="fa-solid fa-trash-can text-xl"></i> Hapus Akun
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            {{-- 2. SECTION: DAFTAR ALAMAT --}}
                            <div id="section-alamat" class="tab-pane hidden space-y-8 animate-in fade-in slide-in-from-bottom-2 duration-500 text-black">
                                <div class="flex flex-col sm:flex-row justify-between items-center gap-5">
                                    <div class="relative w-full max-w-xl">
                                        <input type="text" id="search-address-list" placeholder="Cari alamat..." class="input input-bordered w-full pl-12 rounded-xl h-12 text-sm border-gray-200 focus:border-blue-500 text-black bg-white">
                                        <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 z-10 pointer-events-none"></i>
                                    </div>
                                    <button onclick="openAddAddressModal()" class="btn bg-blue-500 hover:bg-blue-600 border-none text-white rounded-xl h-12 px-8 font-black shadow-lg shadow-blue-100">+ Tambah Alamat</button>
                                </div>

                                <div class="grid grid-cols-1 gap-5 mt-8">
                                    @forelse ($addresses as $addr)
                                        <div class="address-card p-8 rounded-xl border-2 transition-all relative {{ $addr->is_primary ? 'border-blue-500 bg-[#f7faff]' : 'border-gray-100 bg-white hover:border-gray-200' }}">
                                            @if ($addr->is_primary)
                                                <div class="absolute top-6 right-6 bg-blue-500 w-10 h-10 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-100 animate-in zoom-in duration-300">
                                                    <i class="fas fa-check text-xl"></i>
                                                </div>
                                            @endif
                                            <div class="flex items-center gap-3 mb-4">
                                                <span class="text-[11px] font-black uppercase text-gray-900 tracking-wider">
                                                    @php $label = $addr?->label; @endphp
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
                                                @if ($addr->is_primary)
                                                    <span class="bg-blue-500 text-white text-[10px] px-2.5 py-1 rounded-md font-bold uppercase">Utama</span>
                                                @endif
                                            </div>

                                            <div class="flex justify-between items-start gap-10">
                                                <div class="space-y-1.5 text-black">
                                                    <h5 class="font-black text-gray-900 text-xl leading-none">{{ $addr->recipient_name }}</h5>
                                                    <p class="text-sm font-bold text-gray-900">{{ $addr->phone_number }}</p>
                                                    <p class="text-sm text-gray-500 leading-relaxed max-w-2xl font-medium">
                                                        {{ $addr->full_address }}, {{ $addr->village }}, {{ $addr->district }}, Mojokerto
                                                    </p>
                                                    @if ($addr->latitude)
                                                        <p class="text-[11px] text-blue-600 font-black uppercase mt-3 flex items-center gap-1.5">
                                                            <i class="fas fa-map-marker-alt"></i> Sudah Pinpoint
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="mt-5 flex items-center gap-8">
                                                <button onclick="editAddress({{ json_encode($addr) }})" class="btn bg-blue-400 hover:bg-blue-500 text-white font-black text-sm  transition-all">
                                                    <i class="fas fa-edit text-lg"></i>
                                                    Ubah
                                                </button>
                                                @if (!$addr->is_primary)
                                                    <div class="w-0.5 h-4 bg-gray-100"></div>
                                                    <button type="button" onclick="setPrimaryAddress({{ $addr->id }})" class="text-gray-400 font-black text-sm hover:text-blue-500 transition-all">
                                                        Jadikan Utama
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-24 bg-slate-50 rounded-[3rem] border-2 border-dashed border-slate-200">
                                            <i class="fas fa-map-location-dot text-5xl text-gray-200 mb-4 block"></i>
                                            <p class="font-bold text-gray-400">Belum ada alamat tersimpan.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            {{-- 3. SECTION: KEAMANAN (FULL WIDTH & INTERAKTIF) --}}
                            <div id="section-keamanan" class="tab-pane hidden space-y-10 animate-in fade-in slide-in-from-bottom-2 duration-500 text-black">
                                <div class="w-full"> {{-- Menghapus max-w-md agar memenuhi card --}}
                                    <div class="mb-10">
                                        <h4 class="font-black text-gray-900 text-xl flex items-center gap-3">
                                            <i class="fas fa-shield-halved text-blue-600"></i> Keamanan Akun
                                        </h4>
                                        <p class="text-sm text-gray-500 mt-2">Kelola kata sandi Anda untuk menjaga keamanan akun ShoeCycle.</p>
                                    </div>

                                    <form action="{{ route('settings.password.update') }}" method="POST" id="form-change-password" class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-6">
                                        @csrf @method('PUT')

                                        {{-- Password Saat Ini --}}
                                        <div class="form-control w-full md:col-span-2">
                                            <label class="label"><span class="label-text font-bold text-gray-700">Password Saat Ini</span></label>
                                            <div class="relative">
                                                <input type="password" id="current_password" name="current_password" class="input input-bordered w-full rounded-xl bg-slate-50 focus:bg-white border-gray-200 transition-all h-14" placeholder="Masukkan password lama" required>
                                                <div id="status-current-pw" class="absolute right-5 top-1/2 -translate-y-1/2 hidden"></div>
                                            </div>
                                            <span id="err-current-pw" class="text-[10px] text-red-500 font-bold mt-2 hidden"></span>
                                        </div>

                                        <div class="divider md:col-span-2 opacity-50 my-2"> Buat password baru </div>

                                        {{-- Password Baru --}}
                                        <div class="form-control w-full">
                                            <label class="label"><span class="label-text font-bold text-gray-700">Password Baru</span></label>
                                            <input type="password" id="new_password" name="new_password" class="input input-bordered w-full rounded-xl bg-slate-50 focus:bg-white border-gray-200 transition-all h-14" placeholder="Min. 8 karakter" required disabled>
                                            <span id="err-new-pw" class="text-[10px] text-red-500 font-bold mt-2 hidden"></span>
                                        </div>

                                        {{-- Konfirmasi Password --}}
                                        <div class="form-control w-full">
                                            <label class="label"><span class="label-text font-bold text-gray-700">Konfirmasi Password Baru</span></label>
                                            <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="input input-bordered w-full rounded-xl bg-slate-50 focus:bg-white border-gray-200 transition-all h-14" placeholder="Ulangi password baru" required disabled>
                                            <span id="err-confirm-pw" class="text-[10px] text-red-500 font-bold mt-2 hidden"></span>
                                        </div>

                                        <div class="md:col-span-2 flex justify-end">
                                            <button type="submit" id="btn-save-password" class="btn bg-blue-400 hover:bg-blue-500 rounded-xl px-8 text-white font-black h-10 border-none normal-case disabled:opacity-50" disabled>
                                                Perbarui Password
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>

    <dialog id="delete_account_modal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-white p-8 rounded-xl text-black">
            <h3 class="font-black text-xl text-center">Hapus Akun ShoeCycle?</h3>
            <p class="py-4 text-sm text-gray-500 text-center">Seluruh data profil akan dinonaktifkan. Data transaksi Anda tetap tersimpan secara anonim untuk keperluan sistem.</p>
            <div class="modal-action flex justify-center gap-3">
                <form method="dialog" class="flex-1">
                    <button class="btn btn-ghost w-full rounded-xl border-gray-100 border-2 normal-case font-bold">Batal</button>
                </form>
                <form action="{{ route('settings.account.delete') }}" method="POST" class="flex-1">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn bg-red-500 hover:bg-red-600 w-full rounded-xl text-white font-black normal-case">Ya, Hapus</button>
                </form>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop bg-black/50 backdrop-blur-sm"><button>close</button></form>
    </dialog>

    {{-- MODAL TAMBAH ALAMAT --}}
    <dialog id="add_address_modal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box w-11/12 rounded-xl max-w-4xl bg-white p-0 h-[650px] flex flex-col overflow-hidden text-black">

            {{-- Header & Stepper --}}
            <div class="bg-white px-8 pt-8 pb-4 border-b border-gray-50">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-black text-xl text-gray-900">Tambah Alamat Baru</h3>
                    <form method="dialog"><button class="btn btn-sm btn-circle btn-ghost">✕</button></form>
                </div>
                <div class="stepper-wrapper">
                    <div class="stepper-item active" id="step-indicator-1">
                        <div class="stepper-circle">1</div>
                        <div class="stepper-title">Mulai</div>
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

            <div class="flex-1 overflow-hidden relative">
                {{-- STEP 1: WELCOME --}}
                <div id="step-content-1" class="p-8 h-full flex flex-col items-center justify-center text-center">
                    <div class="w-20 h-20 bg-blue-50 text-blue-600 rounded-3xl flex items-center justify-center mb-6 shadow-sm">
                        <i class="fas fa-map-marked-alt text-3xl"></i>
                    </div>
                    <h4 class="font-black text-2xl mb-2">Tentukan Lokasi</h4>
                    <p class="text-gray-500 max-w-sm mb-8">Gunakan peta untuk akurasi pengiriman yang lebih baik bagi kurir kami.</p>
                    <button onclick="goToStep(2)" class="btn bg-blue-600 hover:bg-blue-700 border-none text-white px-12 rounded-xl h-14 font-black">Mulai Pinpoint</button>
                </div>

                {{-- STEP 2: PINPOINT DENGAN PENCARIAN --}}
                <div id="step-content-2" class="flex h-full relative flex-col">
                    <div class="absolute top-4 left-4 right-4 z-1000">
                        <div class="relative group">
                            <div class="flex gap-2">
                                <div class="relative flex-1 shadow-xl rounded-xl overflow-hidden border border-blue-200 focus-within:border-blue-500 transition-all bg-white">
                                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-blue-500 z-60"></i>
                                    <input type="text" id="search-address" placeholder="Ketik nama jalan, perumahan, atau gedung..." class="input w-full pl-12 bg-white text-black border-none focus:ring-0 text-sm h-12">
                                </div>
                            </div>
                            <ul id="search-results" class="hidden mt-2 bg-white rounded-xl shadow-2xl border border-gray-100 overflow-hidden max-h-60 overflow-y-auto z-1001 text-black">
                            </ul>
                        </div>
                    </div>

                    <div id="map-container" class="flex-1 bg-slate-100"></div>

                    <div class="absolute top-1/2 left-1/2 z-500 pointer-events-none -translate-x-1/2 -translate-y-10">
                        <div class="relative flex flex-col items-center">
                            <i class="fas fa-map-marker-alt text-red-500 text-4xl drop-shadow-lg"></i>
                            <div class="w-2 h-2 bg-black/30 rounded-full blur-[1px] mt-[-5px]"></div>
                        </div>
                    </div>

                    <div class="bg-white p-4 border-t border-gray-100 shadow-[0_-5px_20px_rgba(0,0,0,0.05)] z-600">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest"><i class="fas fa-map-pin mr-1"></i> Lokasi Terpilih</p>
                                <p id="current-address-text" class="text-xs text-gray-700 font-medium truncate mt-0.5 italic">Geser peta untuk menentukan titik...</p>
                            </div>
                            <button type="button" onclick="goToStep(3)" class="btn btn-primary text-white font-bold rounded-xl px-6 shrink-0 h-12">
                                Pilih Lokasi <i class="fas fa-arrow-right ml-1"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- STEP 3: DETAIL FORM --}}
                <div id="step-content-3" class="h-full flex flex-col">
                    <div class="flex-1 overflow-y-auto p-6 md:px-8">
                        <div class="flex items-center gap-3 mb-6 bg-blue-50 p-4 rounded-xl border border-blue-100">
                            <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-blue-600 shadow-sm shrink-0"><i class="fas fa-check"></i></div>
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

                            @php
                                $hasPrimary = $userAddresses->where('is_primary', true)->first();
                            @endphp

                            <div class="form-control bg-gray-50 p-3 rounded-xl border border-gray-100 {{ $hasPrimary ? 'hidden' : '' }}">
                                <label class="label cursor-pointer justify-start gap-3 p-0">
                                    <input type="checkbox" name="is_primary" value="1" class="checkbox checkbox-primary checkbox-sm rounded" {{ !$hasPrimary ? 'checked' : '' }}>
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
        <form method="dialog" class="modal-backdrop bg-black/50 backdrop-blur-sm"><button>close</button></form>
    </dialog>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        const userAddresses = @json($userAddresses);
        const STORE_LAT = {{ $storeConfig['lat'] }};
        const STORE_LNG = {{ $storeConfig['lng'] }};
        let map = null,
            selectedLat = STORE_LAT,
            selectedLng = STORE_LNG,
            currentStep = 1,
            searchTimeout,
            listSearchTimer;

        let isCurrentPwValid = false;
        let isNewPwValid = false;
        let isEmailAvailable = true;
        const originalAvatar = "https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=256&background=2563eb&color=fff&bold=true";

        // --- LOGIKA TAB DENGAN HASH URL ---
        function switchTab(tab) {
            // 1. Sembunyikan semua konten
            document.querySelectorAll('.tab-pane').forEach(el => el.classList.add('hidden'));

            // 2. Tampilkan target
            const target = document.getElementById('section-' + tab);
            if (target) target.classList.remove('hidden');

            // 3. Update UI Tab Header
            document.querySelectorAll('.tab-item').forEach(btn => {
                btn.classList.remove('border-blue-600', 'text-blue-600', 'font-bold');
                btn.classList.add('border-transparent', 'text-gray-400');
            });

            const activeBtn = document.getElementById('tab-' + tab);
            if (activeBtn) {
                activeBtn.classList.add('border-blue-600', 'text-blue-600', 'font-bold');
                activeBtn.classList.remove('border-transparent', 'text-gray-400');
            }

            // 4. Update URL dengan # tanpa reload
            if (history.pushState) {
                history.pushState(null, null, '#' + tab);
            } else {
                location.hash = '#' + tab;
            }

            // 5. Fix Map Render (Penting untuk Leaflet)
            if (tab === 'alamat' && map) {
                setTimeout(() => {
                    map.invalidateSize();
                }, 300);
            }
        }

        function validateAddressForm() {
            const form = $('#add-address-form');
            const btnSave = $('#btn-save-address');

            // Field wajib diisi
            const fullAddress = form.find('textarea[name="full_address"]').val().trim();
            const district = form.find('input[name="district"]').val().trim();
            const village = form.find('input[name="village"]').val().trim();
            const recipient = form.find('input[name="recipient_name"]').val().trim();
            const phone = form.find('input[name="phone_number"]').val().trim();
            const lat = $('#form-lat').val();

            const isValid = fullAddress !== "" &&
                district !== "" &&
                village !== "" &&
                recipient !== "" &&
                phone !== "" &&
                lat !== "";

            btnSave.prop('disabled', !isValid);

            // Feedback visual sederhana pada tombol
            if (isValid) {
                btnSave.removeClass('opacity-50 bg-blue-400').addClass('bg-blue-600');
            } else {
                btnSave.addClass('opacity-50 bg-blue-400').removeClass('bg-blue-600');
            }
        }

        // Listener untuk input di form alamat
        $(document).on('input', '#add-address-form input, #add-address-form textarea', function() {
            validateAddressForm();
        });

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
                    window.updateAddressInfo(selectedLat, selectedLng);
                }, 400);
            }

            if (step === 3) {
                $('#form-lat').val(selectedLat);
                $('#form-lng').val(selectedLng);
                validateAddressForm();
            }

            if (step > 1) $('#btn-back-step').removeClass('invisible');
            else $('#btn-back-step').addClass('invisible');
        };

        window.prevStep = function() {
            if (currentStep > 1) window.goToStep(currentStep - 1);
        };

        $(document).ready(function() {
            // --- LOGIKA INITIAL LOAD (REFRESH) ---
            const currentHash = window.location.hash.substring(1); // Ambil teks setelah #
            const validTabs = ['biodata', 'alamat', 'keamanan'];

            // CEK APAKAH ADA TOAST YANG TERTUNDA DARI SESSION STORAGE
            const toastStatus = sessionStorage.getItem('flash_toast_status');
            const toastMessage = sessionStorage.getItem('flash_toast_message');

            if (toastStatus && toastMessage) {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top', // Top Center
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.style.marginTop = '20px';
                    }
                });

                Toast.fire({
                    icon: toastStatus,
                    title: toastMessage
                });

                // SANGAT PENTING: Hapus tanda setelah ditampilkan agar tidak muncul lagi saat refresh manual
                sessionStorage.removeItem('flash_toast_status');
                sessionStorage.removeItem('flash_toast_message');
            }

            if (validTabs.includes(currentHash)) {
                switchTab(currentHash);
            } else {
                switchTab('biodata'); // Default jika tidak ada hash
            }

            // --- 2. LOGIKA PENCARIAN DAFTAR ALAMAT (DEBOUNCE) ---
            $('#search-address-list').on('input', function() {
                const query = $(this).val().toLowerCase();

                clearTimeout(listSearchTimer);
                listSearchTimer = setTimeout(() => {
                    $('.address-card').each(function() {
                        const content = $(this).text().toLowerCase();
                        if (content.includes(query)) {
                            $(this).fadeIn(200);
                        } else {
                            $(this).fadeOut(200);
                        }
                    });
                }, 300); // Tunggu 300ms setelah user berhenti mengetik
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('.group').length) $('#search-results').addClass('hidden');
            });
        });

        window.selectSearchResult = function(lat, lng, name) {
            selectedLat = lat;
            selectedLng = lng;
            $('#search-address').val(name);
            $('#search-results').addClass('hidden');
            if (map) map.setView([lat, lng], 18);
            updateAddressInfo(lat, lng);
        };

        window.updateAddressInfo = function(lat, lng) {
            $('#current-address-text').text('Mencari alamat...');
            $('#form-lat').val(lat);
            $('#form-lng').val(lng);
            $('#current-address-text').text(`Koordinat: ${lat.toFixed(6)}, ${lng.toFixed(6)}`);
            if (typeof validate === 'function') validate();
        };

        window.initMap = function() {
            if (map) {
                map.invalidateSize();
                return;
            }
            map = L.map('map-container', {
                zoomControl: false
            }).setView([selectedLat, selectedLng], 16);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            map.on('moveend', function() {
                let center = map.getCenter();
                selectedLat = center.lat;
                selectedLng = center.lng;
                updateAddressInfo(selectedLat, selectedLng);
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
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                success: function() {
                    window.location.reload();
                },
                error: function(xhr) {
                    let msg = 'Gagal menyimpan alamat.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    alert(msg);
                    $btn.prop('disabled', false).text('Simpan & Gunakan');
                    validateAddressForm();
                }
            });
        };

        function validateBiodataChanges() {
            const nameInput = document.getElementById('input-name');
            const emailInput = document.getElementById('input-email');
            const fileInput = document.getElementById('profile-input');
            const saveBtn = document.getElementById('btn-save-biodata');

            const isNameChanged = nameInput.value.trim() !== nameInput.getAttribute('data-original');
            const isEmailChanged = emailInput.value.trim() !== emailInput.getAttribute('data-original');
            const isFileChanged = fileInput.files.length > 0;

            const canSubmit = (isNameChanged || isEmailChanged || isFileChanged) &&
                isEmailAvailable &&
                nameInput.value.trim() !== "" &&
                emailInput.value.trim() !== "";

            saveBtn.disabled = !canSubmit;

            if (canSubmit) {
                saveBtn.classList.remove('bg-blue-400');
                saveBtn.classList.add('bg-blue-600');
            } else {
                saveBtn.classList.add('bg-blue-400');
                saveBtn.classList.remove('bg-blue-600');
            }
        }

        let emailDebounce;
        document.getElementById('input-email').addEventListener('input', function() {
            const email = this.value.trim();
            const original = this.getAttribute('data-original');
            const errorMsg = document.getElementById('email-error-msg');

            clearTimeout(emailDebounce);

            if (email === original) {
                isEmailAvailable = true;
                errorMsg.classList.add('hidden');
                validateBiodataChanges();
                return;
            }

            if (email === "") {
                isEmailAvailable = false;
                validateBiodataChanges();
                return;
            }

            emailDebounce = setTimeout(() => {
                fetch("{{ route('settings.check-email') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            email: email
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.exists) {
                            isEmailAvailable = false;
                            errorMsg.textContent = "Email ini sudah terdaftar di akun lain.";
                            errorMsg.classList.remove('hidden');
                        } else {
                            isEmailAvailable = true;
                            errorMsg.classList.add('hidden');
                        }
                        validateBiodataChanges();
                    })
                    .catch(() => {
                        console.error("Gagal mengecek email");
                    });
            }, 500);
        });

        function handleImageSelect(input) {
            const file = input.files[0];
            const preview = document.getElementById('image-preview');
            const btnRemove = document.getElementById('btn-remove-image');
            const infoDiv = document.getElementById('file-info');
            const instruct = document.getElementById('file-instruction');

            if (file) {
                if (file.size > 102400) {
                    Swal.fire({
                        icon: 'error',
                        title: 'File Terlalu Besar',
                        text: 'Maksimum ukuran gambar profil adalah 100KB.',
                        confirmButtonColor: '#2563eb'
                    });
                    input.value = "";
                    return;
                }

                const reader = new FileReader();
                reader.onload = (e) => {
                    preview.src = e.target.result;
                    btnRemove.classList.remove('hidden');
                    btnRemove.classList.add('group-hover:opacity-100');
                    infoDiv.classList.remove('hidden');
                    instruct.classList.add('hidden');
                    document.getElementById('file-name').textContent = file.name;
                    document.getElementById('file-size').textContent = (file.size / 1024).toFixed(1) + ' KB';
                    validateBiodataChanges();
                };
                reader.readAsDataURL(file);
            }
        }

        /**
         * FUNGSI JADIKAN ALAMAT UTAMA (AJAX)
         */
        function setPrimaryAddress(id) {
            $.ajax({
                url: `/address/set-primary/${id}`,
                type: 'PUT',
                data: {
                    _token: csrfToken
                },
                success: function(res) {
                    if (res.status === 'success') {
                        // 1. Simpan tanda dan pesan ke sessionStorage sebelum refresh
                        sessionStorage.setItem('flash_toast_status', 'success');
                        sessionStorage.setItem('flash_toast_message', res.message);

                        // 2. Lakukan Refresh
                        window.location.reload();
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Opps...',
                        text: xhr.responseJSON ? xhr.responseJSON.message : 'Gagal mengubah alamat utama.',
                        confirmButtonColor: '#2563eb'
                    });
                }
            });
        }

        function removeImagePreview() {
            const input = document.getElementById('profile-input');
            const btnRemove = document.getElementById('btn-remove-image');
            input.value = "";
            document.getElementById('image-preview').src = databaseAvatar;
            btnRemove.classList.add('hidden');
            document.getElementById('file-info').classList.add('hidden');
            document.getElementById('file-instruction').classList.remove('hidden');
            validateBiodataChanges();
        }

        function validateSecurityForm() {
            const newPw = $('#new_password').val();
            const confirmPw = $('#new_password_confirmation').val();
            const currentPw = $('#current_password').val();
            const btnSave = $('#btn-save-password');

            if (newPw !== "" && newPw === currentPw) {
                $('#err-new-pw').text("Password baru tidak boleh sama dengan password lama!").removeClass('hidden');
                isNewPwValid = false;
            } else if (newPw !== "" && newPw.length < 8) {
                $('#err-new-pw').text("Password minimal 8 karakter.").removeClass('hidden');
                isNewPwValid = false;
            } else {
                $('#err-new-pw').addClass('hidden');
                isNewPwValid = (newPw !== "");
            }

            const isConfirmValid = (confirmPw === newPw && confirmPw !== "");
            if (confirmPw !== "" && confirmPw !== newPw) {
                $('#err-confirm-pw').text("Konfirmasi password tidak cocok.").removeClass('hidden');
            } else {
                $('#err-confirm-pw').addClass('hidden');
            }

            if (isCurrentPwValid && isNewPwValid && isConfirmValid && newPw !== currentPw) {
                btnSave.prop('disabled', false);
            } else {
                btnSave.prop('disabled', true);
            }
        }

        $('#current_password').on('input', function() {
            const val = $(this).val();
            const statusIcon = $('#status-current-pw');
            const errSpan = $('#err-current-pw');

            clearTimeout(verifyTimer);
            if (val.length < 1) {
                statusIcon.addClass('hidden');
                $('#new_password, #new_password_confirmation').prop('disabled', true);
                return;
            }

            verifyTimer = setTimeout(() => {
                $.ajax({
                    url: "{{ route('settings.verify-password') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        current_password: val
                    },
                    success: function(res) {
                        if (res.valid) {
                            isCurrentPwValid = true;
                            statusIcon.html('<i class="fas fa-check-circle text-green-500"></i>').removeClass('hidden');
                            errSpan.addClass('hidden');
                            $('#new_password, #new_password_confirmation').prop('disabled', false);
                        } else {
                            isCurrentPwValid = false;
                            statusIcon.html('<i class="fas fa-times-circle text-red-500"></i>').removeClass('hidden');
                            errSpan.text("Password saat ini salah.").removeClass('hidden');
                            $('#new_password, #new_password_confirmation').prop('disabled', true);
                        }
                        validateSecurityForm();
                    }
                });
            }, 600);
        });

        $('#new_password, #new_password_confirmation').on('input', validateSecurityForm);
    </script>
@endpush
