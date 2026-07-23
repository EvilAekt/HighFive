@props(['product'])

@php
    $isWishlisted = auth()->check() ? \App\Models\Wishlist::where('user_id', auth()->id())->where('product_id', $product->id)->exists() : false;
@endphp

<div x-data="{ activeImage: 0, hoverActive: false, images: {{ json_encode(collect([$product->thumbnail])->merge($product->images->pluck('image_path'))->filter()->unique()->values()->toArray()) }} }" 
     @mouseenter="if(images.length > 1) { activeImage = 1; hoverActive = true; }"
     @mouseleave="if(hoverActive) { activeImage = 0; hoverActive = false; }"
     class="group block animate-fade-up relative">
    <div class="bg-white dark:bg-onyx-800 border-2 border-transparent dark:border-onyx-700 hover:border-black dark:hover:border-onyx-500 transition-all duration-300 relative shadow-sm hover:shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[8px_8px_0px_0px_rgba(255,255,255,0.2)] hover:-translate-y-1 hover:-translate-x-1">
        <div class="relative aspect-[3/4] bg-primary-50 dark:bg-onyx-900 overflow-hidden group/slider" x-data="{ loaded: false }">
            <!-- Skeleton Loader (Shimmer) -->
            <div x-show="!loaded" class="absolute inset-0 z-0 bg-gray-200 dark:bg-onyx-700 animate-pulse"></div>

            <a href="{{ route('product.show', $product->id) }}" class="block w-full h-full z-10 relative">
                @if($product->thumbnail)
                    <template x-for="(image, index) in images" :key="index">
                        <img :src="image" :alt="'{{ $product->name }}'" x-show="activeImage === index" class="w-full h-full object-cover transition-all duration-700 absolute inset-0 pointer-events-none select-none" :class="[loaded ? 'opacity-100' : 'opacity-0', images.length === 1 ? 'group-hover:scale-105 group-hover:brightness-90' : '']" x-transition.opacity.duration.500ms draggable="false" oncontextmenu="return false;" />
                    </template>
                    <!-- Preload / Event listener for the first image -->
                    <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}" x-show="false" @load="loaded = true" x-init="if($el.complete) loaded = true" class="w-full h-full object-cover absolute inset-0 pointer-events-none select-none" draggable="false" oncontextmenu="return false;" />
                @else
                    <div class="w-full h-full flex items-center justify-center bg-primary-100 dark:bg-onyx-800 absolute inset-0">
                        <span class="text-xs uppercase tracking-widest text-primary-400 dark:text-gray-500">No Image</span>
                    </div>
                @endif
                
                <!-- Quick View Bar -->
                <div class="absolute bottom-0 inset-x-0 bg-black dark:bg-white text-white dark:text-black text-[10px] sm:text-xs font-bold uppercase tracking-widest py-3 sm:py-4 text-center translate-y-full group-hover:translate-y-0 transition-transform duration-300 z-20">
                    View Details
                </div>
            </a>

            <!-- Wishlist Button -->
            <div class="absolute top-2 right-2 z-10" x-data="{ isWishlisted: {{ $isWishlisted ? 'true' : 'false' }}, loading: false }">
                <button @click.prevent="
                    @if(auth()->check())
                        if(loading) return;
                        loading = true;
                        fetch('{{ route('wishlist.toggle') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ product_id: '{{ $product->id }}' })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if(data.status === 'added') {
                                isWishlisted = true;
                            } else {
                                isWishlisted = false;
                            }
                        })
                        .finally(() => loading = false);
                    @else
                        window.location.href = '{{ route('login') }}';
                    @endif
                " class="w-8 h-8 flex items-center justify-center bg-white/90 dark:bg-onyx-800/90 hover:bg-white dark:hover:bg-onyx-700 text-black dark:text-white shadow-sm rounded-full backdrop-blur-sm transition-transform hover:scale-110">
                    <i data-lucide="heart" class="w-4 h-4 transition-colors" :class="isWishlisted ? 'fill-red-500 text-red-500' : 'text-gray-500 dark:text-gray-400'"></i>
                </button>
            </div>

            <!-- Slider Controls -->
            <template x-if="images.length > 1">
                <div class="absolute inset-x-0 top-1/2 -translate-y-1/2 flex justify-between px-2 opacity-0 group-hover/slider:opacity-100 transition-opacity duration-300 pointer-events-none">
                    <button @click.prevent="activeImage = (activeImage - 1 + images.length) % images.length" class="w-8 h-8 flex items-center justify-center bg-white/80 dark:bg-onyx-800/80 hover:bg-white dark:hover:bg-onyx-700 text-black dark:text-white shadow pointer-events-auto rounded-full backdrop-blur-sm transition-transform hover:scale-110">
                        <i data-lucide="chevron-left" class="w-4 h-4"></i>
                    </button>
                    <button @click.prevent="activeImage = (activeImage + 1) % images.length" class="w-8 h-8 flex items-center justify-center bg-white/80 dark:bg-onyx-800/80 hover:bg-white dark:hover:bg-onyx-700 text-black dark:text-white shadow pointer-events-auto rounded-full backdrop-blur-sm transition-transform hover:scale-110">
                        <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    </button>
                </div>
            </template>
            
            <!-- Dots Indicator -->
            <template x-if="images.length > 1">
                <div class="absolute bottom-3 left-0 right-0 flex justify-center gap-1.5 opacity-0 group-hover/slider:opacity-100 transition-opacity duration-300">
                    <template x-for="(image, index) in images" :key="index">
                        <div class="w-1.5 h-1.5 rounded-full transition-colors duration-300" :class="activeImage === index ? 'bg-black dark:bg-white' : 'bg-black/20 dark:bg-white/20'"></div>
                    </template>
                </div>
            </template>

            @if($product->total_stock == 0)
                <div class="absolute inset-0 bg-white/80 dark:bg-onyx-900/80 flex items-center justify-center">
                    <span class="text-xs font-semibold uppercase tracking-widest bg-black text-white px-4 py-2">
                        Sold Out
                    </span>
                </div>
            @endif
        </div>

        <div class="p-4">
            @if($product->category)
                <p class="text-[10px] text-primary-400 dark:text-gray-500 uppercase tracking-widest mb-1">
                    {{ $product->category->name }}
                </p>
            @endif
            <h3 class="font-medium text-primary-900 dark:text-white mb-2 line-clamp-1 text-sm">
                {{ $product->name }}
            </h3>

            <div class="flex items-center justify-between">
                <div class="flex items-center gap-1">
                    <i data-lucide="star" class="w-3 h-3 fill-black text-black dark:fill-yellow-400 dark:text-yellow-400"></i>
                    <span class="text-xs text-primary-500 dark:text-gray-400">
                        {{ number_format($product->average_rating, 1) }}
                        <span class="text-primary-400 dark:text-gray-500 ml-1">({{ $product->review_count }})</span>
                    </span>
                </div>
                <div class="flex flex-col items-end">
                    @php
                        $isFlashSaleActive = $product->is_flash_sale && \Carbon\Carbon::now()->lt($product->flash_sale_end);
                    @endphp
                    
                    @if($isFlashSaleActive)
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-primary-400 dark:text-gray-500 line-through">{{ formatPrice($product->price) }}</span>
                            <span class="font-bold text-red-600 dark:text-red-400 text-sm">{{ formatPrice($product->flash_sale_price) }}</span>
                        </div>
                    @else
                        <p class="font-semibold text-primary-900 dark:text-white text-sm">
                            {{ formatPrice($product->price) }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
        
        @if(isset($isFlashSaleActive) && $isFlashSaleActive)
            <div class="absolute top-2 left-2 bg-red-600 text-white text-[10px] font-bold uppercase tracking-widest px-2 py-1 shadow-md">
                Flash Sale
            </div>
        @endif
    </div>
</div>
