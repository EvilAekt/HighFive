<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin - HIGH FIVE</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="font-sans antialiased min-h-screen bg-primary-100 flex">
    
    <!-- Sidebar -->
    <aside class="w-64 bg-primary-900 text-white flex flex-col">
        <div class="p-6">
            <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold">
                HIGH<span class="font-light">FIVE</span>
            </a>
            <p class="text-primary-400 text-sm mt-1">Admin Panel</p>
        </div>

        <nav class="flex-1 px-4 py-6">
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-md transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-white text-primary-900' : 'text-primary-300 hover:bg-primary-800 hover:text-white' }}">
                        <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.products.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md transition-colors {{ request()->routeIs('admin.products.*') ? 'bg-white text-primary-900' : 'text-primary-300 hover:bg-primary-800 hover:text-white' }}">
                        <i data-lucide="package" class="w-5 h-5"></i>
                        Produk
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.coupons.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md transition-colors {{ request()->routeIs('admin.coupons.*') ? 'bg-white text-primary-900' : 'text-primary-300 hover:bg-primary-800 hover:text-white' }}">
                        <i data-lucide="ticket" class="w-5 h-5"></i>
                        Kupon Diskon
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md transition-colors {{ request()->routeIs('admin.orders.*') ? 'bg-white text-primary-900' : 'text-primary-300 hover:bg-primary-800 hover:text-white' }}">
                        <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                        Pesanan
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-white text-primary-900' : 'text-primary-300 hover:bg-primary-800 hover:text-white' }}">
                        <i data-lucide="users" class="w-5 h-5"></i>
                        Pelanggan
                    </a>
                </li>

                <li>
                    <a href="{{ route('settings.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md transition-colors {{ request()->routeIs('settings.*') ? 'bg-white text-primary-900' : 'text-primary-300 hover:bg-primary-800 hover:text-white' }}">
                        <i data-lucide="settings" class="w-5 h-5"></i>
                        Pengaturan Akun
                    </a>
                </li>
            </ul>
        </nav>

        <div class="p-4 border-t border-primary-800">
            <div class="flex items-center gap-3 px-3 py-2">
                @if(auth()->user()->avatar)
                    <img src="{{ str_starts_with(auth()->user()->avatar, 'http') ? auth()->user()->avatar : asset('storage/' . auth()->user()->avatar) }}" alt="User" class="w-8 h-8 rounded-full object-cover shadow-sm border border-primary-700">
                @else
                    <div class="w-8 h-8 bg-primary-700 rounded-full flex items-center justify-center">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                @endif
                <div class="flex-1">
                    <p class="text-sm font-medium">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-primary-400">{{ ucfirst(auth()->user()->role) }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center gap-2 w-full px-3 py-2 mt-2 text-primary-400 hover:text-white transition-colors text-left">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                    <span class="text-sm">Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-6 overflow-auto">
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-4 mb-6 border border-green-200 flex items-center gap-2">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 text-red-800 p-4 mb-6 border border-red-200 flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-5 h-5"></i>
                {{ session('error') }}
            </div>
        @endif
        @if($errors->any())
            <div class="bg-red-100 text-red-800 p-4 mb-6 border border-red-200">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <script>
        lucide.createIcons();
    </script>
    @stack('scripts')
</body>
</html>
