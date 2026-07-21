@extends('layouts.admin')

@section('content')
<div>
    <h1 class="text-2xl font-bold text-primary-900 mb-2">Kelola Penarikan Dana</h1>
    <p class="text-sm text-primary-500 mb-6">Kelola permintaan penarikan dana dari Owner. Lakukan transfer secara manual, lalu perbarui statusnya di sini.</p>

    @if(session('success'))
        <div class="mb-6 bg-green-50 text-green-700 p-4 border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white border border-primary-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-primary-50">
                    <tr class="text-left text-sm text-primary-600 border-b border-primary-200">
                        <th class="px-4 py-3 font-medium">Tanggal</th>
                        <th class="px-4 py-3 font-medium">Owner</th>
                        <th class="px-4 py-3 font-medium">Nominal</th>
                        <th class="px-4 py-3 font-medium">Rekening Tujuan</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($withdrawals as $withdrawal)
                        <tr class="border-b border-primary-100 last:border-0 hover:bg-primary-50">
                            <td class="px-4 py-3 text-sm">{{ formatDate($withdrawal->created_at) }}</td>
                            <td class="px-4 py-3 text-sm font-medium">{{ $withdrawal->user->name ?? 'Unknown' }}</td>
                            <td class="px-4 py-3 text-sm font-bold text-black">{{ formatPrice($withdrawal->amount) }}</td>
                            <td class="px-4 py-3 text-sm">
                                <div><span class="font-bold">{{ $withdrawal->bank_name }}</span> - {{ $withdrawal->account_number }}</div>
                                <div class="text-xs text-primary-500 mt-1">A.N: {{ $withdrawal->account_name }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-block px-2 py-1 text-xs font-medium rounded uppercase tracking-wider
                                    {{ $withdrawal->status === 'completed' ? 'bg-green-100 text-green-700' : ($withdrawal->status === 'failed' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                    {{ $withdrawal->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <form action="{{ route('admin.withdrawals.update', $withdrawal->id) }}" method="POST" class="inline-flex gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" onchange="this.form.submit()" class="text-xs px-2 py-1 border border-primary-300 rounded outline-none focus:border-black cursor-pointer bg-white">
                                        <option value="pending" {{ $withdrawal->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="completed" {{ $withdrawal->status === 'completed' ? 'selected' : '' }}>Completed (Sukses)</option>
                                        <option value="failed" {{ $withdrawal->status === 'failed' ? 'selected' : '' }}>Failed (Gagal)</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-primary-500">Tidak ada permintaan penarikan dana.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
