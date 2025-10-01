@extends('dashboard.layout')

@section('title', $site->name . ' - SitePulse Analytics')
@section('page-title', $site->name)

@section('page-actions')
    <div class="btn-group" role="group">
        <a href="{{ route('sites.edit', $site) }}" class="btn btn-outline-secondary">
            <i class="fas fa-edit me-1"></i>
            Edit
        </a>
        <a href="{{ route('analytics.site', $site) }}" class="btn btn-primary">
            <i class="fas fa-chart-bar me-1"></i>
            View Analytics
        </a>
    </div>
@endsection

@section('content')
<div class="row">
    <!-- Site Information -->
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Site Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Name:</strong> {{ $site->name }}</p>
                        <p><strong>Domain:</strong> {{ $site->domain }}</p>
                        <p><strong>Status:</strong> 
                            <span class="badge bg-{{ $site->is_active ? 'success' : 'secondary' }}">
                                {{ $site->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Widget ID:</strong> <code>{{ $site->widget_id }}</code></p>
                        <p><strong>Created:</strong> {{ $site->created_at->format('M d, Y') }}</p>
                        <p><strong>Last Updated:</strong> {{ $site->updated_at->format('M d, Y') }}</p>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-12">
                        <h6>Settings</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <span class="badge bg-{{ $site->anonymize_ips ? 'success' : 'secondary' }}">
                                    {{ $site->anonymize_ips ? 'IP Anonymized' : 'IP Not Anonymized' }}
                                </span>
                            </div>
                            <div class="col-md-4">
                                <span class="badge bg-{{ $site->track_events ? 'success' : 'secondary' }}">
                                    {{ $site->track_events ? 'Events Tracked' : 'Events Not Tracked' }}
                                </span>
                            </div>
                            <div class="col-md-4">
                                <span class="badge bg-{{ $site->collect_feedback ? 'success' : 'secondary' }}">
                                    {{ $site->collect_feedback ? 'Feedback Enabled' : 'Feedback Disabled' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Embed Code -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Embed Code</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Add this code to your website to start tracking analytics:</p>
                <div class="input-group">
                    <textarea class="form-control font-monospace" rows="3" readonly>{{ $embedCode }}</textarea>
                    <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard(this)">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Statistics -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Statistics (Last 30 Days)</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="text-muted small">Sessions</div>
                        <div class="h4 text-primary">{{ $stats['sessions'] ?? 0 }}</div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="text-muted small">Visits</div>
                        <div class="h4 text-success">{{ $stats['visits'] ?? 0 }}</div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted small">Events</div>
                        <div class="h4 text-info">{{ $stats['events'] ?? 0 }}</div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted small">Unique Visitors</div>
                        <div class="h4 text-warning">{{ $stats['unique_visitors'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('analytics.site', $site) }}" class="btn btn-primary">
                        <i class="fas fa-chart-bar me-1"></i>
                        View Analytics
                    </a>
                    <a href="{{ route('reviews.site', $site) }}" class="btn btn-outline-primary">
                        <i class="fas fa-star me-1"></i>
                        View Reviews
                    </a>
                    <form method="POST" action="{{ route('sites.toggle-status', $site) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-{{ $site->is_active ? 'warning' : 'success' }} w-100">
                            <i class="fas fa-{{ $site->is_active ? 'pause' : 'play' }} me-1"></i>
                            {{ $site->is_active ? 'Deactivate' : 'Activate' }} Site
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function copyToClipboard(button) {
    const textarea = button.previousElementSibling;
    textarea.select();
    document.execCommand('copy');
    
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-check"></i>';
    button.classList.remove('btn-outline-secondary');
    button.classList.add('btn-success');
    
    setTimeout(() => {
        button.innerHTML = originalText;
        button.classList.remove('btn-success');
        button.classList.add('btn-outline-secondary');
    }, 2000);
}
</script>
@endpush
