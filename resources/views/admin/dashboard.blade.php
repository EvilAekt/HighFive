@extends('layouts.admin')

@section('content')
<div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <h1 class="text-2xl font-black uppercase tracking-[0.1em] text-black">Admin Overview</h1>
    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
        <!-- AI Toggle -->
        <div x-data="{ 
                active: {{ $aiActive ? 'true' : 'false' }},
                async toggle() {
                    try {
                        await fetch('{{ route('admin.chat.toggle', 'ai') }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'X-Requested-With': 'XMLHttpRequest' },
                            body: JSON.stringify({ active: this.active })
                        });
                    } catch(e) {}
                }
            }" class="flex items-center gap-3 bg-white border border-gray-200 px-3 py-2 w-full sm:w-auto">
            <label class="text-xs font-bold uppercase tracking-widest text-gray-500">Gemini AI</label>
            <div class="relative inline-block w-10 align-middle select-none transition duration-200 ease-in">
                <input type="checkbox" id="toggleAi" x-model="active" @change="toggle()" class="toggle-checkbox absolute block w-5 h-5 rounded-full bg-white border-2 border-gray-300 appearance-none cursor-pointer transition-transform duration-200 ease-in-out" :class="active ? 'translate-x-5 border-black' : 'translate-x-0'"/>
                <label for="toggleAi" class="toggle-label block overflow-hidden h-5 rounded-full cursor-pointer transition-colors duration-200 ease-in-out" :class="active ? 'bg-black' : 'bg-gray-300'"></label>
            </div>
            <span x-text="active ? 'ON' : 'OFF'" class="text-xs font-black w-6 text-center" :class="active ? 'text-black' : 'text-gray-400'"></span>
        </div>

        <!-- Bot Toggle -->
        <div x-data="{ 
                active: {{ $botActive ? 'true' : 'false' }},
                async toggle() {
                    try {
                        await fetch('{{ route('admin.chat.toggle', 'bot') }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'X-Requested-With': 'XMLHttpRequest' },
                            body: JSON.stringify({ active: this.active })
                        });
                    } catch(e) {}
                }
            }" class="flex items-center gap-3 bg-white border border-gray-200 px-3 py-2 w-full sm:w-auto">
            <label class="text-xs font-bold uppercase tracking-widest text-gray-500">Rule Bot</label>
            <div class="relative inline-block w-10 align-middle select-none transition duration-200 ease-in">
                <input type="checkbox" id="toggleBot" x-model="active" @change="toggle()" class="toggle-checkbox absolute block w-5 h-5 rounded-full bg-white border-2 border-gray-300 appearance-none cursor-pointer transition-transform duration-200 ease-in-out" :class="active ? 'translate-x-5 border-black' : 'translate-x-0'"/>
                <label for="toggleBot" class="toggle-label block overflow-hidden h-5 rounded-full cursor-pointer transition-colors duration-200 ease-in-out" :class="active ? 'bg-black' : 'bg-gray-300'"></label>
            </div>
            <span x-text="active ? 'ON' : 'OFF'" class="text-xs font-black w-6 text-center" :class="active ? 'text-black' : 'text-gray-400'"></span>
        </div>
    </div>
