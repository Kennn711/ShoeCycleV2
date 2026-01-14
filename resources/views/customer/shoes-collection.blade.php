@extends('layouts/frontend/index')
@section('title', 'Koleksi Sepatu | ShoeCycle')

@section('frontend-content')
    {{-- Breadcrumb --}}
    <div class="bg-gray-50 border-b border-gray-100 py-4">
        <div class="container mx-auto px-4 lg:px-6">
            <div class="text-sm breadcrumbs text-gray-500">
                <ul>
                    <li><a href="{{ route('landing-page') }}">Beranda</a></li>
                    <li class="font-bold text-gray-900">Koleksi</li>
                </ul>
            </div>
        </div>
    </div>

    <section class="py-12 bg-white">
        <div class="container mx-auto px-4 lg:px-6">
            <div class="flex flex-col lg:flex-row gap-10">

                {{-- SIDEBAR FILTER --}}
                <aside class="w-full lg:w-64 flex-shrink-0">
                    <form action="{{ route('shoes-collection.index') }}" method="GET" id="filterForm" class="space-y-8 sticky top-24">
                        {{-- Search --}}
                        <div>
                            <h4 class="text-sm font-bold text-gray-900 uppercase tracking-widest mb-4">Cari Sepatu</h4>
                            <div class="relative">
                                <input type="text" name="q" value="{{ request('q') }}" placeholder="Nama atau merk..." class="input input-bordered w-full rounded-xl bg-gray-50 focus:bg-white border-gray-200">
                                <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Category Filter --}}
                        <div>
                            <h4 class="text-sm font-bold text-gray-900 uppercase tracking-widest mb-4">Kategori</h4>
                            <div class="space-y-3">
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="radio" name="category" value="" class="radio radio-primary radio-sm" {{ request('category') == '' ? 'checked' : '' }} onchange="this.form.submit()">
                                    <span class="text-sm text-gray-600 group-hover:text-blue-600 transition-colors">Semua Produk</span>
                                </label>
                                @foreach ($categories as $cat)
                                    <label class="flex items-center gap-3 cursor-pointer group">
                                        <input type="radio" name="category" value="{{ $cat->id }}" class="radio radio-primary radio-sm" {{ request('category') == $cat->id ? 'checked' : '' }} onchange="this.form.submit()">
                                        <span class="text-sm text-gray-600 group-hover:text-blue-600 transition-colors">{{ $cat->category_name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Reset Button --}}
                        @if (request('q') || request('category'))
                            <a href="{{ route('shoes-collection.index') }}" class="btn bg-red-400 btn-sm w-full text-white hover:bg-red-500 rounded-xl">
                                <i class="fas fa-times-circle mr-2"></i> Hapus Filter
                            </a>
                        @endif
                    </form>
                </aside>

                {{-- MAIN CONTENT (GRID) --}}
                <div class="flex-1">
                    {{-- Info Hasil --}}
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4 text-center sm:text-left">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Koleksi Produk</h1>
                            <p class="text-sm text-gray-500 mt-1">Menampilkan {{ $shoes->firstItem() ?? 0 }}-{{ $shoes->lastItem() ?? 0 }} dari {{ $shoes->total() }} produk</p>
                        </div>
                    </div>

                    {{-- Grid Sepatu --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-8">
                        @forelse($shoes as $index => $shoe)
                            @php
                                $firstVariant = $shoe->variants->first();
                                $image = $firstVariant ? $firstVariant->images->where('is_primary', true)->first() : null;
                                $imageUrl = $image ? asset('storage/' . $image->image_path) : asset('assets/upload/testing/dummy.jpg');

                                // Rating & Sold Dummy sesuai request landing page
                                $soldCount = rand(50, 500);
                                $rating = 4.8;
                            @endphp

                            {{-- PRODUCT CARD (Layout SAMA PERSIS dengan All Category) --}}
                            <div class="group bg-white rounded-3xl border border-gray-100 hover:border-blue-200 hover:shadow-xl hover:shadow-blue-50 transition-all duration-300 overflow-hidden h-full flex flex-col relative" data-aos="fade-up" data-aos-delay="{{ ($index % 4) * 50 }}">

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
                                        <a href="{{ route('detail-shoes', $shoe->slug) }}" class="btn btn-sm rounded-full bg-blue-500 hover:bg-blue-600 text-white border-none px-4 font-normal shadow-sm transition-colors duration-300">
                                            Lihat Selengkapnya
                                        </a>
                                    </div>

                                    {{-- Footer: Price Only --}}
                                    <div class="mt-auto pt-4 border-t border-gray-100">
                                        <p class="text-[10px] text-gray-400 uppercase font-semibold mb-0.5">Harga</p>
                                        <div class="text-lg font-bold text-gray-900 leading-none">
                                            {{ $shoe->price_range }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full py-20 text-center">
                                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
                                    <i class="fas fa-search text-3xl"></i>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900">Produk tidak ditemukan</h3>
                                <p class="text-gray-500 text-sm">Coba gunakan kata kunci lain atau hapus filter.</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- PAGINATION --}}
                    <div class="mt-16 flex justify-center">
                        {{ $shoes->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        /* Styling khusus untuk memastikan pagination rapi */
        nav[role="navigation"] svg {
            width: 20px;
        }

        .pagination {
            display: flex;
            gap: 5px;
        }

        /* Reset background container pagination */
        nav[role="navigation"] {
            background-color: transparent !important;
        }

        /* 1. Tombol Biasa (Link) */
        nav[role="navigation"] a {
            background-color: #ffffff !important;
            color: #374151 !important;
            /* Gray-700 */
            border-color: #e5e7eb !important;
        }

        nav[role="navigation"] a:hover {
            background-color: #f3f4f6 !important;
        }

        /* 2. Tombol Disabled (Arrow Kiri/Kanan saat mentok) - INI PERBAIKANNYA */
        nav[role="navigation"] span[aria-disabled="true"] span,
        nav[role="navigation"] span[aria-disabled="true"] {
            background-color: #ffffff !important;
            /* Paksa Putih */
            color: #d1d5db !important;
            /* Gray-300 (Warna disabled) */
            border-color: #e5e7eb !important;
            cursor: not-allowed;
        }

        /* 3. Tombol Aktif (Halaman saat ini) */
        nav[role="navigation"] span[aria-current="page"]>span {
            background-color: #3b82f6 !important;
            /* Blue-500 */
            color: #ffffff !important;
            border-color: #3b82f6 !important;
        }

        /* Sembunyikan Text Previous/Next bawaan Laravel jika mengganggu layout */
        nav[role="navigation"] svg {
            width: 20px;
            height: 20px;
        }
    </style>
@endpush
