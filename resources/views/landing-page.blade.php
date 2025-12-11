@extends('layouts/frontend/index')
@section('title', 'ShoeCycle')

@section('frontend-content')
    {{-- 1. HERO SECTION (Updated Size) --}}
    <section class="relative bg-gradient-to-br from-gray-50 to-blue-50 overflow-hidden min-h-[90vh] flex items-center">
        {{-- Background Pattern --}}
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-blue-100 opacity-50 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-purple-100 opacity-50 blur-3xl"></div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="flex flex-col md:flex-row items-center">
                {{-- Text Content --}}
                <div class="w-full md:w-5/12 text-center md:text-left mb-16 md:mb-0 z-20" data-aos="fade-right">
                    <span class="px-4 py-2 rounded-full bg-blue-100 text-blue-700 text-sm font-bold tracking-wide uppercase mb-6 inline-block shadow-sm">
                        • ShoeCycle 2026
                    </span>
                    <h1 class="text-5xl md:text-7xl lg:text-8xl font-extrabold text-gray-900 leading-tight mb-8 font-heading">
                        Langkah Baru, <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">
                            Gaya Baru
                        </span>
                    </h1>
                    <p class="text-gray-600 text-lg md:text-xl mb-10 leading-relaxed max-w-lg mx-auto md:mx-0 font-light">
                        Temukan koleksi sepatu eksklusif dari brand ternama. Nyaman dipakai, stylish dilihat, dan siap menemani setiap langkahmu.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                        <a href="#new-arrivals" class="btn btn-primary btn-lg border-none bg-blue-600 hover:bg-blue-700 shadow-xl shadow-blue-200 text-white px-10 h-14 rounded-full font-bold">
                            Belanja Sekarang <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                        <a href="#categories" class="btn btn-outline btn-lg hover:bg-white px-10 h-14 rounded-full border-gray-300 font-medium">
                            Lihat Kategori
                        </a>
                    </div>
                </div>

                {{-- Hero Image (Updated Size: Bigger) --}}
                {{-- Container diperlebar jadi w-7/12 --}}
                <div class="w-full md:w-7/12 flex justify-center relative mt-12 md:mt-0" data-aos="fade-left" data-aos-duration="1000">
                    {{-- Max-width container diperbesar jadi max-w-2xl --}}
                    <div class="relative w-full max-w-2xl mx-auto">

                        {{-- Background Glow diperbesar --}}
                        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[140%] h-[140%]">
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-400/20 to-purple-600/20 rounded-full blur-3xl animate-pulse"></div>
                        </div>

                        {{-- Shoe Image --}}
                        <div class="relative z-10 floating-shoe">
                            {{-- Scale diperbesar sedikit di CSS atau class --}}
                            <img src="{{ asset('assets/upload/logo/shoes-hero-section.png') }}" alt="Hero Shoe" class="w-full h-auto drop-shadow-2xl transform -rotate-12 hover:rotate-0 transition-all duration-700 scale-110">
                        </div>

                        {{-- Badges (Posisi disesuaikan agar tidak tertutup gambar besar) --}}
                        <div class="absolute -top-8 -right-8 md:top-10 md:-right-4 bg-white/90 backdrop-blur-md rounded-2xl shadow-xl p-5 animate-float border border-white/60 z-20" style="animation-delay: 0.5s;">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 bg-green-100 rounded-2xl flex items-center justify-center text-green-600 shadow-inner">
                                    <i class="fas fa-shipping-fast text-2xl"></i>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Pengiriman</div>
                                    <div class="font-bold text-gray-900 text-lg">Same Day</div>
                                </div>
                            </div>
                        </div>

                        <div class="absolute -bottom-8 -left-8 md:bottom-20 md:left-0 bg-white/90 backdrop-blur-md rounded-2xl shadow-xl p-5 animate-float border border-white/60 z-20" style="animation-delay: 1.5s;">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center text-blue-600 shadow-inner">
                                    <i class="fas fa-shield-alt text-2xl"></i>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Garansi</div>
                                    <div class="font-bold text-gray-900 text-lg">100% Original</div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ========================================================= --}}
    {{-- 2. BRAND MARQUEE - Trusted Partners                      --}}
    {{-- ========================================================= --}}
    <section class="bg-white border-y border-gray-100 py-12">
        <div class="container mx-auto px-4">
            <p class="text-center text-gray-400 text-xs font-bold uppercase tracking-widest mb-8">
                Dipercaya oleh Brand Kelas Dunia
            </p>
            <div class="flex flex-wrap justify-center items-center gap-8 md:gap-16">
                @foreach ($brands as $brand)
                    <div class="group">
                        <span class="text-2xl md:text-3xl font-bold text-gray-300 group-hover:text-gray-900 transition-colors duration-300 font-heading">
                            {{ $brand }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ========================================================= --}}
    {{-- 3. CATEGORY SECTION - Interactive Cards                  --}}
    {{-- ========================================================= --}}
    <section id="categories" class="py-20 bg-slate-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                    Kategori Pilihan
                </h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Temukan sepatu yang sesuai dengan aktivitas dan gaya hidupmu
                </p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach ($categories as $index => $cat)
                    <a href="#" class="group bg-white p-6 rounded-2xl shadow-sm hover:shadow-xl border border-gray-100 hover:border-blue-200 text-center transition-all duration-300 hover:-translate-y-2" data-aos="fade-up" data-aos-delay="{{ $index * 50 }}">

                        <div class="w-16 h-16 bg-gradient-to-br from-blue-50 to-purple-50 text-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:from-blue-600 group-hover:to-purple-600 group-hover:text-white transition-all duration-300 group-hover:scale-110 group-hover:rotate-3">
                            @if ($cat->category_name == 'Running')
                                <i class="fas fa-running text-2xl"></i>
                            @elseif($cat->category_name == 'Basketball')
                                <i class="fas fa-basketball-ball text-2xl"></i>
                            @elseif($cat->category_name == 'Sneakers')
                                <i class="fas fa-shoe-prints text-2xl"></i>
                            @elseif($cat->category_name == 'Formal')
                                <i class="fas fa-user-tie text-2xl"></i>
                            @elseif($cat->category_name == 'Casual')
                                <i class="fas fa-walking text-2xl"></i>
                            @else
                                <i class="fas fa-shoe-prints text-2xl"></i>
                            @endif
                        </div>

                        <h3 class="font-bold text-gray-900 mb-1 group-hover:text-blue-600 transition-colors">
                            {{ $cat->category_name }}
                        </h3>
                        <p class="text-xs text-gray-400">{{ $cat->shoes_count }} Produk</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- 4. NEW ARRIVALS (Updated Design) --}}
    <section id="new-arrivals" class="py-24 bg-white">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-end mb-12 gap-4">
                <div>
                    <h2 class="text-4xl font-bold text-gray-900 mb-3 font-heading">Koleksi Terbaru</h2>
                    <p class="text-gray-500 text-lg">Pilihan eksklusif untuk gaya Anda.</p>
                </div>
                <a href="#" class="btn btn-outline rounded-lg text-white px-8 bg-blue-500 hover:bg-blue-600  border-gray-300">
                    Lihat Semua
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @forelse($newArrivals as $index => $shoe)
                    @php
                        $firstVariant = $shoe->variants->first();
                        $image = $firstVariant ? $firstVariant->images->where('is_primary', true)->first() : null;
                        $imageUrl = $image ? asset('storage/' . $image->image_path) : asset('assets/upload/testing/dummy.jpg');
                        $price = $firstVariant ? number_format($firstVariant->price, 0, ',', '.') : '-';

                        $soldCount = rand(50, 500);
                        $rating = 4.8;
                    @endphp

                    {{-- Product Card --}}
                    <div class="group bg-white rounded-3xl border border-gray-100 hover:border-blue-200 hover:shadow-xl hover:shadow-blue-50 transition-all duration-300 overflow-hidden h-full flex flex-col relative" data-aos="fade-up" data-aos-delay="{{ $index * 50 }}">

                        {{-- Image Container --}}
                        <div class="relative bg-gray-50 flex items-center justify-center overflow-hidden">
                            <img src="{{ $imageUrl }}" alt="{{ $shoe->name }}" class="w-full h-full rounded-[4rem] object-contain p-4 mix-blend-multiply" />
                        </div>

                        {{-- Content --}}
                        <div class="p-5 flex flex-col flex-grow relative">
                            {{-- Category --}}
                            <div class="text-[14px] font-bold text-blue-600 uppercase mb-2">
                                {{ $shoe->category->category_name ?? 'Shoes' }}
                            </div>

                            {{-- Name --}}
                            <h3 class="card-title text-lg font-bold text-gray-900 mb-3 leading-snug group-hover:text-blue-600 transition-colors duration-300">
                                <a href="#" class="line-clamp-2">{{ $shoe->name }}</a>
                            </h3>

                            {{-- Row: Rating, Sold & Button (UPDATED) --}}
                            {{-- justify-between mendorong elemen kiri dan kanan berjauhan --}}
                            <div class="flex items-center justify-between mb-6">

                                {{-- Kiri: Rating & Terjual --}}
                                <div class="flex items-center gap-3 text-xs text-gray-500">
                                    <div class="flex items-center gap-1 bg-amber-50 px-2 py-1 rounded-md border border-amber-100">
                                        <i class="fas fa-star text-amber-400 text-[10px]"></i>
                                        <span class="font-bold text-amber-700">{{ $rating }}</span>
                                    </div>
                                    <div class="w-1 h-1 bg-gray-300 rounded-full"></div>
                                    <div class="font-medium">{{ $soldCount }} Terjual</div>
                                </div>

                                {{-- Kanan: Button Lihat (Dipindah kesini) --}}
                                <a href="#" class="btn btn-sm rounded-full bg-blue-500 hover:bg-blue-600 text-white border-none px-4 font-normal shadow-sm transition-colors duration-300">
                                    Lihat Selengkapnya
                                </a>
                            </div>

                            {{-- Footer: Price Only --}}
                            <div class="mt-auto pt-4 border-t border-gray-100">
                                <p class="text-[10px] text-gray-400 uppercase font-semibold mb-0.5">Harga</p>
                                <div class="text-lg font-bold text-gray-900 leading-none">
                                    {{ $shoe->price_display }}
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-20 text-center">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-box-open text-3xl text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">Belum ada produk</h3>
                        <p class="text-gray-500 text-sm">Nantikan koleksi terbaru kami segera.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- ========================================================= --}}
    {{-- 5. PROMO BANNER - Eye-catching                           --}}
    {{-- ========================================================= --}}
    <section class="py-16 px-4">
        <div class="container mx-auto">
            <div class="relative rounded-3xl bg-gradient-to-br from-blue-600 via-blue-700 to-purple-700 overflow-hidden shadow-2xl" data-aos="zoom-in">
                {{-- Decorative Pattern --}}
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute top-0 right-0 w-96 h-96 bg-white rounded-full blur-3xl"></div>
                    <div class="absolute bottom-0 left-0 w-96 h-96 bg-white rounded-full blur-3xl"></div>
                </div>

                <div class="relative grid grid-cols-1 lg:grid-cols-2 items-center gap-8 p-8 md:p-16">
                    {{-- Text Content --}}
                    <div class="text-center lg:text-left text-white space-y-6">
                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full">
                            <i class="fas fa-shipping-fast text-amber-400"></i>
                            <span class="text-sm font-bold">Promo Spesial</span>
                        </div>

                        <h2 class="text-3xl md:text-5xl font-bold leading-tight">
                            Gratis Ongkir<br>
                            se-Mojokerto!
                        </h2>

                        <p class="text-lg text-blue-100 leading-relaxed max-w-md">
                            Khusus pembelian di atas Rp 500.000 menggunakan driver lokal kami. Cepat, aman, dan hemat.
                        </p>

                        <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                            <button class="px-8 py-4 bg-white text-blue-700 font-bold rounded-xl hover:bg-blue-50 transition-all hover:-translate-y-1 shadow-lg">
                                Belanja Sekarang
                            </button>
                            <button class="px-8 py-4 bg-white/10 backdrop-blur-sm text-white font-bold rounded-xl border-2 border-white/30 hover:bg-white/20 transition-all">
                                Lihat Syarat & Ketentuan
                            </button>
                        </div>
                    </div>

                    {{-- Illustration --}}
                    <div class="flex justify-center lg:justify-end">
                        <div class="relative w-64 h-64 md:w-80 md:h-80">
                            <div class="absolute inset-0 bg-white/10 backdrop-blur-sm rounded-full animate-pulse"></div>
                            <div class="absolute inset-8 bg-white/20 backdrop-blur-sm rounded-full animate-pulse animation-delay-1000"></div>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <i class="fas fa-shipping-fast text-9xl text-white/30 transform rotate-12"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ========================================================= --}}
    {{-- 6. FAQ SECTION - Improved Accordion                      --}}
    {{-- ========================================================= --}}
    <section class="py-20 bg-slate-50">
        <div class="container mx-auto px-4 max-w-4xl">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                    Pertanyaan Umum
                </h2>
                <p class="text-lg text-gray-600">
                    Jawaban untuk pertanyaan yang sering diajukan pelanggan
                </p>
            </div>

            <div class="space-y-4">
                @php
                    $faqs = [
                        [
                            'question' => 'Bagaimana cara menghitung ongkos kirim?',
                            'answer' => 'Untuk area Mojokerto, kami menggunakan driver lokal dengan perhitungan tarif berdasarkan radius jarak dari toko kami. Anda bisa melihat estimasi ongkir saat checkout.',
                        ],
                        [
                            'question' => 'Apakah produk di ShoeCycle 100% Original?',
                            'answer' => 'Ya, kami menjamin keaslian semua produk (100% Original Authentic). Kami bekerja sama langsung dengan distributor resmi brand terkait.',
                        ],
                        [
                            'question' => 'Berapa lama proses pengiriman?',
                            'answer' => 'Untuk pengiriman lokal (Mojokerto), pesanan yang dikonfirmasi sebelum jam 14.00 akan dikirim di hari yang sama (Same Day Delivery). Untuk luar kota menyesuaikan ekspedisi.',
                        ],
                        [
                            'question' => 'Apakah bisa tukar ukuran jika tidak pas?',
                            'answer' => 'Tentu! Kami menyediakan garansi tukar size maksimal 3 hari setelah barang diterima, selama tag belum dilepas dan kondisi sepatu belum dipakai jalan (hanya fitting).',
                        ],
                    ];
                @endphp

                @foreach ($faqs as $index => $faq)
                    <div class="bg-white rounded-2xl border border-gray-100 hover:border-gray-200 hover:shadow-lg transition-all overflow-hidden" data-aos="fade-up" data-aos-delay="{{ $index * 50 }}">
                        <div class="collapse collapse-plus">
                            <input type="radio" name="faq-accordion" @if ($index === 0) checked @endif />
                            <div class="collapse-title text-lg font-bold text-gray-900 pr-16">
                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center flex-shrink-0 mt-1">
                                        <i class="fas fa-question text-lg"></i>
                                    </div>
                                    <span class="flex-1">{{ $faq['question'] }}</span>
                                </div>
                            </div>
                            <div class="collapse-content text-gray-600">
                                <div class="pl-14 pr-4 pb-2">
                                    <p class="leading-relaxed">{{ $faq['answer'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Contact Support --}}
            <div class="mt-12 text-center" data-aos="fade-up">
                <p class="text-gray-600 mb-4">Masih ada pertanyaan lain?</p>

                {{-- TOMBOL YANG DIPERBAIKI --}}
                <a href="https://wa.me/6285162698173?text=Halo,%20saya%20ingin%20bertanya" target="_blank" class="inline-flex items-center gap-3 px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-blue-600/30 group">
                    <svg class="w-6 h-6 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                    </svg>
                    <span>Hubungi Customer Service</span>
                </a>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        /* Floating Animation */
        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) rotate(-12deg);
            }

            50% {
                transform: translateY(-20px) rotate(-10deg);
            }
        }

        .floating-shoe {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float-badge {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .animate-float {
            animation: float-badge 3s ease-in-out infinite;
        }

        /* Blob Animation */
        @keyframes blob {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            25% {
                transform: translate(20px, -50px) scale(1.1);
            }

            50% {
                transform: translate(-20px, 20px) scale(0.9);
            }

            75% {
                transform: translate(50px, 50px) scale(1.05);
            }
        }

        .animate-blob {
            animation: blob 20s infinite;
        }

        .animation-delay-2000 {
            animation-delay: 2s;
        }

        .animation-delay-4000 {
            animation-delay: 4s;
        }

        .animation-delay-1000 {
            animation-delay: 1s;
        }

        /* Gradient Animation */
        @keyframes gradient {

            0%,
            100% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }
        }

        .animate-gradient {
            background-size: 200% 200%;
            animation: gradient 3s ease infinite;
        }

        /* Line Clamp */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Smooth Scroll */
        html {
            scroll-behavior: smooth;
        }
    </style>
@endpush
