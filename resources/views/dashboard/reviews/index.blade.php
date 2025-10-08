@extends('layouts.ui')

@section('title', 'Reviews - SitePulse Widgets')

@section('page-header')
    <div class="flex items-center justify-between border-b border-border pb-4 mb-6">
        <div>
            <h1 class="text-2xl font-semibold">Reviews</h1>
            <p class="text-muted-foreground mt-1">Selecione um site para visualizar e gerenciar suas reviews</p>
        </div>
        <div class="flex items-center gap-2">
            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
            </svg>
        </div>
    </div>
@endsection

@section('content')
<div class="w-full">
    @if($sites->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($sites as $site)
                <x-ui.card class="hover:shadow-lg transition-all duration-200 cursor-pointer group" onclick="window.location.href='{{ route('reviews.site', $site) }}'">
                    <div class="p-6">
                        <!-- Site Header -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-primary rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-primary-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-foreground">{{ $site->name }}</h3>
                                    <p class="text-sm text-muted-foreground">{{ $site->domain }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-ui.badge variant="{{ $site->is_active ? 'success' : 'destructive' }}">
                                    {{ $site->is_active ? 'Ativo' : 'Inativo' }}
                                </x-ui.badge>
                            </div>
                        </div>

                        <!-- Estatísticas de Reviews -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="text-center p-3 bg-muted rounded-lg">
                                <div class="text-2xl font-bold text-primary">{{ $site->reviews_count ?? 0 }}</div>
                                <div class="text-xs text-muted-foreground">Total de Reviews</div>
                            </div>
                            <div class="text-center p-3 bg-muted rounded-lg">
                                <div class="text-2xl font-bold text-success">
                                    {{ $site->approved_reviews_count }}
                                </div>
                                <div class="text-xs text-muted-foreground">Aprovadas</div>
                            </div>
                        </div>

                        <!-- Reviews Pendentes -->
                        @php
                            $pendingCount = $site->pending_reviews_count;
                        @endphp
                        @if($pendingCount > 0)
                            <div class="flex items-center gap-2 p-3 bg-warning/10 border border-warning/20 rounded-lg mb-4">
                                <svg class="w-4 h-4 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                                <span class="text-sm font-medium text-warning">{{ $pendingCount }} review(s) pendente(s)</span>
                            </div>
                        @endif

                        <!-- Action Button -->
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-muted-foreground">
                                Criado em {{ $site->created_at->format('d/m/Y') }}
                            </div>
                            <div class="flex items-center gap-2">
                                <x-ui.button variant="outline" size="sm" class="group-hover:bg-primary group-hover:text-primary-foreground transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                    Ver Reviews
                                </x-ui.button>
                            </div>
                        </div>
                    </div>
                </x-ui.card>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-16">
            <div class="w-24 h-24 bg-muted rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"/>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-foreground mb-2">Nenhum site encontrado</h3>
            <p class="text-muted-foreground mb-6">Você ainda não possui sites cadastrados. Crie seu primeiro site para começar a receber reviews.</p>
            <x-ui.button variant="primary" href="{{ route('sites.create') }}">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Criar Primeiro Site
            </x-ui.button>
        </div>
    @endif
</div>

<!-- Pagination -->
@if($sites->hasPages())
    <div class="mt-8">
        {{ $sites->links() }}
    </div>
@endif
@endsection