@extends('layouts.app')

@section('title', 'FAQ - HIGH FIVE')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-16 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-primary-900 mb-8 text-center">Frequently Asked Questions</h1>
    
    <div class="space-y-6" x-data="{ active: null }">
        <!-- Question 1 -->
        <div class="border border-primary-200 bg-white">
            <button @click="active = active === 1 ? null : 1" class="w-full px-6 py-4 flex justify-between items-center text-left focus:outline-none">
                <span class="font-medium text-primary-900">Apakah produk HIGH FIVE selalu tersedia?</span>
                <i data-lucide="chevron-down" class="w-5 h-5 text-primary-500 transition-transform duration-300" :class="{'rotate-180': active === 1}"></i>
            </button>
            <div x-show="active === 1" x-collapse>
                <div class="px-6 pb-4 text-primary-600 text-sm leading-relaxed">
                    Kami memproduksi koleksi dalam jumlah terbatas untuk menjaga eksklusivitas. Jika sebuah produk habis, kami mungkin tidak memproduksinya kembali dalam waktu dekat.
                </div>
            </div>
        </div>

        <!-- Question 2 -->
        <div class="border border-primary-200 bg-white">
            <button @click="active = active === 2 ? null : 2" class="w-full px-6 py-4 flex justify-between items-center text-left focus:outline-none">
                <span class="font-medium text-primary-900">Bagaimana cara menentukan ukuran yang tepat?</span>
                <i data-lucide="chevron-down" class="w-5 h-5 text-primary-500 transition-transform duration-300" :class="{'rotate-180': active === 2}"></i>
            </button>
            <div x-show="active === 2" x-collapse>
                <div class="px-6 pb-4 text-primary-600 text-sm leading-relaxed">
                    Setiap produk memiliki panduan ukuran (Size Chart) pada halaman deskripsinya. Kami sarankan Anda mengukur pakaian yang paling pas dengan Anda dan mencocokkannya dengan panduan kami.
                </div>
            </div>
        </div>

        <!-- Question 3 -->
        <div class="border border-primary-200 bg-white">
            <button @click="active = active === 3 ? null : 3" class="w-full px-6 py-4 flex justify-between items-center text-left focus:outline-none">
                <span class="font-medium text-primary-900">Apakah saya bisa mengubah pesanan setelah checkout?</span>
                <i data-lucide="chevron-down" class="w-5 h-5 text-primary-500 transition-transform duration-300" :class="{'rotate-180': active === 3}"></i>
            </button>
            <div x-show="active === 3" x-collapse>
                <div class="px-6 pb-4 text-primary-600 text-sm leading-relaxed">
                    Pesanan yang sudah dibayar akan langsung masuk ke sistem pemrosesan kami untuk memastikan pengiriman cepat. Oleh karena itu, pesanan tidak dapat diubah setelah pembayaran berhasil.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
