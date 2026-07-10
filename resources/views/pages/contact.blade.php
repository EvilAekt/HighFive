@extends('layouts.app')

@section('title', 'Hubungi Kami - HIGH FIVE')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-16 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-primary-900 mb-8 text-center">Hubungi Kami</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Contact Info -->
        <div class="bg-primary-50 p-8 border border-primary-200">
            <h2 class="text-xl font-semibold text-primary-900 mb-6">Informasi Kontak</h2>
            
            <div class="space-y-6">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center flex-shrink-0 shadow-sm">
                        <i data-lucide="mail" class="w-5 h-5 text-primary-600"></i>
                    </div>
                    <div>
                        <h3 class="font-medium text-primary-900">Email</h3>
                        <p class="text-sm text-primary-600 mt-1">hello@highfive.id</p>
                        <p class="text-xs text-primary-400 mt-1">Kami akan membalas dalam 24 jam</p>
                    </div>
                </div>

                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center flex-shrink-0 shadow-sm">
                        <i data-lucide="message-circle" class="w-5 h-5 text-primary-600"></i>
                    </div>
                    <div>
                        <h3 class="font-medium text-primary-900">WhatsApp</h3>
                        <p class="text-sm text-primary-600 mt-1">+62 812 3456 7890</p>
                        <p class="text-xs text-primary-400 mt-1">Senin - Jumat, 09.00 - 17.00 WIB</p>
                    </div>
                </div>

                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center flex-shrink-0 shadow-sm">
                        <i data-lucide="map-pin" class="w-5 h-5 text-primary-600"></i>
                    </div>
                    <div>
                        <h3 class="font-medium text-primary-900">Operasional</h3>
                        <p class="text-sm text-primary-600 mt-1">Berbasis Online di Yogyakarta<br>Daerah Istimewa Yogyakarta</p>
                        <p class="text-xs text-primary-400 mt-1">Hanya Melayani Pembelian Online</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="bg-white p-8 border border-primary-200 shadow-sm">
            <h2 class="text-xl font-semibold text-primary-900 mb-6">Kirim Pesan</h2>
            
            <form onsubmit="event.preventDefault(); alert('Pesan berhasil terkirim! Tim kami akan membalas via email Anda.'); this.reset();" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Nama Lengkap</label>
                    <input type="text" class="input-field" required placeholder="Nama Anda" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Email</label>
                    <input type="email" class="input-field" required placeholder="email@contoh.com" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Subjek</label>
                    <input type="text" class="input-field" required placeholder="Pertanyaan tentang pesanan" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Pesan</label>
                    <textarea rows="4" class="input-field" required placeholder="Tuliskan pesan Anda di sini..."></textarea>
                </div>
                <button type="submit" class="w-full btn-primary py-3">
                    Kirim Pesan
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
