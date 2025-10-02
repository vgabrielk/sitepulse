@props([
    'variant' => 'primary', // primary, secondary, success, warning, destructive, outline, muted
    'size' => 'md', // sm, md, lg
    'href' => null,
    'type' => 'button'
])

@php
    $base = 'inline-flex items-center justify-center font-medium rounded-lg transition focus:outline-none focus:ring-2 focus:ring-ring disabled:opacity-50 disabled:pointer-events-none';
    $sizes = [
        'sm' => 'px-3 py-1.5 text-sm',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-5 py-3 text-base',
    ];
    $variants = [
        'primary' => 'bg-primary text-primary-foreground hover:opacity-90',
        'secondary' => 'bg-secondary text-secondary-foreground hover:opacity-90',
        'success' => 'bg-success text-success-foreground hover:opacity-90',
        'warning' => 'bg-warning text-warning-foreground hover:opacity-90',
        'destructive' => 'bg-destructive text-destructive-foreground hover:opacity-90',
        'outline' => 'border border-border hover:bg-muted text-foreground',
        'muted' => 'bg-muted text-muted-foreground',
    ];
    $classes = $base.' '.($sizes[$size] ?? $sizes['md']).' '.($variants[$variant] ?? $variants['primary']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif



