@extends('dashboard.layout')

@section('title', 'Sites - SitePulse Widgets')
@section('page-title', 'Sites')

@section('page-actions')
    <x-ui.button href="{{ route('sites.create') }}">
        Add Site
    </x-ui.button>
@endsection

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @if(count($sites) > 0)
        @foreach($sites as $site)
            <x-ui.card class="h-full">
                <div class="flex items-start justify-between mb-2">
                    <h5 class="text-lg font-semibold">{{ $site->name }}</h5>
                    @if($site->isActive)
                        <x-ui.badge variant="success">Active</x-ui.badge>
                    @else
                        <x-ui.badge variant="muted">Inactive</x-ui.badge>
                    @endif
                </div>
                <p class="text-sm text-muted-foreground"><strong>Domain:</strong> {{ $site->domain }}</p>
                <p class="text-sm text-muted-foreground"><strong>Widget ID:</strong> <code>{{ $site->widgetId }}</code></p>
                <div class="grid grid-cols-3 text-center mt-4">
                    <div>
                        <div class="text-xs text-muted-foreground">Sessions</div>
                        <div class="font-semibold">{{ $site->sessions()->count() }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-muted-foreground">Visits</div>
                        <div class="font-semibold">{{ $site->visits()->count() }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-muted-foreground">Events</div>
                        <div class="font-semibold">{{ $site->events()->count() }}</div>
                    </div>
                </div>
                <div class="mt-4 flex gap-2">
                    <x-ui.button href="{{ route('sites.show', $site->id) }}" variant="outline" size="sm">View</x-ui.button>
                    <x-ui.button href="{{ route('sites.edit', $site->id) }}" variant="outline" size="sm">Edit</x-ui.button>
                    <form method="POST" action="{{ route('sites.toggle-status', $site->id) }}">
                        @csrf
                        <x-ui.button type="submit" variant="outline" size="sm">{{ $site->isActive ? 'Pause' : 'Activate' }}</x-ui.button>
                    </form>
                    <form method="POST" action="{{ route('sites.destroy', $site->id) }}" onsubmit="return confirm('Are you sure you want to delete this site?')">
                        @csrf
                        @method('DELETE')
                        <x-ui.button type="submit" variant="destructive" size="sm">Delete</x-ui.button>
                    </form>
                </div>
            </x-ui.card>
        @endforeach
    @else
        <x-ui.card class="col-span-full text-center p-12">
            <svg class="w-12 h-12 mx-auto text-muted-foreground mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 1.657-1.343 3-3 3S6 12.657 6 11s1.343-3 3-3 3 1.343 3 3z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2 12s3-6 10-6 10 6 10 6-3 6-10 6S2 12 2 12z"/></svg>
            <h3 class="text-lg font-semibold mb-2">No Sites Found</h3>
            <p class="text-muted-foreground mb-4">Get started by adding your first site to embed widgets.</p>
            <x-ui.button href="{{ route('sites.create') }}">Add Your First Site</x-ui.button>
        </x-ui.card>
    @endif
</div>
@endsection
