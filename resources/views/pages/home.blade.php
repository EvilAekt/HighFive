@extends('layouts.app')

@section('title', 'HIGH FIVE - Defined by the Streets')

@section('content')
<!-- Hero Section - Gothic/Streetwear -->
<section class="relative min-h-screen bg-black flex flex-col justify-center items-center overflow-hidden pt-20">
    <div class="absolute inset-0">
        <!-- Menggunakan gambar hero kustom dari brand (Gothic Pattern) -->
        <img src="{{ asset('images/hero-bg.jpg') }}" alt="High Five Hero" class="w-full h-full object-cover pointer-events-none select-none" draggable="false" oncontextmenu="return false;">
    </div>
    
    <div class="relative z-10 px-4 sm:px-6 lg:px-12 w-full flex flex-col items-center justify-center h-full mt-auto pb-20 text-center">
        <div class="mt-auto w-full flex flex-col items-center justify-center gap-8">
            <a href="{{ route('catalog') }}" class="group relative inline-flex items-center justify-center px-10 py-4 border border-white text-white text-xs font-bold tracking-[0.3em] uppercase overflow-hidden transition-all duration-500 hover:bg-white hover:text-black animate-fade-up shadow-[0_0_20px_rgba(255,255,255,0.1)] hover:shadow-[0_0_30px_rgba(255,255,255,0.5)]" style="animation-delay: 300ms;">
                <span class="relative z-10 transition-colors duration-300">Shop The Drop</span>
            </a>
        </div>
    </div>
</section>

<!-- Brutalist Marquee -->
<section class="bg-black text-white py-6 md:py-8 overflow-hidden border-y border-white/20">
    <div class="flex gap-12 animate-marquee whitespace-nowrap">
        @for($i = 0; $i < 6; $i++)
            <span class="text-3xl md:text-5xl font-black uppercase tracking-tighter flex items-center gap-12 select-none">
                <span class="italic font-serif">NEW</span> ARRIVALS
                <span class="text-red-600">✦</span>
                NO COMPROMISE
                <span class="text-red-600">✦</span>
            </span>
        @endfor
    </div>
</section>

<!-- Lookbook / Collections (Asymmetric Grid) -->
<section class="py-20 md:py-32 bg-white dark:bg-onyx-900 transition-colors">
    <div class="max-w-[95%] md:max-w-[90%] mx-auto px-4 sm:px-0">
        <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6">
            <h2 class="text-6xl md:text-8xl font-black uppercase tracking-tighter leading-none text-black dark:text-white">
                THE<br/>
                <span class="italic font-serif text-gray-400">ARCHIVE</span>
            </h2>
            <p class="text-xs uppercase tracking-widest font-bold max-w-xs md:text-right">Curated garments redefining the street aesthetic.</p>
        </div>

        @php
            $cats = $categories->count() > 0 ? $categories : collect([
                (object)['name' => 'Atasan', 'slug' => 'atasan'],
                (object)['name' => 'Bawahan', 'slug' => 'bawahan'],
                (object)['name' => 'Outerwear', 'slug' => 'outerwear']
            ]);
            $defaultImages = [
                asset('images/home_1.jpg'),
                asset('images/home_3.jpg'),
                asset('images/home_2.jpg')
            ];
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 md:gap-10">
            @if($cats->count() > 0)
                <!-- Large Item -->
                <a href="{{ route('catalog', ['category' => $cats[0]->slug]) }}" class="group relative md:col-span-7 h-[60vh] md:h-[80vh] overflow-hidden bg-gray-100">
                    <img src="{{ $defaultImages[0] }}" class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-700 group-hover:scale-105">
                    <div class="absolute inset-0 bg-black/30 group-hover:bg-black/10 transition-colors duration-500"></div>
                    <div class="absolute bottom-8 left-8">
                        <span class="px-3 py-1 bg-white text-black text-[10px] uppercase tracking-widest font-bold mb-3 inline-block">Collection 01</span>
                        <h3 class="text-4xl md:text-6xl font-black text-white uppercase tracking-tighter">{{ $cats[0]->name }}</h3>
                    </div>
                </a>
            @endif

            <div class="md:col-span-5 flex flex-col gap-6 md:gap-10">
                @if($cats->count() > 1)
                    <!-- Medium Item -->
                    <a href="{{ route('catalog', ['category' => $cats[1]->slug]) }}" class="group relative h-[45vh] md:h-[40vh] overflow-hidden bg-gray-100">
                        <img src="{{ $defaultImages[1] }}" class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-700 group-hover:scale-105">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                        <div class="absolute bottom-6 left-6">
                            <h3 class="text-3xl font-black text-white uppercase tracking-tighter">{{ $cats[1]->name }}</h3>
                        </div>
                    </a>
                @endif
                
                @if($cats->count() > 2)
                    <!-- Medium Item 2 -->
                    <a href="{{ route('catalog', ['category' => $cats[2]->slug]) }}" class="group relative h-[45vh] md:h-[calc(40vh-2.5rem)] overflow-hidden bg-gray-100">
                        <img src="{{ $defaultImages[2] }}" class="w-full h-full object-cover object-top grayscale group-hover:grayscale-0 transition-all duration-700 group-hover:scale-105">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                        <div class="absolute bottom-6 left-6">
                            <h3 class="text-3xl font-black text-white uppercase tracking-tighter">{{ $cats[2]->name }}</h3>
                        </div>
                    </a>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Featured Products - Edgy Grid -->
