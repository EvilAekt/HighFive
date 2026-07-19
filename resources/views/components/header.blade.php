@php
    $cartCount = 0;
    $cartItems = collect();
    if (auth()->check()) {
        $cartCount = \App\Models\Cart::where('user_id', auth()->id())->sum('quantity');
        $cartItems = \App\Models\Cart::with(['variant.product.images'])->where('user_id', auth()->id())->get();
    }
    $currentPath = request()->path();
@endphp

<header class="sticky top-0 z-50 bg-white dark:bg-onyx-800 border-b border-primary-200 dark:border-onyx-700 transition-colors duration-300" x-data="{ searchOpen: false, mobileMenuOpen: false, cartOpen: false }" @open-cart.window="cartOpen = true; if(window.innerWidth < 768) { mobileMenuOpen = false; }">
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

        <!-- Search Drawer -->
        <div x-show="searchOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="absolute top-full left-0 w-full bg-white border-b border-primary-200 shadow-2xl z-40 p-6 sm:p-10"
             style="display: none;"
             @click.outside="searchOpen = false">
            <form action="{{ route('catalog') }}" method="GET" class="max-w-4xl mx-auto">
                <div class="relative flex items-center border-b-2 border-primary-200 focus-within:border-black transition-colors pb-2">
                    <i data-lucide="search" class="w-6 h-6 text-primary-400 mr-4"></i>
                    <input
                        type="search"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Ketik untuk mencari produk..."
                        class="w-full py-2 text-xl sm:text-2xl font-light outline-none transition-colors text-black placeholder-primary-300 bg-transparent"
                        x-ref="searchInput"
                        @keydown.escape="searchOpen = false"
                    />
                    <button
                        type="submit"
                        class="hidden sm:block text-sm font-bold uppercase tracking-widest text-black hover:text-primary-500 transition-colors ml-4"
                    >
                        Cari
                    </button>
                    <button type="button" @click="searchOpen = false" class="ml-4 sm:ml-8 text-primary-400 hover:text-black transition-colors" aria-label="Close search">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>
                <div class="mt-6 flex flex-wrap items-center gap-4 text-sm">
                    <span class="text-primary-400 font-semibold uppercase tracking-widest text-xs">Pencarian Populer:</span>
                    <a href="{{ route('catalog', ['category' => 'atasan']) }}" class="text-primary-900 hover:text-black hover:underline underline-offset-4 decoration-primary-300">Atasan</a>
                    <a href="{{ route('catalog', ['category' => 'bawahan']) }}" class="text-primary-900 hover:text-black hover:underline underline-offset-4 decoration-primary-300">Bawahan</a>
                    <a href="{{ route('catalog', ['category' => 'outerwear']) }}" class="text-primary-900 hover:text-black hover:underline underline-offset-4 decoration-primary-300">Outerwear</a>
                </div>
            </form>
        </div>

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
                                            <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-primary-400 hover:text-red-500 transition-colors"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                                            </form>
                                        </div>
                                        <div class="mt-auto flex items-end justify-between">
                                            <div class="flex items-center border border-primary-200 dark:border-onyx-700">
                                                <form action="{{ route('cart.update', $item->id) }}" method="POST" class="flex items-center">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="quantity" value="{{ $item->quantity - 1 }}">
                                                    <button type="submit" class="px-2 py-1 text-primary-500 hover:text-black dark:hover:text-white" {{ $item->quantity <= 1 ? 'disabled' : '' }}>-</button>
                                                </form>
                                                <span class="text-xs font-bold px-2">{{ $item->quantity }}</span>
                                                <form action="{{ route('cart.update', $item->id) }}" method="POST" class="flex items-center">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="quantity" value="{{ $item->quantity + 1 }}">
                                                    <button type="submit" class="px-2 py-1 text-primary-500 hover:text-black dark:hover:text-white">+</button>
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
            </div>
        </div>
    </div>
    @endauth
</header>
