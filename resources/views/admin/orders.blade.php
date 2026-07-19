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
                            <td class="px-4 py-3">
                                <div x-data="{ copied: false }" class="flex items-center gap-2 group">
                                    <span class="text-sm font-medium">{{ $order->order_code }}</span>
                                    <button @click.prevent="navigator.clipboard.writeText('{{ $order->order_code }}'); copied = true; setTimeout(() => copied = false, 2000)" class="text-primary-400 hover:text-black transition-colors focus:outline-none" title="Salin ID">
                                        <span x-show="!copied" class="opacity-0 group-hover:opacity-100 transition-opacity"><i data-lucide="copy" class="w-4 h-4"></i></span>
                                        <span x-show="copied" style="display: none;"><i data-lucide="check" class="w-4 h-4 text-green-500"></i></span>
                                    </button>
                                </div>
                                @if($order->resi_number)
                                <div x-data="{ copiedResi: false }" class="flex items-center gap-2 group mt-1">
                                    <span class="text-xs text-primary-500 font-mono">{{ $order->resi_number }}</span>
                                    <button @click.prevent="navigator.clipboard.writeText('{{ $order->resi_number }}'); copiedResi = true; setTimeout(() => copiedResi = false, 2000)" class="text-primary-400 hover:text-black transition-colors focus:outline-none" title="Salin Resi">
                                        <span x-show="!copiedResi" class="opacity-0 group-hover:opacity-100 transition-opacity"><i data-lucide="copy" class="w-3.5 h-3.5"></i></span>
                                        <span x-show="copiedResi" style="display: none;"><i data-lucide="check" class="w-3.5 h-3.5 text-green-500"></i></span>
                                    </button>
                                </div>
                                @endif
                            </td>
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
                                    
                                    @if($nextStatus === 'shipped')
                                        <div x-data="{ openResiModal: false }" class="inline">
                                            <button @click="openResiModal = true" class="p-2 hover:bg-green-50 text-green-600 rounded transition-colors" title="{{ $nextLabel }}">
                                                <i data-lucide="{{ $nextIcon }}" class="w-4 h-4"></i>
                                            </button>
                                            
                                            <!-- Resi Modal -->
                                            <div x-show="openResiModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" x-cloak style="display: none;">
                                                <div @click.away="openResiModal = false" class="bg-white p-6 w-full max-w-md shadow-xl border border-primary-200 text-left">
                                                    <div class="flex justify-between items-center mb-4">
                                                        <h2 class="text-lg font-bold">Kirim Pesanan</h2>
                                                        <button @click="openResiModal = false" class="text-primary-500 hover:text-black">
                                                            <i data-lucide="x" class="w-5 h-5"></i>
                                                        </button>
                                                    </div>
                                                    <div class="mb-4">
                                                        <p class="text-sm text-primary-600">Pelanggan memilih ekspedisi:</p>
                                                        <p class="font-bold text-black">{{ $order->shipping_courier ?? 'Standar' }} - {{ $order->shipping_service ?? 'Reguler' }}</p>
                                                    </div>
                                                    <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" class="space-y-4">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="shipped">
                                                        <div>
                                                            <label class="block text-sm font-medium text-primary-700 mb-1">Nomor Resi Pengiriman</label>
                                                            <input type="text" name="resi_number" required class="w-full px-3 py-2 border border-primary-300 focus:border-black outline-none transition-colors bg-white" placeholder="Masukkan nomor resi...">
                                                        </div>
                                                        <div class="pt-4 flex justify-end gap-2">
                                                            <button type="button" @click="openResiModal = false" class="px-4 py-2 border border-primary-300 text-sm font-medium hover:bg-primary-50">Batal</button>
                                                            <button type="submit" class="bg-primary-900 text-white px-4 py-2 text-sm font-medium hover:bg-black">Simpan & Kirim</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @elseif($nextStatus)
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
