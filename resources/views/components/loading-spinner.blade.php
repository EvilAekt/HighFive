@props(['size' => 'md'])

@php
$sizes = [
    'sm' => 'w-4 h-4',
    'md' => 'w-8 h-8',
    'lg' => 'w-12 h-12',
];
$sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center justify-center']) }}>
    <i data-lucide="loader-2" class="animate-spin text-primary-400 {{ $sizeClass }}"></i>
</div>
