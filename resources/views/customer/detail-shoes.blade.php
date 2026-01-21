@extends('layouts/frontend/index')
@section('title', $shoe->name . ' | ShoeCycle')

@section('frontend-content')
    {{-- Breadcrumb --}}
    <div class="bg-gray-50 border-b border-gray-100 py-4">
        <div class="container mx-auto px-4">
            <div class="text-sm breadcrumbs text-gray-500">
                <ul>
                    <li><a href="{{ route('landing-page') }}">Beranda</a></li>
                    <li><a href="{{ route('all-category.index') }}#cat-{{ $shoe->category->id }}">{{ $shoe->category->category_name }}</a></li> {{-- fitur mendatang --}}
                    <li class="font-bold text-gray-900 overflow-hidden text-ellipsis whitespace-nowrap max-w-[200px]">{{ $shoe->name }}</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Main Product Section --}}
    <section class="py-10 bg-white">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

                {{-- LEFT: IMAGE GALLERY (Col-7) --}}
                <div class="lg:col-span-7">
                    <div class="sticky top-24 flex flex-col-reverse md:flex-row gap-4">
                        {{-- Thumbnails (Vertical on Desktop, Horizontal on Mobile) --}}
                        <div class="flex md:flex-col gap-3 overflow-x-auto md:overflow-y-auto md:h-[500px] w-full md:w-24 no-scrollbar" id="thumbnail-container">
                            {{-- Diisi via JS saat ganti varian --}}
                        </div>

                        {{-- Main Image --}}
                        <div class="flex-1 bg-gray-50 rounded-2xl overflow-hidden border border-gray-100 relative aspect-square md:aspect-auto md:h-[500px] flex items-center justify-center">
                            <img id="main-image" src="" alt="Main Product" class="w-full h-full object-contain mix-blend-multiply p-6 transition-transform duration-500 hover:cursor-zoom-in">

                            {{-- Badges --}}
                            <div class="absolute top-4 left-4 flex flex-col gap-2">
                                <span class="badge badge-primary badge-md">Original</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RIGHT: PRODUCT INFO (Col-5) --}}
                <div class="lg:col-span-5">
                    <div class="flex flex-col h-full">
                        {{-- Brand & Category --}}
                        <div class="mb-2">
                            <span class="text-blue-600 font-bold text-sm tracking-wider uppercase">{{ $shoe->brand_name }}</span>
                            <span class="text-gray-300 mx-2">|</span>
                            <span class="text-gray-500 text-sm">{{ $shoe->category->category_name }}</span>
                        </div>

                        {{-- Title --}}
                        <h1 class="text-3xl md:text-4xl font-heading font-bold text-gray-900 mb-4 leading-tight">
                            {{ $shoe->name }}
                        </h1>

                        {{-- Rating & Sold --}}
                        <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-100">
                            <div class="flex items-center gap-1 text-amber-400">
                                {{-- Logika Bintang Dinamis --}}
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= floor($avgRating))
                                        <i class="fas fa-star"></i> {{-- Bintang Penuh --}}
                                    @elseif ($i == ceil($avgRating) && $avgRating - floor($avgRating) >= 0.5)
                                        <i class="fas fa-star-half-alt"></i> {{-- Bintang Setengah --}}
                                    @else
                                        <i class="far fa-star text-gray-300"></i> {{-- Bintang Kosong --}}
                                    @endif
                                @endfor
                                <span class="text-gray-600 ml-1 text-sm font-medium">({{ $avgRating }})</span>
                            </div>
                            <div class="w-1 h-4 bg-gray-200"></div>
                            <div class="text-sm text-gray-600">
                                <span class="font-bold text-gray-900">{{ $totalReviews }}</span> Ulasan
                            </div>
                        </div>

                        {{-- PRICE SECTION --}}
                        <div class="mb-8">
                            <h2 class="text-3xl font-bold text-gray-900" id="display-price">{{ $priceRange }}</h2>
                            <div id="stock-display" class="text-sm text-gray-500 mt-1">Pilih varian untuk melihat stok</div>
                        </div>

                        {{-- VARIANT SELECTION --}}
                        <div class="space-y-6 mb-8">

                            {{-- 1. Pilih Warna --}}
                            <div>
                                <h3 class="text-sm font-bold text-gray-900 mb-3">Pilih Warna: <span id="selected-color-text" class="font-normal text-gray-500">-</span></h3>
                                <div class="flex flex-wrap gap-3">
                                    @foreach ($uniqueColors as $variant)
                                        <label class="cursor-pointer group relative">
                                            <input type="radio" name="color" value="{{ $variant->color }}" class="peer sr-only variant-color-selector">
                                            {{-- Style Radio Button Warna --}}
                                            <div class="w-12 h-12 rounded-full border-2 border-gray-200 peer-checked:border-blue-600 peer-checked:ring-2 peer-checked:ring-blue-100 flex items-center justify-center hover:border-gray-400 transition-all p-1 bg-white" title="{{ $variant->color }}">
                                                <div class="w-full h-full rounded-full" style="background-color: {{ $variant->color_code }}; border: 1px solid rgba(0,0,0,0.1);"></div>
                                            </div>
                                            {{-- Tooltip name --}}
                                            <span class="absolute -bottom-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-10">{{ $variant->color }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- 2. Pilih Ukuran --}}
                            <div>
                                <div class="flex justify-between items-center mb-3">
                                    <h3 class="text-sm font-bold text-gray-900">Pilih Ukuran: <span id="selected-size-text" class="font-normal text-gray-500">-</span></h3>
                                    <button class="text-xs text-blue-600 hover:underline">Lihat Panduan Ukuran</button>
                                </div>

                                {{-- Update Grid: Pakai cols-5 di mobile, cols-7 di desktop agar range 35-48 terlihat compact --}}
                                <div class="grid grid-cols-5 sm:grid-cols-7 gap-2" id="size-container">
                                    <div class="col-span-full text-sm text-gray-400 italic">Pilih warna terlebih dahulu</div>
                                </div>
                            </div>

                        </div>

                        {{-- ACTION BUTTONS --}}
                        <div class="mt-auto pt-6 border-t border-gray-100">

                            <div class="flex flex-col sm:flex-row gap-4">
                                {{-- Wrapper Input Qty --}}
                                <div>
                                    <div class="flex items-center border border-gray-300 rounded-xl h-12 w-32">
                                        <button type="button" class="w-10 h-full flex items-center justify-center text-gray-500 hover:bg-gray-100 rounded-l-xl transition-colors" onclick="updateQty(-1)">-</button>
                                        <input type="number" id="qty-input" value="1" min="1" class="w-full h-full text-center border-none focus:ring-0 text-gray-900 font-bold" readonly>
                                        <button type="button" class="w-10 h-full flex items-center justify-center text-gray-500 hover:bg-gray-100 rounded-r-xl transition-colors" onclick="updateQty(1)">+</button>
                                    </div>
                                    {{-- Pesan Error Live Validation --}}
                                    <span id="qty-error" class="text-xs text-red-500 mt-1 block h-4 transition-all opacity-0"></span>
                                </div>

                                {{-- Main Buttons --}}
                                <button id="btn-add-to-cart" class="btn bg-blue-600 hover:bg-blue-700 text-white border-none flex-1 h-12 text-base rounded-xl shadow-lg shadow-blue-200 disabled:opacity-50 disabled:cursor-not-allowed transition-all" disabled>
                                    <i class="fas fa-shopping-bag mr-2"></i> Masukkan Keranjang
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- DESCRIPTION & TABS --}}
    <section class="py-10 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-10 max-w-4xl mx-auto">

                {{-- Tab Headers --}}
                <div class="border-b border-gray-100 mb-8">
                    <div class="flex gap-8">
                        <button id="btn-tab-desc" onclick="switchTab('desc')" class="tab-btn pb-4 border-b-2 border-blue-600 text-blue-600 font-bold text-sm uppercase tracking-wide transition-all duration-300">
                            Deskripsi Produk
                        </button>
                        <button id="btn-tab-review" onclick="switchTab('review')" class="tab-btn pb-4 border-b-2 border-transparent text-gray-400 hover:text-gray-800 font-medium text-sm uppercase tracking-wide transition-all duration-300">
                            Ulasan ({{ $totalReviews }})
                        </button>
                    </div>
                </div>

                {{-- AREA KONTEN --}}
                <div id="tab-container-content">

                    {{-- CONTENT 1: DESKRIPSI --}}
                    <div id="content-desc" class="tab-pane block"> {{-- Gunakan 'block' untuk default --}}
                        <div class="prose max-w-none text-gray-600 leading-relaxed">
                            {!! nl2br(e($shoe->description)) !!}
                        </div>
                    </div>

                    {{-- CONTENT 2: RATING & ULASAN --}}
                    <div id="content-review" class="tab-pane hidden">
                        <div class="space-y-10">
                            {{-- Ringkasan Rating --}}
                            <div class="flex flex-col md:flex-row items-center gap-8 bg-slate-50 p-6 rounded-2xl border border-slate-100">
                                <div class="text-center">
                                    <h4 class="text-5xl font-bold text-gray-900">{{ $avgRating }}</h4>
                                    <p class="text-sm text-gray-500 mt-1 uppercase tracking-widest font-bold">dari 5.0</p>
                                    <div class="flex gap-1 text-amber-400 mt-3 text-lg justify-center">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="{{ $i <= round($avgRating) ? 'fas' : 'far' }} fa-star"></i>
                                        @endfor
                                    </div>
                                </div>

                                <div class="flex-1 w-full space-y-2">
                                    @php
                                        $counts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
                                        foreach ($shoe->reviews as $r) {
                                            if (isset($counts[$r->rating])) {
                                                $counts[$r->rating]++;
                                            }
                                        }
                                    @endphp
                                    @foreach ([5, 4, 3, 2, 1] as $star)
                                        <div class="flex items-center gap-3">
                                            <span class="text-xs font-bold text-gray-600 w-3">{{ $star }}</span>
                                            <i class="fas fa-star text-amber-400 text-[10px]"></i>
                                            <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                                                <div class="h-full bg-amber-400 rounded-full" style="width: {{ $totalReviews > 0 ? ($counts[$star] / $totalReviews) * 100 : 0 }}%"></div>
                                            </div>
                                            <span class="text-xs text-gray-400 w-8 text-right">{{ $counts[$star] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Daftar List Komentar --}}
                            <div class="space-y-6">
                                @forelse ($shoe->reviews()->latest()->get() as $review)
                                    <div class="flex gap-4 pb-6 border-b border-gray-50 last:border-0">
                                        <div class="w-10 h-10 rounded-full overflow-hidden shrink-0 border border-gray-100">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($review->user->name) }}&background=0D8ABC&color=fff" class="w-full h-full object-cover">
                                        </div>
                                        <div class="flex-1 text-black">
                                            <div class="flex justify-between items-center mb-1">
                                                <h4 class="text-sm font-bold text-gray-900">{{ $review->user->name }}</h4>
                                                <span class="text-[10px] text-gray-400">{{ $review->created_at->diffForHumans() }}</span>
                                            </div>
                                            <div class="flex gap-0.5 text-amber-400 text-[10px] mb-2">
                                                @for ($r = 1; $r <= 5; $r++)
                                                    <i class="{{ $r <= $review->rating ? 'fas' : 'far' }} fa-star"></i>
                                                @endfor
                                            </div>
                                            <p class="text-sm text-gray-600 italic leading-relaxed">"{{ $review->comment ?: 'Pembeli tidak memberikan komentar teks.' }}"</p>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-10">
                                        <i class="fas fa-comment-slash text-gray-200 text-xl block mb-3"></i>
                                        <p class="text-gray-400 italic text-sm">Belum ada ulasan untuk produk ini.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                </div> {{-- End tab-container-content --}}
            </div>
        </div>
    </section>

    {{-- FIXED: Beri ID 'btn-buy-now' pada tombol di sticky footer agar tidak terjadi JS Error --}}
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-4 z-50 lg:hidden shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)]">
        <div class="flex gap-3">
            <button class="btn btn-square btn-ghost border border-gray-200 text-gray-500">
                <i class="far fa-comment-dots text-xl"></i>
            </button>
            <button id="btn-buy-now" class="btn bg-blue-600 text-white flex-1 rounded-xl shadow-lg border-none" disabled>
                Beli Sekarang
            </button>
        </div>
    </div>

    {{-- MOBILE STICKY FOOTER (Optional, for UX like Shopee/Tokped) --}}
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-4 z-50 lg:hidden shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)]">
        <div class="flex gap-3">
            <button class="btn btn-square btn-ghost border border-gray-200 text-gray-500">
                <i class="far fa-comment-dots text-xl"></i>
            </button>
            <button class="btn bg-blue-600 text-white flex-1 rounded-xl shadow-lg border-none" onclick="document.getElementById('btn-buy-now').click()">
                Beli Sekarang
            </button>
        </div>
    </div>

