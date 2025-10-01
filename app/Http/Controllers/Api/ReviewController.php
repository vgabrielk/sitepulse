<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReviewService;
use App\Models\Site;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    public function __construct(
        private ReviewService $reviewService
    ) {}

    public function index(Request $request, int $siteId): JsonResponse
    {
        $client = $request->user();
        
        $validator = Validator::make($request->all(), [
            'limit' => 'sometimes|integer|min:1|max:100',
            'offset' => 'sometimes|integer|min:0',
            'status' => 'sometimes|string|in:pending,approved,rejected',
            'rating' => 'sometimes|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $site = Site::where('id', $siteId)
                ->where('client_id', $client->id)
                ->first();
            
            if (!$site) {
                return response()->json([
                    'success' => false,
                    'message' => 'Site not found',
                ], 404);
            }
            
            $limit = $request->get('limit', 50);
            $offset = $request->get('offset', 0);
            $status = $request->get('status');
            $rating = $request->get('rating');
            
            if ($status) {
                $reviews = $this->reviewService->getReviewsByStatus($site, $status, $limit, $offset);
            } elseif ($rating) {
                $reviews = $this->reviewService->getReviewsByRating($site, $rating);
            } else {
                $reviews = $this->reviewService->getReviewsBySite($site, $limit, $offset);
            }
            
            return response()->json([
                'success' => true,
                'data' => $reviews,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get reviews',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getStats(Request $request, int $siteId): JsonResponse
    {
        $client = $request->user();
        
        $validator = Validator::make($request->all(), [
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $site = Site::where('id', $siteId)
                ->where('client_id', $client->id)
                ->first();
            
            if (!$site) {
                return response()->json([
                    'success' => false,
                    'message' => 'Site not found',
                ], 404);
            }
            
            $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
            $endDate = $request->get('end_date', now()->format('Y-m-d'));
            
            $stats = $this->reviewService->getReviewStats($site, $startDate, $endDate);
            
            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get review stats',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getOverview(Request $request, int $siteId): JsonResponse
    {
        $client = $request->user();
        
        try {
            $site = Site::where('id', $siteId)
                ->where('client_id', $client->id)
                ->first();
            
            if (!$site) {
                return response()->json([
                    'success' => false,
                    'message' => 'Site not found',
                ], 404);
            }
            
            $overallRating = $this->reviewService->getOverallRating($site);
            $ratingDistribution = $this->reviewService->getRatingDistribution($site);
            $recentReviews = $this->reviewService->getRecentReviews($site, 5);
            $topRatedReviews = $this->reviewService->getTopRatedReviews($site, 3);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'overall_rating' => $overallRating,
                    'rating_distribution' => $ratingDistribution,
                    'recent_reviews' => $recentReviews,
                    'top_rated_reviews' => $topRatedReviews,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get review overview',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getPending(Request $request, int $siteId): JsonResponse
    {
        $client = $request->user();
        
        try {
            $site = Site::where('id', $siteId)
                ->where('client_id', $client->id)
                ->first();
            
            if (!$site) {
                return response()->json([
                    'success' => false,
                    'message' => 'Site not found',
                ], 404);
            }
            
            $pendingReviews = $this->reviewService->getPendingReviews($site);
            
            return response()->json([
                'success' => true,
                'data' => $pendingReviews,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get pending reviews',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getApproved(Request $request, int $siteId): JsonResponse
    {
        $client = $request->user();
        
        $validator = Validator::make($request->all(), [
            'limit' => 'sometimes|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $site = Site::where('id', $siteId)
                ->where('client_id', $client->id)
                ->first();
            
            if (!$site) {
                return response()->json([
                    'success' => false,
                    'message' => 'Site not found',
                ], 404);
            }
            
            $limit = $request->get('limit', 10);
            $approvedReviews = $this->reviewService->getApprovedReviews($site, $limit);
            
            return response()->json([
                'success' => true,
                'data' => $approvedReviews,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get approved reviews',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getRejected(Request $request, int $siteId): JsonResponse
    {
        $client = $request->user();
        
        try {
            $site = Site::where('id', $siteId)
                ->where('client_id', $client->id)
                ->first();
            
            if (!$site) {
                return response()->json([
                    'success' => false,
                    'message' => 'Site not found',
                ], 404);
            }
            
            $rejectedReviews = $this->reviewService->getReviewsByStatus($site, 'rejected', 50, 0);
            
            return response()->json([
                'success' => true,
                'data' => $rejectedReviews,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get rejected reviews',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function search(Request $request, int $siteId): JsonResponse
    {
        $client = $request->user();
        
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|min:2|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $site = Site::where('id', $siteId)
                ->where('client_id', $client->id)
                ->first();
            
            if (!$site) {
                return response()->json([
                    'success' => false,
                    'message' => 'Site not found',
                ], 404);
            }
            
            $searchResults = $this->reviewService->searchReviews($site, $request->query);
            
            return response()->json([
                'success' => true,
                'data' => $searchResults,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search reviews',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function approve(Request $request, int $id): JsonResponse
    {
        $client = $request->user();
        
        try {
            $review = Review::where('id', $id)
                ->whereHas('site', function ($query) use ($client) {
                    $query->where('client_id', $client->id);
                })
                ->first();
            
            if (!$review) {
                return response()->json([
                    'success' => false,
                    'message' => 'Review not found',
                ], 404);
            }
            
            $approvedReview = $this->reviewService->approveReview($review);
            
            return response()->json([
                'success' => true,
                'message' => 'Review approved successfully',
                'data' => $approvedReview->toArray(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve review',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function reject(Request $request, int $id): JsonResponse
    {
        $client = $request->user();
        
        try {
            $review = Review::where('id', $id)
                ->whereHas('site', function ($query) use ($client) {
                    $query->where('client_id', $client->id);
                })
                ->first();
            
            if (!$review) {
                return response()->json([
                    'success' => false,
                    'message' => 'Review not found',
                ], 404);
            }
            
            $rejectedReview = $this->reviewService->rejectReview($review);
            
            return response()->json([
                'success' => true,
                'message' => 'Review rejected successfully',
                'data' => $rejectedReview->toArray(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject review',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function bulkApprove(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'review_ids' => 'required|array|min:1',
            'review_ids.*' => 'required|integer|exists:reviews,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $client = $request->user();
            $reviewIds = $request->review_ids;
            
            // Verify all reviews belong to client's sites
            $reviews = Review::whereIn('id', $reviewIds)
                ->whereHas('site', function ($query) use ($client) {
                    $query->where('client_id', $client->id);
                })
                ->get();
            
            if ($reviews->count() !== count($reviewIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Some reviews not found or not accessible',
                ], 404);
            }
            
            $approvedCount = $this->reviewService->bulkApproveReviews($reviewIds);
            
            return response()->json([
                'success' => true,
                'message' => "Successfully approved {$approvedCount} reviews",
                'data' => [
                    'approved_count' => $approvedCount,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to bulk approve reviews',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function bulkReject(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'review_ids' => 'required|array|min:1',
            'review_ids.*' => 'required|integer|exists:reviews,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $client = $request->user();
            $reviewIds = $request->review_ids;
            
            // Verify all reviews belong to client's sites
            $reviews = Review::whereIn('id', $reviewIds)
                ->whereHas('site', function ($query) use ($client) {
                    $query->where('client_id', $client->id);
                })
                ->get();
            
            if ($reviews->count() !== count($reviewIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Some reviews not found or not accessible',
                ], 404);
            }
            
            $rejectedCount = $this->reviewService->bulkRejectReviews($reviewIds);
            
            return response()->json([
                'success' => true,
                'message' => "Successfully rejected {$rejectedCount} reviews",
                'data' => [
                    'rejected_count' => $rejectedCount,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to bulk reject reviews',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $client = $request->user();
        
        try {
            $review = Review::where('id', $id)
                ->whereHas('site', function ($query) use ($client) {
                    $query->where('client_id', $client->id);
                })
                ->first();
            
            if (!$review) {
                return response()->json([
                    'success' => false,
                    'message' => 'Review not found',
                ], 404);
            }
            
            $this->reviewService->deleteReview($review);
            
            return response()->json([
                'success' => true,
                'message' => 'Review deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete review',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function exportReviews(Request $request, int $siteId): JsonResponse
    {
        $client = $request->user();
        
        $validator = Validator::make($request->all(), [
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
            'format' => 'sometimes|string|in:csv,excel,json',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $site = Site::where('id', $siteId)
                ->where('client_id', $client->id)
                ->first();
            
            if (!$site) {
                return response()->json([
                    'success' => false,
                    'message' => 'Site not found',
                ], 404);
            }
            
            $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
            $endDate = $request->get('end_date', now()->format('Y-m-d'));
            $format = $request->get('format', 'csv');
            
            $reviews = $this->reviewService->getReviewsByDateRange($site, $startDate, $endDate);
            
            // TODO: Implement actual export functionality
            return response()->json([
                'success' => true,
                'message' => 'Export functionality will be implemented',
                'data' => [
                    'reviews_count' => count($reviews),
                    'format' => $format,
                    'date_range' => [
                        'start' => $startDate,
                        'end' => $endDate,
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export reviews',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
