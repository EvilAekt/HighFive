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
                <h1 class="text-3xl md:text-4xl font-black uppercase tracking-tight text-primary-900">
                    @if(request('category') && request('category') !== 'all')
                        {{ ucfirst(request('category')) }}
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
                    
                    <div class="relative">
                        <select name="sort" onchange="document.getElementById('sortForm').submit()" class="appearance-none bg-white border border-primary-300 py-2 pl-4 pr-10 text-sm focus:outline-none focus:border-black rounded-none cursor-pointer">
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                            <option value="price-asc" {{ request('sort') == 'price-asc' ? 'selected' : '' }}>Harga: Rendah ke Tinggi</option>
                            <option value="price-desc" {{ request('sort') == 'price-desc' ? 'selected' : '' }}>Harga: Tinggi ke Rendah</option>
                        </select>
                        <i data-lucide="chevron-down" class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-primary-500 pointer-events-none"></i>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Active Search Filter -->
    @if(request('q'))
        <div class="flex items-center gap-2 mb-8">
            <span class="text-sm text-primary-600">Hasil pencarian untuk:</span>
            <span class="inline-flex items-center gap-2 px-3 py-1 bg-primary-100 text-sm font-medium">
                "{{ request('q') }}"
                <a href="{{ route('catalog', ['category' => request('category'), 'sort' => request('sort')]) }}" class="hover:text-red-500">
                    <i data-lucide="x" class="w-3 h-3"></i>
                </a>
            </span>
        </div>
    @endif

    <!-- Product Grid -->
    @if($products->isEmpty())
        <div class="text-center py-24 bg-primary-50 border border-primary-200">
            <i data-lucide="search" class="w-12 h-12 mx-auto text-primary-300 mb-4"></i>
            <h3 class="text-lg font-medium text-primary-900 mb-2">Produk tidak ditemukan</h3>
            <p class="text-primary-500 mb-6">Maaf, kami tidak dapat menemukan produk yang sesuai dengan pencarian Anda.</p>
            <a href="{{ route('catalog') }}" class="btn-primary">
                Lihat Semua Produk
            </a>
        </div>
    @else
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
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
@endsection
