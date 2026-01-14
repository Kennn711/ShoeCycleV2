@extends('layouts/frontend/index')
@section('title', 'Semua Kategori | ShoeCycle')

@section('frontend-content')
    {{-- Breadcrumb --}}
    <div class="bg-gray-50 border-b border-gray-100 py-4">
        <div class="container mx-auto px-4 lg:px-6">
            <div class="text-sm breadcrumbs text-gray-500">
                <ul>
                    <li><a href="{{ route('landing-page') }}">Beranda</a></li>
                    <li class="font-bold text-gray-900">Semua Kategori</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Jump to Category Section (Sticky) --}}
    <div class="sticky top-[80px] z-30 bg-slate-50/80 backdrop-blur-md border-b border-gray-100 py-4">
        <div class="container mx-auto px-4 lg:px-6">
            <div class="flex items-center gap-4">
                <span class="text-xs font-bold text-gray-400 uppercase whitespace-nowrap hidden md:block">Lompat ke:</span>
                <div class="flex overflow-x-auto gap-2 no-scrollbar pb-1">
                    @foreach ($categories as $category)
                        @if ($category->shoes->count() > 0)
                            <a href="#cat-{{ $category->id }}" class="px-4 py-1.5 bg-white hover:bg-blue-600 hover:text-white border border-gray-200 hover:border-blue-600 rounded-full text-xs font-bold text-gray-600 transition-all whitespace-nowrap shadow-sm active:scale-95">
                                {{ $category->category_name }}
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 lg:px-6 py-12">
        {{-- Page Heading --}}
        <div class="mb-16" data-aos="fade-down">
            <h1 class="text-4xl font-extrabold text-gray-900 font-heading">Koleksi Kategori</h1>
            <p class="text-gray-500 mt-2 italic">Temukan gaya yang sesuai dengan karaktermu.</p>
        </div>

        @forelse($categories as $category)
            @if ($category->shoes->count() > 0)
                {{-- Tambahkan ID untuk Anchor Link dan scroll-mt agar tidak tertutup navbar --}}
                <section id="cat-{{ $category->id }}" class="mb-24 scroll-mt-40">
                    {{-- Header Kategori --}}
                    <div class="flex items-center gap-4 mb-10" data-aos="fade-right">
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 font-heading uppercase tracking-tight">
                            {{ $category->category_name }}
                        </h2>
                        <div class="h-[2px] bg-blue-600 flex-grow rounded-full opacity-20"></div>
                        <span class="text-sm font-bold text-blue-600 bg-blue-50 px-4 py-1 rounded-full border border-blue-100">
                            {{ $category->shoes->count() }} Produk
                        </span>
                    </div>

                    {{-- Grid Sepatu (SAMA PERSIS DENGAN LANDING PAGE) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                        @foreach ($category->shoes as $index => $shoe)
                            @php
                                $firstVariant = $shoe->variants->first();
                                $image = $firstVariant ? $firstVariant->images->where('is_primary', true)->first() : null;
                                $imageUrl = $image ? asset('storage/' . $image->image_path) : asset('assets/upload/testing/dummy.jpg');

                                // Logic Stok: Hitung total stok dari semua varian
                                $totalStock = $shoe->variants->sum('stock');
                                $isOutOfStock = $totalStock <= 0;

                                // Logic Card sesuai Landing Page
                                $soldCount = rand(50, 500);
                                $rating = 4.8;
                            @endphp

                            {{-- Product Card --}}
                            <div class="group bg-white rounded-3xl border border-gray-100 hover:border-blue-200 hover:shadow-xl hover:shadow-blue-50 transition-all duration-300 overflow-hidden h-full flex flex-col relative" data-aos="fade-up" data-aos-delay="{{ $index * 50 }}">
                                {{-- Stok Habis Badge --}}
                                @if ($isOutOfStock)
                                    <div class="absolute top-4 left-4 z-20">
                                        <span class="bg-red-500 text-white text-[10px] font-black uppercase px-3 py-1.5 rounded-xl shadow-md shadow-red-200 flex items-center gap-1.5">
                                            <i class="fas fa-exclamation-circle text-xs"></i> Stok Habis
                                        </span>
                                    </div>
                                @endif

                                {{-- Image Container --}}
                                <div class="relative bg-gray-50 flex items-center justify-center overflow-hidden">
                                    <img src="{{ $imageUrl }}" alt="{{ $shoe->name }}" class="w-full h-full rounded-[4rem] object-contain p-4 mix-blend-multiply" />
                                    @if ($isOutOfStock)
                                        <div class="absolute inset-0 bg-gray-900/5 transition-opacity"></div>
                                    @endif
                                </div>

                                {{-- Content --}}
                                <div class="p-5 flex flex-col flex-grow relative">
                                    {{-- Category --}}
                                    <div class="text-[14px] font-bold text-blue-600 uppercase mb-2">
                                        {{ $category->category_name }}
                                    </div>

                                    {{-- Name --}}
                                    <h3 class="card-title text-lg font-bold text-gray-900 mb-3 leading-snug group-hover:text-blue-600 transition-colors duration-300">
                                        <a href="{{ route('detail-shoes', $shoe->slug) }}" class="line-clamp-2">{{ $shoe->name }}</a>
                                    </h3>

                                    {{-- Row: Rating, Sold & Button --}}
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

                                        {{-- Kanan: Button Lihat --}}
                                        <a href="{{ route('detail-shoes', $shoe->slug) }}" class="btn btn-sm rounded-full bg-blue-500 hover:bg-blue-600 text-white border-none px-4 font-normal shadow-sm transition-colors duration-300 text-[11px]">
                                            Lihat Selengkapnya
                                        </a>
                                    </div>

                                    {{-- Footer: Price --}}
                                    <div class="mt-auto pt-4 border-t border-gray-100">
                                        <p class="text-[10px] text-gray-400 uppercase font-semibold mb-0.5">Harga</p>
                                        <div class="text-lg font-bold {{ $isOutOfStock ? 'text-gray-400 line-through opacity-70' : 'text-gray-900' }} leading-none">
                                            {{ $shoe->price_range }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        @empty
            {{-- Empty State tetap sama --}}
            <div class="py-24 text-center">
                <i class="fas fa-folder-open text-6xl text-gray-200 mb-4"></i>
                <h3 class="text-xl font-bold text-gray-900">Tidak ada kategori</h3>
            </div>
        @endforelse
    </div>
@endsection

@push('styles')
    <style>
        /* Menghilangkan scrollbar pada navigasi kategori tapi tetap bisa discroll */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
@endpush
