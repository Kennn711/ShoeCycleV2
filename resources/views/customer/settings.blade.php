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
                                <div class="w-14 rounded-4xl ring ring-blue-50 ring-offset-2">
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
                                <a href="#" class="py-3.5 px-4 rounded-2xl hover:bg-slate-50 transition-all duration-200 flex items-center gap-3 w-full">
                                    <i class="fas fa-heart w-5 text-gray-400"></i>
                                    <span class="text-sm">Wishlist</span>
                                </a>
                            </li>

                            <li class="px-2 mt-1">
                                <button onclick="switchTab('biodata')" class="active bg-blue-50 text-blue-600 py-3.5 px-4 rounded-2xl font-bold flex items-center gap-3 w-full transition-all duration-200">
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
                                <form action="{{ route('settings.profile.update') }}" method="POST" class="grid grid-cols-1 md:grid-cols-12 gap-12">
                                    @csrf @method('PUT')
                                    {{-- Foto Profil --}}
                                    <div class="md:col-span-4 flex flex-col items-center">
                                        <div class="relative group">
                                            {{-- Container Gambar --}}
                                            <div class="w-52 h-52 rounded-[2.5rem] overflow-hidden border-[6px] border-slate-100 shadow-xl bg-slate-50 relative">
                                                <img id="image-preview" src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=256&background=2563eb&color=fff&bold=true" class="w-full h-full object-cover transition-opacity duration-300">

                                                {{-- Overlay Tombol Trash (Muncul saat hover & hanya jika ada file dipilih) --}}
                                                <button type="button" id="btn-remove-image" onclick="removeImagePreview()" class="hidden absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300">
                                                    <div class="w-12 h-12 bg-red-500 text-white rounded-2xl flex items-center justify-center shadow-lg transform hover:scale-110 active:scale-95">
                                                        <i class="fas fa-trash-can text-xl"></i>
                                                    </div>
                                                </button>
                                            </div>

                                            {{-- Tombol Kamera (Input Trigger) --}}
                                            <label for="profile-input" class="absolute -bottom-2 -right-2 w-14 h-14 bg-blue-600 text-white rounded-2xl shadow-2xl flex items-center justify-center cursor-pointer hover:bg-blue-700 hover:scale-110 transition-all z-10 border-4 border-white">
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
                                                <input type="text" id="input-name" name="name" value="{{ $user->name }}" data-original="{{ $user->name }}" class="input input-bordered w-full rounded-2xl bg-slate-50 focus:bg-white border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 transition-all">
                                            </div>

                                            <div class="form-control w-full">
                                                <label class="label"><span class="label-text font-bold text-gray-700">Alamat Email</span></label>
                                                <div class="relative">
                                                    <input type="email" id="input-email" name="email" value="{{ $user->email }}" data-original="{{ $user->email }}" class="input input-bordered w-full rounded-2xl bg-slate-50 focus:bg-white border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 transition-all">
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
                                        <div class="p-8 rounded-4xl border-2 transition-all relative {{ $addr->is_primary ? 'border-[#03ac0e] bg-[#f7fffa]' : 'border-gray-100 bg-white hover:border-gray-200' }}">

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

    {{-- MODAL DAISYUI (Tambahkan sebelum @endsection) --}}
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
@endsection

