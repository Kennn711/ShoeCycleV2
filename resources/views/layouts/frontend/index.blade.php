<!DOCTYPE html>
<html lang="en" data-theme="light" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'ShoeCycle')</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/upload/logo/logo.png') }}">

    {{-- 1. Google Fonts (Inter & Poppins) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">

    {{-- 2. FontAwesome (Untuk Ikon) --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome/css/all.min.css') }}">

    {{-- 3. AOS Animation CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/aos/aos.css') }}">

    {{-- 4. Vite (Tailwind + Custom CSS/JS) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Custom Font Settings */
        body {
            font-family: 'Inter', sans-serif;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        .font-heading {
            font-family: 'Poppins', sans-serif;
        }
    </style>
    @stack('styles')
</head>

<body class="flex flex-col min-h-screen bg-white text-gray-800">

    {{-- ========================================================= --}}
    {{-- NAVBAR SECTION                                            --}}
    {{-- ========================================================= --}}
    <div class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-gray-100 shadow-sm transition-all duration-300" id="navbar">
        <div class="navbar container mx-auto px-4">

            {{-- Navbar Start: Logo & Mobile Menu --}}
            <div class="navbar-start">
                {{-- Dropdown Mobile --}}
                <div class="dropdown">
                    <div tabindex="0" role="button" class="btn btn-ghost lg:hidden">
                        <i class="fas fa-bars text-xl"></i>
                    </div>
                    <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                        <li><a href="{{ url('/') }}">Beranda</a></li>
                        <li><a href="#categories">Kategori</a></li>
                        <li><a href="#new-arrivals">Terbaru</a></li>
                        <li><a href="#">Tentang Kami</a></li>
                    </ul>
                </div>

                {{-- Logo --}}
                <a href="{{ route('landing-page') }}" class="btn btn-ghost text-xl font-heading font-bold text-blue-600">
                    <img src="{{ asset('assets/upload/logo/logo.png') }}" alt="Logo" class="w-8 h-8 mr-2 object-contain">
                    ShoeCycle
                </a>
            </div>

            {{-- Navbar Center: Desktop Menu --}}
            <div class="navbar-center hidden lg:flex">
                <ul class="menu menu-horizontal px-1 font-medium text-gray-600">
                    <li><a href="{{ url('/') }}" class="hover:text-blue-600">Beranda</a></li>
                    <li>
                        <details>
                            <summary class="hover:text-blue-600">Belanja</summary>
                            <ul class="p-2 bg-white shadow-lg rounded-xl border border-gray-100 min-w-[200px]">
                                <li><a href="#">Semua Produk</a></li>
                                <li><a href="#">Pria</a></li>
                                <li><a href="#">Wanita</a></li>
                                <li><a href="#">Anak-anak</a></li>
                            </ul>
                        </details>
                    </li>
                    <li><a href="#new-arrivals" class="hover:text-blue-600">Terbaru</a></li>
                    <li><a href="#" class="hover:text-blue-600">Lacak Pesanan</a></li>
                </ul>
            </div>

            {{-- Navbar End: Search, Cart, Profile --}}
            <div class="navbar-end gap-2">
                {{-- Search Button (Ghost) --}}
                <button class="btn btn-ghost btn-circle text-gray-500 hover:text-blue-600">
                    <i class="fas fa-search text-lg"></i>
                </button>

                {{-- Cart Button --}}
                <div class="dropdown dropdown-end">
                    <div tabindex="0" role="button" class="btn btn-ghost btn-circle text-gray-500 hover:text-blue-600">
                        <div class="indicator">
                            <i class="fas fa-shopping-bag text-lg"></i>
                            <span class="badge badge-sm badge-primary indicator-item">0</span>
                        </div>
                    </div>
                    <div tabindex="0" class="mt-3 z-[1] card card-compact dropdown-content w-52 bg-base-100 shadow border border-gray-100">
                        <div class="card-body">
                            <span class="font-bold text-lg">0 Barang</span>
                            <span class="text-info">Subtotal: Rp 0</span>
                            <div class="card-actions">
                                <button class="btn btn-primary btn-block btn-sm text-white">Lihat Keranjang</button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Auth Buttons --}}
                @auth
                    {{-- Jika User Login --}}
                    <div class="dropdown dropdown-end">
                        <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar border border-gray-200">
                            <div class="w-10 rounded-full">
                                {{-- Gunakan foto profil user atau default --}}
                                <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=0D8ABC&color=fff" alt="User" />
                            </div>
                        </div>
                        <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52 border border-gray-100">
                            <li class="menu-title px-4 py-2">Hai, {{ Auth::user()->name }}</li>
                            <li><a href="#"><i class="fas fa-user mr-2"></i> Profil Saya</a></li>
                            <li><a href="#"><i class="fas fa-box-open mr-2"></i> Pesanan Saya</a></li>
                            <li class="border-t border-gray-100 mt-1 pt-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="text-red-500 hover:bg-red-50 w-full text-left"><i class="fas fa-sign-out-alt mr-2"></i> Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    {{-- Jika Belum Login --}}
                    <a href="{{ route('login') }}" class="btn btn-primary btn-sm px-5 rounded-full text-white ml-2">
                        Login
                    </a>
                @endauth
            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- MAIN CONTENT                                              --}}
    {{-- ========================================================= --}}
    <main class="flex-grow">
        @yield('frontend-content')
    </main>

    {{-- ========================================================= --}}
    {{-- FOOTER SECTION                                            --}}
    {{-- ========================================================= --}}
    <footer class="bg-gray-900 text-gray-300 pt-16 pb-8">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">

                {{-- Brand Info --}}
                <div class="col-span-1 md:col-span-1">
                    <a href="#" class="text-2xl font-bold text-white font-heading flex items-center gap-2 mb-4">
                        <img src="{{ asset('assets/upload/logo/logo.png') }}" alt="Logo" class="w-8 h-8 bg-white rounded-full p-1">
                        ShoeCycle
                    </a>
                    <p class="text-sm leading-relaxed mb-6">
                        Platform e-commerce sepatu terpercaya dengan layanan pengiriman lokal tercepat di Mojokerto. Kualitas original, harga bersahabat.
                    </p>
                    <div class="flex gap-4">
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-colors"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-pink-600 hover:text-white transition-colors"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-green-500 hover:text-white transition-colors"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>

                {{-- Links 1 --}}
                <div>
                    <h3 class="text-white font-bold mb-4 uppercase text-sm tracking-wider">Belanja</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white transition-colors">Semua Produk</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Pria</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Wanita</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Diskon</a></li>
                    </ul>
                </div>

                {{-- Links 2 --}}
                <div>
                    <h3 class="text-white font-bold mb-4 uppercase text-sm tracking-wider">Bantuan</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white transition-colors">Lacak Pesanan</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Syarat & Ketentuan</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Kebijakan Privasi</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Hubungi Kami</a></li>
                    </ul>
                </div>

                {{-- Newsletter --}}
                <div>
                    <h3 class="text-white font-bold mb-4 uppercase text-sm tracking-wider">Tetap Terhubung</h3>
                    <p class="text-sm mb-4">Dapatkan info promo terbaru langsung ke inboxmu.</p>
                    <div class="join w-full">
                        <input class="input input-bordered input-sm join-item w-full bg-gray-800 border-gray-700 text-white" placeholder="Email kamu..." />
                        <button class="btn btn-primary btn-sm join-item text-white">Join</button>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center text-sm">
                <p>&copy; {{ date('Y') }} ShoeCycle. All rights reserved.</p>
                <div class="flex gap-4 mt-4 md:mt-0">
                    <i class="fab fa-cc-visa text-2xl text-gray-500 hover:text-white"></i>
                    <i class="fab fa-cc-mastercard text-2xl text-gray-500 hover:text-white"></i>
                    <i class="fas fa-wallet text-2xl text-gray-500 hover:text-white"></i>
                </div>
            </div>
        </div>
    </footer>

    {{-- ========================================================= --}}
    {{-- SCRIPTS                                                   --}}
    {{-- ========================================================= --}}

    {{-- AOS Animation JS --}}
    <script src="assets/vendor/aos/aos.js"></script>
    <script>
        // Init AOS
        AOS.init({
            duration: 800,
            once: true,
            offset: 100,
        });

        // Navbar Scroll Effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('shadow-md');
                navbar.classList.replace('bg-white/80', 'bg-white/95');
            } else {
                navbar.classList.remove('shadow-md');
                navbar.classList.replace('bg-white/95', 'bg-white/80');
            }
        });
    </script>

    @stack('scripts')
</body>

</html>
