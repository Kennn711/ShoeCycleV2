@extends('layouts/backend/index')
@section('title', 'ShoeCycle | Sepatu')
@section('breadcrumb', 'Tabel > Sepatu')

@section('backend-content')
    <!-- Shoes Content -->
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-900">Data Sepatu</h2>
            <button class="btn bg-blue-400 hover:bg-blue-500 text-white">
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
                                <li><a class="text-sm text-gray-700 hover:bg-gray-100">Running</a></li>
                                <li><a class="text-sm text-gray-700 hover:bg-gray-100">Casual</a></li>
                                <li><a class="text-sm text-gray-700 hover:bg-gray-100">Basketball</a></li>
                                <li><a class="text-sm text-gray-700 hover:bg-gray-100">Football</a></li>
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

                <!-- Search Input - Simple Version -->
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
                            <th class="sortable" data-sort="no">
                                No
                                <i class="fas fa-sort ml-1"></i>
                            </th>
                            <th class="sortable" data-sort="name">
                                Nama Sepatu
                                <i class="fas fa-sort ml-1"></i>
                            </th>
                            <th class="sortable" data-sort="category">
                                Kategori
                                <i class="fas fa-sort ml-1"></i>
                            </th>
                            <th class="sortable" data-sort="brand">
                                Merk
                                <i class="fas fa-sort ml-1"></i>
                            </th>
                            <th class="sortable" data-sort="description">
                                Deskripsi
                                <i class="fas fa-sort ml-1"></i>
                            </th>
                            <th class="sortable" data-sort="created_at">
                                Dibuat Pada
                                <i class="fas fa-sort ml-1"></i>
                            </th>
                            <th class="sortable" data-sort="updated_at">
                                Diubah Terakhir
                                <i class="fas fa-sort ml-1"></i>
                            </th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <tr>
                            <td colspan="10" class="text-center py-8">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-shoe-prints text-gray-300 text-4xl mb-2"></i>
                                    <p class="text-gray-500">Tidak ada data sepatu</p>
                                    <p class="text-gray-400 text-sm">Klik "Tambah Sepatu" untuk menambahkan data baru</p>
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
@endsection

@push('scripts')
    <script>
        class ShoesTableManager {
            constructor() {
                this.currentPage = 1;
                this.itemsPerPage = 10;
                this.searchTerm = '';
                this.sortBy = 'name';
                this.sortOrder = 'asc';
                this.allData = [];
                this.filteredData = [];

                this.initializeEventListeners();
                this.loadData();
            }
        }
    </script>
@endpush
