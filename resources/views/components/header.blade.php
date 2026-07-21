@php
    $cartCount = 0;
    $cartItems = collect();
    if (auth()->check()) {
        $cartCount = \App\Models\Cart::where('user_id', auth()->id())->sum('quantity');
        $cartItems = \App\Models\Cart::with(['variant.product.images'])->where('user_id', auth()->id())->get();
    }
    $currentPath = request()->path();

    // Cache recommended products for the search modal (1 hour) to avoid querying on every page load
    $recommendedProducts = Cache::remember('header_recommended_products', 3600, function () {
        return \App\Models\Product::with('images')
            ->where('is_active', true)
            ->inRandomOrder()
            ->take(4)
            ->get();
    });
@endphp

<header class="sticky top-0 z-50 bg-white dark:bg-onyx-800 border-b border-primary-200 dark:border-onyx-700 transition-colors duration-300" x-data="headerData()" @open-cart.window="cartOpen = true; if(window.innerWidth < 768) { mobileMenuOpen = false; }" x-init="$watch('cartOpen', value => $dispatch('cart-toggled', value))">
    <!-- Top announcement bar -->
    <div class="bg-black text-white text-center py-2 px-4">
        <p class="text-xs tracking-widest uppercase font-medium">
            Free Shipping for orders above Rp 500.000
        </p>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 sm:h-20 md:h-24">
            <!-- Left Side: Logo & Nav -->
            <div class="flex items-center gap-4 lg:gap-10">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="text-xl sm:text-2xl md:text-3xl font-black tracking-tight text-primary-900 dark:text-white flex-shrink-0">
                    HIGH<span class="font-light">FIVE</span>
                </a>

            <!-- Nav -->
            <nav class="hidden md:flex items-center gap-3 lg:gap-6">
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
                        class="text-xs lg:text-sm font-bold uppercase tracking-wider transition-colors pb-1 {{ $currentPath === ltrim(parse_url($link['href'], PHP_URL_PATH), '/') ? 'text-black dark:text-white border-b-2 border-black dark:border-white' : 'text-primary-500 hover:text-black dark:hover:text-white' }}"
                    >
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </nav>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-1">
                <!-- Dark Mode Toggle -->
                <button
                    @click="$store.theme.toggle()"
                    class="p-1 lg:p-1.5 hover:bg-primary-100 dark:hover:bg-gray-800 transition-colors rounded-none text-primary-900 dark:text-white flex items-center justify-center"
                    aria-label="Toggle Dark Mode"
                >
                    <span x-show="!$store.theme.isDark"><i data-lucide="moon" class="w-5 h-5 lg:w-6 lg:h-6"></i></span>
                    <span x-show="$store.theme.isDark" style="display: none;"><i data-lucide="sun" class="w-5 h-5 lg:w-6 lg:h-6"></i></span>
                </button>

                <!-- Search Toggle -->
                <button
                    @click="searchOpen = !searchOpen; if(searchOpen) $nextTick(() => $refs.searchInput.focus())"
                    class="p-1 lg:p-1.5 hover:bg-primary-100 dark:hover:bg-gray-800 transition-colors rounded-none text-primary-900 dark:text-white"
                    aria-label="Search"
                >
                    <i data-lucide="search" class="w-5 h-5 lg:w-6 lg:h-6"></i>
                </button>


                <!-- Wishlist -->
                @auth
                <a
                    href="{{ route('wishlist.index') }}"
                    class="p-1 lg:p-1.5 hover:bg-primary-100 dark:hover:bg-gray-800 transition-colors rounded-none relative text-primary-900 dark:text-white"
                    aria-label="Wishlist"
                >
                    <i data-lucide="heart" class="w-5 h-5 lg:w-6 lg:h-6"></i>
                </a>
                @endauth

                <!-- Cart -->
                @auth
                <button
                    @click="cartOpen = true"
                    class="p-1 lg:p-1.5 hover:bg-primary-100 dark:hover:bg-gray-800 transition-colors rounded-none relative text-primary-900 dark:text-white"
                    aria-label="Cart"
                >
                    <i data-lucide="shopping-bag" class="w-5 h-5 lg:w-6 lg:h-6"></i>
                    @if($cartCount > 0)
                        <span class="absolute top-0.5 right-0.5 bg-black dark:bg-white dark:text-black text-white text-[10px] w-4 h-4 sm:w-5 sm:h-5 flex items-center justify-center font-bold rounded-full" id="cart-count-badge">
                            {{ $cartCount }}
                        </span>
                    @endif
                </button>
                @endauth

                <!-- User Menu -->
                @auth
                    <div class="hidden sm:block relative group" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                        <button class="flex items-center gap-2 lg:gap-3 p-1 lg:p-1.5 hover:bg-primary-100 dark:hover:bg-gray-800 transition-colors">
                            @if(auth()->user()->avatar)
                                <img src="{{ str_starts_with(auth()->user()->avatar, 'http') ? auth()->user()->avatar : asset('storage/' . auth()->user()->avatar) }}" alt="User" class="w-7 h-7 lg:w-8 lg:h-8 rounded-full object-cover shadow-sm border border-primary-200">
                            @else
                                <i data-lucide="user" class="w-6 h-6 lg:w-7 lg:h-7"></i>
                            @endif
                            <span class="hidden lg:inline text-xs lg:text-sm font-bold uppercase tracking-wider">
                                {{ explode(' ', auth()->user()->name)[0] }}
                            </span>
                        </button>
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                             class="absolute right-0 top-full mt-2 w-64 bg-white/95 dark:bg-onyx-900/95 backdrop-blur-xl border border-black/10 dark:border-white/10 shadow-[0_20px_50px_rgba(0,0,0,0.15)] z-50 overflow-hidden">
                            
                            <!-- Header Info -->
                            <div class="p-5 border-b border-black/5 dark:border-white/5 bg-gray-50/50 dark:bg-black/20">
                                <p class="text-[10px] text-gray-500 dark:text-gray-400 font-bold uppercase tracking-[0.2em] mb-1">Akun Anda</p>
                                <p class="text-base font-black tracking-tight text-black dark:text-white truncate">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium truncate mt-0.5">{{ auth()->user()->email }}</p>
                            </div>
                            
                            <!-- Menu Links -->
                            <div class="py-2">
                                <a href="{{ route('orders.index') }}" class="group flex items-center justify-between px-5 py-3 text-gray-900 dark:text-white hover:bg-gray-900 hover:text-white dark:hover:bg-white dark:hover:text-gray-900 transition-all duration-300">
                                    <div class="flex items-center gap-3">
                                        <i data-lucide="package" class="w-4 h-4 opacity-60 group-hover:opacity-100 transition-opacity"></i>
                                        <span class="text-xs font-bold uppercase tracking-widest">Pesanan Saya</span>
                                    </div>
                                    <i data-lucide="arrow-right" class="w-4 h-4 opacity-0 -translate-x-2 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300"></i>
                                </a>

                                @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="group flex items-center justify-between px-5 py-3 text-gray-900 dark:text-white hover:bg-gray-900 hover:text-white dark:hover:bg-white dark:hover:text-gray-900 transition-all duration-300">
                                    <div class="flex items-center gap-3">
                                        <i data-lucide="shield" class="w-4 h-4 opacity-60 group-hover:opacity-100 transition-opacity"></i>
                                        <span class="text-xs font-bold uppercase tracking-widest">Admin Panel</span>
                                    </div>
                                    <i data-lucide="arrow-right" class="w-4 h-4 opacity-0 -translate-x-2 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300"></i>
                                </a>
                                @endif
                                
                                @if(auth()->user()->isOwner())
                                <a href="{{ route('owner.dashboard') }}" class="group flex items-center justify-between px-5 py-3 text-gray-900 dark:text-white hover:bg-gray-900 hover:text-white dark:hover:bg-white dark:hover:text-gray-900 transition-all duration-300">
                                    <div class="flex items-center gap-3">
                                        <i data-lucide="crown" class="w-4 h-4 opacity-60 group-hover:opacity-100 transition-opacity"></i>
                                        <span class="text-xs font-bold uppercase tracking-widest">Owner Panel</span>
                                    </div>
                                    <i data-lucide="arrow-right" class="w-4 h-4 opacity-0 -translate-x-2 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300"></i>
                                </a>
                                @endif
                                
                                <a href="{{ route('settings.index') }}" class="group flex items-center justify-between px-5 py-3 text-gray-900 dark:text-white hover:bg-gray-900 hover:text-white dark:hover:bg-white dark:hover:text-gray-900 transition-all duration-300">
                                    <div class="flex items-center gap-3">
                                        <i data-lucide="settings" class="w-4 h-4 opacity-60 group-hover:opacity-100 transition-opacity"></i>
                                        <span class="text-xs font-bold uppercase tracking-widest">Pengaturan</span>
                                    </div>
                                    <i data-lucide="arrow-right" class="w-4 h-4 opacity-0 -translate-x-2 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300"></i>
                                </a>

                                @if(auth()->user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="group flex items-center justify-between px-5 py-3 text-gray-900 dark:text-white hover:bg-gray-900 hover:text-white dark:hover:bg-white dark:hover:text-gray-900 transition-all duration-300 bg-gray-50 dark:bg-onyx-800/50 border-y border-black/5 dark:border-white/5 my-1">
                                        <div class="flex items-center gap-3">
                                            <i data-lucide="layout-dashboard" class="w-4 h-4 opacity-60 group-hover:opacity-100 transition-opacity"></i>
                                            <span class="text-xs font-bold uppercase tracking-widest">Dashboard {{ ucfirst(auth()->user()->role) }}</span>
                                        </div>
                                        <i data-lucide="arrow-right" class="w-4 h-4 opacity-0 -translate-x-2 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300"></i>
                                    </a>
                                @endif
                            </div>
                            
                            <!-- Logout Button -->
                            <div class="border-t border-black/5 dark:border-white/5 p-2 bg-gray-50/50 dark:bg-black/20">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="group flex items-center justify-between w-full px-3 py-2.5 text-red-500 hover:bg-red-600 hover:text-white transition-all duration-300 rounded">
                                        <div class="flex items-center gap-3">
                                            <i data-lucide="log-out" class="w-4 h-4 opacity-80 group-hover:opacity-100 transition-opacity"></i>
                                            <span class="text-xs font-bold uppercase tracking-widest">Keluar</span>
                                        </div>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a
                        href="{{ route('login') }}"
                        class="hidden sm:flex p-2 hover:bg-primary-100 transition-colors items-center gap-2"
                        aria-label="Masuk"
                    >
                        <i data-lucide="user" class="w-5 h-5 lg:w-6 lg:h-6"></i>
                        <span class="hidden lg:inline text-xs font-medium uppercase tracking-wider">Masuk</span>
                    </a>
                @endauth

                <!-- Mobile menu button -->
                <button
                    @click="mobileMenuOpen = !mobileMenuOpen"
                    class="md:hidden p-1.5 sm:p-2 hover:bg-primary-100 transition-colors"
                    aria-label="Menu"
                >
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
            </div>
        </div>

        <!-- Search Modal -->
        <template x-teleport="body">
            <div x-show="searchOpen" 
                 class="fixed inset-0 z-[100] flex items-start justify-center pt-20 sm:pt-32 px-4 bg-black/60 backdrop-blur-sm"
                 x-transition.opacity
                 style="display: none;">
                 
                <div @click.outside="searchOpen = false"
                     class="bg-white rounded-2xl w-full max-w-3xl shadow-2xl overflow-hidden"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-8 scale-95">
                     
                    <form action="{{ route('catalog') }}" method="GET">
                        <!-- Search Input Area -->
                        <div class="relative flex items-center p-6 border-b border-gray-100">
                            <i data-lucide="search" class="w-6 h-6 text-gray-400 absolute left-6"></i>
                            <input
                                type="search"
                                name="q"
                                value="{{ request('q') }}"
                                placeholder="Ketik untuk mencari produk..."
                                class="w-full pl-12 pr-12 py-3 text-xl font-light outline-none transition-colors text-black placeholder-gray-300 bg-transparent"
                                x-ref="searchInput"
                                @keydown.escape="searchOpen = false"
                            />
                            <button type="button" @click="searchOpen = false" class="absolute right-6 text-gray-400 hover:text-black transition-colors" aria-label="Close search">
                                <i data-lucide="x" class="w-6 h-6"></i>
                            </button>
                        </div>
                        
                        <!-- Popular Searches & Suggestions -->
                        <div class="p-6 bg-gray-50/50 flex flex-col sm:flex-row items-start sm:items-center gap-4 text-sm border-b border-gray-100">
                            <span class="text-gray-400 font-bold uppercase tracking-widest text-[10px]">Pencarian Populer:</span>
                            <div class="flex flex-wrap items-center gap-2 sm:gap-4">
                                <a href="{{ route('catalog', ['category' => 'atasan']) }}" class="px-4 py-2 bg-white border border-gray-200 rounded-full text-gray-700 hover:border-primary-500 hover:text-primary-900 transition-colors text-xs font-bold uppercase tracking-wide shadow-sm">Atasan</a>
                                <a href="{{ route('catalog', ['category' => 'bawahan']) }}" class="px-4 py-2 bg-white border border-gray-200 rounded-full text-gray-700 hover:border-primary-500 hover:text-primary-900 transition-colors text-xs font-bold uppercase tracking-wide shadow-sm">Bawahan</a>
                                <a href="{{ route('catalog', ['category' => 'outerwear']) }}" class="px-4 py-2 bg-white border border-gray-200 rounded-full text-gray-700 hover:border-primary-500 hover:text-primary-900 transition-colors text-xs font-bold uppercase tracking-wide shadow-sm">Outerwear</a>
                            </div>
                        </div>

                        <!-- Recommended Products Grid -->
                        <div class="p-6">
                            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Mungkin Anda Suka</h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach($recommendedProducts as $prod)
                                    <a href="{{ route('product.show', $prod->id) }}" class="group text-left bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-primary-500 relative flex flex-col">
                                        <!-- Image -->
                                        <div class="aspect-square bg-gray-100 w-full overflow-hidden relative">
                                            @php
                                                $imageUrl = $prod->thumbnail ? (str_starts_with($prod->thumbnail, 'http') ? $prod->thumbnail : (str_starts_with($prod->thumbnail, '/') ? $prod->thumbnail : '/' . $prod->thumbnail)) : ($prod->images->count() > 0 ? '/storage/' . $prod->images->first()->image_path : 'https://placehold.co/300');
                                            @endphp
                                            <img src="{{ $imageUrl }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                            @if($prod->is_flash_sale)
                                                <div class="absolute top-2 right-2 bg-red-600 text-white text-[10px] font-bold px-2 py-1 uppercase tracking-wider rounded">
                                                    Promo
                                                </div>
                                            @endif
                                        </div>
                                        <!-- Info -->
                                        <div class="p-4 flex-1 flex flex-col justify-center text-center gap-1.5">
                                            <p class="text-[10px] font-bold text-gray-900 line-clamp-2 leading-tight uppercase tracking-wide">{{ $prod->name }}</p>
                                            <p class="text-[10px] font-bold text-gray-500">{{ formatPrice($prod->price) }}</p>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        <!-- Mobile menu -->
        <div x-show="mobileMenuOpen" x-transition.opacity class="md:hidden py-4 border-t border-primary-200">
            <nav class="flex flex-col">
                @foreach($navLinks as $link)
                    <a
                        href="{{ url($link['href']) }}"
                        class="px-2 py-3 text-xs font-semibold uppercase tracking-widest transition-colors border-b border-primary-100 {{ $currentPath === ltrim(parse_url($link['href'], PHP_URL_PATH), '/') ? 'text-black dark:text-white' : 'text-primary-500 hover:text-black dark:hover:text-white' }}"
                    >
                        {{ $link['label'] }}
                    </a>
                @endforeach

                @guest
                    <a
                        href="{{ route('login') }}"
                        class="px-2 py-3 text-xs font-semibold uppercase tracking-widest text-primary-500 hover:text-black dark:hover:text-white transition-colors"
                    >
                        Masuk / Daftar
                    </a>
                @endguest
                
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="px-2 py-3 text-xs font-semibold uppercase tracking-widest text-primary-500 hover:text-black dark:hover:text-white transition-colors border-b border-primary-100 flex items-center gap-2">
                            <i data-lucide="shield" class="w-4 h-4"></i> Admin Panel
                        </a>
                    @endif
                    @if(auth()->user()->isOwner())
                        <a href="{{ route('owner.dashboard') }}" class="px-2 py-3 text-xs font-semibold uppercase tracking-widest text-primary-500 hover:text-black dark:hover:text-white transition-colors border-b border-primary-100 flex items-center gap-2">
                            <i data-lucide="crown" class="w-4 h-4"></i> Owner Panel
                        </a>
                    @endif
                    <a href="{{ route('orders.index') }}" class="px-2 py-3 text-xs font-semibold uppercase tracking-widest text-primary-500 hover:text-black dark:hover:text-white transition-colors border-b border-primary-100 flex items-center gap-2">
                        <i data-lucide="package" class="w-4 h-4"></i> Pesanan Saya
                    </a>
                    <a href="{{ route('settings.index') }}" class="px-2 py-3 text-xs font-semibold uppercase tracking-widest text-primary-500 hover:text-black dark:hover:text-white transition-colors border-b border-primary-100 flex items-center gap-2">
                        <i data-lucide="settings" class="w-4 h-4"></i> Pengaturan Akun
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit" class="w-full text-left px-2 py-3 text-xs font-semibold uppercase tracking-widest text-red-500 hover:text-red-700 transition-colors flex items-center gap-2">
                            <i data-lucide="log-out" class="w-4 h-4"></i> Keluar
                        </button>
                    </form>
                @endauth
            </nav>
        </div>
    </div>
    
    <!-- Cart Slide-over Drawer -->
    @auth
    <div x-show="cartOpen" class="fixed inset-0 z-[100] overflow-hidden" style="display: none;">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity" x-show="cartOpen" x-transition.opacity @click="cartOpen = false"></div>
        <div class="fixed inset-y-0 right-0 max-w-md w-full flex">
            <div class="w-full h-full bg-white dark:bg-onyx-900 shadow-2xl flex flex-col transform transition-transform duration-300" 
                 x-show="cartOpen"
                 x-transition:enter="transition ease-in-out duration-300 transform"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition ease-in-out duration-300 transform"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full"
                 @click.stop>
                
                <!-- Drawer Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-primary-200 dark:border-onyx-700">
                    <h2 class="text-lg font-black uppercase tracking-widest text-primary-900 dark:text-white">Keranjang</h2>
                    <button @click="cartOpen = false" class="text-primary-400 hover:text-black dark:hover:text-white transition-colors">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>
                
                <div id="cart-drawer-content" class="flex flex-col flex-1 overflow-hidden relative">
                    <!-- Loading overlay -->
                    <div x-show="isCartUpdating" class="absolute inset-0 z-50 bg-white/40 dark:bg-black/40 backdrop-blur-[2px] flex items-center justify-center transition-opacity" style="display: none;">
                        <svg class="animate-spin h-8 w-8 text-black dark:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </div>

                    <!-- Drawer Body (Cart Items) -->
                    <div class="flex-1 overflow-y-auto p-6" id="cart-drawer-items">
                    @if($cartItems->count() > 0)
                        <div class="space-y-6">
                            @php $totalPrice = 0; @endphp
                            @foreach($cartItems as $item)
                                @php
                                    $itemPrice = $item->variant->product->price;
                                    if ($item->variant->product->is_flash_sale && \Carbon\Carbon::now()->lt($item->variant->product->flash_sale_end)) {
                                        $itemPrice = $item->variant->product->flash_sale_price;
                                    }
                                    $itemPrice += $item->variant->additional_price;
                                    $subtotal = $itemPrice * $item->quantity;
                                    $totalPrice += $subtotal;
                                @endphp
                                <div class="flex gap-4">
                                    <div class="w-24 h-32 bg-primary-50 dark:bg-onyx-800 flex-shrink-0">
                                        <img src="{{ $item->variant->product->thumbnail }}" alt="{{ $item->variant->product->name }}" class="w-full h-full object-cover">
                                    </div>
                                    <div class="flex-1 flex flex-col">
                                        <div class="flex justify-between">
                                            <div>
                                                <h3 class="text-sm font-bold uppercase tracking-tight line-clamp-1">{{ $item->variant->product->name }}</h3>
                                                <p class="text-xs text-primary-500 uppercase tracking-widest mt-1">{{ $item->variant->color }} - {{ $item->variant->size }}</p>
                                            </div>
                                            <form action="{{ route('cart.remove', $item->id) }}" method="POST" @submit.prevent="updateCart($el)">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-primary-400 hover:text-red-500 transition-colors"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                                            </form>
                                        </div>
                                        <div class="mt-auto flex items-end justify-between">
                                            <div class="flex items-center border border-primary-200 dark:border-onyx-700">
                                                <form action="{{ route('cart.update', $item->id) }}" method="POST" class="flex items-center" @submit.prevent="updateCart($el)">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="quantity" value="{{ $item->quantity - 1 }}">
                                                    <button type="submit" class="px-2 py-1 text-primary-500 hover:text-black dark:hover:text-white disabled:opacity-30 disabled:hover:text-primary-500 disabled:cursor-not-allowed" {{ $item->quantity <= 1 ? 'disabled' : '' }}>-</button>
                                                </form>
                                                <span class="text-xs font-bold px-2">{{ $item->quantity }}</span>
                                                <form action="{{ route('cart.update', $item->id) }}" method="POST" class="flex items-center" @submit.prevent="updateCart($el)">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="quantity" value="{{ $item->quantity + 1 }}">
                                                    <button type="submit" class="px-2 py-1 text-primary-500 hover:text-black dark:hover:text-white disabled:opacity-30 disabled:hover:text-primary-500 disabled:cursor-not-allowed" {{ $item->quantity >= $item->variant->stock ? 'disabled' : '' }}>+</button>
                                                </form>
                                            </div>
                                            <span class="text-sm font-bold text-black dark:text-white">{{ formatPrice($subtotal) }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="h-full flex flex-col items-center justify-center text-center text-primary-400">
                            <i data-lucide="shopping-bag" class="w-12 h-12 mb-4 opacity-50"></i>
                            <p class="text-sm font-medium uppercase tracking-widest">Keranjang Anda Kosong</p>
                            <button @click="cartOpen = false" class="mt-6 text-xs font-bold uppercase tracking-widest text-black dark:text-white hover:underline underline-offset-4 decoration-primary-300">
                                Lanjut Belanja
                            </button>
                        </div>
                    @endif
                </div>
                
                <!-- Drawer Footer -->
                @if($cartItems->count() > 0)
                <div class="border-t border-primary-200 dark:border-onyx-700 p-6 bg-primary-50 dark:bg-onyx-800">
                    @php
                        $freeShippingThreshold = 500000;
                        $progress = min(100, ($totalPrice / $freeShippingThreshold) * 100);
                        $remaining = max(0, $freeShippingThreshold - $totalPrice);
                    @endphp
                    
                    <div class="mb-4">
                        @if($remaining > 0)
                            <p class="text-[10px] font-bold text-primary-900 dark:text-white uppercase tracking-widest mb-1 flex justify-between">
                                <span>Kurang <span class="text-red-600 dark:text-red-400">{{ formatPrice($remaining) }}</span></span>
                                <span>Gratis Ongkir!</span>
                            </p>
                        @else
                            <p class="text-[10px] font-bold text-green-600 dark:text-green-400 uppercase tracking-widest mb-1 flex justify-between">
                                <span>Selamat!</span>
                                <span>Anda dapat Gratis Ongkir 🎉</span>
                            </p>
                        @endif
                        <div class="w-full h-1 bg-primary-200 dark:bg-onyx-600 overflow-hidden relative">
                            <div class="absolute top-0 left-0 h-full transition-all duration-1000 ease-out {{ $remaining > 0 ? 'bg-black dark:bg-white' : 'bg-green-500 dark:bg-green-400' }}" style="width: {{ $progress }}%"></div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mb-4">
                        <span class="text-sm font-bold uppercase tracking-widest text-primary-900 dark:text-white">Subtotal</span>
                        <span class="text-xl font-black text-black dark:text-white">{{ formatPrice($totalPrice) }}</span>
                    </div>
                    <p class="text-[10px] text-primary-500 uppercase tracking-widest mb-6">Pajak dan ongkos kirim dihitung saat checkout.</p>
                    <a href="{{ route('checkout.index') }}" class="block w-full py-4 text-center bg-black dark:bg-white text-white dark:text-black text-xs font-bold uppercase tracking-[0.2em] hover:bg-primary-900 dark:hover:bg-gray-200 transition-colors">
                        Checkout Sekarang
                    </a>
                    <a href="{{ route('cart.index') }}" class="block w-full text-center mt-4 text-xs font-bold uppercase tracking-widest text-primary-900 dark:text-white hover:underline underline-offset-4 decoration-primary-300">
                        Lihat Keranjang Penuh
                    </a>
                </div>
                @endif
                </div> <!-- End of cart-drawer-content -->
            </div>
        </div>
    </div>
    @endauth
</header>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('headerData', () => ({
            searchOpen: false,
            mobileMenuOpen: false,
            cartOpen: false,
            isCartUpdating: false,
            updateCart(form) {
                if(this.isCartUpdating) return;
                this.isCartUpdating = true;
                
                fetch(form.action, {
                    method: form.method,
                    body: new FormData(form),
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.text())
                .then(html => {
                    const doc = new DOMParser().parseFromString(html, 'text/html');
                    const newContent = doc.getElementById('cart-drawer-content');
                    if(newContent) {
                        document.getElementById('cart-drawer-content').innerHTML = newContent.innerHTML;
                    }
                    
                    const newBadge = doc.getElementById('cart-count-badge');
                    const currentBadge = document.getElementById('cart-count-badge');
                    if(newBadge && currentBadge) {
                        currentBadge.outerHTML = newBadge.outerHTML;
                    } else if(!newBadge && currentBadge) {
                        currentBadge.remove();
                    }
                    
                    if(typeof lucide !== 'undefined') { 
                        lucide.createIcons(); 
                    }
                    
                    // If on the full cart page or checkout, reload to sync changes
                    if (window.location.pathname === '/cart' || window.location.pathname === '/checkout') {
                        window.location.reload();
                    }
                })
                .catch(() => window.location.reload())
                .finally(() => this.isCartUpdating = false);
            }
        }));
    });
</script>
