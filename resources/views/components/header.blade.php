<header class="bg-white/80 backdrop-blur-md border-b border-slate-200 shadow-sm transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo & Location -->
            <div class="flex items-center gap-4">
                <a href="/" class="flex items-center gap-2 group">
                    <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center text-white font-bold text-xl shadow-lg group-hover:scale-105 transition-transform">
                        S
                    </div>
                    <span class="font-bold text-xl tracking-tight text-secondary group-hover:text-primary-600 transition-colors">ScanEatz</span>
                </a>
                
                <div class="hidden md:flex items-center gap-2 pl-4 border-l border-slate-300 text-sm text-slate-600 hover:text-primary-600 cursor-pointer transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span class="truncate max-w-[200px]">Gajah Mada Food Street, Jakpus</span>
                </div>
            </div>

            <!-- Search (Desktop) -->
            <div class="hidden md:block flex-1 max-w-md mx-8">
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-slate-400 group-focus-within:text-primary-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" class="block w-full pl-10 pr-3 py-2 border border-slate-200 rounded-full leading-5 bg-slate-50 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500 sm:text-sm transition-all shadow-sm" placeholder="Cari ayam geprek, mie, minuman..." aria-label="Search">
                </div>
            </div>

            <!-- Right Actions -->
            <div class="flex items-center gap-3 sm:gap-6">
                <!-- Cart Button -->
                <a href="/cart" class="relative group p-2 rounded-full hover:bg-slate-100 transition-colors">
                    <svg class="h-6 w-6 text-slate-600 group-hover:text-primary-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    <!-- Badge -->
                    <span class="absolute top-1 right-0 rounded-full bg-red-500 text-white text-[10px] w-4 h-4 flex items-center justify-center font-bold shadow-sm">
                        2
                    </span>
                </a>

                <!-- User Account -->
                @auth
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-2 focus:outline-none">
                            <img class="h-8 w-8 rounded-full object-cover border border-slate-200" src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=16a34a&color=fff" alt="{{ Auth::user()->name }}" />
                            <span class="hidden sm:block text-sm font-medium text-slate-700">{{ Str::limit(Auth::user()->name, 10) }}</span>
                        </button>
                    </div>
                @else
                    <a href="/login" class="text-sm font-semibold text-slate-600 hover:text-primary-600 transition-colors">
                        Masuk
                    </a>
                    <a href="/register" class="hidden sm:inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-full shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all hover:shadow-md">
                        Daftar
                    </a>
                @endauth
            </div>
        </div>
    </div>
</header>
