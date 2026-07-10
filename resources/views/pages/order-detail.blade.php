@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <a href="{{ route('orders.index') }}" class="text-sm font-semibold uppercase tracking-widest text-primary-600 hover:text-black mb-8 inline-flex items-center gap-2">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Pesanan
    </a>

    <div class="bg-white border border-primary-200 p-6 sm:p-8 mb-6">
        <div class="flex flex-wrap items-start justify-between gap-4 mb-8 pb-8 border-b border-primary-200">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-primary-500 mb-1">Order Code</p>
                <h1 class="text-2xl font-bold text-primary-900">{{ $order->order_code }}</h1>
                <p class="text-sm text-primary-600 mt-2">{{ formatDate($order->created_at) }}</p>
            </div>
            <div class="flex flex-col items-end gap-2">
                @include('components.status-badge', ['status' => $order->status])
                @if($order->status === 'cancelled')
                    <p class="text-xs text-red-600 mt-1">Pesanan ini telah dibatalkan</p>
                @endif
            </div>
        </div>

        @if($order->payment)
            <div class="mb-8 pb-8 border-b border-primary-200">
                <div class="flex items-center gap-2 mb-4">
                    <i data-lucide="credit-card" class="w-5 h-5 text-primary-900"></i>
                    <h2 class="text-lg font-bold uppercase tracking-widest">Pembayaran</h2>
                </div>

                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm text-primary-600 uppercase tracking-widest">Status Pembayaran</span>
                    @include('components.status-badge', ['status' => $order->payment->status])
                </div>

                @if($order->payment->status === 'pending' && $order->payment->snap_token)
                    <button id="pay-button" class="w-full btn-primary mt-4">
                        Bayar Sekarang
                    </button>
                    
                    @push('scripts')
                    <script type="text/javascript">
                        var payButton = document.getElementById('pay-button');
                        if (payButton) {
                            payButton.addEventListener('click', function () {
                                window.snap.pay('{{ $order->payment->snap_token }}', {
                                    onSuccess: function(result){
                                        window.location.reload();
                                    },
                                    onPending: function(result){
                                        alert("Waiting your payment!");
                                    },
                                    onError: function(result){
                                        alert("Payment failed!");
                                    },
                                    onClose: function(){
                                        console.log('Payment popup closed');
                                    }
                                });
                            });
                        }
                        
                        // Auto open payment modal if redirected from checkout
                        @if(request('payment') === 'true')
                            setTimeout(() => {
                                if(payButton) payButton.click();
                            }, 500);
                        @endif
                    </script>
                    @endpush
                @endif

                @if($order->payment->status === 'paid' && $order->payment->paid_at)
                    <div class="mt-4 p-4 bg-green-50 border border-green-200 flex flex-col gap-1">
                        <p class="text-sm text-green-800">
                            <strong>Dibayar pada:</strong> {{ formatDate($order->payment->paid_at) }}
                        </p>
                        @if($order->payment->payment_type)
                            <p class="text-sm text-green-800">
                                <strong>Metode:</strong> {{ strtoupper(str_replace('_', ' ', $order->payment->payment_type)) }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>
        @endif

        <div class="mb-8 pb-8 border-b border-primary-200">
            <h2 class="text-lg font-bold uppercase tracking-widest mb-4">Item Pesanan</h2>
            <div class="space-y-4">
                @foreach($order->items as $item)
                    <div class="flex flex-col sm:flex-row sm:items-center gap-4 p-4 border border-primary-100 {{ $loop->even ? 'bg-primary-50' : 'bg-white' }}" x-data="{ openReviewModal: false }">
                        <div class="w-20 h-24 bg-primary-100 flex-shrink-0">
                            <img src="{{ $item->variant->product->thumbnail }}" class="w-full h-full object-cover" />
                        </div>
                        <div class="flex-1">
                            <a href="{{ route('product.show', $item->variant->product->id) }}" class="font-bold text-sm hover:underline text-primary-900">
                                {{ $item->variant->product->name }}
                            </a>
                            <p class="text-xs text-primary-500 mt-1 uppercase tracking-widest">
                                {{ $item->variant->color }} / {{ $item->variant->size }}
                            </p>
                            <p class="text-xs text-primary-600 mt-1 font-medium">Qty: {{ $item->quantity }}</p>
                        </div>
                        <div class="text-right sm:text-right w-full sm:w-auto">
                            <p class="font-bold text-sm">{{ formatPrice($item->price * $item->quantity) }}</p>
                            <p class="text-xs text-primary-500 mt-1">{{ formatPrice($item->price) }} / item</p>
                            
                            @if(!in_array($order->status, ['pending', 'cancelled']))
                                @php
                                    $hasReviewed = \App\Models\Review::where('order_id', $order->id)->where('product_id', $item->variant->product->id)->exists();
                                @endphp
                                @if(!$hasReviewed)
                                    <button @click="openReviewModal = true" class="mt-3 text-xs font-semibold bg-black text-white px-3 py-1.5 uppercase tracking-widest hover:bg-primary-800 transition-colors inline-block">
                                        Beri Ulasan
                                    </button>
                                @else
                                    <span class="mt-3 inline-block text-xs font-semibold text-green-600 bg-green-50 px-3 py-1.5 border border-green-200">Diulas</span>
                                @endif
                            @endif
                        </div>

                        <!-- Review Modal -->
                        <div x-show="openReviewModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" x-cloak style="display: none;">
                            <div @click.away="openReviewModal = false" class="bg-white p-6 w-full max-w-md shadow-xl border border-primary-200 text-left">
                                <div class="flex justify-between items-center mb-4">
                                    <h2 class="text-lg font-bold">Ulas Produk</h2>
                                    <button @click="openReviewModal = false" class="text-primary-500 hover:text-black">
                                        <i data-lucide="x" class="w-5 h-5"></i>
                                    </button>
                                </div>
                                <form action="{{ route('reviews.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                    @csrf
                                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                                    <input type="hidden" name="product_id" value="{{ $item->variant->product->id }}">
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-primary-700 mb-1">Penilaian (Bintang)</label>
                                        <select name="rating" required class="w-full px-3 py-2 border border-primary-300 focus:border-black outline-none transition-colors bg-white">
                                            <option value="5">⭐⭐⭐⭐⭐ Sangat Bagus</option>
                                            <option value="4">⭐⭐⭐⭐ Bagus</option>
                                            <option value="3">⭐⭐⭐ Biasa Saja</option>
                                            <option value="2">⭐⭐ Kurang Bagus</option>
                                            <option value="1">⭐ Sangat Buruk</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-primary-700 mb-1">Foto Ulasan (Opsional)</label>
                                        <input type="file" name="image" accept="image/*" class="w-full text-sm text-primary-700 file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 border border-primary-300 transition-colors">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-primary-700 mb-1">Video Ulasan (Opsional)</label>
                                        <input type="file" name="video" accept="video/mp4,video/quicktime,video/x-msvideo" class="w-full text-sm text-primary-700 file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 border border-primary-300 transition-colors">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-primary-700 mb-1">Komentar</label>
                                        <textarea name="comment" rows="3" class="w-full px-3 py-2 border border-primary-300 focus:border-black outline-none transition-colors" placeholder="Tulis pengalaman Anda tentang produk ini..."></textarea>
                                    </div>
                                    <div class="pt-4 flex justify-end gap-2">
                                        <button type="button" @click="openReviewModal = false" class="px-4 py-2 border border-primary-300 text-sm font-medium hover:bg-primary-50">Batal</button>
                                        <button type="submit" class="bg-primary-900 text-white px-4 py-2 text-sm font-medium hover:bg-black">Kirim Ulasan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mb-8 pb-8 border-b border-primary-200">
            <h2 class="text-lg font-bold uppercase tracking-widest mb-4">Alamat Pengiriman</h2>
            <p class="text-sm text-primary-600 whitespace-pre-line leading-relaxed">
                {{ $order->shipping_address }}
            </p>
        </div>

        <div>
            <h2 class="text-lg font-bold uppercase tracking-widest mb-4">Ringkasan Pembayaran</h2>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-primary-600 uppercase tracking-widest">Subtotal</span>
                    <span class="font-medium">{{ formatPrice($order->total_price - $order->shipping_cost + $order->discount_amount) }}</span>
                </div>
                @if($order->discount_amount > 0)
                <div class="flex justify-between">
                    <span class="text-primary-600 uppercase tracking-widest">Diskon ({{ $order->coupon_code }})</span>
                    <span class="font-medium text-green-600">-{{ formatPrice($order->discount_amount) }}</span>
                </div>
                @endif
                <div class="flex justify-between">
                    <span class="text-primary-600 uppercase tracking-widest">Ongkos Kirim</span>
                    <span class="font-medium">{{ formatPrice($order->shipping_cost) }}</span>
                </div>
                <div class="border-t border-primary-200 pt-3 mt-3">
                    <div class="flex justify-between items-center">
                        <span class="font-bold uppercase tracking-widest text-primary-900 text-base">Total</span>
                        <span class="text-xl font-bold text-primary-900">{{ formatPrice($order->total_price) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
