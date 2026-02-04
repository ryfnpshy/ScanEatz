<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ScanEatz') }} - Pesan Makanan Cepat</title>
    <meta name="description" content="Pesan makanan online dari Gajah Mada Food Street Jakarta Pusat. Delivery cepat, banyak promo, dan bebas antri.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    <!-- Tailwind via CDN for dev speed if generic build fails, but mainly relying on Vite -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            500: '#22c55e',
                            600: '#16a34a', // Brand Primary
                            700: '#15803d',
                        },
                        secondary: '#0f172a',
                    }
                }
            }
        }
    </script>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-800 flex flex-col min-h-screen">
    
    <div class="fixed top-0 left-0 w-full z-50">
        @include('components.header')
    </div>

    <!-- Spacer for fixed header -->
    <div class="h-16"></div>

    <main class="flex-grow">
        {{ $slot }}
    </main>

    @include('components.footer')

    @livewireScripts
</body>
</html>
