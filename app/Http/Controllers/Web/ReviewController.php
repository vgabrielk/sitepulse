<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\ReviewService;
use App\Models\Site;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function __construct(
        private ReviewService $reviewService
    ) {}

    public function index()
    {
        $user = Auth::user();
        $client = $user->client;
        
        if (!$client) {
            return redirect()->route('login')->with('error', 'Client not found.');
        }

        $sites = $client->sites()
            ->select(['id', 'name', 'domain', 'is_active', 'created_at'])
            ->withCount(['reviews', 'reviews as approved_reviews_count' => function($query) {
                $query->where('status', 'approved');
            }, 'reviews as pending_reviews_count' => function($query) {
                $query->where('status', 'pending');
            }])
            ->paginate(12);
        
        return view('dashboard.reviews.index', compact('sites'));
    }

    public function site(Site $site)
    {
        $user = Auth::user();
        $client = $user->client;
        
        if (!$client || $site->client_id !== $client->id) {
            abort(403, 'Unauthorized access to this site.');
        }
        
        $reviewsArray = $this->reviewService->getReviewsBySite($site, 100);
        $reviews = collect($reviewsArray)->map(function($review) use ($site) {
            $reviewData = $review->toArray();
            $reviewData['site_name'] = $site->name;
            return $reviewData;
        });

        $customization = $site->widget_customization ?? $site->getDefaultCustomization();

        return view('dashboard.reviews.site', compact('site', 'reviews', 'customization'));
    }


    public function approve(Review $review)
    {
        $user = Auth::user();
        $client = $user->client;
        
        if (!$client || $review->site->client_id !== $client->id) {
            abort(403, 'Unauthorized access to this review.');
        }
        
        try {
            $this->reviewService->approveReview($review);
            
            return redirect()->back()->with('success', 'Review approved successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to approve review: ' . $e->getMessage());
        }
    }

    public function reject(Review $review)
    {
        $user = Auth::user();
        $client = $user->client;
        
        if (!$client || $review->site->client_id !== $client->id) {
            abort(403, 'Unauthorized access to this review.');
        }
        
        try {
            $this->reviewService->rejectReview($review);
            
            return redirect()->back()->with('success', 'Review rejected successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to reject review: ' . $e->getMessage());
        }
    }

    public function bulkApprove(Request $request)
    {
        $request->validate([
            'review_ids' => 'required|array',
            'review_ids.*' => 'exists:reviews,id'
        ]);

        try {
            $this->reviewService->bulkApproveReviews($request->review_ids);
            
            return redirect()->back()->with('success', 'Reviews approved successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to approve reviews: ' . $e->getMessage());
        }
    }

    public function bulkReject(Request $request)
    {
        $request->validate([
            'review_ids' => 'required|array',
            'review_ids.*' => 'exists:reviews,id'
        ]);

        try {
            $this->reviewService->bulkRejectReviews($request->review_ids);
            
            return redirect()->back()->with('success', 'Reviews rejected successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to reject reviews: ' . $e->getMessage());
        }
    }

    public function destroy(Review $review)
    {
        $user = Auth::user();
        $client = $user->client;
        
        if (!$client || $review->site->client_id !== $client->id) {
            abort(403, 'Unauthorized access to this review.');
        }
        
        try {
            $this->reviewService->deleteReview($review);
            
            return redirect()->back()->with('success', 'Review deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete review: ' . $e->getMessage());
        }
    }
}
