@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ selectedVariant: null, quantity: 1 }">
    <!-- Breadcrumb -->
    <nav class="flex text-sm text-primary-500 mb-8" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2">
            <li><a href="{{ route('home') }}" class="hover:text-primary-900">Home</a></li>
            <li><span class="mx-2">/</span></li>
            <li><a href="{{ route('catalog') }}" class="hover:text-primary-900">Catalog</a></li>
            <li><span class="mx-2">/</span></li>
            <li class="text-primary-900 font-medium truncate" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-16">
        <!-- Images -->
        <div class="space-y-4">
            <div class="aspect-[3/4] bg-primary-100 border border-primary-200">
                <img id="main-image" src="{{ $product->thumbnail }}" alt="{{ $product->name }}" class="w-full h-full object-cover" />
            </div>
            
            @if($product->images->count() > 0)
                <div class="grid grid-cols-4 gap-4">
                    <button class="aspect-square border border-black p-1" onclick="document.getElementById('main-image').src='{{ $product->thumbnail }}'">
                        <img src="{{ $product->thumbnail }}" class="w-full h-full object-cover" />
                    </button>
                    @foreach($product->images as $image)
                        <button class="aspect-square border border-primary-200 p-1 hover:border-black transition-colors" onclick="document.getElementById('main-image').src='{{ $image->image_path }}'">
                            <img src="{{ $image->image_path }}" class="w-full h-full object-cover" />
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Product Info -->
        <div class="flex flex-col">
            <div class="mb-6 pb-6 border-b border-primary-200">
                @if($product->category)
                    <p class="text-xs font-semibold uppercase tracking-widest text-primary-500 mb-2">
                        {{ $product->category->name }}
                    </p>
                @endif
                <h1 class="text-3xl font-black uppercase tracking-tight text-primary-900 mb-4">
                    {{ $product->name }}
                </h1>
                
                <div class="flex items-center gap-4 mb-4">
                    <p class="text-2xl font-bold text-primary-900">
                        {{ formatPrice($product->price) }}
                    </p>
                    <div class="flex items-center gap-2 pl-4 border-l border-primary-200">
                        @include('components.star-rating', ['rating' => $product->average_rating, 'size' => 'sm'])
                        <a href="#reviews" class="text-sm text-primary-500 hover:text-black">
                            {{ $product->review_count }} Ulasan
                        </a>
                    </div>
                </div>
            </div>

            <!-- Add to Cart Form -->
            <form action="{{ route('cart.add') }}" method="POST" class="mb-8">
                @csrf
                <div class="mb-6">
                    <h3 class="text-sm font-semibold uppercase tracking-widest mb-3">Pilih Varian</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach($product->variants as $variant)
                            <label class="relative border cursor-pointer hover:border-black p-3 transition-colors {{ $variant->stock === 0 ? 'opacity-50 cursor-not-allowed bg-primary-50' : '' }}" :class="{'border-black bg-primary-50': selectedVariant == '{{ $variant->id }}'}">
                                <input type="radio" name="variant_id" value="{{ $variant->id }}" class="sr-only" 
                                       x-model="selectedVariant"
                                       {{ $variant->stock === 0 ? 'disabled' : '' }} required>
                                
                                <p class="text-sm font-medium">{{ $variant->color }} - {{ $variant->size }}</p>
                                <p class="text-xs text-primary-500 mt-1">Stok: {{ $variant->stock }}</p>
                                @if($variant->additional_price > 0)
                                    <p class="text-xs text-primary-900 font-semibold mt-1">+{{ formatPrice($variant->additional_price) }}</p>
                                @endif
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-end gap-4 mb-8">
                    <div class="w-24">
                        <label class="block text-xs font-semibold uppercase tracking-widest text-primary-500 mb-2">
                            Kuantitas
                        </label>
                        <div class="flex border border-primary-300">
                            <button type="button" @click="if(quantity > 1) quantity--" class="px-3 py-2 hover:bg-primary-100 text-primary-600">-</button>
                            <input type="number" name="quantity" x-model="quantity" value="1" min="1" class="w-full text-center py-2 appearance-none outline-none text-sm font-medium text-black" required>
                            <button type="button" @click="quantity++" class="px-3 py-2 hover:bg-primary-100 text-primary-600">+</button>
                        </div>
                    </div>
                    <button type="submit" class="flex-1 btn-primary" :disabled="!selectedVariant">
                        Tambah ke Keranjang
                    </button>
                </div>
            </form>
            
            <div class="mt-auto pt-6 border-t border-primary-200">
                <div class="prose prose-sm prose-p:text-primary-600 prose-headings:text-primary-900 max-w-none">
                    <h3 class="text-sm font-semibold uppercase tracking-widest mb-3">Deskripsi Produk</h3>
                    <p class="whitespace-pre-line">{{ $product->description }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
        <div class="mb-16 border-t border-primary-200 pt-16">
            <h2 class="text-2xl font-black uppercase tracking-tight text-primary-900 mb-8">Anda Mungkin Suka</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach($relatedProducts as $related)
                    @include('components.product-card', ['product' => $related])
                @endforeach
            </div>
        </div>
    @endif

    <!-- Reviews Section -->
    <div id="reviews" class="border-t border-primary-200 pt-16" x-data="{ lightboxOpen: false, lightboxUrl: '', lightboxType: '' }">
        <h2 class="text-2xl font-black uppercase tracking-tight text-primary-900 mb-8">Ulasan Pelanggan</h2>
        
        <!-- Only showing existing reviews -->
        <div class="space-y-6">
            @forelse($product->reviews as $review)
                <div class="pb-6 border-b border-primary-100 last:border-0">
                    <div class="flex items-center justify-between mb-2">
                        <p class="font-medium text-sm">{{ $review->user->name }}</p>
                        <span class="text-xs text-primary-500">{{ formatDate($review->created_at) }}</span>
                    </div>
                    @include('components.star-rating', ['rating' => $review->rating, 'size' => 'sm'])
                    @if($review->comment)
                        <p class="text-primary-700 text-sm mt-3">{{ $review->comment }}</p>
                    @endif
                    <div class="flex gap-4 mt-4">
                        @if($review->image)
                            <div>
                                <img src="{{ asset('storage/' . $review->image) }}" alt="Foto Ulasan" 
                                     class="w-24 h-24 object-cover border border-primary-200 shadow-sm hover:scale-105 transition-transform cursor-pointer" 
                                     @click="lightboxOpen = true; lightboxUrl = '{{ asset('storage/' . $review->image) }}'; lightboxType = 'image'">
                            </div>
                        @endif
                        @if($review->video)
                            <div class="relative group cursor-pointer" @click="lightboxOpen = true; lightboxUrl = '{{ asset('storage/' . $review->video) }}'; lightboxType = 'video'">
                                <video src="{{ asset('storage/' . $review->video) }}" class="w-32 h-24 object-cover border border-primary-200 shadow-sm group-hover:opacity-80 transition-opacity"></video>
                                <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-20 group-hover:bg-opacity-40 transition-all">
                                    <i data-lucide="play-circle" class="w-8 h-8 text-white opacity-80"></i>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-primary-500">Belum ada ulasan untuk produk ini.</p>
            @endforelse
        </div>

        <!-- Lightbox Modal -->
        <div x-show="lightboxOpen" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-95 p-4" x-transition.opacity>
            <button @click="lightboxOpen = false; lightboxUrl = ''" class="absolute top-6 right-6 text-white/50 hover:text-white transition-colors">
                <i data-lucide="x" class="w-8 h-8"></i>
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
    </div>
</div>
@endsection
