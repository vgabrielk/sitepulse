@extends('dashboard.layout')

@section('title', 'Dashboard - SitePulse Analytics')
@section('page-title', 'Dashboard')

@section('content')
<div class="row">
    <!-- Stats Cards -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Sites
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['sites_count'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-globe fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total Sessions
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_sessions'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Total Visits
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_visits'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-eye fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Total Reviews
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_reviews'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-star fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Sites -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Recent Sites</h6>
                <a href="{{ route('sites.index') }}" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body">
                @if(isset($recentSites) && count($recentSites) > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Domain</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentSites as $site)
                                <tr>
                                    <td>{{ $site->name }}</td>
                                    <td>{{ $site->domain }}</td>
                                    <td>
                                        @if($site->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('sites.show', $site->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">No sites found. <a href="{{ route('sites.create') }}">Create your first site</a></p>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Reviews -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Recent Reviews</h6>
                <a href="{{ route('reviews.index') }}" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body">
                @if(isset($recentReviews) && count($recentReviews) > 0)
                    @foreach($recentReviews as $review)
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                {{ substr($review['visitor_name'] ?? 'A', 0, 1) }}
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="d-flex justify-content-between">
                                <h6 class="mb-0">{{ $review['visitor_name'] ?? 'Anonymous' }}</h6>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($review['submitted_at'])->diffForHumans() }}</small>
                            </div>
                            <div class="d-flex align-items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $review['rating'] ? 'text-warning' : 'text-muted' }}"></i>
                                @endfor
                            </div>
                            @if($review['comment'])
                                <p class="mb-0 text-muted small">{{ Str::limit($review['comment'], 100) }}</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                @else
                    <p class="text-muted">No reviews yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Analytics Chart -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Analytics Overview (Last 30 Days)</h6>
            </div>
            <div class="card-body">
                <canvas id="analyticsChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('sites.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add New Site
                    </a>
                    <a href="{{ route('analytics.overview') }}" class="btn btn-outline-primary">
                        <i class="fas fa-chart-bar me-2"></i>View Analytics
                    </a>
                    <a href="{{ route('reviews.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-star me-2"></i>Manage Reviews
                    </a>
                    <a href="{{ route('exports.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-download me-2"></i>Export Data
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Analytics Chart
    const ctx = document.getElementById('analyticsChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartData['labels'] ?? []) !!},
            datasets: [{
                label: 'Sessions',
                data: {!! json_encode($chartData['sessions'] ?? []) !!},
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }, {
                label: 'Visits',
                data: {!! json_encode($chartData['visits'] ?? []) !!},
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>
@endpush
