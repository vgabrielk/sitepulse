<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\SiteController;
use App\Http\Controllers\Web\ReviewController;
use App\Http\Controllers\Web\ExportController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\SettingsController;
use App\Http\Controllers\Web\BillingController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\FaqController;
use App\Http\Controllers\Web\AdminController;
use App\Http\Controllers\Web\AdminClientController;
use App\Http\Controllers\Auth\VerificationController;
use Illuminate\Support\Facades\Auth;

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

// Email verification routes
Route::get('/email/verify', [VerificationController::class, 'show'])->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
Route::post('/email/verification-notification', [VerificationController::class, 'resend'])->name('verification.send');

// Password reset routes
Route::get('/password/reset', [AuthController::class, 'showPasswordReset'])->name('password.request');
Route::post('/password/email', [AuthController::class, 'passwordReset'])->name('password.email');
Route::get('/password/reset/{token}', [AuthController::class, 'showPasswordResetForm'])->name('password.reset');
Route::post('/password/reset', [AuthController::class, 'passwordResetUpdate'])->name('password.update');

// Protected routes
Route::middleware(['auth:web', 'email.verified'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Sites
    Route::resource('sites', SiteController::class);
    Route::post('/sites/{site}/toggle-status', [SiteController::class, 'toggleStatus'])->name('sites.toggle-status');
    Route::get('/sites/{site}/customize', [SiteController::class, 'customize'])->name('sites.customize');
    Route::post('/sites/{site}/customize', [SiteController::class, 'saveCustomization'])->name('sites.save-customization');

    // FAQ Inteligente (site-scoped)
    Route::get('/sites/{site}/faq', [FaqController::class, 'index'])->name('sites.faq.index');
    Route::post('/sites/{site}/faq/customize', [FaqController::class, 'saveCustomization'])->name('sites.faq.customize');
    Route::post('/sites/{site}/faq', [FaqController::class, 'store'])->name('sites.faq.store');
    Route::put('/sites/{site}/faq/{faq}', [FaqController::class, 'update'])->name('sites.faq.update');
    Route::delete('/sites/{site}/faq/{faq}', [FaqController::class, 'destroy'])->name('sites.faq.destroy');
    
    
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
    
    // Exports (widgets & reviews only)
    Route::prefix('exports')->name('exports.')->group(function () {
        Route::get('/', [ExportController::class, 'index'])->name('index');
        Route::post('/reviews', [ExportController::class, 'reviews'])->name('reviews');
        Route::post('/events', [ExportController::class, 'events'])->name('events');
    });

    // Widgets gallery
    Route::get('/widgets', function(){
        $user = Auth::user();
        $sites = $user && $user->client ? $user->client->sites()->latest()->get() : collect();
        return view('dashboard.widgets.gallery', compact('sites'));
    })->name('widgets.gallery');
    
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

// Demo routes for widgets
    Route::get('/widget/demo/faq', function () {
    $faqs = [
        ['q' => 'Como funciona o período de teste?', 'a' => 'Você pode usar por 14 dias gratuitamente.'],
        ['q' => 'Posso cancelar a qualquer momento?', 'a' => 'Sim, sem multas ou taxas.'],
        ['q' => 'Quais métodos de pagamento?', 'a' => 'Cartão de crédito e boleto (via parceiro).'],
    ];
    return view('widget.demo.faq', compact('faqs'));
});

Route::get('/widget/demo/before-after', function () {
    $before = 'https://via.placeholder.com/640x360?text=Antes';
    $after = 'https://via.placeholder.com/640x360?text=Depois';
    return view('widget.demo.before-after', compact('before', 'after'));
});

Route::get('/widget/demo/reviews', function () {
    return view('widget.demo.reviews');
});

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

// Public FAQs JSON for widget
Route::get('/widget/{widgetId}/faqs', function ($widgetId) {
    $site = \App\Models\Site::where('widget_id', $widgetId)->first();
    if (!$site || !$site->is_active) {
        return response()->json([]);
    }
    $faqs = \App\Models\Faq::where('site_id', $site->id)
        ->where('published', true)
        ->orderBy('position')
        ->get(['question','answer','position','published']);
    return response()->json($faqs);
})->name('widget.faqs');

// Public FAQs JSON by site_id (alternate embed support)
Route::get('/widget/faqs', function (\Illuminate\Http\Request $request) {
    $siteId = (int) $request->query('site_id');
    if (!$siteId) {
        return response()->json([]);
    }
    $site = \App\Models\Site::find($siteId);
    if (!$site || !$site->is_active) {
        return response()->json([]);
    }
    $faqs = \App\Models\Faq::where('site_id', $site->id)
        ->where('published', true)
        ->orderBy('position')
        ->get(['question','answer','position','published']);
    return response()->json($faqs);
})->name('widget.faqs.by_site');

// Public FAQ embed (iframe-safe)
Route::get('/widget/{widgetId}/faq', function ($widgetId) {
    $site = \App\Models\Site::where('widget_id', $widgetId)->first();
    if (!$site || !$site->is_active) {
        // Render an iframe-safe minimal view instead of plain text
        return response()->view('widget.faq-unavailable', [], 404);
    }
    $faqs = \App\Models\Faq::where('site_id', $site->id)
        ->where('published', true)
        ->orderBy('position')
        ->get();
    return view('widget.faq', compact('site','faqs'));
})->name('widget.faq');

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

    $validator = \Validator::make($request->all(), [
        'visitor_name' => 'required|string|max:255',
        'visitor_email' => 'required|email|max:255',
        'rating' => 'required|numeric|min:1|max:5',
        'comment' => 'nullable|string|max:1000',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Por favor, corrija os erros abaixo:',
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        $data = $request->only(['visitor_name', 'visitor_email', 'rating', 'comment']);
        $data['ip_address'] = $request->ip();
        $data['submitted_at'] = now();
        $data['status'] = 'approved'; // Auto-approve reviews
        $data['approved_at'] = now(); // Set approval timestamp
        
        $review = \App\Models\Review::create([
            'site_id' => $site->id,
            'visitor_name' => $data['visitor_name'],
            'visitor_email' => $data['visitor_email'],
            'rating' => $data['rating'],
            'comment' => $data['comment'],
            'ip_address' => $data['ip_address'],
            'submitted_at' => $data['submitted_at'],
            'status' => $data['status'],
            'approved_at' => $data['approved_at'],
        ]);
        
        \Log::info('Review created successfully', ['review_id' => $review->id, 'site_id' => $site->id]);
        
        // Return JSON response for iframe
        return response()->json([
            'success' => true,
            'message' => 'Review submitted successfully! It will be reviewed before being published.',
            'review_id' => $review->id
        ]);
    } catch (\Exception $e) {
        \Log::error('Failed to create review', ['error' => $e->getMessage(), 'site_id' => $site->id]);
        return response()->json([
            'success' => false,
            'message' => 'Failed to submit review: ' . $e->getMessage()
        ], 500);
    }
})->name('widget.submit-review.post');

// Admin routes
Route::middleware(['auth:web', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Admin dashboard
    Route::middleware(['permission:admin.dashboard'])->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    });

    // Client management routes with specific permissions
    Route::middleware(['permission:clients.view'])->group(function () {
        Route::get('/clients', [AdminClientController::class, 'index'])->name('clients.index');
        Route::get('/clients/{client}', [AdminClientController::class, 'show'])->name('clients.show');
    });

    // Sites (admin) - placeholder redirect until admin listing is implemented
    Route::middleware(['permission:sites.view_all'])->group(function () {
        Route::get('/sites', function () {
            return redirect()->route('admin.dashboard');
        })->name('sites.index');
    });
    
    Route::middleware(['permission:clients.create'])->group(function () {
        Route::get('/clients/create', [AdminClientController::class, 'create'])->name('clients.create');
        Route::post('/clients', [AdminClientController::class, 'store'])->name('clients.store');
    });
    
    Route::middleware(['permission:clients.edit'])->group(function () {
        Route::get('/clients/{client}/edit', [AdminClientController::class, 'edit'])->name('clients.edit');
        Route::put('/clients/{client}', [AdminClientController::class, 'update'])->name('clients.update');
        Route::post('/clients/{client}/toggle-status', [AdminClientController::class, 'toggleStatus'])->name('clients.toggle-status');
        Route::post('/clients/{client}/update-plan', [AdminClientController::class, 'updatePlan'])->name('clients.update-plan');
        Route::post('/clients/{client}/regenerate-api-key', [AdminClientController::class, 'regenerateApiKey'])->name('clients.regenerate-api-key');
        Route::post('/clients/{client}/reset-password', [AdminClientController::class, 'resetPassword'])->name('clients.reset-password');
    });
    
    Route::middleware(['permission:clients.delete'])->group(function () {
        Route::delete('/clients/{client}', [AdminClientController::class, 'destroy'])->name('clients.destroy');
    });
    
    Route::middleware(['permission:clients.manage'])->group(function () {
        // Bulk actions
        Route::post('/clients/bulk-action', [AdminClientController::class, 'bulkAction'])->name('clients.bulk-action');
        Route::get('/clients/export', [AdminClientController::class, 'export'])->name('clients.export');
    });

    // Reviews (admin) - placeholder redirect until admin moderation is implemented
    Route::middleware(['permission:reviews.moderate'])->group(function () {
        Route::get('/reviews', function () {
            return redirect()->route('admin.dashboard');
        })->name('reviews.index');
    });

    // System settings - placeholder redirect until settings page is implemented
    Route::middleware(['permission:system.settings'])->group(function () {
        Route::get('/settings', function () {
            return redirect()->route('admin.dashboard');
        })->name('settings');
    });
});