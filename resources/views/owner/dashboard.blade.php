@extends('layouts.owner')

@section('content')
<div x-data="{ withdrawModal: false }">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4 border-b border-gray-200 pb-4">
        <div>
            <h1 class="text-2xl font-black uppercase tracking-[0.1em] text-black">Business Analytics</h1>
        </div>
        <button @click="withdrawModal = true" class="px-5 py-2 bg-black text-white text-xs font-bold uppercase tracking-widest hover:bg-gray-800 transition-colors flex items-center gap-2">
            <i data-lucide="banknote" class="w-4 h-4"></i> Tarik Dana
        </button>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
        <div class="bg-white p-6 border border-gray-200">
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total Pendapatan</p>
            <p class="text-2xl font-black text-black">{{ formatPrice($grossRevenue) }}</p>
        </div>
        
        <div class="bg-white p-6 border border-gray-200">
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1">Dana Ditarik</p>
            <p class="text-2xl font-black text-black">{{ formatPrice($totalWithdrawn) }}</p>
        </div>
        
        <div class="bg-black p-6 border border-black lg:col-span-2 text-white">
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1">Saldo Tersedia</p>
            <p class="text-3xl font-black">{{ formatPrice($availableBalance) }}</p>
        </div>
    </div>

    <!-- Charts & Analytics -->
    <div class="grid lg:grid-cols-3 gap-6 mb-6">
        
        <!-- Sales Chart -->
        <div class="lg:col-span-2 bg-white border border-gray-200 p-6">
            <h2 class="text-sm font-bold uppercase tracking-widest mb-6">Penjualan 7 Hari</h2>
            <div class="relative h-64 w-full">
                <canvas id="ownerSalesChart"></canvas>
            </div>
        </div>

        <!-- Low Stock Alerts -->
        <div class="bg-white border-l-4 border-black shadow-sm p-6">
            <div class="flex items-center gap-2 mb-4">
                <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                <h2 class="text-sm font-bold uppercase tracking-widest">Peringatan Stok</h2>
            </div>
            
            @if($lowStockProducts->count() > 0)
                <div class="space-y-0 divide-y divide-gray-100">
                    @foreach($lowStockProducts as $variant)
                    <div class="py-3 flex justify-between items-center">
                        <div>
                            <p class="font-bold text-sm text-black">{{ $variant->product->name }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $variant->color }} - {{ $variant->size }}</p>
                        </div>
                        <span class="text-xs font-bold text-black border border-black px-2 py-1">
                            Sisa {{ $variant->stock }}
                        </span>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500 py-4">Semua stok dalam kondisi aman.</p>
            @endif
        </div>
    </div>

    <!-- Lists Section -->
    <div class="grid lg:grid-cols-3 gap-6">
        
        <!-- Bestsellers -->
        <div class="bg-white border border-gray-200 p-6">
            <h2 class="text-sm font-bold uppercase tracking-widest mb-6">Produk Terlaris</h2>
            <div class="space-y-4">
                @forelse($bestsellers as $index => $product)
                    <div class="flex items-center gap-4">
                        <div class="w-6 h-6 border border-gray-200 flex items-center justify-center font-bold text-gray-400 text-[10px] shrink-0">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-sm text-black truncate">{{ $product->name }}</p>
                            <p class="text-xs font-semibold text-gray-500 mt-0.5">{{ formatPrice($product->price) }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-sm font-black text-black">{{ $product->sold_count }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 py-4">Belum ada data penjualan.</p>
                @endforelse
            </div>
        </div>

        <!-- Pending Orders -->
        <div class="bg-white border border-gray-200 p-6">
            <h2 class="text-sm font-bold uppercase tracking-widest mb-6">Pesanan Pending</h2>
            <div class="space-y-0 divide-y divide-gray-100">
                @forelse($pendingOrders as $order)
                    <div class="py-3">
                        <div class="flex justify-between items-start mb-1">
                            <p class="font-bold text-sm text-black">#{{ $order->id }}</p>
                            <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest border border-gray-200 px-1">
                                {{ $order->status }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500">{{ $order->user->name ?? 'Guest' }} - {{ \Carbon\Carbon::parse($order->created_at)->diffForHumans() }}</p>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 py-4">Tidak ada pesanan tertunda.</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Withdrawals -->
        <div class="bg-white border border-gray-200 p-6">
            <h2 class="text-sm font-bold uppercase tracking-widest mb-6">Riwayat Penarikan</h2>
            <div class="space-y-0 divide-y divide-gray-100">
                @forelse($withdrawals as $withdrawal)
                    <div class="py-3">
                        <div class="flex justify-between items-start mb-1">
                            <p class="font-bold text-sm text-black">{{ formatPrice($withdrawal->amount) }}</p>
                            <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-widest border border-gray-200
                                {{ $withdrawal->status === 'completed' ? 'text-black' : 'text-gray-500' }}">
                                {{ $withdrawal->status }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500">{{ $withdrawal->bank_name }} - {{ \Carbon\Carbon::parse($withdrawal->created_at)->format('d M') }}</p>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 py-4">Belum ada riwayat penarikan.</p>
                @endforelse
            </div>
        </div>

    </div>

    <!-- Withdraw Modal -->
    <div x-show="withdrawModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" x-transition.opacity>
        <div class="bg-white w-full max-w-md border border-black shadow-[4px_4px_0_0_#000]" @click.outside="withdrawModal = false">
            <div class="flex items-center justify-between p-5 border-b border-gray-200">
                <h3 class="text-sm font-black uppercase tracking-widest text-black">Form Tarik Dana</h3>
                <button @click="withdrawModal = false" class="text-gray-400 hover:text-black transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ route('owner.withdraw') }}" method="POST" class="p-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Jumlah Tarik (Rp)</label>
                    <input type="number" name="amount" class="w-full bg-white border border-gray-200 p-2 text-sm font-bold text-black focus:outline-none focus:border-black transition-colors" min="10000" max="{{ $availableBalance }}" value="{{ $availableBalance }}" required>
                    <p class="text-[10px] text-gray-500 mt-1">Maksimal: {{ formatPrice($availableBalance) }}</p>
                </div>
                
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Nama Bank / E-Wallet</label>
                    <input type="text" name="bank_name" class="w-full bg-white border border-gray-200 p-2 text-sm font-bold text-black focus:outline-none focus:border-black transition-colors" placeholder="BCA / Mandiri / GoPay" required>
                </div>
                
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Nomor Rekening</label>
                    <input type="text" name="account_number" class="w-full bg-white border border-gray-200 p-2 text-sm font-bold text-black focus:outline-none focus:border-black transition-colors" placeholder="1234567890" required>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Atas Nama</label>
                    <input type="text" name="account_name" value="{{ auth()->user()->name }}" class="w-full bg-white border border-gray-200 p-2 text-sm font-bold text-black focus:outline-none focus:border-black transition-colors" required>
                </div>

                <div class="pt-2 flex gap-3">
                    <button type="button" @click="withdrawModal = false" class="flex-1 py-2 bg-gray-100 text-black text-xs font-bold uppercase tracking-widest hover:bg-gray-200 transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 py-2 bg-black text-white text-xs font-bold uppercase tracking-widest hover:bg-gray-800 transition-colors">
                        Tarik Dana
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('ownerSalesChart').getContext('2d');
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
