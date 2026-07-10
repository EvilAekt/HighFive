@props(['status', 'size' => 'md'])

@php
$sizeClass = $size === 'sm' ? 'text-xs px-2 py-0.5' : 'text-sm px-3 py-1';
$colorClass = getStatusColor($status);
$label = getStatusLabel($status);
@endphp

<span class="inline-flex items-center font-semibold uppercase tracking-wider rounded {{ $colorClass }} {{ $sizeClass }}">
    {{ $label }}
</span>
