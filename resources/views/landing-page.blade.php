@extends('layouts/frontend/index')
@section('title', 'ShoeCycle | Langkah Baru, Gaya Baru')

@section('frontend-content')

    {{-- 1. HERO SECTION (Dengan Animasi Floating) --}}
    <section class="relative bg-gradient-to-br from-gray-50 to-blue-50 overflow-hidden">
        {{-- Background Pattern --}}
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-blue-100 opacity-50 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-purple-100 opacity-50 blur-3xl"></div>

        <div class="container mx-auto px-4 py-20 md:py-32 relative z-10">
            <div class="flex flex-col md:flex-row items-center">
                {{-- Text Content --}}
                <div class="w-full md:w-1/2 text-center md:text-left mb-12 md:mb-0" data-aos="fade-right">
                    <span class="px-4 py-2 rounded-full bg-blue-100 text-blue-700 text-sm font-bold tracking-wide uppercase mb-4 inline-block">
                        New Collection 2024
                    </span>
                    <h1 class="text-5xl md:text-7xl font-extrabold text-gray-900 leading-tight mb-6">
                        Step Up <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">
                            Your Game
                        </span>
                    </h1>
                    <p class="text-gray-600 text-lg mb-8 leading-relaxed max-w-lg mx-auto md:mx-0">
                        Temukan koleksi sepatu eksklusif dari brand ternama. Nyaman dipakai, stylish dilihat, dan siap menemani setiap langkahmu.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                        <a href="#new-arrivals" class="btn btn-primary btn-lg border-none bg-blue-600 hover:bg-blue-700 shadow-lg hover:shadow-blue-500/30 text-white px-8">
                            Belanja Sekarang <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                        <a href="#categories" class="btn btn-outline btn-lg hover:bg-gray-100 px-8">
                            Lihat Kategori
                        </a>
                    </div>
                </div>

                {{-- Hero Image (Floating Animation) --}}
                <div class="w-full md:w-1/2 flex justify-center relative">
                    <div class="relative w-full max-w-lg">
                        {{-- Lingkaran dekorasi di belakang sepatu --}}
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-200 to-purple-200 rounded-full blur-2xl opacity-60 transform scale-90"></div>

                        {{-- Ganti src ini dengan gambar sepatu andalan (PNG Transparent recommended) --}}
                        {{-- Jika belum ada, pakai placeholder dulu --}}
                        <img src="https://pngimg.com/d/running_shoes_PNG5823.png" alt="Hero Shoe" class="relative z-10 w-full h-auto drop-shadow-2xl floating-shoe transform -rotate-12 hover:rotate-0 transition-all duration-500">
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 2. BRAND MARQUEE (Logo Brand) --}}
    <div class="bg-white border-y border-gray-100 py-8 overflow-hidden">
        <div class="container mx-auto px-4">
            <p class="text-center text-gray-400 text-sm font-semibold uppercase tracking-widest mb-6">Trusted by World Class Brands</p>
            <div class="flex flex-wrap justify-center gap-8 md:gap-16 opacity-60 grayscale hover:grayscale-0 transition-all duration-500">
                @foreach ($brands as $brand)
                    <span class="text-2xl font-bold text-gray-800 font-serif">{{ $brand }}</span>
                @endforeach
            </div>
        </div>
    </div>

    {{-- 3. CATEGORY SECTION --}}
    <section id="categories" class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Kategori Pilihan</h2>
                <p class="text-gray-500 max-w-2xl mx-auto">Temukan sepatu yang sesuai dengan aktivitas dan gaya hidupmu.</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach ($categories as $cat)
                    <a href="#" class="group bg-white p-6 rounded-2xl shadow-sm hover:shadow-md border border-gray-100 text-center transition-all hover:-translate-y-1">
                        <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                            {{-- Ikon dinamis sederhana berdasarkan nama --}}
                            @if ($cat->category_name == 'Running')
                                <i class="fas fa-running text-xl"></i>
                            @elseif($cat->category_name == 'Basketball')
                                <i class="fas fa-basketball-ball text-xl"></i>
                            @else
                                <i class="fas fa-shoe-prints text-xl"></i>
                            @endif
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-1">{{ $cat->category_name }}</h3>
                        <p class="text-xs text-gray-400">{{ $cat->shoes_count }} Produk</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- 4. NEW ARRIVALS (Produk dari Database) --}}
    <section id="new-arrivals" class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-end mb-10">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Pendatang Baru</h2>
                    <p class="text-gray-500">Koleksi terbaru minggu ini.</p>
                </div>
                <a href="#" class="text-blue-600 font-semibold hover:text-blue-700 flex items-center gap-2">
                    Lihat Semua <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @forelse($newArrivals as $shoe)
                    {{-- Logic ambil gambar utama --}}
                    @php
                        // Ambil varian pertama
                        $firstVariant = $shoe->variants->first();
                        // Ambil gambar utama dari varian pertama
                        $image = $firstVariant ? $firstVariant->images->where('is_primary', true)->first() : null;
                        $imageUrl = $image ? asset('storage/' . $image->image_path) : asset('assets/upload/testing/dummy.jpg');

                        // Hitung harga (sudah ada accessor di model, tapi untuk contoh kita pakai raw)
                        $price = $firstVariant ? number_format($firstVariant->price, 0, ',', '.') : '-';
                    @endphp

                    {{-- Product Card --}}
                    <div class="card bg-white border border-gray-100 hover:shadow-xl transition-all duration-300 group">
                        <figure class="px-4 pt-4 relative overflow-hidden h-64 bg-gray-50">
                            {{-- Badge New --}}
                            <div class="absolute top-4 left-4 z-10">
                                <span class="badge badge-primary badge-sm">New</span>
                            </div>

                            {{-- Image --}}
                            <img src="{{ $imageUrl }}" alt="{{ $shoe->name }}" class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-500" />

                            {{-- Quick Action Button (Muncul saat hover) --}}
                            <div class="absolute bottom-4 right-4 translate-y-20 group-hover:translate-y-0 transition-transform duration-300">
                                <button class="btn btn-circle btn-sm bg-white hover:bg-blue-600 hover:text-white border shadow-md" title="Add to Cart">
                                    <i class="fas fa-shopping-cart"></i>
                                </button>
                            </div>
                        </figure>

                        <div class="card-body p-5">
                            <div class="text-xs text-gray-400 font-semibold uppercase mb-1">{{ $shoe->brand_name }}</div>
                            <h3 class="card-title text-base font-bold text-gray-900 mb-2 h-12 line-clamp-2">
                                <a href="#" class="hover:text-blue-600 transition-colors">{{ $shoe->name }}</a>
                            </h3>

                            <div class="flex justify-between items-center mt-auto">
                                <div class="text-lg font-bold text-blue-600">
                                    <small class="text-xs text-gray-500 font-normal">Mulai</small>
                                    Rp {{ $price }}
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <p class="text-gray-500">Belum ada produk baru.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- 5. PROMO BANNER --}}
    <section class="py-10 px-4">
        <div class="container mx-auto rounded-3xl bg-gray-900 overflow-hidden relative">
            <div class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-blue-900 to-transparent opacity-50"></div>

            <div class="grid grid-cols-1 md:grid-cols-2 items-center relative z-10 p-8 md:p-16">
                <div class="text-center md:text-left">
                    <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Gratis Ongkir se-Mojokerto!</h2>
                    <p class="text-gray-300 mb-8 text-lg">Khusus pembelian di atas Rp 500.000 menggunakan driver lokal kami. Cepat, aman, dan hemat.</p>
                    <button class="btn bg-white text-gray-900 hover:bg-gray-100 border-none px-8">Belanja Sekarang</button>
                </div>
                <div class="hidden md:flex justify-center">
                    <i class="fas fa-shipping-fast text-9xl text-blue-500 opacity-20 transform -rotate-12"></i>
                </div>
            </div>
        </div>
    </section>

    {{-- 6. FAQ SECTION (Accordion) --}}
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-4 max-w-3xl">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Pertanyaan Umum (FAQ)</h2>
                <p class="text-gray-500">Jawaban untuk pertanyaan yang sering diajukan pelanggan.</p>
            </div>

            <div class="join join-vertical w-full bg-white shadow-sm rounded-xl border border-gray-100">

                {{-- Item 1 --}}
                <div class="collapse collapse-plus join-item border-b border-gray-100">
                    <input type="radio" name="my-accordion-4" checked="checked" />
                    <div class="collapse-title text-lg font-medium text-gray-800">
                        Bagaimana cara menghitung ongkos kirim?
                    </div>
                    <div class="collapse-content text-gray-600">
                        <p>Untuk area Mojokerto, kami menggunakan driver lokal dengan perhitungan tarif berdasarkan radius jarak dari toko kami. Anda bisa melihat estimasi ongkir saat checkout.</p>
                    </div>
                </div>

                {{-- Item 2 --}}
                <div class="collapse collapse-plus join-item border-b border-gray-100">
                    <input type="radio" name="my-accordion-4" />
                    <div class="collapse-title text-lg font-medium text-gray-800">
                        Apakah produk di ShoeCycle 100% Original?
                    </div>
                    <div class="collapse-content text-gray-600">
                        <p>Ya, kami menjamin keaslian semua produk (100% Original Authentic). Kami bekerja sama langsung dengan distributor resmi brand terkait.</p>
                    </div>
                </div>

                {{-- Item 3 --}}
                <div class="collapse collapse-plus join-item border-b border-gray-100">
                    <input type="radio" name="my-accordion-4" />
                    <div class="collapse-title text-lg font-medium text-gray-800">
                        Berapa lama proses pengiriman?
                    </div>
                    <div class="collapse-content text-gray-600">
                        <p>Untuk pengiriman lokal (Mojokerto), pesanan yang dikonfirmasi sebelum jam 14.00 akan dikirim di hari yang sama (Same Day Delivery). Untuk luar kota menyesuaikan ekspedisi.</p>
                    </div>
                </div>

                {{-- Item 4 --}}
                <div class="collapse collapse-plus join-item">
                    <input type="radio" name="my-accordion-4" />
                    <div class="collapse-title text-lg font-medium text-gray-800">
                        Apakah bisa tukar ukuran jika tidak pas?
                    </div>
                    <div class="collapse-content text-gray-600">
                        <p>Tentu! Kami menyediakan garansi tukar size maksimal 3 hari setelah barang diterima, selama tag belum dilepas dan kondisi sepatu belum dipakai jalan (hanya fitting).</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

@endsection

@push('styles')
    <style>
        /* Animasi Sepatu Melayang di Hero */
        @keyframes float {
            0% {
                transform: translateY(0px) rotate(-12deg);
            }

            50% {
                transform: translateY(-20px) rotate(-10deg);
            }

            100% {
                transform: translateY(0px) rotate(-12deg);
            }
        }

        .floating-shoe {
            animation: float 6s ease-in-out infinite;
        }

        /* Smooth Scroll */
        html {
            scroll-behavior: smooth;
        }
    </style>
@endpush
