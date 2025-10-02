@props(['variant' => 'info', 'title' => null])
@php
    $colors = [
        'info' => 'bg-blue-50 border-l-4 border-primary text-foreground',
        'success' => 'bg-green-50 border-l-4 border-success text-foreground',
        'warning' => 'bg-yellow-50 border-l-4 border-warning text-foreground',
        'error' => 'bg-red-50 border-l-4 border-destructive text-foreground',
    ];
    $icon = [
        'info' => 'ℹ',
        'success' => '✓',
        'warning' => '⚠',
        'error' => '✕',
    ][$variant] ?? 'ℹ';
@endphp
<div {{ $attributes->merge(['class' => ($colors[$variant] ?? $colors['info']).' p-4 rounded-lg']) }}>
    <div class="flex items-start gap-3">
        <span class="text-lg">{{ $icon }}</span>
        <div>
            @if($title)
                <h4 class="font-semibold capitalize">{{ $title }}</h4>
            @endif
            <div class="text-sm">{{ $slot }}</div>
        </div>
    </div>
    </div>



