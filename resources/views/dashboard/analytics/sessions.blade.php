@extends('dashboard.layout')

@section('title', $site->name . ' Sessions - SitePulse Analytics')
@section('page-title', $site->name . ' - Sessions')

@section('content')
<div class="row">
    <!-- Session Statistics -->
    <div class="col-12 mb-4">
        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title">{{ $sessionStats['total_sessions'] ?? 0 }}</h4>
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
                                <h4 class="card-title">{{ $sessionStats['unique_visitors'] ?? 0 }}</h4>
                                <p class="card-text">Unique Visitors</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-user-friends fa-2x opacity-75"></i>
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
                                <h4 class="card-title">{{ gmdate('H:i:s', $sessionStats['avg_session_duration'] ?? 0) }}</h4>
                                <p class="card-text">Avg. Duration</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-clock fa-2x opacity-75"></i>
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
                                <h4 class="card-title">{{ number_format($sessionStats['bounce_rate'] ?? 0, 1) }}%</h4>
                                <p class="card-text">Bounce Rate</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-chart-line fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Session Chart -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Sessions Over Time</h5>
            </div>
            <div class="card-body">
                <canvas id="sessionsChart" height="100"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Sessions Chart
const ctx = document.getElementById('sessionsChart').getContext('2d');
const sessionsChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7'],
        datasets: [{
            label: 'Sessions',
            data: [12, 19, 3, 5, 2, 3, 8],
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
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
