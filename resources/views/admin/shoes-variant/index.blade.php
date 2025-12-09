@extends('layouts/backend/index')

@section('title', 'ShoeCycle | Varian Sepatu')
@section('breadcrumb', 'Tabel > Varian Sepatu')

@section('backend-content')
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-900">Varian Sepatu</h2>
        </div>

        {{-- Filter & Search Controls --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            {{-- Bungkus SEMUA filter dalam satu form agar filter bekerja bersamaan --}}
            <form action="{{ route('shoes-variant.index') }}" method="GET">

                <div class="flex flex-col md:flex-row gap-4 justify-between">
                    {{-- Bagian Filter Kiri --}}
                    <div class="flex flex-wrap gap-4">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium">Merek</span>
                            <select name="brand" class="select select-sm select-bordered w-full max-w-xs bg-white" onchange="this.form.submit()">
                                <option value="">Semua</option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand }}" {{ request('brand') == $brand ? 'selected' : '' }}>
                                        {{ $brand }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Bagian Search Kanan --}}
                    <div class="flex items-center gap-2 w-full md:w-auto">
                        <label class="input input-bordered input-md flex items-center gap-2 bg-white w-full md:w-64">
                            <i class="fas fa-search text-gray-400"></i>
                            <input type="text" name="q" class="grow text-black min-w-0" placeholder="Cari sepatu..." value="{{ request('q') }}" />
                        </label>

                        <button type="submit" class="btn btn-md bg-blue-500 hover:bg-blue-600 text-white border-none">
                            Cari
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- ======================================================================== --}}
        {{-- GRID CARDS OF SHOES --}}
        {{-- ======================================================================== --}}

        {{-- Grid Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse ($shoes as $shoe)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow shoe-card relative group">
                    {{-- Card Header --}}
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-shoe-prints text-2xl text-blue-600"></i>
                        </div>
                        {{-- Status menggunakan is_active dari tabel shoes --}}
                        <span class="px-3 py-1 {{ $shoe->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} rounded-full text-xs font-medium">
                            {{ $shoe->is_active ? 'Aktif' : 'Non-Aktif' }}
                        </span>
                    </div>

                    {{-- Card Info --}}
                    <h3 class="text-lg font-bold text-gray-900 mb-2 truncate" title="{{ $shoe->name }}">{{ $shoe->name }}</h3>
                    <div class="space-y-2 mb-4 text-sm text-gray-600">
                        <div class="flex justify-between">
                            <span>Merk :</span>
                            <span class="font-bold">{{ $shoe->brand_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Stok Total :</span>
                            {{-- Menggunakan Accessor dari Model --}}
                            <span class="font-bold">{{ $shoe->total_stock }}</span>
                        </div>
                        {{-- Menggunakan Accessor getPriceRangeAttribute dari Model --}}
                        <div class="text-blue-600 font-medium pt-1">{{ $shoe->price_range }}</div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex gap-2">
                        <button class="flex-1 px-3 py-2 text-sm bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition-colors flex items-center justify-center gap-2" onclick="showShoeVariantsModal({{ $shoe->id }})">
                            <i class="fa-solid fa-table-list"></i> Lihat Varian
                        </button>
                        <button class="px-3 py-2 text-sm bg-green-100 text-green-600 rounded-lg hover:bg-green-200 transition-colors flex items-center justify-center w-10 tooltip tooltip-top" data-tip="Tambah Varian" onclick="openCreateVariantModal({{ $shoe->id }}, '{{ $shoe->name }}')">
                            <i class="fa-solid fa-plus"></i>
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center text-gray-500">
                    <div class="flex flex-col items-center justify-center">
                        <i class="fa-solid fa-box-open text-4xl text-gray-300 mb-2"></i>
                        <p>Belum ada data sepatu.</p>
                        <p class="text-xs">Silakan tambahkan data di menu Master Sepatu.</p>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $shoes->links() }}
        </div>
    </div>

    {{-- ======================================================================== --}}
    {{-- MODALS --}}
    {{-- ======================================================================== --}}

    {{-- 1. MODAL DETAIL / LIST VARIAN --}}
    <dialog id="modal_detail_varian" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-white max-w-6xl max-h-[85vh] p-0 overflow-hidden flex flex-col">
            {{-- Header --}}
            <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-white sticky top-0 z-10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                        <i class="fa-solid fa-shoe-prints text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-gray-900" id="modal-shoe-name">-</h3>
                        <p class="text-xs text-gray-500">Total Varian : <span id="modal-variant-count">0</span></p>
                    </div>
                </div>
                <button class="btn btn-sm btn-circle btn-ghost" onclick="document.getElementById('modal_detail_varian').close()">✕</button>
            </div>

            {{-- Table --}}
            <div class="overflow-y-auto flex-1 p-4">
                <table class="table table-zebra w-full">
                    <thead class="bg-gray-50 text-gray-600 text-xs uppercase font-semibold sticky top-0">
                        <tr>
                            <th>Warna</th>
                            <th>Ukuran</th>
                            <th>SKU</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Status</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="modal-variants-table" class="text-sm">
                        {{-- Data via JS --}}
                    </tbody>
                </table>
            </div>

            {{-- Footer --}}
            <div class="p-4 border-t border-gray-100 bg-white flex justify-end gap-2">
                <button class="btn btn-ghost btn-sm" onclick="document.getElementById('modal_detail_varian').close()">Tutup</button>
                <button class="btn bg-blue-500 hover:bg-blue-600 text-white btn-sm" id="btn-add-variant-from-list">
                    <i class="fas fa-plus mr-1"></i> Tambah Varian
                </button>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>

    {{-- 2. MODAL CREATE VARIAN --}}
    <dialog id="modal_create_varian" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-white max-w-4xl max-h-[90vh] overflow-hidden p-0 flex flex-col">
            {{-- Header Modal --}}
            <div class="sticky top-0 bg-white z-10 p-4 border-b border-gray-200 flex justify-between items-start">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fa-solid fa-plus text-2xl text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Tambah Varian Sepatu</h3>
                        <p class="text-sm text-gray-500" id="create-shoe-info">-</p>
                    </div>
                </div>
                <button class="btn btn-sm btn-circle btn-ghost" type="button" onclick="document.getElementById('modal_create_varian').close()">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>

            {{-- Form Create Varian --}}
            <div class="overflow-y-auto p-6 flex-1">
                <form id="form-create-variant" action="{{ route('shoes-variant.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <input type="hidden" name="shoe_id" id="create-shoe-id">

                    {{-- Informasi Dasar --}}
                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                        <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-circle-info text-blue-500"></i> Informasi Dasar
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Input Warna --}}
                            <div class="form-control">
                                <label class="label"><span class="label-text font-medium">Warna <span class="text-red-500">*</span></span></label>
                                <input type="text" name="color" id="input-color" class="input input-bordered w-full bg-white" placeholder="Contoh: Hitam Putih" required maxlength="50">
                                {{-- Error Message Span --}}
                                <span id="error-color" class="text-red-500 text-xs mt-1 hidden"></span>
                            </div>

                            {{-- Input Kode Warna --}}
                            <div class="form-control">
                                <label class="label"><span class="label-text font-medium">Kode Warna</span></label>
                                <div class="flex gap-2">
                                    <input type="color" name="color_code" class="w-10 h-10 cursor-pointer rounded border p-0" value="#000000" id="color-picker">
                                    <input type="text" name="color_code_hex" class="input input-bordered w-full bg-white font-mono" placeholder="#000000" id="color-hex" maxlength="7">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Ukuran & Harga --}}
                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                        <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-ruler text-blue-500"></i> Ukuran & Harga
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Input Ukuran --}}
                            <div class="form-control">
                                <label class="label"><span class="label-text font-medium">Ukuran <span class="text-red-500">*</span></span></label>
                                <select name="size" id="input-size" class="select select-bordered w-full bg-white" required>
                                    <option value="" disabled selected>Pilih ukuran</option>
                                    @for ($i = 35; $i <= 48; $i++)
                                        <option value="{{ $i }}">EU {{ $i }}</option>
                                    @endfor
                                </select>
                                {{-- Error Message Span --}}
                                <span id="error-size" class="text-red-500 text-xs mt-1 hidden"></span>
                            </div>

                            {{-- Input Harga --}}
                            <div class="form-control">
                                <label class="label"><span class="label-text font-medium">Harga <span class="text-red-500">*</span></span></label>
                                <div class="relative w-full">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 z-10">
                                        <span class="text-gray-500 font-semibold sm:text-sm">Rp</span>
                                    </div>
                                    <input type="number" name="price" id="input-price" class="input input-bordered w-full bg-white pl-10 text-gray-900" placeholder="0" min="0" required>
                                </div>
                                {{-- Error Message Span --}}
                                <span id="error-price" class="text-red-500 text-xs mt-1 hidden"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Stok & Ketersediaan --}}
                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                        <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-boxes-stacked text-blue-500"></i> Stok & Ketersediaan
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Input Stok --}}
                            <div class="form-control">
                                <label class="label"><span class="label-text font-medium">Stok Awal</span></label>
                                <input type="number" name="stock" id="input-stock" class="input input-bordered w-full bg-white" placeholder="0" min="0" value="0">
                                {{-- Error Message Span --}}
                                <span id="error-stock" class="text-red-500 text-xs mt-1 hidden"></span>
                            </div>

                            {{-- Input Status --}}
                            <div class="form-control">
                                <label class="label"><span class="label-text font-medium">Status Ketersediaan</span></label>
                                <div class="flex items-center gap-4 pt-3">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="is_available" value="1" class="radio radio-primary" checked>
                                        <span class="label-text">Tersedia</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="is_available" value="0" class="radio radio-primary">
                                        <span class="label-text">Tidak Tersedia</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Gambar Varian --}}
                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                        <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-images text-blue-500"></i> Gambar Varian <span class="text-red-500">*</span>
                        </h4>
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-blue-400 transition-colors cursor-pointer bg-white" id="image-dropzone">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fa-solid fa-cloud-arrow-up text-4xl text-gray-400 mb-3"></i>
                                <p class="text-gray-700 font-medium">Upload Gambar Varian</p>
                                <p class="text-gray-500 text-sm">Drag & drop atau klik disini</p>
                                <input type="file" name="images[]" id="image-upload" class="hidden" accept="image/*" multiple>
                            </div>
                        </div>
                        {{-- Error Message Span untuk Gambar --}}
                        <span id="error-images" class="text-red-500 text-xs mt-2 hidden text-center block"></span>

                        <div id="image-preview-container" class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-3 hidden"></div>
                    </div>

                    {{-- SKU --}}
                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                        {{-- ... (Bagian SKU sama seperti sebelumnya) ... --}}
                        <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-barcode text-blue-500"></i> SKU (Stock Keeping Unit)
                        </h4>
                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" id="auto-sku-toggle" class="toggle toggle-primary" checked>
                                <span class="label-text font-medium">Generate SKU Otomatis</span>
                            </label>
                            <div id="manual-sku-container" class="mt-3 hidden">
                                <input type="text" name="sku" id="input-sku" class="input input-bordered w-full bg-white font-mono" placeholder="Contoh: NIKE-BLK-42-001" maxlength="50">
                                {{-- Error Message Span --}}
                                <span id="error-sku" class="text-red-500 text-xs mt-1 hidden"></span>
                            </div>
                            <div id="auto-sku-preview" class="mt-3">
                                <div class="p-3 bg-gray-100 rounded-lg flex gap-2 items-center">
                                    <span class="text-sm text-gray-600">Preview:</span>
                                    <span class="font-mono font-bold text-gray-800" id="sku-preview-text">-</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Container Error Global (Backend Error) --}}
                    <div id="create-global-error" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 mx-5" role="alert">
                        <span class="block sm:inline" id="create-error-message"></span>
                    </div>
                </form>
            </div>

            {{-- Footer Modal --}}
            <div class="p-4 border-t border-gray-200 bg-white flex justify-end gap-2">
                <button type="button" class="btn btn-ghost text-gray-700" id="btn-batal-create">Batal</button>
                <button type="submit" form="form-create-variant" id="btn-submit-create" class="btn bg-blue-500 hover:bg-blue-600 text-white cursor-not-allowed opacity-50" disabled>
                    <i class="fa-solid fa-save mr-2"></i>
                    <span id="btn-text-create">Simpan Varian</span>
                    <span class="loading loading-spinner loading-sm hidden" id="btn-loading-create"></span>
                </button>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>

    {{-- 3. MODAL HAPUS VARIAN --}}
    <dialog id="modal_hapus_varian" class="modal">
        <div class="modal-box bg-white max-w-md">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" onclick="document.getElementById('modal_hapus_varian').close()">✕</button>

            <div class="text-center mb-6">
                <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-exclamation-triangle text-4xl text-red-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Hapus Varian?</h3>
                <p class="text-gray-600 text-sm">Anda yakin ingin menghapus varian sepatu ini?</p>
            </div>

            <div class="bg-gray-50 rounded-lg p-4 mb-6 border border-gray-100">
                <div class="flex items-center gap-3 mb-2">
                    <div id="delete-color-preview" class="w-10 h-10 bg-white rounded border border-gray-200 flex items-center justify-center font-bold text-gray-400 shadow-sm">
                        #
                    </div>
                    <div class="text-left">
                        <p class="text-xs text-gray-500">Produk</p>
                        <p class="font-bold text-gray-900 text-sm" id="delete-shoe-name">-</p>
                    </div>
                </div>
                {{-- ... Sisa kode sama ... --}}
                <div class="grid grid-cols-2 gap-2 mt-2 pt-2 border-t border-gray-200">
                    <div>
                        <p class="text-xs text-gray-500">Warna</p>
                        <p class="font-semibold text-gray-800 text-sm" id="delete-variant-color">-</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Ukuran</p>
                        <p class="font-semibold text-gray-800 text-sm" id="delete-variant-size">-</p>
                    </div>
                </div>
            </div>

            {{-- ... Alert Warning & Form (sama) ... --}}
            <div class="alert alert-warning mb-6 py-2 px-4 flex items-center gap-2 rounded-lg text-sm">
                <i class="fas fa-info-circle"></i>
                <span>Data yang dihapus tidak dapat dikembalikan!</span>
            </div>

            <form id="form-hapus-varian" method="POST">
                @csrf
                @method('DELETE')
                <input type="hidden" id="delete-variant-id" name="variant_id">
                <div class="flex gap-3 justify-end">
                    <button type="button" class="btn btn-ghost text-gray-600" onclick="document.getElementById('modal_hapus_varian').close()">Batal</button>
                    {{-- Tambahkan Loading state pada button hapus --}}
                    <button type="submit" class="btn bg-red-500 hover:bg-red-600 text-white" id="btn-confirm-delete">
                        <span class="loading loading-spinner loading-sm hidden mr-2" id="loading-delete"></span>
                        <i class="fas fa-trash-can mr-2" id="icon-delete"></i> Ya, Hapus
                    </button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>

    {{-- 4. MODAL EDIT VARIAN --}}
    <dialog id="modal_edit_varian" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-white max-w-4xl max-h-[90vh] overflow-hidden p-0 flex flex-col">
            {{-- Header --}}
            <div class="sticky top-0 bg-white z-10 p-4 border-b border-gray-200 flex justify-between items-start">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fa-solid fa-pen-to-square text-2xl text-yellow-600"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Edit Varian Sepatu</h3>
                        <p class="text-sm text-gray-500" id="edit-shoe-info">-</p>
                    </div>
                </div>
                <button class="btn btn-sm btn-circle btn-ghost" onclick="document.getElementById('modal_edit_varian').close()">✕</button>
            </div>

            {{-- Form Edit --}}
            <div class="overflow-y-auto p-6 flex-1">
                <form id="form-edit-variant" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit-variant-id">
                    <input type="hidden" name="deleted_images" id="edit-deleted-images"> {{-- Menyimpan ID gambar yg dihapus --}}

                    {{-- Informasi Dasar --}}
                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                        <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2"><i class="fa-solid fa-circle-info text-blue-500"></i> Informasi Dasar</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-control">
                                <label class="label"><span class="label-text font-medium">Warna <span class="text-red-500">*</span></span></label>
                                <input type="text" name="color" id="edit-input-color" class="input input-bordered w-full bg-white" required>
                                <span id="edit-error-color" class="text-red-500 text-xs mt-1 hidden"></span>
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text font-medium">Kode Warna</span></label>
                                <div class="flex gap-2">
                                    <input type="color" name="color_code" id="edit-color-picker" class="w-10 h-10 cursor-pointer rounded border p-0">
                                    <input type="text" name="color_code_hex" id="edit-color-hex" class="input input-bordered w-full bg-white font-mono" maxlength="7">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Ukuran & Harga --}}
                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                        <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2"><i class="fa-solid fa-ruler text-blue-500"></i> Ukuran & Harga</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-control">
                                <label class="label"><span class="label-text font-medium">Ukuran <span class="text-red-500">*</span></span></label>
                                <select name="size" id="edit-input-size" class="select select-bordered w-full bg-white" required>
                                    @for ($i = 35; $i <= 48; $i++)
                                        <option value="{{ $i }}">EU {{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text font-medium">Harga <span class="text-red-500">*</span></span></label>
                                <div class="relative w-full">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 z-10"><span class="text-gray-500 font-semibold sm:text-sm">Rp</span></div>
                                    <input type="number" name="price" id="edit-input-price" class="input input-bordered w-full bg-white pl-10 text-gray-900" required>
                                </div>
                                <span id="edit-error-price" class="text-red-500 text-xs mt-1 hidden"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Stok & SKU --}}
                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                        <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2"><i class="fa-solid fa-boxes-stacked text-blue-500"></i> Stok & SKU</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-control">
                                <label class="label"><span class="label-text font-medium">Stok</span></label>
                                <input type="number" name="stock" id="edit-input-stock" class="input input-bordered w-full bg-white" min="0">
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text font-medium">SKU (Opsional)</span></label>
                                <input type="text" name="sku" id="edit-input-sku" class="input input-bordered w-full bg-white font-mono">
                            </div>
                        </div>
                        <div class="form-control mt-4">
                            <label class="label"><span class="label-text font-medium">Status</span></label>
                            <div class="flex items-center gap-4">
                                <label class="flex items-center gap-2 cursor-pointer"><input type="radio" name="is_available" value="1" id="edit-status-1" class="radio radio-primary"><span class="label-text">Tersedia</span></label>
                                <label class="flex items-center gap-2 cursor-pointer"><input type="radio" name="is_available" value="0" id="edit-status-0" class="radio radio-primary"><span class="label-text">Tidak Tersedia</span></label>
                            </div>
                        </div>
                    </div>

                    {{-- Manage Gambar --}}
                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                        <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2"><i class="fa-solid fa-images text-blue-500"></i> Gambar Varian</h4>

                        {{-- 1. Gambar Existing --}}
                        <p class="text-sm text-gray-500 mb-2 font-medium">Gambar Saat Ini:</p>
                        <div id="edit-existing-images" class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                            {{-- Diisi via JS --}}
                        </div>
                        <span id="edit-error-images" class="text-red-500 text-xs hidden mb-4 block">Jangan hapus semua gambar. Sisakan minimal 1 atau upload baru.</span>

                        {{-- 2. Upload Baru --}}
                        <p class="text-sm text-gray-500 mb-2 font-medium border-t border-gray-200 pt-4">Tambah Gambar Baru:</p>
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-4 text-center hover:border-blue-400 transition-colors cursor-pointer bg-white" id="edit-image-dropzone">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fa-solid fa-plus text-2xl text-gray-400 mb-1"></i>
                                <p class="text-gray-500 text-sm">Klik / Drag untuk tambah</p>
                                <input type="file" name="images[]" id="edit-image-upload" class="hidden" accept="image/*" multiple>
                            </div>
                        </div>
                        <div id="edit-new-image-preview" class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-3 hidden"></div>
                    </div>

                    {{-- Global Error --}}
                    <div id="edit-global-error" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 mx-5">
                        <span class="block sm:inline" id="edit-error-message"></span>
                    </div>
                </form>
            </div>

            {{-- Footer --}}
            <div class="p-4 border-t border-gray-200 bg-white flex justify-end gap-2">
                <button type="button" class="btn btn-ghost text-gray-700" onclick="document.getElementById('modal_edit_varian').close()">Batal</button>
                <button type="submit" form="form-edit-variant" id="btn-submit-edit" class="btn bg-yellow-500 hover:bg-yellow-600 text-white cursor-not-allowed opacity-50" disabled>
                    <i class="fas fa-edit mr-2 text-xl"></i> Ubah
                </button>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>

