@extends('dashboard.layout')

@section('title', $site->name . ' Analytics - SitePulse Analytics')
@section('page-title', $site->name . ' Analytics')

@section('page-actions')
    <div class="btn-group" role="group">
        <a href="{{ route('analytics.sessions', $site) }}" class="btn btn-outline-primary">
            <i class="fas fa-users me-1"></i>
            Sessions
        </a>
        <a href="{{ route('analytics.events', $site) }}" class="btn btn-outline-primary">
            <i class="fas fa-mouse-pointer me-1"></i>
            Events
        </a>
        <a href="{{ route('analytics.heatmap', $site) }}" class="btn btn-outline-primary">
            <i class="fas fa-fire me-1"></i>
            Heatmap
        </a>
    </div>
@endsection

@section('content')
<div class="row">
    <!-- Key Metrics -->
    <div class="col-12 mb-4">
        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title">{{ $stats['sessions'] ?? 0 }}</h4>
                                <p class="card-text">Sessions</p>
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
                                <h4 class="card-title">{{ $stats['visits'] ?? 0 }}</h4>
                                <p class="card-text">Visits</p>
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
                                <h4 class="card-title">{{ $stats['events'] ?? 0 }}</h4>
                                <p class="card-text">Events</p>
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
                                <h4 class="card-title">{{ $stats['unique_visitors'] ?? 0 }}</h4>
                                <p class="card-text">Unique Visitors</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-user-friends fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Real-time Metrics -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Real-time Activity</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="text-muted small">Active Sessions</div>
                        <div class="h4 text-primary">{{ $realTimeMetrics['active_sessions'] ?? 0 }}</div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted small">Current Visitors</div>
                        <div class="h4 text-success">{{ $realTimeMetrics['current_visitors'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Performance Metrics -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Performance</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="text-muted small">Bounce Rate</div>
                        <div class="h4 text-{{ ($stats['bounce_rate'] ?? 0) > 50 ? 'danger' : 'success' }}">
                            {{ number_format($stats['bounce_rate'] ?? 0, 1) }}%
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted small">Avg. Session Duration</div>
                        <div class="h4 text-info">{{ gmdate('H:i:s', $stats['avg_session_duration'] ?? 0) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Traffic Overview (Last 30 Days)</h5>
            </div>
            <div class="card-body">
                <canvas id="trafficChart" height="100"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Traffic Chart
const ctx = document.getElementById('trafficChart').getContext('2d');
const trafficChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7'],
        datasets: [{
            label: 'Sessions',
            data: [12, 19, 3, 5, 2, 3, 8],
            borderColor: 'rgb(75, 192, 192)',
            tension: 0.1
        }, {
            label: 'Visits',
            data: [2, 3, 20, 5, 1, 4, 6],
            borderColor: 'rgb(255, 99, 132)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>
@endpush
