@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-black uppercase tracking-tight text-primary-900 mb-8">Checkout</h1>

    <div class="lg:grid lg:grid-cols-12 lg:gap-8">
        <div class="lg:col-span-7">
            <form id="checkout-form" action="{{ route('checkout.store') }}" method="POST" class="bg-white border border-primary-200 p-6 sm:p-8">
                @csrf
                
                <h2 class="text-lg font-bold uppercase tracking-widest mb-6 border-b border-primary-200 pb-4">Informasi Pengiriman</h2>
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-widest text-primary-500 mb-2">
                            Nama Lengkap
                        </label>
                        <input type="text" value="{{ auth()->user()->name }}" class="input-field bg-primary-50" readonly />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-widest text-primary-500 mb-2">
                            Email
                        </label>
                        <input type="email" value="{{ auth()->user()->email }}" class="input-field bg-primary-50" readonly />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-widest text-primary-500 mb-2">
                            Nomor Telepon *
                        </label>
                        <input type="tel" name="phone" value="{{ old('phone', auth()->user()->phone) }}" class="input-field @error('phone') border-red-500 @enderror" placeholder="08xxxxxxxxxx" required />
                        @error('phone')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-widest text-primary-500 mb-2">
                            Alamat Lengkap *
                        </label>
                        <textarea name="address" rows="4" class="input-field @error('address') border-red-500 @enderror" placeholder="Nama Jalan, Gedung, No. Rumah, RT/RW, Kelurahan, Kecamatan, Kota, Provinsi, Kode Pos" required>{{ old('address', auth()->user()->address) }}</textarea>
                        @error('address')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </form>
        </div>

        <div class="lg:col-span-5 mt-8 lg:mt-0">
            <div class="bg-primary-50 border border-primary-200 p-6 sticky top-24">
                <h2 class="text-lg font-bold uppercase tracking-widest mb-6 border-b border-primary-200 pb-4">Pesanan Anda</h2>
                
                <div class="space-y-4 mb-6 max-h-64 overflow-y-auto pr-2">
                    @foreach($carts as $cart)
                        <div class="flex gap-4">
                            <div class="w-16 h-20 bg-primary-100 flex-shrink-0">
                                <img src="{{ $cart->variant->product->thumbnail }}" alt="{{ $cart->variant->product->name }}" class="w-full h-full object-cover" />
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-sm line-clamp-1">{{ $cart->variant->product->name }}</p>
                                <p class="text-xs text-primary-500 mt-1 uppercase tracking-widest">
                                    {{ $cart->variant->color }} / {{ $cart->variant->size }}
                                </p>
                                <p class="text-xs text-primary-600 mt-1">Qty: {{ $cart->quantity }}</p>
                            </div>
                            <p class="font-semibold text-sm">
                                {{ formatPrice($cart->variant->product->price * $cart->quantity) }}
                            </p>
                        </div>
                    @endforeach
                </div>
                
                <div class="space-y-3 text-sm mb-6 border-t border-primary-200 pt-4">
                    <div class="flex justify-between">
                        <span class="text-primary-600">Subtotal</span>
                        <span class="font-medium">{{ formatPrice($subtotal) }}</span>
                    </div>
                    @if(isset($discountAmount) && $discountAmount > 0)
                        <div class="flex justify-between text-primary-600">
                            <span>Diskon ({{ $appliedCoupon }})</span>
                            <span class="text-green-600">-Rp {{ number_format($discountAmount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-primary-600">
                        <span>Biaya Pengiriman</span>
                        <span>{{ $shippingCost == 0 ? 'Gratis' : 'Rp ' . number_format($shippingCost, 0, ',', '.') }}</span>
                    </div>
                    @if($shippingCost > 0 && 500000 - $subtotal > 0)
                        <div class="text-xs text-primary-500 text-right mt-1">
                            Tambah {{ formatPrice(500000 - $subtotal) }} lagi untuk gratis ongkir
                        </div>
                    @endif
                </div>

                <!-- Coupon Section -->
                <div class="border-t border-primary-200 pt-4 pb-4">
                    <label class="block text-sm font-medium text-primary-700 mb-2 uppercase tracking-wider text-xs">Punya Kode Promo?</label>
                    <form action="{{ route('checkout.coupon') }}" method="POST" class="flex gap-2">
                        @csrf
                        <input type="text" name="coupon_code" value="{{ $appliedCoupon ?? '' }}" placeholder="Masukkan kode..." class="flex-1 px-3 py-2 border border-primary-300 focus:border-black outline-none transition-colors uppercase" {{ $appliedCoupon ? 'readonly' : '' }}>
                        <button type="submit" class="bg-black text-white px-4 py-2 text-xs font-semibold uppercase tracking-widest hover:bg-primary-900 transition-colors" {{ $appliedCoupon ? 'disabled' : '' }}>
                            {{ $appliedCoupon ? 'Dipakai' : 'Pakai' }}
                        </button>
                    </form>
                    @if($appliedCoupon)
                        <p class="text-xs text-green-600 mt-2 font-medium">Kupon aktif! Diskon telah diterapkan.</p>
                    @endif
                </div>

                <div class="border-t border-primary-200 pt-4 space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="font-bold uppercase tracking-widest text-primary-900">Total</span>
                        <span class="text-xl font-bold text-primary-900">{{ formatPrice($total) }}</span>
                    </div>
                </div>
                
                <button type="submit" form="checkout-form" class="w-full btn-primary block text-center">
                    Buat Pesanan
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
