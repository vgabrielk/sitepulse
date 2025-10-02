@props(['variant' => 'primary'])
@php
    $styles = [
        'primary' => 'bg-primary text-primary-foreground',
        'secondary' => 'bg-secondary text-secondary-foreground',
        'success' => 'bg-success text-success-foreground',
        'warning' => 'bg-warning text-warning-foreground',
        'destructive' => 'bg-destructive text-destructive-foreground',
        'muted' => 'bg-muted text-muted-foreground',
        'outline' => 'border border-border text-foreground',
    ];
@endphp
<span {{ $attributes->merge(['class' => ($styles[$variant] ?? $styles['primary']).' text-xs font-medium px-2 py-1 rounded-full']) }}>
    {{ $slot }}
</span>



