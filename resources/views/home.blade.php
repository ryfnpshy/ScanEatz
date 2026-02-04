<x-app-layout>
    <!-- Hero Section -->
    <div class="relative bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto">
            <div class="relative z-10 pb-8 bg-white sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
                <main class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
                    <div class="sm:text-center lg:text-left">
                        <h1 class="text-4xl tracking-tight font-extrabold text-slate-900 sm:text-5xl md:text-6xl">
                            <span class="block xl:inline">Lapar? Pesan Cepat di</span>
                            <span class="block text-primary-600 xl:inline">ScanEatz</span>
                        </h1>
                        <p class="mt-3 text-base text-slate-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                            Nikmati kuliner favorit dari Gajah Mada Food Street tanpa antri. Delivery cepat ke meja atau rumah kamu.
                        </p>
                        <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
                            <div class="rounded-md shadow">
                                <a href="#menu" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-full text-white bg-primary-600 hover:bg-primary-700 md:py-4 md:text-lg transition-transform hover:scale-105 shadow-primary-500/30 shadow-lg">
                                    Pesan Sekarang
                                </a>
                            </div>
                            <div class="mt-3 sm:mt-0 sm:ml-3">
                                <a href="#" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-full text-primary-700 bg-primary-100 hover:bg-primary-200 md:py-4 md:text-lg transition-colors">
                                    Lihat Promo
                                </a>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
        <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2">
            <img class="h-56 w-full object-cover sm:h-72 md:h-96 lg:w-full lg:h-full" src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1470&q=80" alt="Delicious Food">
            <div class="absolute inset-0 bg-gradient-to-r from-white via-white/50 to-transparent lg:via-white/20"></div>
        </div>
    </div>

    <!-- Quick Categories -->
    <div id="menu" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h2 class="text-2xl font-bold text-slate-900 mb-6">Mau makan apa hari ini?</h2>
        <div class="flex gap-4 overflow-x-auto pb-4 scrollbar-hide">
            @php
                $dbCategories = \App\Models\Category::ordered()->active()->get();
            @endphp
            @foreach($dbCategories as $cat)
            <a href="/catalog?category={{ $cat->slug }}" class="flex-shrink-0 flex flex-col items-center gap-2 group cursor-pointer">
                <div class="w-20 h-20 rounded-2xl bg-slate-100 overflow-hidden shadow-sm group-hover:shadow-md transition-all border border-slate-200 group-hover:border-primary-200">
                    @if($cat->image_url)
                        <img src="{{ $cat->image_url }}" alt="{{ $cat->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-3xl">
                            {{ $cat->icon ?? '🍔' }}
                        </div>
                    @endif
                </div>
                <span class="text-sm font-medium text-slate-700 group-hover:text-primary-600">{{ $cat->name }}</span>
            </a>
            @endforeach
        </div>
    </div>

    <!-- Featured / Best Sellers (Placeholder for Livewire Component) -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 bg-slate-50/50 rounded-3xl mb-12">
        <div class="flex justify-between items-end mb-6">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">Paling Laris 🔥</h2>
                <p class="text-slate-500 text-sm mt-1">Favorit pelanggan minggu ini</p>
            </div>
            <a href="/catalog" class="text-primary-600 font-medium hover:text-primary-700 text-sm flex items-center gap-1">
                Lihat Semua <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </a>
        </div>
        
        <!-- Grid Items -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @php
                $featured = \App\Models\Product::with('category')->bestSellers(4)->get();
            @endphp
            @foreach($featured as $item)
            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden hover:shadow-xl transition-all duration-500 group flex flex-col h-full">
                <div class="relative aspect-[4/3] bg-slate-200 overflow-hidden">
                    <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    
                    @if($item->average_rating > 4.0)
                    <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-3 py-1.5 rounded-full text-xs font-bold text-slate-900 flex items-center gap-1.5 shadow-sm">
                        <span class="text-yellow-500">★</span> {{ number_format($item->average_rating, 1) }}
                    </div>
                    @endif

                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center backdrop-blur-[2px]">
                        <a href="/catalog" class="flex items-center gap-2 text-white font-medium transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                            </svg>
                            <span>Lihat Detail</span>
                        </a>
                    </div>
                </div>
                <div class="p-6 flex flex-col flex-grow">
                    <h3 class="font-bold text-primary-600 text-lg mb-2 line-clamp-1 group-hover:text-primary-700 transition-colors">{{ $item->name }}</h3>
                    <p class="text-slate-500 text-xs mb-6 line-clamp-2 leading-relaxed flex-grow">{{ $item->description }}</p>
                    
                    <div class="flex justify-between items-center pt-4 border-t border-slate-50">
                        <span class="font-bold text-lg text-slate-900">{{ $item->formatted_price }}</span>
                        <a href="/catalog" class="w-10 h-10 rounded-xl bg-primary-600 text-white flex items-center justify-center hover:bg-primary-700 transition-all shadow-md shadow-primary-200 active:scale-95">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

</x-app-layout>
