@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-black uppercase tracking-tight text-primary-900 mb-8">Keranjang Belanja</h1>

    @if($carts->isEmpty())
        <div class="text-center py-16 bg-white border border-primary-200">
            <i data-lucide="shopping-bag" class="w-16 h-16 mx-auto text-primary-300 mb-4"></i>
            <p class="text-primary-600 mb-6">Keranjang belanja Anda masih kosong</p>
            <a href="{{ route('catalog') }}" class="btn-primary">
                Mulai Belanja
            </a>
        </div>
    @else
        <div class="lg:grid lg:grid-cols-12 lg:gap-8">
            <div class="lg:col-span-8">
                <div class="bg-white border border-primary-200">
                    <ul class="divide-y divide-primary-100">
                        @foreach($carts as $cart)
                            <li class="p-6 flex flex-col sm:flex-row gap-6">
                                <div class="w-24 h-32 bg-primary-100 flex-shrink-0">
                                    <img src="{{ $cart->variant->product->thumbnail }}" alt="{{ $cart->variant->product->name }}" class="w-full h-full object-cover" />
                                </div>
                                <div class="flex-1 flex flex-col">
                                    <div class="flex justify-between">
                                        <div>
                                            <a href="{{ route('product.show', $cart->variant->product->id) }}" class="font-medium text-lg hover:underline text-primary-900">
                                                {{ $cart->variant->product->name }}
                                            </a>
                                            <p class="text-sm text-primary-500 mt-1 uppercase tracking-widest">
                                                {{ $cart->variant->color }} / {{ $cart->variant->size }}
                                            </p>
                                        </div>
                                        <p class="font-semibold text-primary-900">
                                            {{ formatPrice($cart->variant->product->price * $cart->quantity) }}
                                        </p>
                                    </div>
                                    
                                    <div class="mt-auto pt-4 flex items-center justify-between">
                                        <form action="{{ route('cart.update', $cart->id) }}" method="POST" class="flex border border-primary-300">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button" onclick="var el=this.nextElementSibling; if(el.value > 1) { el.value--; this.form.submit(); }" class="px-3 py-1 text-primary-600 hover:bg-primary-100">-</button>
                                            <input type="number" name="quantity" value="{{ $cart->quantity }}" min="1" max="{{ $cart->variant->stock }}" class="w-12 text-center py-1 appearance-none outline-none text-sm font-medium border-x border-primary-300 text-black" onchange="this.form.submit()">
                                            <button type="button" onclick="var el=this.previousElementSibling; if(el.value < {{ $cart->variant->stock }}) { el.value++; this.form.submit(); }" class="px-3 py-1 text-primary-600 hover:bg-primary-100">+</button>
                                        </form>
                                        
                                        <form action="{{ route('cart.remove', $cart->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-sm text-primary-500 hover:text-red-600 flex items-center gap-1 uppercase tracking-widest font-semibold">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="lg:col-span-4 mt-8 lg:mt-0">
                <div class="bg-primary-50 border border-primary-200 p-6 sticky top-24">
                    <h2 class="text-lg font-bold uppercase tracking-widest mb-6 border-b border-primary-200 pb-4">Ringkasan Belanja</h2>
                    
                    @php
                        $subtotal = $carts->sum(function($cart) { return $cart->variant->product->price * $cart->quantity; });
                    @endphp

                    <div class="space-y-4 text-sm mb-6">
                        <div class="flex justify-between">
                            <span class="text-primary-600">Subtotal</span>
                            <span class="font-medium">{{ formatPrice($subtotal) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-primary-600">Pengiriman</span>
                            <span class="font-medium text-primary-500">Dihitung saat checkout</span>
                        </div>
                    </div>
                    
                    <div class="border-t border-primary-200 pt-4 mb-6">
                        <div class="flex justify-between items-center">
                            <span class="font-bold uppercase tracking-widest text-primary-900">Estimasi Total</span>
                            <span class="text-xl font-bold text-primary-900">{{ formatPrice($subtotal) }}</span>
                        </div>
                    </div>
                    
                    <a href="{{ route('checkout.index') }}" class="w-full btn-primary block text-center">
                        Lanjut ke Pembayaran
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
