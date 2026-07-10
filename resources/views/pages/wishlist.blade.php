@extends('layouts.app')

@section('title', 'Wishlist Saya - HIGH FIVE')

@section('content')
<div class="min-h-screen bg-primary-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-primary-900">Wishlist Saya</h1>
            @if($wishlists->count() > 0)
                <span class="text-sm text-primary-500">{{ $wishlists->count() }} item</span>
            @endif
        </div>

        @if($wishlists->isEmpty())
            <div class="text-center py-20 bg-white border border-primary-200">
                <i data-lucide="heart" class="w-16 h-16 mx-auto text-primary-300 mb-4"></i>
                <h2 class="text-lg font-semibold text-primary-700 mb-2">Wishlist kosong</h2>
                <p class="text-primary-500 mb-6 text-sm">Belum ada produk yang ditambahkan ke wishlist.</p>
                <a href="{{ route('catalog') }}" class="btn-primary">
                    Mulai Belanja
                </a>
            </div>
        @else
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($wishlists as $wishlist)
                    @include('components.product-card', ['product' => $wishlist->product])
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
