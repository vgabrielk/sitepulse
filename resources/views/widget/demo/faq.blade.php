@extends('layouts.blank')

@section('title', 'Widget - FAQ Inteligente')

@section('content')
<div class="w-full h-full">
    <div class="bg-card rounded-2xl shadow-xl p-6  h-full overflow-auto max-w-3xl mx-auto">
        <div class="divide-y divide-border">
            @foreach($faqs as $faq)
                <details class="py-3">
                    <summary class="cursor-pointer font-medium flex items-center justify-between">
                        <span>{{ $faq['q'] }}</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </summary>
                    <div class="mt-2 text-sm text-foreground">{{ $faq['a'] }}</div>
                </details>
            @endforeach
        </div>
        <div class="mt-4 text-xs text-muted-foreground">Versão demo</div>
    </div>
</div>
@endsection



