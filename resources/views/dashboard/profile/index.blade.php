@extends('layouts.ui')

@section('title', 'Perfil - SitePulse Widgets')

@section('page-header')
    <div class="flex items-center justify-between border-b border-border pb-4 mb-6">
        <div>
            <h1 class="text-2xl font-semibold">Meu Perfil</h1>
            <p class="text-muted-foreground mt-1">Gerencie suas informações pessoais e configurações de conta</p>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-10 h-10 bg-primary rounded-full flex items-center justify-center">
                <span class="text-primary-foreground text-sm font-semibold">{{ substr($user->name, 0, 1) }}</span>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="w-full space-y-6">
    <!-- Profile Information Card -->
    <x-ui.card>
        <div class="p-6">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center">
                    <span class="text-primary-foreground text-xl font-semibold">{{ substr($user->name, 0, 1) }}</span>
                </div>
                <div>
                    <h2 class="text-xl font-semibold">{{ $user->name }}</h2>
                    <p class="text-muted-foreground">{{ $user->email }}</p>
                    @if($client->company)
                        <p class="text-sm text-muted-foreground">{{ $client->company }}</p>
                    @endif
                </div>
            </div>
            
            <form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
                @csrf
                
                <!-- Personal Information -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-foreground">Informações Pessoais</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div class="space-y-2">
                            <label for="name" class="block text-sm font-medium text-foreground">
                                Nome Completo <span class="text-destructive">*</span>
                            </label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $user->name) }}" 
                                   class="w-full px-3 py-2 border border-input rounded-lg focus:outline-none focus:ring-2 focus:ring-ring @error('name') border-destructive @enderror" 
                                   placeholder="Seu nome completo"
                                   required>
                            @error('name')
                                <p class="text-sm text-destructive">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Email -->
                        <div class="space-y-2">
                            <label for="email" class="block text-sm font-medium text-foreground">
                                Email <span class="text-destructive">*</span>
                            </label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $user->email) }}" 
                                   class="w-full px-3 py-2 border border-input rounded-lg focus:outline-none focus:ring-2 focus:ring-ring @error('email') border-destructive @enderror" 
                                   placeholder="seu@email.com"
                                   required>
                            @error('email')
                                <p class="text-sm text-destructive">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Company -->
                        <div class="space-y-2">
                            <label for="company" class="block text-sm font-medium text-foreground">
                                Empresa
                            </label>
                            <input type="text" 
                                   id="company" 
                                   name="company" 
                                   value="{{ old('company', $client->company) }}" 
                                   class="w-full px-3 py-2 border border-input rounded-lg focus:outline-none focus:ring-2 focus:ring-ring @error('company') border-destructive @enderror" 
                                   placeholder="Nome da sua empresa">
                            @error('company')
                                <p class="text-sm text-destructive">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Phone -->
                        <div class="space-y-2">
                            <label for="phone" class="block text-sm font-medium text-foreground">
                                Telefone
                            </label>
                            <input type="tel" 
                                   id="phone" 
                                   name="phone" 
                                   value="{{ old('phone', $client->phone) }}" 
                                   class="w-full px-3 py-2 border border-input rounded-lg focus:outline-none focus:ring-2 focus:ring-ring @error('phone') border-destructive @enderror" 
                                   placeholder="(11) 99999-9999">
                            @error('phone')
                                <p class="text-sm text-destructive">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <!-- Password Section -->
                <div class="space-y-4 pt-6 border-t border-border">
                    <h3 class="text-lg font-medium text-foreground">Alterar Senha</h3>
                    <p class="text-sm text-muted-foreground">Deixe em branco se não quiser alterar sua senha</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Current Password -->
                        <div class="space-y-2">
                            <label for="current_password" class="block text-sm font-medium text-foreground">
                                Senha Atual
                            </label>
                            <input type="password" 
                                   id="current_password" 
                                   name="current_password" 
                                   class="w-full px-3 py-2 border border-input rounded-lg focus:outline-none focus:ring-2 focus:ring-ring @error('current_password') border-destructive @enderror" 
                                   placeholder="Digite sua senha atual">
                            @error('current_password')
                                <p class="text-sm text-destructive">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- New Password -->
                        <div class="space-y-2">
                            <label for="password" class="block text-sm font-medium text-foreground">
                                Nova Senha
                            </label>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   class="w-full px-3 py-2 border border-input rounded-lg focus:outline-none focus:ring-2 focus:ring-ring @error('password') border-destructive @enderror" 
                                   placeholder="Digite sua nova senha">
                            @error('password')
                                <p class="text-sm text-destructive">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Confirm Password -->
                    <div class="space-y-2">
                        <label for="password_confirmation" class="block text-sm font-medium text-foreground">
                            Confirmar Nova Senha
                        </label>
                        <input type="password" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               class="w-full px-3 py-2 border border-input rounded-lg focus:outline-none focus:ring-2 focus:ring-ring" 
                               placeholder="Confirme sua nova senha">
                    </div>
                </div>
                
                <!-- Form Actions -->
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-border">
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
    
    <!-- Account Information Card -->
    <x-ui.card>
        <div class="p-6">
            <h3 class="text-lg font-medium text-foreground mb-4">Informações da Conta</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-sm font-medium text-muted-foreground">Plano Atual</label>
                    <div class="flex items-center gap-2">
                        <x-ui.badge variant="primary">{{ ucfirst($client->plan) }}</x-ui.badge>
                        @if($client->plan === 'free')
                            <x-ui.button variant="outline" size="sm" href="{{ route('billing') }}">
                                Fazer Upgrade
                            </x-ui.button>
                        @endif
                    </div>
                </div>
                
                <div class="space-y-2">
                    <label class="text-sm font-medium text-muted-foreground">Status da Conta</label>
                    <div class="flex items-center gap-2">
                        <x-ui.badge variant="{{ $client->is_active ? 'success' : 'destructive' }}">
                            {{ $client->is_active ? 'Ativa' : 'Inativa' }}
                        </x-ui.badge>
                    </div>
                </div>
                
                <div class="space-y-2">
                    <label class="text-sm font-medium text-muted-foreground">Membro Desde</label>
                    <p class="text-sm text-foreground">{{ $client->created_at->format('d/m/Y') }}</p>
                </div>
                
                <div class="space-y-2">
                    <label class="text-sm font-medium text-muted-foreground">Email Verificado</label>
                    <div class="flex items-center gap-2">
                        @if($user->hasVerifiedEmail())
                            <x-ui.badge variant="success">Verificado</x-ui.badge>
                        @else
                            <x-ui.badge variant="warning">Não Verificado</x-ui.badge>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </x-ui.card>
</div>
@endsection