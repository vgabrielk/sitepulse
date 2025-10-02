@extends('dashboard.layout')

@section('title', 'Dashboard - SitePulse Widgets')
@section('page-title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <x-ui.card>
        <div class="flex items-center justify-between">
            <div>
                <div class="text-xs font-semibold text-primary uppercase mb-1">Total Sites</div>
                <div class="text-2xl font-bold">{{ $stats['sites_count'] ?? 0 }}</div>
            </div>
            <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/></svg>
            </div>
        </div>
    </x-ui.card>
    <x-ui.card>
        <div class="flex items-center justify-between">
            <div>
                <div class="text-xs font-semibold text-success uppercase mb-1">Total Sessions</div>
                <div class="text-2xl font-bold">{{ $stats['total_sessions'] ?? 0 }}</div>
            </div>
            <div class="w-10 h-10 rounded-full bg-success/10 text-success flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-5M12 20v-8M7 20v-4M2 20v-2"/></svg>
            </div>
        </div>
    </x-ui.card>
    <x-ui.card>
        <div class="flex items-center justify-between">
            <div>
                <div class="text-xs font-semibold text-primary uppercase mb-1">Total Visits</div>
                <div class="text-2xl font-bold">{{ $stats['total_visits'] ?? 0 }}</div>
            </div>
            <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </div>
        </div>
    </x-ui.card>
    <x-ui.card>
        <div class="flex items-center justify-between">
            <div>
                <div class="text-xs font-semibold text-warning uppercase mb-1">Total Reviews</div>
                <div class="text-2xl font-bold">{{ $stats['total_reviews'] ?? 0 }}</div>
            </div>
            <div class="w-10 h-10 rounded-full bg-warning/10 text-warning flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81H7.03a1 1 0 00.95-.69l1.07-3.292z"/></svg>
            </div>
        </div>
    </x-ui.card>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <x-ui.card>
        <div class="flex items-center justify-between mb-4">
            <h6 class="text-sm font-semibold text-primary">Recent Sites</h6>
            <x-ui.button href="{{ route('sites.index') }}" size="sm">View All</x-ui.button>
        </div>
        @if(isset($recentSites) && count($recentSites) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-muted">
                        <tr>
                            <th class="px-4 py-2 text-left">Name</th>
                            <th class="px-4 py-2 text-left">Domain</th>
                            <th class="px-4 py-2 text-left">Status</th>
                            <th class="px-4 py-2 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach($recentSites as $site)
                        <tr>
                            <td class="px-4 py-2">{{ $site->name }}</td>
                            <td class="px-4 py-2">{{ $site->domain }}</td>
                            <td class="px-4 py-2">
                                @if($site->is_active)
                                    <x-ui.badge variant="success">Active</x-ui.badge>
                                @else
                                    <x-ui.badge variant="muted">Inactive</x-ui.badge>
                                @endif
                            </td>
                            <td class="px-4 py-2">
                                <x-ui.button variant="outline" size="sm" href="{{ route('sites.show', $site->id) }}">View</x-ui.button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted-foreground">No sites found. <a class="text-primary underline" href="{{ route('sites.create') }}">Create your first site</a></p>
        @endif
    </x-ui.card>

    <x-ui.card>
        <div class="flex items-center justify-between mb-4">
            <h6 class="text-sm font-semibold text-primary">Recent Reviews</h6>
            <x-ui.button href="{{ route('reviews.index') }}" size="sm">View All</x-ui.button>
        </div>
        @if(isset($recentReviews) && count($recentReviews) > 0)
            <div class="space-y-4">
                @foreach($recentReviews as $review)
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-primary text-primary-foreground flex items-center justify-center">
                        {{ substr($review['visitor_name'] ?? 'A', 0, 1) }}
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <h6 class="font-medium">{{ $review['visitor_name'] ?? 'Anonymous' }}</h6>
                            <small class="text-muted-foreground">{{ \Carbon\Carbon::parse($review['submitted_at'])->diffForHumans() }}</small>
                        </div>
                        <div class="flex items-center">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 {{ $i <= $review['rating'] ? 'text-warning' : 'text-muted-foreground' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81H7.03a1 1 0 00.95-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                        @if($review['comment'])
                            <p class="text-sm text-muted-foreground">{{ Str::limit($review['comment'], 100) }}</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <p class="text-muted-foreground">No reviews yet.</p>
        @endif
    </x-ui.card>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <x-ui.card>
        <h6 class="text-sm font-semibold text-primary mb-4">Quick Actions</h6>
        <div class="grid gap-2">
            <x-ui.button href="{{ route('sites.create') }}">Add New Site</x-ui.button>
            <x-ui.button href="{{ route('reviews.index') }}" variant="outline">
                Manage Reviews
            </x-ui.button>
            <x-ui.button href="{{ route('exports.index') }}" variant="outline">
                Export Data
            </x-ui.button>
        </div>
    </x-ui.card>
</div>
@endsection

@push('scripts')
<script>
// No analytics scripts needed
</script>
@endpush
