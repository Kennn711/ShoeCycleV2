<!-- Dashboard Content -->
<div id="dashboard-content" class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Total Penjualan</p>
                    <h3 class="text-2xl font-bold text-gray-900 mt-2">Rp 0</h3>
                    <p class="text-sm text-green-600 font-medium mt-1">+0%</p>
                </div>
                <div class="bg-green-500 w-12 h-12 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v20m5-17H9.5a3.5 3.5 0 1 0 0 7h5a3.5 3.5 0 1 1 0 7H6" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Total Orders</p>
                    <h3 class="text-2xl font-bold text-gray-900 mt-2">0</h3>
                    <p class="text-sm text-green-600 font-medium mt-1">+0%</p>
                </div>
                <div class="bg-blue-500 w-12 h-12 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4ZM3 6h18m-8 4a4 4 0 1 1-8 0" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Total Produk</p>
                    <h3 class="text-2xl font-bold text-gray-900 mt-2">0</h3>
                    <p class="text-sm text-green-600 font-medium mt-1">+0%</p>
                </div>
                <div class="bg-purple-500 w-12 h-12 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m7.5 4.27 9 5.15M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Zm-18-1 8.7 5 8.7-5M12 22V12" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Customer Aktif</p>
                    <h3 class="text-2xl font-bold text-gray-900 mt-2">0</h3>
                    <p class="text-sm text-green-600 font-medium mt-1">+0%</p>
                </div>
                <div class="bg-orange-500 w-12 h-12 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Recent Orders</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Order ID</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Customer</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Product</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Size</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Total</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td colspan="6" class="py-8 text-center text-gray-500">
                            Tidak ada data orders
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Shoes Content -->
<div id="shoes-content" class="space-y-6 hidden">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-900">Kelola Sepatu</h2>
        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
            + Tambah Sepatu
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left py-4 px-6 text-sm font-semibold text-gray-700">Product</th>
                        <th class="text-left py-4 px-6 text-sm font-semibold text-gray-700">Category</th>
                        <th class="text-left py-4 px-6 text-sm font-semibold text-gray-700">Sizes</th>
                        <th class="text-left py-4 px-6 text-sm font-semibold text-gray-700">Stock</th>
                        <th class="text-left py-4 px-6 text-sm font-semibold text-gray-700">Price</th>
                        <th class="text-left py-4 px-6 text-sm font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td colspan="6" class="py-8 text-center text-gray-500">
                            Tidak ada data sepatu
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Categories Content -->
<div id="categories-content" class="space-y-6 hidden">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-900">Kategori Sepatu</h2>
        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
            + Tambah Kategori
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="grid" class="w-6 h-6 text-blue-600"></i>
                </div>
                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                    Active
                </span>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Running</h3>
            <p class="text-sm text-gray-500 mb-4">0 produk</p>
            <div class="flex gap-2">
                <button class="flex-1 px-3 py-2 text-sm bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors">
                    Edit
                </button>
                <button class="px-3 py-2 text-sm bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors">
                    Delete
                </button>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="grid" class="w-6 h-6 text-blue-600"></i>
                </div>
                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                    Active
                </span>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Casual</h3>
            <p class="text-sm text-gray-500 mb-4">0 produk</p>
            <div class="flex gap-2">
                <button class="flex-1 px-3 py-2 text-sm bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors">
                    Edit
                </button>
                <button class="px-3 py-2 text-sm bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors">
                    Delete
                </button>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="grid" class="w-6 h-6 text-blue-600"></i>
                </div>
                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                    Active
                </span>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Formal</h3>
            <p class="text-sm text-gray-500 mb-4">0 produk</p>
            <div class="flex gap-2">
                <button class="flex-1 px-3 py-2 text-sm bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors">
                    Edit
                </button>
                <button class="px-3 py-2 text-sm bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors">
                    Delete
                </button>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="grid" class="w-6 h-6 text-blue-600"></i>
                </div>
                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                    Active
                </span>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Sports</h3>
            <p class="text-sm text-gray-500 mb-4">0 produk</p>
            <div class="flex gap-2">
                <button class="flex-1 px-3 py-2 text-sm bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors">
                    Edit
                </button>
                <button class="px-3 py-2 text-sm bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors">
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Sizes Content -->
<div id="sizes-content" class="space-y-6 hidden">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-900">Kelola Ukuran</h2>
        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
            + Tambah Ukuran
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-500 hover:shadow-md transition-all cursor-pointer">
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="text-2xl font-bold text-blue-600">35</span>
                    </div>
                    <div class="space-y-1">
                        <p class="text-sm text-gray-500">Stock</p>
                        <p class="text-lg font-bold text-gray-900">0</p>
                        <p class="text-xs text-gray-400">0 products</p>
                    </div>
                </div>
            </div>
            <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-500 hover:shadow-md transition-all cursor-pointer">
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="text-2xl font-bold text-blue-600">36</span>
                    </div>
                    <div class="space-y-1">
                        <p class="text-sm text-gray-500">Stock</p>
                        <p class="text-lg font-bold text-gray-900">0</p>
                        <p class="text-xs text-gray-400">0 products</p>
                    </div>
                </div>
            </div>
            <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-500 hover:shadow-md transition-all cursor-pointer">
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="text-2xl font-bold text-blue-600">37</span>
                    </div>
                    <div class="space-y-1">
                        <p class="text-sm text-gray-500">Stock</p>
                        <p class="text-lg font-bold text-gray-900">0</p>
                        <p class="text-xs text-gray-400">0 products</p>
                    </div>
                </div>
            </div>
            <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-500 hover:shadow-md transition-all cursor-pointer">
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="text-2xl font-bold text-blue-600">38</span>
                    </div>
                    <div class="space-y-1">
                        <p class="text-sm text-gray-500">Stock</p>
                        <p class="text-lg font-bold text-gray-900">0</p>
                        <p class="text-xs text-gray-400">0 products</p>
                    </div>
                </div>
            </div>
            <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-500 hover:shadow-md transition-all cursor-pointer">
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="text-2xl font-bold text-blue-600">39</span>
                    </div>
                    <div class="space-y-1">
                        <p class="text-sm text-gray-500">Stock</p>
                        <p class="text-lg font-bold text-gray-900">0</p>
                        <p class="text-xs text-gray-400">0 products</p>
                    </div>
                </div>
            </div>
            <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-500 hover:shadow-md transition-all cursor-pointer">
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="text-2xl font-bold text-blue-600">40</span>
                    </div>
                    <div class="space-y-1">
                        <p class="text-sm text-gray-500">Stock</p>
                        <p class="text-lg font-bold text-gray-900">0</p>
                        <p class="text-xs text-gray-400">0 products</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Stock Distribution</h3>
        <div class="space-y-3">
            <div class="flex items-center gap-4">
                <span class="text-sm font-medium text-gray-700 w-12">Size 35</span>
                <div class="flex-1 bg-gray-100 rounded-full h-3 overflow-hidden">
                    <div class="bg-blue-500 h-full rounded-full transition-all" style="width: 0%"></div>
                </div>
                <span class="text-sm font-medium text-gray-900 w-20 text-right">0 pcs</span>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-sm font-medium text-gray-700 w-12">Size 36</span>
                <div class="flex-1 bg-gray-100 rounded-full h-3 overflow-hidden">
                    <div class="bg-blue-500 h-full rounded-full transition-all" style="width: 0%"></div>
                </div>
                <span class="text-sm font-medium text-gray-900 w-20 text-right">0 pcs</span>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-sm font-medium text-gray-700 w-12">Size 37</span>
                <div class="flex-1 bg-gray-100 rounded-full h-3 overflow-hidden">
                    <div class="bg-blue-500 h-full rounded-full transition-all" style="width: 0%"></div>
                </div>
                <span class="text-sm font-medium text-gray-900 w-20 text-right">0 pcs</span>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-sm font-medium text-gray-700 w-12">Size 38</span>
                <div class="flex-1 bg-gray-100 rounded-full h-3 overflow-hidden">
                    <div class="bg-blue-500 h-full rounded-full transition-all" style="width: 0%"></div>
                </div>
                <span class="text-sm font-medium text-gray-900 w-20 text-right">0 pcs</span>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-sm font-medium text-gray-700 w-12">Size 39</span>
                <div class="flex-1 bg-gray-100 rounded-full h-3 overflow-hidden">
                    <div class="bg-blue-500 h-full rounded-full transition-all" style="width: 0%"></div>
                </div>
                <span class="text-sm font-medium text-gray-900 w-20 text-right">0 pcs</span>
            </div>
        </div>
    </div>
</div>

<!-- Other Pages Content -->
<div id="other-content" class="hidden">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
        <h3 class="text-xl font-bold text-gray-900 mb-2">Coming Soon</h3>
        <p class="text-gray-500">Halaman ini sedang dalam pengembangan</p>
    </div>
</div>
