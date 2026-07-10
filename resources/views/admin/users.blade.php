@extends('layouts.admin')

@section('content')
<div x-data="{ filter: '{{ request('filter', 'all') }}' }">
    <h1 class="text-2xl font-bold text-primary-900 mb-6">Kelola Pelanggan</h1>

    <!-- Header & Filter -->
    <div class="flex justify-between items-center mb-6" x-data="{ openCreateModal: false }">
        <div class="flex gap-2">
            <a href="{{ route('admin.users.index') }}" 
               class="px-4 py-2 text-sm font-medium border transition-colors {{ request('filter', 'all') === 'all' ? 'bg-primary-900 text-white border-primary-900' : 'bg-white text-primary-600 border-primary-300 hover:border-primary-900' }}">
                Semua
            </a>
            <a href="{{ route('admin.users.index', ['filter' => 'user']) }}" 
               class="px-4 py-2 text-sm font-medium border transition-colors {{ request('filter') === 'user' ? 'bg-primary-900 text-white border-primary-900' : 'bg-white text-primary-600 border-primary-300 hover:border-primary-900' }}">
                Pelanggan
            </a>
            <a href="{{ route('admin.users.index', ['filter' => 'admin']) }}" 
               class="px-4 py-2 text-sm font-medium border transition-colors {{ request('filter') === 'admin' ? 'bg-primary-900 text-white border-primary-900' : 'bg-white text-primary-600 border-primary-300 hover:border-primary-900' }}">
                Admin
            </a>
        </div>
        
        <button @click="openCreateModal = true" class="bg-primary-900 text-white px-4 py-2 text-sm font-medium hover:bg-black transition-colors flex items-center gap-2">
            <i data-lucide="plus" class="w-4 h-4"></i> Tambah Pengguna
        </button>

        <!-- Create User Modal -->
        <div x-show="openCreateModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" x-cloak style="display: none;">
            <div @click.away="openCreateModal = false" class="bg-white p-6 w-full max-w-md shadow-xl border border-primary-200">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold">Tambah Pengguna Baru</h2>
                    <button @click="openCreateModal = false" class="text-primary-500 hover:text-black">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-primary-700 mb-1">Nama</label>
                        <input type="text" name="name" required class="w-full px-3 py-2 border border-primary-300 focus:border-black outline-none transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-primary-700 mb-1">Email</label>
                        <input type="email" name="email" required class="w-full px-3 py-2 border border-primary-300 focus:border-black outline-none transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-primary-700 mb-1">Password</label>
                        <input type="password" name="password" required minlength="8" class="w-full px-3 py-2 border border-primary-300 focus:border-black outline-none transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-primary-700 mb-1">Role</label>
                        <select name="role" required class="w-full px-3 py-2 border border-primary-300 focus:border-black outline-none transition-colors bg-white">
                            <option value="pengunjung">Pengunjung</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="pt-4 flex justify-end gap-2">
                        <button type="button" @click="openCreateModal = false" class="px-4 py-2 border border-primary-300 text-sm font-medium hover:bg-primary-50">Batal</button>
                        <button type="submit" class="bg-primary-900 text-white px-4 py-2 text-sm font-medium hover:bg-black">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="bg-white border border-primary-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-primary-50">
                    <tr class="text-left text-sm text-primary-600 border-b border-primary-200">
                        <th class="px-4 py-3 font-medium">Nama</th>
                        <th class="px-4 py-3 font-medium">Email</th>
                        <th class="px-4 py-3 font-medium">Telepon</th>
                        <th class="px-4 py-3 font-medium">Role</th>
                        <th class="px-4 py-3 font-medium">Terdaftar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr class="border-b border-primary-100 last:border-0 hover:bg-primary-50">
                            <td class="px-4 py-3 text-sm font-medium">{{ $user->name }}</td>
                            <td class="px-4 py-3 text-sm">{{ $user->email }}</td>
                            <td class="px-4 py-3 text-sm">{{ $user->phone ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-block px-2 py-1 text-xs font-medium rounded {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm">{{ formatDate($user->created_at) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-12 text-primary-500">Tidak ada pengguna</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
