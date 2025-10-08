@extends('layouts.ui')

@section('title', 'Editar ' . $site->name . ' - SitePulse Widgets')

@section('page-header')
    <div class="flex items-center justify-between border-b border-border pb-4 mb-6">
        <div>
            <h1 class="text-2xl font-semibold">Editar Site</h1>
            <p class="text-muted-foreground mt-1">Atualize as informações do seu site</p>
        </div>
        <div class="flex items-center gap-2">
            <x-ui.button variant="outline" href="{{ route('sites.show', $site) }}">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Voltar ao Site
            </x-ui.button>
        </div>
    </div>
@endsection

@section('content')
<div class="w-full">
    <x-ui.card>
        <div class="p-6">
            <!-- Site Info Header -->
            <div class="flex items-center gap-4 mb-6 pb-4 border-b border-border">
                <div class="w-12 h-12 bg-primary rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-semibold">{{ $site->name }}</h2>
                    <p class="text-muted-foreground">{{ $site->domain }}</p>
                    <div class="flex items-center gap-2 mt-1">
                        <x-ui.badge variant="{{ $site->is_active ? 'success' : 'destructive' }}">
                            {{ $site->is_active ? 'Ativo' : 'Inativo' }}
                        </x-ui.badge>
                        <span class="text-xs text-muted-foreground">
                            Criado em {{ $site->created_at->format('d/m/Y') }}
                        </span>
                    </div>
                </div>
            </div>
            
            <form method="POST" action="{{ route('sites.update', $site) }}" class="space-y-6">
                @csrf
                @method('PUT')
                
                <!-- Basic Information -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-foreground">Informações Básicas</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div class="space-y-2">
                            <label for="name" class="block text-sm font-medium text-foreground">
                                Nome do Site <span class="text-destructive">*</span>
                            </label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $site->name) }}" 
                                   class="w-full px-3 py-2 border border-input rounded-lg focus:outline-none focus:ring-2 focus:ring-ring @error('name') border-destructive @enderror" 
                                   placeholder="Ex: Meu Site Principal"
                                   required>
                            @error('name')
                                <p class="text-sm text-destructive">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Domain -->
                        <div class="space-y-2">
                            <label for="domain" class="block text-sm font-medium text-foreground">
                                Domínio <span class="text-destructive">*</span>
                            </label>
                            <input type="text" 
                                   id="domain" 
                                   name="domain" 
                                   value="{{ old('domain', $site->domain) }}" 
                                   class="w-full px-3 py-2 border border-input rounded-lg focus:outline-none focus:ring-2 focus:ring-ring @error('domain') border-destructive @enderror" 
                                   placeholder="exemplo.com"
                                   required>
                            <p class="text-sm text-muted-foreground">Digite seu domínio sem http:// ou https://</p>
                            @error('domain')
                                <p class="text-sm text-destructive">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <!-- Settings Section -->
                <div class="space-y-4 pt-6 border-t border-border">
                    <h3 class="text-lg font-medium text-foreground">Configurações</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Anonymize IPs -->
                        <div class="space-y-3">
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="checkbox" 
                                       id="anonymize_ips" 
                                       name="anonymize_ips" 
                                       value="1" 
                                       {{ old('anonymize_ips', $site->anonymize_ips) ? 'checked' : '' }}
                                       class="w-4 h-4 text-primary bg-background border-input rounded focus:ring-ring focus:ring-2">
                                <div>
                                    <div class="text-sm font-medium text-foreground">Anonimizar IPs</div>
                                    <div class="text-xs text-muted-foreground">Protege a privacidade dos visitantes</div>
                                </div>
                            </label>
                        </div>
                        
                        <!-- Track Events -->
                        <div class="space-y-3">
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="checkbox" 
                                       id="track_events" 
                                       name="track_events" 
                                       value="1" 
                                       {{ old('track_events', $site->track_events) ? 'checked' : '' }}
                                       class="w-4 h-4 text-primary bg-background border-input rounded focus:ring-ring focus:ring-2">
                                <div>
                                    <div class="text-sm font-medium text-foreground">Rastrear Eventos</div>
                                    <div class="text-xs text-muted-foreground">Monitora interações dos usuários</div>
                                </div>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Collect Feedback -->
                    <div class="space-y-3">
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input type="checkbox" 
                                   id="collect_feedback" 
                                   name="collect_feedback" 
                                   value="1" 
                                   {{ old('collect_feedback', $site->collect_feedback) ? 'checked' : '' }}
                                   class="w-4 h-4 text-primary bg-background border-input rounded focus:ring-ring focus:ring-2">
                            <div>
                                <div class="text-sm font-medium text-foreground">Coletar Feedback</div>
                                <div class="text-xs text-muted-foreground">Permite que visitantes deixem avaliações e comentários</div>
                            </div>
                        </label>
                    </div>
                </div>
                
                <!-- Form Actions -->
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-border">
                    <x-ui.button variant="outline" href="{{ route('sites.show', $site) }}">
                        Cancelar
                    </x-ui.button>
                    <x-ui.button type="submit" variant="primary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Salvar Alterações
                    </x-ui.button>
                </div>
            </form>
        </div>
    </x-ui.card>
</div>
@endsection