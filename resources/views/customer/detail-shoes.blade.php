@extends('layouts/frontend/index')
@section('title', $shoe->name . ' | ShoeCycle')

@section('frontend-content')
    {{-- Breadcrumb --}}
    <div class="bg-gray-50 border-b border-gray-100 py-4">
        <div class="container mx-auto px-4">
            <div class="text-sm breadcrumbs text-gray-500">
                <ul>
                    <li><a href="{{ route('landing-page') }}">Beranda</a></li>
                    <li><a href="#">{{ $shoe->category->category_name }}</a></li> {{-- fitur mendatang --}}
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
                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                                <span class="text-gray-600 ml-1 text-sm font-medium">(4.8)</span>
                            </div>
                            <div class="w-1 h-4 bg-gray-200"></div>
                            <div class="text-sm text-gray-600"><span class="font-bold text-gray-900">120+</span> Terjual</div>
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
                <div class="border-b border-gray-100 mb-6">
                    <div class="flex gap-8">
                        <button class="pb-4 border-b-2 border-blue-600 text-blue-600 font-bold text-sm uppercase tracking-wide">Deskripsi Produk</button>
                        <button class="pb-4 border-b-2 border-transparent text-gray-500 hover:text-gray-800 font-medium text-sm uppercase tracking-wide transition-colors">Ulasan (0)</button>
                    </div>
                </div>
                <div class="prose max-w-none text-gray-600 leading-relaxed">
                    <p>{{ $shoe->description }}</p>
                </div>
            </div>
        </div>
    </section>

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
        // Mapping kombinasi varian: "Warna_Size" => {price, stock, images}
        const variantMap = @json($variantMap);
        // Mapping Warna => List Size yang tersedia: "Hitam" => [40, 41, 42]
        const availableSizes = @json($availableSizesPerColor);
        // Default Images
        const defaultImages = @json($defaultImages);

        // State Variables
        let selectedColor = null;
        let selectedSize = null;
        let currentStock = 0;

        // --- 2. INIT DEFAULT VIEW ---
        $(document).ready(function() {
            // Render gambar default saat load (jika belum pilih varian)
            if (defaultImages.length > 0) {
                renderGallery(defaultImages.map(img => `/storage/${img.image_path}`));
            } else {
                renderGallery(['/assets/upload/testing/dummy.jpg']);
            }
        });

        // --- 3. LOGIC PILIH WARNA ---
        $('.variant-color-selector').on('change', function() {
            selectedColor = $(this).val();
            selectedSize = null; // Reset size saat ganti warna

            // UI Updates
            $('#selected-color-text').text(selectedColor);
            $('#selected-size-text').text('-');

            // Reset Tombol Beli
            resetPurchaseButtons();

            // Render Ulang Size Button berdasarkan Warna
            renderSizeButtons(selectedColor);

            // Update Gambar Galeri (Ambil preview dari varian pertama warna ini)
            // Kita cari size pertama yang available buat warna ini untuk ambil gambarnya
            const firstSize = availableSizes[selectedColor][0];
            const variantKey = `${selectedColor}_${firstSize}`;
            if (variantMap[variantKey] && variantMap[variantKey].images.length > 0) {
                renderGallery(variantMap[variantKey].images);
            }
        });

        // --- HELPER: RENDER SIZE BUTTONS (UPDATED: RANGE 35-48) ---
        function renderSizeButtons(color) {
            const container = $('#size-container');
            container.empty();

            // 1. Definisikan Range Ukuran Standar (35 - 48)
            const startSize = 35;
            const endSize = 48;

            // Loop dari 35 sampai 48
            for (let size = startSize; size <= endSize; size++) {

                // 2. Cek Ketersediaan di Data Varian
                // Kita cek langsung ke variantMap (yang memuat data stok & harga)
                const key = `${color}_${size}`;
                const variantData = variantMap[key];

                // Kondisi Available: Data ada, Status Available = true, Stok > 0
                const isAvailable = variantData && variantData.is_available && variantData.stock > 0;

                // 3. Tentukan Styling Class
                let btnClass = '';
                let attributes = '';

                if (isAvailable) {
                    // Style: AKTIF / TERSEDIA
                    // Border abu-abu, hover jadi biru, text hitam
                    btnClass = 'bg-white text-gray-900 border-gray-200 hover:border-blue-600 hover:text-blue-600 cursor-pointer size-selector shadow-sm';
                    attributes = `onclick="selectSize(this)"`;
                } else {
                    // Style: DISABLED / TIDAK TERSEDIA
                    // Background abu-abu muda, text abu-abu pudar, border transparan, cursor not-allowed
                    btnClass = 'bg-gray-100 text-gray-300 border-transparent cursor-not-allowed';
                    // Opsional: Tambahkan tooltip title
                    const reason = !variantData ? 'Varian tidak ada' : 'Stok habis';
                    attributes = `title="${reason}"`;
                }

                // Cek jika ukuran ini sedang dipilih (agar highlight tetap ada saat render ulang)
                if (selectedSize == size && isAvailable) {
                    btnClass += ' ring-2 ring-blue-600 border-blue-600 bg-blue-50 text-blue-700';
                }

                // 4. Render HTML
                const html = `
                <div class="${btnClass} border rounded-lg py-2.5 text-center transition-all font-medium text-sm select-none"
                     data-size="${size}" 
                     ${attributes}>
                    ${size}
                </div>
            `;
                container.append(html);
            }
        }

        // --- 5. LOGIC PILIH SIZE (FINAL SELECTION) ---
        window.selectSize = function(el) {
            // Visual Selection
            $('.size-selector').removeClass('ring-2 ring-blue-600 border-blue-600 bg-blue-50 text-blue-700');
            $(el).addClass('ring-2 ring-blue-600 border-blue-600 bg-blue-50 text-blue-700');

            selectedSize = $(el).data('size');
            $('#selected-size-text').text('EU ' + selectedSize);

            // --- FINAL LOGIC: UPDATE HARGA & STOK & TOMBOL ---
            updateProductDetails();
        }

        function updateProductDetails() {
            if (!selectedColor || !selectedSize) return;

            const key = `${selectedColor}_${selectedSize}`;
            const data = variantMap[key];

            if (data) {
                // Update Harga
                $('#display-price').text(data.formatted_price);

                // Update Stok Display
                currentStock = data.stock;
                $('#stock-display').html(`Stok: <span class="font-bold text-gray-900">${currentStock}</span> tersisa`);

                // Update Tombol Beli
                $('#btn-add-to-cart, #btn-buy-now').prop('disabled', false).removeClass('opacity-50 cursor-not-allowed');

                // Reset Qty Input jika melebihi stok baru
                if (parseInt($('#qty-input').val()) > currentStock) {
                    $('#qty-input').val(1);
                }
            }
        }

        function resetPurchaseButtons() {
            $('#btn-add-to-cart, #btn-buy-now').prop('disabled', true).addClass('opacity-50 cursor-not-allowed');
            $('#stock-display').text('Pilih varian untuk melihat stok');
            // Harga tidak perlu direset ke range, biarkan harga terakhir atau range awal
        }

        // --- 6. IMAGE GALLERY LOGIC ---
        function renderGallery(images) {
            const thumbContainer = $('#thumbnail-container');
            const mainImage = $('#main-image');

            thumbContainer.empty();

            if (images.length === 0) {
                mainImage.attr('src', '/assets/upload/testing/dummy.jpg');
                return;
            }

            // Set Main Image ke gambar pertama
            mainImage.attr('src', images[0]);
            // Animasi fade in simple
            mainImage.hide().fadeIn(300);

            // Generate Thumbnails
            images.forEach((imgSrc, index) => {
                const activeClass = index === 0 ? 'border-blue-600 ring-1 ring-blue-600' : 'border-gray-200 hover:border-gray-400';
                const thumb = `
                <div class="w-16 h-16 md:w-20 md:h-20 flex-shrink-0 bg-gray-50 rounded-lg border ${activeClass} cursor-pointer overflow-hidden p-1 transition-all thumbnail-item"
                     onmouseover="changeMainImage(this, '${imgSrc}')">
                    <img src="${imgSrc}" class="w-full h-full object-contain mix-blend-multiply">
                </div>
            `;
                thumbContainer.append(thumb);
            });
        }

        window.changeMainImage = function(el, src) {
            // Ganti source gambar utama
            $('#main-image').attr('src', src);

            // Update active state thumbnail
            $('.thumbnail-item').removeClass('border-blue-600 ring-1 ring-blue-600').addClass('border-gray-200');
            $(el).removeClass('border-gray-200').addClass('border-blue-600 ring-1 ring-blue-600');
        }

        // --- 7. QTY LOGIC (LIVE VALIDATION) ---
        window.updateQty = function(change) {
            const errorSpan = $('#qty-error');

            // 1. Validasi: Belum Pilih Varian
            if ($('#btn-add-to-cart').is(':disabled')) {
                showQtyError('Pilih varian dulu');
                // Efek shake ringan pada container varian agar user notice
                $('.variant-color-selector').parent().parent().addClass('animate-pulse');
                setTimeout(() => $('.variant-color-selector').parent().parent().removeClass('animate-pulse'), 500);
                return;
            }

            const input = $('#qty-input');
            let currentVal = parseInt(input.val());
            let newVal = currentVal + change;

            // Reset Error dulu
            hideQtyError();

            // 2. Validasi: Minimal 1
            if (newVal < 1) {
                newVal = 1;
                // Optional: Bisa kasih feedback visual mentok bawah
            }

            // 3. Validasi: Maksimal Stok
            if (newVal > currentStock) {
                newVal = currentStock;
                showQtyError(`Maks. stok ${currentStock}`);
            } else {
                // Jika valid dan tidak melebihi stok, sembunyikan error
                hideQtyError();
            }

            input.val(newVal);
        }

        // Helper: Show Error Text
        function showQtyError(msg) {
            const span = $('#qty-error');
            span.text(msg).removeClass('opacity-0').addClass('opacity-100');

            // Auto hide setelah 3 detik agar bersih
            if (window.qtyTimeout) clearTimeout(window.qtyTimeout);
            window.qtyTimeout = setTimeout(() => hideQtyError(), 3000);
        }

        // Helper: Hide Error Text
        function hideQtyError() {
            $('#qty-error').removeClass('opacity-100').addClass('opacity-0');
        }

        // --- 8. ADD TO CART ACTION ---
        $('#btn-add-to-cart').click(function() {

            // --- VALIDASI LOGIN (Blade Directive) ---
            // Kita inject status login dari server ke variabel JS
            const isLoggedIn = {{ Auth::check() ? 'true' : 'false' }};

            if (!isLoggedIn) {
                // Jika belum login, buka modal dan hentikan proses
                openLoginModal();
                // Opsional: Tampilkan pesan di modal agar user tau kenapa dia disuruh login
                $('#login-error-msg').text("Silakan login untuk belanja.").parent().removeClass('hidden');
                return;
            }

            // --- JIKA SUDAH LOGIN, LANJUT PROSES ---
            const btn = $(this);
            const originalText = btn.html();

            // 1. Validasi Client Side (Warna & Ukuran)
            if (!selectedColor || !selectedSize) {
                alert('Pilih warna dan ukuran dulu'); // Atau pakai toast
                return;
            }

            // 2. Loading State
            btn.prop('disabled', true).html('<span class="loading loading-spinner loading-xs"></span> Menyimpan...');

            // 3. AJAX Request
            $.ajax({
                url: "{{ route('cart.store') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    shoe_slug: "{{ $shoe->slug }}", // Pastikan slug dikirim
                    color: selectedColor,
                    size: selectedSize,
                    qty: $('#qty-input').val()
                },
                success: function(response) {
                    // Balikkan tombol
                    btn.prop('disabled', false).html(originalText);

                    if (response.status === 'success') {
                        // Tampilkan Notifikasi Sukses (Bisa ganti SweetAlert nanti)
                        alert(response.message);

                        // Update Badge Cart di Navbar (Cari elemen badge di navbar Anda)
                        $('.badge-cart-count').text(response.cart_count); // Pastikan class badge di navbar sesuai
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html(originalText);

                    let msg = 'Terjadi kesalahan.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    alert(msg);
                }
            });
        });
    </script>
@endpush
