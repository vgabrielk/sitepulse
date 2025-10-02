@extends('dashboard.layout')

@section('title', $site->name . ' - SitePulse Widgets')
@section('page-title', $site->name)

@section('page-actions')
    <div class="flex items-center gap-2">
        <x-ui.button href="{{ route('sites.edit', $site) }}" variant="outline">Edit</x-ui.button>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    <x-ui.card>
        <div class="flex flex-col gap-4">
            <div class="flex items-start md:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 text-white flex items-center justify-center font-semibold shadow-md">
                        {{ strtoupper(mb_substr($site->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="text-xs text-muted-foreground">Site</div>
                        <div class="text-xl md:text-2xl font-semibold leading-tight">{{ $site->name }}</div>
                        <a href="{{ Str::startsWith($site->domain, ['http://','https://']) ? $site->domain : 'https://'.$site->domain }}" target="_blank" class="text-sm text-primary underline break-all">
                            {{ $site->domain }}
                        </a>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <x-ui.badge variant="{{ $site->is_active ? 'success' : 'muted' }}">{{ $site->is_active ? 'Ativo' : 'Inativo' }}</x-ui.badge>
                    <form method="POST" action="{{ route('sites.toggle-status', $site) }}">
                        @csrf
                        <x-ui.button type="submit" variant="outline">{{ $site->is_active ? 'Desativar' : 'Ativar' }}</x-ui.button>
                    </form>
                    <x-ui.button href="{{ route('sites.edit', $site) }}" variant="outline">Editar</x-ui.button>
                </div>
            </div>

            <div class="border-t border-border"></div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="rounded-lg border border-border p-3">
                    <div class="text-xs text-muted-foreground">ID do Widget</div>
                    <div class="mt-1 flex items-center justify-between gap-2">
                        <code id="widgetIdText" class="text-sm break-all">{{ $site->widget_id }}</code>
                        <x-ui.button type="button" size="sm" variant="outline" onclick="copyWidgetId()">Copiar</x-ui.button>
                    </div>
                </div>
                <div class="rounded-lg border border-border p-3">
                    <div class="text-xs text-muted-foreground">Criado em</div>
                    <div class="mt-1 font-medium">{{ $site->created_at->format('d/m/Y') }}</div>
                </div>
                <div class="rounded-lg border border-border p-3">
                    <div class="text-xs text-muted-foreground">Atualizado em</div>
                    <div class="mt-1 font-medium">{{ $site->updated_at->format('d/m/Y') }}</div>
                </div>
            </div>
        </div>
    </x-ui.card>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <x-ui.card>
            <h5 class="text-base font-semibold mb-4">Informações do site</h5>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-muted-foreground"><strong>Nome:</strong> {{ $site->name }}</p>
                    <p class="text-sm text-muted-foreground"><strong>Domínio:</strong> 
                        <a href="{{ Str::startsWith($site->domain, ['http://','https://']) ? $site->domain : 'https://'.$site->domain }}" target="_blank" class="underline">{{ $site->domain }}</a>
                    </p>
                    <p class="text-sm text-muted-foreground"><strong>Status:</strong> 
                        @if($site->is_active)
                            <x-ui.badge variant="success">Ativo</x-ui.badge>
                        @else
                            <x-ui.badge variant="muted">Inativo</x-ui.badge>
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-sm text-muted-foreground flex items-center gap-2"><strong>ID do Widget:</strong> <code id="widgetIdText">{{ $site->widget_id }}</code> <x-ui.button type="button" size="sm" variant="outline" onclick="copyWidgetId()">Copiar</x-ui.button></p>
                    <p class="text-sm text-muted-foreground"><strong>Criado em:</strong> {{ $site->created_at->format('d/m/Y') }}</p>
                    <p class="text-sm text-muted-foreground"><strong>Atualizado em:</strong> {{ $site->updated_at->format('d/m/Y') }}</p>
                </div>
            </div>
            <div class="mt-4">
                <h6 class="font-semibold mb-2">Configurações</h6>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div class="rounded-lg border border-border p-3 flex items-center justify-between">
                        <div class="text-sm">IP anonimizado</div>
                        <x-ui.badge variant="{{ $site->anonymize_ips ? 'success' : 'muted' }}">{{ $site->anonymize_ips ? 'Ativo' : 'Inativo' }}</x-ui.badge>
                    </div>
                    <div class="rounded-lg border border-border p-3 flex items-center justify-between">
                        <div class="text-sm">Eventos</div>
                        <x-ui.badge variant="{{ $site->track_events ? 'success' : 'muted' }}">{{ $site->track_events ? 'Ativo' : 'Inativo' }}</x-ui.badge>
                    </div>
                    <div class="rounded-lg border border-border p-3 flex items-center justify-between">
                        <div class="text-sm">Feedback</div>
                        <x-ui.badge variant="{{ $site->collect_feedback ? 'success' : 'muted' }}">{{ $site->collect_feedback ? 'Habilitado' : 'Desabilitado' }}</x-ui.badge>
                    </div>
                </div>
            </div>
        </x-ui.card>

        {{-- Embeds removidos desta página conforme solicitado --}}
    </div>

    <div class="space-y-6">
        <x-ui.card>
            <h5 class="text-base font-semibold mb-4">Estatísticas (últimos 30 dias)</h5>
            <div class="grid grid-cols-2 gap-4 text-center">
                <div>
                    <div class="text-xs text-muted-foreground">Sessões</div>
                    <div class="text-xl text-primary font-semibold">{{ $stats['sessions'] ?? 0 }}</div>
                </div>
                <div>
                    <div class="text-xs text-muted-foreground">Visitas</div>
                    <div class="text-xl text-success font-semibold">{{ $stats['visits'] ?? 0 }}</div>
                </div>
                <div>
                    <div class="text-xs text-muted-foreground">Eventos</div>
                    <div class="text-xl text-primary font-semibold">{{ $stats['events'] ?? 0 }}</div>
                </div>
                <div>
                    <div class="text-xs text-muted-foreground">Visitantes únicos</div>
                    <div class="text-xl text-warning font-semibold">{{ $stats['unique_visitors'] ?? 0 }}</div>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card>
            <h5 class="text-base font-semibold mb-4">Ações rápidas</h5>
            <div class="grid gap-2">
                <x-ui.button href="{{ route('reviews.site', $site) }}" variant="outline">Ver Reviews</x-ui.button>
                <x-ui.button href="{{ route('sites.faq.index', $site) }}" variant="outline">Gerenciar FAQ</x-ui.button>
                <x-ui.button href="{{ route('sites.customize', $site) }}" variant="outline">Personalizar Reviews</x-ui.button>
            </div>
        </x-ui.card>
    </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function copySiblingTextarea(button){
    const textarea = button.previousElementSibling;
    if(!textarea) return;
    textarea.select();
    document.execCommand('copy');
    showToast('success', 'Copied to clipboard');
}

function copyWidgetId(){
    var el = document.getElementById('widgetIdText');
    if(!el) return;
    var txt = el.textContent;
    navigator.clipboard.writeText(txt).then(function(){
        try { showToast && showToast('success', 'ID do widget copiado'); } catch(e) {}
    });
}
</script>
@endpush
