@props(['rating', 'size' => 'md', 'interactive' => false, 'inputName' => 'rating'])

@php
$sizes = [
    'sm' => 'w-4 h-4',
    'md' => 'w-5 h-5',
    'lg' => 'w-6 h-6',
];
$sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

<div class="flex items-center gap-1" x-data="{ currentRating: {{ $rating ?? 0 }}, hoverRating: 0 }">
    @if($interactive)
        <input type="hidden" name="{{ $inputName }}" x-model="currentRating">
    @endif
    
    @foreach([1, 2, 3, 4, 5] as $value)
        <button
            type="button"
            @if($interactive)
                @click="currentRating = {{ $value }}"
                @mouseenter="hoverRating = {{ $value }}"
                @mouseleave="hoverRating = 0"
            @endif
            {{ !$interactive ? 'disabled' : '' }}
            class="transition-colors {{ $interactive ? 'cursor-pointer hover:scale-110' : 'cursor-default' }}"
        >
            <i data-lucide="star" 
               class="{{ $sizeClass }} text-primary-300"
               :class="{ 
                   'fill-yellow-400 text-yellow-400': {{ $interactive ? "hoverRating >= $value || (!hoverRating && currentRating >= $value)" : ($value <= ($rating ?? 0) ? 'true' : 'false') }},
                   'text-primary-300': !({{ $interactive ? "hoverRating >= $value || (!hoverRating && currentRating >= $value)" : ($value <= ($rating ?? 0) ? 'true' : 'false') }})
               }">
            </i>
        </button>
    @endforeach
</div>
