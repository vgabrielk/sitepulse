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
        
        /* Review Form Styles */
        .review-form-container {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .review-form-toggle {
            background: #3498db;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .review-form-toggle:hover {
            background: #2980b9;
            transform: translateY(-1px);
        }
        
        .review-form {
            display: none;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ecf0f1;
        }
        
        .review-form.show {
            display: block;
        }
        
        .form-group {
            margin-bottom: 16px;
        }
        
        .form-label {
            display: block;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 6px;
            font-size: 14px;
        }
        
        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 2px solid #ecf0f1;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #3498db;
        }
        
        .star-rating {
            display: flex;
            gap: 4px;
            margin-bottom: 16px;
        }
        
        .star-input {
            display: none;
        }
        
        .star-label {
            font-size: 24px;
            color: #ddd;
            cursor: pointer;
            transition: color 0.2s ease;
        }
        
        .star-label:hover,
        .star-label.active {
            color: #f39c12;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        
        .submit-btn {
            background: #27ae60;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .submit-btn:hover {
            background: #229954;
            transform: translateY(-1px);
        }
        
        .cancel-btn {
            background: #95a5a6;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-left: 10px;
        }
        
        .cancel-btn:hover {
            background: #7f8c8d;
        }
        
        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 16px;
        }
        
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 16px;
            font-size: 14px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="reviews-container">
        <div class="reviews-header">
            <div class="reviews-title">Customer Feedbacks</div>
            <div class="reviews-subtitle">{{ $site->name }}</div>
            @if($reviews->count() > 0)
                <div class="reviews-count">{{ $reviews->count() }} {{ $reviews->count() === 1 ? 'review' : 'reviews' }}</div>
            @endif
        </div>
        
        <!-- Review Form Toggle -->
        <div class="review-form-container">
            <button class="review-form-toggle" onclick="toggleReviewForm()">
                <span>⭐</span>
                Write a Review
            </button>
            
            <!-- Review Form -->
            <div class="review-form" id="reviewForm">
                <form method="POST" action="{{ route('widget.submit-review.post', $site->widget_id) }}">
                    @csrf
                    
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="visitor_name">Your Name *</label>
                            <input type="text" 
                                   class="form-control @error('visitor_name') is-invalid @enderror" 
                                   id="visitor_name" 
                                   name="visitor_name" 
                                   value="{{ old('visitor_name') }}" 
                                   placeholder="Enter your name"
                                   required>
                            @error('visitor_name')
                                <div class="text-danger" style="font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="visitor_email">Email Address *</label>
                            <input type="email" 
                                   class="form-control @error('visitor_email') is-invalid @enderror" 
                                   id="visitor_email" 
                                   name="visitor_email" 
                                   value="{{ old('visitor_email') }}" 
                                   placeholder="your@email.com"
                                   required>
                            @error('visitor_email')
                                <div class="text-danger" style="font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Your Rating *</label>
                        <div class="star-rating">
                            <input type="radio" name="rating" value="1" id="star1" class="star-input" {{ old('rating') == '1' ? 'checked' : '' }}>
                            <label for="star1" class="star-label">★</label>
                            
                            <input type="radio" name="rating" value="2" id="star2" class="star-input" {{ old('rating') == '2' ? 'checked' : '' }}>
                            <label for="star2" class="star-label">★</label>
                            
                            <input type="radio" name="rating" value="3" id="star3" class="star-input" {{ old('rating') == '3' ? 'checked' : '' }}>
                            <label for="star3" class="star-label">★</label>
                            
                            <input type="radio" name="rating" value="4" id="star4" class="star-input" {{ old('rating') == '4' ? 'checked' : '' }}>
                            <label for="star4" class="star-label">★</label>
                            
                            <input type="radio" name="rating" value="5" id="star5" class="star-input" {{ old('rating') == '5' ? 'checked' : '' }}>
                            <label for="star5" class="star-label">★</label>
                        </div>
                        @error('rating')
                            <div class="text-danger" style="font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="comment">Your Review</label>
                        <textarea class="form-control @error('comment') is-invalid @enderror" 
                                  id="comment" 
                                  name="comment" 
                                  rows="4" 
                                  placeholder="Share your experience...">{{ old('comment') }}</textarea>
                        @error('comment')
                            <div class="text-danger" style="font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="btn-group">
                        <button type="submit" class="submit-btn">
                            Submit Review
                        </button>
                        <button type="button" class="cancel-btn" onclick="toggleReviewForm()">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
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
    
    <script>
        function toggleReviewForm() {
            const form = document.getElementById('reviewForm');
            const toggle = document.querySelector('.review-form-toggle');
            
            if (form.classList.contains('show')) {
                form.classList.remove('show');
                toggle.innerHTML = '<span>⭐</span> Write a Review';
            } else {
                form.classList.add('show');
                toggle.innerHTML = '<span>✖</span> Cancel';
            }
        }
        
        // Star rating functionality
        document.addEventListener('DOMContentLoaded', function() {
            const starInputs = document.querySelectorAll('.star-input');
            const starLabels = document.querySelectorAll('.star-label');
            
            starLabels.forEach((label, index) => {
                label.addEventListener('click', function() {
                    const rating = index + 1;
                    const input = document.getElementById('star' + rating);
                    input.checked = true;
                    updateStarDisplay(rating);
                });
                
                label.addEventListener('mouseenter', function() {
                    const rating = index + 1;
                    updateStarDisplay(rating);
                });
            });
            
            document.querySelector('.star-rating').addEventListener('mouseleave', function() {
                const checkedInput = document.querySelector('.star-input:checked');
                if (checkedInput) {
                    const rating = parseInt(checkedInput.value);
                    updateStarDisplay(rating);
                } else {
                    updateStarDisplay(0);
                }
            });
            
            function updateStarDisplay(rating) {
                starLabels.forEach((label, index) => {
                    if (index < rating) {
                        label.classList.add('active');
                    } else {
                        label.classList.remove('active');
                    }
                });
            }
        });
    </script>
</body>
</html>
