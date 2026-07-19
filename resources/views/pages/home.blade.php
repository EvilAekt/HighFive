@extends('layouts.app')

@section('title', 'HIGH FIVE - Premium Fashion Brand')

@section('content')
<!-- Hero Section -->
<section class="relative min-h-[90vh] bg-black flex items-center justify-center overflow-hidden">
    <div class="absolute inset-0">
        <video 
            autoplay loop muted playsinline 
            class="w-full h-full object-cover opacity-60"
            poster="https://images.pexels.com/photos/298863/pexels-photo-298863.jpeg?auto=compress&cs=tinysrgb&w=1920"
        >
            <!-- Fashion related stock video -->
            <source src="https://cdn.coverr.co/videos/coverr-fashion-model-posing-2693/1080p.mp4" type="video/mp4">
            <!-- Fallback image if video fails -->
            <img src="https://images.pexels.com/photos/298863/pexels-photo-298863.jpeg?auto=compress&cs=tinysrgb&w=1920" alt="Hero" class="w-full h-full object-cover opacity-60">
        </video>
    </div>
    <!-- Subtle gradient overlay for better text readability -->
    <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-transparent to-black/80"></div>
    
    <div class="relative z-10 text-center px-4 max-w-5xl mx-auto flex flex-col items-center pb-24 sm:pb-32">
        <p class="text-white/80 text-xs uppercase tracking-[0.3em] mb-6 font-medium animate-fade-up">
            {{ $brandProfile->tagline ?? 'Premium Fashion Brand — Jogja' }}
        </p>
        
        <!-- Huge, sleek typography -->
        <h1 class="text-6xl sm:text-7xl md:text-9xl font-black text-white leading-[0.85] mb-8 uppercase tracking-tighter drop-shadow-2xl animate-fade-up" style="animation-delay: 100ms;">
            HIGH<br />
            <span class="font-thin text-transparent bg-clip-text bg-gradient-to-r from-gray-100 to-gray-500">FIVE</span>
        </h1>
        
        <p class="text-sm md:text-base text-gray-300 mb-12 max-w-xl leading-relaxed font-light animate-fade-up" style="animation-delay: 200ms;">
            {{ $brandProfile->story ?? 'Koleksi fashion minimalis dan elegan untuk gaya hidup modern Anda. Kualitas premium tanpa kompromi.' }}
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 sm:gap-6 animate-fade-up w-full max-w-[280px] sm:max-w-none" style="animation-delay: 300ms;">
            <a href="{{ route('catalog') }}" class="group relative inline-flex items-center justify-center px-6 sm:px-10 py-3 sm:py-4 bg-white text-black text-xs font-bold tracking-[0.2em] uppercase overflow-hidden w-full sm:w-auto">
                <span class="relative z-10 transition-colors duration-300 group-hover:text-white">Shop The Collection</span>
                <div class="absolute inset-0 bg-black translate-y-full group-hover:translate-y-0 transition-transform duration-500 ease-out z-0"></div>
            </a>
            <a href="{{ route('catalog', ['sort' => 'newest']) }}" class="inline-flex items-center justify-center px-6 sm:px-10 py-3 sm:py-4 text-xs font-bold uppercase tracking-[0.2em] text-white border border-white/30 hover:border-white hover:bg-white/10 transition-all duration-300 backdrop-blur-sm w-full sm:w-auto">
                New Arrivals
            </a>
        </div>
    </div>
    
    <!-- Minimalist Scroll indicator -->
    <div class="absolute bottom-6 sm:bottom-10 left-1/2 -translate-x-1/2 flex flex-col items-center gap-3 sm:gap-4 text-white/50 animate-bounce">
        <span class="text-[10px] uppercase tracking-[0.3em]">Scroll</span>
        <div class="w-[1px] h-12 bg-gradient-to-b from-white/50 to-transparent"></div>
    </div>
</section>

<!-- Marquee strip - Ultra minimal -->
<section class="bg-black text-white py-5 overflow-hidden border-b border-white/10">
    <div class="flex gap-16 animate-marquee whitespace-nowrap opacity-80">
        @for($i = 0; $i < 8; $i++)
            <span class="text-[10px] uppercase tracking-[0.2em] font-medium shrink-0 flex items-center gap-16">
                <span>New Arrivals</span>
                <span class="w-1 h-1 rounded-full bg-white/30"></span>
                <span>Premium Quality</span>
                <span class="w-1 h-1 rounded-full bg-white/30"></span>
                <span>Free Shipping</span>
                <span class="w-1 h-1 rounded-full bg-white/30"></span>
                <span>HIGH FIVE</span>
            </span>
        @endfor
    </div>
