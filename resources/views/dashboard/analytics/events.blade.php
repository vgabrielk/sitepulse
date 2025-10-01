@extends('dashboard.layout')

@section('title', $site->name . ' Events - SitePulse Analytics')
@section('page-title', $site->name . ' - Events')

@section('content')
<div class="row">
    <!-- Event Statistics -->
    <div class="col-12 mb-4">
        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title">{{ $eventStats['total_events'] ?? 0 }}</h4>
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
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title">{{ $eventStats['click_events'] ?? 0 }}</h4>
                                <p class="card-text">Click Events</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-hand-pointer fa-2x opacity-75"></i>
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
                                <h4 class="card-title">{{ $eventStats['scroll_events'] ?? 0 }}</h4>
                                <p class="card-text">Scroll Events</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-arrows-alt-v fa-2x opacity-75"></i>
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
                                <h4 class="card-title">{{ $eventStats['form_events'] ?? 0 }}</h4>
                                <p class="card-text">Form Events</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-wpforms fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Top Clicked Elements -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Top Clicked Elements</h5>
            </div>
            <div class="card-body">
                @if(count($topElements) > 0)
                    <div class="list-group list-group-flush">
                        @foreach($topElements as $element)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $element['element'] ?? 'Unknown' }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $element['page'] ?? 'Unknown Page' }}</small>
                                </div>
                                <span class="badge bg-primary rounded-pill">{{ $element['count'] ?? 0 }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">No click data available.</p>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Event Types Chart -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Event Types Distribution</h5>
            </div>
            <div class="card-body">
                <canvas id="eventTypesChart" height="200"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Events Over Time -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Events Over Time</h5>
            </div>
            <div class="card-body">
                <canvas id="eventsChart" height="100"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Event Types Chart
const eventTypesCtx = document.getElementById('eventTypesChart').getContext('2d');
const eventTypesChart = new Chart(eventTypesCtx, {
    type: 'doughnut',
    data: {
        labels: ['Clicks', 'Scrolls', 'Forms', 'Other'],
        datasets: [{
            data: [{{ $eventStats['click_events'] ?? 0 }}, {{ $eventStats['scroll_events'] ?? 0 }}, {{ $eventStats['form_events'] ?? 0 }}, {{ ($eventStats['total_events'] ?? 0) - (($eventStats['click_events'] ?? 0) + ($eventStats['scroll_events'] ?? 0) + ($eventStats['form_events'] ?? 0)) }}],
            backgroundColor: [
                'rgb(255, 99, 132)',
                'rgb(54, 162, 235)',
                'rgb(255, 205, 86)',
                'rgb(201, 203, 207)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

// Events Over Time Chart
const eventsCtx = document.getElementById('eventsChart').getContext('2d');
const eventsChart = new Chart(eventsCtx, {
    type: 'line',
    data: {
        labels: ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7'],
        datasets: [{
            label: 'Events',
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
