<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold text-slate-900 mb-8">Checkout Pesanan</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left: Form -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- Fulfillment Type -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                <h3 class="font-bold text-slate-900 text-lg mb-4">Metode Pemesanan</h3>
                <div class="flex gap-4">
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" wire:model.live="fulfillmentType" value="delivery" class="peer sr-only">
                        <div class="rounded-xl border-2 border-slate-200 p-4 text-center peer-checked:border-primary-600 peer-checked:bg-primary-50 transition-all">
                            <div class="text-2xl mb-1">🛵</div>
                            <span class="font-bold text-slate-700 peer-checked:text-primary-700">Delivery</span>
                        </div>
                    </label>
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" wire:model.live="fulfillmentType" value="pickup" class="peer sr-only">
                        <div class="rounded-xl border-2 border-slate-200 p-4 text-center peer-checked:border-primary-600 peer-checked:bg-primary-50 transition-all">
                            <div class="text-2xl mb-1">🏪</div>
                            <span class="font-bold text-slate-700 peer-checked:text-primary-700">Ambil Sendiri</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Address (If Delivery) -->
            @if($fulfillmentType === 'delivery')
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                <h3 class="font-bold text-slate-900 text-lg mb-4">Alamat Pengiriman</h3>
                
                @if($addresses->isEmpty())
                    <div class="text-center py-6 bg-slate-50 rounded-xl border border-dashed border-slate-300">
                        <p class="text-slate-500 mb-4">Belum ada alamat tersimpan.</p>
                        <button class="text-primary-600 font-bold hover:underline">+ Tambah Alamat Baru</button>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($addresses as $address)
                        <label class="block relative cursor-pointer group">
                            <input type="radio" wire:model.live="selectedAddressId" value="{{ $address->id }}" class="peer sr-only">
                            <div class="rounded-xl border border-slate-200 p-4 flex gap-4 items-start peer-checked:border-primary-600 peer-checked:ring-1 peer-checked:ring-primary-600 hover:border-primary-300 transition-all">
                                <div class="mt-1">
                                    <div class="w-5 h-5 rounded-full border border-slate-300 peer-checked:border-primary-600 peer-checked:bg-primary-600"></div>
                                </div>
                                <div>
                                    <span class="font-bold text-slate-900 block">{{ $address->label }} <span class="text-xs font-normal text-slate-500">({{ $address->receiver_name }})</span></span>
                                    <p class="text-sm text-slate-600 mt-1">{{ $address->full_address }}</p>
                                </div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                @endif
            </div>
            @endif

            <!-- Outlet Selection -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                <h3 class="font-bold text-slate-900 text-lg mb-4">Pilih Outlet</h3>
                <select wire:model.live="selectedOutletId" class="w-full rounded-xl border-slate-200 focus:border-primary-500 focus:ring-primary-500">
                    <option value="">-- Pilih Outlet --</option>
                    @foreach($outlets as $outlet)
                        <option value="{{ $outlet->id }}">{{ $outlet->name }} ({{ $outlet->district }})</option>
                    @endforeach
                </select>
                @if($errorMessage)
                    <p class="text-red-500 text-sm mt-2 font-medium">⚠️ {{ $errorMessage }}</p>
                @endif
            </div>

            <!-- Payment -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                <h3 class="font-bold text-slate-900 text-lg mb-4">Metode Pembayaran</h3>
                <div class="space-y-3">
                    <label class="block relative cursor-pointer">
                        <input type="radio" wire:model="paymentMethod" value="cod" class="peer sr-only">
                        <div class="rounded-xl border border-slate-200 p-4 flex items-center gap-3 peer-checked:border-primary-600 peer-checked:bg-primary-50">
                            <span class="text-xl">💵</span>
                            <span class="font-medium text-slate-900">Bayar di Tempat (COD)</span>
                        </div>
                    </label>
                    <label class="block relative cursor-pointer">
                        <input type="radio" wire:model="paymentMethod" value="transfer" class="peer sr-only">
                        <div class="rounded-xl border border-slate-200 p-4 flex items-center gap-3 peer-checked:border-primary-600 peer-checked:bg-primary-50">
                            <span class="text-xl">🏦</span>
                            <span class="font-medium text-slate-900">Transfer Bank / E-Wallet</span>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Right: Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 sticky top-24">
                <h3 class="font-bold text-slate-900 text-lg mb-6">Rincian Pembayaran</h3>

                <!-- Coupon -->
                <div class="flex gap-2 mb-6">
                    <input type="text" wire:model="couponCode" placeholder="Kode Promo" class="flex-grow rounded-lg border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500">
                    <button wire:click="applyCoupon" class="bg-slate-900 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-slate-800">Pakai</button>
                </div>

                <div class="space-y-3 mb-6 text-sm">
                    <div class="flex justify-between text-slate-600">
                        <span>Subtotal ({{ $cart->total_items }} item)</span>
                        <span>Rp {{ number_format($totals['subtotal'] ?? 0, 0, ',', '.') }}</span>
                    </div>
                    
                    @if($fulfillmentType === 'delivery')
                    <div class="flex justify-between text-slate-600">
                        <span>Ongkos Kirim</span>
                        <span>Rp {{ number_format($totals['delivery_fee'] ?? 0, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    
                    <div class="flex justify-between text-slate-600">
                        <span>Pajak (10%)</span>
                        <span>Rp {{ number_format($totals['tax'] ?? 0, 0, ',', '.') }}</span>
                    </div>

                    @if(($totals['discount'] ?? 0) > 0)
                    <div class="flex justify-between text-primary-600 font-medium">
                        <span>Diskon</span>
                        <span>- Rp {{ number_format($totals['discount'], 0, ',', '.') }}</span>
                    </div>
                    @endif
                </div>

                <div class="border-t border-slate-100 pt-4 mb-6">
                    <div class="flex justify-between items-center text-lg font-bold text-slate-900">
                        <span>Total Bayar</span>
                        <span>Rp {{ number_format($totals['total'] ?? 0, 0, ',', '.') }}</span>
                    </div>
                </div>

                <button wire:click="placeOrder" wire:loading.attr="disabled" class="w-full block text-center py-4 bg-primary-600 text-white font-bold rounded-2xl hover:bg-primary-700 transition-all shadow-lg shadow-primary-500/30 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove>Buat Pesanan</span>
                    <span wire:loading>Memproses...</span>
                </button>
                
                @if($errorMessage)
                <p class="text-center text-red-500 text-xs mt-3">{{ $errorMessage }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
