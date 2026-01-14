<!DOCTYPE html>
<html lang="id" data-theme="light" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'ShoeCycle')</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/upload/logo/logo.png') }}">

    {{-- Google Fonts - DM Sans & Outfit untuk kesan modern --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">

    {{-- FontAwesome --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome/css/all.min.css') }}">

    {{-- AOS Animation --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/aos/aos.css') }}">

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --font-body: 'DM Sans', system-ui, -apple-system, sans-serif;
            --font-heading: 'Outfit', system-ui, -apple-system, sans-serif;
            --color-primary: #2563eb;
            --color-primary-dark: #1e40af;
            --color-accent: #f59e0b;
            --navbar-height: 80px;
        }

        body {
            font-family: var(--font-body);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        .font-heading {
            font-family: var(--font-heading);
            letter-spacing: -0.02em;
        }

        /* Navbar Enhancement */
        .navbar-glass {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px) saturate(180%);
            -webkit-backdrop-filter: blur(12px) saturate(180%);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .navbar-glass.scrolled {
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 4px 24px -1px rgba(0, 0, 0, 0.08);
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .btn-primary:disabled,
        .btn-primary[disabled] {
            background-color: #2563eb !important;
            /* Biru Solid */
            color: #ffffff !important;
            opacity: 0.8;
            border: none;
        }
    </style>
    @stack('styles')
</head>

<body class="flex flex-col min-h-screen bg-slate-50 text-gray-900">

    {{-- ========================================================= --}}
    {{-- NAVBAR SECTION - Cleaner & More Professional            --}}
    {{-- ========================================================= --}}
    <nav class="navbar-glass fixed top-0 left-0 right-0 z-50" id="navbar">
        <div class="container mx-auto px-4 lg:px-6">
            <div class="flex items-center justify-between h-20">

                {{-- Logo & Brand --}}
                <div class="flex items-center gap-12">
                    <a href="{{ route('landing-page') }}" class="flex items-center gap-3 group">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/20 group-hover:shadow-blue-500/40 transition-all">
                            <img src="{{ asset('assets/upload/logo/logo.png') }}" alt="Logo" class="w-10 h-10 object-contain rounded-xl">
                        </div>
                        <span class="text-xl font-bold text-gray-900 font-heading hidden sm:block">ShoeCycle</span>
                    </a>

                    {{-- Desktop Navigation --}}
                    <div class="hidden lg:flex items-center gap-1">
                        <a href="{{ route('landing-page') }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all">
                            Beranda
                        </a>
                        <a href="{{ route('shoes-collection.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all">
                            Koleksi
                        </a>
                        <a href="{{ route('all-category.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all">
                            Kategori
                        </a>
                    </div>
                </div>

                {{-- Right Actions --}}
                <div class="flex items-center gap-2">
                    {{-- Search --}}
                    <button class="btn btn-ghost btn-circle text-gray-600 hover:text-blue-600 hover:bg-blue-50 hidden sm:flex">
                        <i class="fas fa-search text-lg"></i>
                    </button>

                    {{-- Dropdown Keranjang Belanja --}}
                    <div class="dropdown dropdown-end">
                        <div tabindex="0" role="button" class="btn btn-ghost btn-circle text-gray-600 hover:text-blue-600 hover:bg-blue-50 transition-all">
                            <div class="indicator">
                                <i class="fas fa-shopping-bag text-lg"></i>

                                {{-- PERBAIKAN: Hapus @if di luar span, masukkan logika hidden ke dalam class --}}
                                <span id="cart-badge-count" class="badge badge-sm bg-blue-600 text-white border-none indicator-item shadow-sm {{ isset($cartItems) && $cartItems->count() > 0 ? '' : 'hidden' }}">
                                    {{ isset($cartItems) ? $cartItems->count() : 0 }}
                                </span>
                            </div>
                        </div>

                        <div tabindex="0" id="mini-cart-dropdown-content" class="dropdown-content mt-3 z-[60] card card-compact w-80 bg-white shadow-2xl border border-gray-100 rounded-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
                            @include('layouts.frontend.partial.mini-cart-items')
                        </div>
                    </div>

                    {{-- Auth Section --}}
                    @auth
                        <div class="dropdown dropdown-end">
                            <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
                                <div class="w-9 rounded-full ring-2 ring-gray-200 ring-offset-2">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=2563eb&color=fff&bold=true" alt="{{ Auth::user()->name }}" />
                                </div>
                            </div>
                            <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-1 p-2 shadow-xl bg-white rounded-xl w-56 border border-gray-100">
                                <li class="menu-title px-4 py-2">
                                    <span class="text-xs text-gray-500">Hai, {{ Auth::user()->name }}</span>
                                </li>
                                <li><a href="#" class="text-sm"><i class="fa-solid fa-gear mr-2 w-4"></i> Pengaturan</a></li>
                                <li><a href="{{ route('my-order.index') }}" class="text-sm"><i class="fa-solid fa-square-poll-horizontal mr-2 w-4"></i> Pesanan Saya</a></li>
                                <li class="border-t border-gray-100 mt-1 pt-1">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="text-red-500  w-full text-left text-sm">
                                            <i class="fas fa-sign-out-alt mr-2 w-4"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        {{-- Ubah href menjadi onclick --}}
                        <button onclick="openLoginModal()" class="btn btn-primary btn-sm rounded-lg text-white text-sm px-6 ml-2 hidden sm:flex shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 transition-all transform hover:-translate-y-0.5">
                            Masuk
                        </button>
                    @endauth

                    {{-- Mobile Menu Toggle --}}
                    <button class="btn btn-ghost btn-circle lg:hidden" onclick="toggleMobileMenu()">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div id="mobileMenu" class="lg:hidden hidden bg-white border-t border-gray-100">
            <div class="container mx-auto px-4 py-4">
                <div class="flex flex-col gap-2">
                    <a href="{{ url('/') }}" class="px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-blue-50 rounded-lg">Beranda</a>
                    <a href="#" class="px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-blue-50 rounded-lg">Semua Produk</a>
                    <a href="#new-arrivals" class="px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-blue-50 rounded-lg">Koleksi</a>
                    <a href="#" class="px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-blue-50 rounded-lg">Pesanan Saya</a>
                    @guest
                        <a href="{{ route('login') }}" class="btn btn-primary btn-sm rounded-lg text-white mt-2">Login</a>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    {{-- Spacer untuk fixed navbar --}}
    <div style="height: var(--navbar-height);"></div>

    {{-- ========================================================= --}}
    {{-- MAIN CONTENT                                              --}}
    {{-- ========================================================= --}}
    <main class="flex-grow">
        @yield('frontend-content')
    </main>

    {{-- ========================================================= --}}
    {{-- FOOTER SECTION - Modern & Clean                          --}}
    {{-- ========================================================= --}}
    <footer class="bg-gradient-to-br from-gray-900 via-gray-900 to-gray-800 text-gray-300 pt-16 pb-8 relative overflow-hidden">
        {{-- Decorative elements --}}
        <div class="absolute top-0 right-0 w-96 h-96 bg-blue-600 rounded-full opacity-5 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-80 h-80 bg-purple-600 rounded-full opacity-5 blur-3xl"></div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">

                {{-- Brand Info --}}
                <div class="lg:col-span-1">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl flex items-center justify-center shadow-lg">
                            <img src="{{ asset('assets/upload/logo/logo.png') }}" alt="Logo" class="w-10 h-10 object-contain rounded-xl">
                        </div>
                        <span class="text-2xl font-bold text-white font-heading">ShoeCycle</span>
                    </div>
                    <p class="text-sm leading-relaxed text-gray-400 mb-6">
                        Platform e-commerce sepatu terpercaya dengan layanan pengiriman lokal tercepat di Mojokerto. Kualitas original, harga bersahabat.
                    </p>
                    <div class="flex gap-3">
                        {{-- 1. Facebook --}}
                        <a href="#" class="w-10 h-10 rounded-lg bg-gray-800 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all group">
                            <svg class="w-6 h-6 fill-current group-hover:scale-110 transition-transform duration-300" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                            </svg>
                        </a>

                        {{-- 2. Instagram --}}
                        <a href="#" class="w-10 h-10 rounded-lg bg-gray-800 flex items-center justify-center hover:bg-pink-600 hover:text-white transition-all group">
                            <svg class="w-6 h-6 fill-current group-hover:scale-110 transition-transform duration-300" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                            </svg>
                        </a>

                        {{-- 3. WhatsApp --}}
                        <a href="#" class="w-10 h-10 rounded-lg bg-gray-800 flex items-center justify-center hover:bg-green-500 hover:text-white transition-all group">
                            <svg class="w-6 h-6 fill-current group-hover:scale-110 transition-transform duration-300" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                            </svg>
                        </a>
                    </div>
                </div>

                {{-- Quick Links --}}
                <div>
                    <h3 class="text-white font-bold mb-4 text-sm uppercase tracking-wider">Belanja</h3>
                    <ul class="space-y-2.5">
                        <li><a href="#" class="text-sm text-gray-400 hover:text-white transition-colors flex items-center gap-2">
                                <i class="fas fa-chevron-right text-xs text-blue-600"></i> Semua Produk
                            </a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-white transition-colors flex items-center gap-2">
                                <i class="fas fa-chevron-right text-xs text-blue-600"></i> Pria
                            </a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-white transition-colors flex items-center gap-2">
                                <i class="fas fa-chevron-right text-xs text-blue-600"></i> Wanita
                            </a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-white transition-colors flex items-center gap-2">
                                <i class="fas fa-chevron-right text-xs text-blue-600"></i> Diskon
                            </a></li>
                    </ul>
                </div>

                {{-- Support --}}
                <div>
                    <h3 class="text-white font-bold mb-4 text-sm uppercase tracking-wider">Bantuan</h3>
                    <ul class="space-y-2.5">
                        <li><a href="#" class="text-sm text-gray-400 hover:text-white transition-colors flex items-center gap-2">
                                <i class="fas fa-chevron-right text-xs text-blue-600"></i>Pesanan Saya
                            </a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-white transition-colors flex items-center gap-2">
                                <i class="fas fa-chevron-right text-xs text-blue-600"></i> Syarat & Ketentuan
                            </a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-white transition-colors flex items-center gap-2">
                                <i class="fas fa-chevron-right text-xs text-blue-600"></i> Kebijakan Privasi
                            </a></li>
                        <li><a href="#" class="text-sm text-gray-400 hover:text-white transition-colors flex items-center gap-2">
                                <i class="fas fa-chevron-right text-xs text-blue-600"></i> Hubungi Kami
                            </a></li>
                    </ul>
                </div>

                {{-- Newsletter --}}
                <div>
                    <h3 class="text-white font-bold mb-4 text-sm uppercase tracking-wider">Tetap Terhubung</h3>
                    <p class="text-sm text-gray-400 mb-4">Dapatkan info promo terbaru langsung ke inbox Anda.</p>
                    <div class="flex gap-2">
                        <input type="email" placeholder="Email Anda..." class="input input-sm bg-gray-800 border-gray-700 text-white focus:border-blue-600 flex-1 rounded-lg" />
                        <button class="btn btn-primary btn-sm text-white rounded-lg px-4">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center text-sm gap-4">
                <p class="text-gray-500">&copy; {{ date('Y') }} ShoeCycle. All rights reserved.</p>
                <div class="flex items-center gap-4">
                    <span class="text-gray-500 text-xs">Metode Pembayaran:</span>
                    <i class="fa-solid fa-credit-card text-2xl text-gray-600 hover:text-white transition-colors"></i>
                    <i class="fas fa-wallet text-2xl text-gray-600 hover:text-white transition-colors"></i>
                </div>
            </div>
        </div>
    </footer>

    {{-- ========================================================= --}}
    {{-- MODAL LOGIN                                              --}}
    {{-- ========================================================= --}}
    <dialog id="login_modal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-white p-0 overflow-hidden shadow-2xl rounded-2xl max-w-md w-full relative">

            {{-- Close Button --}}
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-3 top-3 z-10 text-white-500 hover:bg-gray-100">✕</button>
            </form>

            <div class="flex flex-col">
                {{-- Header Image / Illustration (Optional) --}}
                <div class="h-32 bg-gradient-to-br from-blue-600 to-blue-800 flex items-center justify-center relative overflow-hidden">
                    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
                    <div class="text-center z-10">
                        <div class="w-16 h-16 bg-white rounded-xl mx-auto flex items-center justify-center shadow-lg mb-2">
                            <img src="{{ asset('assets/upload/logo/logo.png') }}" alt="Logo" class="w-14 h-14 object-contain rounded-xl">
                        </div>
                        <h3 class="text-white font-bold text-lg font-heading">Masuk ke akun anda</h3>
                    </div>
                </div>

                {{-- Login Form --}}
                <div class="p-8 pt-6">
                    <form id="form-login" action="{{ route('login') }}" method="POST" class="space-y-4">
                        @csrf

                        {{-- Email Input --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium text-gray-700">Email</span>
                            </label>
                            <div class="relative">
                                {{-- Ikon Email (SVG) --}}
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <input type="email" name="email" id="login-email" placeholder="akun@gmail.com" class="input input-bordered w-full pl-10 bg-gray-50 focus:bg-white focus:border-blue-500 transition-colors rounded-xl" required />
                            </div>
                            <span class="text-xs text-red-500 mt-1 hidden" id="error-login-email"></span>
                        </div>

                        {{-- Password Input --}}
                        <div class="form-control">
                            <label class="label flex justify-between">
                                <span class="label-text font-medium text-gray-700">Password</span>
                            </label>
                            <div class="relative">
                                {{-- Ikon Lock (SVG) --}}
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                                <input type="password" name="password" id="login-password" placeholder="••••••••" class="input input-bordered w-full pl-10 pr-10 bg-gray-50 focus:bg-white focus:border-blue-500 transition-colors rounded-xl" required />
                                {{-- Tombol Toggle Eye --}}
                                <button type="button" onclick="togglePassword('login-password', this)" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 cursor-pointer focus:outline-none">
                                    {{-- Ikon Eye (SVG) - Default: Eye Open --}}
                                    <svg id="icon-eye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    {{-- Ikon Eye Slash (Hidden by default) --}}
                                    <svg id="icon-eye-slash" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                    </svg>
                                </button>
                            </div>
                            <span class="text-xs text-red-500 mt-1 hidden" id="error-login-password"></span>
                        </div>

                        {{-- Global Error Alert --}}
                        <div id="login-global-error" class="alert alert-error text-sm py-3 rounded-lg hidden flex flex-row items-center justify-between shadow-sm border border-red-800">

                            {{-- Kiri: Ikon & Pesan --}}
                            <div class="flex items-center gap-2">
                                <i class="fas fa-exclamation-circle text-red-600"></i>
                                <span id="login-error-msg" class="text-red-700 font-medium text-left">Login gagal.</span>
                            </div>

                            {{-- Kanan: Tombol Close (X) --}}
                            <button type="button" class="btn btn-xs btn-circle btn-ghost text-red-600 hover:bg-red-100 border-none" onclick="document.getElementById('login-global-error').classList.add('hidden')">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        {{-- Submit Button --}}
                        <button type="submit" id="btn-login-submit" class="btn btn-primary w-full rounded-xl text-white shadow-lg shadow-blue-500/30 mt-2 
               disabled:bg-blue-400 disabled:text-white disabled:border-blue-400 disabled:cursor-not-allowed" disabled>
                            <span class="loading loading-spinner loading-sm hidden" id="login-loading"></span>
                            Masuk
                        </button>
                    </form>

                    {{-- Divider --}}
                    <div class="divider text-xs text-gray-400 my-4">ATAU</div>

                    {{-- Register Link --}}
                    <p class="text-center text-sm text-gray-600 mt-2">
                        Belum punya akun? <button onclick="switchToRegister()" type="button" class="text-blue-600 font-bold hover:underline cursor-pointer">Daftar Sekarang</button>
                    </p>
                </div>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

    {{-- ========================================================= --}}
    {{-- MODAL REGISTER                                           --}}
    {{-- ========================================================= --}}
    <dialog id="register_modal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-white p-0 overflow-hidden shadow-2xl rounded-2xl max-w-md w-full relative">

            {{-- Close Button --}}
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-3 top-3 z-10 text-black-500 hover:bg-gray-100">✕</button>
            </form>

            <div class="flex flex-col">
                {{-- Header Image / Illustration (Optional) --}}
                <div class="h-32 bg-gradient-to-br from-blue-600 to-blue-800 flex items-center justify-center relative overflow-hidden">
                    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
                    <div class="text-center z-10">
                        <div class="w-16 h-16 bg-white rounded-xl mx-auto flex items-center justify-center shadow-lg mb-2">
                            <img src="{{ asset('assets/upload/logo/logo.png') }}" alt="Logo" class="w-14 h-14 object-contain rounded-xl">
                        </div>
                        <h3 class="text-white font-bold text-lg font-heading">Buat akun baru</h3>
                    </div>
                </div>

                {{-- Register Form --}}
                <div class="p-8 pt-6">
                    <form id="form-register" action="{{ route('register') }}" method="POST" class="space-y-4">
                        @csrf

                        {{-- Nama Lengkap --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text font-medium text-gray-700">Nama Lengkap</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <input type="text" name="name" id="reg-name" placeholder="Nama Lengkap" class="input input-bordered w-full pl-10 bg-gray-50 focus:bg-white rounded-xl" required />
                            </div>
                            <span class="text-xs text-red-500 mt-1 hidden" id="error-reg-name"></span>
                        </div>

                        {{-- Email --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text font-medium text-gray-700">Email</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <input type="email" name="email" id="reg-email" placeholder="contoh@email.com" class="input input-bordered w-full pl-10 bg-gray-50 focus:bg-white rounded-xl" required />
                            </div>
                            <span class="text-xs text-red-500 mt-1 hidden" id="error-reg-email"></span>
                        </div>

                        {{-- Password --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text font-medium text-gray-700">Password</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                                <input type="password" name="password" id="reg-password" placeholder="Min. 8 karakter" class="input input-bordered w-full pl-10 pr-10 bg-gray-50 focus:bg-white rounded-xl" required />
                                <button type="button" onclick="togglePassword('reg-password', this)" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 cursor-pointer focus:outline-none z-10">
                                    <svg class="w-5 h-5 icon-eye" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg class="w-5 h-5 icon-slash hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </button>
                            </div>
                            <span class="text-xs text-red-500 mt-1 hidden" id="error-reg-password"></span>
                        </div>

                        {{-- Global Error --}}
                        <div id="reg-global-error" class="alert alert-error text-sm py-2 rounded-lg hidden">
                            <i class="fas fa-exclamation-circle"></i>
                            <span id="reg-error-msg">Registrasi gagal.</span>
                        </div>

                        {{-- Submit --}}
                        <button type="submit" id="btn-reg-submit" class="btn btn-primary w-full rounded-xl text-white shadow-lg shadow-blue-500/30 mt-2 disabled:bg-blue-400 disabled:text-white disabled:border-blue-400" disabled>
                            <span class="loading loading-spinner loading-sm hidden" id="reg-loading"></span>
                            Daftar
                        </button>
                    </form>

                    <div class="divider text-xs text-gray-400 my-4">Sudah punya akun?</div>

                    {{-- Switch to Login --}}
                    <button onclick="switchToLogin()" class="btn btn-outline w-full rounded-xl border-gray-200 text-gray-600 hover:bg-gray-50 hover:border-gray-300 font-medium normal-case">
                        Masuk ke Akun Saya
                    </button>
                </div>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>

    <script src="{{ asset('assets/vendor/fontawesome/js/all.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/sweetalert2/sweetalert.js') }}"></script>
    <script src="{{ asset('assets/vendor/aos/aos.js') }}"></script>
    <script>
        // Init AOS
        AOS.init({
            duration: 600,
            once: true,
            offset: 50,
            easing: 'ease-out-cubic'
        });

        // Navbar Scroll Effect
        const navbar = document.getElementById('navbar');
        let lastScroll = 0;

        window.addEventListener('scroll', () => {
            const currentScroll = window.pageYOffset;

            if (currentScroll > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }

            lastScroll = currentScroll;
        });

        // Mobile Menu Toggle
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobileMenu');
            mobileMenu.classList.toggle('hidden');
        }

        // Close mobile menu on link click
        document.querySelectorAll('#mobileMenu a').forEach(link => {
            link.addEventListener('click', () => {
                document.getElementById('mobileMenu').classList.add('hidden');
            });
        });
    </script>

    {{-- Login Modal Script --}}
    <script>
        // --- 1. MODAL CONTROLS ---
        function openLoginModal() {
            // Reset form saat dibuka
            $('#form-login')[0].reset();
            $('.input-error').removeClass('input-error');
            $('[id^="error-login-"]').addClass('hidden');
            $('#login-global-error').addClass('hidden');
            $('#btn-login-submit').prop('disabled', true);

            document.getElementById('login_modal').showModal();
        }

        // Toggle Password Visibility (Updated for SVG)
        function togglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            const iconEye = btn.querySelector('#icon-eye');
            const iconSlash = btn.querySelector('#icon-eye-slash');

            if (input.type === "password") {
                input.type = "text";
                iconEye.classList.add('hidden');
                iconSlash.classList.remove('hidden');
            } else {
                input.type = "password";
                iconEye.classList.remove('hidden');
                iconSlash.classList.add('hidden');
            }
        }

        // --- VALIDASI LOGIN (CUSTOM MESSAGES) ---
        function validateLogin() {
            const email = $('#login-email').val().trim();
            const password = $('#login-password').val();
            const btn = $('#btn-login-submit');
            let isValid = true;

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            // 1. Validasi Email
            if (email === '') {
                // Jika kosong, anggap tidak valid tapi jangan munculkan error merah dulu (supaya bersih)
                isValid = false;
            } else if (!emailRegex.test(email)) {
                // Jika diketik tapi format salah -> Munculkan Error
                $('#error-login-email').text('Format email tidak valid').removeClass('hidden');
                isValid = false;
            } else {
                // Jika benar -> Sembunyikan Error
                $('#error-login-email').addClass('hidden');
            }

            // 2. Validasi Password
            if (password === '') {
                isValid = false;
            } else if (password.length < 6) {
                $('#error-login-password').text('Password minimal 6 karakter').removeClass('hidden');
                isValid = false;
            } else {
                $('#error-login-password').addClass('hidden');
            }

            // 3. Update Tombol Masuk
            if (isValid) {
                // Aktifkan Tombol
                btn.prop('disabled', false)
                    .removeClass('btn-disabled disabled:bg-blue-400 disabled:text-white disabled:border-blue-400');
            } else {
                // Matikan Tombol (Tetap Biru Pudar sesuai request sebelumnya)
                btn.prop('disabled', true)
                    .addClass('btn-disabled disabled:bg-blue-400 disabled:text-white disabled:border-blue-400');
            }
        }

        // Trigger Validation
        $('#login-email, #login-password').on('input', validateLogin);

        // --- 3. AJAX LOGIN SUBMISSION ---
        $('#form-login').on('submit', function(e) {
            e.preventDefault();

            const btn = $('#btn-login-submit');
            const loading = $('#login-loading');
            const globalError = $('#login-global-error');
            const errorMsg = $('#login-error-msg');

            // UI Loading State
            btn.prop('disabled', true);
            loading.removeClass('hidden');
            globalError.addClass('hidden');
            $('.input-error').removeClass('input-error');
            $('[id^="error-login-"]').addClass('hidden');

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    // Jika admin, dia akan ke /dashboard
                    // Jika customer, dia akan ke / (landing page)
                    window.location.href = response.redirect_url;
                },
                error: function(xhr) {
                    btn.prop('disabled', false);
                    loading.addClass('hidden');

                    if (xhr.status === 422) {
                        // Error Validasi Laravel (Email/Password salah format/salah data)
                        const errors = xhr.responseJSON.errors;

                        if (errors.email) {
                            $('#login-email').addClass('input-error');
                            $('#error-login-email').text(errors.email[0]).removeClass('hidden');
                        }
                        if (errors.password) {
                            $('#login-password').addClass('input-error');
                            $('#error-login-password').text(errors.password[0]).removeClass('hidden');
                        }
                    } else if (xhr.status === 401 || xhr.status === 429) {
                        // Error Auth (Salah password atau too many attempts)
                        // Pesan error dari Laravel biasanya ada di 'message' atau 'email' error bag
                        let msg = xhr.responseJSON.message || 'Email atau password salah.';
                        errorMsg.text(msg);
                        globalError.removeClass('hidden');
                    } else {
                        errorMsg.text('Terjadi kesalahan server. Coba lagi nanti.');
                        globalError.removeClass('hidden');
                    }
                }
            });
        });

        // --- SWITCH MODALS ---
        function switchToRegister() {
            document.getElementById('login_modal').close(); // Tutup Login
            setTimeout(() => {
                // Reset form register
                $('#form-register')[0].reset();
                $('.input-error').removeClass('input-error');
                $('[id^="error-reg-"]').addClass('hidden');
                $('#reg-global-error').addClass('hidden');
                $('#btn-reg-submit').prop('disabled', true);

                document.getElementById('register_modal').showModal(); // Buka Register
            }, 200); // Delay dikit biar transisi halus
        }

        function switchToLogin() {
            document.getElementById('register_modal').close();
            setTimeout(() => {
                openLoginModal(); // Fungsi yang sudah ada
            }, 200);
        }

        // --- VALIDASI REGISTER (CUSTOM MESSAGES) ---
        function validateRegister() {
            const name = $('#reg-name').val().trim();
            const email = $('#reg-email').val().trim();
            const password = $('#reg-password').val();
            const btn = $('#btn-reg-submit');
            let isValid = true;

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            // Validasi Nama
            if (name === '') {
                // Opsional: Tampilkan error jika kosong (saat ini logicnya hanya matikan tombol)
                isValid = false;
            } else if (name.length < 3) {
                $('#error-reg-name').text('Nama minimal 3 karakter').removeClass('hidden');
                isValid = false;
            } else {
                $('#error-reg-name').addClass('hidden');
            }

            // Validasi Email
            if (email === '') {
                isValid = false;
            } else if (!emailRegex.test(email)) {
                $('#error-reg-email').text('Format email tidak valid').removeClass('hidden');
                isValid = false;
            } else {
                $('#error-reg-email').addClass('hidden');
            }

            // Validasi Password
            if (password === '') {
                isValid = false;
            } else if (password.length < 8) {
                $('#error-reg-password').text('Password minimal 8 karakter').removeClass('hidden');
                isValid = false;
            } else {
                $('#error-reg-password').addClass('hidden');
            }

            // Update Tombol Submit
            if (isValid) {
                btn.prop('disabled', false).removeClass('btn-disabled disabled:bg-blue-400 disabled:text-white disabled:border-blue-400');
            } else {
                btn.prop('disabled', true).addClass('btn-disabled disabled:bg-blue-400 disabled:text-white disabled:border-blue-400');
            }
        }

        // Trigger Validation Register
        $('#reg-name, #reg-email, #reg-password').on('input', validateRegister);

        // --- AJAX REGISTER SUBMISSION ---
        $('#form-register').on('submit', function(e) {
            e.preventDefault();

            const btn = $('#btn-reg-submit');
            const loading = $('#reg-loading');
            const globalError = $('#reg-global-error');
            const errorMsg = $('#reg-error-msg');

            btn.prop('disabled', true);
            loading.removeClass('hidden');
            globalError.addClass('hidden');
            $('.input-error').removeClass('input-error');
            $('[id^="error-reg-"]').addClass('hidden');

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    // Sukses daftar -> Auto login -> Reload
                    window.location.reload();
                },
                error: function(xhr) {
                    btn.prop('disabled', false);
                    loading.addClass('hidden');

                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        if (errors.name) {
                            $('#reg-name').addClass('input-error');
                            $('#error-reg-name').text(errors.name[0]).removeClass('hidden');
                        }
                        if (errors.email) {
                            $('#reg-email').addClass('input-error');
                            $('#error-reg-email').text(errors.email[0]).removeClass('hidden');
                        }
                        if (errors.password) {
                            $('#reg-password').addClass('input-error');
                            $('#error-reg-password').text(errors.password[0]).removeClass('hidden');
                        }
                    } else {
                        errorMsg.text('Gagal mendaftar. Silakan coba lagi.');
                        globalError.removeClass('hidden');
                    }
                }
            });
        });
    </script>
    {{-- Auto Open Login Modal jika ada session --}}
    @if (session('open_modal'))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Panggil fungsi buka modal yang sudah kita buat
                openLoginModal();

                // Opsional: Tampilkan pesan error di dalam modal jika ada
                @if (session('error'))
                    // Masukkan pesan ke dalam alert error di modal login
                    $('#login-error-msg').text("{{ session('error') }}");
                    $('#login-global-error').removeClass('hidden');
                @endif
            });
        </script>
    @endif

    @stack('scripts')
</body>

</html>
