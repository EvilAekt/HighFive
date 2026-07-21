@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="checkoutData()">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
        <h1 class="text-3xl font-black uppercase tracking-tight text-primary-900 dark:text-white">Checkout</h1>
        <a href="{{ route('cart.index') }}" class="inline-flex items-center gap-2 text-sm font-bold uppercase tracking-widest text-gray-500 hover:text-black dark:text-gray-400 dark:hover:text-white transition-colors group">
            <i data-lucide="arrow-left" class="w-4 h-4 group-hover:-translate-x-1 transition-transform"></i>
            Kembali ke Keranjang
        </a>
    </div>
    <div class="lg:grid lg:grid-cols-12 lg:gap-8">
        <div class="lg:col-span-7">
            <form id="checkout-form" action="{{ route('checkout.store') }}" method="POST" class="bg-white dark:bg-onyx-900 border-4 border-black dark:border-white p-6 sm:p-8 shadow-[8px_8px_0_0_#000] dark:shadow-[8px_8px_0_0_#fff] mb-8 lg:mb-0 transition-all hover:-translate-y-1 hover:shadow-[12px_12px_0_0_#000] dark:hover:shadow-[12px_12px_0_0_#fff]">
                @csrf
                
                <h2 class="text-xl sm:text-2xl font-black uppercase tracking-widest mb-6 border-b-4 border-black dark:border-white pb-4 text-black dark:text-white">Informasi Pengiriman</h2>
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-widest text-primary-500 mb-2">
                            Nama Lengkap *
                        </label>
                        <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" class="input-field @error('name') border-red-500 @enderror bg-white" required />
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-widest text-primary-500 mb-2">
                            Email *
                        </label>
                        <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" class="input-field @error('email') border-red-500 @enderror bg-white" required />
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
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

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-widest text-primary-500 mb-2">Provinsi *</label>
                            <select x-model="selectedProvince" class="input-field w-full bg-white" required>
                                <option value="">Pilih Provinsi</option>
                                <template x-for="prov in provinces" :key="prov.province_id">
                                    <option :value="prov.province_id" x-text="prov.province"></option>
                                </template>
                            </select>
                            <input type="hidden" name="shipping_province" :value="provinces.find(p => p.province_id == selectedProvince)?.province || ''">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-widest text-primary-500 mb-2">Kota/Kabupaten *</label>
                            <select name="shipping_city_id" x-model="selectedCity" class="input-field w-full bg-white" :disabled="!selectedProvince" required>
                                <option value="">Pilih Kota/Kabupaten</option>
                                <template x-for="city in cities" :key="city.city_id">
                                    <option :value="city.city_id" x-text="city.type + ' ' + city.city_name"></option>
                                </template>
                            </select>
                            <input type="hidden" name="shipping_city" :value="(cities.find(c => c.city_id == selectedCity)?.type || '') + ' ' + (cities.find(c => c.city_id == selectedCity)?.city_name || '')">
                        </div>
                    </div>

                <h2 class="text-xl sm:text-2xl font-black uppercase tracking-widest mb-6 mt-12 border-b-4 border-black dark:border-white pb-4 text-black dark:text-white">Opsi Pengiriman</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-widest text-primary-500 mb-2">Kurir Pengiriman *</label>
                        <select name="shipping_courier" x-model="selectedCourier" class="input-field w-full bg-white" required>
                            <option value="">Pilih Kurir</option>
                            <option value="jne">JNE</option>
                            <option value="pos">POS Indonesia</option>
                            <option value="tiki">TIKI</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-widest text-primary-500 mb-2">Layanan *</label>
                        <select name="shipping_service" x-model="selectedService" class="input-field w-full bg-white" :disabled="costs.length === 0 || isLoadingCost" required>
                            <option value="">Pilih Layanan</option>
                            <template x-for="cost in costs" :key="cost.service">
                                <option :value="cost.service" x-text="cost.service + ' (' + cost.description + ') - ' + cost.cost[0].etd + ' hari'"></option>
                            </template>
                        </select>
                    </div>
                </div>
            </form>
        </div>

        <div class="lg:col-span-5 mt-8 lg:mt-0">
            <div class="bg-white dark:bg-onyx-900 border-4 border-black dark:border-white p-6 sm:p-8 sticky top-24 shadow-[8px_8px_0_0_#000] dark:shadow-[8px_8px_0_0_#fff] transition-all hover:-translate-y-1 hover:shadow-[12px_12px_0_0_#000] dark:hover:shadow-[12px_12px_0_0_#fff]">
                <div class="flex items-center justify-between mb-6 border-b-4 border-black dark:border-white pb-4">
                    <h2 class="text-xl sm:text-2xl font-black uppercase tracking-widest text-black dark:text-white">Pesanan Anda</h2>
                    <a href="{{ route('cart.index') }}" class="text-xs font-bold uppercase tracking-widest text-gray-500 hover:text-black dark:text-gray-400 dark:hover:text-white underline decoration-2 underline-offset-4">
                        Edit
                    </a>
                </div>
                
                <div class="space-y-4 mb-6 max-h-64 overflow-y-auto pr-2">
                    @foreach($carts as $cart)
                        <div class="flex gap-4 p-3 border-2 border-transparent hover:border-black dark:hover:border-white transition-colors bg-gray-50 dark:bg-onyx-800">
                            <div class="w-16 h-20 bg-gray-200 dark:bg-onyx-700 flex-shrink-0 border border-black dark:border-white">
                                <img src="{{ $cart->variant->product->thumbnail }}" alt="{{ $cart->variant->product->name }}" class="w-full h-full object-cover grayscale hover:grayscale-0 transition-all" />
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
                
                <div class="space-y-3 text-sm mb-6 border-t-4 border-black dark:border-white pt-6 mt-6">
                    <div class="flex justify-between font-bold">
                        <span class="text-primary-600">Subtotal</span>
                        <span class="font-medium" x-text="formatPrice(subtotal)"></span>
                    </div>
                    @if(isset($discountAmount) && $discountAmount > 0)
                        <div class="flex justify-between text-primary-600">
                            <span>Diskon ({{ $appliedCoupon }})</span>
                            <span class="text-green-600">-<span x-text="formatPrice(discount)"></span></span>
                        </div>
                    @endif
                    <div class="flex justify-between font-bold text-gray-600 dark:text-gray-400">
                        <span>Biaya Pengiriman <span x-show="isLoadingCost" class="text-[10px] animate-pulse text-black dark:text-white bg-yellow-300 dark:bg-yellow-600 px-1 ml-1">(Menghitung...)</span></span>
                        <span x-text="currentShippingCost == 0 ? 'Gratis' : formatPrice(currentShippingCost)"></span>
                    </div>
                </div>

                <!-- Coupon Section -->
                <div class="border-t-4 border-black dark:border-white pt-6 pb-6">
                    <label class="block text-sm font-black text-black dark:text-white mb-3 uppercase tracking-wider">Punya Kode Promo?</label>
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

                <div class="border-t-4 border-black dark:border-white pt-6 space-y-2">
                    <div class="flex justify-between items-center bg-black text-white dark:bg-white dark:text-black p-4 shadow-[4px_4px_0_0_#e5e7eb] dark:shadow-[4px_4px_0_0_#374151]">
                        <span class="font-black uppercase tracking-widest">Total</span>
                        <span class="text-2xl font-black" x-text="formatPrice(total)"></span>
                    </div>
                </div>
                
                <button type="submit" form="checkout-form" class="w-full btn-primary block text-center mt-6" :disabled="isLoadingCost || (costs.length > 0 && !selectedService)">
                    Buat Pesanan
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('checkoutData', () => ({
        subtotal: {{ $subtotal }},
        discount: {{ $discountAmount }},
        
        provinces: [],
        cities: [],
        costs: [],
        
        selectedProvince: '',
        selectedCity: '',
        selectedCourier: '',
        selectedService: '',
        
        isLoadingCost: false,
        
        init() {
            this.fetchProvinces();
            
            this.$watch('selectedProvince', value => {
                this.selectedCity = '';
                this.selectedService = '';
                this.costs = [];
                if (value) this.fetchCities(value);
            });
            
            this.$watch('selectedCity', value => {
                this.fetchCosts();
            });
            
            this.$watch('selectedCourier', value => {
                this.fetchCosts();
            });
        },
        
        async fetchProvinces() {
            try {
                const res = await fetch('/api/rajaongkir/provinces');
                this.provinces = await res.json();
            } catch (e) {
                console.error(e);
            }
        },
        
        async fetchCities(provinceId) {
            try {
                const res = await fetch(`/api/rajaongkir/cities/${provinceId}`);
                this.cities = await res.json();
            } catch (e) {
                console.error(e);
            }
        },
        
        async fetchCosts() {
            if (!this.selectedCity || !this.selectedCourier) return;
            
            this.isLoadingCost = true;
            this.selectedService = '';
            this.costs = [];
            
            try {
                const res = await fetch('/api/rajaongkir/cost', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({
                        destination: this.selectedCity,
                        courier: this.selectedCourier
                    })
                });
                this.costs = await res.json();
                
                if (this.costs.length > 0) {
                    this.selectedService = this.costs[0].service;
                }
            } catch (e) {
                console.error(e);
            } finally {
                this.isLoadingCost = false;
            }
        },
        
        get currentShippingCost() {
            if (!this.selectedService) return 0;
            const costObj = this.costs.find(c => c.service === this.selectedService);
            return costObj ? costObj.cost[0].value : 0;
        },
        
        get total() {
            return this.subtotal - this.discount + this.currentShippingCost;
        },
        
        formatPrice(amount) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount);
        }
    }))
})
</script>
@endsection
