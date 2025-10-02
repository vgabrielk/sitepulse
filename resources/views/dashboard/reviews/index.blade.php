@extends('dashboard.layout')

@section('title', 'Reviews - SitePulse Widgets')
@section('page-title', 'Reviews')

@section('page-actions')
    <div class="flex items-center gap-2">
        <x-ui.button variant="success" onclick="bulkApprove()">Bulk Approve</x-ui.button>
        <x-ui.button variant="destructive" onclick="bulkReject()">Bulk Reject</x-ui.button>
    </div>
@endsection

@section('content')
<div class="space-y-8">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-ui.card>
            <div class="text-xs font-semibold text-primary uppercase mb-1">Pending Reviews</div>
            <div class="text-2xl font-bold">{{ $reviews->where('status', 'pending')->count() }}</div>
        </x-ui.card>
        <x-ui.card>
            <div class="text-xs font-semibold text-success uppercase mb-1">Approved Reviews</div>
            <div class="text-2xl font-bold">{{ $reviews->where('status', 'approved')->count() }}</div>
        </x-ui.card>
        <x-ui.card>
            <div class="text-xs font-semibold text-destructive uppercase mb-1">Rejected Reviews</div>
            <div class="text-2xl font-bold">{{ $reviews->where('status', 'rejected')->count() }}</div>
        </x-ui.card>
        <x-ui.card>
            <div class="text-xs font-semibold text-warning uppercase mb-1">Average Rating</div>
            <div class="text-2xl font-bold">{{ number_format($reviews->avg('rating') ?? 0, 1) }}</div>
        </x-ui.card>
    </div>

        <x-ui.card>
            <h5 class="text-base font-semibold mb-4">All Reviews</h5>
        @if($reviews->count() > 0)
            <form id="bulkActionsForm" method="POST" action="{{ route('reviews.bulk-approve') }}">
                @csrf
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-muted">
                            <tr>
                                <th class="px-4 py-2"><input type="checkbox" id="selectAll" onchange="toggleSelectAll()"></th>
                                <th class="px-4 py-2 text-left">Review</th>
                                <th class="px-4 py-2 text-left">Rating</th>
                                <th class="px-4 py-2 text-left">Site</th>
                                <th class="px-4 py-2 text-left">Status</th>
                                <th class="px-4 py-2 text-left">Date</th>
                                <th class="px-4 py-2 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach($reviews as $review)
                                <tr>
                                    <td class="px-4 py-2"><input type="checkbox" name="review_ids[]" value="{{ $review['id'] }}" class="review-checkbox"></td>
                                    <td class="px-4 py-2">
                                        <div class="font-medium">{{ $review['name'] ?? 'Anonymous' }}</div>
                                        <p class="text-sm text-muted-foreground">{{ Str::limit($review['comment'] ?? '', 100) }}</p>
                                        <small class="text-xs text-muted-foreground">{{ $review['email'] ?? 'No email' }}</small>
                                    </td>
                                    <td class="px-4 py-2">
                                        <div class="flex items-center gap-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-4 h-4 {{ $i <= ($review['rating'] ?? 0) ? 'text-warning' : 'text-muted-foreground' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81H7.03a1 1 0 00.95-.69l1.07-3.292z"/></svg>
                                            @endfor
                                            <span class="ml-2">{{ $review['rating'] ?? 0 }}/5</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2"><x-ui.badge variant="secondary">{{ $review['site_name'] ?? 'Unknown Site' }}</x-ui.badge></td>
                                    <td class="px-4 py-2">
                                        <x-ui.badge variant="{{ $review['status'] === 'approved' ? 'success' : ($review['status'] === 'rejected' ? 'destructive' : 'warning') }}">{{ ucfirst($review['status'] ?? 'pending') }}</x-ui.badge>
                                    </td>
                                    <td class="px-4 py-2"><small class="text-muted-foreground">{{ \Carbon\Carbon::parse($review['created_at'] ?? now())->format('M d, Y') }}</small></td>
                                    <td class="px-4 py-2">
                                        <div class="flex items-center gap-2">
                                            @if($review['status'] === 'pending')
                                                <form method="POST" action="{{ route('reviews.approve', ['review' => $review['id']]) }}" class="d-inline approve-form">
                                                    @csrf
                                                    <x-ui.button type="submit" variant="success" size="sm">Approve</x-ui.button>
                                                </form>
                                                <form method="POST" action="{{ route('reviews.reject', $review['id']) }}" class="d-inline">
                                                    @csrf
                                                    <x-ui.button type="submit" variant="destructive" size="sm">Reject</x-ui.button>
                                                </form>
                                            @endif
                                            <form method="POST" action="{{ route('reviews.destroy', $review['id']) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this review?')">
                                                @csrf
                                                @method('DELETE')
                                                <x-ui.button type="submit" variant="destructive" size="sm">Delete</x-ui.button>
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
            <div class="text-center py-12">
                <svg class="w-12 h-12 mx-auto text-muted-foreground mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81H7.03a1 1 0 00.95-.69l1.07-3.292z"/></svg>
                <h3 class="text-lg font-semibold">No Reviews Found</h3>
                <p class="text-muted-foreground">Reviews will appear here once users start submitting feedback.</p>
            </div>
        @endif
    </x-ui.card>
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
