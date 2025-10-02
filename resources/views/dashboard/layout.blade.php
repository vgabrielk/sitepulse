@extends('layouts.ui')

@section('page-header')
    <div class="flex items-center justify-between border-b border-border pb-4 mb-6">
        <h1 class="text-2xl font-semibold">@yield('page-title', 'Dashboard')</h1>
        <div class="flex items-center gap-2">
            @yield('page-actions')
        </div>
    </div>
@endsection
