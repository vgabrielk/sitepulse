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
            background: white;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            height: 100vh;
        }
        
        .reviews-container {
            margin: 0 auto;
            padding: 20px;
            width: 100%;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        
        .reviews-widget {
            background: white;
            border-radius: 16px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            padding: 32px;
            width: 100%;
            max-width: 1000px;
            border: 1px solid #f1f5f9;
            margin: 0 auto;
        }
        
        @media (max-width: 768px) {
            .reviews-container {
                max-width: 450px;
                padding: 16px;
            }
            
            .reviews-widget {
                max-width: 400px;
                padding: 24px;
            }
        }
        
        @media (max-width: 480px) {
            .reviews-container {
                max-width: 100%;
                padding: 12px;
            }
            
            .reviews-widget {
                max-width: 100%;
                padding: 20px;
                margin: 0 8px;
            }
        }
        
        .reviews-carousel {
            position: relative;
            width: 100%;
            overflow: hidden;
        }
        
        .reviews-track {
            display: flex;
            transition: transform 0.3s ease;
            width: 100%;
        }
        
        .review-slide {
            min-width: 100%;
            flex-shrink: 0;
        }
        
        .carousel-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 16px;
            padding: 0 8px;
        }
        
        .carousel-btn {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .carousel-btn:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
        }
        
        .carousel-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .carousel-indicators {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        
        .indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #cbd5e1;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        
        .indicator.active {
            background: #2563eb;
            transform: scale(1.2);
        }
        
        /* Desktop carousel improvements */
        @media (min-width: 768px) {
            .carousel-controls {
                margin-top: 24px;
                padding: 0 16px;
            }
            
            .carousel-btn {
                padding: 12px;
                border-radius: 10px;
            }
            
            .carousel-indicators {
                gap: 10px;
            }
            
            .indicator {
                width: 10px;
                height: 10px;
            }
        }
        
        /* Review Form Styles */
        .review-form-section {
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid #f1f5f9;
        }
        
        .form-toggle-btn {
            background: #2563eb;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0 auto;
        }
        
        .form-toggle-btn:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }
        
        .review-form {
            display: none;
            margin-top: 16px;
            padding: 16px;
            background: #f8fafc;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
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
            color: #374151;
            margin-bottom: 6px;
            font-size: 14px;
        }
        
        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.2s ease;
            box-sizing: border-box;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
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
            font-size: 20px;
            color: #d1d5db;
            cursor: pointer;
            transition: color 0.2s ease;
        }
        
        .star-label:hover,
        .star-label.active {
            color: #fbbf24;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        
        .submit-btn {
            background: #10b981;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .submit-btn:hover {
            background: #059669;
            transform: translateY(-1px);
        }
        
        .cancel-btn {
            background: #6b7280;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-left: 8px;
        }
        
        .cancel-btn:hover {
            background: #4b5563;
        }
        
        .btn-group {
            display: flex;
            gap: 8px;
            margin-top: 16px;
        }
        
        @media (max-width: 480px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .btn-group {
                flex-direction: column;
            }
            
            .cancel-btn {
                margin-left: 0;
            }
        }
        
            
            .form-control, .form-select {
                width: 100% !important;
                box-sizing: border-box;
            }
            
            .btn-group {
                flex-direction: column;
                gap: 10px;
            }
            
            .submit-btn, .cancel-btn {
                width: 100% !important;
                margin-left: 0 !important;
            }
        }
        
        .reviews-header {
            text-align: center;
            margin-bottom: 24px;
        }
        
        .reviews-title {
            font-size: 24px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 8px;
        }
        
        /* Desktop header improvements */
        @media (min-width: 768px) {
            .reviews-header {
                margin-bottom: 32px;
            }
            
            .reviews-title {
                font-size: 28px;
                margin-bottom: 12px;
            }
        }
        
        .reviews-subtitle {
            font-size: 14px;
            color: #64748b;
        }
        
        .reviews-count {
            font-size: 12px;
            color: #94a3b8;
            margin-top: 5px;
            font-weight: 500;
        }
        
        .review-item {
            margin-bottom: 24px;
            width: 100%;
            box-sizing: border-box;
            word-wrap: break-word;
            overflow-wrap: break-word;
            padding: 20px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        
        .review-item:last-child {
            margin-bottom: 0;
            border-bottom: none;
        }
        
        .review-header {
            display: flex;
            align-items: start;
            gap: 16px;
            margin-bottom: 16px;
        }
        
        .reviewer-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, #60a5fa, #2563eb);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 18px;
            flex-shrink: 0;
        }
        
        .reviewer-info {
            flex: 1;
        }
        
        .reviewer-name {
            font-weight: 600;
            color: #0f172a;
            font-size: 16px;
            margin-bottom: 2px;
        }
        
        .review-date {
            font-size: 12px;
            color: #64748b;
            margin-bottom: 8px;
        }
        
        .review-rating {
            display: flex;
            gap: 2px;
            margin-bottom: 0;
        }
        
        .star {
            color: #fbbf24;
            font-size: 16px;
        }
        
        .star.empty {
            color: #e5e7eb;
        }
        
        .review-comment {
            font-size: 14px;
            color: #475569;
            line-height: 1.625;
            word-wrap: break-word;
            overflow-wrap: break-word;
            hyphens: auto;
            max-width: 100%;
            margin-bottom: 8px;
            background: #f8fafc;
            padding: 16px;
            border-radius: 8px;
            border-left: 3px solid #2563eb;
            font-style: italic;
        }
        
        /* Desktop layout improvements */
        @media (min-width: 768px) {
            .review-item {
                padding: 32px 0;
            }
            
            .review-header {
                gap: 20px;
                margin-bottom: 20px;
            }
            
            .reviewer-avatar {
                width: 64px;
                height: 64px;
                font-size: 24px;
            }
            
            .reviewer-name {
                font-size: 20px;
                margin-bottom: 4px;
            }
            
            .review-date {
                font-size: 14px;
                margin-bottom: 10px;
            }
            
            .star {
                font-size: 20px;
                gap: 3px;
            }
            
            .review-comment {
                font-size: 18px;
                line-height: 1.7;
                padding: 20px;
                border-radius: 12px;
                margin-top: 4px;
            }
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
            margin-top: 24px;
            padding-top: 16px;
        }
        
        .powered-by-text {
            font-size: 12px;
            color: #94a3b8;
        }
        
        .powered-by-link {
            color: #2563eb;
            text-decoration: none;
            font-weight: 500;
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
        <div class="reviews-widget">
        <div class="reviews-header">
            <div class="reviews-title">O que dizem nossos clientes</div>
            @if($reviews->count() > 0)
                <div class="reviews-count">{{ $reviews->count() }} {{ $reviews->count() === 1 ? 'avaliação' : 'avaliações' }}</div>
            @endif
        </div>
        
        @if($reviews->count() > 0)
            <div class="reviews-carousel">
                <div class="reviews-track" id="reviewsTrack">
                    @foreach($reviews as $review)
                        <div class="review-slide">
                            <div class="review-item">
                                <div class="review-header">
                                    <div class="reviewer-avatar">
                                        {{ strtoupper(substr($review->visitor_name ?: 'A', 0, 1)) }}
                                    </div>
                                    <div class="reviewer-info">
                                        <div class="reviewer-name">{{ $review->visitor_name ?: 'Anônimo' }}</div>
                                        <div class="review-date">{{ $review->submitted_at->format('d/m/Y') }}</div>
                                        <div class="review-rating">
                                            @for($i = 1; $i <= 5; $i++)
                                                <span class="star {{ $i <= $review->rating ? '' : 'empty' }}">★</span>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                                
                                @if($review->comment)
                                    <div class="review-comment">"{{ $review->comment }}"</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                
                @if($reviews->count() > 1)
                    <div class="carousel-controls">
                        <button class="carousel-btn" id="prevBtn" onclick="previousReview()">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        
                        <div class="carousel-indicators" id="indicators">
                            @for($i = 0; $i < $reviews->count(); $i++)
                                <div class="indicator {{ $i === 0 ? 'active' : '' }}" onclick="goToReview({{ $i }})"></div>
                            @endfor
                        </div>
                        
                        <button class="carousel-btn" id="nextBtn" onclick="nextReview()">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                @endif
            </div>
        @else
            <div class="no-reviews">
                <div class="no-reviews-icon">⭐</div>
                <div class="no-reviews-text">Nenhuma avaliação ainda</div>
                <div class="no-reviews-subtext">Seja o primeiro a compartilhar sua experiência!</div>
            </div>
        @endif
        
        <!-- Review Form Section -->
        <div class="review-form-section">
            <button class="form-toggle-btn" onclick="toggleReviewForm()">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
                Deixar Avaliação
            </button>
            
            <div class="review-form" id="reviewForm">
                <form method="POST" action="{{ route('widget.submit-review.post', $site->widget_id) }}">
                    @csrf
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="visitor_name">Seu Nome *</label>
                            <input type="text" class="form-control" id="visitor_name" name="visitor_name" placeholder="Digite seu nome" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="visitor_email">Email *</label>
                            <input type="email" class="form-control" id="visitor_email" name="visitor_email" placeholder="seu@email.com" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Sua Avaliação *</label>
                        <div class="star-rating">
                            @for($i = 1; $i <= 5; $i++)
                                <input type="radio" name="rating" value="{{ $i }}" id="star{{ $i }}" class="star-input" required>
                                <label for="star{{ $i }}" class="star-label">★</label>
                            @endfor
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="comment">Seu Comentário</label>
                        <textarea class="form-control" id="comment" name="comment" rows="3" placeholder="Compartilhe sua experiência..."></textarea>
                    </div>
                    
                    <div class="btn-group">
                        <button type="submit" class="submit-btn">Enviar Avaliação</button>
                        <button type="button" class="cancel-btn" onclick="toggleReviewForm()">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
        
            <div class="powered-by">
                <div class="powered-by-text">
                    Powered by <a href="https://sitepulse.com" class="powered-by-link" target="_blank">SitePulse</a>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Carousel functionality
        let currentReviewIndex = 0;
        const totalReviews = {{ $reviews->count() }};
        
        function updateCarousel() {
            const track = document.getElementById('reviewsTrack');
            const indicators = document.querySelectorAll('.indicator');
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            
            if (track) {
                track.style.transform = `translateX(-${currentReviewIndex * 100}%)`;
            }
            
            // Update indicators
            indicators.forEach((indicator, index) => {
                indicator.classList.toggle('active', index === currentReviewIndex);
            });
            
            // Update button states
            if (prevBtn) prevBtn.disabled = currentReviewIndex === 0;
            if (nextBtn) nextBtn.disabled = currentReviewIndex === totalReviews - 1;
        }
        
        function nextReview() {
            if (currentReviewIndex < totalReviews - 1) {
                currentReviewIndex++;
                updateCarousel();
            }
        }
        
        function previousReview() {
            if (currentReviewIndex > 0) {
                currentReviewIndex--;
                updateCarousel();
            }
        }
        
        function goToReview(index) {
            currentReviewIndex = index;
            updateCarousel();
        }
        
        // Review form functionality
        function toggleReviewForm() {
            const form = document.getElementById('reviewForm');
            const btn = document.querySelector('.form-toggle-btn');
            
            if (form.classList.contains('show')) {
                form.classList.remove('show');
                btn.innerHTML = `
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                    Deixar Avaliação
                `;
            } else {
                form.classList.add('show');
                btn.innerHTML = `
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Fechar Formulário
                `;
            }
        }
        
        // Star rating functionality
        document.addEventListener('DOMContentLoaded', function() {
            const starLabels = document.querySelectorAll('.star-label');
            const starInputs = document.querySelectorAll('.star-input');
            
            starLabels.forEach((label, index) => {
                label.addEventListener('mouseenter', function() {
                    // Highlight stars up to this one
                    starLabels.forEach((star, i) => {
                        star.classList.toggle('active', i <= index);
                    });
                });
                
                label.addEventListener('mouseleave', function() {
                    // Reset to selected rating
                    const selectedRating = document.querySelector('.star-input:checked');
                    const selectedIndex = selectedRating ? parseInt(selectedRating.value) - 1 : -1;
                    
                    starLabels.forEach((star, i) => {
                        star.classList.toggle('active', i <= selectedIndex);
                    });
                });
                
                label.addEventListener('click', function() {
                    starInputs[index].checked = true;
                    
                    // Update visual state
                    starLabels.forEach((star, i) => {
                        star.classList.toggle('active', i <= index);
                    });
                });
            });
            
            // Initialize carousel
            if (totalReviews > 0) {
                updateCarousel();
            }
            
            // Auto-advance carousel every 5 seconds (optional)
            if (totalReviews > 1) {
                setInterval(function() {
                    if (currentReviewIndex < totalReviews - 1) {
                        nextReview();
                    } else {
                        currentReviewIndex = 0;
                        updateCarousel();
                    }
                }, 5000);
            }
        });
    </script>
</body>
</html>
