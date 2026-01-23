@extends('layouts/frontend/index')
@section('title', 'Pengaturan Akun | ShoeCycle')

@section('frontend-content')
    <div class="bg-[#f0f3f7] min-h-screen py-12">
        <div class="container mx-auto px-4 max-w-7xl">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                {{-- SIDEBAR KIRI --}}
                <aside class="lg:col-span-3 space-y-5 sticky top-24">
                    <div class="bg-white rounded-3xl border border-gray-200 p-6 shadow-sm">
                        <div class="flex items-center gap-4">
                            <div class="avatar">
                                <div class="w-14 rounded-2xl ring ring-blue-50 ring-offset-2">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=2563eb&color=fff&bold=true" />
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
                                <span class="text-gray-500 flex items-center gap-2"><i class="fa-solid fa-wallet text-blue-500"></i> ShoePay</span>
                                <span class="font-black text-gray-900">Rp 0</span>
                            </div>
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-gray-500 flex items-center gap-2"><i class="fa-solid fa-coins text-amber-500"></i> ShoePoints</span>
                                <span class="font-black text-gray-900">1.250</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl border border-gray-200 overflow-hidden shadow-sm text-black">
                        <ul class="menu p-2 font-medium">
                            <li class="menu-title text-[10px] uppercase text-gray-400 py-4 px-5 tracking-[0.2em]">Navigasi</li>
                            <li><a href="{{ route('my-order.index') }}" class="py-3 rounded-2xl"><i class="fas fa-shopping-bag w-5 text-gray-400"></i> Pembelian</a></li>
                            <li><a href="#" class="py-3 rounded-2xl"><i class="fas fa-heart w-5 text-gray-400"></i> Wishlist</a></li>
                            <li><button onclick="switchTab('biodata')" class="active bg-blue-50 text-blue-600 py-3 rounded-2xl font-bold"><i class="fas fa-user-cog w-5"></i> Pengaturan</button></li>
                        </ul>
                    </div>
                </aside>

                {{-- CONTENT KANAN --}}
                <main class="lg:col-span-9">
                    <div class="bg-white rounded-[2rem] border border-gray-200 shadow-sm min-h-[700px] flex flex-col overflow-hidden">

                        {{-- Tab Header --}}
                        <div class="flex border-b border-gray-100 px-8 bg-white overflow-x-auto no-scrollbar">
                            <button onclick="switchTab('biodata')" class="tab-item py-6 px-6 text-sm font-bold border-b-4 border-blue-600 text-blue-600 whitespace-nowrap transition-all duration-300" id="tab-biodata">Biodata Diri</button>
                            <button onclick="switchTab('alamat')" class="tab-item py-6 px-6 text-sm font-medium border-b-4 border-transparent text-gray-400 whitespace-nowrap transition-all duration-300" id="tab-alamat">Daftar Alamat</button>
                            <button onclick="switchTab('keamanan')" class="tab-item py-6 px-6 text-sm font-medium border-b-4 border-transparent text-gray-400 whitespace-nowrap transition-all duration-300" id="tab-keamanan">Keamanan</button>
                        </div>

                        <div class="p-8 md:p-12 flex-1">

                            {{-- 1. SECTION: BIODATA DIRI (EDITABLE) --}}
                            <div id="section-biodata" class="tab-pane space-y-12 animate-in fade-in slide-in-from-bottom-2 duration-500">
                                <form action="{{ route('settings.profile.update') }}" method="POST" class="grid grid-cols-1 md:grid-cols-12 gap-12">
                                    @csrf @method('PUT')
                                    {{-- Foto Profil --}}
                                    <div class="md:col-span-4 flex flex-col items-center">
                                        <div class="relative group">
                                            <div class="w-52 h-52 rounded-[2.5rem] overflow-hidden border-[6px] border-slate-100 shadow-xl transition-all duration-500 group-hover:shadow-blue-100">
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=256&background=2563eb&color=fff&bold=true" class="w-full h-full object-cover">
                                            </div>
                                            <label class="absolute bottom-4 right-4 w-12 h-12 bg-white rounded-2xl shadow-xl flex items-center justify-center cursor-pointer hover:bg-blue-600 hover:text-white transition-all text-blue-600">
                                                <i class="fas fa-camera text-lg"></i>
                                                <input type="file" class="hidden">
                                            </label>
                                        </div>
                                        <p class="text-[10px] text-gray-400 mt-6 text-center leading-relaxed font-medium uppercase tracking-widest">JPG, PNG, atau JPEG. <br>Maks. 5MB.</p>
                                    </div>

                                    {{-- Inputs --}}
                                    <div class="md:col-span-8 space-y-8 text-black">
                                        <div class="space-y-6">
                                            <h4 class="font-black text-gray-900 text-lg">Ubah Data Diri</h4>

                                            <div class="form-control w-full">
                                                <label class="label"><span class="label-text font-bold text-gray-700">Nama Lengkap</span></label>
                                                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="input input-bordered w-full rounded-2xl bg-slate-50 focus:bg-white border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 transition-all" placeholder="Masukkan nama lengkap">
                                            </div>

                                            <div class="form-control w-full">
                                                <label class="label"><span class="label-text font-bold text-gray-700">Alamat Email</span></label>
                                                <div class="relative">
                                                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="input input-bordered w-full rounded-2xl bg-slate-50 focus:bg-white border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 transition-all pl-12" placeholder="nama@email.com">
                                                    <i class="fas fa-envelope absolute left-5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                                </div>
                                            </div>

                                            <button type="submit" class="btn btn-primary rounded-2xl px-10 text-white font-bold shadow-lg shadow-blue-500/20 normal-case h-12">Simpan Perubahan</button>
                                        </div>

                                        <div class="divider opacity-50"></div>

                                        {{-- Danger Zone --}}
                                        <div class="bg-red-50 p-6 rounded-[2rem] border border-red-100 flex items-center justify-between">
                                            <div class="pr-4">
                                                <h5 class="font-bold text-red-700 text-sm">Hapus Akun</h5>
                                                <p class="text-[11px] text-red-600 mt-1 font-medium">Akun akan dinonaktifkan permanen (Soft Delete).</p>
                                            </div>
                                            <button type="button" onclick="confirmDeleteAccount()" class="btn btn-error btn-sm rounded-xl text-white font-bold px-6 border-none">Hapus</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            {{-- 2. SECTION: ALAMAT (TOKOPEDIA EXACT STYLE) --}}
                            <div id="section-alamat" class="tab-pane hidden space-y-8 animate-in fade-in slide-in-from-bottom-2 duration-500">
                                {{-- Top Bar Alamat --}}
                                <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                                    <div class="relative w-full max-w-lg">
                                        <input type="text" placeholder="Tulis Nama Alamat / Kota / Kecamatan tujuan pengiriman" class="input input-bordered w-full pl-12 rounded-2xl h-12 text-sm bg-white border-gray-200 focus:border-[#03ac0e] focus:ring-4 focus:ring-green-500/5 transition-all">
                                        <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-gray-300 text-lg"></i>
                                    </div>
                                    <button class="btn bg-[#03ac0e] hover:bg-[#028b0b] border-none text-white rounded-2xl h-12 px-8 font-black normal-case shadow-md shadow-green-500/20">+ Tambah Alamat</button>
                                </div>

                                {{-- Address Cards --}}
                                <div class="grid grid-cols-1 gap-5">
                                    @forelse ($addresses as $addr)
                                        <div class="p-8 rounded-[2rem] border-2 transition-all relative {{ $addr->is_primary ? 'border-[#03ac0e] bg-[#f7fffa]' : 'border-gray-100 bg-white hover:border-gray-200' }}">

                                            <div class="flex items-center gap-3 mb-4">
                                                <span class="text-[11px] font-black uppercase text-gray-900 tracking-wider">{{ $addr->label }}</span>
                                                @if ($addr->is_primary)
                                                    <span class="text-[10px] font-black uppercase bg-[#e5f7e6] text-[#03ac0e] px-2 py-1 rounded-md">Utama</span>
                                                @endif
                                            </div>

                                            <div class="flex justify-between items-start">
                                                <div class="space-y-1.5">
                                                    <h5 class="font-black text-gray-900 text-lg">{{ $addr->recipient_name }}</h5>
                                                    <p class="text-sm text-gray-900 font-bold tracking-tight">{{ $addr->phone_number }}</p>
                                                    <p class="text-sm text-gray-600 leading-relaxed max-w-2xl font-medium">
                                                        {{ $addr->full_address }}, {{ $addr->village }}, {{ $addr->district }}, Kabupaten Mojokerto, 61371
                                                    </p>
                                                    <p class="text-[11px] text-[#03ac0e] font-bold mt-2"><i class="fas fa-map-marker-alt"></i> Sudah Pinpoint</p>
                                                </div>
                                                @if ($addr->is_primary)
                                                    <div class="text-[#03ac0e] bg-white w-10 h-10 rounded-full flex items-center justify-center shadow-sm border border-green-100">
                                                        <i class="fas fa-check text-xl"></i>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="mt-6 flex items-center gap-6 border-t border-gray-100 pt-5">
                                                <button class="text-[#03ac0e] font-black text-sm hover:opacity-70 transition-opacity">Ubah Alamat</button>
                                                <div class="w-[1.5px] h-3 bg-gray-200"></div>
                                                <button class="text-[#03ac0e] font-black text-sm hover:opacity-70 transition-opacity">Share</button>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-24 bg-slate-50 rounded-[2.5rem] border-2 border-dashed border-gray-200">
                                            <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm">
                                                <i class="fas fa-map-location-dot text-4xl text-gray-200"></i>
                                            </div>
                                            <p class="text-gray-500 font-bold text-lg">Alamat belum tersedia</p>
                                            <p class="text-gray-400 text-sm mt-1">Tambahkan alamat untuk mempermudah pengiriman pesananmu.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            {{-- 3. SECTION: KEAMANAN --}}
                            <div id="section-keamanan" class="tab-pane hidden space-y-10 animate-in fade-in slide-in-from-bottom-2 duration-500">
                                <div class="max-w-md">
                                    <div class="mb-10">
                                        <h4 class="font-black text-gray-900 text-xl flex items-center gap-3">
                                            <i class="fas fa-shield-halved text-blue-600"></i> Ganti Password
                                        </h4>
                                        <p class="text-sm text-gray-500 mt-2">Demi keamanan akun, jangan bagikan password Anda kepada siapa pun.</p>
                                    </div>

                                    <form action="{{ route('settings.password.update') }}" method="POST" class="space-y-6">
                                        @csrf @method('PUT')
                                        <div class="form-control">
                                            <label class="label"><span class="label-text font-bold text-gray-700">Password Saat Ini</span></label>
                                            <input type="password" name="current_password" class="input input-bordered w-full rounded-2xl bg-slate-50 focus:bg-white border-gray-200 focus:border-blue-500 transition-all h-12" placeholder="••••••••" required>
                                        </div>

                                        <div class="grid grid-cols-1 gap-6">
                                            <div class="form-control">
                                                <label class="label"><span class="label-text font-bold text-gray-700">Password Baru</span></label>
                                                <input type="password" name="new_password" class="input input-bordered w-full rounded-2xl bg-slate-50 focus:bg-white border-gray-200 focus:border-blue-500 transition-all h-12" placeholder="Min. 8 karakter" required>
                                            </div>
                                            <div class="form-control">
                                                <label class="label"><span class="label-text font-bold text-gray-700">Ulangi Password Baru</span></label>
                                                <input type="password" name="new_password_confirmation" class="input input-bordered w-full rounded-2xl bg-slate-50 focus:bg-white border-gray-200 focus:border-blue-500 transition-all h-12" placeholder="Ulangi password" required>
                                            </div>
                                        </div>

                                        <button type="submit" class="btn btn-primary w-full rounded-2xl text-white font-black h-12 shadow-xl shadow-blue-500/20 mt-4 normal-case">Simpan Password Baru</button>
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>

    {{-- Hidden Form Delete --}}
    <form id="delete-account-form" action="{{ route('settings.account.delete') }}" method="POST" class="hidden">
        @csrf @method('DELETE')
    </form>
