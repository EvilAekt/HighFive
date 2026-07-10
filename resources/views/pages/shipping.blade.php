@extends('layouts.app')

@section('title', 'Kebijakan Pengiriman - HIGH FIVE')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-16 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-primary-900 mb-8 text-center">Kebijakan Pengiriman</h1>
    
    <div class="prose prose-primary max-w-none text-primary-600">
        <div class="bg-white p-8 border border-primary-200">
            <h2 class="text-xl font-semibold text-primary-900 mb-4">Waktu Proses</h2>
            <p class="mb-6">
                Semua pesanan yang masuk dan telah dibayar sebelum pukul 15.00 WIB (Senin - Jumat) akan diproses dan dikirim pada hari yang sama. Pesanan yang masuk setelah waktu tersebut atau pada hari libur akan diproses pada hari kerja berikutnya.
            </p>

            <h2 class="text-xl font-semibold text-primary-900 mb-4">Estimasi Pengiriman (Dari Yogyakarta)</h2>
            <ul class="list-disc pl-5 mb-6 space-y-2">
                <li><strong>Yogyakarta & Sekitarnya:</strong> 1-2 hari kerja</li>
                <li><strong>Jawa Tengah & Jawa Timur:</strong> 2-3 hari kerja</li>
                <li><strong>Jabodetabek & Jawa Barat:</strong> 3-4 hari kerja</li>
                <li><strong>Luar Pulau Jawa:</strong> 4-7 hari kerja</li>
            </ul>

            <h2 class="text-xl font-semibold text-primary-900 mb-4">Lacak Pesanan</h2>
            <p>
                Setelah pesanan Anda dikirim, Anda akan menerima email berisi nomor resi pengiriman. Anda juga dapat melacak status pesanan secara langsung melalui dashboard akun Anda di menu "Pesanan Saya".
            </p>
        </div>
    </div>
</div>
@endsection
