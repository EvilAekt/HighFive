@extends('layouts.app')

@section('title', 'Kebijakan Pengembalian - HIGH FIVE')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-16 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-primary-900 mb-8 text-center">Kebijakan Pengembalian</h1>
    
    <div class="prose prose-primary max-w-none text-primary-600">
        <div class="bg-white p-8 border border-primary-200">
            <h2 class="text-xl font-semibold text-primary-900 mb-4">Syarat Pengembalian (Return & Exchange)</h2>
            <p class="mb-4">Kami menerima pengembalian atau penukaran barang dengan syarat berikut:</p>
            <ul class="list-disc pl-5 mb-6 space-y-2">
                <li>Klaim dilakukan maksimal <strong>3 hari</strong> setelah barang diterima.</li>
                <li>Barang belum pernah dicuci atau dipakai (kecuali untuk mencoba ukuran).</li>
                <li>Tag (label) produk masih terpasang dengan baik.</li>
                <li>Wajib menyertakan video unboxing sebagai bukti jika barang cacat/salah kirim.</li>
            </ul>

            <h2 class="text-xl font-semibold text-primary-900 mb-4">Prosedur Pengembalian</h2>
            <ol class="list-decimal pl-5 mb-6 space-y-2">
                <li>Hubungi Customer Service kami melalui halaman <a href="{{ route('page.contact') }}" class="text-black underline">Hubungi Kami</a>.</li>
                <li>Kirimkan nomor pesanan dan video unboxing.</li>
                <li>Tim kami akan melakukan verifikasi dan memberikan alamat retur.</li>
                <li>Kirimkan barang kembali ke gudang kami (ongkos kirim ditanggung pembeli, kecuali jika kesalahan dari pihak kami).</li>
            </ol>

            <h2 class="text-xl font-semibold text-primary-900 mb-4">Refund Dana</h2>
            <p>
                Jika Anda memilih refund, dana akan dikembalikan ke metode pembayaran awal dalam waktu 3-5 hari kerja setelah barang retur kami terima dan periksa.
            </p>
        </div>
    </div>
</div>
@endsection
