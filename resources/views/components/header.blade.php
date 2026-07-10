@php
    $cartCount = 0;
    if (auth()->check()) {
        $cartCount = \App\Models\Cart::where('user_id', auth()->id())->sum('quantity');
    }
    $currentPath = request()->path();
@endphp

<header class="sticky top-0 z-50 bg-white border-b border-primary-200" x-data="{ searchOpen: false, mobileMenuOpen: false }">
    <!-- Top announcement bar -->
    <div class="bg-black text-white text-center py-2 px-4">
        <p class="text-xs tracking-widest uppercase font-medium">
            Free Shipping for orders above Rp 500.000
        </p>
    </div>

    <div class="max-w-7xl mx-auto px-6 sm:px-12 lg:px-20 xl:px-24">
        <div class="flex items-center justify-between h-24">
            <!-- Left Side: Logo & Nav -->
            <div class="flex items-center gap-16">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="text-3xl font-black tracking-tight text-primary-900 dark:text-white flex-shrink-0">
                    HIGH<span class="font-light">FIVE</span>
                </a>

            <!-- Nav -->
            <nav class="hidden md:flex items-center gap-8">
                @php
                    $navLinks = [
                        ['href' => '/catalog', 'label' => 'Shop'],
                        ['href' => '/catalog?category=atasan', 'label' => 'Atasan'],
                        ['href' => '/catalog?category=bawahan', 'label' => 'Bawahan'],
                        ['href' => '/catalog?category=outerwear', 'label' => 'Outerwear'],
                    ];
                @endphp
                @foreach($navLinks as $link)
                    <a
                        href="{{ url($link['href']) }}"
                        class="text-base font-bold uppercase tracking-wider transition-colors pb-1 {{ $currentPath === ltrim(parse_url($link['href'], PHP_URL_PATH), '/') ? 'text-black dark:text-white border-b-2 border-black dark:border-white' : 'text-primary-500 hover:text-black dark:hover:text-white' }}"
                    >
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </nav>

            <!-- Actions -->
            <div class="flex items-center gap-1">
                <!-- Search Toggle -->
                <button
                    @click="searchOpen = !searchOpen"
                    class="p-2 hover:bg-primary-100 transition-colors rounded-none text-primary-900 dark:text-white"
                    aria-label="Search"
                >
                    <i data-lucide="search" class="w-7 h-7"></i>
                </button>

                <!-- Wishlist -->
                <a
                    href="{{ route('wishlist.index') }}"
                    class="p-2 hover:bg-primary-100 transition-colors rounded-none text-primary-900 dark:text-white"
                    aria-label="Wishlist"
                >
                    <i data-lucide="heart" class="w-7 h-7"></i>
                </a>

                <!-- Cart -->
                @auth
                <a
                    href="{{ route('cart.index') }}"
                    class="p-2 hover:bg-primary-100 transition-colors rounded-none relative text-primary-900 dark:text-white"
                    aria-label="Cart"
                >
                    <i data-lucide="shopping-bag" class="w-7 h-7"></i>
                    @if($cartCount > 0)
                        <span class="absolute top-0.5 right-0.5 bg-black dark:bg-white dark:text-black text-white text-xs w-5 h-5 flex items-center justify-center font-bold rounded-full">
                            {{ $cartCount }}
                        </span>
                    @endif
                </a>
                @endauth

                <!-- User Menu -->
                @auth
                    <div class="relative group" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                        <button class="flex items-center gap-3 p-2 hover:bg-primary-100 transition-colors">
                            @if(auth()->user()->avatar)
                                <img src="{{ str_starts_with(auth()->user()->avatar, 'http') ? auth()->user()->avatar : asset('storage/' . auth()->user()->avatar) }}" alt="User" class="w-10 h-10 rounded-full object-cover shadow-sm border border-primary-200">
                            @else
                                <i data-lucide="user" class="w-8 h-8"></i>
                            @endif
                            <span class="hidden sm:inline text-base font-bold uppercase tracking-wider">
                                {{ explode(' ', auth()->user()->name)[0] }}
                            </span>
                        </button>
                        <div x-show="open" 
                             x-transition.opacity.duration.200ms
                             class="absolute right-0 top-full mt-0 w-52 bg-white border border-primary-200 shadow-2xl z-50">
                            <div class="px-4 py-3 border-b border-primary-100">
                                <p class="text-xs text-primary-500 uppercase tracking-widest">Akun</p>
                                <p class="text-sm font-medium mt-0.5">{{ auth()->user()->name }}</p>
                            </div>
                            <a
                                href="{{ route('orders.index') }}"
                                class="flex items-center gap-2 px-4 py-3 hover:bg-primary-50 text-xs uppercase tracking-wider font-medium transition-colors"
                            >
                                <i data-lucide="package" class="w-3.5 h-3.5"></i>
                                Pesanan Saya
                            </a>
                            <a
                                href="{{ route('settings.index') }}"
                                class="flex items-center gap-2 px-4 py-3 hover:bg-primary-50 text-xs uppercase tracking-wider font-medium transition-colors"
                            >
                                <i data-lucide="settings" class="w-3.5 h-3.5"></i>
                                Pengaturan Akun
                            </a>
                            @if(auth()->user()->isAdmin())
                                <a
                                    href="{{ route('admin.dashboard') }}"
                                    class="flex items-center gap-2 px-4 py-3 hover:bg-primary-50 text-xs uppercase tracking-wider font-medium transition-colors"
                                >
                                    <i data-lucide="layout-dashboard" class="w-3.5 h-3.5"></i>
                                    Dashboard {{ ucfirst(auth()->user()->role) }}
                                </a>
                            @endif
                            <div class="border-t border-primary-100">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button
                                        type="submit"
                                        class="w-full flex items-center gap-2 px-4 py-3 hover:bg-primary-50 text-xs uppercase tracking-wider font-medium text-left transition-colors text-primary-500 hover:text-black"
                                    >
                                        <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a
                        href="{{ route('login') }}"
                        class="p-2 hover:bg-primary-100 transition-colors flex items-center gap-2"
                        aria-label="Masuk"
                    >
                        <i data-lucide="user" class="w-5 h-5"></i>
                        <span class="hidden sm:inline text-xs font-medium uppercase tracking-wider">Masuk</span>
                    </a>
                @endauth

                <!-- Mobile menu button -->
                <button
                    @click="mobileMenuOpen = !mobileMenuOpen"
                    class="md:hidden p-2 hover:bg-primary-100 transition-colors"
                    aria-label="Menu"
                >
                    <i data-lucide="menu" class="w-4 h-4"></i>
                </button>
            </div>
        </div>

        <!-- Search bar -->
        <div x-show="searchOpen" x-transition.opacity class="py-4 border-t border-primary-200">
            <form action="{{ route('catalog') }}" method="GET" class="max-w-xl mx-auto">
                <div class="relative">
                    <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-primary-400"></i>
                    <input
                        type="search"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Cari produk..."
                        class="w-full pl-11 pr-4 py-3 border border-primary-300 focus:border-black outline-none transition-colors text-sm bg-primary-50"
                    />
                    <button
                        type="submit"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-semibold uppercase tracking-widest text-primary-500 hover:text-black transition-colors"
                    >
                        Cari
                    </button>
                </div>
            </form>
        </div>

        <!-- Mobile menu -->
        <div x-show="mobileMenuOpen" x-transition.opacity class="md:hidden py-4 border-t border-primary-200">
            <nav class="flex flex-col">
                @foreach($navLinks as $link)
                    <a
                        href="{{ url($link['href']) }}"
                        class="px-2 py-3 text-xs font-semibold uppercase tracking-widest transition-colors border-b border-primary-100 last:border-b-0 {{ $currentPath === ltrim(parse_url($link['href'], PHP_URL_PATH), '/') ? 'text-black' : 'text-primary-500 hover:text-black' }}"
                    >
                        {{ $link['label'] }}
                    </a>
                @endforeach
                @guest
                    <a
                        href="{{ route('login') }}"
                        class="px-2 py-3 text-xs font-semibold uppercase tracking-widest text-primary-500 hover:text-black transition-colors"
                    >
                        Masuk
                    </a>
                @endguest
            </nav>
        </div>
    </div>
</header>
