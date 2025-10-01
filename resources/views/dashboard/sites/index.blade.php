@extends('dashboard.layout')

@section('title', 'Sites - SitePulse Analytics')
@section('page-title', 'Sites')

@section('page-actions')
    <a href="{{ route('sites.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i>
        Add Site
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        @if(count($sites) > 0)
            <div class="row">
                @foreach($sites as $site)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">{{ $site->name }}</h5>
                                <span class="badge bg-{{ $site->isActive ? 'success' : 'secondary' }}">
                                    {{ $site->isActive ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <div class="card-body">
                                <p class="card-text">
                                    <strong>Domain:</strong> {{ $site->domain }}
                                </p>
                                <p class="card-text">
                                    <strong>Widget ID:</strong> 
                                    <code class="small">{{ $site->widgetId }}</code>
                                </p>
                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="text-muted small">Sessions</div>
                                        <div class="fw-bold">{{ $site->sessions()->count() }}</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-muted small">Visits</div>
                                        <div class="fw-bold">{{ $site->visits()->count() }}</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-muted small">Events</div>
                                        <div class="fw-bold">{{ $site->events()->count() }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="btn-group w-100" role="group">
                                    <a href="{{ route('sites.show', $site->id) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('sites.edit', $site->id) }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('sites.toggle-status', $site->id) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-{{ $site->isActive ? 'warning' : 'success' }} btn-sm">
                                            <i class="fas fa-{{ $site->isActive ? 'pause' : 'play' }}"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('sites.destroy', $site->id) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this site?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-globe fa-3x text-muted mb-3"></i>
                <h3 class="text-muted">No Sites Found</h3>
                <p class="text-muted">Get started by adding your first site to track analytics.</p>
                <a href="{{ route('sites.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>
                    Add Your First Site
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
