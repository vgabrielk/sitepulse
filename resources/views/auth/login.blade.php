@extends('layouts.app')

@section('title', 'Login - SitePulse Widgets')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Entre na sua conta
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Ou
                <a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                    crie uma nova conta
                </a>
            </p>
        </div>
        
        <form class="mt-8 space-y-6" method="POST" action="{{ route('login') }}">
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

            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="email" class="block text-sm font-medium mb-2">Email</label>
                    <input id="email" name="email" type="email" autocomplete="email" required 
                           class="w-full px-4 py-2 border border-input rounded-lg focus:outline-none focus:ring-2 focus:ring-ring" 
                           placeholder="Endereço de email" value="{{ old('email') }}">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium mb-2">Senha</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required 
                           class="w-full px-4 py-2 border border-input rounded-lg focus:outline-none focus:ring-2 focus:ring-ring" 
                           placeholder="Senha">
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox" class="sr-only peer">
                    <div class="w-11 h-6 bg-input peer-checked:bg-primary rounded-full peer relative transition-colors">
                        <div class="absolute top-0.5 left-0.5 bg-card w-5 h-5 rounded-full transition-transform peer-checked:translate-x-5"></div>
                    </div>
                    <label for="remember" class="ml-3 text-sm">Lembrar de mim</label>
                </div>

                <div class="text-sm">
                    <a href="{{ route('password.request') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                        Esqueceu sua senha?
                    </a>
                </div>
            </div>

            <div>
                <x-ui.button type="submit" class="w-full">Entrar</x-ui.button>
            </div>
        </form>
    </div>
</div>
@endsection
