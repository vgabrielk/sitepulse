@extends('admin.layout')

@section('title', 'Admin Dashboard - SitePulse')
@section('page-title', 'Admin Dashboard')

@section('content')
<div class="row">
    <!-- System Stats -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Clients
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_clients'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
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
                            Total Sites
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_sites'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-globe fa-2x text-gray-300"></i>
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
                            Total Sessions
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_sessions'] ?? 0 }}</div>
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
                            Total Events
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_events'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Revenue Overview -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">System Overview (Last 30 Days)</h6>
            </div>
            <div class="card-body">
                <canvas id="systemChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Plan Distribution -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Client Plans</h6>
            </div>
            <div class="card-body">
                <canvas id="planChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Clients -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Recent Clients</h6>
                <a href="{{ route('admin.clients.index') }}" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body">
                @if(isset($recentClients) && count($recentClients) > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Plan</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentClients as $client)
                                <tr>
                                    <td>{{ $client['name'] }}</td>
                                    <td>{{ $client['email'] }}</td>
                                    <td>
                                        <span class="badge bg-{{ $client['plan'] === 'enterprise' ? 'dark' : ($client['plan'] === 'premium' ? 'warning' : ($client['plan'] === 'basic' ? 'info' : 'secondary')) }}">
                                            {{ ucfirst($client['plan']) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($client['is_active'])
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($client['created_at'])->diffForHumans() }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">No clients found.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- System Status -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">System Status</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <div class="status-indicator {{ $systemStatus['database'] ? 'active' : 'error' }}"></div>
                            <span>Database</span>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="status-indicator {{ $systemStatus['redis'] ? 'active' : 'error' }}"></div>
                            <span>Redis Cache</span>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="status-indicator {{ $systemStatus['queue'] ? 'active' : 'error' }}"></div>
                            <span>Queue Workers</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-3">
                            <div class="status-indicator {{ $systemStatus['storage'] ? 'active' : 'error' }}"></div>
                            <span>File Storage</span>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="status-indicator {{ $systemStatus['mail'] ? 'active' : 'error' }}"></div>
                            <span>Mail Service</span>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="status-indicator {{ $systemStatus['api'] ? 'active' : 'error' }}"></div>
                            <span>API Service</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Top Sites by Traffic -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Top Sites by Traffic</h6>
            </div>
            <div class="card-body">
                @if(isset($topSites) && count($topSites) > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Site</th>
                                    <th>Client</th>
                                    <th>Sessions</th>
                                    <th>Visits</th>
                                    <th>Events</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topSites as $site)
                                <tr>
                                    <td>
                                        <div>
                                            <strong>{{ $site['name'] }}</strong><br>
                                            <small class="text-muted">{{ $site['domain'] }}</small>
                                        </div>
                                    </td>
                                    <td>{{ $site['client_name'] }}</td>
                                    <td>{{ number_format($site['sessions_count']) }}</td>
                                    <td>{{ number_format($site['visits_count']) }}</td>
                                    <td>{{ number_format($site['events_count']) }}</td>
                                    <td>
                                        @if($site['is_active'])
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">No data available.</p>
                @endif
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
                    <a href="{{ route('admin.clients.create') }}" class="btn btn-primary">
                        <i class="fas fa-user-plus me-2"></i>Add New Client
                    </a>
                    <a href="{{ route('admin.analytics.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-chart-bar me-2"></i>View Analytics
                    </a>
                    <a href="{{ route('admin.system.logs') }}" class="btn btn-outline-primary">
                        <i class="fas fa-file-alt me-2"></i>View Logs
                    </a>
                    <a href="{{ route('admin.system.settings') }}" class="btn btn-outline-primary">
                        <i class="fas fa-cog me-2"></i>System Settings
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
    // System Overview Chart
    const systemCtx = document.getElementById('systemChart').getContext('2d');
    new Chart(systemCtx, {
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
                label: 'Events',
                data: {!! json_encode($chartData['events'] ?? []) !!},
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

    // Plan Distribution Chart
    const planCtx = document.getElementById('planChart').getContext('2d');
    new Chart(planCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($planData['labels'] ?? []) !!},
            datasets: [{
                data: {!! json_encode($planData['data'] ?? []) !!},
                backgroundColor: [
                    '#6c757d', // Free
                    '#17a2b8', // Basic
                    '#ffc107', // Premium
                    '#343a40'  // Enterprise
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>
@endpush