@endsection

@push('scripts')
    <script>
        function switchTab(tab) {
            // 1. Sembunyikan semua tab-pane
            document.querySelectorAll('.tab-pane').forEach(el => el.classList.add('hidden'));

            // 2. Tampilkan yang diklik
            const target = document.getElementById('section-' + tab);
            if (target) target.classList.remove('hidden');

            // 3. Reset Header Tab Visual
            document.querySelectorAll('.tab-item').forEach(btn => {
                btn.classList.remove('border-blue-600', 'text-blue-600', 'font-bold');
                btn.classList.add('border-transparent', 'text-gray-400', 'font-medium');
            });

            // 4. Set Aktif Visual
            const activeBtn = document.getElementById('tab-' + tab);
            if (activeBtn) {
                activeBtn.classList.add('border-blue-600', 'text-blue-600', 'font-bold');
                activeBtn.classList.remove('border-transparent', 'text-gray-400', 'font-medium');
            }
        }

        function confirmDeleteAccount() {
            Swal.fire({
                title: 'Hapus Akun Anda?',
                text: "Seluruh data profil akan dinonaktifkan. Data transaksi Anda tetap aman di sistem kami.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus Akun',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-[2.5rem]',
                    confirmButton: 'rounded-2xl px-10',
                    cancelButton: 'rounded-2xl px-10'
                }
            }).then((result) => {
                if (result.isConfirmed) document.getElementById('delete-account-form').submit();
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            // Cek jika ada session sukses dari Controller
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    timer: 2500,
                    showConfirmButton: false,
                    customClass: {
                        popup: 'rounded-[2rem]'
                    }
                });
            @endif

            // Jalankan default tab
            switchTab('biodata');
        });
    </script>
@endpush
