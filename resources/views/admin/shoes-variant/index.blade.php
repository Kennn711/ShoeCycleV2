@extends('layouts/backend/index')

@section('title', 'ShoeCycle | Varian Sepatu')
@section('breadcrumb', 'Tabel > Varian Sepatu')

{{-- Data Dummy Backend --}}
@php
    $dummyShoes = [
        1 => [
            'id' => 1,
            'name' => 'Nike Air Max 90',
            'variant_count' => 5,
            'total_stock' => 60,
            'available_count' => 4,
            'min_price' => 1299000,
            'brand' => 'Nike',
            'category' => 'Sneakers',
        ],
        2 => [
            'id' => 2,
            'name' => 'Adidas Ultraboost',
            'variant_count' => 3,
            'total_stock' => 28,
            'available_count' => 3,
            'min_price' => 1999000,
            'brand' => 'Adidas',
            'category' => 'Running',
        ],
        3 => [
            'id' => 3,
            'name' => 'Converse Chuck 70',
            'variant_count' => 6,
            'total_stock' => 45,
            'available_count' => 6,
            'min_price' => 799000,
            'brand' => 'Converse',
            'category' => 'Casual',
        ],
    ];
@endphp

@section('backend-content')
    <div class="space-y-6">
        {{-- Header Section --}}
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-900">Varian Sepatu</h2>
            <button class="btn bg-blue-500 hover:bg-blue-600 text-white" onclick="openShoeSelectionModal()">
                <i class="fas fa-plus mr-2"></i> Tambah
            </button>
        </div>

        {{-- Filter & Search Controls --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex flex-col md:flex-row gap-4 justify-between">
                <div class="flex flex-wrap gap-4">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium">Merek:</span>
                        <select class="select select-sm select-bordered w-full max-w-xs bg-white">
                            <option>Semua</option>
                            <option>Nike</option>
                            <option>Adidas</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium">Kategori:</span>
                        <select class="select select-sm select-bordered w-full max-w-xs bg-white">
                            <option>Semua</option>
                            <option>Sneakers</option>
                            <option>Running</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-end gap-2">
                    <label class="input input-bordered input-md flex items-center gap-2 bg-white w-full md:w-auto">
                        <i class="fas fa-search text-gray-400"></i>
                        <input type="text" class="grow text-black" placeholder="Cari sepatu..." />
                    </label>
                </div>
            </div>
        </div>

        {{-- Grid Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse ($dummyShoes as $shoe)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow shoe-card relative group">
                    {{-- Card Header --}}
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-shoe-prints text-2xl text-blue-600"></i>
                        </div>
                        <span class="px-3 py-1 {{ $shoe['available_count'] > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} rounded-full text-xs font-medium">
                            {{ $shoe['available_count'] > 0 ? 'Tersedia' : 'Habis' }}
                        </span>
                    </div>

                    {{-- Card Info --}}
                    <h3 class="text-lg font-bold text-gray-900 mb-2 truncate">{{ $shoe['name'] }}</h3>
                    <div class="space-y-2 mb-4 text-sm text-gray-600">
                        <div class="flex justify-between"><span>Varian:</span> <span class="font-bold">{{ $shoe['variant_count'] }}</span></div>
                        <div class="flex justify-between"><span>Stok Total:</span> <span class="font-bold">{{ $shoe['total_stock'] }}</span></div>
                        <div class="text-blue-600 font-medium pt-1">Rp {{ number_format($shoe['min_price'], 0, ',', '.') }}</div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex gap-2">
                        <button class="flex-1 px-3 py-2 text-sm bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition-colors flex items-center justify-center gap-2" onclick="showShoeVariantsModal({{ $shoe['id'] }})">
                            <i class="fa-solid fa-table-list"></i> Lihat Varian
                        </button>
                        <button class="px-3 py-2 text-sm bg-green-100 text-green-600 rounded-lg hover:bg-green-200 transition-colors flex items-center justify-center w-10 tooltip tooltip-top" data-tip="Tambah Varian" onclick="openCreateVariantModal({{ $shoe['id'] }}, '{{ $shoe['name'] }}')">
                            <i class="fa-solid fa-plus"></i>
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center text-gray-500">Belum ada data sepatu.</div>
            @endforelse
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
                        <p class="text-xs text-gray-500"><span id="modal-variant-count">0</span> varian ditemukan</p>
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

    {{-- 2. MODAL CREATE VARIAN (UI ORIGINAL ANDA) --}}
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
                <form id="form-create-variant" class="space-y-6">
                    <input type="hidden" name="shoe_id" id="create-shoe-id">

                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                        <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-circle-info text-blue-500"></i> Informasi Dasar
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-control">
                                <label class="label"><span class="label-text font-medium">Warna <span class="text-red-500">*</span></span></label>
                                <input type="text" name="color" id="input-color" class="input input-bordered w-full bg-white" placeholder="Contoh: Hitam Putih" required>
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text font-medium">Kode Warna</span></label>
                                <div class="flex gap-2">
                                    <input type="color" name="color_code" class="w-10 h-10 cursor-pointer rounded border p-0" value="#000000" id="color-picker">
                                    <input type="text" name="color_code_hex" class="input input-bordered w-full bg-white font-mono" placeholder="#000000" id="color-hex" maxlength="7">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                        <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-ruler text-blue-500"></i> Ukuran & Harga
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-control">
                                <label class="label"><span class="label-text font-medium">Ukuran <span class="text-red-500">*</span></span></label>
                                <select name="size" id="input-size" class="select select-bordered w-full bg-white" required>
                                    <option value="" disabled selected>Pilih ukuran</option>
                                    @for ($i = 35; $i <= 48; $i++)
                                        <option value="{{ $i }}">EU {{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text font-medium">Harga <span class="text-red-500">*</span></span></label>
                                <div class="relative w-full">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 z-10">
                                        <span class="text-gray-500 font-semibold sm:text-sm">Rp</span>
                                    </div>
                                    <input type="number" name="price" class="input input-bordered w-full bg-white pl-10 text-gray-900" placeholder="0" min="0" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                        <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-boxes-stacked text-blue-500"></i> Stok & Ketersediaan
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-control">
                                <label class="label"><span class="label-text font-medium">Stok Awal</span></label>
                                <input type="number" name="stock" class="input input-bordered w-full bg-white" placeholder="0" min="0" value="0">
                            </div>
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

                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                        <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-images text-blue-500"></i> Gambar Varian
                        </h4>
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-blue-400 transition-colors cursor-pointer bg-white" id="image-dropzone">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fa-solid fa-cloud-arrow-up text-4xl text-gray-400 mb-3"></i>
                                <p class="text-gray-700 font-medium">Upload Gambar Varian</p>
                                <p class="text-gray-500 text-sm">Drag & drop atau klik disini</p>
                                <input type="file" name="images[]" id="image-upload" class="hidden" accept="image/*" multiple>
                            </div>
                        </div>
                        <div id="image-preview-container" class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-3 hidden"></div>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                        <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-barcode text-blue-500"></i> SKU (Stock Keeping Unit)
                        </h4>
                        <div class="form-control">
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="checkbox" id="auto-sku-toggle" class="toggle toggle-primary" checked>
                                <span class="label-text font-medium">Generate SKU Otomatis</span>
                            </label>

                            <div id="manual-sku-container" class="mt-3 hidden">
                                <input type="text" name="sku" class="input input-bordered w-full bg-white font-mono" placeholder="Contoh: NIKE-BLK-42-001">
                            </div>

                            <div id="auto-sku-preview" class="mt-3">
                                <div class="p-3 bg-gray-100 rounded-lg flex gap-2 items-center">
                                    <span class="text-sm text-gray-600">Preview:</span>
                                    <span class="font-mono font-bold text-gray-800" id="sku-preview-text">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Footer Modal --}}
            <div class="p-4 border-t border-gray-200 bg-white flex justify-end gap-2">
                <button type="button" class="btn btn-ghost text-gray-700" onclick="document.getElementById('modal_create_varian').close()">Batal</button>
                <button type="submit" form="form-create-variant" class="btn bg-blue-500 hover:bg-blue-600 text-white">
                    <i class="fa-solid fa-save mr-2"></i> Simpan Varian
                </button>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>

    {{-- 3. MODAL HAPUS VARIAN (STYLE KATEGORI) --}}
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
                    <div class="w-10 h-10 bg-white rounded border border-gray-200 flex items-center justify-center font-bold text-gray-400">#</div>
                    <div class="text-left">
                        <p class="text-xs text-gray-500">Produk</p>
                        <p class="font-bold text-gray-900 text-sm" id="delete-shoe-name">-</p>
                    </div>
                </div>
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
                    <button type="submit" class="btn bg-red-500 hover:bg-red-600 text-white">
                        <i class="fas fa-trash-can mr-2"></i> Ya, Hapus
                    </button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>

    {{-- Helper Modal: Select Shoe --}}
    <dialog id="modal_select_shoe" class="modal">
        <div class="modal-box bg-white">
            <h3 class="font-bold text-lg mb-4">Pilih Sepatu</h3>
            <div class="flex flex-col gap-2 max-h-60 overflow-y-auto">
                @foreach ($dummyShoes as $shoe)
                    <button class="btn btn-outline justify-start normal-case" onclick="selectShoeHelper({{ $shoe['id'] }}, '{{ $shoe['name'] }}')">
                        <i class="fa-solid fa-shoe-prints mr-2"></i> {{ $shoe['name'] }}
                    </button>
                @endforeach
            </div>
            <div class="modal-action">
                <form method="dialog"><button class="btn">Batal</button></form>
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
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('assets/scripts/shoes/shoes-variants.js') }}"></script>
@endpush