<section class="py-20 md:py-32 bg-gray-100 dark:bg-onyx-800 transition-colors border-y border-gray-200 dark:border-onyx-700">
    <div class="max-w-[95%] md:max-w-[90%] mx-auto px-4 sm:px-0">
        <div class="flex flex-col md:flex-row items-center justify-between mb-16 gap-8">
            <h2 class="text-5xl md:text-7xl font-black uppercase tracking-tighter text-black dark:text-white leading-[0.9]">
                <span class="text-transparent [-webkit-text-stroke:1.5px_black] dark:[-webkit-text-stroke:1.5px_white] italic hover:scale-105 hover:-rotate-2 inline-block transition-transform duration-300">STREET</span><br/>
                <span class="italic font-serif text-transparent [-webkit-text-stroke:1.5px_black] dark:[-webkit-text-stroke:1.5px_white]">ESSENTIALS</span>
            </h2>
            <a href="{{ route('catalog') }}" class="group relative inline-flex items-center justify-center px-8 py-4 bg-black text-white text-[10px] font-bold tracking-[0.2em] uppercase overflow-hidden dark:bg-white dark:text-black">
                <span class="relative z-10">View All Items</span>
                <div class="absolute inset-0 bg-red-600 translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-out z-0"></div>
            </a>
        </div>

        @if(isset($featuredProducts) && $featuredProducts->count() > 0)
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-8">
                @foreach($featuredProducts as $product)
                    @include('components.product-card', ['product' => $product])
                @endforeach
            </div>
        @else
            <div class="text-center py-32 border-2 border-dashed border-gray-300 dark:border-gray-700">
                <p class="text-sm font-bold uppercase tracking-widest text-gray-500">Dropping soon.</p>
            </div>
        @endif
    </div>
</section>

