@extends('layouts/backend/index')
@section('title', 'ShoeCycle | Kategori Sepatu')
@section('breadcrumb', 'Tabel > Kategori Sepatu')

@section('backend-content')
    <!-- Categories Content -->
    <div id="categories-content" class="space-y-6">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-900">Kategori Sepatu</h2>
            <button class="btn bg-blue-400 hover:bg-blue-500 text-white" id="btn-tambah-kategori">
                <i class="fas fa-plus"></i>
                Tambah
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse ($category as $see)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-layer-group text-2xl w-6 h-6 text-blue-600"></i>
                        </div>
                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                            <i class="fa-solid fa-calendar"></i>
                            {{ $see->created_at->format('d M Y') }}
                        </span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $see->category_name }}</h3>
                    <p class="text-sm text-gray-500 mb-4">
                        {{ $see->shoes_count ?? 0 }} produk
                    </p>
                    <div class="flex gap-2">
                        <!-- Tombol Daftar Sepatu (panjang) -->
                        <button class="btn-list-shoes flex-1 px-3 py-2 text-sm bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition-colors flex items-center justify-center gap-2" data-id="{{ $see->id }}" data-name="{{ $see->category_name }}">
                            <i class="fa-solid fa-table-list text-xl w-5 text-center"></i>
                            Daftar Sepatu
                        </button>

                        <!-- Tombol Ubah (ukuran kecil) -->
                        <button class="btn-edit px-3 py-2 text-sm bg-yellow-100 text-yellow-600 rounded-lg hover:bg-yellow-200 transition-colors flex items-center justify-center w-10" data-id="{{ $see->id }}" data-name="{{ $see->category_name }}">
                            <i class="fa-solid fa-pen-to-square text-xl w-5 text-center"></i>
                        </button>

                        <!-- Tombol Hapus (ukuran kecil) -->
                        <button class="btn-delete px-3 py-2 text-sm bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-colors flex items-center justify-center w-10" data-id="{{ $see->id }}" data-name="{{ $see->category_name }}" data-created="{{ $see->created_at->format('d M Y') }}">
                            <i class="fa-solid fa-trash-can text-xl w-5 text-center"></i>
                        </button>
                    </div>
                </div>
            @empty
                <!-- Empty State -->
                <div class="col-span-full flex flex-col items-center justify-center py-16 px-4">
                    <div class="w-32 h-32 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                        <i class="fa-solid fa-layer-group text-6xl text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Belum Ada Kategori</h3>
                    <p class="text-gray-500 text-center mb-6 max-w-md">
                        Anda belum memiliki kategori sepatu. Mulai dengan menambahkan kategori pertama untuk mengorganisir produk.
                    </p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Modal Tambah Kategori -->
    <dialog id="modal_tambah_kategori" class="modal" @if ($errors->any() && !session('edit_mode')) open @endif>
        <div class="modal-box bg-white max-w-md">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" type="button" id="btn-close-modal">✕</button>

            <h3 class="text-xl font-bold text-gray-900 mb-6">
                <i class="fas fa-plus-circle text-blue-500 mr-2"></i>
                Tambah Kategori Sepatu
            </h3>

            <form id="form-tambah-kategori" action="{{ route('category.store') }}" method="POST">
                @csrf

                <!-- Input Nama Kategori -->
                <div class="form-control w-full mb-6">
                    <label class="label" for="category_name">
                        <span class="label-text text-gray-700 font-medium mb-1">
                            Nama Kategori <span class="text-red-500">*</span>
                        </span>
                    </label>
                    <input type="text" id="category_name" name="category_name" placeholder="Contoh: Running, Casual, Sport" class="input input-bordered w-full bg-white text-gray-900 focus:border-blue-500 focus:outline-none @if ($errors->any() && !session('edit_mode')) @error('category_name') input-error @enderror @endif" value="{{ !session('edit_mode') ? old('category_name') : '' }}" required minlength="3" maxlength="30" />

                    <!-- Pesan Error dari Backend -->
                    @if ($errors->any() && !session('edit_mode'))
                        @error('category_name')
                            <label class="label" id="backend-error">
                                <span class="label-text-alt text-red-500">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </span>
                            </label>
                        @enderror
                    @endif

                    <!-- Pesan Live Validation (jQuery) -->
                    <label class="label">
                        <span class="label-text-alt text-red-500 text-sm hidden" id="error-message"></span>
                    </label>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3 justify-end">
                    <button type="submit" class="btn bg-blue-500 hover:bg-blue-600 text-white transition-opacity duration-200 cursor-not-allowed opacity-50" id="btn-submit" disabled>
                        <i class="fas fa-save mr-2"></i>
                        <span id="btn-text">Simpan</span>
                        <span class="loading loading-spinner loading-sm hidden" id="btn-loading"></span>
                    </button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button type="button">close</button>
        </form>
    </dialog>

    <!-- Modal Edit Kategori -->
    <dialog id="modal_edit_kategori" class="modal" @if ($errors->any() && session('edit_mode')) open @endif>
        <div class="modal-box bg-white max-w-md">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" type="button" id="btn-close-modal-edit">✕</button>

            <h3 class="text-xl font-bold text-gray-900 mb-6">
                <i class="fas fa-edit text-yellow-500 mr-2"></i>
                Edit Kategori Sepatu
            </h3>

            <form id="form-edit-kategori" method="POST">
                @csrf
                @method('PUT')

                <!-- Hidden input untuk menyimpan ID kategori -->
                <input type="hidden" id="edit_category_id" name="category_id" value="{{ session('edit_mode') ? session('edit_category_id') : '' }}">

                <!-- Input Nama Kategori -->
                <div class="form-control w-full mb-6">
                    <label class="label" for="edit_category_name">
                        <span class="label-text text-gray-700 font-medium mb-1">
                            Nama Kategori <span class="text-red-500">*</span>
                        </span>
                    </label>
                    <input type="text" id="edit_category_name" name="category_name" placeholder="Contoh: Running, Casual, Sport" class="input input-bordered w-full bg-white text-gray-900 focus:border-blue-500 focus:outline-none @if ($errors->any() && session('edit_mode')) @error('category_name') input-error @enderror @endif" value="{{ session('edit_mode') ? old('category_name') : '' }}" required minlength="3" maxlength="30" />

                    <!-- Pesan Error dari Backend -->
                    @if ($errors->any() && session('edit_mode'))
                        @error('category_name')
                            <label class="label" id="backend-error-edit">
                                <span class="label-text-alt text-red-500">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </span>
                            </label>
                        @enderror
                    @endif

                    <!-- Pesan Live Validation (jQuery) -->
                    <label class="label">
                        <span class="label-text-alt text-red-500 text-sm hidden" id="error-message-edit"></span>
                    </label>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3 justify-end">
                    <button type="button" class="btn btn-ghost text-gray-600" id="btn-batal-edit">
                        <i class="fas fa-times mr-2"></i>
                        Batal
                    </button>
                    <button type="submit" class="btn bg-yellow-500 hover:bg-yellow-600 text-white transition-opacity duration-200 cursor-not-allowed opacity-50" id="btn-submit-edit" disabled>
                        <i class="fas fa-edit mr-2 text-md"></i>
                        <span id="btn-text-edit">Update</span>
                        <span class="loading loading-spinner loading-sm hidden" id="btn-loading-edit"></span>
                    </button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button type="button">close</button>
        </form>
    </dialog>

    <!-- Modal Konfirmasi Hapus -->
    <dialog id="modal_hapus_kategori" class="modal">
        <div class="modal-box bg-white max-w-md">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" type="button" id="btn-close-modal-delete">✕</button>

            <div class="text-center mb-6">
                <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-exclamation-triangle text-4xl text-red-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">
                    Hapus Kategori?
                </h3>
                <p class="text-gray-600 text-sm">
                    Anda yakin ingin menghapus kategori ini?
                </p>
            </div>

            <!-- Detail Kategori yang akan dihapus -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center flex-shrink    ">
                        <i class="fa-solid fa-layer-group text-xl text-red-600"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-500 mb-1">Nama Kategori</p>
                        <p class="font-bold text-gray-900" id="delete-category-name">-</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-xs text-gray-600">
                    <i class="fa-solid fa-calendar"></i>
                    <span>Dibuat: <span id="delete-category-date">-</span></span>
                </div>
            </div>

            <!-- Peringatan -->
            <div class="alert alert-warning mb-6">
                <i class="fas fa-info-circle"></i>
                <span class="text-sm">Data yang sudah dihapus tidak dapat dikembalikan !</span>
            </div>

            <!-- Form Delete -->
            <form id="form-hapus-kategori" method="POST">
                @csrf
                @method('DELETE')

                <div class="flex gap-3 justify-end">
                    <button type="button" class="btn btn-ghost text-gray-600" id="btn-batal-hapus">
                        <i class="fas fa-times mr-2"></i>
                        Batal
                    </button>
                    <button type="submit" class="btn bg-red-500 hover:bg-red-600 text-white" id="btn-konfirmasi-hapus">
                        <i class="fas fa-trash mr-2"></i>
                        <span id="btn-hapus-text">Ya, Hapus</span>
                        <span class="loading loading-spinner loading-sm hidden" id="btn-hapus-loading"></span>
                    </button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button type="button">close</button>
        </form>
    </dialog>

    <!-- Modal Daftar Sepatu -->
    <dialog id="modal_daftar_sepatu" class="modal">
        <div class="modal-box bg-white max-w-5xl">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" type="button" id="btn-close-modal-shoes">✕</button>

            <div class="mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fa-solid fa-layer-group text-2xl text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Daftar Sepatu</h3>
                        <p class="text-sm text-gray-500">Kategori: <span id="shoes-category-name" class="font-semibold">-</span></p>
                    </div>
                </div>
            </div>

            <!-- Loading State -->
            <div id="shoes-loading" class="flex flex-col items-center justify-center py-12 hidden">
                <span class="loading loading-spinner loading-lg text-blue-500"></span>
                <p class="text-gray-500 mt-4">Memuat data sepatu...</p>
            </div>

            <!-- Shoes List Container -->
            <div id="shoes-list-container">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button type="button">close</button>
        </form>
    </dialog>

@endsection

@push('scripts')
    <script src="{{ asset('assets/scripts/category/category.js') }}"></script>
@endpush
