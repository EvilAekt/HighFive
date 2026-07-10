@extends('layouts.admin')

@section('content')
<div x-data="{ openCreateModal: false }">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-primary-900">Kelola Kupon Diskon</h1>
        <button @click="openCreateModal = true" class="bg-primary-900 text-white px-4 py-2 text-sm font-medium hover:bg-black transition-colors flex items-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i> Tambah Kupon
        </button>
    </div>

    <!-- Create Coupon Modal -->
    <div x-show="openCreateModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" x-cloak style="display: none;">
        <div @click.away="openCreateModal = false" class="bg-white p-6 w-full max-w-lg shadow-xl border border-primary-200 h-screen md:h-auto overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Tambah Kupon Baru</h2>
                <button @click="openCreateModal = false" class="text-primary-500 hover:text-black">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form action="{{ route('admin.coupons.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-primary-700 mb-1">Kode Kupon (Tanpa Spasi)</label>
                    <input type="text" name="code" required class="w-full px-3 py-2 border border-primary-300 focus:border-black outline-none transition-colors uppercase">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-primary-700 mb-1">Tipe Diskon</label>
                        <select name="type" required class="w-full px-3 py-2 border border-primary-300 focus:border-black outline-none transition-colors bg-white">
                            <option value="percentage">Persentase (%)</option>
                            <option value="fixed">Nominal (Rp)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-primary-700 mb-1">Nilai Diskon</label>
                        <input type="number" name="value" required class="w-full px-3 py-2 border border-primary-300 focus:border-black outline-none transition-colors">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-primary-700 mb-1">Min. Belanja (Rp)</label>
                        <input type="number" name="min_purchase" value="0" required class="w-full px-3 py-2 border border-primary-300 focus:border-black outline-none transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-primary-700 mb-1">Maks. Diskon (Rp) *opsional</label>
                        <input type="number" name="max_discount" class="w-full px-3 py-2 border border-primary-300 focus:border-black outline-none transition-colors">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-primary-700 mb-1">Batas Kuota *opsional</label>
                        <input type="number" name="max_uses" class="w-full px-3 py-2 border border-primary-300 focus:border-black outline-none transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-primary-700 mb-1">Berlaku Sampai *opsional</label>
                        <input type="date" name="valid_until" class="w-full px-3 py-2 border border-primary-300 focus:border-black outline-none transition-colors">
                    </div>
                </div>
                <div>
                    <label class="flex items-center gap-2 text-sm font-medium text-primary-700">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded text-black focus:ring-black">
                        Aktifkan Kupon
                    </label>
                </div>
                <div class="pt-4 flex justify-end gap-2">
                    <button type="button" @click="openCreateModal = false" class="px-4 py-2 border border-primary-300 text-sm font-medium hover:bg-primary-50">Batal</button>
                    <button type="submit" class="bg-primary-900 text-white px-4 py-2 text-sm font-medium hover:bg-black">Simpan Kupon</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Coupons Table -->
    <div class="bg-white border border-primary-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-primary-50">
                    <tr class="text-left text-sm text-primary-600 border-b border-primary-200">
                        <th class="px-6 py-3 font-medium">Kode</th>
                        <th class="px-6 py-3 font-medium">Diskon</th>
                        <th class="px-6 py-3 font-medium">Syarat & Ketentuan</th>
                        <th class="px-6 py-3 font-medium">Penggunaan</th>
                        <th class="px-6 py-3 font-medium">Status</th>
                        <th class="px-6 py-3 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($coupons as $coupon)
                        <tr class="border-b border-primary-100 last:border-0 hover:bg-primary-50">
                            <td class="px-6 py-4">
                                <span class="font-bold text-primary-900 uppercase tracking-widest">{{ $coupon->code }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium">
                                @if($coupon->type === 'percentage')
                                    {{ $coupon->value }}%
                                    @if($coupon->max_discount)
                                        <span class="block text-xs font-normal text-primary-500 mt-1">Maks. Rp {{ number_format($coupon->max_discount, 0, ',', '.') }}</span>
                                    @endif
                                @else
                                    Rp {{ number_format($coupon->value, 0, ',', '.') }}
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-primary-600">
                                Min. Belanja: Rp {{ number_format($coupon->min_purchase, 0, ',', '.') }}<br>
                                Exp: {{ $coupon->valid_until ? $coupon->valid_until->format('d M Y') : 'Tanpa batas' }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                {{ $coupon->current_uses }} / {{ $coupon->max_uses ?? '∞' }}
                            </td>
                            <td class="px-6 py-4">
                                @if($coupon->isValid())
                                    <span class="inline-block px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded">Aktif</span>
                                @else
                                    <span class="inline-block px-2 py-1 text-xs font-medium bg-red-100 text-red-700 rounded">Tidak Aktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus kupon ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-primary-500">Belum ada kupon diskon.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
