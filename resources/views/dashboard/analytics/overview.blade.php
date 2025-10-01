@extends('dashboard.layout')

@section('title', 'Analytics Overview - SitePulse Analytics')
@section('page-title', 'Analytics Overview')

@section('content')
<div class="row">
    <!-- Summary Cards -->
    <div class="col-12 mb-4">
        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title">{{ array_sum(array_column($overviewData, 'stats.sessions')) }}</h4>
                                <p class="card-text">Total Sessions</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-users fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title">{{ array_sum(array_column($overviewData, 'stats.visits')) }}</h4>
                                <p class="card-text">Total Visits</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-eye fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title">{{ array_sum(array_column($overviewData, 'stats.events')) }}</h4>
                                <p class="card-text">Total Events</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-mouse-pointer fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title">{{ count($overviewData) }}</h4>
                                <p class="card-text">Active Sites</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-globe fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sites Overview -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Sites Performance</h5>
            </div>
            <div class="card-body">
                @if(count($overviewData) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Site</th>
                                    <th>Domain</th>
                                    <th>Sessions</th>
                                    <th>Visits</th>
                                    <th>Events</th>
                                    <th>Unique Visitors</th>
                                    <th>Bounce Rate</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($overviewData as $data)
                                    <tr>
                                        <td>
                                            <strong>{{ $data['site']->name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $data['site']->domain }}</small>
                                        </td>
                                        <td>{{ $data['site']->domain }}</td>
                                        <td>
                                            <span class="badge bg-primary">{{ $data['stats']['sessions'] ?? 0 }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">{{ $data['stats']['visits'] ?? 0 }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $data['stats']['events'] ?? 0 }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning">{{ $data['stats']['unique_visitors'] ?? 0 }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ ($data['stats']['bounce_rate'] ?? 0) > 50 ? 'danger' : 'success' }}">
                                                {{ number_format($data['stats']['bounce_rate'] ?? 0, 1) }}%
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('analytics.site', $data['site']) }}" class="btn btn-outline-primary">
                                                    <i class="fas fa-chart-bar"></i>
                                                </a>
                                                <a href="{{ route('sites.show', $data['site']) }}" class="btn btn-outline-secondary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                        <h3 class="text-muted">No Analytics Data</h3>
                        <p class="text-muted">Add sites and start tracking to see analytics data.</p>
                        <a href="{{ route('sites.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>
                            Add Your First Site
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
