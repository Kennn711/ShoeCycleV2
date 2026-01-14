<div class="card-body p-0">
    <div class="p-4 bg-gray-50/50 border-b border-gray-100 flex justify-between items-center">
        <h3 class="font-bold text-gray-900">Keranjang Belanja</h3>
    </div>

    @if (isset($cartItems) && $cartItems->count() > 0)
        <div class="max-h-80 overflow-y-auto p-4 space-y-4 no-scrollbar">
            @foreach ($cartItems as $item)
                <div class="flex gap-4 items-center group">
                    <div class="w-14 h-14 rounded-xl bg-gray-50 flex-shrink-0 border border-gray-100 overflow-hidden p-1">
                        @php
                            $primaryImg = $item->variant->images->where('is_primary', true)->first();
                            $imgUrl = $primaryImg ? asset('storage/' . $primaryImg->image_path) : asset('assets/upload/testing/dummy.jpg');
                        @endphp
                        <img src="{{ $imgUrl }}" class="w-full h-full object-contain mix-blend-multiply group-hover:scale-110 transition-transform">
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-bold text-gray-900 truncate mb-0.5">{{ $item->variant->shoe->name }}</h4>
                        {{-- Menggunakan $item->quantity sesuai database --}}
                        <p class="text-[10px] text-gray-500 uppercase font-medium">Size {{ $item->variant->size }} | {{ $item->quantity }}pcs</p>
                        <p class="text-sm font-bold text-blue-600">Rp {{ number_format($item->variant->price * $item->quantity, 0, ',', '.') }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="p-4 bg-gray-50 border-t border-gray-100">
            <div class="grid grid-cols-1 gap-2">
                <a href="{{ route('cart.index') }}" class="btn btn-primary btn-md rounded-xl text-white font-bold shadow-lg shadow-blue-200">
                    Lihat Keranjang
                </a>
            </div>
        </div>
    @else
        <div class="py-12 px-6 text-center">
            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                <i class="fas fa-shopping-basket text-2xl text-gray-300"></i>
            </div>
            <p class="text-sm font-bold text-gray-900">Wah, keranjangmu kosong</p>
            <p class="text-xs text-gray-400 mt-1 mb-6">Yuk, cari sepatu impianmu dan isi keranjang ini!</p>
            <a href="{{ route('shoes-collection.index') }}" class="btn btn-sm border-blue-600 text-white bg-blue-500 hover:bg-blue-600 hover:border-blue-600 rounded-lg px-6">
                Mulai Belanja
            </a>
        </div>
    @endif
</div>
