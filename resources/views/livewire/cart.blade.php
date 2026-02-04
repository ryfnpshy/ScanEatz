<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold text-slate-900 mb-8">Keranjang Belanja</h1>

    @if($cartItems->isEmpty())
        <div class="bg-white rounded-3xl p-12 text-center border border-slate-100 shadow-sm">
            <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl">
                🛒
            </div>
            <h2 class="text-xl font-bold text-slate-900 mb-2">Keranjangmu masih kosong</h2>
            <p class="text-slate-500 mb-8 max-w-md mx-auto">Sepertinya kamu belum memilih makanan lezat kami. Yuk lihat menu favorit!</p>
            <a href="/catalog" class="inline-flex items-center justify-center px-8 py-3 bg-primary-600 text-white font-bold rounded-full hover:bg-primary-700 transition-all shadow-lg shadow-primary-500/30">
                Mulai Pesan Sekarang
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Items List -->
            <div class="lg:col-span-2 space-y-4">
                @foreach($cartItems as $item)
                <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-sm border border-slate-100 flex gap-4 sm:gap-6 items-start transition-all hover:shadow-md">
                    <!-- Image -->
                    <div class="w-20 h-20 sm:w-24 sm:h-24 bg-slate-100 rounded-xl overflow-hidden flex-shrink-0">
                         <img src="{{ $item->product->image_url ?? 'https://placehold.co/200?text=' . urlencode($item->product->name) }}" 
                              class="w-full h-full object-cover" 
                              alt="{{ $item->product->name }}">
                    </div>

                    <!-- Details -->
                    <div class="flex-grow">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="font-bold text-slate-900 text-lg">{{ $item->product->name }}</h3>
                                @if($item->variant_name)
                                    <span class="text-xs text-primary-600 font-medium bg-primary-50 px-2 py-1 rounded-md">{{ $item->variant_name }}</span>
                                @endif
                                
                                {{-- Display Addons if any (simple text) --}}
                                @if(!empty($item->addons))
                                <div class="mt-1 text-xs text-slate-500">
                                    + Addons: {{ count($item->addons) }} item
                                </div>
                                @endif
                                
                                @if($item->notes)
                                <p class="text-xs text-slate-400 mt-1 italic">"{{ $item->notes }}"</p>
                                @endif
                            </div>
                            <button wire:click="removeItem({{ $item->id }})" class="text-slate-400 hover:text-red-500 transition-colors p-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>

                        <!-- Price & Qty -->
                        <div class="flex justify-between items-end mt-4">
                            <span class="font-bold text-slate-900">{{ $item->formatted_line_total }}</span>
                            
                            <div class="flex items-center gap-3 bg-slate-50 rounded-full px-1 py-1 border border-slate-200">
                                <button wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})" class="w-8 h-8 rounded-full bg-white shadow-sm flex items-center justify-center text-slate-600 hover:text-primary-600 disabled:opacity-50">
                                    -
                                </button>
                                <span class="font-medium text-slate-900 w-4 text-center text-sm">{{ $item->quantity }}</span>
                                <button wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})" class="w-8 h-8 rounded-full bg-white shadow-sm flex items-center justify-center text-slate-600 hover:text-primary-600">
                                    +
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 sticky top-24">
                    <h3 class="font-bold text-slate-900 text-lg mb-6">Ringkasan Pesanan</h3>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-slate-600">
                            <span>Total Item</span>
                            <span>{{ $cartItems->sum('quantity') }}</span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 pt-4 mb-6">
                        <div class="flex justify-between items-center text-lg font-bold text-slate-900">
                            <span>Total Estimasi</span>
                            <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <p class="text-xs text-slate-400 mt-2">*Belum termasuk ongkir & pajak</p>
                    </div>

                    <a href="/checkout" class="w-full block text-center py-4 bg-primary-600 text-white font-bold rounded-2xl hover:bg-primary-700 transition-all shadow-lg shadow-primary-500/30 transform hover:-translate-y-1">
                        Lanjut ke Pembayaran
                    </a>
                    
                    <a href="/catalog" class="w-full block text-center py-3 mt-3 text-slate-500 text-sm font-medium hover:text-primary-600 transition-colors">
                        Tambah Menu Lain
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
