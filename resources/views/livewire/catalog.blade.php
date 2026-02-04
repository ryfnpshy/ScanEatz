<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ showFilters: false }">
    
    <!-- Search & Filters Header -->
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-8 sticky top-16 bg-slate-50/95 backdrop-blur z-20 py-4 transition-all">
        <!-- Categories (Horizontal Scroll) -->
        <div class="w-full md:w-auto flex overflow-x-auto gap-2 pb-2 md:pb-0 scrollbar-hide">
            <button 
                wire:click="setCategory('all')" 
                class="px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-colors border {{ $categorySlug === 'all' ? 'bg-primary-600 text-white border-primary-600' : 'bg-white text-slate-700 border-slate-200 hover:border-primary-500' }}">
                Semua
            </button>
            @foreach($categories as $cat)
            <button 
                wire:click="setCategory('{{ $cat->slug }}')" 
                class="px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-colors border {{ $categorySlug === $cat->slug ? 'bg-primary-600 text-white border-primary-600' : 'bg-white text-slate-700 border-slate-200 hover:border-primary-500' }}">
                {{ $cat->name }}
            </button>
            @endforeach
        </div>

        <!-- Sort & Search -->
        <div class="w-full md:w-auto flex gap-3">
            <div class="relative flex-grow md:flex-grow-0 md:w-64">
                <input wire:model.live.debounce.300ms="search" type="text" class="w-full pl-10 pr-4 py-2 rounded-lg border border-slate-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 text-sm" placeholder="Cari makanan...">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
            </div>
            
            <select wire:model.live="sortBy" class="pl-3 pr-8 py-2 rounded-lg border border-slate-200 focus:border-primary-500 focus:ring-1 focus:ring-primary-500 text-sm bg-white cursor-pointer">
                <option value="popularity">Populer</option>
                <option value="rating">Rating</option>
                <option value="price_asc">Harga Terendah</option>
                <option value="price_desc">Harga Tertinggi</option>
            </select>
        </div>
    </div>

    <!-- Loading State -->
    <div wire:loading.flex class="w-full justify-center py-12">
        <div class="flex items-center gap-3">
             <svg class="animate-spin h-8 w-8 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
             <span class="text-slate-500 font-medium">Memuat menu lezat...</span>
        </div>
    </div>

    <!-- Product Grid -->
    <div wire:loading.remove class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
        @forelse($products as $product)
        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-xl transition-all duration-500 group flex flex-col h-full overflow-hidden">
            <!-- Image Area with Overlay matching the reference -->
            <div class="relative aspect-[4/3] overflow-hidden bg-slate-100">
                <img src="{{ $product->image_url ?? 'https://placehold.co/400?text=' . urlencode($product->name) }}" 
                     alt="{{ $product->name }}" 
                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                
                <!-- "Lihat Detail" Overlay from reference -->
                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center backdrop-blur-[2px]">
                    <div class="flex items-center gap-2 text-white font-medium transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                        </svg>
                        <span>Lihat Detail</span>
                    </div>
                </div>

                @if($product->average_rating > 4.0)
                <div class="absolute top-4 right-4 bg-white/90 backdrop-blur text-slate-900 text-xs font-bold px-3 py-1.5 rounded-full shadow-sm flex items-center gap-1.5">
                    <span class="text-yellow-500">★</span> {{ number_format($product->average_rating, 1) }}
                </div>
                @endif
            </div>

            <!-- Content Area matching the reference -->
            <div class="p-6 flex-grow flex flex-col">
                <h3 class="font-bold text-primary-600 text-xl mb-2 leading-snug group-hover:text-primary-700 transition-colors line-clamp-1">{{ $product->name }}</h3>
                <p class="text-slate-500 text-sm mb-6 line-clamp-2 leading-relaxed flex-grow">{{ $product->description }}</p>

                <!-- Tags Area matching the reference -->
                <div class="flex flex-wrap gap-2 mb-6">
                    <span class="px-4 py-1.5 rounded-full bg-primary-50 text-primary-600 text-xs font-semibold border border-primary-100">
                        {{ $product->category->name }}
                    </span>
                    @if($product->is_halal)
                    <span class="px-4 py-1.5 rounded-full bg-emerald-50 text-emerald-600 text-xs font-semibold border border-emerald-100">
                        Halal
                    </span>
                    @endif
                </div>

                <!-- Footer Action -->
                <div class="pt-4 border-t border-slate-50 flex items-center justify-between">
                    <div class="flex flex-col">
                        <span class="text-xs text-slate-400 font-medium">Mulai dari</span>
                        <span class="font-bold text-xl text-slate-900">{{ $product->formatted_price }}</span>
                    </div>
                    
                    <button wire:click="addToCart({{ $product->id }})" 
                            class="group/btn flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-xl hover:bg-primary-700 transition-all shadow-md shadow-primary-200 active:scale-95"
                            aria-label="Add to cart">
                        <svg class="w-5 h-5 group-hover/btn:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full py-12 text-center text-slate-500">
            <p class="text-lg">Menu tidak ditemukan.</p>
            <button wire:click="setCategory('all')" class="mt-2 text-primary-600 hover:underline">Lihat semua menu</button>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $products->links() }}
    </div>

    <!-- Notifications (Toast) -->
    <div x-data="{ show: false, message: '', type: 'success' }"
         @notify.window="show = true; message = $event.detail.message; type = $event.detail.type || 'success'; setTimeout(() => show = false, 3000)"
         x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-2"
         class="fixed bottom-6 right-6 z-50 px-6 py-3 rounded-xl shadow-lg flex items-center gap-3 text-white font-medium"
         :class="type === 'success' ? 'bg-slate-900' : 'bg-red-600'">
        
        <span x-text="message"></span>
    </div>
</div>
