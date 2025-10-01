<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\SiteController;
use App\Http\Controllers\Web\AnalyticsController;
use App\Http\Controllers\Web\ReviewController;
use App\Http\Controllers\Web\ExportController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\SettingsController;
use App\Http\Controllers\Web\BillingController;
use App\Http\Controllers\Web\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public routes
Route::get('/', function () {
    return view('welcome');
});

// Auth routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password reset routes
Route::get('/password/reset', [AuthController::class, 'showPasswordReset'])->name('password.request');
Route::post('/password/email', [AuthController::class, 'passwordReset'])->name('password.email');
Route::get('/password/reset/{token}', [AuthController::class, 'showPasswordResetForm'])->name('password.reset');
Route::post('/password/reset', [AuthController::class, 'passwordResetUpdate'])->name('password.update');

// Protected routes
Route::middleware(['auth:web'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Sites
    Route::resource('sites', SiteController::class);
    Route::post('/sites/{site}/toggle-status', [SiteController::class, 'toggleStatus'])->name('sites.toggle-status');
    
    // Analytics
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/', [AnalyticsController::class, 'overview'])->name('overview');
        Route::get('/sites/{site}', [AnalyticsController::class, 'site'])->name('site');
        Route::get('/sites/{site}/sessions', [AnalyticsController::class, 'sessions'])->name('sessions');
        Route::get('/sites/{site}/events', [AnalyticsController::class, 'events'])->name('events');
        Route::get('/sites/{site}/pages', [AnalyticsController::class, 'pages'])->name('pages');
        Route::get('/sites/{site}/heatmap', [AnalyticsController::class, 'heatmap'])->name('heatmap');
    });
    
    // Reviews
    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::get('/', [ReviewController::class, 'index'])->name('index');
        Route::get('/sites/{site}', [ReviewController::class, 'site'])->name('site');
        Route::post('/{review}/approve', [ReviewController::class, 'approve'])->name('approve');
        Route::post('/{review}/reject', [ReviewController::class, 'reject'])->name('reject');
        Route::post('/bulk-approve', [ReviewController::class, 'bulkApprove'])->name('bulk-approve');
        Route::post('/bulk-reject', [ReviewController::class, 'bulkReject'])->name('bulk-reject');
        Route::delete('/{review}', [ReviewController::class, 'destroy'])->name('destroy');
    });
    
    // Exports
    Route::prefix('exports')->name('exports.')->group(function () {
        Route::get('/', [ExportController::class, 'index'])->name('index');
        Route::post('/analytics', [ExportController::class, 'analytics'])->name('analytics');
        Route::post('/reviews', [ExportController::class, 'reviews'])->name('reviews');
        Route::post('/events', [ExportController::class, 'events'])->name('events');
    });
    
    // Profile & Settings
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::get('/billing', [BillingController::class, 'index'])->name('billing');
    Route::post('/billing/upgrade', [BillingController::class, 'upgrade'])->name('billing.upgrade');
});

// Widget routes (public)
Route::get('/widget/{widgetId}.js', function ($widgetId) {
    $site = \App\Models\Site::where('widget_id', $widgetId)->first();
    
    if (!$site || !$site->is_active) {
        return response('// SitePulse: Site not found or inactive', 404)
            ->header('Content-Type', 'application/javascript');
    }
    
    $script = view('widget.script', compact('site'))->render();
    
    return response($script)
        ->header('Content-Type', 'application/javascript')
        ->header('Cache-Control', 'public, max-age=3600');
})->name('widget.script');

// Review iframe route
Route::get('/widget/{widgetId}/reviews', function ($widgetId) {
    $site = \App\Models\Site::where('widget_id', $widgetId)->first();
    
    if (!$site || !$site->is_active) {
        return response('Site not found or inactive', 404);
    }
    
    $reviews = \App\Models\Review::where('site_id', $site->id)
        ->where('status', 'approved')
        ->orderBy('submitted_at', 'desc')
        ->limit(10)
        ->get();
    
    return view('widget.reviews', compact('site', 'reviews'));
})->name('widget.reviews');

// Public review submission
Route::get('/widget/{widgetId}/submit-review', function ($widgetId) {
    $site = \App\Models\Site::where('widget_id', $widgetId)->first();
    
    if (!$site || !$site->is_active) {
        return response('Site not found or inactive', 404);
    }
    
    return view('widget.submit-review', compact('site'));
})->name('widget.submit-review');

Route::post('/widget/{widgetId}/submit-review', function ($widgetId, \Illuminate\Http\Request $request) {
    $site = \App\Models\Site::where('widget_id', $widgetId)->first();
    
    if (!$site || !$site->is_active) {
        return response('Site not found or inactive', 404);
    }

    $request->validate([
        'visitor_name' => 'required|string|max:255',
        'visitor_email' => 'required|email|max:255',
        'rating' => 'required|numeric|min:1|max:5',
        'comment' => 'nullable|string|max:1000',
    ]);

    try {
        $data = $request->only(['visitor_name', 'visitor_email', 'rating', 'comment']);
        $data['ip_address'] = $request->ip();
        $data['submitted_at'] = now();
        $data['status'] = 'pending';
        
        $review = \App\Models\Review::create([
            'site_id' => $site->id,
            'visitor_name' => $data['visitor_name'],
            'visitor_email' => $data['visitor_email'],
            'rating' => $data['rating'],
            'comment' => $data['comment'],
            'ip_address' => $data['ip_address'],
            'submitted_at' => $data['submitted_at'],
            'status' => $data['status'],
        ]);
        
        \Log::info('Review created successfully', ['review_id' => $review->id, 'site_id' => $site->id]);
        
        return redirect()->back()->with('success', 'Review submitted successfully! It will be reviewed before being published.');
    } catch (\Exception $e) {
        \Log::error('Failed to create review', ['error' => $e->getMessage(), 'site_id' => $site->id]);
        return redirect()->back()->with('error', 'Failed to submit review: ' . $e->getMessage());
    }
})->name('widget.submit-review.post');