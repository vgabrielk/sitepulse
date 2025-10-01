<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\ClientService;
use App\Services\SiteService;
use App\Services\AnalyticsService;
use App\Services\ReviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(
        private ClientService $clientService,
        private SiteService $siteService,
        private AnalyticsService $analyticsService,
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
        
        // Get recent sites (using models directly)
        $recentSites = $client->sites()->latest()->take(5)->get();
        
        // Get recent reviews
        $recentReviews = [];
        foreach ($recentSites as $site) {
            $siteReviews = $this->reviewService->getRecentReviews($site, 3);
            $recentReviews = array_merge($recentReviews, $siteReviews);
        }
        $recentReviews = array_slice($recentReviews, 0, 5);
        
        // Get chart data for last 30 days
        $chartData = $this->getChartData($client);
        
        return view('dashboard.index', compact('stats', 'recentSites', 'recentReviews', 'chartData'));
    }
    
    private function getChartData($client)
    {
        $labels = [];
        $sessions = [];
        $visits = [];
        
        // Generate last 30 days data
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('M j');
            
            // Get aggregated data for this date
            $sessionsCount = 0;
            $visitsCount = 0;
            
            foreach ($client->sites as $site) {
                $siteMetrics = $this->analyticsService->getSiteMetrics(
                    $site,
                    $date->format('Y-m-d'),
                    $date->format('Y-m-d')
                );
                
                $sessionsCount += $siteMetrics['sessions'] ?? 0;
                $visitsCount += $siteMetrics['visits'] ?? 0;
            }
            
            $sessions[] = $sessionsCount;
            $visits[] = $visitsCount;
        }
        
        return [
            'labels' => $labels,
            'sessions' => $sessions,
            'visits' => $visits,
        ];
    }
}
