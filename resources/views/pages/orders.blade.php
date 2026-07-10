@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-3xl font-black uppercase tracking-tight text-primary-900">Pesanan Saya</h1>
    </div>

    <!-- Filter Status -->
    <div class="flex flex-wrap gap-2 mb-8 border-b border-primary-200 pb-4">
        @php
            $filters = [
                'all' => 'Semua',
                'pending' => 'Menunggu Pembayaran',
                'processing' => 'Diproses',
                'shipped' => 'Dikirim',
                'delivered' => 'Selesai',
                'cancelled' => 'Dibatalkan',
            ];
            $currentFilter = $filter ?? 'all';
        @endphp
        
        @foreach($filters as $key => $label)
            <a href="{{ route('orders.index', ['filter' => $key]) }}" 
               class="px-4 py-2 text-sm font-medium rounded-full border transition-colors whitespace-nowrap {{ $currentFilter === $key ? 'bg-primary-900 text-white border-primary-900 shadow-sm' : 'bg-white text-primary-600 border-primary-300 hover:border-primary-900 hover:text-primary-900' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    @if($orders->isEmpty())
        <div class="text-center py-16 bg-white border border-primary-200">
            <i data-lucide="package" class="w-16 h-16 mx-auto text-primary-300 mb-4"></i>
            <p class="text-primary-600 mb-6">Anda belum memiliki pesanan</p>
            <a href="{{ route('catalog') }}" class="btn-primary">
                Mulai Belanja
            </a>
        </div>
    @else
        <div class="space-y-6">
            @foreach($orders as $order)
                <div class="bg-white border border-primary-200 overflow-hidden">
                    <div class="bg-primary-50 px-6 py-4 border-b border-primary-200 flex flex-wrap items-center justify-between gap-4">
                        <div class="flex flex-wrap items-center gap-6">
                            <div>
                                <p class="text-xs uppercase tracking-widest text-primary-500 mb-1">Order ID</p>
                                <p class="font-semibold text-primary-900">{{ $order->order_code }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-widest text-primary-500 mb-1">Tanggal</p>
                                <p class="font-medium">{{ formatDate($order->created_at) }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-widest text-primary-500 mb-1">Total</p>
                                <p class="font-semibold text-primary-900">{{ formatPrice($order->total_price) }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            @include('components.status-badge', ['status' => $order->status])
                            @include('components.status-badge', ['status' => $order->payment->status ?? 'pending'])
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <div class="space-y-4 mb-6">
                            @foreach($order->items->take(2) as $item)
                                <div class="flex items-center gap-4">
                                    <div class="w-16 h-20 bg-primary-100 flex-shrink-0">
                                        <img src="{{ $item->variant->product->thumbnail }}" class="w-full h-full object-cover" />
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium text-sm">{{ $item->variant->product->name }}</p>
                                        <p class="text-xs text-primary-500 mt-1 uppercase tracking-widest">
                                            {{ $item->variant->color }} / {{ $item->variant->size }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-medium text-sm">Qty: {{ $item->quantity }}</p>
                                    </div>
                                </div>
                            @endforeach
                            
                            @if($order->items->count() > 2)
                                <p class="text-sm text-primary-500 italic">+ {{ $order->items->count() - 2 }} item lainnya</p>
                            @endif
                        </div>
                        
                        <div class="flex justify-end gap-2 border-t border-primary-100 pt-4">
                            @if(!in_array($order->status, ['pending', 'cancelled']))
                                <a href="{{ route('orders.show', $order->id) }}" class="bg-black text-white px-6 py-2 text-sm font-semibold uppercase tracking-widest hover:bg-primary-800 transition-colors">
                                    Ulas Produk
                                </a>
                            @endif
                            <a href="{{ route('orders.show', $order->id) }}" class="btn-secondary py-2 px-6">
                                Detail Pesanan
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
