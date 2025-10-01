<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use App\Services\SiteService;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnalyticsController extends Controller
{
    public function __construct(
        private AnalyticsService $analyticsService,
        private SiteService $siteService
    ) {}

    public function overview()
    {
        $user = Auth::user();
        $client = $user->client;
        
        if (!$client) {
            return redirect()->route('login')->with('error', 'Client not found.');
        }

        $sites = $client->sites;
        $overviewData = [];

        foreach ($sites as $site) {
            $stats = $this->analyticsService->getSiteMetrics(
                $site,
                now()->subDays(30)->format('Y-m-d'),
                now()->format('Y-m-d')
            );
            
            $overviewData[] = [
                'site' => $site,
                'stats' => $stats
            ];
        }

        return view('dashboard.analytics.overview', compact('overviewData'));
    }

    public function site(Site $site)
    {
        $user = Auth::user();
        $client = $user->client;
        
        if (!$client || $site->client_id !== $client->id) {
            abort(403, 'Unauthorized access to this site.');
        }
        
        $stats = $this->analyticsService->getSiteMetrics(
            $site,
            now()->subDays(30)->format('Y-m-d'),
            now()->addDay()->format('Y-m-d')
        );

        $realTimeMetrics = $this->analyticsService->getRealTimeMetrics($site);

        return view('dashboard.analytics.site', compact('site', 'stats', 'realTimeMetrics'));
    }

    public function sessions(Site $site)
    {
        $user = Auth::user();
        $client = $user->client;
        
        if (!$client || $site->client_id !== $client->id) {
            abort(403, 'Unauthorized access to this site.');
        }
        
        $sessionStats = $this->analyticsService->getSessionStats(
            $site,
            now()->subDays(30)->format('Y-m-d'),
            now()->format('Y-m-d')
        );

        return view('dashboard.analytics.sessions', compact('site', 'sessionStats'));
    }

    public function events(Site $site)
    {
        $user = Auth::user();
        $client = $user->client;
        
        if (!$client || $site->client_id !== $client->id) {
            abort(403, 'Unauthorized access to this site.');
        }
        
        $eventStats = $this->analyticsService->getEventStats(
            $site,
            now()->subDays(30)->format('Y-m-d'),
            now()->format('Y-m-d')
        );

        $topElements = $this->analyticsService->getTopClickedElements($site, now()->subDays(30)->format('Y-m-d'), now()->format('Y-m-d'));

        return view('dashboard.analytics.events', compact('site', 'eventStats', 'topElements'));
    }

    public function pages(Site $site)
    {
        $user = Auth::user();
        $client = $user->client;
        
        if (!$client || $site->client_id !== $client->id) {
            abort(403, 'Unauthorized access to this site.');
        }
        
        $pageStats = $this->analyticsService->getEventStats(
            $site,
            now()->subDays(30)->format('Y-m-d'),
            now()->format('Y-m-d')
        );

        return view('dashboard.analytics.pages', compact('site', 'pageStats'));
    }

    public function heatmap(Site $site)
    {
        $user = Auth::user();
        $client = $user->client;
        
        if (!$client || $site->client_id !== $client->id) {
            abort(403, 'Unauthorized access to this site.');
        }
        
        $heatmapData = $this->analyticsService->getHeatmapData(
            $site,
            now()->subDays(30)->format('Y-m-d'),
            now()->format('Y-m-d')
        );

        return view('dashboard.analytics.heatmap', compact('site', 'heatmapData'));
    }
}
