@extends('layouts.app')

@section('title', 'Pengaturan Akun - HIGH FIVE')

@section('content')
<div class="bg-primary-50/50 dark:bg-onyx-800 min-h-screen py-12 transition-colors">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{ activeTab: 'profile' }">
        
        <!-- Header Section -->
        <div class="mb-10 text-center md:text-left flex flex-col md:flex-row md:items-end justify-between border-b border-primary-200 dark:border-gray-700 pb-6">
            <div>
                <h1 class="text-4xl font-black tracking-tight text-primary-900 dark:text-white uppercase">Pengaturan</h1>
                <p class="text-primary-500 mt-2 text-sm tracking-wide">Kelola preferensi dan profil akun Anda.</p>
            </div>
            <div class="mt-4 md:mt-0 flex items-center gap-4 justify-center md:justify-end">
                @if($user->avatar)
                    <img src="{{ str_starts_with($user->avatar, 'http') ? $user->avatar : asset('storage/' . $user->avatar) }}" alt="Avatar" class="w-12 h-12 rounded-full object-cover border border-primary-200 shadow-sm">
                @else
                    <div class="w-12 h-12 rounded-full bg-primary-900 flex items-center justify-center text-white shadow-sm">
                        <span class="text-lg font-bold tracking-widest">{{ substr($user->name, 0, 1) }}</span>
                    </div>
                @endif
                <div class="text-left">
                    <p class="text-sm font-bold text-primary-900 dark:text-white">{{ $user->name }}</p>
                    <p class="text-xs text-primary-500 uppercase tracking-widest">{{ $user->role }}</p>
                </div>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-10">
            
            <!-- Sleek Sidebar Navigation -->
            <div class="w-full lg:w-64 flex-shrink-0">
                <nav class="flex flex-row lg:flex-col overflow-x-auto lg:overflow-visible gap-2 pb-4 lg:pb-0 hide-scrollbar border-b lg:border-b-0 lg:border-r border-primary-200 lg:pr-6">
                    <button @click="activeTab = 'profile'" 
                            :class="{'text-black dark:text-white font-bold border-b-2 lg:border-b-0 lg:border-r-2 border-black dark:border-white': activeTab === 'profile', 'text-primary-500 hover:text-black dark:hover:text-white': activeTab !== 'profile'}" 
                            class="flex items-center gap-3 px-1 lg:px-4 py-4 text-sm transition-all duration-300 text-left whitespace-nowrap lg:whitespace-normal">
                        <i data-lucide="user" class="w-5 h-5"></i> 
                        Biodata Diri
                    </button>
                    
                    <button @click="activeTab = 'security'" 
                            :class="{'text-black dark:text-white font-bold border-b-2 lg:border-b-0 lg:border-r-2 border-black dark:border-white': activeTab === 'security', 'text-primary-500 hover:text-black dark:hover:text-white': activeTab !== 'security'}" 
                            class="flex items-center gap-3 px-1 lg:px-4 py-4 text-sm transition-all duration-300 text-left whitespace-nowrap lg:whitespace-normal">
                        <i data-lucide="shield-check" class="w-5 h-5"></i> 
                        Keamanan
                    </button>
                    
                    <button @click="activeTab = 'address'" 
                            :class="{'text-black dark:text-white font-bold border-b-2 lg:border-b-0 lg:border-r-2 border-black dark:border-white': activeTab === 'address', 'text-primary-500 hover:text-black dark:hover:text-white': activeTab !== 'address'}" 
                            class="flex items-center gap-3 px-1 lg:px-4 py-4 text-sm transition-all duration-300 text-left whitespace-nowrap lg:whitespace-normal">
                        <i data-lucide="map-pin" class="w-5 h-5"></i> 
                        Daftar Alamat
                    </button>
                    
                    <button @click="activeTab = 'bank'" 
                            :class="{'text-black dark:text-white font-bold border-b-2 lg:border-b-0 lg:border-r-2 border-black dark:border-white': activeTab === 'bank', 'text-primary-500 hover:text-black dark:hover:text-white': activeTab !== 'bank'}" 
                            class="flex items-center gap-3 px-1 lg:px-4 py-4 text-sm transition-all duration-300 text-left whitespace-nowrap lg:whitespace-normal">
                        <i data-lucide="credit-card" class="w-5 h-5"></i> 
                        Rekening Bank
                    </button>
                    
                    <button @click="activeTab = 'preferences'" 
                            :class="{'text-black dark:text-white font-bold border-b-2 lg:border-b-0 lg:border-r-2 border-black dark:border-white': activeTab === 'preferences', 'text-primary-500 hover:text-black dark:hover:text-white': activeTab !== 'preferences'}" 
                            class="flex items-center gap-3 px-1 lg:px-4 py-4 text-sm transition-all duration-300 text-left whitespace-nowrap lg:whitespace-normal">
                        <i data-lucide="sliders" class="w-5 h-5"></i> 
                        Preferensi
                    </button>
                </nav>
            </div>

            <!-- Main Content Area with Modern Cards -->
            <div class="flex-1 max-w-3xl">
                
                @if($errors->any())
                    <div class="mb-6 bg-red-50 text-red-700 p-5 rounded-xl border border-red-100 flex items-start gap-4 shadow-sm animate-fade-in">
                        <i data-lucide="alert-circle" class="w-6 h-6 text-red-500 flex-shrink-0 mt-0.5"></i>
                        <ul class="list-disc pl-5 text-sm space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Tab: Biodata Diri -->
                <div x-show="activeTab === 'profile'" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-cloak class="bg-white dark:bg-onyx-700 p-8 md:p-10 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-primary-100 dark:border-onyx-600">
                    
                    <h2 class="text-2xl font-bold text-primary-900 dark:text-white mb-8 flex items-center gap-3">
                        <i data-lucide="user" class="text-primary-400"></i> Informasi Profil
                    </h2>
                    
                    <form action="{{ route('settings.profile') }}" method="POST" enctype="multipart/form-data" class="space-y-8" 
                          x-data="{ 
                              photoName: null,
                              photoPreview: null,
                              updatePreview(event) {
                                  const file = event.target.files[0];
                                  if (!file) return;
                                  this.photoName = file.name;
                                  const reader = new FileReader();
                                  reader.onload = (e) => {
                                      this.photoPreview = e.target.result;
                                  };
                                  reader.readAsDataURL(file);
                              }
                          }">
                        @csrf
                        @method('PUT')
                        
                        <!-- Premium Avatar Uploader -->
                        <div class="flex items-center gap-8 bg-primary-50/50 dark:bg-gray-700/50 p-6 rounded-xl border border-primary-100 dark:border-gray-600">
                            <label for="avatar_upload" class="relative group cursor-pointer block">
                                <!-- Default Image -->
                                <div x-show="!photoPreview">
                                    @if($user->avatar)
                                        <img src="{{ str_starts_with($user->avatar, 'http') ? $user->avatar : asset('storage/' . $user->avatar) }}" alt="Avatar" class="w-24 h-24 rounded-full object-cover shadow-md border-4 border-white dark:border-gray-800 transition-transform group-hover:scale-105">
                                    @else
                                        <div class="w-24 h-24 rounded-full bg-primary-200 dark:bg-gray-600 flex items-center justify-center shadow-md border-4 border-white dark:border-gray-800 transition-transform group-hover:scale-105">
                                            <i data-lucide="camera" class="w-8 h-8 text-primary-500 dark:text-gray-400"></i>
                                        </div>
                                    @endif
                                </div>
                                <!-- Preview Image -->
                                <div x-show="photoPreview" style="display: none;">
                                    <img :src="photoPreview" class="w-24 h-24 rounded-full object-cover shadow-md border-4 border-white dark:border-gray-800 transition-transform group-hover:scale-105">
                                </div>

                                <div class="absolute inset-0 bg-black/40 rounded-full opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                                    <i data-lucide="upload" class="w-6 h-6 text-white"></i>
                                </div>
                                <input type="file" id="avatar_upload" name="avatar" accept="image/*" class="hidden" @change="updatePreview">
                            </label>
                            <div class="flex-1">
                                <h3 class="text-sm font-bold text-primary-900 dark:text-white mb-1">Foto Profil</h3>
                                <p class="text-xs text-primary-500 dark:text-gray-400 mb-3 leading-relaxed">Pilih gambar resolusi tinggi dengan format JPG atau PNG. Ukuran maksimal 10MB.</p>
                                <label for="avatar_upload" class="inline-flex items-center justify-center px-4 py-2 text-xs font-bold tracking-widest text-primary-900 dark:text-white uppercase bg-white dark:bg-gray-800 border border-primary-200 dark:border-gray-600 rounded-lg hover:bg-primary-50 dark:hover:bg-gray-700 transition-colors cursor-pointer shadow-sm">
                                    Pilih File Baru
                                </label>
                                <p x-show="photoName" class="text-xs text-green-600 dark:text-green-400 mt-2 font-medium flex items-center gap-1" style="display: none;">
                                    <i data-lucide="check-circle" class="w-3 h-3"></i> <span x-text="photoName"></span>
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold uppercase tracking-widest text-primary-500">Nama Lengkap</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full px-4 py-3 bg-primary-50/50 dark:bg-gray-700 border border-primary-200 dark:border-gray-600 rounded-xl focus:bg-white dark:focus:bg-gray-600 focus:ring-2 focus:ring-black dark:focus:ring-white outline-none" required>
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold uppercase tracking-widest text-primary-500">No. WhatsApp</label>
                                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full px-4 py-3 bg-primary-50/50 dark:bg-gray-700 border border-primary-200 dark:border-gray-600 rounded-xl focus:bg-white dark:focus:bg-gray-600 focus:ring-2 focus:ring-black dark:focus:ring-white outline-none" placeholder="08123456789">
                            </div>
                        </div>
                        
                        <div class="pt-4 border-t border-primary-100 flex justify-end">
                            <button type="submit" class="bg-black text-white px-8 py-3 rounded-xl text-sm font-bold tracking-widest uppercase hover:bg-primary-900 hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Tab: Keamanan -->
                <div x-show="activeTab === 'security'" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-cloak class="bg-white dark:bg-onyx-700 p-8 md:p-10 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-primary-100 dark:border-onyx-600">
                    
                    <h2 class="text-2xl font-bold text-primary-900 mb-8 flex items-center gap-3">
                        <i data-lucide="shield-check" class="text-primary-400"></i> Akun & Keamanan
                    </h2>
                    
                    <form action="{{ route('settings.security') }}" method="POST" class="space-y-8">
                        @csrf
                        @method('PUT')
                        
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-widest text-primary-500">Alamat Email</label>
                            <div class="relative">
                                <i data-lucide="mail" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-primary-400"></i>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full pl-12 pr-4 py-3 bg-primary-50/50 dark:bg-gray-700 border border-primary-200 dark:border-gray-600 rounded-xl focus:bg-white dark:focus:bg-gray-600 outline-none" required>
                            </div>
                        </div>
                        
                        <div class="pt-6 border-t border-primary-100 dark:border-gray-700">
                            <h3 class="text-lg font-bold text-primary-900 dark:text-white mb-2">Ubah Kata Sandi</h3>
                            <p class="text-sm text-primary-500 mb-6">Kosongkan bidang ini jika Anda tidak ingin mengubah kata sandi Anda saat ini.</p>

                            <div class="space-y-5 bg-primary-50/30 dark:bg-gray-700/50 p-6 rounded-xl border border-primary-100 dark:border-gray-600">
                                <div class="space-y-2">
                                    <label class="text-xs font-bold uppercase tracking-widest text-primary-500">Sandi Saat Ini</label>
                                    <input type="password" name="current_password" class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-primary-200 dark:border-gray-600 rounded-xl outline-none">
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div class="space-y-2">
                                        <label class="text-xs font-bold uppercase tracking-widest text-primary-500">Sandi Baru</label>
                                        <input type="password" name="password" class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-primary-200 dark:border-gray-600 rounded-xl outline-none">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-xs font-bold uppercase tracking-widest text-primary-500">Ulangi Sandi Baru</label>
                                        <input type="password" name="password_confirmation" class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-primary-200 dark:border-gray-600 rounded-xl outline-none">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 flex justify-end">
                            <button type="submit" class="bg-black text-white px-8 py-3 rounded-xl text-sm font-bold tracking-widest uppercase hover:bg-primary-900 hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                                Update Keamanan
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Tab: Daftar Alamat -->
                <div x-show="activeTab === 'address'" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-cloak class="bg-white dark:bg-onyx-700 p-8 md:p-10 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-primary-100 dark:border-onyx-600" 
                     x-data="{ showForm: false }">
                    
                    <div class="flex items-center justify-between mb-8">
                        <h2 class="text-2xl font-bold text-primary-900 flex items-center gap-3">
                            <i data-lucide="map-pin" class="text-primary-400"></i> Alamat Saya
                        </h2>
                        <button @click="showForm = !showForm" class="flex items-center gap-2 text-xs font-bold tracking-widest uppercase px-4 py-2 rounded-lg transition-colors" :class="showForm ? 'bg-red-50 text-red-600 hover:bg-red-100' : 'bg-black text-white hover:bg-primary-900'">
                            <i data-lucide="plus" class="w-4 h-4" x-show="!showForm"></i>
                            <i data-lucide="x" class="w-4 h-4" x-show="showForm"></i>
                            <span x-text="showForm ? 'Batal' : 'Tambah Alamat'"></span>
                        </button>
                    </div>

                    <!-- Add Address Form -->
                    <div x-show="showForm" x-collapse class="mb-10">
                        <div class="bg-primary-50 p-6 rounded-xl border border-primary-200">
                            <h3 class="text-sm font-bold text-primary-900 mb-6 uppercase tracking-widest">Detail Alamat Baru</h3>
                            <form action="{{ route('settings.address.store') }}" method="POST" class="space-y-5">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div class="space-y-2">
                                        <label class="text-xs font-bold uppercase tracking-widest text-primary-500">Label (Mis: Rumah)</label>
                                        <input type="text" name="label" class="w-full px-4 py-3 bg-white border border-primary-200 rounded-xl focus:ring-2 focus:ring-black outline-none" required>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-xs font-bold uppercase tracking-widest text-primary-500">Nama Penerima</label>
                                        <input type="text" name="recipient_name" class="w-full px-4 py-3 bg-white border border-primary-200 rounded-xl focus:ring-2 focus:ring-black outline-none" required>
                                    </div>
                                    <div class="space-y-2 md:col-span-2">
                                        <label class="text-xs font-bold uppercase tracking-widest text-primary-500">No. Telepon / HP</label>
                                        <input type="text" name="phone" class="w-full px-4 py-3 bg-white border border-primary-200 rounded-xl focus:ring-2 focus:ring-black outline-none" required>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs font-bold uppercase tracking-widest text-primary-500">Alamat Lengkap (Jalan, RT/RW, Kec, Kota)</label>
                                    <textarea name="full_address" rows="3" class="w-full px-4 py-3 bg-white border border-primary-200 rounded-xl focus:ring-2 focus:ring-black outline-none resize-none" required></textarea>
                                </div>
                                <label class="flex items-center gap-3 cursor-pointer p-4 border border-primary-200 rounded-xl bg-white hover:bg-primary-50 transition-colors">
                                    <input type="checkbox" name="is_primary" value="1" class="w-5 h-5 text-black border-primary-300 rounded focus:ring-black">
                                    <div>
                                        <span class="block text-sm font-bold text-primary-900">Jadikan Alamat Utama</span>
                                        <span class="block text-xs text-primary-500">Alamat ini akan otomatis dipilih saat checkout.</span>
                                    </div>
                                </label>
                                <button type="submit" class="w-full bg-black text-white px-8 py-3 rounded-xl text-sm font-bold tracking-widest uppercase hover:bg-primary-900 transition-colors mt-2">
                                    Simpan Alamat
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Address List -->
                    <div class="space-y-5">
                        @forelse($addresses as $address)
                            <div class="group p-6 rounded-xl border transition-all duration-300 relative overflow-hidden {{ $address->is_primary ? 'border-black bg-primary-50/30 shadow-md' : 'border-primary-200 bg-white hover:border-primary-400 hover:shadow-sm' }}">
                                @if($address->is_primary)
                                    <div class="absolute top-0 right-0 bg-black text-white text-[10px] font-bold uppercase tracking-widest px-4 py-1.5 rounded-bl-xl">
                                        Utama
                                    </div>
                                @endif
                                
                                <div class="flex items-start gap-4">
                                    <div class="mt-1 flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center {{ $address->is_primary ? 'bg-black text-white' : 'bg-primary-100 text-primary-500' }}">
                                        <i data-lucide="map-pin" class="w-5 h-5"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-lg font-bold text-primary-900 mb-1">{{ $address->label }}</h3>
                                        <p class="text-sm font-medium text-primary-700 mb-3">{{ $address->recipient_name }} &bull; {{ $address->phone }}</p>
                                        <p class="text-sm text-primary-600 leading-relaxed max-w-lg">{{ $address->full_address }}</p>
                                        
                                        <div class="mt-6 flex items-center gap-4 border-t border-primary-100 pt-4">
                                            @if(!$address->is_primary)
                                                <form action="{{ route('settings.address.primary', $address->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="text-xs font-bold uppercase tracking-widest text-primary-500 hover:text-black transition-colors">Jadikan Utama</button>
                                                </form>
                                                <span class="text-primary-300">|</span>
                                            @endif
                                            <form action="{{ route('settings.address.destroy', $address->id) }}" method="POST" onsubmit="return confirm('Hapus alamat ini secara permanen?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs font-bold uppercase tracking-widest text-red-500 hover:text-red-700 transition-colors">Hapus</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12 px-4 border-2 border-dashed border-primary-200 rounded-2xl bg-primary-50/50">
                                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm text-primary-300">
                                    <i data-lucide="map-pin" class="w-8 h-8"></i>
                                </div>
                                <h3 class="text-lg font-bold text-primary-900 mb-2">Belum Ada Alamat</h3>
                                <p class="text-sm text-primary-500 mb-6 max-w-sm mx-auto">Anda belum menambahkan daftar alamat untuk pengiriman. Tambahkan sekarang untuk mempermudah checkout.</p>
                                <button @click="showForm = true" class="bg-black text-white px-6 py-2.5 rounded-lg text-xs font-bold tracking-widest uppercase hover:bg-primary-900 transition-colors">
                                    Tambah Alamat Pertama
                                </button>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Tab: Rekening Bank -->
                <div x-show="activeTab === 'bank'" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-cloak class="bg-white dark:bg-onyx-700 p-8 md:p-10 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-primary-100 dark:border-onyx-600">
                    
                    <h2 class="text-2xl font-bold text-primary-900 mb-6 flex items-center gap-3">
                        <i data-lucide="credit-card" class="text-primary-400"></i> Info Pembayaran
                    </h2>
                    
                    <div class="bg-gradient-to-br from-black to-gray-800 p-6 rounded-2xl text-white mb-8 shadow-xl relative overflow-hidden">
                        <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-white opacity-5 rounded-full blur-2xl"></div>
                        <i data-lucide="info" class="w-6 h-6 mb-4 text-primary-300"></i>
                        <h3 class="text-lg font-bold mb-2">Pentingnya Data Rekening</h3>
                        <p class="text-sm text-primary-200 leading-relaxed opacity-90 max-w-xl">
                            Informasi rekening ini akan digunakan oleh sistem secara otomatis saat memproses pengembalian dana (Refund) pesanan Anda.
                        </p>
                    </div>

                    <form action="{{ route('settings.bank') }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2 md:col-span-2">
                                <label class="text-xs font-bold uppercase tracking-widest text-primary-500">Nama Bank (BCA / Mandiri / BNI)</label>
                                <input type="text" name="bank_name" value="{{ old('bank_name', $user->bank_name) }}" class="w-full px-4 py-3 bg-primary-50/50 border border-primary-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-black outline-none transition-all uppercase" placeholder="Misal: BCA" required>
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold uppercase tracking-widest text-primary-500">Nomor Rekening</label>
                                <input type="text" name="bank_account" value="{{ old('bank_account', $user->bank_account) }}" class="w-full px-4 py-3 bg-primary-50/50 border border-primary-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-black outline-none transition-all font-mono" placeholder="1234567890" required>
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold uppercase tracking-widest text-primary-500">Nama Pemilik Rekening</label>
                                <input type="text" name="bank_account_name" value="{{ old('bank_account_name', $user->bank_account_name) }}" class="w-full px-4 py-3 bg-primary-50/50 border border-primary-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-black outline-none transition-all uppercase" placeholder="Sesuai buku tabungan" required>
                            </div>
                        </div>
                        <div class="pt-4 flex justify-end border-t border-primary-100">
                            <button type="submit" class="bg-black text-white px-8 py-3 rounded-xl text-sm font-bold tracking-widest uppercase hover:bg-primary-900 hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                                Simpan Rekening
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Tab: Preferensi -->
                <div x-show="activeTab === 'preferences'" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-cloak class="bg-white dark:bg-onyx-700 p-8 md:p-10 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-primary-100 dark:border-onyx-600">
                    
                    <h2 class="text-2xl font-bold text-primary-900 mb-8 flex items-center gap-3">
                        <i data-lucide="sliders" class="text-primary-400"></i> Preferensi Sistem
                    </h2>
                    
                    <form action="{{ route('settings.preferences') }}" method="POST" class="space-y-8">
                        @csrf
                        @method('PUT')
                        @php $settings = $user->settings ?? []; @endphp

                        <!-- Tema Tampilan -->
                        <div>
                            <h3 class="text-sm font-bold uppercase tracking-widest text-primary-900 mb-4">Mode Tampilan Web</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <label class="relative cursor-pointer" @click="darkMode = false">
                                    <input type="radio" name="theme" value="light" class="peer sr-only" {{ ($settings['theme'] ?? 'light') === 'light' ? 'checked' : '' }}>
                                    <div class="p-5 border-2 rounded-xl transition-all peer-checked:border-black peer-checked:bg-primary-50 hover:bg-primary-50 border-primary-200 bg-white dark:bg-onyx-800 dark:border-onyx-600 dark:peer-checked:border-white">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-12 h-12 rounded-full bg-primary-100 flex items-center justify-center text-primary-900">
                                                <i data-lucide="sun" class="w-6 h-6"></i>
                                            </div>
                                            <span class="font-bold text-sm">Mode Terang</span>
                                        </div>
                                    </div>
                                    <div class="absolute top-3 right-3 text-black opacity-0 peer-checked:opacity-100 transition-opacity">
                                        <i data-lucide="check-circle-2" class="w-5 h-5 fill-white"></i>
                                    </div>
                                </label>
                                
                                <label class="relative cursor-pointer" @click="darkMode = true">
                                    <input type="radio" name="theme" value="dark" class="peer sr-only" {{ ($settings['theme'] ?? 'light') === 'dark' ? 'checked' : '' }}>
                                    <div class="p-5 border-2 rounded-xl transition-all peer-checked:border-black peer-checked:bg-primary-900 hover:bg-primary-50 border-primary-200 bg-white dark:bg-onyx-800 dark:border-onyx-600 dark:peer-checked:border-white">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-12 h-12 rounded-full bg-black flex items-center justify-center text-white">
                                                <i data-lucide="moon" class="w-6 h-6"></i>
                                            </div>
                                            <span class="font-bold text-sm peer-checked:text-white transition-colors">Mode Gelap</span>
                                        </div>
                                    </div>
                                    <div class="absolute top-3 right-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity">
                                        <i data-lucide="check-circle-2" class="w-5 h-5 fill-black"></i>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="border-t border-primary-100 my-8"></div>

                        <!-- Notifikasi Email -->
                        <div>
                            <h3 class="text-sm font-bold uppercase tracking-widest text-primary-900 mb-4">Notifikasi Email</h3>
                            <div class="space-y-4">
                                <label class="flex items-center justify-between p-4 border border-primary-200 rounded-xl hover:bg-primary-50 transition-colors cursor-pointer">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-600">
                                            <i data-lucide="newspaper" class="w-5 h-5"></i>
                                        </div>
                                        <div>
                                            <span class="block text-sm font-bold text-primary-900">Promo & Koleksi Baru</span>
                                            <span class="block text-xs text-primary-500 mt-0.5">Dapatkan email diskon dan berita eksklusif.</span>
                                        </div>
                                    </div>
                                    <div class="relative">
                                        <input type="checkbox" name="email_notifications" value="1" class="sr-only peer" {{ isset($settings['email_notifications']) && $settings['email_notifications'] ? 'checked' : '' }}>
                                        <div class="w-11 h-6 bg-primary-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-black"></div>
                                    </div>
                                </label>

                                <label class="flex items-center justify-between p-4 border border-primary-200 rounded-xl hover:bg-primary-50 transition-colors cursor-pointer">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-600">
                                            <i data-lucide="package" class="w-5 h-5"></i>
                                        </div>
                                        <div>
                                            <span class="block text-sm font-bold text-primary-900">Pembaruan Status Pesanan</span>
                                            <span class="block text-xs text-primary-500 mt-0.5">Terima struk dan resi langsung ke email.</span>
                                        </div>
                                    </div>
                                    <div class="relative">
                                        <input type="checkbox" name="order_updates" value="1" class="sr-only peer" {{ isset($settings['order_updates']) && $settings['order_updates'] ? 'checked' : '' }}>
                                        <div class="w-11 h-6 bg-primary-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-black"></div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="pt-6 flex justify-end">
                            <button type="submit" class="bg-black text-white px-8 py-3 rounded-xl text-sm font-bold tracking-widest uppercase hover:bg-primary-900 hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                                Simpan Preferensi
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
    /* Hide scrollbar for sidebar on mobile */
    .hide-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .hide-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
@endsection
