<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviews - {{ $site->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }
        
        .reviews-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .reviews-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 16px;
            margin-top: 20px;
        }
        
        .reviews-header {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .reviews-title {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .reviews-subtitle {
            font-size: 14px;
            color: #7f8c8d;
        }
        
        .reviews-count {
            font-size: 12px;
            color: #95a5a6;
            margin-top: 5px;
            font-weight: 500;
        }
        
        .review-item {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border-left: 4px solid #3498db;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            height: fit-content;
        }
        
        .review-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
        }
        
        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }
        
        .reviewer-name {
            font-weight: 600;
            color: #2c3e50;
            font-size: 14px;
        }
        
        .review-date {
            font-size: 12px;
            color: #7f8c8d;
        }
        
        .review-rating {
            display: flex;
            gap: 2px;
            margin-bottom: 8px;
        }
        
        .star {
            color: #f39c12;
            font-size: 14px;
        }
        
        .star.empty {
            color: #ddd;
        }
        
        .review-comment {
            font-size: 14px;
            color: #555;
            line-height: 1.5;
        }
        
        .no-reviews {
            text-align: center;
            padding: 40px 20px;
            color: #7f8c8d;
        }
        
        .no-reviews-icon {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.5;
        }
        
        .no-reviews-text {
            font-size: 16px;
            margin-bottom: 8px;
        }
        
        .no-reviews-subtext {
            font-size: 14px;
        }
        
        .powered-by {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ecf0f1;
        }
        
        .powered-by-text {
            font-size: 12px;
            color: #95a5a6;
        }
        
        .powered-by-link {
            color: #3498db;
            text-decoration: none;
        }
        
        .powered-by-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="reviews-container">
        <div class="reviews-header">
            <div class="reviews-title">Customer Reviews</div>
            <div class="reviews-subtitle">{{ $site->name }}</div>
            @if($reviews->count() > 0)
                <div class="reviews-count">{{ $reviews->count() }} {{ $reviews->count() === 1 ? 'review' : 'reviews' }}</div>
            @endif
        </div>
        
        @if($reviews->count() > 0)
            <div class="reviews-grid">
                @foreach($reviews as $review)
                    <div class="review-item">
                        <div class="review-header">
                            <div class="reviewer-name">{{ $review->visitor_name ?: 'Anonymous' }}</div>
                            <div class="review-date">{{ $review->submitted_at->format('M d, Y') }}</div>
                        </div>
                        
                        <div class="review-rating">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="star {{ $i <= $review->rating ? '' : 'empty' }}">★</span>
                            @endfor
                        </div>
                        
                        @if($review->comment)
                            <div class="review-comment">{{ $review->comment }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="no-reviews">
                <div class="no-reviews-icon">⭐</div>
                <div class="no-reviews-text">No reviews yet</div>
                <div class="no-reviews-subtext">Be the first to share your experience!</div>
            </div>
        @endif
        
        <div class="powered-by">
            <div class="powered-by-text">
                Powered by <a href="https://sitepulse.com" class="powered-by-link" target="_blank">SitePulse</a>
            </div>
        </div>
    </div>
</body>
</html>