@endsection

@push('styles')
    <style>
        /* Styling khusus untuk Create Modal Original */
        #image-dropzone.dragover {
            border-color: #3b82f6;
            background-color: #eff6ff;
        }

        input[type="color"] {
            -webkit-appearance: none;
            border: none;
        }

        input[type="color"]::-webkit-color-swatch-wrapper {
            padding: 0;
        }

        input[type="color"]::-webkit-color-swatch {
            border: 1px solid #e5e7eb;
            border-radius: 4px;
        }

        .image-preview-item:hover .btn-remove-img {
            opacity: 1;
        }

        /* Sembunyikan badge utama secara default */
        .primary-badge {
            display: none;
        }

        /* Hanya munculkan badge pada elemen preview PERTAMA (anak pertama) */
        .image-preview-item:first-child .primary-badge {
            display: flex;
        }

        /* Berikan border biru pada item utama supaya lebih jelas */
        .image-preview-item:first-child {
            border: 4px solid #3b82f6;
        }

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
    {{-- INJECT DATA DARI CONTROLLER KE JAVASCRIPT GLOBAL --}}
    <script>
        // Data Varian dari DB untuk Modal List (Di-load sekali saat page render)
        window.variantsData = @json($variantsData);
    </script>
    <script src="{{ asset('assets/scripts/shoes/shoes-variants.js') }}?v={{ time() }}"></script>
@endpush
