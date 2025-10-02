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
                    <label for="email" class="block text-sm font-medium mb-2">Email</label>
                    <input id="email" name="email" type="email" autocomplete="email" required 
                           class="w-full px-4 py-2 border border-input rounded-lg focus:outline-none focus:ring-2 focus:ring-ring" 
                           placeholder="seu@email.com" value="{{ old('email') }}">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium mb-2">Nova senha</label>
                    <input id="password" name="password" type="password" autocomplete="new-password" required 
                           class="w-full px-4 py-2 border border-input rounded-lg focus:outline-none focus:ring-2 focus:ring-ring" 
                           placeholder="Mínimo 8 caracteres">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium mb-2">Confirmar nova senha</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required 
                           class="w-full px-4 py-2 border border-input rounded-lg focus:outline-none focus:ring-2 focus:ring-ring" 
                           placeholder="Digite a senha novamente">
                </div>
            </div>

            <div>
                <x-ui.button type="submit" class="w-full">Redefinir senha</x-ui.button>
            </div>

            <div class="text-center">
                <a href="{{ route('login') }}" class="text-primary hover:underline">Voltar ao login</a>
            </div>
        </form>
    </div>
</div>
@endsection
