@extends('layouts/backend/index')
@section('title', 'ShoeCycle | Driver')
@section('breadcrumb', 'Tabel > Driver')

@section('backend-content')
    <div class="space-y-6">
        {{-- Header & Search --}}
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <h2 class="text-2xl font-bold text-gray-900">Data Driver</h2>
            <div class="flex gap-3 w-full md:w-auto">
                <form action="{{ route('driver.index') }}" method="GET" class="flex-1 md:flex-none">
                    <label class="input input-bordered input-md flex items-center gap-2 bg-white w-full md:w-64">
                        <i class="fas fa-search text-gray-400"></i>
                        <input type="text" name="q" class="grow text-black min-w-0" placeholder="Cari driver..." value="{{ request('q') }}" />
                    </label>
                </form>
                <button class="btn bg-blue-500 hover:bg-blue-600 text-white border-none" onclick="window.openCreateDriverModal()">
                    <i class="fas fa-user-plus mr-2"></i> Tambah
                </button>
            </div>
        </div>

        {{-- Content: CARD LAYOUT --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse ($drivers as $driver)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow relative group text-center">
                    {{-- Avatar --}}
                    <div class="relative mx-auto w-24 h-24 mb-4">
                        <div class="rounded-full overflow-hidden w-full h-full border-4 border-blue-50">
                            @if ($driver->profile_picture)
                                <img src="{{ asset('storage/' . $driver->profile_picture) }}" alt="{{ $driver->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-400">
                                    <i class="fas fa-user text-3xl"></i>
                                </div>
                            @endif
                        </div>
                        <div class="absolute bottom-0 right-0 bg-blue-500 text-white text-xs px-2 py-1 rounded-full border-2 border-white">
                            Driver
                        </div>
                    </div>

                    {{-- Info --}}
                    <h3 class="text-lg font-bold text-gray-900 mb-1 truncate" title="{{ $driver->name }}">{{ $driver->name }}</h3>
                    <p class="text-sm text-gray-500 mb-4 truncate" title="{{ $driver->email }}">{{ $driver->email }}</p>

                    <div class="divider my-2"></div>

                    {{-- Stats --}}
                    <div class="flex justify-center gap-4 text-xs text-gray-500 mb-4">
                        <div class="text-center">
                            <span class="block font-bold text-gray-800 text-sm">{{ $driver->created_at->format('d M Y') }}</span>
                            <span>Bergabung</span>
                        </div>
                    </div>

                    {{-- Action Buttons (Konsisten dengan Category) --}}
                    <div class="flex justify-center gap-2">
                        {{-- Tombol Edit (Kuning) --}}
                        <button class="px-3 py-2 text-sm bg-yellow-100 text-yellow-600 rounded-lg hover:bg-yellow-200 transition-colors flex items-center justify-center w-10" onclick="window.openEditDriverModal({{ $driver->id }})">
                            <i class="fa-solid fa-pen-to-square text-xl"></i>
                        </button>

                        {{-- Tombol Hapus (Merah) --}}
                        <button class="px-3 py-2 text-sm bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-colors flex items-center justify-center w-10" onclick="window.openDeleteDriverModal({{ $driver->id }}, '{{ $driver->name }}', '{{ $driver->email }}')">
                            <i class="fa-solid fa-trash-can text-xl"></i>
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center text-gray-500">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users-slash text-4xl text-gray-300"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Belum Ada Driver</h3>
                    <p class="text-xs">Silakan tambahkan driver baru.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $drivers->links() }}
        </div>
    </div>

    {{-- MODAL CREATE DRIVER (Sama seperti sebelumnya) --}}
    <dialog id="modal_create_driver" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-white max-w-2xl overflow-hidden p-0 flex flex-col">
            <div class="sticky top-0 bg-white z-10 p-4 border-b border-gray-200 flex justify-between items-start">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-plus text-2xl text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Tambah Driver Baru</h3>
                        <p class="text-sm text-gray-500">Isi informasi akun driver</p>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-circle btn-ghost btn-close-modal" onclick="window.closeCreateDriverModal()">✕</button>
            </div>
            <div class="overflow-y-auto p-6">
                <form id="form-create-driver" action="{{ route('driver.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    <div class="flex flex-col items-center mb-4">
                        <div class="relative w-32 h-32 mb-3 group cursor-pointer" onclick="document.getElementById('input-foto').click()">
                            <div class="w-full h-full rounded-full overflow-hidden border-4 border-gray-100 bg-gray-50 flex items-center justify-center" id="preview-container">
                                <i id="icon-camera" class="fas fa-camera text-3xl text-gray-300 absolute"></i>
                                <img id="img-preview" src="#" class="w-full h-full object-cover hidden z-10 relative">
                            </div>
                            <div class="absolute inset-0 bg-black bg-opacity-40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-35 transition-opacity z-20">
                                <span class="text-white text-xs font-medium">Ubah Foto</span>
                            </div>
                        </div>
                        <span class="text-xs text-gray-500">Klik untuk upload foto (Max 2MB) <span class="text-red-500">*</span></span>
                        <input type="file" name="profile_picture" id="input-foto" class="hidden" accept="image/*">
                        <span id="error-profile_picture" class="text-red-500 text-xs mt-1 hidden text-center"></span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-control">
                            <label class="label"><span class="label-text font-medium">Nama Lengkap <span class="text-red-500">*</span></span></label>
                            <input type="text" name="name" id="input-name" class="input input-bordered bg-white" placeholder="Nama Driver" required>
                            <span id="error-name" class="text-red-500 text-xs mt-1 hidden"></span>
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text font-medium">Email <span class="text-red-500">*</span></span></label>
                            <input type="email" name="email" id="input-email" class="input input-bordered bg-white" placeholder="email@contoh.com" required>
                            <span id="error-email" class="text-red-500 text-xs mt-1 hidden"></span>
                        </div>
                        <div class="form-control col-span-1 md:col-span-2">
                            <label class="label"><span class="label-text font-medium">Password <span class="text-red-500">*</span></span></label>
                            <input type="password" name="password" id="input-password" class="input input-bordered bg-white w-full" placeholder="Minimal 6 karakter" required>
                            <span id="error-password" class="text-red-500 text-xs mt-1 hidden"></span>
                        </div>
                    </div>
                    <div id="create-global-error" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                        <span id="create-error-message"></span>
                    </div>
                </form>
            </div>
            <div class="p-4 border-t border-gray-200 bg-white flex justify-end gap-2">
                <button type="button" class="btn btn-ghost text-gray-700" onclick="window.closeCreateDriverModal()">Batal</button>
                <button type="submit" form="form-create-driver" id="btn-submit-create" class="btn bg-blue-500 hover:bg-blue-600 text-white cursor-not-allowed opacity-50" disabled>
                    <span class="loading loading-spinner loading-sm hidden" id="btn-loading"></span> Simpan
                </button>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>

    {{-- MODAL EDIT DRIVER --}}
    <dialog id="modal_edit_driver" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-white max-w-2xl overflow-hidden p-0 flex flex-col">
            {{-- Header --}}
            <div class="sticky top-0 bg-white z-10 p-4 border-b border-gray-200 flex justify-between items-start">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-pen text-2xl text-yellow-600"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Edit Data Driver</h3>
                        <p class="text-sm text-gray-500">Perbarui informasi akun driver</p>
                    </div>
                </div>
                <button class="btn btn-sm btn-circle btn-ghost btn-close-modal" onclick="document.getElementById('modal_edit_driver').close()">✕</button>
            </div>

            {{-- Form --}}
            <div class="overflow-y-auto p-6">
                <form id="form-edit-driver" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit-id" name="id">

                    {{-- Foto Profil --}}
                    <div class="flex flex-col items-center mb-4">
                        <div class="relative w-32 h-32 mb-3 group cursor-pointer" onclick="document.getElementById('edit-input-foto').click()">
                            <div class="w-full h-full rounded-full overflow-hidden border-4 border-gray-100 bg-gray-50 flex items-center justify-center relative">
                                {{-- Preview Image (Selalu ada di edit, entah dummy atau foto asli) --}}
                                <img id="edit-img-preview" src="#" class="w-full h-full object-cover z-10 relative">
                            </div>
                            <div class="absolute inset-0 bg-black bg-opacity-40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-35 transition-opacity z-20">
                                <span class="text-white text-xs font-medium">Ganti Foto</span>
                            </div>
                        </div>
                        <span class="text-xs text-gray-500">Klik gambar untuk mengganti</span>
                        <input type="file" name="profile_picture" id="edit-input-foto" class="hidden" accept="image/*">
                        <span id="edit-error-profile_picture" class="text-red-500 text-xs mt-1 text-center block"></span>
                    </div>

                    {{-- Grid Input --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-control">
                            <label class="label"><span class="label-text font-medium">Nama Lengkap <span class="text-red-500">*</span></span></label>
                            <input type="text" name="name" id="edit-input-name" class="input input-bordered bg-white w-full" required>
                            <span id="edit-error-name" class="text-red-500 text-xs mt-1 block"></span>
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text font-medium">Email <span class="text-red-500">*</span></span></label>
                            <input type="email" name="email" id="edit-input-email" class="input input-bordered bg-white w-full" required>
                            <span id="edit-error-email" class="text-red-500 text-xs mt-1 block"></span>
                        </div>

                        <div class="form-control col-span-1 md:col-span-2">
                            <label class="label"><span class="label-text font-medium">Password Baru <span class="text-gray-400 font-normal text-xs">(Kosongkan jika tidak ingin mengganti)</span></span></label>
                            <input type="password" name="password" id="edit-input-password" class="input input-bordered bg-white w-full" placeholder="Minimal 6 karakter">
                            <span id="edit-error-password" class="text-red-500 text-xs mt-1 block"></span>
                        </div>
                    </div>

                    {{-- Global Error --}}
                    <div id="edit-global-error" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                        <span id="edit-error-message"></span>
                    </div>
                </form>
            </div>

            {{-- Footer --}}
            <div class="p-4 border-t border-gray-200 bg-white flex justify-end gap-2">
                <button type="button" class="btn btn-ghost text-gray-700" onclick="window.closeEditDriverModal()">Batal</button>
                <button type="submit" form="form-edit-driver" id="btn-submit-edit" class="btn bg-yellow-500 hover:bg-yellow-600 text-white cursor-not-allowed opacity-50" disabled>
                    <span class="loading loading-spinner loading-sm hidden" id="btn-loading-edit"></span>
                    Update
                </button>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>

    {{-- MODAL HAPUS DRIVER (Style Konsisten) --}}
    <dialog id="modal_hapus_driver" class="modal">
        <div class="modal-box bg-white max-w-md">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" onclick="document.getElementById('modal_hapus_driver').close()">✕</button>

            <div class="text-center mb-6">
                <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-exclamation-triangle text-4xl text-red-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Hapus Driver?</h3>
                <p class="text-gray-600 text-sm">Anda yakin ingin menghapus akun driver ini?</p>
            </div>

            {{-- Detail --}}
            <div class="bg-gray-50 rounded-lg p-4 mb-6 border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white rounded border border-gray-200 flex items-center justify-center font-bold text-gray-400 shadow-sm">
                        <i class="fas fa-user text-gray-500"></i>
                    </div>
                    <div class="text-left overflow-hidden">
                        <p class="text-xs text-gray-500">Nama Driver</p>
                        <p class="font-bold text-gray-900 text-sm truncate" id="delete-name">-</p>
                        <p class="text-xs text-gray-500 truncate" id="delete-email">-</p>
                    </div>
                </div>
            </div>

            <div class="alert alert-warning mb-6 py-2 px-4 flex items-center gap-2 rounded-lg text-sm">
                <i class="fas fa-info-circle"></i>
                <span>Data yang dihapus tidak dapat dikembalikan!</span>
            </div>

            <form id="form-hapus-driver" method="POST">
                @csrf
                @method('DELETE')
                <input type="hidden" id="delete-id" name="id">

                <div class="flex gap-3 justify-end">
                    <button type="button" class="btn btn-ghost text-gray-700" onclick="window.closeDeleteDriverModal()">Batal</button>
                    <button type="submit" class="btn bg-red-500 hover:bg-red-600 text-white" id="btn-confirm-delete">
                        <span class="loading loading-spinner loading-sm hidden mr-2" id="loading-delete"></span>
                        <i class="fas fa-trash-can mr-2" id="icon-delete"></i> Ya, Hapus
                    </button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>
@endsection

@push('styles')
    <style>
        /* ========================================= */
        /* FIX PAGINATION (FORCE LIGHT MODE)         */
        /* ========================================= */

        /* Reset background container pagination */
        nav[role="navigation"] {
            background-color: transparent !important;
        }

        /* 1. Tombol Biasa (Link) */
        nav[role="navigation"] a {
            background-color: #ffffff !important;
            color: #374151 !important;
            /* Gray-700 */
            border-color: #e5e7eb !important;
        }

        nav[role="navigation"] a:hover {
            background-color: #f3f4f6 !important;
        }

        /* 2. Tombol Disabled (Arrow Kiri/Kanan saat mentok) - INI PERBAIKANNYA */
        nav[role="navigation"] span[aria-disabled="true"] span,
        nav[role="navigation"] span[aria-disabled="true"] {
            background-color: #ffffff !important;
            /* Paksa Putih */
            color: #d1d5db !important;
            /* Gray-300 (Warna disabled) */
            border-color: #e5e7eb !important;
            cursor: not-allowed;
        }

        /* 3. Tombol Aktif (Halaman saat ini) */
        nav[role="navigation"] span[aria-current="page"]>span {
            background-color: #3b82f6 !important;
            /* Blue-500 */
            color: #ffffff !important;
            border-color: #3b82f6 !important;
        }

        /* Sembunyikan Text Previous/Next bawaan Laravel jika mengganggu layout */
        nav[role="navigation"] svg {
            width: 20px;
            height: 20px;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('assets/scripts/driver-list/driver-list.js') }}?v={{ time() }}"></script>
@endpush
