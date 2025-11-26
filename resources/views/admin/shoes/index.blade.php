@extends('layouts/backend/index')
@section('title', 'ShoeCycle | Sepatu')
@section('breadcrumb', 'Tabel > Sepatu')

@section('backend-content')
    <!-- Shoes Content -->
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-900">Data Sepatu</h2>
            <button class="btn bg-blue-400 hover:bg-blue-500 text-white" id="btn-tambah-sepatu">
                <i class="fas fa-plus"></i>
                Tambah
            </button>
        </div>

        <!-- Controls Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex flex-col md:flex-row gap-4 justify-between">
                <!-- Filter Controls -->
                <div class="flex flex-wrap gap-4">
                    <!-- Items Per Page -->
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-black whitespace-nowrap">Tampilkan :</span>
                        <div class="dropdown dropdown-center">
                            <div tabindex="0" role="button" class="btn btn-outline btn-sm bg-white border-gray-200 text-black hover:bg-gray-50 flex items-center gap-2 w-24 justify-between">
                                <span>10 Data</span>
                                <i class="fas fa-chevron-down text-gray-500 text-xs"></i>
                            </div>
                            <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-white border border-gray-200 rounded-box w-44">
                                <li><a class="text-sm text-gray-700 hover:bg-gray-100">5 Data</a></li>
                                <li><a class="text-sm text-gray-700 hover:bg-gray-100">10 Data</a></li>
                                <li><a class="text-sm text-gray-700 hover:bg-gray-100">15 Data</a></li>
                                <li><a class="text-sm text-gray-700 hover:bg-gray-100">20 Data</a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Filter by Category Dropdown -->
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-black whitespace-nowrap">Kategori :</span>
                        <div class="dropdown dropdown-center">
                            <div tabindex="0" role="button" class="btn btn-outline btn-sm bg-white border-gray-200 text-black hover:bg-gray-50 flex items-center gap-2 w-32 justify-between">
                                <span>Pilih Kategori</span>
                                <i class="fas fa-chevron-down text-gray-500 text-xs"></i>
                            </div>
                            <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-white border border-gray-200 rounded-box w-44">
                                <li><a class="text-sm text-gray-700 hover:bg-gray-100">Semua Kategori</a></li>
                                @foreach ($categories as $cat)
                                    <li><a class="text-sm text-gray-700 hover:bg-gray-100">{{ $cat->category_name }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <!-- Sort Options -->
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-black whitespace-nowrap">Urutkan :</span>
                        <div class="dropdown dropdown-center">
                            <div tabindex="0" role="button" class="btn btn-outline btn-sm bg-white border-gray-200 text-black hover:bg-gray-50 flex items-center gap-2 w-32 justify-between">
                                <span>Nama</span>
                                <i class="fas fa-chevron-down text-gray-500 text-xs"></i>
                            </div>
                            <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-white border border-gray-200 rounded-box w-44">
                                <li><a class="text-sm text-gray-700 hover:bg-gray-100">Nama</a></li>
                                <li><a class="text-sm text-gray-700 hover:bg-gray-100">Kategori</a></li>
                                <li><a class="text-sm text-gray-700 hover:bg-gray-100">Stok</a></li>
                                <li><a class="text-sm text-gray-700 hover:bg-gray-100">Harga</a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Sort Order -->
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-black whitespace-nowrap">Urutan :</span>
                        <div class="dropdown dropdown-center">
                            <div tabindex="0" role="button" class="btn btn-outline btn-sm bg-white border-gray-200 text-black hover:bg-gray-50 flex items-center gap-2 w-40 justify-between">
                                <span>A-Z / Terkecil</span>
                                <i class="fas fa-chevron-down text-gray-500 text-xs"></i>
                            </div>
                            <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-white border border-gray-200 rounded-box w-44">
                                <li><a class="text-sm text-gray-700 hover:bg-gray-100">A-Z / Terkecil</a></li>
                                <li><a class="text-sm text-gray-700 hover:bg-gray-100">Z-A / Terbesar</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Search Input -->
                <div class="flex items-end gap-2">
                    <div class="form-control w-full max-w-xs">
                        <label class="input input-bordered input-md flex items-center gap-2 bg-white border-gray-200">
                            <i class="fas fa-search text-gray-400"></i>
                            <input type="text" id="searchInput" class="grow text-black placeholder-gray-400" placeholder="Cari berdasarkan nama, kategori, atau kode..." />
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <thead class="bg-gray-50 text-gray-700">
                        <tr>
                            <th class="sortable" data-sort="no">No <i class="fas fa-sort ml-1"></i></th>
                            <th class="sortable" data-sort="name">Nama Sepatu <i class="fas fa-sort ml-1"></i></th>
                            <th class="sortable" data-sort="category">Kategori <i class="fas fa-sort ml-1"></i></th>
                            <th class="sortable" data-sort="brand">Merk <i class="fas fa-sort ml-1"></i></th>
                            <th class="sortable" data-sort="description">Deskripsi <i class="fas fa-sort ml-1"></i></th>
                            <th class="sortable" data-sort="created_at">Dibuat Pada <i class="fas fa-sort ml-1"></i></th>
                            <th class="sortable" data-sort="updated_at">Diubah Terakhir <i class="fas fa-sort ml-1"></i></th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <tr>
                            <td colspan="8" class="text-center py-8">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-shoe-prints text-gray-300 text-4xl mb-2"></i>
                                    <p class="text-gray-500">Tidak ada data sepatu</p>
                                    <p class="text-gray-400 text-sm">Klik "Tambah" untuk menambahkan data baru</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Section -->
            <div class="flex flex-col md:flex-row justify-between items-center p-4 border-t border-gray-100">
                <div class="text-sm text-gray-700 mb-4 md:mb-0">
                    Menampilkan <span id="showingStart" class="font-medium">0</span> - <span id="showingEnd" class="font-medium">0</span> dari <span id="totalItems" class="font-medium">0</span> item
                </div>
                <div class="join" id="paginationContainer">
                    <!-- Pagination buttons will be generated by JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Sepatu -->
    <dialog id="modal_tambah_sepatu" class="modal" @if ($errors->any()) open @endif>
        <div class="modal-box bg-white max-w-3xl">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" type="button" id="btn-close-modal-shoe">✕</button>

            <h3 class="text-xl font-bold text-gray-900 mb-6">
                <i class="fas fa-plus-circle text-blue-500 mr-2"></i>
                Tambah Sepatu Baru
            </h3>

            <form id="form-tambah-sepatu" action="{{ route('shoes.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Nama Sepatu -->
                    <div class="form-control w-full">
                        <label class="label" for="shoe_name">
                            <span class="label-text text-gray-700 font-medium">
                                Nama Sepatu <span class="text-red-500">*</span>
                            </span>
                        </label>
                        <input type="text" id="shoe_name" name="name" placeholder="Contoh: Nike Air Max 90" class="input input-bordered w-full bg-white text-gray-900 focus:border-blue-500 focus:outline-none @error('name') input-error @enderror" value="{{ old('name') }}" required minlength="3" maxlength="100" />
                        @error('name')
                            <label class="label">
                                <span class="label-text-alt text-red-500">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </span>
                            </label>
                        @enderror
                        <label class="label">
                            <span class="label-text-alt text-red-500 text-sm hidden" id="error-shoe-name"></span>
                        </label>
                    </div>

                    <!-- Kategori -->
                    <div class="form-control w-full">
                        <label class="label" for="shoe_category">
                            <span class="label-text text-gray-700 font-medium">
                                Kategori <span class="text-red-500">*</span>
                            </span>
                        </label>
                        <select id="shoe_category" name="category_id" class="select select-bordered w-full bg-white text-gray-900 focus:border-blue-500 focus:outline-none @error('category_id') input-error @enderror" required>
                            <option value="" disabled selected>Pilih Kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->category_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <label class="label">
                                <span class="label-text-alt text-red-500">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </span>
                            </label>
                        @enderror
                        <label class="label">
                            <span class="label-text-alt text-red-500 text-sm hidden" id="error-shoe-category"></span>
                        </label>
                    </div>

                    <!-- Merk -->
                    <div class="form-control w-full">
                        <label class="label" for="shoe_brand">
                            <span class="label-text text-gray-700 font-medium">
                                Merk
                            </span>
                        </label>
                        <input type="text" id="shoe_brand" name="brand" placeholder="Contoh: Nike, Adidas, Puma" class="input input-bordered w-full bg-white text-gray-900 focus:border-blue-500 focus:outline-none @error('brand') input-error @enderror" value="{{ old('brand') }}" maxlength="50" />
                        @error('brand')
                            <label class="label">
                                <span class="label-text-alt text-red-500">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </span>
                            </label>
                        @enderror
                        <label class="label">
                            <span class="label-text-alt text-red-500 text-sm hidden" id="error-shoe-brand"></span>
                        </label>
                    </div>

                    <!-- Status Aktif -->
                    <div class="form-control w-full">
                        <label class="label" for="shoe_is_active">
                            <span class="label-text text-gray-700 font-medium">
                                Status
                            </span>
                        </label>
                        <select id="shoe_is_active" name="is_active" class="select select-bordered w-full bg-white text-gray-900 focus:border-blue-500 focus:outline-none">
                            <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                    </div>
                </div>

                <!-- Deskripsi (Full Width) -->
                <div class="form-control w-full mt-4">
                    <label class="label" for="shoe_description">
                        <span class="label-text text-gray-700 font-medium">
                            Deskripsi
                        </span>
                    </label>
                    <textarea id="shoe_description" name="description" placeholder="Deskripsi produk sepatu..." class="textarea textarea-bordered w-full bg-white text-gray-900 focus:border-blue-500 focus:outline-none @error('description') input-error @enderror" rows="4" maxlength="1000">{{ old('description') }}</textarea>
                    @error('description')
                        <label class="label">
                            <span class="label-text-alt text-red-500">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </span>
                        </label>
                    @enderror
                    <label class="label">
                        <span class="label-text-alt text-gray-500">
                            <span id="desc-count">0</span>/1000 karakter
                        </span>
                    </label>
                </div>

                <!-- Info Box -->
                <div class="alert alert-info mt-4">
                    <i class="fas fa-info-circle"></i>
                    <span class="text-sm">Setelah menyimpan data sepatu, Anda dapat menambahkan varian (warna, ukuran, harga, dan stok) pada halaman Varian Sepatu.</span>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3 justify-end mt-6">
                    <button type="button" class="btn btn-ghost text-gray-600" id="btn-batal-shoe">
                        <i class="fas fa-times mr-2"></i>
                        Batal
                    </button>
                    <button type="submit" class="btn bg-blue-500 hover:bg-blue-600 text-white transition-opacity duration-200 cursor-not-allowed opacity-50" id="btn-submit-shoe" disabled>
                        <i class="fas fa-save mr-2"></i>
                        <span id="btn-text-shoe">Simpan</span>
                        <span class="loading loading-spinner loading-sm hidden" id="btn-loading-shoe"></span>
                    </button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button type="button">close</button>
        </form>
    </dialog>

@endsection

@push('scripts')
    <script src="{{ asset('assets/scripts/shoes/shoes.js') }}"></script>
@endpush
