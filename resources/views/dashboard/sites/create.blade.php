@extends('layouts.ui')

@section('title', 'Adicionar Site - SitePulse Widgets')

@section('page-header')
    <div class="flex items-center justify-between border-b border-border pb-4 mb-6">
        <div>
            <h1 class="text-2xl font-semibold">Adicionar Novo Site</h1>
            <p class="text-muted-foreground mt-1">Configure um novo site para começar a coletar dados e feedback</p>
        </div>
        <div class="flex items-center gap-2">
            <x-ui.button variant="outline" href="{{ route('sites.index') }}">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Voltar
            </x-ui.button>
        </div>
    </div>
@endsection

@section('content')
<div class="w-full">
    <x-ui.card>
        <div class="p-6">
            <form method="POST" action="{{ route('sites.store') }}" class="space-y-6">
                @csrf
                
                <!-- Site Name -->
                <div class="space-y-2">
                    <label for="name" class="block text-sm font-medium text-foreground">
                        Nome do Site <span class="text-destructive">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}" 
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
                           value="{{ old('domain') }}" 
                           class="w-full px-3 py-2 border border-input rounded-lg focus:outline-none focus:ring-2 focus:ring-ring @error('domain') border-destructive @enderror" 
                           placeholder="exemplo.com"
                           required>
                    <p class="text-sm text-muted-foreground">Digite seu domínio sem http:// ou https://</p>
                    @error('domain')
                        <p class="text-sm text-destructive">{{ $message }}</p>
                    @enderror
                </div>
                
                
                <!-- Form Actions -->
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-border">
                    <x-ui.button variant="outline" href="{{ route('sites.index') }}">
                        Cancelar
                    </x-ui.button>
                    <x-ui.button type="submit" variant="primary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Criar Site
                    </x-ui.button>
                </div>
            </form>
        </div>
    </x-ui.card>
</div>
@endsection