@push('scripts')
    <script>
        // Inisialisasi State
        let isEmailAvailable = true;
        const originalAvatar = "https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=256&background=2563eb&color=fff&bold=true";

        /**
         * 1. LOGIKA UTAMA: ON/OFF Tombol Simpan
         * Memeriksa apakah ada perubahan pada Nama, Email, atau Foto
         */
        function validateBiodataChanges() {
            const nameInput = document.getElementById('input-name');
            const emailInput = document.getElementById('input-email');
            const fileInput = document.getElementById('profile-input');
            const saveBtn = document.getElementById('btn-save-biodata');

            // Cek perubahan teks
            const isNameChanged = nameInput.value.trim() !== nameInput.getAttribute('data-original');
            const isEmailChanged = emailInput.value.trim() !== emailInput.getAttribute('data-original');

            // Cek apakah ada file yang dipilih
            const isFileChanged = fileInput.files.length > 0;

            // Validasi Akhir: Harus ada perubahan DAN email harus valid/tersedia DAN input tidak boleh kosong
            const canSubmit = (isNameChanged || isEmailChanged || isFileChanged) &&
                isEmailAvailable &&
                nameInput.value.trim() !== "" &&
                emailInput.value.trim() !== "";

            saveBtn.disabled = !canSubmit;

            // Perubahan visual tombol agar lebih interaktif
            if (canSubmit) {
                saveBtn.classList.remove('bg-blue-400');
                saveBtn.classList.add('bg-blue-600');
            } else {
                saveBtn.classList.add('bg-blue-400');
                saveBtn.classList.remove('bg-blue-600');
            }
        }

        /**
         * 2. LISTENER EMAIL: Live Validation via AJAX
         */
        let emailDebounce;
        document.getElementById('input-email').addEventListener('input', function() {
            const email = this.value.trim();
            const original = this.getAttribute('data-original');
            const errorMsg = document.getElementById('email-error-msg');

            clearTimeout(emailDebounce);

            // Jika email dikembalikan ke awal, anggap tersedia
            if (email === original) {
                isEmailAvailable = true;
                errorMsg.classList.add('hidden');
                validateBiodataChanges();
                return;
            }

            // Jangan cek jika kosong
            if (email === "") {
                isEmailAvailable = false;
                validateBiodataChanges();
                return;
            }

            // Beri jeda 500ms sebelum tembak ke server agar ringan
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

        /**
         * 3. LOGIKA GAMBAR: Preview & Validation Size (Max 100KB)
         */
        function handleImageSelect(input) {
            const file = input.files[0];
            const preview = document.getElementById('image-preview');
            const btnRemove = document.getElementById('btn-remove-image');
            const infoDiv = document.getElementById('file-info');
            const instruct = document.getElementById('file-instruction');

            if (file) {
                // Validasi Ukuran: 100KB = 102400 Bytes
                if (file.size > 102400) {
                    Swal.fire({
                        icon: 'error',
                        title: 'File Terlalu Besar',
                        text: 'Maksimum ukuran gambar profil adalah 100KB.',
                        confirmButtonColor: '#2563eb'
                    });
                    input.value = ""; // Reset input file
                    return;
                }

                // Jalankan Preview
                const reader = new FileReader();
                reader.onload = (e) => {
                    preview.src = e.target.result;

                    // Tampilkan elemen info & tombol hapus
                    btnRemove.classList.remove('hidden');
                    infoDiv.classList.remove('hidden');
                    instruct.classList.add('hidden');

                    // Isi Metadata
                    document.getElementById('file-name').textContent = file.name;
                    document.getElementById('file-size').textContent = (file.size / 1024).toFixed(1) + ' KB';

                    validateBiodataChanges();
                };
                reader.readAsDataURL(file);
            }
        }

        /**
         * 4. RESET GAMBAR: Fungsi tombol Trash (X)
         */
        function removeImagePreview() {
            const input = document.getElementById('profile-input');
            input.value = ""; // Kosongkan input

            document.getElementById('image-preview').src = originalAvatar;
            document.getElementById('btn-remove-image').classList.add('hidden');
            document.getElementById('file-info').classList.add('hidden');
            document.getElementById('file-instruction').classList.remove('hidden');

            validateBiodataChanges();
        }

        /**
         * 5. FUNGSI DASAR: Switch Tab & Global Listeners
         */
        function switchTab(tab) {
            // Sembunyikan semua konten
            document.querySelectorAll('.tab-pane').forEach(el => el.classList.add('hidden'));
            // Tampilkan target
            const target = document.getElementById('section-' + tab);
            if (target) target.classList.remove('hidden');

            // Update UI Tab Header
            document.querySelectorAll('.tab-item').forEach(btn => {
                btn.classList.remove('border-blue-600', 'text-blue-600', 'font-bold');
                btn.classList.add('border-transparent', 'text-gray-400');
            });
            document.getElementById('tab-' + tab).classList.add('border-blue-600', 'text-blue-600', 'font-bold');
            document.getElementById('tab-' + tab).classList.remove('border-transparent', 'text-gray-400');
        }

        // Listener untuk input Nama
        document.getElementById('input-name').addEventListener('input', validateBiodataChanges);

        document.addEventListener('DOMContentLoaded', () => {
            // Buka tab biodata secara default
            switchTab('biodata');

            // Notifikasi Sukses dari Laravel Session
            @if (session('success'))
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top',
                    showConfirmButton: false,
                    timer: 1000,
                    timerProgressBar: true,
                });
                Toast.fire({
                    icon: 'success',
                    title: "{{ session('success') }}"
                });
            @endif
        });
    </script>
@endpush
