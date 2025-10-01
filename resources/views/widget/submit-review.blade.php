<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Review - {{ $site->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .review-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .star-rating {
            display: flex;
            gap: 5px;
            justify-content: center;
            margin: 20px 0;
        }
        .star {
            font-size: 2rem;
            color: #ddd;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .star:hover,
        .star.active {
            color: #ffc107;
            transform: scale(1.1);
        }
        .btn-submit {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .site-info {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="review-container p-4">
                    <!-- Site Info -->
                    <div class="site-info text-center">
                        <h2 class="mb-2">{{ $site->name }}</h2>
                        <p class="mb-0">Share your experience with us</p>
                    </div>

                    <!-- Success/Error Messages -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Review Form -->
                    <form method="POST" action="{{ route('widget.submit-review.post', $site->widget_id) }}">
                        @csrf
                        
                        <!-- Name and Email -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="visitor_name" class="form-label fw-bold">
                                    <i class="fas fa-user me-1"></i>Your Name *
                                </label>
                                <input type="text" 
                                       class="form-control @error('visitor_name') is-invalid @enderror" 
                                       id="visitor_name" 
                                       name="visitor_name" 
                                       value="{{ old('visitor_name') }}" 
                                       placeholder="Enter your name"
                                       required>
                                @error('visitor_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="visitor_email" class="form-label fw-bold">
                                    <i class="fas fa-envelope me-1"></i>Email Address *
                                </label>
                                <input type="email" 
                                       class="form-control @error('visitor_email') is-invalid @enderror" 
                                       id="visitor_email" 
                                       name="visitor_email" 
                                       value="{{ old('visitor_email') }}" 
                                       placeholder="your@email.com"
                                       required>
                                @error('visitor_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Star Rating -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-center d-block">
                                <i class="fas fa-star me-1"></i>Your Rating *
                            </label>
                            <div class="star-rating" id="starRating">
                                <span class="star" data-rating="1">★</span>
                                <span class="star" data-rating="2">★</span>
                                <span class="star" data-rating="3">★</span>
                                <span class="star" data-rating="4">★</span>
                                <span class="star" data-rating="5">★</span>
                            </div>
                            <input type="hidden" name="rating" id="ratingInput" value="{{ old('rating', '') }}" required>
                            @error('rating')
                                <div class="text-danger text-center mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Comment -->
                        <div class="mb-4">
                            <label for="comment" class="form-label fw-bold">
                                <i class="fas fa-comment me-1"></i>Your Review (Optional)
                            </label>
                            <textarea class="form-control @error('comment') is-invalid @enderror" 
                                      id="comment" 
                                      name="comment" 
                                      rows="4" 
                                      placeholder="Tell us about your experience...">{{ old('comment') }}</textarea>
                            @error('comment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="text-center">
                            <button type="submit" class="btn btn-submit text-white">
                                <i class="fas fa-paper-plane me-2"></i>
                                Submit Review
                            </button>
                        </div>
                    </form>

                    <!-- Footer -->
                    <div class="text-center mt-4">
                        <small class="text-muted">
                            <i class="fas fa-shield-alt me-1"></i>
                            Your review will be moderated before being published
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Star rating functionality
        document.addEventListener('DOMContentLoaded', function() {
            const stars = document.querySelectorAll('.star');
            const ratingInput = document.getElementById('ratingInput');
            const oldRating = '{{ old("rating") }}';
            
            // Set initial rating if there's an old value
            if (oldRating) {
                setRating(parseInt(oldRating));
            }
            
            stars.forEach((star, index) => {
                star.addEventListener('click', function() {
                    const rating = parseInt(this.dataset.rating);
                    setRating(rating);
                });
                
                star.addEventListener('mouseenter', function() {
                    const rating = parseInt(this.dataset.rating);
                    highlightStars(rating);
                });
            });
            
            document.getElementById('starRating').addEventListener('mouseleave', function() {
                const currentRating = parseInt(ratingInput.value) || 0;
                highlightStars(currentRating);
            });
            
            function setRating(rating) {
                ratingInput.value = rating;
                highlightStars(rating);
            }
            
            function highlightStars(rating) {
                stars.forEach((star, index) => {
                    if (index < rating) {
                        star.classList.add('active');
                    } else {
                        star.classList.remove('active');
                    }
                });
            }
        });
    </script>
</body>
</html>
