@extends('dashboard.layout')

@section('title', $site->name . ' Reviews - SitePulse Analytics')
@section('page-title', $site->name . ' - Reviews')

@section('content')
<div class="row">
    <!-- Review Statistics -->
    <div class="col-12 mb-4">
        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title">{{ $reviews->where('status', 'pending')->count() }}</h4>
                                <p class="card-text">Pending Reviews</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-clock fa-2x opacity-75"></i>
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
                                <h4 class="card-title">{{ $reviews->where('status', 'approved')->count() }}</h4>
                                <p class="card-text">Approved Reviews</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-check fa-2x opacity-75"></i>
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
                                <h4 class="card-title">{{ number_format($reviews->avg('rating') ?? 0, 1) }}</h4>
                                <p class="card-text">Average Rating</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-star fa-2x opacity-75"></i>
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
                                <h4 class="card-title">{{ $reviews->count() }}</h4>
                                <p class="card-text">Total Reviews</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-comments fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Rating Distribution -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Rating Distribution</h5>
            </div>
            <div class="card-body">
                <canvas id="ratingChart" height="200"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Recent Reviews -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Recent Reviews</h5>
            </div>
            <div class="card-body">
                @if($reviews->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($reviews->take(5) as $review)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-1">
                                            <strong>{{ $review['name'] ?? 'Anonymous' }}</strong>
                                            <div class="ms-2">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star {{ $i <= ($review['rating'] ?? 0) ? 'text-warning' : 'text-muted' }}"></i>
                                                @endfor
                                            </div>
                                        </div>
                                        <p class="mb-1">{{ Str::limit($review['comment'] ?? '', 100) }}</p>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($review['created_at'] ?? now())->format('M d, Y') }}</small>
                                    </div>
                                    <span class="badge bg-{{ $review['status'] === 'approved' ? 'success' : ($review['status'] === 'rejected' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($review['status'] ?? 'pending') }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">No reviews found for this site.</p>
                @endif
            </div>
        </div>
    </div>
    
    <!-- All Reviews -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">All Reviews</h5>
            </div>
            <div class="card-body">
                @if($reviews->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Reviewer</th>
                                    <th>Rating</th>
                                    <th>Comment</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reviews as $review)
                                    <tr>
                                        <td>
                                            <div>
                                                <strong>{{ $review['name'] ?? 'Anonymous' }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $review['email'] ?? 'No email' }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star {{ $i <= ($review['rating'] ?? 0) ? 'text-warning' : 'text-muted' }}"></i>
                                                @endfor
                                                <span class="ms-2">{{ $review['rating'] ?? 0 }}/5</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="max-width: 300px;">
                                                {{ Str::limit($review['comment'] ?? '', 150) }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $review['status'] === 'approved' ? 'success' : ($review['status'] === 'rejected' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($review['status'] ?? 'pending') }}
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($review['created_at'] ?? now())->format('M d, Y') }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                @if($review['status'] === 'pending')
                                                    <form method="POST" action="{{ route('reviews.approve', $review['id']) }}" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-success" title="Approve">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="{{ route('reviews.reject', $review['id']) }}" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-danger" title="Reject">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                <form method="POST" action="{{ route('reviews.destroy', $review['id']) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this review?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-star fa-3x text-muted mb-3"></i>
                        <h3 class="text-muted">No Reviews Found</h3>
                        <p class="text-muted">Reviews for this site will appear here once users start submitting feedback.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Rating Distribution Chart
const ctx = document.getElementById('ratingChart').getContext('2d');
const ratingChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['5 Stars', '4 Stars', '3 Stars', '2 Stars', '1 Star'],
        datasets: [{
            data: [12, 8, 3, 1, 2],
            backgroundColor: [
                'rgb(255, 99, 132)',
                'rgb(54, 162, 235)',
                'rgb(255, 205, 86)',
                'rgb(75, 192, 192)',
                'rgb(153, 102, 255)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});
</script>
@endpush
