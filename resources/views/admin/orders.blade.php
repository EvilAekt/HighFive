@extends('layouts.admin')

@section('content')
<div x-data="{ filter: '{{ request('filter', 'all') }}' }">
    <h1 class="text-2xl font-bold text-primary-900 mb-6">Kelola Pesanan</h1>

    <!-- Filter -->
    <div class="flex flex-wrap gap-2 mb-6">
        @php
            $filters = [
                'all' => 'Semua',
                'pending' => 'Menunggu',
                'processing' => 'Diproses',
                'shipped' => 'Dikirim',
                'delivered' => 'Selesai',
                'cancelled' => 'Dibatalkan',
            ];
        @endphp
        
        @foreach($filters as $key => $label)
            <a href="{{ route('admin.orders.index', ['filter' => $key]) }}" 
               class="px-4 py-2 text-sm font-medium border transition-colors {{ request('filter', 'all') === $key ? 'bg-primary-900 text-white border-primary-900' : 'bg-white text-primary-600 border-primary-300 hover:border-primary-900' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="bg-white border border-primary-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-primary-50">
                    <tr class="text-left text-sm text-primary-600 border-b border-primary-200">
                        <th class="px-4 py-3 font-medium">Order ID</th>
                        <th class="px-4 py-3 font-medium">Pelanggan</th>
                        <th class="px-4 py-3 font-medium">Tanggal</th>
                        <th class="px-4 py-3 font-medium">Items</th>
                        <th class="px-4 py-3 font-medium">Total</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr class="border-b border-primary-100 last:border-0 hover:bg-primary-50">
                            <td class="px-4 py-3 text-sm font-medium">{{ $order->order_code }}</td>
                            <td class="px-4 py-3">
                                <div>
                                    <p class="text-sm font-medium">{{ $order->user->name ?? 'N/A' }}</p>
                                    <p class="text-xs text-primary-500">{{ $order->user->phone ?? 'No phone' }}</p>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm">{{ formatDate($order->created_at) }}</td>
                            <td class="px-4 py-3 text-sm">
                                {{ $order->items->sum('quantity') }} items
                            </td>
                            <td class="px-4 py-3 text-sm font-medium">{{ formatPrice($order->total_price) }}</td>
                            <td class="px-4 py-3">
                                @include('components.status-badge', ['status' => $order->status, 'size' => 'sm'])
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    @php
                                        $nextStatus = match($order->status) {
                                            'processing' => 'shipped',
                                            'shipped' => 'delivered',
                                            default => null
                                        };
                                        $nextIcon = match($order->status) {
                                            'processing' => 'truck',
                                            'shipped' => 'check-circle',
                                            default => 'check'
                                        };
                                        $nextLabel = match($order->status) {
                                            'processing' => 'Kirim Pesanan',
                                            'shipped' => 'Selesaikan Pesanan',
                                            default => ''
                                        };
                                    @endphp
                                    
                                    @if($nextStatus)
                                        <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="{{ $nextStatus }}">
                                            <button type="submit" class="p-2 hover:bg-green-50 text-green-600 rounded transition-colors" title="{{ $nextLabel }}">
                                                <i data-lucide="{{ $nextIcon }}" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <a href="{{ route('orders.show', $order->id) }}" target="_blank" class="p-2 hover:bg-primary-100 rounded transition-colors" title="Lihat Detail">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-primary-500">Tidak ada pesanan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
