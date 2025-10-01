<?php

namespace App\Services;

use App\Models\Site;
use App\Models\Review;
use App\Models\Session;
use App\Repositories\ReviewRepository;
use App\DTOs\ReviewDTO;
use Illuminate\Database\Eloquent\Collection;

class ReviewService
{
    public function __construct(
        private ReviewRepository $reviewRepository
    ) {}

    public function createReview(Site $site, array $data, ?Session $session = null): ReviewDTO
    {
        $data['site_id'] = $site->id;
        $data['session_id'] = $session?->id;
        $data['submitted_at'] = now();
        $data['status'] = Review::STATUS_PENDING;
        
        $review = $this->reviewRepository->create($data);
        
        return ReviewDTO::fromModel($review);
    }

    public function getReviewById(int $id): ?ReviewDTO
    {
        $review = $this->reviewRepository->findById($id);
        
        return $review ? ReviewDTO::fromModel($review) : null;
    }

    public function getReviewsBySite(Site $site, int $limit = 50, int $offset = 0): array
    {
        $reviews = $this->reviewRepository->getBySiteId($site->id, $limit, $offset);
        
        return $reviews->map(fn($review) => ReviewDTO::fromModel($review))->toArray();
    }

    public function getReviewsByStatus(Site $site, string $status, int $limit = 50, int $offset = 0): array
    {
        $reviews = $this->reviewRepository->getByStatus($site->id, $status, $limit, $offset);
        
        return $reviews->map(fn($review) => ReviewDTO::fromModel($review))->toArray();
    }

    public function getApprovedReviews(Site $site, int $limit = 10): array
    {
        $reviews = $this->reviewRepository->getApprovedBySiteId($site->id, $limit);
        
        return $reviews->map(fn($review) => ReviewDTO::fromModel($review))->toArray();
    }

    public function getPendingReviews(Site $site): array
    {
        $reviews = $this->reviewRepository->getPendingBySiteId($site->id);
        
        return $reviews->map(fn($review) => ReviewDTO::fromModel($review))->toArray();
    }

    public function getRecentReviews(Site $site, int $limit = 10): array
    {
        $reviews = $this->reviewRepository->getRecentReviews($site->id, $limit);
        
        return $reviews->map(fn($review) => ReviewDTO::fromModel($review))->toArray();
    }

    public function getTopRatedReviews(Site $site, int $limit = 5): array
    {
        $reviews = $this->reviewRepository->getTopRatedReviews($site->id, $limit);
        
        return $reviews->map(fn($review) => ReviewDTO::fromModel($review))->toArray();
    }

    public function getReviewsByRating(Site $site, int $rating): array
    {
        $reviews = $this->reviewRepository->getReviewsByRating($site->id, $rating);
        
        return $reviews->map(fn($review) => ReviewDTO::fromModel($review))->toArray();
    }

    public function searchReviews(Site $site, string $query): array
    {
        $reviews = $this->reviewRepository->searchReviews($site->id, $query);
        
        return $reviews->map(fn($review) => ReviewDTO::fromModel($review))->toArray();
    }

    public function approveReview(Review $review): ReviewDTO
    {
        $this->reviewRepository->approve($review);
        
        return ReviewDTO::fromModel($review->fresh());
    }

    public function rejectReview(Review $review): ReviewDTO
    {
        $this->reviewRepository->reject($review);
        
        return ReviewDTO::fromModel($review->fresh());
    }

    public function updateReview(Review $review, array $data): ReviewDTO
    {
        $this->reviewRepository->update($review, $data);
        
        return ReviewDTO::fromModel($review->fresh());
    }

    public function deleteReview(Review $review): bool
    {
        return $this->reviewRepository->delete($review);
    }

    public function getReviewStats(Site $site, string $startDate, string $endDate): array
    {
        return $this->reviewRepository->getReviewStats($site->id, $startDate, $endDate);
    }

    public function getOverallRating(Site $site): float
    {
        $avgRating = Review::where('site_id', $site->id)
            ->where('status', Review::STATUS_APPROVED)
            ->avg('rating');
        
        return round($avgRating ?? 0, 2);
    }

    public function getRatingDistribution(Site $site): array
    {
        $distribution = Review::where('site_id', $site->id)
            ->where('status', Review::STATUS_APPROVED)
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->orderBy('rating')
            ->get();
        
        $total = $distribution->sum('count');
        
        return $distribution->map(function ($item) use ($total) {
            return [
                'rating' => $item->rating,
                'count' => $item->count,
                'percentage' => $total > 0 ? round(($item->count / $total) * 100, 2) : 0,
            ];
        })->toArray();
    }

    public function getRecentReviewsByRating(Site $site, int $rating, int $limit = 5): array
    {
        $reviews = Review::where('site_id', $site->id)
            ->where('rating', $rating)
            ->where('status', Review::STATUS_APPROVED)
            ->orderBy('submitted_at', 'desc')
            ->limit($limit)
            ->get();
        
        return $reviews->map(fn($review) => ReviewDTO::fromModel($review))->toArray();
    }

    public function getReviewsByDateRange(Site $site, string $startDate, string $endDate): array
    {
        $reviews = $this->reviewRepository->getByDateRange($site->id, $startDate, $endDate);
        
        return $reviews->map(fn($review) => ReviewDTO::fromModel($review))->toArray();
    }

    public function bulkApproveReviews(array $reviewIds): int
    {
        $count = 0;
        foreach ($reviewIds as $reviewId) {
            $review = Review::find($reviewId);
            if ($review && $review->isPending()) {
                $this->reviewRepository->approve($review);
                $count++;
            }
        }
        
        return $count;
    }

    public function bulkRejectReviews(array $reviewIds): int
    {
        $count = 0;
        foreach ($reviewIds as $reviewId) {
            $review = Review::find($reviewId);
            if ($review && $review->isPending()) {
                $this->reviewRepository->reject($review);
                $count++;
            }
        }
        
        return $count;
    }

    public function validateReviewData(array $data): array
    {
        $errors = [];
        
        if (!isset($data['rating']) || !is_numeric($data['rating'])) {
            $errors[] = 'Rating is required and must be numeric';
        } elseif ($data['rating'] < 1 || $data['rating'] > 5) {
            $errors[] = 'Rating must be between 1 and 5';
        }
        
        if (isset($data['visitor_email']) && !filter_var($data['visitor_email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format';
        }
        
        if (isset($data['comment']) && strlen($data['comment']) > 1000) {
            $errors[] = 'Comment must be less than 1000 characters';
        }
        
        return $errors;
    }
}