</div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
        <div class="bg-white p-6 border border-gray-200">
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total Pendapatan</p>
            <p class="text-2xl font-black text-black">{{ formatPrice($totalRevenue) }}</p>
        </div>
        
        <div class="bg-white p-6 border border-gray-200">
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total Pesanan</p>
            <p class="text-2xl font-black text-black">{{ $totalOrders }}</p>
        </div>

        <div class="bg-white p-6 border border-gray-200">
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total Produk</p>
            <p class="text-2xl font-black text-black">{{ $totalProducts }}</p>
        </div>
        
        <div class="bg-black p-6 border border-black text-white">
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1">Pelanggan Aktif</p>
            <p class="text-2xl font-black">{{ $totalCustomers }}</p>
        </div>
    </div>

    <!-- Charts & Tables -->
    <div class="grid lg:grid-cols-3 gap-6">
        
        <!-- Left Column: Chart & Low Stock -->
        <div class="lg:col-span-2 space-y-6">
            <!-- 7-Day Sales Chart -->
            <div class="bg-white border border-gray-200 p-6">
                <h2 class="text-sm font-bold uppercase tracking-widest mb-6">Penjualan 7 Hari</h2>
                <div class="relative h-64 w-full">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            <!-- Pending Orders -->
            <div class="bg-white border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-sm font-bold uppercase tracking-widest">Menunggu Diproses</h2>
                    <span class="bg-black text-white text-[10px] font-bold px-2 py-1 uppercase tracking-widest">{{ $pendingOrders->count() }} Baru</span>
                </div>
                
                @if($pendingOrders->count() > 0)
                    <div class="space-y-0 divide-y divide-gray-100">
                        @foreach($pendingOrders as $order)
                        <div class="py-3 flex justify-between items-center">
                            <div>
                                <p class="font-bold text-sm">#{{ $order->id }} - {{ $order->user->name ?? 'Guest' }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ \Carbon\Carbon::parse($order->created_at)->diffForHumans() }}</p>
                            </div>
                            <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold uppercase tracking-widest hover:underline">
                                Proses
                            </a>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">Tidak ada pesanan tertunda.</p>
                @endif
            </div>
            
            <!-- Low Stock Alerts -->
            @if($lowStockProducts->count() > 0)
            <div class="bg-white border-l-4 border-black p-6 shadow-sm">
                <h2 class="text-sm font-bold uppercase tracking-widest mb-4 flex items-center gap-2">
                    <i data-lucide="alert-triangle" class="w-4 h-4"></i> Stok Menipis
                </h2>
                <div class="space-y-0 divide-y divide-gray-100">
                    @foreach($lowStockProducts as $variant)
                    <div class="py-3 flex justify-between items-center">
                        <div>
                            <p class="font-bold text-sm">{{ $variant->product->name }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $variant->color }} - {{ $variant->size }}</p>
                        </div>
                        <span class="text-xs font-bold text-black border border-black px-2 py-1">
                            Sisa {{ $variant->stock }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column: Top Products & Recent Reviews -->
        <div class="space-y-6">
            
            <!-- Top Products -->
            <div class="bg-white border border-gray-200 p-6">
                <h2 class="text-sm font-bold uppercase tracking-widest mb-6">Produk Terfavorit</h2>
                <div class="space-y-4">
                    @foreach($topProducts as $product)
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-16 bg-gray-100 flex-shrink-0">
                                @if($product->thumbnail)
                                    <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}" class="w-full h-full object-cover" />
                                @endif
                            </div>
                            <div class="flex-1">
                                <p class="font-bold text-sm truncate">{{ $product->name }}</p>
                                <div class="flex items-center gap-1 text-xs font-semibold text-gray-500 mt-1">
                                    <i data-lucide="star" class="w-3 h-3 fill-black text-black"></i>
                                    {{ number_format($product->average_rating, 1) }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Recent Reviews -->
            <div class="bg-white border border-gray-200 p-6">
                <h2 class="text-sm font-bold uppercase tracking-widest mb-6">Ulasan Terbaru</h2>
                @if($recentReviews->count() > 0)
                    <div class="space-y-6">
                        @foreach($recentReviews as $review)
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <p class="font-bold text-xs">{{ $review->user->name ?? 'Guest' }}</p>
                                <div class="flex items-center">
                                    @for($i = 0; $i < $review->rating; $i++)
                                        <i data-lucide="star" class="w-3 h-3 fill-black text-black"></i>
                                    @endfor
                                </div>
                            </div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 truncate">{{ $review->product->name ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-600 line-clamp-3">"{{ $review->comment }}"</p>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">Belum ada ulasan.</p>
                @endif
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesData = @json($sales7DaysData);
        const labels = @json($last7Days);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: salesData,
                    borderColor: '#000000',
                    borderWidth: 2,
                    pointBackgroundColor: '#000000',
                    pointRadius: 0,
                    pointHoverRadius: 4,
                    fill: false,
                    tension: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f3f4f6', drawBorder: false },
                        ticks: {
                            font: { family: "'Inter', sans-serif", size: 10, weight: 'bold' },
                            color: '#9ca3af',
                            callback: function(value) {
                                return 'Rp ' + (value/1000) + 'k';
                            }
                        }
                    },
                    x: {
                        grid: { display: false, drawBorder: false },
                        ticks: {
                            font: { family: "'Inter', sans-serif", size: 10, weight: 'bold' },
                            color: '#9ca3af'
                        }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#000',
                        titleFont: { family: "'Inter', sans-serif", size: 11, weight: 'bold' },
                        bodyFont: { family: "'Inter', sans-serif", size: 12, weight: 'bold' },
                        padding: 10,
                        cornerRadius: 0,
                        displayColors: false
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection
