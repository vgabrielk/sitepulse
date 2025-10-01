<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SiteController;
use App\Http\Controllers\Api\WidgetController;
use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\ReviewController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Widget routes (public)
Route::get('/widget/{widgetId}.js', [WidgetController::class, 'getScript']);
Route::post('/widget/events', [WidgetController::class, 'trackEvents']);
Route::post('/widget/review', [WidgetController::class, 'submitReview']);
Route::get('/widget/reviews', [WidgetController::class, 'getReviews']);
Route::get('/widget/config/{widgetId}', [WidgetController::class, 'getConfig']);

// Protected routes (require API key authentication)
Route::middleware('auth:api')->group(function () {
    
    // Auth routes
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::put('/auth/profile', [AuthController::class, 'updateProfile']);
    Route::post('/auth/regenerate-key', [AuthController::class, 'regenerateApiKey']);
    Route::get('/auth/stats', [AuthController::class, 'getStats']);
    
    // Site routes
    Route::apiResource('sites', SiteController::class)->names([
        'index' => 'api.sites.index',
        'store' => 'api.sites.store',
        'show' => 'api.sites.show',
        'update' => 'api.sites.update',
        'destroy' => 'api.sites.destroy',
    ]);
    Route::get('/sites/{id}/stats', [SiteController::class, 'getStats'])->name('api.sites.stats');
    Route::get('/sites/{id}/metrics', [SiteController::class, 'getMetrics'])->name('api.sites.metrics');
    Route::get('/sites/{id}/widget-code', [SiteController::class, 'getWidgetCode'])->name('api.sites.widget-code');
    
    // Analytics routes
    Route::prefix('analytics')->group(function () {
        Route::get('/sites/{siteId}/overview', [AnalyticsController::class, 'getOverview']);
        Route::get('/sites/{siteId}/sessions', [AnalyticsController::class, 'getSessions']);
        Route::get('/sites/{siteId}/events', [AnalyticsController::class, 'getEvents']);
        Route::get('/sites/{siteId}/top-pages', [AnalyticsController::class, 'getTopPages']);
        Route::get('/sites/{siteId}/top-events', [AnalyticsController::class, 'getTopEvents']);
        Route::get('/sites/{siteId}/heatmap', [AnalyticsController::class, 'getHeatmapData']);
        Route::get('/sites/{siteId}/real-time', [AnalyticsController::class, 'getRealTimeMetrics']);
        Route::get('/sites/{siteId}/trends', [AnalyticsController::class, 'getTrendData']);
    });
    
    // Review routes
    Route::prefix('reviews')->group(function () {
        Route::get('/sites/{siteId}', [ReviewController::class, 'index']);
        Route::get('/sites/{siteId}/stats', [ReviewController::class, 'getStats']);
        Route::get('/sites/{siteId}/overview', [ReviewController::class, 'getOverview']);
        Route::get('/sites/{siteId}/pending', [ReviewController::class, 'getPending']);
        Route::get('/sites/{siteId}/approved', [ReviewController::class, 'getApproved']);
        Route::get('/sites/{siteId}/rejected', [ReviewController::class, 'getRejected']);
        Route::get('/sites/{siteId}/search', [ReviewController::class, 'search']);
        Route::post('/{id}/approve', [ReviewController::class, 'approve']);
        Route::post('/{id}/reject', [ReviewController::class, 'reject']);
        Route::post('/bulk-approve', [ReviewController::class, 'bulkApprove']);
        Route::post('/bulk-reject', [ReviewController::class, 'bulkReject']);
        Route::delete('/{id}', [ReviewController::class, 'destroy']);
    });
    
    // Export routes
    Route::prefix('exports')->group(function () {
        Route::get('/sites/{siteId}/analytics', [AnalyticsController::class, 'exportAnalytics']);
        Route::get('/sites/{siteId}/reviews', [ReviewController::class, 'exportReviews']);
        Route::get('/sites/{siteId}/events', [AnalyticsController::class, 'exportEvents']);
    });
});

// Admin routes (require admin authentication)
Route::middleware(['auth:api', 'admin'])->prefix('admin')->group(function () {
    
    // Client management
    Route::get('/clients', [AuthController::class, 'getAllClients']);
    Route::get('/clients/{id}', [AuthController::class, 'getClient']);
    Route::put('/clients/{id}', [AuthController::class, 'updateClient']);
    Route::delete('/clients/{id}', [AuthController::class, 'deleteClient']);
    Route::post('/clients/{id}/activate', [AuthController::class, 'activateClient']);
    Route::post('/clients/{id}/deactivate', [AuthController::class, 'deactivateClient']);
    Route::put('/clients/{id}/plan', [AuthController::class, 'updatePlan']);
    
    // System stats
    Route::get('/stats/overview', [AuthController::class, 'getSystemStats']);
    Route::get('/stats/clients', [AuthController::class, 'getClientStats']);
    Route::get('/stats/sites', [SiteController::class, 'getSystemStats']);
    Route::get('/stats/analytics', [AnalyticsController::class, 'getSystemStats']);
});
