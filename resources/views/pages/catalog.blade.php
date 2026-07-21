@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumbs & Title -->
    <div class="mb-8">
        <nav class="flex text-sm text-primary-500 mb-4" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2">
                <li><a href="{{ route('home') }}" class="hover:text-primary-900">Home</a></li>
                <li><span class="mx-2">/</span></li>
                <li class="text-primary-900 font-medium" aria-current="page">Catalog</li>
            </ol>
        </nav>
        
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl md:text-4xl font-black uppercase tracking-tight text-primary-900 dark:text-white">
                    @if(request('category') && request('category') !== 'all')
                        @php
                            $cat = \App\Models\Category::where('slug', request('category'))->first();
                            $catName = $cat ? $cat->name : str_replace('-', ' ', request('category'));
                        @endphp
                        Koleksi {{ ucwords($catName) }}
                    @else
                        Semua Produk
                    @endif
                </h1>
                <p class="text-primary-500 mt-2">Menampilkan {{ $products->count() }} produk</p>
            </div>
            
            <div class="flex items-center gap-4">
                <form action="{{ route('catalog') }}" method="GET" class="flex items-center gap-4" id="sortForm">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    @if(request('q'))
                        <input type="hidden" name="q" value="{{ request('q') }}">
                    @endif
                    @if(request('min_price'))
                        <input type="hidden" name="min_price" value="{{ request('min_price') }}">
                    @endif
                    @if(request('max_price'))
                        <input type="hidden" name="max_price" value="{{ request('max_price') }}">
                    @endif
                    
                    <div class="relative">
                        <select name="sort" onchange="document.getElementById('sortForm').submit()" class="appearance-none bg-white dark:bg-onyx-900 border border-primary-300 dark:border-onyx-700 py-2 pl-4 pr-10 text-sm focus:outline-none focus:border-black dark:focus:border-white rounded-none cursor-pointer text-black dark:text-white">
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                            <option value="price-asc" {{ request('sort') == 'price-asc' ? 'selected' : '' }}>Harga: Rendah ke Tinggi</option>
                            <option value="price-desc" {{ request('sort') == 'price-desc' ? 'selected' : '' }}>Harga: Tinggi ke Rendah</option>
                        </select>
                        <i data-lucide="chevron-down" class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-primary-500 dark:text-gray-400 pointer-events-none"></i>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sidebar Filters -->
        <div class="w-full lg:w-1/4 flex-shrink-0">
            <div class="bg-white dark:bg-onyx-900 border border-primary-200 dark:border-onyx-700 p-6 sticky top-28 transition-colors">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-bold uppercase tracking-widest text-black dark:text-white">Filter</h2>
                    <a href="{{ route('catalog') }}" class="text-xs text-primary-500 dark:text-gray-400 hover:text-black dark:hover:text-white hover:underline transition-colors">Reset</a>
                </div>
                
                <form id="filterForm" action="{{ route('catalog') }}" method="GET" x-data x-on:change="$el.submit()">
                    <!-- Preserve search and sort -->
                    @if(request('q'))
                        <input type="hidden" name="q" value="{{ request('q') }}">
                    @endif
                    @if(request('sort'))
                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                    @endif

                    <!-- Category Filter -->
                    <div class="mb-6">
                        <h3 class="text-xs font-semibold text-primary-900 dark:text-white uppercase mb-3">Kategori</h3>
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" name="category" value="all" {{ request('category', 'all') === 'all' ? 'checked' : '' }} class="w-4 h-4 text-black focus:ring-black border-primary-300 dark:border-onyx-600 dark:bg-onyx-800">
                                <span class="text-sm text-primary-600 dark:text-gray-400 group-hover:text-black dark:group-hover:text-white transition-colors">Semua Produk</span>
                            </label>
                            @php
                                $categories = \App\Models\Category::all();
                            @endphp
                            @foreach($categories as $cat)
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="radio" name="category" value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'checked' : '' }} class="w-4 h-4 text-black focus:ring-black border-primary-300 dark:border-onyx-600 dark:bg-onyx-800">
                                    <span class="text-sm text-primary-600 dark:text-gray-400 group-hover:text-black dark:group-hover:text-white transition-colors">{{ $cat->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Price Filter -->
                    <div>
                        <h3 class="text-xs font-semibold text-primary-900 dark:text-white uppercase mb-3">Rentang Harga</h3>
                        <div class="flex items-center gap-2">
                            <div class="relative flex-1">
                                <span class="absolute left-2 top-1/2 -translate-y-1/2 text-xs text-primary-500 dark:text-gray-400">Rp</span>
                                <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" class="w-full pl-7 pr-2 py-1.5 text-sm border border-primary-300 dark:border-onyx-600 focus:outline-none focus:border-black dark:focus:border-white bg-white dark:bg-onyx-800 text-black dark:text-white transition-colors" min="0">
                            </div>
                            <span class="text-primary-400 dark:text-gray-500">-</span>
                            <div class="relative flex-1">
                                <span class="absolute left-2 top-1/2 -translate-y-1/2 text-xs text-primary-500 dark:text-gray-400">Rp</span>
                                <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" class="w-full pl-7 pr-2 py-1.5 text-sm border border-primary-300 dark:border-onyx-600 focus:outline-none focus:border-black dark:focus:border-white bg-white dark:bg-onyx-800 text-black dark:text-white transition-colors" min="0">
                            </div>
                        </div>
                        <button type="submit" class="w-full mt-3 btn-secondary text-xs py-1.5">Terapkan Harga</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Product Grid Content -->
        <div class="w-full lg:w-3/4">
            <!-- Active Search Filter -->
            @if(request('q') || request('min_price') || request('max_price'))
                <div class="flex flex-wrap items-center gap-2 mb-6">
                    <span class="text-sm text-primary-600">Filter Aktif:</span>
                    
                    @if(request('q'))
                        <span class="inline-flex items-center gap-2 px-3 py-1 bg-primary-100 text-sm font-medium">
                            Pencarian: "{{ request('q') }}"
                            <a href="{{ route('catalog', array_merge(request()->except(['q', 'page']))) }}" class="hover:text-red-500"><i data-lucide="x" class="w-3 h-3"></i></a>
                        </span>
                    @endif
                    
                    @if(request('min_price') || request('max_price'))
                        <span class="inline-flex items-center gap-2 px-3 py-1 bg-primary-100 text-sm font-medium">
                            Harga: {{ request('min_price') ? 'Rp ' . number_format(request('min_price'),0,',','.') : '0' }} - {{ request('max_price') ? 'Rp ' . number_format(request('max_price'),0,',','.') : 'Max' }}
                            <a href="{{ route('catalog', array_merge(request()->except(['min_price', 'max_price', 'page']))) }}" class="hover:text-red-500"><i data-lucide="x" class="w-3 h-3"></i></a>
                        </span>
                    @endif
                </div>
            @endif

            @if($products->isEmpty())
                <div class="text-center py-24 bg-primary-50 dark:bg-onyx-900 border border-primary-200 dark:border-onyx-700 transition-colors">
                    <i data-lucide="search" class="w-12 h-12 mx-auto text-primary-300 dark:text-gray-600 mb-4"></i>
                    <h3 class="text-lg font-medium text-primary-900 dark:text-white mb-2">Produk tidak ditemukan</h3>
                    <p class="text-primary-500 dark:text-gray-400 mb-6">Maaf, kami tidak dapat menemukan produk yang sesuai dengan filter Anda.</p>
                    <a href="{{ route('catalog') }}" class="btn-primary dark:bg-white dark:text-black dark:hover:bg-gray-200 transition-colors">Reset Filter</a>
                </div>
            @else
                <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                    @foreach($products as $product)
                        @include('components.product-card', ['product' => $product])
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="mt-12 flex justify-center">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