</section>

<!-- Categories - Editorial Style -->
<section class="py-12 md:py-16 bg-white dark:bg-onyx-800 transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10 md:mb-16">
            <h2 class="text-4xl md:text-5xl font-black uppercase tracking-tighter text-black dark:text-white">
                The Categories
            </h2>
            <div class="w-12 h-1 bg-black dark:bg-white mx-auto mt-6"></div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-12">
            @php
                $defaultImages = [
                    'atasan' => 'https://images.pexels.com/photos/1043474/pexels-photo-1043474.jpeg?auto=compress&cs=tinysrgb&w=800',
                    'bawahan' => 'https://images.pexels.com/photos/1598507/pexels-photo-1598507.jpeg?auto=compress&cs=tinysrgb&w=800',
                    'outerwear' => 'https://images.pexels.com/photos/1689731/pexels-photo-1689731.jpeg?auto=compress&cs=tinysrgb&w=800'
                ];
                $cats = $categories->count() > 0 ? $categories : collect([
                    (object)['name' => 'Atasan', 'slug' => 'atasan'],
                    (object)['name' => 'Bawahan', 'slug' => 'bawahan'],
                    (object)['name' => 'Outerwear', 'slug' => 'outerwear']
                ]);
            @endphp
            
            @foreach($cats as $category)
                <a href="{{ route('catalog', ['category' => $category->slug]) }}" class="group block relative">
                    <div class="relative h-[450px] md:h-[600px] overflow-hidden mb-6 bg-gray-100 dark:bg-onyx-700">
                        <img 
                            src="{{ $defaultImages[strtolower($category->name)] ?? 'https://images.pexels.com/photos/2983464/pexels-photo-2983464.jpeg?auto=compress&cs=tinysrgb&w=800' }}" 
                            alt="{{ $category->name }}"
                            class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110 grayscale group-hover:grayscale-0"
                        />
                        <div class="absolute inset-0 bg-black/20 group-hover:bg-transparent transition-colors duration-500"></div>
                    </div>
                    <div class="flex items-center justify-between">
                        <h3 class="text-2xl font-bold uppercase tracking-tight text-black dark:text-white">
                            {{ $category->name }}
                        </h3>
                        <i data-lucide="arrow-right" class="w-5 h-5 text-black dark:text-white transform group-hover:translate-x-2 transition-transform"></i>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-widest mt-2">Discover Collection</p>
                </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Flash Sale - Urgent Elegance -->
@if(isset($flashSaleProduct))
<section class="py-12 md:py-16 bg-red-900 text-white relative overflow-hidden" x-data="countdownTimer(new Date('{{ $flashSaleProduct->flash_sale_end }}').getTime())">
    <div class="absolute inset-0 opacity-10">
        <div class="w-full h-full" style="background-image: repeating-linear-gradient(45deg, transparent, transparent 10px, #ffffff 10px, #ffffff 20px);"></div>
    </div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
        <div>
            <p class="text-[10px] uppercase tracking-[0.3em] mb-2 font-medium">Limited Time Offer</p>
            <h2 class="text-4xl md:text-5xl font-black uppercase tracking-tighter mb-4">Flash Sale</h2>
            <p class="text-white/80 font-light text-sm max-w-md">Dapatkan potongan harga eksklusif untuk koleksi terpilih kami. Waktu terus berjalan.</p>
        </div>
        
        <!-- Timer -->
        <div class="flex items-center gap-4 text-center">
            <div class="flex flex-col items-center">
                <span class="text-4xl md:text-5xl font-black font-mono tracking-tighter" x-text="hours">00</span>
                <span class="text-[10px] uppercase tracking-widest mt-1">Hours</span>
            </div>
            <span class="text-3xl font-black mb-4">:</span>
            <div class="flex flex-col items-center">
                <span class="text-4xl md:text-5xl font-black font-mono tracking-tighter" x-text="minutes">00</span>
                <span class="text-[10px] uppercase tracking-widest mt-1">Mins</span>
            </div>
            <span class="text-3xl font-black mb-4">:</span>
            <div class="flex flex-col items-center">
                <span class="text-4xl md:text-5xl font-black font-mono tracking-tighter" x-text="seconds">00</span>
                <span class="text-[10px] uppercase tracking-widest mt-1">Secs</span>
            </div>
        </div>
        
        <a href="{{ route('product.show', $flashSaleProduct->id) }}" class="inline-flex items-center justify-center px-8 py-4 bg-white text-red-900 text-xs font-bold uppercase tracking-widest hover:bg-gray-100 transition-colors">
            Shop Flash Sale
        </a>
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

