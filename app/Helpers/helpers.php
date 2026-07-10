<?php

if (!function_exists('formatPrice')) {
    function formatPrice($amount)
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}

if (!function_exists('formatDate')) {
    function formatDate($date)
    {
        if (!$date) return '';
        return \Carbon\Carbon::parse($date)->locale('id')->isoFormat('D MMMM YYYY, HH:mm');
    }
}

if (!function_exists('getStatusColor')) {
    function getStatusColor($status)
    {
        return match ($status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'processing' => 'bg-blue-100 text-blue-800',
            'shipped' => 'bg-purple-100 text-purple-800',
            'delivered', 'paid' => 'bg-green-100 text-green-800',
            'cancelled', 'failed' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}

if (!function_exists('getStatusLabel')) {
    function getStatusLabel($status)
    {
        return match ($status) {
            'pending' => 'Menunggu',
            'processing' => 'Diproses',
            'shipped' => 'Dikirim',
            'delivered' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            'paid' => 'Dibayar',
            'failed' => 'Gagal',
            default => ucfirst($status),
        };
    }
}
