@props(['product'])

@php
$isWishlisted = false;
if (auth()->check()) {
    $isWishlisted = \App\Models\Wishlist::where('user_id', auth()->id())
                    ->where('product_id', $product->id)
                    ->exists();
}
@endphp

<div x-data="{ activeImage: 0, images: {{ json_encode(array_merge([$product->thumbnail], $product->images->pluck('image_path')->toArray())) }} }" class="group block animate-fade-up">
    <div class="bg-white border border-transparent hover:border-black transition-all duration-500 relative shadow-sm hover:shadow-2xl">
        <div class="relative aspect-[3/4] bg-primary-50 overflow-hidden group/slider">
            <a href="{{ route('product.show', $product->id) }}" class="block w-full h-full">
                @if($product->thumbnail)
                    <template x-for="(image, index) in images" :key="index">
                        <img :src="image" :alt="'{{ $product->name }}'" x-show="activeImage === index" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110 absolute inset-0" x-transition.opacity.duration.500ms />
                    </template>
                    <!-- Fallback for non-JS / initial load -->
                    <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}" x-show="false" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110 absolute inset-0" />
                @else
                    <div class="w-full h-full flex items-center justify-center bg-primary-100 absolute inset-0">
                        <span class="text-xs uppercase tracking-widest text-primary-400">No Image</span>
                    </div>
                @endif
            </a>

            <!-- Slider Controls -->
            <template x-if="images.length > 1">
                <div class="absolute inset-x-0 top-1/2 -translate-y-1/2 flex justify-between px-2 opacity-0 group-hover/slider:opacity-100 transition-opacity duration-300 pointer-events-none">
                    <button @click.prevent="activeImage = (activeImage - 1 + images.length) % images.length" class="w-8 h-8 flex items-center justify-center bg-white/80 hover:bg-white text-black shadow pointer-events-auto rounded-full backdrop-blur-sm transition-transform hover:scale-110">
                        <i data-lucide="chevron-left" class="w-4 h-4"></i>
                    </button>
                    <button @click.prevent="activeImage = (activeImage + 1) % images.length" class="w-8 h-8 flex items-center justify-center bg-white/80 hover:bg-white text-black shadow pointer-events-auto rounded-full backdrop-blur-sm transition-transform hover:scale-110">
                        <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    </button>
                </div>
            </template>
            
            <!-- Dots Indicator -->
            <template x-if="images.length > 1">
                <div class="absolute bottom-3 left-0 right-0 flex justify-center gap-1.5 opacity-0 group-hover/slider:opacity-100 transition-opacity duration-300">
                    <template x-for="(image, index) in images" :key="index">
                        <div class="w-1.5 h-1.5 rounded-full transition-colors duration-300" :class="activeImage === index ? 'bg-black' : 'bg-black/20'"></div>
                    </template>
                </div>
            </template>

            @auth
            <form action="{{ route('wishlist.toggle') }}" method="POST" class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-all duration-200">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <button type="submit" class="p-2 bg-white border border-primary-200 hover:bg-black hover:text-white hover:border-black" aria-label="Toggle wishlist">
                    <i data-lucide="heart" class="w-3.5 h-3.5 {{ $isWishlisted ? 'fill-black text-black' : '' }}"></i>
                </button>
            </form>
            @endauth

            @if($product->total_stock == 0)
                <div class="absolute inset-0 bg-white/80 flex items-center justify-center">
                    <span class="text-xs font-semibold uppercase tracking-widest bg-black text-white px-4 py-2">
                        Sold Out
                    </span>
                </div>
            @endif
        </div>

        <div class="p-4">
            @if($product->category)
                <p class="text-[10px] text-primary-400 uppercase tracking-widest mb-1">
                    {{ $product->category->name }}
                </p>
            @endif
            <h3 class="font-medium text-primary-900 mb-2 line-clamp-1 text-sm">
                {{ $product->name }}
            </h3>

            <div class="flex items-center justify-between">
                <div class="flex items-center gap-1">
                    <i data-lucide="star" class="w-3 h-3 fill-black text-black"></i>
                    <span class="text-xs text-primary-500">
                        {{ number_format($product->average_rating, 1) }}
                        <span class="text-primary-400 ml-1">({{ $product->review_count }})</span>
                    </span>
                </div>
                <p class="font-semibold text-primary-900 text-sm">
                    {{ formatPrice($product->price) }}
                </p>
            </div>
        </div>
    </div>
</div>
