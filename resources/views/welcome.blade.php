@extends('layouts.app')

@section('title', 'SitePulse Analytics')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 py-20">
        <div class="text-center">
            <h1 class="text-4xl font-bold text-gray-900 mb-6">
                SitePulse Analytics
            </h1>
            <p class="text-xl text-gray-600 mb-8">
                Analytics e feedback para seu site
            </p>
            <div class="space-x-4">
                <a href="{{ route('login') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-lg">
                    Entrar
                </a>
                <a href="{{ route('register') }}" class="border border-indigo-600 text-indigo-600 px-6 py-3 rounded-lg">
                    Registrar
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
