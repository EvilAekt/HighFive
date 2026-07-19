@extends('layouts.admin')

@section('content')
<div>
    <h1 class="text-2xl font-bold text-primary-900 mb-6">Dashboard</h1>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-6 border border-primary-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-primary-600">Total Pendapatan</p>
                    <p class="text-2xl font-bold text-primary-900 mt-1">{{ formatPrice($totalRevenue) }}</p>
                </div>
                <div class="bg-black p-3 rounded-lg">
                    <i data-lucide="dollar-sign" class="w-6 h-6 text-white"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 border border-primary-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-primary-600">Total Pesanan</p>
                    <p class="text-2xl font-bold text-primary-900 mt-1">{{ $totalOrders }}</p>
                </div>
                <div class="bg-primary-700 p-3 rounded-lg">
                    <i data-lucide="shopping-cart" class="w-6 h-6 text-white"></i>
                </div>
            </div>
        </div>

        
        <div class="bg-white p-6 border border-primary-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-primary-600">Total Produk</p>
                    <p class="text-2xl font-bold text-primary-900 mt-1">{{ $totalProducts }}</p>
                </div>
                <div class="bg-primary-800 p-3 rounded-lg">
                    <i data-lucide="package" class="w-6 h-6 text-white"></i>
                </div>
            </div>
        </div>
        

        <div class="bg-white p-6 border border-primary-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-primary-600">Pelanggan</p>
                    <p class="text-2xl font-bold text-primary-900 mt-1">{{ $totalCustomers }}</p>
                </div>
                <div class="bg-primary-600 p-3 rounded-lg">
                    <i data-lucide="users" class="w-6 h-6 text-white"></i>
                </div>
            </div>
        </div>

    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        <!-- Orders by Status -->
        <div class="bg-white border border-primary-200 p-6">
            <h2 class="text-lg font-semibold mb-4">Pesanan per Status</h2>
            <div class="relative h-64 w-full">
                <canvas id="orderStatusChart"></canvas>
            </div>
        </div>


        <!-- Top Products -->
        <div class="bg-white border border-primary-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold">Produk Terlaris (Berdasarkan Ulasan)</h2>
                <a href="{{ route('admin.products.index') }}" class="text-sm text-primary-600 hover:text-primary-900">
                    Lihat Semua
                </a>
            </div>
            <div class="space-y-3">
                @foreach($topProducts as $product)
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-primary-100 flex-shrink-0">
                            @if($product->thumbnail)
                                <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}" class="w-full h-full object-cover" />
                            @endif
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-sm">{{ $product->name }}</p>
                            <div class="flex items-center gap-1 text-xs text-primary-600">
                                <i data-lucide="star" class="w-3 h-3 fill-yellow-400 text-yellow-400"></i>
                                {{ number_format($product->average_rating, 1) }} ({{ $product->reviews_count }})
                            </div>
                        </div>
                        <p class="text-sm font-semibold">{{ formatPrice($product->price) }}</p>
                    </div>
                @endforeach
            </div>
        </div>


        <!-- Recent Orders -->
        <div class="bg-white border border-primary-200 p-6 lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold">Pesanan Terbaru</h2>
                <a href="{{ route('admin.orders.index') }}" class="text-sm text-primary-600 hover:text-primary-900">
                    Lihat Semua
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-sm text-primary-600 border-b border-primary-200">
                            <th class="pb-3 font-medium">Order ID</th>
                            <th class="pb-3 font-medium">Pelanggan</th>
                            <th class="pb-3 font-medium">Total</th>
                            <th class="pb-3 font-medium">Status</th>
                            <th class="pb-3 font-medium">Pembayaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentOrders as $order)
                            <tr class="border-b border-primary-100 last:border-0">
                                <td class="py-3 text-sm font-medium">{{ $order->order_code }}</td>
                                <td class="py-3 text-sm">{{ $order->user->name ?? 'N/A' }}</td>
                                <td class="py-3 text-sm">{{ formatPrice($order->total_price) }}</td>
                                <td class="py-3 text-sm capitalize">{{ $order->status }}</td>
                                <td class="py-3 text-sm capitalize">{{ $order->payment->status ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>



        <!-- 4-Month Sales Report -->
        <div class="bg-white border border-primary-200 p-6 lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold">Laporan Penjualan (4 Bulan Terakhir)</h2>
            </div>
            <div class="relative h-72 w-full">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Orders By Status Chart
    const statusData = {!! json_encode($ordersByStatus) !!};
    const ctxStatus = document.getElementById('orderStatusChart').getContext('2d');
    new Chart(ctxStatus, {
        type: 'doughnut',
        data: {
            labels: Object.keys(statusData).map(s => s.charAt(0).toUpperCase() + s.slice(1)),
            datasets: [{
                data: Object.values(statusData),
                backgroundColor: [
                    '#f59e0b', // pending
                    '#3b82f6', // processing
                    '#8b5cf6', // shipped
                    '#10b981', // completed
                    '#ef4444'  // cancelled
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'right' }
            }
        }
    });

    // Sales Chart
    const salesData = {!! json_encode($sales4Months) !!};
    const ctxSales = document.getElementById('salesChart').getContext('2d');
    new Chart(ctxSales, {
        type: 'bar',
        data: {
            labels: salesData.map(item => item.name.length > 20 ? item.name.substring(0, 20) + '...' : item.name),
            datasets: [{
                label: 'Total Terjual (Item)',
                data: salesData.map(item => item.total_sold),
                backgroundColor: '#000000',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, grid: { color: '#f3f4f6' } },
                x: { grid: { display: false } }
            }
        }
    });
});
</script>
@endpush
@endsection
