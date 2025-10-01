@extends('dashboard.layout')

@section('title', 'Reviews - SitePulse Analytics')
@section('page-title', 'Reviews')

@section('page-actions')
    <div class="btn-group" role="group">
        <button type="button" class="btn btn-success" onclick="bulkApprove()">
            <i class="fas fa-check me-1"></i>
            Bulk Approve
        </button>
        <button type="button" class="btn btn-danger" onclick="bulkReject()">
            <i class="fas fa-times me-1"></i>
            Bulk Reject
        </button>
    </div>
@endsection

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
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title">{{ $reviews->where('status', 'rejected')->count() }}</h4>
                                <p class="card-text">Rejected Reviews</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-times fa-2x opacity-75"></i>
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
        </div>
    </div>
    
    <!-- Reviews List -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">All Reviews</h5>
            </div>
            <div class="card-body">
                @if($reviews->count() > 0)
                    <form id="bulkActionsForm" method="POST">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                        </th>
                                        <th>Review</th>
                                        <th>Rating</th>
                                        <th>Site</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reviews as $review)
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="review_ids[]" value="{{ $review['id'] }}" class="review-checkbox">
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-start">
                                                    <div class="flex-grow-1">
                                                        <strong>{{ $review['name'] ?? 'Anonymous' }}</strong>
                                                        <p class="mb-1">{{ Str::limit($review['comment'] ?? '', 100) }}</p>
                                                        <small class="text-muted">{{ $review['email'] ?? 'No email' }}</small>
                                                    </div>
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
                                                <span class="badge bg-secondary">{{ $review['site_name'] ?? 'Unknown Site' }}</span>
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
                    </form>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-star fa-3x text-muted mb-3"></i>
                        <h3 class="text-muted">No Reviews Found</h3>
                        <p class="text-muted">Reviews will appear here once users start submitting feedback.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.review-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}

function bulkApprove() {
    const selectedReviews = getSelectedReviews();
    if (selectedReviews.length === 0) {
        alert('Please select reviews to approve.');
        return;
    }
    
    if (confirm(`Are you sure you want to approve ${selectedReviews.length} review(s)?`)) {
        const form = document.getElementById('bulkActionsForm');
        form.action = '{{ route("reviews.bulk-approve") }}';
        form.submit();
    }
}

function bulkReject() {
    const selectedReviews = getSelectedReviews();
    if (selectedReviews.length === 0) {
        alert('Please select reviews to reject.');
        return;
    }
    
    if (confirm(`Are you sure you want to reject ${selectedReviews.length} review(s)?`)) {
        const form = document.getElementById('bulkActionsForm');
        form.action = '{{ route("reviews.bulk-reject") }}';
        form.submit();
    }
}

function getSelectedReviews() {
    return Array.from(document.querySelectorAll('.review-checkbox:checked')).map(cb => cb.value);
}
</script>
@endpush