<!-- Featured Products - Museum Gallery Style -->
<section class="py-12 md:py-16 bg-gray-50 dark:bg-onyx-800/50 transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-6">
            <div>
                <p class="text-[10px] uppercase tracking-[0.3em] text-gray-500 mb-4">Handpicked Selections</p>
                <h2 class="text-4xl font-black uppercase tracking-tighter text-black dark:text-white">
                    Curated Pieces
                </h2>
            </div>
            <a href="{{ route('catalog') }}" class="group inline-flex items-center gap-3 text-xs font-bold uppercase tracking-widest text-black dark:text-white pb-2 border-b-2 border-black dark:border-white hover:text-gray-500 dark:hover:text-gray-400 hover:border-gray-500 transition-colors">
                View Entire Collection
                <i data-lucide="arrow-right" class="w-4 h-4 transform group-hover:translate-x-1 transition-transform"></i>
            </a>
        </div>

        @if(isset($featuredProducts) && $featuredProducts->count() > 0)
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-12">
                @foreach($featuredProducts as $product)
                    @include('components.product-card', ['product' => $product])
                @endforeach
            </div>
        @else
            <div class="text-center py-32 border border-dashed border-gray-300 dark:border-gray-700">
                <p class="text-xs uppercase tracking-widest text-gray-500">New collection dropping soon.</p>
            </div>
        @endif
    </div>
</section>

<!-- Brand Story - Split Layout Elegance -->
<section class="bg-black text-white relative overflow-hidden">
    <div class="grid lg:grid-cols-2">
        <div class="relative h-[50vh] lg:h-auto">
            <img
                src="{{ $brandProfile->logo ?? 'https://images.pexels.com/photos/7679720/pexels-photo-7679720.jpeg?auto=compress&cs=tinysrgb&w=1200' }}"
                alt="Brand Story"
                class="absolute inset-0 w-full h-full object-cover opacity-70 grayscale"
            />
            <div class="absolute inset-0 bg-black/30"></div>
        </div>
        <div class="p-12 md:p-24 lg:p-32 flex flex-col justify-center">
            <p class="text-[10px] uppercase tracking-[0.3em] text-gray-400 mb-6">The Philosophy</p>
            <h2 class="text-4xl md:text-6xl font-black mb-10 uppercase leading-none tracking-tighter">
                Elegance in<br /><span class="font-thin">Simplicity</span>
            </h2>
            <div class="w-12 h-1 bg-white mb-10"></div>
            <p class="text-gray-300 mb-8 leading-relaxed font-light text-lg">
                {{ $brandProfile->story ?? 'Berakar dari Jogja, HIGH FIVE mendefinisikan ulang makna fashion premium. Kami memotong hal-hal rumit untuk fokus pada apa yang benar-benar penting: kualitas jahitan, kenyamanan bahan, dan siluet yang tak lekang oleh waktu.' }}
            </p>
            <p class="text-gray-500 leading-relaxed font-light text-sm">
                {{ $brandProfile->vision ?? 'Bukan sekadar pakaian, melainkan investasi untuk rasa percaya diri Anda setiap harinya.' }}
            </p>
        </div>
    </div>
</section>

<!-- Newsletter - Minimalist Block -->
<section class="py-12 md:py-16 bg-white dark:bg-onyx-800 transition-colors">
    <div class="max-w-2xl mx-auto px-4 text-center">
        <h2 class="text-3xl font-black uppercase tracking-tighter mb-4 text-black dark:text-white">
            Join The Club
        </h2>
        <p class="text-gray-500 dark:text-gray-400 mb-10 text-sm font-light">
            Dapatkan akses awal ke koleksi terbaru, penawaran eksklusif, dan inspirasi gaya langsung ke kotak masuk Anda.
        </p>
        <form class="flex flex-col sm:flex-row relative">
            <input
                type="email"
                placeholder="EMAIL ADDRESS"
                class="w-full px-0 py-4 bg-transparent border-0 border-b-2 border-gray-200 dark:border-gray-700 focus:ring-0 focus:border-black dark:focus:border-white text-center sm:text-left text-sm uppercase tracking-widest outline-none transition-colors dark:text-white"
                required
            />
            <button type="submit" class="mt-6 sm:mt-0 sm:absolute sm:right-0 sm:bottom-0 sm:py-4 text-xs font-bold uppercase tracking-[0.2em] text-black dark:text-white hover:text-gray-500 transition-colors">
                Subscribe
            </button>
        </form>
    </div>
</section>
@endsection
