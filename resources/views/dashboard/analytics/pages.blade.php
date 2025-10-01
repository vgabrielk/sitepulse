@extends('dashboard.layout')

@section('title', $site->name . ' Pages - SitePulse Analytics')
@section('page-title', $site->name . ' - Pages')

@section('content')
<div class="row">
    <!-- Page Statistics -->
    <div class="col-12 mb-4">
        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title">{{ $pageStats['total_pages'] ?? 0 }}</h4>
                                <p class="card-text">Total Pages</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-file-alt fa-2x opacity-75"></i>
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
                                <h4 class="card-title">{{ $pageStats['total_views'] ?? 0 }}</h4>
                                <p class="card-text">Total Views</p>
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
                                <h4 class="card-title">{{ number_format($pageStats['avg_time_on_page'] ?? 0, 1) }}s</h4>
                                <p class="card-text">Avg. Time on Page</p>
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
                                <h4 class="card-title">{{ number_format($pageStats['bounce_rate'] ?? 0, 1) }}%</h4>
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
    
    <!-- Top Pages -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Top Pages</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Page</th>
                                <th>Views</th>
                                <th>Unique Views</th>
                                <th>Avg. Time</th>
                                <th>Bounce Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <strong>/</strong>
                                    <br>
                                    <small class="text-muted">Homepage</small>
                                </td>
                                <td><span class="badge bg-primary">{{ $pageStats['homepage_views'] ?? 0 }}</span></td>
                                <td><span class="badge bg-success">{{ $pageStats['homepage_unique_views'] ?? 0 }}</span></td>
                                <td>{{ number_format($pageStats['homepage_avg_time'] ?? 0, 1) }}s</td>
                                <td>
                                    <span class="badge bg-{{ ($pageStats['homepage_bounce_rate'] ?? 0) > 50 ? 'danger' : 'success' }}">
                                        {{ number_format($pageStats['homepage_bounce_rate'] ?? 0, 1) }}%
                                    </span>
                                </td>
                            </tr>
                            <!-- Add more pages as needed -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
