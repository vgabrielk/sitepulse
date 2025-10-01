<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\SiteService;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiteController extends Controller
{
    public function __construct(
        private SiteService $siteService
    ) {}

    public function index()
    {
        $user = Auth::user();
        
        \Log::info('SiteController@index - User: ' . $user->email);
        \Log::info('SiteController@index - User ID: ' . $user->id);
        
        // Debug the relationship
        $client = $user->client;
        \Log::info('SiteController@index - Client: ' . ($client ? $client->email : 'null'));
        \Log::info('SiteController@index - Client ID: ' . ($client ? $client->id : 'null'));
        
        if (!$client) {
            \Log::warning('SiteController@index - No client found for user: ' . $user->email);
            \Log::warning('SiteController@index - User email: ' . $user->email);
            
            // Try to find client manually
            $manualClient = \App\Models\Client::where('email', $user->email)->first();
            \Log::info('SiteController@index - Manual client lookup: ' . ($manualClient ? $manualClient->email : 'null'));
            
            return redirect()->route('login')->with('error', 'Client not found.');
        }
        
        // Get sites as models instead of DTOs
        $sites = $client->sites()->get();
        
        \Log::info('SiteController@index - Sites count: ' . $sites->count());
        
        return view('dashboard.sites.index', compact('sites'));
    }

    public function create()
    {
        return view('dashboard.sites.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255',
            'anonymize_ips' => 'boolean',
            'track_events' => 'boolean',
            'collect_feedback' => 'boolean',
        ]);

        $user = Auth::user();
        $client = $user->client;
        
        if (!$client) {
            return redirect()->route('login')->with('error', 'Client not found.');
        }
        
        // Check site limit
        if (!$this->siteService->checkSiteLimit($client)) {
            return redirect()->back()
                ->with('error', 'Site limit reached for your plan. Please upgrade to add more sites.');
        }

        // Validate domain
        if (!$this->siteService->validateDomain($request->domain)) {
            return redirect()->back()
                ->with('error', 'Invalid domain format.');
        }

        try {
            $siteData = $request->only(['name', 'domain', 'anonymize_ips', 'track_events', 'collect_feedback']);
            $site = $this->siteService->createSite($client, $siteData);
            
            return redirect()->route('sites.show', $site->id)
                ->with('success', 'Site created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create site: ' . $e->getMessage());
        }
    }

    public function show(Site $site)
    {
        $user = Auth::user();
        $client = $user->client;
        
        if (!$client || $site->client_id !== $client->id) {
            abort(403, 'Unauthorized access to this site.');
        }
        
        $stats = $this->siteService->getSiteStats($site);
        $embedCode = $this->siteService->getWidgetEmbedCode($site);
        $reviewEmbedCode = $this->siteService->getReviewEmbedCode($site);
        
        return view('dashboard.sites.show', compact('site', 'stats', 'embedCode', 'reviewEmbedCode'));
    }

    public function edit(Site $site)
    {
        $user = Auth::user();
        $client = $user->client;
        
        if (!$client || $site->client_id !== $client->id) {
            abort(403, 'Unauthorized access to this site.');
        }
        
        return view('dashboard.sites.edit', compact('site'));
    }

    public function update(Request $request, Site $site)
    {
        $user = Auth::user();
        $client = $user->client;
        
        if (!$client || $site->client_id !== $client->id) {
            abort(403, 'Unauthorized access to this site.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255',
            'anonymize_ips' => 'boolean',
            'track_events' => 'boolean',
            'collect_feedback' => 'boolean',
        ]);

        if (!$this->siteService->validateDomain($request->domain)) {
            return redirect()->back()
                ->with('error', 'Invalid domain format.');
        }

        try {
            $updateData = $request->only(['name', 'domain', 'anonymize_ips', 'track_events', 'collect_feedback']);
            $this->siteService->updateSite($site, $updateData);
            
            return redirect()->route('sites.show', $site)
                ->with('success', 'Site updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update site: ' . $e->getMessage());
        }
    }

    public function destroy(Site $site)
    {
        $user = Auth::user();
        $client = $user->client;
        
        if (!$client || $site->client_id !== $client->id) {
            abort(403, 'Unauthorized access to this site.');
        }
        
        try {
            $this->siteService->deleteSite($site);
            
            return redirect()->route('sites.index')
                ->with('success', 'Site deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete site: ' . $e->getMessage());
        }
    }

    public function toggleStatus(Site $site)
    {
        $user = Auth::user();
        $client = $user->client;
        
        if (!$client || $site->client_id !== $client->id) {
            abort(403, 'Unauthorized access to this site.');
        }
        
        try {
            if ($site->is_active) {
                $this->siteService->deactivateSite($site);
                $message = 'Site deactivated successfully!';
            } else {
                $this->siteService->activateSite($site);
                $message = 'Site activated successfully!';
            }
            
            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update site status: ' . $e->getMessage());
        }
    }
}
