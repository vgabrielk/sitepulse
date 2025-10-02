@extends('layouts.app')

@section('title', 'Registrar - SitePulse Widgets')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Crie sua conta
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Ou
                <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                    entre na sua conta existente
                </a>
            </p>
        </div>
        
        <form class="mt-8 space-y-6" method="POST" action="{{ route('register') }}">
            @csrf
            
            @if ($errors->any())
                <x-ui.alert variant="error" title="Erros na submissão">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-ui.alert>
            @endif

            @if ($errors->has('_token'))
                <x-ui.alert variant="warning" title="Sessão expirada">
                    <p class="text-sm">{{ $errors->first('_token') }}</p>
                </x-ui.alert>
            @endif

            @if (session('success'))
                <x-ui.alert variant="success" title="Sucesso">{{ session('success') }}</x-ui.alert>
            @endif

            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium mb-2">Nome completo</label>
                    <input id="name" name="name" type="text" autocomplete="name" required 
                           class="w-full px-4 py-2 border border-input rounded-lg focus:outline-none focus:ring-2 focus:ring-ring" 
                           placeholder="Seu nome completo" value="{{ old('name') }}">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium mb-2">Email</label>
                    <input id="email" name="email" type="email" autocomplete="email" required 
                           class="w-full px-4 py-2 border border-input rounded-lg focus:outline-none focus:ring-2 focus:ring-ring" 
                           placeholder="seu@email.com" value="{{ old('email') }}">
                </div>

                <div>
                    <label for="company" class="block text-sm font-medium mb-2">Empresa (opcional)</label>
                    <input id="company" name="company" type="text" 
                           class="w-full px-4 py-2 border border-input rounded-lg focus:outline-none focus:ring-2 focus:ring-ring" 
                           placeholder="Nome da sua empresa" value="{{ old('company') }}">
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium mb-2">Telefone (opcional)</label>
                    <input id="phone" name="phone" type="tel" 
                           class="w-full px-4 py-2 border border-input rounded-lg focus:outline-none focus:ring-2 focus:ring-ring" 
                           placeholder="(11) 99999-9999" value="{{ old('phone') }}">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium mb-2">Senha</label>
                    <input id="password" name="password" type="password" autocomplete="new-password" required 
                           class="w-full px-4 py-2 border border-input rounded-lg focus:outline-none focus:ring-2 focus:ring-ring" 
                           placeholder="Mínimo 8 caracteres">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium mb-2">Confirmar senha</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required 
                           class="w-full px-4 py-2 border border-input rounded-lg focus:outline-none focus:ring-2 focus:ring-ring" 
                           placeholder="Digite a senha novamente">
                </div>
            </div>

            <div class="flex items-center gap-3">
                <input id="terms" name="terms" type="checkbox" required class="w-4 h-4 text-primary bg-gray-100 border-gray-300 rounded focus:ring-primary focus:ring-2">
                <label for="terms" class="text-sm">
                    Eu aceito os 
                    <a href="#" class="text-primary hover:underline">termos de uso</a> 
                    e a 
                    <a href="#" class="text-primary hover:underline">política de privacidade</a>
                </label>
            </div>

            <div>
                <x-ui.button type="submit" class="w-full">Criar conta</x-ui.button>
            </div>
        </form>
    </div>
</div>
@endsection
