<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\ClientService;
use App\Services\SiteService;
use App\Models\Client;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AdminController extends Controller
{
    public function __construct(
        private ClientService $clientService,
        private SiteService $siteService,
        
    ) {}

    public function dashboard()
    {
        // Get system stats
        $stats = $this->getSystemStats();
        
        // Get recent clients
        $recentClients = Client::orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(fn($client) => [
                'id' => $client->id,
                'name' => $client->name,
                'email' => $client->email,
                'plan' => $client->plan,
                'is_active' => $client->is_active,
                'created_at' => $client->created_at,
            ])
            ->toArray();
        
        // Get top sites by traffic
        $topSites = Site::with('client')
            ->withCount(['sessions', 'visits', 'events'])
            ->orderBy('sessions_count', 'desc')
            ->limit(10)
            ->get()
            ->map(fn($site) => [
                'id' => $site->id,
                'name' => $site->name,
                'domain' => $site->domain,
                'client_name' => $site->client->name,
                'sessions_count' => $site->sessions_count,
                'visits_count' => $site->visits_count,
                'events_count' => $site->events_count,
                'is_active' => $site->is_active,
            ])
            ->toArray();
        
        // Get system status
        $systemStatus = $this->getSystemStatus();
        
        // Get chart data
        $chartData = $this->getChartData();
        $planData = $this->getPlanData();
        
        return view('admin.dashboard', compact(
            'stats',
            'recentClients',
            'topSites',
            'systemStatus',
            'chartData',
            'planData'
        ));
    }
    
    private function getSystemStats(): array
    {
        return Cache::remember('admin_system_stats', 300, function () {
            return [
                'total_clients' => Client::count(),
                'total_sites' => Site::count(),
                'total_sessions' => DB::table('sessions')->count(),
                'total_visits' => DB::table('visits')->count(),
                'total_events' => DB::table('events')->count(),
                'total_reviews' => DB::table('reviews')->count(),
            ];
        });
    }
    
    private function getSystemStatus(): array
    {
        return [
            'database' => $this->checkDatabase(),
            'redis' => $this->checkRedis(),
            'queue' => $this->checkQueue(),
            'storage' => $this->checkStorage(),
            'mail' => $this->checkMail(),
            'api' => $this->checkApi(),
        ];
    }
    
    private function checkDatabase(): bool
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    private function checkRedis(): bool
    {
        try {
            Cache::store('redis')->put('test', 'test', 1);
            return Cache::store('redis')->get('test') === 'test';
        } catch (\Exception $e) {
            return false;
        }
    }
    
    private function checkQueue(): bool
    {
        try {
            // Check if queue workers are running
            return true; // Simplified check
        } catch (\Exception $e) {
            return false;
        }
    }
    
    private function checkStorage(): bool
    {
        try {
            return is_writable(storage_path());
        } catch (\Exception $e) {
            return false;
        }
    }
    
    private function checkMail(): bool
    {
        try {
            // Check mail configuration
            return !empty(config('mail.mailers.smtp.host'));
        } catch (\Exception $e) {
            return false;
        }
    }
    
    private function checkApi(): bool
    {
        try {
            // Check if API routes are accessible
            return true; // Simplified check
        } catch (\Exception $e) {
            return false;
        }
    }
    
    private function getChartData(): array
    {
        $labels = [];
        $sessions = [];
        $events = [];
        
        // Generate last 30 days data
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('M j');
            
            $sessionsCount = DB::table('sessions')
                ->whereDate('started_at', $date->format('Y-m-d'))
                ->count();
            
            $eventsCount = DB::table('events')
                ->whereDate('occurred_at', $date->format('Y-m-d'))
                ->count();
            
            $sessions[] = $sessionsCount;
            $events[] = $eventsCount;
        }
        
        return [
            'labels' => $labels,
            'sessions' => $sessions,
            'events' => $events,
        ];
    }
    
    private function getPlanData(): array
    {
        $planStats = Client::select('plan', DB::raw('count(*) as count'))
            ->groupBy('plan')
            ->get();
        
        $labels = [];
        $data = [];
        
        foreach ($planStats as $stat) {
            $labels[] = ucfirst($stat->plan);
            $data[] = $stat->count;
        }
        
        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }
}
