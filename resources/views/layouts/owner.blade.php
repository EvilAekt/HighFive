<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Owner - HIGH FIVE</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="antialiased min-h-screen bg-[#f8f9fa] flex text-black">
    
    <!-- Sidebar -->
    <aside class="w-64 bg-black text-white flex flex-col z-10 border-r border-gray-200">
        <div class="p-6 border-b border-gray-800">
            <a href="{{ route('owner.dashboard') }}" class="text-2xl font-black uppercase tracking-widest text-white">
                HIGH<span class="text-gray-400">FIVE</span>
            </a>
            <p class="text-gray-400 text-[10px] mt-1 font-bold tracking-[0.2em] uppercase">Owner Workspace</p>
        </div>

        <nav class="flex-1 px-4 py-6 overflow-y-auto">
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('owner.dashboard') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold transition-colors {{ request()->routeIs('owner.dashboard') ? 'bg-white text-black' : 'text-gray-400 hover:bg-gray-900 hover:text-white' }}">
                        <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                        Overview
                    </a>
                </li>
                
                <li class="pt-6 mt-6 border-t border-gray-800">
                    <a href="{{ route('settings.index') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold transition-colors {{ request()->routeIs('settings.*') ? 'bg-white text-black' : 'text-gray-400 hover:bg-gray-900 hover:text-white' }}">
                        <i data-lucide="settings" class="w-4 h-4"></i>
                        Pengaturan
                    </a>
                </li>
                <li>
                    <a href="{{ route('home') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold transition-colors text-gray-400 hover:bg-gray-900 hover:text-white">
                        <i data-lucide="external-link" class="w-4 h-4"></i>
                        Ke Beranda
                    </a>
                </li>
            </ul>
        </nav>

        <div class="p-4 border-t border-gray-800">
            <div class="flex items-center gap-3 px-4 py-2">
                <div class="w-8 h-8 bg-white text-black font-bold flex items-center justify-center text-xs">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-[10px] text-gray-400 uppercase tracking-widest">{{ ucfirst(auth()->user()->role) }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-400 hover:text-white transition-colors flex items-center gap-2">
                    <i data-lucide="log-out" class="w-4 h-4"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden">
        <div class="flex-1 p-8 lg:p-10 overflow-y-auto w-full">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="bg-black text-white p-4 mb-8 text-sm font-medium flex items-center gap-3">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-white border border-black text-black p-4 mb-8 text-sm font-bold flex items-center gap-3">
                    <i data-lucide="alert-circle" class="w-5 h-5"></i>
                    {{ session('error') }}
                </div>
            @endif
            @if($errors->any())
                <div class="bg-white border border-black text-black p-4 mb-8 text-sm font-bold flex items-center gap-3">
                    <i data-lucide="alert-circle" class="w-5 h-5 shrink-0"></i>
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <script>
        lucide.createIcons();
    </script>
    @stack('scripts')
</body>
</html>
