@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ 
    selectedVariant: null, 
    quantity: 1,
    adding: false,
    showSticky: false,
    sizeGuideOpen: false,
    lightboxOpen: false, 
    lightboxUrl: '', 
    lightboxType: '',
    async submitCart(e) {
        if(!this.selectedVariant) return;
        this.adding = true;
        
        try {
            const formData = new FormData(e.target);
            const response = await fetch(e.target.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if(response.ok) {
                // Fetch current page to get updated cart drawer HTML
                const htmlResponse = await fetch(window.location.href);
                const htmlString = await htmlResponse.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(htmlString, 'text/html');
                
                // Update Drawer Items
                const newDrawer = doc.getElementById('cart-drawer-items');
                if(newDrawer && document.getElementById('cart-drawer-items')) {
                    document.getElementById('cart-drawer-items').innerHTML = newDrawer.innerHTML;
                }
                
                // Update Cart Count Badge
                const newBadge = doc.getElementById('cart-count-badge');
                const oldBadge = document.getElementById('cart-count-badge');
                
                if(newBadge && oldBadge) {
                    oldBadge.innerHTML = newBadge.innerHTML;
                } else if(newBadge && !oldBadge) {
                    // Add badge if it didn't exist
                    const cartBtn = document.querySelector('button[aria-label=\'Cart\']');
                    if(cartBtn) cartBtn.innerHTML += newBadge.outerHTML;
                } else if(!newBadge && oldBadge) {
                    oldBadge.remove();
                }
                
                // Open the cart drawer
                window.dispatchEvent(new CustomEvent('open-cart'));
            }
        } catch(error) {
            console.error('Error adding to cart:', error);
        } finally {
            this.adding = false;
        }
    }
}" @scroll.window="showSticky = window.scrollY > 600">
    <!-- Breadcrumb -->
    <nav class="flex text-sm text-primary-500 mb-8" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2">
            <li><a href="{{ route('home') }}" class="hover:text-primary-900 dark:hover:text-white">Home</a></li>
            <li><span class="mx-2">/</span></li>
            <li><a href="{{ route('catalog') }}" class="hover:text-primary-900 dark:hover:text-white">Catalog</a></li>
            <li><span class="mx-2">/</span></li>
            <li class="text-primary-900 dark:text-white font-medium truncate" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-16">
        @php
            $isFlashSaleActive = $product->is_flash_sale && \Carbon\Carbon::now()->lt($product->flash_sale_end);
        @endphp
        <!-- Images -->
        <!-- Images -->
        @php
            $allImages = collect([$product->thumbnail])->merge($product->images->pluck('image_path'))->filter()->unique()->values()->toArray();
        @endphp
        <div class="space-y-4" x-data="{ activeImage: 0, images: {{ json_encode($allImages) }} }">
            <div class="relative aspect-[3/4] bg-primary-100 dark:bg-onyx-700 border border-primary-200 dark:border-onyx-600 overflow-hidden group cursor-pointer"
                 @click="lightboxOpen = true; lightboxUrl = images[activeImage]; lightboxType = 'image'">
                 
                <template x-for="(image, index) in images" :key="index">
                    <div class="absolute inset-0 w-full h-full" x-show="activeImage === index"
                         x-transition:enter="transition ease-out duration-500"
                         x-transition:enter-start="opacity-0 scale-105"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-500 absolute inset-0"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95">
                         
                        <img :src="image" :alt="'{{ $product->name }}'" 
                             class="w-full h-full object-cover transition-all duration-500 group-hover:brightness-75 pointer-events-none select-none" draggable="false" oncontextmenu="return false;" />
                    </div>
                </template>
                <!-- Fallback -->
                <div class="absolute inset-0 w-full h-full" x-show="images.length === 0">
                    <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}" class="w-full h-full object-cover transition-all duration-500 group-hover:brightness-75 pointer-events-none select-none" draggable="false" oncontextmenu="return false;" />
                </div>
                
                <!-- Slider Controls -->
                <template x-if="images.length > 1">
                    <div class="absolute inset-x-0 top-1/2 -translate-y-1/2 flex justify-between px-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <button @click.prevent.stop="activeImage = (activeImage - 1 + images.length) % images.length" class="w-10 h-10 flex items-center justify-center bg-white/80 hover:bg-white text-black shadow rounded-full backdrop-blur-sm transition-transform hover:scale-110">
                            <i data-lucide="chevron-left" class="w-5 h-5"></i>
                        </button>
                        <button @click.prevent.stop="activeImage = (activeImage + 1) % images.length" class="w-10 h-10 flex items-center justify-center bg-white/80 hover:bg-white text-black shadow rounded-full backdrop-blur-sm transition-transform hover:scale-110">
                            <i data-lucide="chevron-right" class="w-5 h-5"></i>
                        </button>
                    </div>
                </template>
                
                <!-- Floating Flash Sale Timer -->
                @if($isFlashSaleActive)
                    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-20 bg-red-600/95 backdrop-blur shadow-2xl rounded-full px-5 py-2.5 flex items-center gap-3 border border-red-500 pointer-events-none animate-bounce" style="animation-duration: 2s;"
                         x-data="{ 
                            endTime: new Date('{{ $product->flash_sale_end }}').getTime(), 
                            now: new Date().getTime(), 
                            distance: 0,
                            days: 0, hours: 0, minutes: 0, seconds: 0,
                            init() {
                                this.updateTime();
                                setInterval(() => this.updateTime(), 1000);
                            },
                            updateTime() {
                                this.now = new Date().getTime();
                                this.distance = this.endTime - this.now;
                                if(this.distance > 0) {
                                    this.days = Math.floor(this.distance / (1000 * 60 * 60 * 24));
                                    this.hours = Math.floor((this.distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)) + (this.days * 24);
                                    this.minutes = Math.floor((this.distance % (1000 * 60 * 60)) / (1000 * 60));
                                    this.seconds = Math.floor((this.distance % (1000 * 60)) / 1000);
                                }
                            }
                         }" x-show="distance > 0" x-transition>
                        <div class="flex items-center gap-1.5 text-white font-bold text-[10px] sm:text-xs uppercase tracking-widest whitespace-nowrap">
                            <i data-lucide="zap" class="w-4 h-4 fill-white"></i>
                            Berakhir Dalam
                        </div>
                        <div class="flex items-center gap-1 text-white font-mono text-sm sm:text-base font-bold">
                            <span x-text="hours.toString().padStart(2, '0')" class="bg-black/20 px-1.5 py-0.5 rounded"></span><span class="text-xs -mx-0.5 text-white/70">:</span>
                            <span x-text="minutes.toString().padStart(2, '0')" class="bg-black/20 px-1.5 py-0.5 rounded"></span><span class="text-xs -mx-0.5 text-white/70">:</span>
                            <span x-text="seconds.toString().padStart(2, '0')" class="bg-black/20 px-1.5 py-0.5 rounded"></span>
                        </div>
                    </div>
                @endif
            </div>
            
            <template x-if="images.length > 1">
                <div class="grid grid-cols-4 gap-4">
                    <template x-for="(image, index) in images" :key="index">
                        <button @click="activeImage = index" 
                                class="aspect-square border p-1 transition-all duration-300"
                                :class="activeImage === index ? 'border-black shadow-sm' : 'border-primary-200 hover:border-black/50 opacity-70 hover:opacity-100'">
                            <img :src="image" class="w-full h-full object-cover pointer-events-none select-none" draggable="false" oncontextmenu="return false;" />
                        </button>
                    </template>
                </div>
            </template>
        </div>

        <!-- Product Info -->
        <div class="flex flex-col">
            <div class="mb-6 pb-6 border-b border-primary-200">
                @if($product->category)
                    <p class="text-xs font-semibold uppercase tracking-widest text-primary-500 mb-2">
                        {{ $product->category->name }}
                    </p>
                @endif
                <h1 class="text-3xl font-black uppercase tracking-tight text-primary-900 dark:text-white mb-4">
                    {{ $product->name }}
                </h1>

                @if($isFlashSaleActive)
                    <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/50 p-4" 
                         x-data="{ 
                            endTime: new Date('{{ $product->flash_sale_end }}').getTime(), 
                            now: new Date().getTime(), 
                            distance: 0,
                            days: 0, hours: 0, minutes: 0, seconds: 0,
                            init() {
                                this.updateTime();
                                setInterval(() => this.updateTime(), 1000);
                            },
                            updateTime() {
                                this.now = new Date().getTime();
                                this.distance = this.endTime - this.now;
                                if(this.distance > 0) {
                                    this.days = Math.floor(this.distance / (1000 * 60 * 60 * 24));
                                    this.hours = Math.floor((this.distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                    this.minutes = Math.floor((this.distance % (1000 * 60 * 60)) / (1000 * 60));
                                    this.seconds = Math.floor((this.distance % (1000 * 60)) / 1000);
                                }
                            }
                         }">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-red-600 dark:text-red-400 uppercase tracking-widest text-sm flex items-center gap-1">
                                <i data-lucide="zap" class="w-4 h-4"></i> Flash Sale
                            </span>
                            <div class="flex items-center gap-2 text-red-700 dark:text-red-300 font-mono text-sm font-bold" x-show="distance > 0">
                                <span x-text="hours.toString().padStart(2, '0')" class="bg-red-600 text-white px-2 py-1 rounded-sm"></span>:
                                <span x-text="minutes.toString().padStart(2, '0')" class="bg-red-600 text-white px-2 py-1 rounded-sm"></span>:
                                <span x-text="seconds.toString().padStart(2, '0')" class="bg-red-600 text-white px-2 py-1 rounded-sm"></span>
                            </div>
                        </div>
                    </div>
                @endif
                
                <div class="flex items-center gap-4 mb-4">
                    @if($isFlashSaleActive)
                        <div class="flex flex-col">
                            <span class="text-sm text-primary-400 line-through mb-1">{{ formatPrice($product->price) }}</span>
                            <p class="text-3xl font-black text-red-600">
                                {{ formatPrice($product->flash_sale_price) }}
                            </p>
                        </div>
                    @else
                        <p class="text-3xl font-black text-primary-900 dark:text-white">
                            {{ formatPrice($product->price) }}
                        </p>
                    @endif
                    
                    <div class="flex items-center gap-2 pl-4 border-l border-primary-200">
                        @include('components.star-rating', ['rating' => $product->average_rating, 'size' => 'sm'])
                        <a href="#reviews" class="text-sm text-primary-500 hover:text-black">
                            {{ $product->review_count }} Ulasan
                        </a>
                    </div>
                </div>
            </div>

            <!-- Add to Cart Form -->
            <form action="{{ route('cart.add') }}" method="POST" class="mb-8" @submit.prevent="submitCart" id="add-to-cart-form">
                @csrf
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="text-sm font-semibold uppercase tracking-widest">Pilih Varian</h3>
                        <button type="button" @click="sizeGuideOpen = true" class="text-xs text-primary-500 hover:text-black dark:hover:text-white underline underline-offset-4 decoration-primary-300 flex items-center gap-1 transition-colors">
                            <i data-lucide="ruler" class="w-3.5 h-3.5"></i> Panduan Ukuran
                        </button>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach($product->variants as $variant)
                            <label class="relative border dark:border-onyx-600 cursor-pointer hover:border-black dark:hover:border-white p-3 transition-colors {{ $variant->stock === 0 ? 'opacity-50 cursor-not-allowed bg-primary-50 dark:bg-onyx-800' : '' }}" :class="{'border-black dark:border-white bg-primary-50 dark:bg-onyx-700': selectedVariant == '{{ $variant->id }}'}">
                                <input type="radio" name="variant_id" value="{{ $variant->id }}" class="sr-only" 
                                       x-model="selectedVariant"
                                       {{ $variant->stock === 0 ? 'disabled' : '' }} required>
                                
                                <p class="text-sm font-medium">{{ $variant->color }} - {{ $variant->size }}</p>
                                <p class="text-xs text-primary-500 mt-1">Stok: {{ $variant->stock }}</p>
                                @if($variant->additional_price > 0)
                                    <p class="text-xs text-primary-900 dark:text-white font-semibold mt-1">+{{ formatPrice($variant->additional_price) }}</p>
                                @endif
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-end gap-4 mb-8">
                    <div class="w-32">
                        <label class="block text-xs font-semibold uppercase tracking-widest text-primary-500 mb-2">
                            Kuantitas
                        </label>
                        <div class="flex border border-primary-300 dark:border-onyx-600 rounded">
                            <button type="button" @click="if(quantity > 1) quantity--" class="px-3 py-2 hover:bg-primary-100 dark:hover:bg-onyx-700 text-primary-600 dark:text-gray-300">-</button>
                            <input type="text" name="quantity" :value="quantity" value="1" class="w-full min-w-0 text-center py-2 outline-none text-sm font-medium text-black bg-white dark:bg-onyx-800 dark:text-white" readonly required>
                            <button type="button" @click="quantity++" class="px-3 py-2 hover:bg-primary-100 dark:hover:bg-onyx-700 text-primary-600 dark:text-gray-300">+</button>
                        </div>
                    </div>
                    <button type="submit" class="flex-1 btn-primary flex items-center justify-center gap-2 transition-all duration-300" :disabled="!selectedVariant || adding">
                        <span x-show="!adding">Tambah ke Keranjang</span>
                        <i x-show="adding" data-lucide="loader-2" class="w-5 h-5 animate-spin" style="display: none;"></i>
                    </button>
                </div>
            </form>
            
            <div class="mt-auto pt-6 border-t border-primary-200">
                <div class="prose prose-sm prose-p:text-primary-600 dark:prose-p:text-gray-300 prose-headings:text-primary-900 dark:prose-headings:text-white max-w-none">
                    <h3 class="text-sm font-semibold uppercase tracking-widest mb-3">Deskripsi Produk</h3>
                    <p class="whitespace-pre-line">{{ $product->description }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
        <div class="mb-16 border-t border-primary-200 pt-16">
            <h2 class="text-2xl font-black uppercase tracking-tight text-primary-900 dark:text-white mb-8">Anda Mungkin Suka</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach($relatedProducts as $related)
                    @include('components.product-card', ['product' => $related])
                @endforeach
            </div>
        </div>
    @endif
    
    <!-- Recently Viewed Products -->
    @if(isset($recentProducts) && $recentProducts->count() > 0)
        <div class="mb-16 border-t border-primary-200 pt-16">
            <h2 class="text-2xl font-black uppercase tracking-tight text-primary-900 dark:text-white mb-8">Terakhir Anda Lihat</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach($recentProducts as $recent)
                    @include('components.product-card', ['product' => $recent])
                @endforeach
            </div>
        </div>
    @endif

    <!-- Reviews Section -->
    <div id="reviews" class="border-t-4 border-black dark:border-white pt-16 mb-16">
        <h2 class="text-3xl font-black uppercase tracking-tight text-black dark:text-white mb-10 text-center">Ulasan Pelanggan</h2>
        
        <!-- Only showing existing reviews -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @forelse($reviews as $review)
                <div class="bg-white dark:bg-onyx-900 border-4 border-black dark:border-white p-6 shadow-[8px_8px_0_0_#000] dark:shadow-[8px_8px_0_0_#fff] flex flex-col transition-transform hover:-translate-y-1 hover:shadow-[12px_12px_0_0_#000] dark:hover:shadow-[12px_12px_0_0_#fff]">
                    <div class="flex items-center justify-between mb-4 border-b-4 border-black dark:border-white pb-4">
                        <p class="font-black text-sm uppercase tracking-widest text-black dark:text-white">{{ $review->user->name }}</p>
                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">{{ formatDate($review->created_at) }}</span>
                    </div>
                    
                    <div class="mb-4">
                        @include('components.star-rating', ['rating' => $review->rating, 'size' => 'sm'])
                    </div>
                    
                    @if($review->comment)
                        <p class="text-black dark:text-gray-300 text-sm font-medium italic flex-1">"{{ $review->comment }}"</p>
                    @endif
                    
                    <div class="flex flex-wrap gap-4 mt-6">
                        @if($review->image)
                            <div class="relative group cursor-pointer border-2 border-black dark:border-white overflow-hidden shadow-[4px_4px_0_0_#000] dark:shadow-[4px_4px_0_0_#fff]" @click="lightboxOpen = true; lightboxUrl = '{{ asset('storage/' . $review->image) }}'; lightboxType = 'image'">
                                <img src="{{ asset('storage/' . $review->image) }}" alt="Foto Ulasan" 
                                     class="w-20 h-20 sm:w-24 sm:h-24 object-cover group-hover:scale-110 grayscale group-hover:grayscale-0 transition-all duration-300 pointer-events-none select-none" draggable="false" oncontextmenu="return false;">
                            </div>
                        @endif
                        @if($review->video)
                            <div class="relative group cursor-pointer border-2 border-black dark:border-white overflow-hidden shadow-[4px_4px_0_0_#000] dark:shadow-[4px_4px_0_0_#fff]" @click="lightboxOpen = true; lightboxUrl = '{{ asset('storage/' . $review->video) }}'; lightboxType = 'video'">
                                <video src="{{ asset('storage/' . $review->video) }}" class="w-24 h-20 sm:w-32 sm:h-24 object-cover grayscale group-hover:grayscale-0 transition-all duration-300"></video>
                                <div class="absolute inset-0 flex items-center justify-center bg-black/40 group-hover:bg-black/20 transition-colors">
                                    <i data-lucide="play" class="w-8 h-8 text-white fill-white"></i>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-primary-500">Belum ada ulasan untuk produk ini.</p>
            @endforelse
        </div>

        @if($reviews->hasPages())
            <div class="mt-8">
                {{ $reviews->fragment('reviews')->links('pagination::tailwind') }}
            </div>
        @endif

    </div>
    
    <!-- Lightbox Modal -->
    <div x-show="lightboxOpen" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-white/20 dark:bg-black/40 backdrop-blur-md p-4" x-transition.opacity>
        <button @click="lightboxOpen = false; lightboxUrl = ''" class="absolute top-6 right-6 text-black dark:text-white bg-white/50 dark:bg-black/50 hover:bg-white dark:hover:bg-black rounded-full p-2 transition-all backdrop-blur-sm shadow-sm">
            <i data-lucide="x" class="w-6 h-6"></i>
        </button>
        
        <div class="max-w-5xl max-h-[90vh] w-full flex justify-center items-center" @click.outside="lightboxOpen = false; lightboxUrl = ''">
            <template x-if="lightboxType === 'image'">
                <img :src="lightboxUrl" class="max-w-full max-h-[90vh] object-contain shadow-2xl">
            </template>
            <template x-if="lightboxType === 'video'">
                <video :src="lightboxUrl" controls autoplay class="max-w-full max-h-[90vh] shadow-2xl outline-none"></video>
            </template>
        </div>
    </div>
    
    <!-- Size Guide Modal -->
    <div x-show="sizeGuideOpen" style="display: none;" class="fixed inset-0 z-[110] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4" x-transition.opacity>
        <div class="bg-white dark:bg-onyx-900 w-full max-w-2xl shadow-2xl" @click.outside="sizeGuideOpen = false" x-show="sizeGuideOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="scale-95 opacity-0" x-transition:enter-end="scale-100 opacity-100">
            <div class="flex items-center justify-between px-6 py-4 border-b border-primary-200 dark:border-onyx-700">
                <h3 class="text-lg font-black uppercase tracking-widest">Panduan Ukuran</h3>
                <button @click="sizeGuideOpen = false" class="text-primary-400 hover:text-black dark:hover:text-white"><i data-lucide="x" class="w-6 h-6"></i></button>
            </div>
            <div class="p-6">
                <p class="text-sm text-primary-500 mb-6">Ukur tubuh Anda dan temukan ukuran yang paling sesuai pada tabel di bawah ini.</p>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs uppercase bg-primary-50 dark:bg-onyx-800 text-primary-900 dark:text-white">
                            <tr>
                                <th class="px-4 py-3 font-bold">Ukuran</th>
                                <th class="px-4 py-3 font-bold">Lingkar Dada (cm)</th>
                                <th class="px-4 py-3 font-bold">Panjang (cm)</th>
                                <th class="px-4 py-3 font-bold">Lengan (cm)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-primary-100 dark:divide-onyx-700">
                            <tr><td class="px-4 py-3 font-semibold">S</td><td class="px-4 py-3">96-100</td><td class="px-4 py-3">68</td><td class="px-4 py-3">21</td></tr>
                            <tr><td class="px-4 py-3 font-semibold">M</td><td class="px-4 py-3">100-104</td><td class="px-4 py-3">70</td><td class="px-4 py-3">22</td></tr>
                            <tr><td class="px-4 py-3 font-semibold">L</td><td class="px-4 py-3">104-108</td><td class="px-4 py-3">72</td><td class="px-4 py-3">23</td></tr>
                            <tr><td class="px-4 py-3 font-semibold">XL</td><td class="px-4 py-3">108-112</td><td class="px-4 py-3">74</td><td class="px-4 py-3">24</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sticky Add to Cart Bar -->
    <div x-show="showSticky" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full opacity-0"
         x-transition:enter-end="translate-y-0 opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0 opacity-100"
         x-transition:leave-end="translate-y-full opacity-0"
         style="display: none;"
         class="fixed bottom-0 left-0 w-full bg-white/90 dark:bg-onyx-900/90 backdrop-blur-md border-t border-primary-200 dark:border-onyx-700 shadow-[0_-10px_40px_rgba(0,0,0,0.1)] z-40 py-3 px-4">
        <div class="max-w-7xl mx-auto flex items-center justify-between gap-4">
            <div class="hidden sm:flex items-center gap-4">
                <img src="{{ $product->thumbnail }}" class="w-12 h-16 object-cover border border-primary-200 dark:border-onyx-700 pointer-events-none select-none" draggable="false" oncontextmenu="return false;">
                <div>
                    <h4 class="text-sm font-bold uppercase tracking-tight line-clamp-1 text-primary-900 dark:text-white">{{ $product->name }}</h4>
                    <p class="text-xs text-primary-500 font-bold mt-0.5">
                        {{ $isFlashSaleActive ? formatPrice($product->flash_sale_price) : formatPrice($product->price) }}
                    </p>
                </div>
            </div>
            
            <div class="flex-1 sm:flex-none flex items-center gap-3 w-full sm:w-auto">
                <div class="text-xs font-semibold uppercase text-red-500 mr-2" x-show="!selectedVariant">Pilih varian di atas</div>
                <button type="button" 
                        @click="if(selectedVariant) { document.getElementById('add-to-cart-form').dispatchEvent(new Event('submit', {cancelable: true, bubbles: true})); } else { window.scrollTo({top: 0, behavior: 'smooth'}); }"
                        class="w-full sm:w-64 btn-primary flex items-center justify-center gap-2 py-3"
                        :class="!selectedVariant ? 'opacity-50 cursor-not-allowed' : ''">
                    <span x-show="!adding">Tambah ke Keranjang</span>
                    <i x-show="adding" data-lucide="loader-2" class="w-5 h-5 animate-spin" style="display: none;"></i>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
