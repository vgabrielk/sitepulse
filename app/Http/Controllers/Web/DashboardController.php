<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\ClientService;
use App\Services\SiteService;
use App\Services\ReviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(
        private ClientService $clientService,
        private SiteService $siteService,
        private ReviewService $reviewService
    ) {}

    public function index(Request $request)
    {
        $user = Auth::user();
        \Log::info('Dashboard access', ['user_id' => $user ? $user->id : 'null', 'authenticated' => Auth::check()]);
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Usuário não autenticado.');
        }
        
        $client = $user->client;
        \Log::info('Client lookup', ['client' => $client ? $client->id : 'null']);
        
        if (!$client) {
            return redirect()->route('login')->with('error', 'Cliente não encontrado.');
        }
        
        // Get client stats
        $stats = $this->clientService->getClientStats($client);
        
        // Get recent sites
        $recentSites = $client->sites()->latest()->take(5)->get();
        
        // Get recent reviews
        $recentReviews = [];
        foreach ($recentSites as $site) {
            $siteReviews = $this->reviewService->getRecentReviews($site, 3);
            // Convert ReviewDTO objects to arrays
            $siteReviewsArray = array_map(fn($review) => $review->toArray(), $siteReviews);
            $recentReviews = array_merge($recentReviews, $siteReviewsArray);
        }
        $recentReviews = array_slice($recentReviews, 0, 5);
        
        return view('dashboard.index', compact('stats', 'recentSites', 'recentReviews'));
    }
    
    
}