<!-- Flash Sale - Aggressive Red -->
@if(isset($flashSaleProduct))
<section class="py-20 md:py-24 bg-red-600 text-white relative overflow-hidden" x-data="countdownTimer(new Date('{{ $flashSaleProduct->flash_sale_end }}').getTime())">
    <div class="absolute inset-0 mix-blend-multiply opacity-20">
        <!-- Menggunakan gambar motif (misal: logo tangan) untuk background promo -->
        <img src="{{ asset('images/hand-logo.png') }}" class="w-full h-full object-cover grayscale opacity-50" style="object-position: center;">
    </div>
    
    <div class="max-w-[95%] md:max-w-[90%] mx-auto px-4 sm:px-0 relative z-10 flex flex-col lg:flex-row items-center justify-between gap-12 lg:gap-8 xl:gap-16">
        <div class="w-full lg:w-7/12 flex flex-col justify-center">
            <p class="text-sm md:text-base uppercase tracking-widest mb-6 font-bold border-l-4 border-black pl-4 animate-pulse">Limited Release</p>
            
            <div class="flex flex-col lg:flex-row lg:items-center justify-start gap-8 lg:gap-10 xl:gap-16 w-full">
                <h2 class="text-7xl md:text-[7.5rem] xl:text-[8.5rem] font-black uppercase tracking-tighter leading-[0.85] text-white drop-shadow-2xl hover:scale-105 transition-transform duration-300 origin-left">
                    FLASH<br/>
                    <span class="italic font-serif text-black drop-shadow-none">DROP</span>
                </h2>
                
                <div class="flex items-center lg:items-end gap-3 sm:gap-4 lg:gap-5 text-black font-black relative z-10 mx-auto lg:mx-0 mt-4 lg:mt-0">
                    <div class="flex flex-col items-center"><span class="text-6xl md:text-7xl lg:text-6xl xl:text-[5.5rem] leading-none tracking-tighter" x-text="hours">00</span><span class="text-xs md:text-sm uppercase mt-1 font-bold">HRS</span></div>
                    <span class="text-5xl md:text-6xl lg:text-5xl xl:text-6xl mb-4 md:mb-6 lg:mb-4 xl:mb-6 animate-pulse text-black">:</span>
                    <div class="flex flex-col items-center"><span class="text-6xl md:text-7xl lg:text-6xl xl:text-[5.5rem] leading-none tracking-tighter" x-text="minutes">00</span><span class="text-xs md:text-sm uppercase mt-1 font-bold">MIN</span></div>
                    <span class="text-5xl md:text-6xl lg:text-5xl xl:text-6xl mb-4 md:mb-6 lg:mb-4 xl:mb-6 animate-pulse text-black">:</span>
                    <div class="flex flex-col items-center"><span class="text-6xl md:text-7xl lg:text-6xl xl:text-[5.5rem] leading-none tracking-tighter text-white drop-shadow-[0_0_15px_rgba(255,255,255,0.8)] animate-pulse" x-text="seconds">00</span><span class="text-xs md:text-sm uppercase mt-1 font-bold text-white">SEC</span></div>
                </div>
            </div>
        </div>
        
        <div class="w-full lg:w-5/12 flex flex-col items-center lg:items-center">
            <div class="w-full max-w-sm bg-black p-4 rotate-2 hover:rotate-0 transition-transform duration-500 shadow-2xl group">
                @php
                    $fsImage = $flashSaleProduct->thumbnail ?? ($flashSaleProduct->images->count() > 0 ? $flashSaleProduct->images->first()->image_path : 'https://placehold.co/600');
                    $fsImageUrl = str_starts_with($fsImage, 'http') ? $fsImage : (str_starts_with($fsImage, '/') ? $fsImage : '/storage/' . $fsImage);
                @endphp
                <div class="overflow-hidden bg-gray-900 aspect-square">
                    <img src="{{ $fsImageUrl }}" class="w-full h-full object-cover grayscale group-hover:grayscale-0 group-hover:scale-105 transition-all duration-700">
                </div>
                <div class="pt-6 pb-2 text-center">
                    <h3 class="text-lg sm:text-xl font-bold uppercase tracking-wide truncate mb-2 text-white">{{ $flashSaleProduct->name }}</h3>
                    <div class="flex justify-center items-center gap-3">
                        <span class="text-gray-500 line-through text-sm">{{ formatPrice($flashSaleProduct->price) }}</span>
                        <span class="text-red-500 font-black text-xl">{{ formatPrice($flashSaleProduct->flash_sale_price) }}</span>
                    </div>
                    <a href="{{ route('product.show', $flashSaleProduct->id) }}" class="mt-6 block w-full py-4 bg-white text-black text-xs font-bold uppercase tracking-widest hover:bg-gray-200 transition-colors">
                        Cop Now
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('countdownTimer', (endTime) => ({
                timeRemaining: 0,
                timer: null,
                init() {
                    this.updateTime();
                    this.timer = setInterval(() => this.updateTime(), 1000);
                },
                updateTime() {
                    this.timeRemaining = Math.max(0, endTime - new Date().getTime());
                },
                get hours() {
                    return String(Math.floor((this.timeRemaining % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))).padStart(2, '0');
                },
                get minutes() {
                    return String(Math.floor((this.timeRemaining % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, '0');
                },
                get seconds() {
                    return String(Math.floor((this.timeRemaining % (1000 * 60)) / 1000)).padStart(2, '0');
                }
            }))
        })
    </script>
    @endpush
