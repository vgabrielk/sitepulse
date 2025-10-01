@extends('dashboard.layout')

@section('title', $site->name . ' Heatmap - SitePulse Analytics')
@section('page-title', $site->name . ' - Heatmap')

@section('content')
<div class="row">
    <!-- Heatmap Controls -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Heatmap Settings</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label for="pageSelect" class="form-label">Select Page</label>
                        <select class="form-select" id="pageSelect">
                            <option value="/">Homepage</option>
                            <option value="/about">About</option>
                            <option value="/contact">Contact</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="dateRange" class="form-label">Date Range</label>
                        <select class="form-select" id="dateRange">
                            <option value="7">Last 7 days</option>
                            <option value="30" selected>Last 30 days</option>
                            <option value="90">Last 90 days</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="deviceType" class="form-label">Device Type</label>
                        <select class="form-select" id="deviceType">
                            <option value="all">All Devices</option>
                            <option value="desktop">Desktop</option>
                            <option value="mobile">Mobile</option>
                            <option value="tablet">Tablet</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Heatmap Visualization -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Click Heatmap</h5>
            </div>
            <div class="card-body">
                <div class="heatmap-container" style="position: relative; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 0.375rem; min-height: 400px;">
                    <div class="text-center py-5">
                        <i class="fas fa-fire fa-3x text-muted mb-3"></i>
                        <h3 class="text-muted">Heatmap Visualization</h3>
                        <p class="text-muted">Click data will be displayed here as a visual heatmap overlay.</p>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-primary" onclick="loadHeatmap()">
                                <i class="fas fa-sync me-1"></i>
                                Load Heatmap
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="exportHeatmap()">
                                <i class="fas fa-download me-1"></i>
                                Export
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Heatmap Statistics -->
    <div class="col-12 mt-4">
        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="text-muted small">Total Clicks</div>
                        <div class="h4 text-primary">{{ $heatmapData['total_clicks'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="text-muted small">Hot Spots</div>
                        <div class="h4 text-danger">{{ $heatmapData['hot_spots'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="text-muted small">Avg. Clicks per Element</div>
                        <div class="h4 text-info">{{ number_format($heatmapData['avg_clicks_per_element'] ?? 0, 1) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="text-muted small">Most Clicked Element</div>
                        <div class="h4 text-warning">{{ $heatmapData['most_clicked_element'] ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.heatmap-container {
    position: relative;
    overflow: hidden;
}

.heatmap-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
}

.heatmap-point {
    position: absolute;
    border-radius: 50%;
    opacity: 0.7;
    pointer-events: none;
}

.heatmap-point.hot {
    background: radial-gradient(circle, rgba(255,0,0,0.8) 0%, rgba(255,0,0,0.4) 50%, rgba(255,0,0,0.1) 100%);
}

.heatmap-point.medium {
    background: radial-gradient(circle, rgba(255,165,0,0.8) 0%, rgba(255,165,0,0.4) 50%, rgba(255,165,0,0.1) 100%);
}

.heatmap-point.cold {
    background: radial-gradient(circle, rgba(0,0,255,0.8) 0%, rgba(0,0,255,0.4) 50%, rgba(0,0,255,0.1) 100%);
}
</style>
@endpush

@push('scripts')
<script>
function loadHeatmap() {
    // Simulate loading heatmap data
    const container = document.querySelector('.heatmap-container');
    container.innerHTML = `
        <div class="heatmap-overlay">
            <div class="heatmap-point hot" style="top: 20%; left: 30%; width: 40px; height: 40px;"></div>
            <div class="heatmap-point medium" style="top: 40%; left: 60%; width: 30px; height: 30px;"></div>
            <div class="heatmap-point cold" style="top: 60%; left: 20%; width: 25px; height: 25px;"></div>
            <div class="heatmap-point hot" style="top: 70%; left: 70%; width: 35px; height: 35px;"></div>
        </div>
        <div class="text-center py-5">
            <h5>Sample Page Layout</h5>
            <p class="text-muted">This is where your actual page content would be displayed with click heatmap overlay.</p>
        </div>
    `;
}

function exportHeatmap() {
    // Simulate heatmap export
    alert('Heatmap data exported successfully!');
}

// Initialize heatmap on page load
document.addEventListener('DOMContentLoaded', function() {
    loadHeatmap();
});
</script>
@endpush
