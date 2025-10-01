@extends('dashboard.layout')

@section('title', 'Data Exports - SitePulse Analytics')
@section('page-title', 'Data Exports')

@section('content')
<div class="row">
    <!-- Export Options -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Export Data</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Analytics Export -->
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-chart-bar fa-3x text-primary mb-3"></i>
                                <h5 class="card-title">Analytics Data</h5>
                                <p class="card-text">Export sessions, visits, and events data for analysis.</p>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#analyticsExportModal">
                                    <i class="fas fa-download me-1"></i>
                                    Export Analytics
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Reviews Export -->
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-star fa-3x text-warning mb-3"></i>
                                <h5 class="card-title">Reviews Data</h5>
                                <p class="card-text">Export user reviews and feedback data.</p>
                                <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#reviewsExportModal">
                                    <i class="fas fa-download me-1"></i>
                                    Export Reviews
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Events Export -->
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-mouse-pointer fa-3x text-info mb-3"></i>
                                <h5 class="card-title">Events Data</h5>
                                <p class="card-text">Export click events, scroll data, and user interactions.</p>
                                <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#eventsExportModal">
                                    <i class="fas fa-download me-1"></i>
                                    Export Events
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Export History -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Export History</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Export Type</th>
                                <th>Site</th>
                                <th>Date Range</th>
                                <th>Format</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <span class="badge bg-primary">Analytics</span>
                                </td>
                                <td>Example Site</td>
                                <td>Jan 1 - Jan 31, 2024</td>
                                <td>CSV</td>
                                <td>
                                    <span class="badge bg-success">Completed</span>
                                </td>
                                <td>Jan 15, 2024</td>
                                <td>
                                    <button class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-download"></i>
                                    </button>
                                </td>
                            </tr>
                            <!-- Add more export history entries as needed -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Analytics Export Modal -->
<div class="modal fade" id="analyticsExportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('exports.analytics') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Export Analytics Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="analytics_site_id" class="form-label">Select Site</label>
                        <select class="form-select" id="analytics_site_id" name="site_id" required>
                            <option value="">Choose a site...</option>
                            @foreach($sites as $site)
                                <option value="{{ $site->id }}">{{ $site->name }} ({{ $site->domain }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="analytics_start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="analytics_start_date" name="start_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="analytics_end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="analytics_end_date" name="end_date" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="analytics_format" class="form-label">Export Format</label>
                        <select class="form-select" id="analytics_format" name="format" required>
                            <option value="csv">CSV</option>
                            <option value="json">JSON</option>
                            <option value="xlsx">Excel (XLSX)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-download me-1"></i>
                        Export Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reviews Export Modal -->
<div class="modal fade" id="reviewsExportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('exports.reviews') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Export Reviews Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="reviews_site_id" class="form-label">Select Site</label>
                        <select class="form-select" id="reviews_site_id" name="site_id" required>
                            <option value="">Choose a site...</option>
                            @foreach($sites as $site)
                                <option value="{{ $site->id }}">{{ $site->name }} ({{ $site->domain }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="reviews_start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="reviews_start_date" name="start_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="reviews_end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="reviews_end_date" name="end_date" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="reviews_format" class="form-label">Export Format</label>
                        <select class="form-select" id="reviews_format" name="format" required>
                            <option value="csv">CSV</option>
                            <option value="json">JSON</option>
                            <option value="xlsx">Excel (XLSX)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-download me-1"></i>
                        Export Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Events Export Modal -->
<div class="modal fade" id="eventsExportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('exports.events') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Export Events Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="events_site_id" class="form-label">Select Site</label>
                        <select class="form-select" id="events_site_id" name="site_id" required>
                            <option value="">Choose a site...</option>
                            @foreach($sites as $site)
                                <option value="{{ $site->id }}">{{ $site->name }} ({{ $site->domain }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="events_start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="events_start_date" name="start_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="events_end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="events_end_date" name="end_date" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="events_format" class="form-label">Export Format</label>
                        <select class="form-select" id="events_format" name="format" required>
                            <option value="csv">CSV</option>
                            <option value="json">JSON</option>
                            <option value="xlsx">Excel (XLSX)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-download me-1"></i>
                        Export Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Set default date ranges
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date();
    const thirtyDaysAgo = new Date(today.getTime() - (30 * 24 * 60 * 60 * 1000));
    
    // Set default dates for all date inputs
    document.querySelectorAll('input[type="date"]').forEach(input => {
        if (input.id.includes('start_date')) {
            input.value = thirtyDaysAgo.toISOString().split('T')[0];
        } else if (input.id.includes('end_date')) {
            input.value = today.toISOString().split('T')[0];
        }
    });
});
</script>
@endpush
