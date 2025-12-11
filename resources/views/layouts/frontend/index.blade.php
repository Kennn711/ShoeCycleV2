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
                        <a href="{{ url('/') }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all">
                            Beranda
                        </a>
                        <div class="dropdown dropdown-hover">
                            <div tabindex="0" role="button" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all flex items-center gap-1">
                                Belanja <i class="fas fa-chevron-down text-xs"></i>
                            </div>
                            <ul tabindex="0" class="dropdown-content menu p-2 shadow-xl bg-white rounded-xl w-56 border border-gray-100 mt-1">
                                <li><a href="#" class="text-sm">Semua Produk</a></li>
                                <li><a href="#" class="text-sm">Pria</a></li>
                                <li><a href="#" class="text-sm">Wanita</a></li>
                                <li><a href="#" class="text-sm">Anak-anak</a></li>
                            </ul>
                        </div>
                        <a href="#new-arrivals" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all">
                            Koleksi
                        </a>
                        <a href="#" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all">
                            Pesanan Saya
                        </a>
                    </div>
                </div>

                {{-- Right Actions --}}
                <div class="flex items-center gap-2">
                    {{-- Search --}}
                    <button class="btn btn-ghost btn-circle text-gray-600 hover:text-blue-600 hover:bg-blue-50 hidden sm:flex">
                        <i class="fas fa-search text-lg"></i>
                    </button>

                    {{-- Cart --}}
                    <div class="dropdown dropdown-end">
                        <div tabindex="0" role="button" class="btn btn-ghost btn-circle text-gray-600 hover:text-blue-600 hover:bg-blue-50">
                            <div class="indicator">
                                <i class="fas fa-shopping-bag text-lg"></i>
                                <span class="badge badge-sm bg-blue-600 text-white border-none indicator-item">0</span>
                            </div>
                        </div>
                        <div tabindex="0" class="dropdown-content mt-3 z-[1] card card-compact w-72 bg-white shadow-xl border border-gray-100 rounded-xl">
                            <div class="card-body">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="font-bold text-lg">Keranjang</span>
                                    <span class="text-sm text-gray-500">0 item</span>
                                </div>
                                <div class="text-center py-8">
                                    <i class="fas fa-shopping-bag text-4xl text-gray-300 mb-3"></i>
                                    <p class="text-sm text-gray-500">Keranjang masih kosong</p>
                                </div>
                                <button class="btn btn-primary btn-sm rounded-lg text-white">Mulai Belanja</button>
                            </div>
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
                            <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow-xl bg-white rounded-xl w-56 border border-gray-100">
                                <li class="menu-title px-4 py-2">
                                    <span class="text-xs text-gray-500">Hai, {{ Auth::user()->name }}</span>
                                </li>
                                <li><a href="#" class="text-sm"><i class="fas fa-user mr-2 w-4"></i> Profil Saya</a></li>
                                <li><a href="#" class="text-sm"><i class="fas fa-box-open mr-2 w-4"></i> Pesanan Saya</a></li>
                                <li class="border-t border-gray-100 mt-1 pt-1">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="text-red-500 hover:bg-red-50 w-full text-left text-sm">
                                            <i class="fas fa-sign-out-alt mr-2 w-4"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary btn-sm rounded-lg text-white text-sm px-6 ml-2 hidden sm:flex">
                            Masuk
                        </a>
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
                        <a href="#" class="w-10 h-10 rounded-lg bg-gray-800 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all group">
                            <i class="bi bi-facebook group-hover:scale-110 transition-transform"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-lg bg-gray-800 flex items-center justify-center hover:bg-pink-600 hover:text-white transition-all group">
                            <i class="fab fa-instagram group-hover:scale-110 transition-transform"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-lg bg-gray-800 flex items-center justify-center hover:bg-green-500 hover:text-white transition-all group">
                            <i class="fab fa-whatsapp group-hover:scale-110 transition-transform"></i>
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
                    <i class="fab fa-cc-visa text-2xl text-gray-600 hover:text-white transition-colors"></i>
                    <i class="fab fa-cc-mastercard text-2xl text-gray-600 hover:text-white transition-colors"></i>
                    <i class="fas fa-wallet text-2xl text-gray-600 hover:text-white transition-colors"></i>
                </div>
            </div>
        </div>
    </footer>

    {{-- ========================================================= --}}
    {{-- SCRIPTS                                                   --}}
    {{-- ========================================================= --}}
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

    @stack('scripts')
</body>

</html>
