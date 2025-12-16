@extends('layouts/frontend/index')
@section('title', 'Keranjang | ShoeCycle')

@section('frontend-content')
    <section class="py-10 bg-slate-50 min-h-[80vh]">
        <div class="container mx-auto px-4">

            <h1 class="text-3xl font-bold text-gray-900 font-heading mb-8">Keranjang</h1>

            @if ($cartItems->count() > 0)
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                    {{-- LEFT COLUMN: CART ITEMS --}}
                    <div class="lg:col-span-8 space-y-4">

                        {{-- Select All Bar --}}
                        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between">
                            <label class="flex items-center gap-3 cursor-pointer select-none">
                                <input type="checkbox" id="check-all" class="checkbox checkbox-primary checkbox-sm rounded-md" checked />
                                <span class="text-gray-700 font-medium">Pilih Semua <span class="text-gray-400 font-normal">({{ $cartItems->count() }})</span></span>
                            </label>
                            {{-- Optional: Fitur Hapus Banyak (Bulk Delete) bisa dikembangkan nanti --}}
                        </div>

                        {{-- Loop Cart Items --}}
                        @foreach ($cartItems as $item)
                            @php
                                // Ambil gambar pertama dari varian atau fallback dummy
                                $image = $item->variant->images->first();
                                $imageUrl = $image ? asset('storage/' . $image->image_path) : asset('assets/upload/testing/dummy.jpg');

                                // Cek Stok
                                $isOutOfStock = $item->variant->stock < 1;
                            @endphp

                            <div class="bg-white p-4 sm:p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300 group cart-item-row" id="cart-row-{{ $item->id }}">

                                {{-- Brand Header --}}
                                <div class="flex items-center gap-2 mb-4 pb-3 border-b border-gray-50">
                                    <div class="badge badge-primary badge-outline badge-sm">{{ $item->variant->shoe->brand_name ?? 'Brand' }}</div>
                                    <span class="text-xs text-gray-400">Garansi 100% Original</span>
                                </div>

                                <div class="flex gap-4 sm:gap-6">
                                    {{-- Checkbox --}}
                                    <div class="flex items-center">
                                        <input type="checkbox" class="checkbox checkbox-primary checkbox-sm rounded-md item-checkbox" data-unit-price="{{ $item->variant->price }}" checked {{ $isOutOfStock ? 'disabled' : '' }} />
                                    </div>

                                    {{-- Image --}}
                                    <div class="w-24 h-24 sm:w-28 sm:h-28 bg-gray-50 rounded-xl flex-shrink-0 border border-gray-100 overflow-hidden relative">
                                        <img src="{{ $imageUrl }}" alt="{{ $item->variant->shoe->name }}" class="w-full h-full object-contain mix-blend-multiply p-2 {{ $isOutOfStock ? 'grayscale opacity-50' : '' }}">
                                        @if ($isOutOfStock)
                                            <div class="absolute inset-0 flex items-center justify-center bg-black/10">
                                                <span class="badge badge-error badge-sm">Habis</span>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Details --}}
                                    <div class="flex-1 flex flex-col justify-between">
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-900 leading-snug mb-1">
                                                <a href="{{ route('detail-shoes', $item->variant->shoe->slug) }}" class="hover:text-blue-600 transition-colors">
                                                    {{ $item->variant->shoe->name }}
                                                </a>
                                            </h3>
                                            <p class="text-sm text-gray-500 mb-2">
                                                Warna: <span class="text-gray-900 font-medium">{{ $item->variant->color }}</span> •
                                                Ukuran: <span class="text-gray-900 font-medium">{{ $item->variant->size }}</span>
                                            </p>
                                        </div>

                                        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                                            {{-- Price --}}
                                            <div>
                                                <div class="text-lg font-bold text-gray-900">Rp {{ number_format($item->variant->price, 0, ',', '.') }}</div>
                                            </div>

                                            {{-- Actions & Qty --}}
                                            <div class="flex items-center gap-4">
                                                {{-- Delete Button --}}
                                                <button class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-red-50 hover:text-red-500 transition-colors text-gray-400" onclick="deleteCartItem({{ $item->id }})" title="Hapus">
                                                    <i class="far fa-trash-alt"></i>
                                                </button>

                                                {{-- Stepper --}}
                                                @if (!$isOutOfStock)
                                                    <div class="flex items-center border border-gray-200 rounded-lg h-9">
                                                        <button class="w-8 h-full flex items-center justify-center text-gray-500 hover:bg-gray-100 rounded-l-lg transition-colors" onclick="updateCartQty({{ $item->id }}, -1)">
                                                            <i class="fas fa-minus text-xs"></i>
                                                        </button>
                                                        <input type="number" id="qty-{{ $item->id }}" value="{{ $item->quantity }}" class="w-10 h-full text-center border-none text-sm font-bold text-gray-900 focus:ring-0 p-0 bg-transparent" readonly>
                                                        <button class="w-8 h-full flex items-center justify-center text-blue-600 hover:bg-blue-50 rounded-r-lg transition-colors" onclick="updateCartQty({{ $item->id }}, 1)">
                                                            <i class="fas fa-plus text-xs"></i>
                                                        </button>
                                                    </div>
                                                @endif
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
                            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-lg shadow-blue-100/50">
                                <h3 class="font-bold text-lg text-gray-900 mb-4">Ringkasan</h3>

                                <div class="flex justify-between items-center mb-6">
                                    <span class="font-bold text-lg text-gray-900">Total Belanja</span>
                                    <span class="font-bold text-xl text-blue-600" id="grand-total-display">
                                        Rp {{ number_format($grandTotal, 0, ',', '.') }}
                                    </span>
                                </div>

                                {{-- SESUDAHNYA (ANCHOR LINK) --}}
                                <a href="{{ route('checkout.index') }}" class="btn btn-primary w-full rounded-xl text-white font-bold h-12 shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 hover:-translate-y-0.5 transition-all">
                                    Beli (<span id="total-items-count">{{ $cartItems->count() }}</span>)
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            @else
                {{-- Empty State --}}
                <div class="text-center py-20 bg-white rounded-3xl border border-gray-100 shadow-sm">
                    <div class="w-32 h-32 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-shopping-cart text-5xl text-blue-300"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Keranjang Belanja Kosong</h2>
                    <p class="text-gray-500 mb-8">Wah, keranjang belanjaanmu kosong nih.<br>Yuk, isi dengan barang-barang impianmu!</p>
                    <a href="{{ route('landing-page') }}" class="btn btn-primary rounded-xl px-8 text-white">Mulai Belanja</a>
                </div>
            @endif
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // --- 1. FUNGSI HITUNG TOTAL (DIPERBAIKI) ---
        function calculateTotal() {
            let total = 0;
            let count = 0;

            document.querySelectorAll('.item-checkbox:checked').forEach(checkbox => {
                // 1. Ambil row elemen parent
                const row = checkbox.closest('.cart-item-row');

                // 2. Ambil Harga Satuan dari atribut data-unit-price (LEBIH AMAN)
                // Pastikan diparsing ke Float/Int agar tidak dianggap string
                const unitPrice = parseFloat(checkbox.getAttribute('data-unit-price')) || 0;

                // 3. Ambil Quantity dari input number
                const qtyInput = row.querySelector('input[type="number"]');
                const qty = parseInt(qtyInput.value) || 0;

                // 4. Hitung
                total += unitPrice * qty;
                count++;
            });

            // Update UI
            document.getElementById('grand-total-display').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
            document.getElementById('total-items-count').innerText = count;

            // Disable tombol beli jika tidak ada yang dipilih
            const btnBuy = document.querySelector('.btn-primary.w-full'); // Selector disesuaikan agar kena tombol yg benar
            if (count === 0) {
                btnBuy.disabled = true;
                btnBuy.classList.add('cursor-not-allowed', 'bg-blue-400');
            } else {
                btnBuy.disabled = false;
                btnBuy.classList.remove('cursor-not-allowed', 'bg-blue-400');
            }
        }

        // Event Listener untuk Checkbox Satuan
        document.querySelectorAll('.item-checkbox').forEach(cb => {
            cb.addEventListener('change', calculateTotal);
        });

        // Event Listener Check All
        const checkAll = document.getElementById('check-all');
        if (checkAll) {
            checkAll.addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.item-checkbox');
                checkboxes.forEach(cb => {
                    if (!cb.disabled) cb.checked = this.checked;
                });
                calculateTotal();
            });
        }

        // Panggil saat load pertama kali
        calculateTotal();


        // --- 2. UPDATE QTY (MODIFIED) ---
        function updateCartQty(id, change) {
            const input = document.getElementById(`qty-${id}`);
            let currentQty = parseInt(input.value);
            let newQty = currentQty + change;

            if (newQty < 1) return;

            input.value = newQty; // Optimistic UI

            fetch(`/cart/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        qty: newQty
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // PENTING: Update Total Lokal juga setelah sukses
                        calculateTotal();
                    } else {
                        input.value = currentQty;
                        if (data.reset_qty) input.value = data.reset_qty;
                        alert(data.message);
                        calculateTotal();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    input.value = currentQty;
                    calculateTotal();
                });
        }

        // --- 3. PROSES CHECKOUT (YANG DIPILIH SAJA) ---
        function proceedToCheckout() {
            const selectedIds = [];
            document.querySelectorAll('.item-checkbox:checked').forEach(cb => {
                // Kita butuh ID Cart dari row ID
                const rowId = cb.closest('.cart-item-row').id.replace('cart-row-', '');
                selectedIds.push(rowId);
            });

            if (selectedIds.length === 0) {
                alert('Pilih minimal 1 barang untuk dibeli.');
                return;
            }

            // Kirim ID yang dipilih ke Checkout Controller
            // Cara paling aman: Redirect dengan query param atau POST form
            // Kita pakai Query Param sederhana: /checkout?items=1,2,3
            window.location.href = "{{ route('checkout.index') }}?items=" + selectedIds.join(',');
        }

        // Bind tombol beli ke fungsi baru ini
        document.querySelector('.btn-primary.w-full').addEventListener('click', proceedToCheckout);

        // Hapus href di tombol beli (jika masih <a>) agar tidak langsung pindah
        document.querySelector('.btn-primary.w-full').removeAttribute('href');

        // --- 2. DELETE ITEM ---
        function deleteCartItem(id) {
            if (!confirm('Yakin ingin menghapus barang ini dari keranjang?')) return;

            fetch(`/cart/destroy/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Hapus row dari DOM dengan animasi
                        const row = document.getElementById(`cart-row-${id}`);
                        row.style.opacity = '0';
                        setTimeout(() => {
                            row.remove();
                            // Jika kosong, reload agar muncul empty state
                            if (document.querySelectorAll('.cart-item-row').length === 0) {
                                window.location.reload();
                            } else {
                                // Trigger update total (bisa panggil updateQty palsu atau reload simple)
                                // Untuk simplisitas saat ini, reload agar total benar
                                window.location.reload();
                            }
                        }, 300);
                    } else {
                        alert('Gagal menghapus item.');
                    }
                });
        }
    </script>
@endpush
