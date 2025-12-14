@extends('layouts/frontend/index')
@section('title', 'Keranjang | ShoeCycle')

@section('frontend-content')
    <section class="py-10 bg-slate-50 min-h-[80vh]">
        <div class="container mx-auto px-4">

            {{-- Header --}}
            <h1 class="text-3xl font-bold text-gray-900 font-heading mb-8">Keranjang Belanja</h1>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                {{-- LEFT COLUMN: CART ITEMS --}}
                <div class="lg:col-span-8 space-y-4">

                    {{-- Select All Bar --}}
                    <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between">
                        <label class="flex items-center gap-3 cursor-pointer select-none">
                            <input type="checkbox" id="check-all" class="checkbox checkbox-primary checkbox-sm rounded-md" checked />
                            <span class="text-gray-700 font-medium">Pilih Semua <span class="text-gray-400 font-normal">(2)</span></span>
                        </label>
                        <button class="text-red-500 hover:text-red-700 text-sm font-medium transition-colors">Hapus</button>
                    </div>

                    {{-- Cart Items Loop (Mock Data untuk Tampilan) --}}
                    @php
                        // Contoh Data Dummy (Nanti diganti data dari Database/Controller)
                        $cartItems = [
                            [
                                'id' => 1,
                                'name' => 'Nike Air Zoom Pegasus 40',
                                'brand' => 'Nike',
                                'variant_color' => 'Black/White',
                                'variant_size' => '42',
                                'price' => 2450000,
                                'discount_price' => 2050000, // Ada diskon
                                'image' => 'assets/upload/testing/dummy.jpg', // Ganti logic image primary nanti
                                'qty' => 1,
                                'stock' => 5,
                            ],
                            [
                                'id' => 2,
                                'name' => 'Adidas Ultraboost Light',
                                'brand' => 'Adidas',
                                'variant_color' => 'Cloud White',
                                'variant_size' => '40',
                                'price' => 3100000,
                                'discount_price' => null, // Tidak ada diskon
                                'image' => 'assets/upload/testing/dummy.jpg',
                                'qty' => 1,
                                'stock' => 3,
                            ],
                        ];
                    @endphp

                    @foreach ($cartItems as $item)
                        <div class="bg-white p-4 sm:p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300 group">
                            {{-- Brand Header (Optional jika multi-store, tapi bagus untuk estetika) --}}
                            <div class="flex items-center gap-2 mb-4 pb-3 border-b border-gray-50">
                                <div class="badge badge-primary badge-outline badge-sm">{{ $item['brand'] }}</div>
                                <span class="text-xs text-gray-400">Garansi 100% Original</span>
                            </div>

                            <div class="flex gap-4 sm:gap-6">
                                {{-- Checkbox --}}
                                <div class="flex items-center">
                                    <input type="checkbox" class="checkbox checkbox-primary checkbox-sm rounded-md item-checkbox" checked />
                                </div>

                                {{-- Image --}}
                                <div class="w-24 h-24 sm:w-28 sm:h-28 bg-gray-50 rounded-xl flex-shrink-0 border border-gray-300 overflow-hidden relative">
                                    <img src="{{ asset($item['image']) }}" alt="{{ $item['name'] }}" class="w-full h-full object-contain mix-blend-multiply p-2">
                                </div>

                                {{-- Details --}}
                                <div class="flex-1 flex flex-col justify-between">
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900 leading-snug mb-1">
                                            <a href="#" class="hover:text-blue-600 transition-colors">{{ $item['name'] }}</a>
                                        </h3>
                                        <p class="text-sm text-gray-500 mb-2">
                                            Warna: <span class="text-gray-900 font-medium">{{ $item['variant_color'] }}</span> •
                                            Ukuran: <span class="text-gray-900 font-medium">{{ $item['variant_size'] }}</span>
                                        </p>
                                    </div>

                                    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                                        {{-- Price --}}
                                        <div>
                                            <div class="text-lg font-bold text-gray-900">Rp {{ number_format($item['price'], 0, ',', '.') }}</div>
                                        </div>

                                        {{-- Actions & Qty --}}
                                        <div class="flex items-center gap-4">
                                            {{-- Wishlist & Delete --}}
                                            <div class="flex items-center gap-2 text-gray-400">
                                                <button class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 hover:text-red-500 transition-colors" title="Pindahkan ke Wishlist">
                                                    <i class="far fa-heart"></i>
                                                </button>
                                                <button class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 hover:text-gray-600 transition-colors" title="Hapus">
                                                    <i class="far fa-trash-alt"></i>
                                                </button>
                                            </div>

                                            {{-- Stepper --}}
                                            <div class="flex items-center border border-gray-200 rounded-lg h-9">
                                                <button class="w-8 h-full flex items-center justify-center text-gray-500 hover:bg-gray-100 rounded-l-lg transition-colors" onclick="changeQty(this, -1, {{ $item['stock'] }})">
                                                    <i class="fas fa-minus text-xs"></i>
                                                </button>
                                                <input type="number" value="{{ $item['qty'] }}" class="w-10 h-full text-center border-none text-sm font-bold text-gray-900 focus:ring-0 p-0" readonly>
                                                <button class="w-8 h-full flex items-center justify-center text-blue-600 hover:bg-blue-50 rounded-r-lg transition-colors" onclick="changeQty(this, 1, {{ $item['stock'] }})">
                                                    <i class="fas fa-plus text-xs"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>

                {{-- RIGHT COLUMN: SUMMARY (Sticky) --}}
                <div class="lg:col-span-4">
                    <div class="sticky top-24 space-y-4">
                        {{-- Summary Box --}}
                        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-lg shadow-blue-100/50">
                            <h3 class="font-bold text-lg text-gray-900 mb-4">Ringkasan Belanja</h3>

                            <div class="flex justify-between items-center mb-6">
                                <span class="font-light text-lg text-gray-900">Total</span>
                                <span class="font-bold text-xl text-blue-600">Rp 4.750.000</span>
                            </div>

                            <button class="btn btn-primary w-full rounded-xl text-white font-bold h-12 shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 hover:-translate-y-0.5 transition-all">
                                Beli (2)
                            </button>
                        </div>

                        {{-- Secure Transaction Badge --}}
                        <div class="flex items-center justify-center gap-2 text-xs text-gray-400">
                            <i class="fas fa-shield-alt"></i> Transaksi Aman & Terpercaya
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        /* Hilangkan spinner pada input number */
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Logic sederhana untuk UI Stepper (+ / -)
        function changeQty(btn, change, maxStock) {
            const input = btn.parentElement.querySelector('input');
            let newVal = parseInt(input.val()) || parseInt(input.value) + change; // Handle jQuery/Vanilla

            // Jika menggunakan jQuery:
            // const $input = $(btn).siblings('input');
            // let currentVal = parseInt($input.val());
            // let newVal = currentVal + change;

            // Fallback Vanilla JS logic inside onclick for prototype:
            // (Di implementasi real nanti pakai logic yg sama dgn detail page)

            let currentVal = parseInt(input.value);
            newVal = currentVal + change;

            if (newVal < 1) newVal = 1;
            if (newVal > maxStock) {
                alert('Stok maksimal tercapai');
                newVal = maxStock;
            }

            input.value = newVal;

            // Disini nanti panggil AJAX update cart ke backend
        }

        // Logic Check All
        document.getElementById('check-all').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.item-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    </script>
@endpush
