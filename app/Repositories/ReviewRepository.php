<?php

namespace App\Repositories;

use App\Models\Review;
use App\DTOs\ReviewDTO;
use Illuminate\Database\Eloquent\Collection;

class ReviewRepository
{
    public function findById(int $id): ?Review
    {
        return Review::find($id);
    }

    public function getBySiteId(int $siteId, int $limit = 50, int $offset = 0): Collection
    {
        return Review::where('site_id', $siteId)
            ->orderBy('submitted_at', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get();
    }

    public function getByStatus(int $siteId, string $status, int $limit = 50, int $offset = 0): Collection
    {
        return Review::where('site_id', $siteId)
            ->where('status', $status)
            ->orderBy('submitted_at', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get();
    }

    public function getByDateRange(int $siteId, string $startDate, string $endDate): Collection
    {
        return Review::where('site_id', $siteId)
            ->whereBetween('submitted_at', [$startDate, $endDate])
            ->orderBy('submitted_at', 'desc')
            ->get();
    }

    public function getApprovedBySiteId(int $siteId, int $limit = 10): Collection
    {
        return Review::where('site_id', $siteId)
            ->where('status', Review::STATUS_APPROVED)
            ->orderBy('submitted_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getPendingBySiteId(int $siteId): Collection
    {
        return Review::where('site_id', $siteId)
            ->where('status', Review::STATUS_PENDING)
            ->orderBy('submitted_at', 'asc')
            ->get();
    }

    public function create(array $data): Review
    {
        return Review::create($data);
    }

    public function update(Review $review, array $data): bool
    {
        return $review->update($data);
    }

    public function delete(Review $review): bool
    {
        return $review->delete();
    }

    public function approve(Review $review): bool
    {
        return $review->approve();
    }

    public function reject(Review $review): bool
    {
        return $review->reject();
    }

    public function getReviewStats(int $siteId, string $startDate, string $endDate): array
    {
        $totalReviews = Review::where('site_id', $siteId)
            ->whereBetween('submitted_at', [$startDate, $endDate])
            ->count();

        $approvedReviews = Review::where('site_id', $siteId)
            ->where('status', Review::STATUS_APPROVED)
            ->whereBetween('submitted_at', [$startDate, $endDate])
            ->count();

        $pendingReviews = Review::where('site_id', $siteId)
            ->where('status', Review::STATUS_PENDING)
            ->whereBetween('submitted_at', [$startDate, $endDate])
            ->count();

        $rejectedReviews = Review::where('site_id', $siteId)
            ->where('status', Review::STATUS_REJECTED)
            ->whereBetween('submitted_at', [$startDate, $endDate])
            ->count();

        $avgRating = Review::where('site_id', $siteId)
            ->whereBetween('submitted_at', [$startDate, $endDate])
            ->avg('rating');

        $ratingDistribution = Review::where('site_id', $siteId)
            ->whereBetween('submitted_at', [$startDate, $endDate])
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->orderBy('rating')
            ->get();

        return [
            'total_reviews' => $totalReviews,
            'approved_reviews' => $approvedReviews,
            'pending_reviews' => $pendingReviews,
            'rejected_reviews' => $rejectedReviews,
            'avg_rating' => round($avgRating ?? 0, 2),
            'rating_distribution' => $ratingDistribution,
        ];
    }

    public function getTopRatedReviews(int $siteId, int $limit = 5): Collection
    {
        return Review::where('site_id', $siteId)
            ->where('status', Review::STATUS_APPROVED)
            ->where('rating', 5)
            ->orderBy('submitted_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getRecentReviews(int $siteId, int $limit = 10): Collection
    {
        return Review::where('site_id', $siteId)
            ->where('status', Review::STATUS_APPROVED)
            ->orderBy('submitted_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getReviewsByRating(int $siteId, int $rating): Collection
    {
        return Review::where('site_id', $siteId)
            ->where('rating', $rating)
            ->where('status', Review::STATUS_APPROVED)
            ->orderBy('submitted_at', 'desc')
            ->get();
    }

    public function searchReviews(int $siteId, string $query): Collection
    {
        return Review::where('site_id', $siteId)
            ->where(function ($q) use ($query) {
                $q->where('visitor_name', 'like', "%{$query}%")
                  ->orWhere('comment', 'like', "%{$query}%");
            })
            ->orderBy('submitted_at', 'desc')
            ->get();
    }
}
