@extends('layouts.app')

@section('title', 'Redefinir Senha - SitePulse Widgets')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Redefinir senha
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Digite sua nova senha
            </p>
        </div>
        
        <form class="mt-8 space-y-6" method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            
            @if ($errors->any())
                <x-ui.alert variant="error" title="Erros na submissão">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-ui.alert>
            @endif

            @if (session('success'))
                <x-ui.alert variant="success" title="Sucesso">{{ session('success') }}</x-ui.alert>
            @endif

            <div class="space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Endereço de email
                    </label>
                    <input id="email" 
                           name="email" 
                           type="email" 
                           autocomplete="email" 
                           required 
                           class="w-full px-4 py-2 border border-input rounded-lg focus:outline-none focus:ring-2 focus:ring-ring @error('email') border-destructive @enderror" 
                           placeholder="seu@email.com" 
                           value="{{ old('email', $email ?? '') }}">
                    @error('email')
                        <p class="text-sm text-destructive mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Nova senha
                    </label>
                    <input id="password" 
                           name="password" 
                           type="password" 
                           autocomplete="new-password" 
                           required 
                           class="w-full px-4 py-2 border border-input rounded-lg focus:outline-none focus:ring-2 focus:ring-ring @error('password') border-destructive @enderror" 
                           placeholder="Mínimo 8 caracteres">
                    @error('password')
                        <p class="text-sm text-destructive mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        Confirmar nova senha
                    </label>
                    <input id="password_confirmation" 
                           name="password_confirmation" 
                           type="password" 
                           autocomplete="new-password" 
                           required 
                           class="w-full px-4 py-2 border border-input rounded-lg focus:outline-none focus:ring-2 focus:ring-ring @error('password_confirmation') border-destructive @enderror" 
                           placeholder="Digite a senha novamente">
                    @error('password_confirmation')
                        <p class="text-sm text-destructive mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-between">
                <a href="{{ route('login') }}" class="text-sm text-muted-foreground hover:text-foreground">
                    ← Voltar ao login
                </a>
            </div>

            <div>
                <x-ui.button type="submit" class="w-full">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Redefinir senha
                </x-ui.button>
            </div>
        </form>
    </div>
</div>
@endsection