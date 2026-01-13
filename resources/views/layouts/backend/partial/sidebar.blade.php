<!-- Sidebar -->
<aside id="sidebar" class="fixed top-0 left-0 h-full bg-white border-r border-gray-200 transition-all duration-300 z-30 w-64">
    <div class="flex flex-col h-full">
        <!-- Logo Section -->
        <div class="h-16 border-b border-gray-200 flex items-center justify-between px-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-gradient-to-br from-blue-600 to-blue-700 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m7.5 4.27 9 5.15M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Zm-18-1 8.7 5 8.7-5M12 22V12" />
                    </svg>
                </div>
                <span class="font-bold text-gray-900">ShoeCycle</span>
            </div>
        </div>

        <nav class="flex-1 px-3 py-6 space-y-2 overflow-y-auto">
            @if (auth()->user()->role === 'admin')
                <a href="{{ route('dashboard-admin') }}" class="menu-item w-full flex items-center gap-3 px-3 py-3 rounded-lg transition-colors {{ Route::is('dashboard-admin') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Zm6 13V12h6v10" />
                    </svg>
                    <span>Dashboard</span>
                </a>

                <div>
                    @php
                        $isTabelActive = Route::is('shoes.*', 'category.*', 'driver.index', 'transaction.index', 'shoes-variant.*');
                    @endphp
                    <button id="tabel-toggle" class="parent-menu w-full flex items-center justify-between px-3 py-3 rounded-lg transition-colors {{ $isTabelActive ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M3 4h18a1 1 0 0 1 1 1v4a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1m0 8h18a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1v-8a1 1 0 0 1 1-1m5-4v12" />
                            </svg>
                            <span class="flex-1 text-left">Tabel</span>
                        </div>
                        <svg id="tabel-icon" class="w-4 h-4 transition-transform duration-300 {{ $isTabelActive ? 'rotate-90' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 18 6-6-6-6" />
                        </svg>
                    </button>

                    <div id="tabel-submenu" class="ml-4 mt-1 space-y-1 overflow-hidden transition-all duration-300" style="max-height: {{ $isTabelActive ? '500px' : '0' }};">

                        <a href="{{ route('driver.index') }}" class="submenu-item w-full flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ Route::is('driver.index') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' }} text-sm">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14c4.418 0 8-3.582 8-8H4c0 4.418 3.582 8 8 8zM12 14v7M8 21h8M9 14H7a3 3 0 00-3 3v1M15 14h2a3 3 0 013 3v1" />
                            </svg>
                            <span>Driver</span>
                        </a>

                        <a href="{{ route('shoes.index') }}" class="submenu-item w-full flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ Route::is('shoes.*') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' }} text-sm">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.42 10.64a1.96 1.96 0 010 2.78l-8.96 8.96a2.45 2.45 0 01-3.47 0L3.58 17.97a2.45 2.45 0 010-3.47l8.96-8.96a1.96 1.96 0 012.78 0l1.05 1.05M16.5 7.5l2.29 2.29M12.85 11.15l2.29 2.29M3.5 17.5L10 11" />
                            </svg>
                            <span>Sepatu</span>
                        </a>

                        <a href="{{ route('category.index') }}" class="submenu-item w-full flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ Route::is('category.*') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' }} text-sm">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <rect width="7" height="7" x="3" y="3" rx="1" stroke-width="2" />
                                <rect width="7" height="7" x="14" y="3" rx="1" stroke-width="2" />
                                <rect width="7" height="7" x="14" y="14" rx="1" stroke-width="2" />
                                <rect width="7" height="7" x="3" y="14" rx="1" stroke-width="2" />
                            </svg>
                            <span>Kategori</span>
                        </a>

                        <a href="{{ route('shoes-variant.index') }}" class="submenu-item w-full flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ Route::is('shoes-variant.*') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' }} text-sm">
                            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9 7h2l1 2.5L13 7h2l-2 5l2 5h-2l-1-2.5l-1 2.5H9l2-5zm7 0h2v8h4v2h-6zm-8 8c0 1.11-.89 2-2 2H2v-2h4v-2H4v-2h2V9H2V7h4a2 2 0 0 1 2 2v1.5c0 .83-.67 1.5-1.5 1.5c.83 0 1.5.67 1.5 1.5z" />
                            </svg>
                            <span>Varian Sepatu</span>
                        </a>

                        <a href="{{ route('transaction.index') }}" class="submenu-item w-full flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ Route::is('transaction.index') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' }} text-sm">
                            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20 2H10a3 3 0 0 0-3 3v7a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V5a3 3 0 0 0-3-3Zm1 10a1 1 0 0 1-1 1H10a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1Zm-3.5-4a1.49 1.49 0 0 0-1 .39a1.5 1.5 0 1 0 0 2.22a1.5 1.5 0 1 0 1-2.61ZM16 17a1 1 0 0 0-1 1v1a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1v-4h1a1 1 0 0 0 0-2H3v-1a1 1 0 0 1 1-1a1 1 0 0 0 0-2a3 3 0 0 0-3 3v7a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-1a1 1 0 0 0-1-1ZM6 18h1a1 1 0 0 0 0-2H6a1 1 0 0 0 0 2Z" />
                            </svg>
                            <span>Transaksi</span>
                        </a>
                    </div>
                </div>
            @endif

            @if (auth()->user()->role === 'driver')
                <a href="{{ route('dashboard-driver') }}" class="menu-item w-full flex items-center gap-3 px-3 py-3 rounded-lg transition-colors {{ Route::is('dashboard-driver') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Zm6 13V12h6v10" />
                    </svg>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('delivery.index') }}" class="menu-item w-full flex items-center gap-3 px-3 py-3 rounded-lg transition-colors {{ Route::is('delivery.index') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 10h4M3 14h2" />
                    </svg>
                    <span>Daftar Pengiriman</span>
                </a>
            @endif

            <!-- Settings -->
            <button data-page="settings" class="menu-item w-full flex items-center gap-3 px-3 py-3 rounded-lg transition-colors text-gray-600 hover:bg-gray-50">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2" />
                    <circle cx="12" cy="12" r="3" />
                </svg>
                <span>Pengaturan</span>
            </button>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="menu-item w-full flex items-center gap-3 px-3 py-3 rounded-lg transition-colors text-red-600 hover:bg-red-50">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span>Keluar</span>
                </button>
            </form>
        </nav>
    </div>
</aside>
