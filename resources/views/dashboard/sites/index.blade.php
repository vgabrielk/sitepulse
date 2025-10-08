@extends('dashboard.layout')

@section('title', 'Sites - SitePulse Widgets')
@section('page-title', 'Sites')

@section('page-actions')
    <x-ui.button href="{{ route('sites.create') }}">
        Adicionar Site
    </x-ui.button>
@endsection

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @if(count($sites) > 0)
        @foreach($sites as $site)
            <x-ui.card class="h-full hover:shadow-lg transition-shadow duration-200 hover:border-primary/40">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-primary/20 to-primary/40 text-primary flex items-center justify-center font-semibold">
                            {{ strtoupper(mb_substr($site->name, 0, 1)) }}
                        </div>
                        <h5 class="text-lg font-semibold leading-tight">{{ $site->name }}</h5>
                    </div>
                    @if($site->is_active)
                        <x-ui.badge variant="success">Ativo</x-ui.badge>
                    @else
                        <x-ui.badge variant="muted">Inativo</x-ui.badge>
                    @endif
                </div>
                <div class="space-y-1">
                    <p class="text-sm text-muted-foreground"><strong>Domínio:</strong> {{ $site->domain }}</p>
                    <p class="text-sm text-muted-foreground"><strong>ID do Widget:</strong> <code>{{ $site->widgetId }}</code></p>
                </div>
                <div class="grid grid-cols-3 gap-2 mt-4">
                    <div class="rounded-md bg-primary/5 p-2 text-center">
                        <div class="text-[10px] text-muted-foreground">Sessões</div>
                        <div class="font-semibold text-primary flex items-center justify-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-5M12 20v-8M7 20v-4M2 20v-2"/></svg>
                            {{ $site->sessions_count }}
                        </div>
                    </div>
                    <div class="rounded-md bg-success/5 p-2 text-center">
                        <div class="text-[10px] text-muted-foreground">Visitas</div>
                        <div class="font-semibold text-success flex items-center justify-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            {{ $site->visits_count }}
                        </div>
                    </div>
                    <div class="rounded-md bg-warning/5 p-2 text-center">
                        <div class="text-[10px] text-muted-foreground">Eventos</div>
                        <div class="font-semibold text-warning flex items-center justify-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            {{ $site->events_count }}
                        </div>
                    </div>
                </div>
                <div class="mt-4 flex gap-2">
                    <x-ui.button href="{{ route('sites.show', $site->id) }}" variant="outline" size="sm">Ver</x-ui.button>
                    <x-ui.button href="{{ route('sites.edit', $site->id) }}" variant="outline" size="sm">Editar</x-ui.button>
                    <form method="POST" action="{{ route('sites.toggle-status', $site->id) }}">
                        @csrf
                        <x-ui.button type="submit" variant="outline" size="sm">{{ $site->is_active ? 'Pausar' : 'Ativar' }}</x-ui.button>
                    </form>
                    <form method="POST" action="{{ route('sites.destroy', $site->id) }}" onsubmit="return confirm('Tem certeza de que deseja excluir este site?')">
                        @csrf
                        @method('DELETE')
                        <x-ui.button type="submit" variant="destructive" size="sm">Excluir</x-ui.button>
                    </form>
                </div>
            </x-ui.card>
        @endforeach
    @else
        <x-ui.card class="col-span-full text-center p-12 hover:shadow-lg transition-shadow duration-200">
            <svg class="w-12 h-12 mx-auto text-muted-foreground mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 1.657-1.343 3-3 3S6 12.657 6 11s1.343-3 3-3 3 1.343 3 3z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2 12s3-6 10-6 10 6 10 6-3 6-10 6S2 12 2 12z"/></svg>
            <h3 class="text-lg font-semibold mb-2">Nenhum site encontrado</h3>
            <p class="text-muted-foreground mb-4">Comece adicionando seu primeiro site para incorporar widgets.</p>
            <x-ui.button href="{{ route('sites.create') }}">Adicionar seu primeiro site</x-ui.button>
        </x-ui.card>
    @endif
</div>

<!-- Pagination -->
@if($sites->hasPages())
    <div class="mt-8">
        {{ $sites->links() }}
    </div>
@endif
@endsection
