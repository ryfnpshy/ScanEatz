<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12" wire:poll.10s="loadOrder">
    <!-- Header -->
    <div class="text-center mb-12">
        <h1 class="text-3xl font-bold text-slate-900 mb-2">Status Pesanan</h1>
        <p class="text-slate-500">Kode Pesanan: <span class="font-mono font-bold text-slate-900">{{ $order->order_code }}</span></p>
    </div>

    <!-- Status Timeline Card -->
    <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 mb-8">
        <!-- Main Status -->
        <div class="flex flex-col items-center justify-center mb-8">
            <div class="w-20 h-20 rounded-full bg-primary-50 flex items-center justify-center text-4xl mb-4 animate-pulse">
                @switch($order->status)
                    @case('PENDING') ⏳ @break
                    @case('CONFIRMED') ✅ @break
                    @case('COOKING') 🍳 @break
                    @case('READY') 🥡 @break
                    @case('ON_DELIVERY') 🛵 @break
                    @case('COMPLETED') 🎉 @break
                    @case('CANCELLED') ❌ @break
                    @default ❓
                @endswitch
            </div>
            <h2 class="text-2xl font-bold text-slate-900">{{ $order->status_display }}</h2>
            <p class="text-slate-500 mt-1">Estimasi Tiba: {{ $order->eta_minutes }} menit</p>
        </div>

        <!-- Progress Bar -->
        <div class="relative mb-12">
            <div class="absolute top-1/2 left-0 w-full h-1 bg-slate-100 -translate-y-1/2 rounded-full"></div>
            @php
                $steps = ['PENDING', 'CONFIRMED', 'COOKING', 'READY', 'ON_DELIVERY', 'COMPLETED'];
                $currentIdx = array_search($order->status, $steps);
                if ($currentIdx === false) $currentIdx = -1;
                $percent = ($currentIdx / (count($steps) - 1)) * 100;
            @endphp
            <div class="absolute top-1/2 left-0 h-1 bg-primary-600 -translate-y-1/2 rounded-full transition-all duration-1000" style="width: {{ $percent }}%"></div>
            
            <div class="relative flex justify-between">
                @foreach($steps as $idx => $step)
                    <div class="flex flex-col items-center gap-2">
                        <div class="w-4 h-4 rounded-full border-2 {{ $idx <= $currentIdx ? 'bg-primary-600 border-primary-600' : 'bg-white border-slate-300' }} transition-colors z-10"></div>
                        <span class="text-[10px] font-bold uppercase tracking-wider {{ $idx <= $currentIdx ? 'text-primary-700' : 'text-slate-400' }} hidden sm:block">
                            {{ str_replace('_', ' ', $step) }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Timeline Details -->
        <div class="space-y-6">
            <h3 class="font-bold text-slate-900 border-b border-slate-100 pb-2">Riwayat Status</h3>
            @foreach(array_reverse($timeline) as $event)
            <div class="flex gap-4">
                <div class="flex flex-col items-center">
                    <div class="w-2 h-2 rounded-full bg-slate-300 mt-2"></div>
                    <div class="w-0.5 flex-grow bg-slate-100 my-1"></div>
                </div>
                <div class="pb-6">
                    <p class="font-bold text-slate-700 text-sm">{{ $event['status'] }}</p>
                    <p class="text-xs text-slate-400">
                        {{ \Carbon\Carbon::parse($event['timestamp'])->isoFormat('dddd, D MMM Y HH:mm') }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Order Details -->
    <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100">
        <h3 class="font-bold text-slate-900 mb-6">Detail Pesanan</h3>
        
        <div class="space-y-4 mb-6">
            @foreach($order->items as $item)
            <div class="flex justify-between items-start">
                <div>
                    <span class="font-bold text-slate-900">{{ $item->quantity }}x {{ $item->product_name }}</span>
                    @if($item->variant_name)
                        <span class="text-xs text-primary-600 bg-primary-50 px-2 py-0.5 rounded ml-2">{{ $item->variant_name }}</span>
                    @endif
                    <p class="text-xs text-slate-500 mt-1">Catatan: {{ $item->notes ?: '-' }}</p>
                </div>
                <span class="font-medium text-slate-700">{{ $item->formatted_line_total }}</span>
            </div>
            @endforeach
        </div>

        <div class="border-t border-slate-100 pt-4 space-y-2 text-sm">
            <div class="flex justify-between text-slate-600">
                <span>Subtotal</span>
                <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-slate-600">
                <span>Ongkir</span>
                <span>Rp {{ number_format($order->delivery_fee, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between font-bold text-slate-900 text-lg pt-2">
                <span>Total</span>
                <span>{{ $order->formatted_total }}</span>
            </div>
        </div>
    </div>
    
    <div class="mt-8 text-center">
        <a href="/" class="text-primary-600 hover:text-primary-700 font-medium">← Kembali ke Beranda</a>
    </div>
</div>
