<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ auth()->check() && (auth()->user()->settings['theme'] ?? 'light') === 'dark' ? 'dark' : '' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="HIGH FIVE - Premium Direct-to-Consumer Fashion Brand. Koleksi pakaian minimalis dan modern.">

    <title>@yield('title', 'HIGH FIVE - Premium Fashion')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;900&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Midtrans Snap -->
    @if(request()->routeIs('checkout.index') || request()->routeIs('orders.show'))
        <script src="{{ env('MIDTRANS_IS_PRODUCTION') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    @endif
</head>
<body class="font-sans antialiased bg-white text-primary-900 min-h-screen flex flex-col dark:bg-gray-900 dark:text-gray-100 transition-colors duration-300">
    @include('components.header')

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-4 text-center border-b border-green-200">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 text-red-800 p-4 text-center border-b border-red-200">
            {{ session('error') }}
        </div>
    @endif

    <main class="flex-1">
        @yield('content')
    </main>

    @include('components.footer')

    <script>
        lucide.createIcons();
    </script>
    @stack('scripts')
</body>
</html>