</section>
@endif

<!-- Brand Manifesto / About -->
<section class="py-20 md:py-32 bg-white dark:bg-onyx-900 text-black dark:text-white border-t-2 border-black dark:border-white relative overflow-hidden">
    <!-- Decorative background elements -->
    <div class="absolute top-10 right-10 text-[10vw] font-black text-black/5 dark:text-white/5 uppercase tracking-tighter pointer-events-none rotate-90 origin-right">
        EST.2026
    </div>
    
    <div class="max-w-[95%] md:max-w-[90%] mx-auto px-4 sm:px-0 flex flex-col lg:flex-row items-center gap-12 lg:gap-24 relative z-10">
        <div class="w-full lg:w-1/2">
            <h2 class="text-6xl md:text-8xl font-black uppercase tracking-tighter mb-8 leading-[0.85]">
                THE<br/>
                <span class="text-transparent [-webkit-text-stroke:2px_black] dark:[-webkit-text-stroke:2px_white] italic">MANIFESTO</span>
            </h2>
            <div class="w-24 h-3 bg-black dark:bg-white mb-10"></div>
            <p class="text-xl md:text-2xl font-bold leading-snug text-gray-800 dark:text-gray-200 uppercase tracking-wide">
                Didirikan pada tahun <span class="bg-black text-white dark:bg-white dark:text-black px-3 py-1 mx-1 italic">2026</span>, HIGH FIVE lahir dari visi sederhana: menciptakan pakaian berkualitas tinggi dengan desain minimalis yang dapat diakses oleh semua orang.
            </p>
            <p class="text-lg md:text-xl font-medium leading-relaxed text-gray-600 dark:text-gray-400 mt-8 border-l-4 border-gray-400 dark:border-gray-500 pl-6">
                Kami percaya bahwa gaya yang baik tidak harus rumit atau mahal.
            </p>
        </div>
        <div class="w-full lg:w-1/2 relative group">
            <!-- Brutalist offset shadow -->
            <div class="absolute inset-0 bg-gray-300 dark:bg-gray-700 translate-x-4 translate-y-4 md:translate-x-8 md:translate-y-8 transition-transform duration-500 group-hover:translate-x-2 group-hover:translate-y-2"></div>
            <img src="{{ asset('images/manifesto-v3.png') }}" alt="High Five Culture" class="relative z-10 w-full h-[400px] md:h-[600px] object-cover grayscale group-hover:grayscale-0 transition-all duration-700 border-4 border-black dark:border-white pointer-events-none select-none" draggable="false" oncontextmenu="return false;">
        </div>
    </div>
</section>

<!-- Footer/Newsletter - Brutalist Block -->
<section class="py-20 md:py-32 bg-black text-white relative border-t border-white/20 overflow-hidden">
    <div class="absolute -top-10 left-1/2 -translate-x-1/2 text-[15vw] font-black text-white/5 uppercase tracking-tighter whitespace-nowrap select-none pointer-events-none">
        HIGH FIVE
    </div>
    
    <div class="max-w-4xl mx-auto px-4 text-center relative z-10">
        <h2 class="text-4xl md:text-6xl font-black uppercase tracking-tighter mb-6">
            ENTER THE <span class="italic font-serif text-gray-400">SYNDICATE</span>
        </h2>
        <p class="text-gray-400 mb-12 text-sm md:text-base font-medium max-w-lg mx-auto">
            Early access to drops. Exclusive pieces. No spam, just heat.
        </p>
        <form class="flex flex-col sm:flex-row relative max-w-2xl mx-auto border-2 border-white/20 focus-within:border-white transition-colors">
            <input
                type="email"
                placeholder="YOUR EMAIL"
                class="w-full px-6 py-5 bg-transparent border-none focus:ring-0 text-white placeholder-gray-600 text-sm md:text-base font-bold uppercase tracking-widest outline-none"
                required
            />
            <button type="submit" class="sm:absolute right-0 top-0 bottom-0 mt-2 sm:mt-0 px-8 py-5 sm:py-0 bg-white text-black text-xs font-black uppercase tracking-widest hover:bg-gray-200 transition-colors">
                Join
            </button>
        </form>
    </div>
</section>
@endsection