@endsection

@push('styles')
    <style>
        /* Hide scrollbar for Chrome, Safari and Opera */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        /* Hide scrollbar for IE, Edge and Firefox */
        .no-scrollbar {
            -ms-overflow-style: none;
            /* IE and Edge */
            scrollbar-width: none;
            /* Firefox */
        }

        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // --- 1. DATA DARI SERVER ---
        const variantMap = @json($variantMap);
        const availableSizes = @json($availableSizesPerColor);
        const defaultImages = @json($defaultImages);

        // State Variables
        let selectedColor = null;
        let selectedSize = null;
        let currentStock = 0;

        // --- 2. FUNGSI PERPINDAHAN TAB (GLOBAL) ---
        window.switchTab = function(type) {
            $('.tab-btn').removeClass('border-blue-600 text-blue-600 font-bold').addClass('border-transparent text-gray-400 font-medium');
            $('.tab-pane').addClass('hidden').removeClass('block');

            if (type === 'desc') {
                $('#btn-tab-desc').addClass('border-blue-600 text-blue-600 font-bold').removeClass('border-transparent text-gray-400 font-medium');
                $('#content-desc').removeClass('hidden').addClass('block');
            } else {
                $('#btn-tab-review').addClass('border-blue-600 text-blue-600 font-bold').removeClass('border-transparent text-gray-400 font-medium');
                $('#content-review').removeClass('hidden').addClass('block');
            }
        };

        // --- 3. INIT DEFAULT VIEW ---
        $(document).ready(function() {
            if (defaultImages.length > 0) {
                renderGallery(defaultImages.map(img => `/storage/${img.image_path}`));
            } else {
                renderGallery(['/assets/upload/testing/sepatu1.webp']);
            }
        });

        // --- 4. LOGIC PILIH WARNA ---
        $('.variant-color-selector').on('change', function() {
            selectedColor = $(this).val();
            selectedSize = null;
            $('#selected-color-text').text(selectedColor);
            $('#selected-size-text').text('-');
            resetPurchaseButtons();
            renderSizeButtons(selectedColor);

            const firstSize = availableSizes[selectedColor][0];
            const variantKey = `${selectedColor}_${firstSize}`;
            if (variantMap[variantKey] && variantMap[variantKey].images.length > 0) {
                renderGallery(variantMap[variantKey].images);
            }
        });

        // --- 5. RENDER SIZE BUTTONS ---
        function renderSizeButtons(color) {
            const container = $('#size-container');
            container.empty();
            const startSize = 35;
            const endSize = 48;

            for (let size = startSize; size <= endSize; size++) {
                const key = `${color}_${size}`;
                const variantData = variantMap[key];
                const isAvailable = variantData && variantData.is_available && variantData.stock > 0;

                let btnClass = isAvailable ?
                    'bg-white text-gray-900 border-gray-200 hover:border-blue-600 hover:text-blue-600 cursor-pointer size-selector shadow-sm' :
                    'bg-gray-100 text-gray-300 border-transparent cursor-not-allowed';

                if (selectedSize == size && isAvailable) {
                    btnClass += ' ring-2 ring-blue-600 border-blue-600 bg-blue-50 text-blue-700';
                }

                const html = `<div class="${btnClass} border rounded-lg py-2.5 text-center transition-all font-medium text-sm select-none"
                                data-size="${size}" ${isAvailable ? `onclick="selectSize(this)"` : ''}>${size}</div>`;
                container.append(html);
            }
        }

        // --- 6. LOGIC PILIH SIZE ---
        window.selectSize = function(el) {
            $('.size-selector').removeClass('ring-2 ring-blue-600 border-blue-600 bg-blue-50 text-blue-700');
            $(el).addClass('ring-2 ring-blue-600 border-blue-600 bg-blue-50 text-blue-700');
            selectedSize = $(el).data('size');
            $('#selected-size-text').text('EU ' + selectedSize);
            updateProductDetails();
        }

        function updateProductDetails() {
            if (!selectedColor || !selectedSize) return;
            const key = `${selectedColor}_${selectedSize}`;
            const data = variantMap[key];
            if (data) {
                $('#display-price').text(data.formatted_price);
                currentStock = data.stock;
                $('#stock-display').html(`Stok: <span class="font-bold text-gray-900">${currentStock}</span> tersisa`);
                $('#btn-add-to-cart, #btn-buy-now').prop('disabled', false).removeClass('opacity-50 cursor-not-allowed');

                // Live validation check jika qty saat ini > stok baru
                if (parseInt($('#qty-input').val()) > currentStock) {
                    $('#qty-input').val(currentStock);
                    showQtyError(`Stok terbatas, disesuaikan ke ${currentStock}`);
                }
            }
        }

        function resetPurchaseButtons() {
            $('#btn-add-to-cart, #btn-buy-now').prop('disabled', true).addClass('opacity-50 cursor-not-allowed');
            $('#stock-display').text('Pilih varian untuk melihat stok');
        }

        // --- 7. IMAGE GALLERY LOGIC ---
        function renderGallery(images) {
            const thumbContainer = $('#thumbnail-container');
            const mainImage = $('#main-image');
            thumbContainer.empty();
            if (images.length === 0) {
                mainImage.attr('src', '/assets/upload/testing/sepatu1.webp');
                return;
            }
            mainImage.attr('src', images[0]);
            images.forEach((imgSrc, index) => {
                const activeClass = index === 0 ? 'border-blue-600 ring-1 ring-blue-600' : 'border-gray-200';
                const thumb = `<div class="w-16 h-16 md:w-20 md:h-20 flex-shrink-0 bg-gray-50 rounded-lg border ${activeClass} cursor-pointer overflow-hidden p-1 thumbnail-item"
                                     onmouseover="changeMainImage(this, '${imgSrc}')">
                                <img src="${imgSrc}" class="w-full h-full object-contain mix-blend-multiply">
                              </div>`;
                thumbContainer.append(thumb);
            });
        }

        window.changeMainImage = function(el, src) {
            $('#main-image').attr('src', src);
            $('.thumbnail-item').removeClass('border-blue-600 ring-1 ring-blue-600').addClass('border-gray-200');
            $(el).removeClass('border-gray-200').addClass('border-blue-600 ring-1 ring-blue-600');
        }

        // --- 8. QTY LOGIC (LIVE VALIDATION RESTORED) ---
        window.updateQty = function(change) {
            const errorSpan = $('#qty-error');
            if ($('#btn-add-to-cart').is(':disabled')) {
                showQtyError('Pilih varian dulu');
                return;
            }

            const input = $('#qty-input');
            let currentVal = parseInt(input.val());
            let newVal = currentVal + change;

            hideQtyError();

            if (newVal < 1) {
                newVal = 1;
            } else if (newVal > currentStock) {
                newVal = currentStock;
                showQtyError(`Maks. stok ${currentStock}`);
            }

            input.val(newVal);
        }

        function showQtyError(msg) {
            $('#qty-error').text(msg).removeClass('opacity-0').addClass('opacity-100');
            if (window.qtyTimeout) clearTimeout(window.qtyTimeout);
            window.qtyTimeout = setTimeout(() => hideQtyError(), 3000);
        }

        function hideQtyError() {
            $('#qty-error').removeClass('opacity-100').addClass('opacity-0');
        }

        // --- 9. ADD TO CART AJAX (SWEETALERT RESTORED) ---
        $('#btn-add-to-cart').click(function() {
            const btn = $(this);
            const originalText = btn.html();
            btn.prop('disabled', true).html('<span class="loading loading-spinner loading-xs"></span>');

            $.ajax({
                url: "{{ route('cart.store') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    shoe_slug: "{{ $shoe->slug }}",
                    color: selectedColor,
                    size: selectedSize,
                    qty: $('#qty-input').val()
                },
                success: function(response) {
                    btn.prop('disabled', false).html(originalText);
                    if (response.status === 'success') {
                        const badge = $('#cart-badge-count');
                        badge.text(response.cart_count).removeClass('hidden');
                        $('#mini-cart-dropdown-content').html(response.mini_cart_html);

                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true,
                        });

                        Toast.fire({
                            icon: 'success',
                            title: response.message
                        });

                        $('.fa-shopping-bag').parent().addClass('animate-bounce');
                        setTimeout(() => $('.fa-shopping-bag').parent().removeClass('animate-bounce'), 1000);
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html(originalText);

                    if (xhr.status === 401) {
                        openLoginModal();

                        $('#login-error-msg').text('Silakan masuk terlebih dahulu untuk menambahkan sepatu ke Keranjang.');
                        $('#login-global-error').removeClass('hidden');
                    } else {
                        Swal.fire('Oops!', xhr.responseJSON.message || 'Gagal menambahkan barang', 'error');
                    }
                }
            });
        });
    </script>
@endpush
