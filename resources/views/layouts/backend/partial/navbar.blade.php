<header class="h-16 bg-white border-b border-gray-200 fixed top-0 right-0 left-64 z-20 transition-all duration-300">
    <div class="h-full px-6 flex items-center justify-between">

        {{-- Tombol Mobile Sidebar --}}
        <div class="flex items-center gap-4 lg:hidden">
            <button id="mobile-sidebar-toggle" class="p-2 rounded-lg hover:bg-gray-100">
                <i class="fas fa-bars text-gray-600"></i>
            </button>
        </div>

        {{-- SEARCH UNIVERSAL (Laravel Doc Style) --}}
        <div class="flex-1 max-w-2xl mx-10 relative group" id="search-backend-wrapper">
            <div class="relative w-full">
                <div class="relative flex items-center">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-search text-slate-400 group-focus-within:text-blue-600 transition-colors"></i>
                    </div>
                    <input type="text" id="input-search-backend" placeholder="{{ Auth::user()->role == 'admin' ? 'Cari sepatu, transaksi, atau driver...' : 'Cari invoice atau nama pelanggan...' }}" class="w-full h-10 pl-11 pr-16 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 transition-all outline-none text-slate-700" autocomplete="off">

                    <div class="absolute inset-y-0 right-3 flex items-center gap-1 pointer-events-none">
                        <kbd class="hidden sm:inline-block px-1.5 py-0.5 border border-slate-200 bg-white text-[10px] text-slate-400 font-bold rounded-md">CTRL</kbd>
                        <kbd class="hidden sm:inline-block px-1.5 py-0.5 border border-slate-200 bg-white text-[10px] text-slate-400 font-bold rounded-md">K</kbd>
                    </div>
                </div>

                {{-- Dropdown Hasil --}}
                <div id="search-results-backend" class="hidden absolute top-full left-0 right-0 mt-2 bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden z-50 animate-in fade-in slide-in-from-top-2 duration-200">
                    <div id="results-container" class="max-h-[400px] overflow-y-auto p-2 custom-scrollbar text-black">
                        {{-- AJAX content here --}}
                    </div>
                    <div class="p-3 bg-slate-50 border-t border-slate-100 flex justify-between items-center px-5">
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Hasil Pencarian</span>
                        <span class="text-[10px] text-slate-400">Tekan <kbd class="bg-white px-1 rounded border">ESC</kbd></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- User Profile Dropdown --}}
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-4">
                <div class="relative">
                    <button id="user-dropdown-toggle" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100">
                        <div class="w-8 h-8 bg-linear-to-br from-blue-600 to-blue-700 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0m-4 7a6 6 0 0 0-6 6v1h12v-1a6 6 0 0 0-6-6" />
                            </svg>
                        </div>
                        <div class="text-left hidden md:block">
                            <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                        </div>
                    </button>

                    <div id="user-dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-2 hidden">
                        <button class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0m-4 7a6 6 0 0 0-6 6v1h12v-1a6 6 0 0 0-6-6" />
                            </svg>
                            Profil Akun
                        </button>
                        <button class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                            Pengaturan
                        </button>
                        <hr class="my-2 border-gray-200">
                        <button class="w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4m7 14 4-4m0 0-4-4m4 4H9" />
                            </svg>
                            Keluar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
