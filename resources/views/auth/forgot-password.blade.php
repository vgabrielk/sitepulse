@extends('layouts.app')

@section('title', 'Recuperar Senha - SitePulse Widgets')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Recuperar senha
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Digite seu email e enviaremos instruções para redefinir sua senha
            </p>
        </div>
        
        <form class="mt-8 space-y-6" method="POST" action="{{ route('password.email') }}">
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

            @if (session('success'))
                <x-ui.alert variant="success" title="Sucesso">{{ session('success') }}</x-ui.alert>
            @endif

            <div>
                <label for="email" class="block text-sm font-medium mb-2">Email</label>
                <input id="email" name="email" type="email" autocomplete="email" required 
                       class="w-full px-4 py-2 border border-input rounded-lg focus:outline-none focus:ring-2 focus:ring-ring" 
                       placeholder="seu@email.com" value="{{ old('email') }}">
            </div>

            <div>
                <x-ui.button type="submit" class="w-full">Enviar instruções</x-ui.button>
            </div>

            <div class="text-center">
                <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                    Voltar ao login
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
