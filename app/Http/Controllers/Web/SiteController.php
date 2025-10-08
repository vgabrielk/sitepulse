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
        
        // Get sites with only necessary fields and counts to avoid N+1 queries
        $sites = $client->sites()
            ->select(['id', 'name', 'domain', 'widget_id', 'is_active', 'created_at', 'updated_at'])
            ->withCount(['sessions', 'visits', 'events'])
            ->paginate(12);
        
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
            $siteData = $request->only(['name', 'domain']);
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

    /**
     * Show widget customization page
     */
    public function customize(Site $site)
    {
        $user = Auth::user();
        $client = $user->client;
        
        if (!$client || $site->client_id !== $client->id) {
            abort(403, 'Unauthorized access to this site.');
        }
        
        $customization = $site->widget_customization ?? $site->getDefaultCustomization();
        $themePresets = $this->getThemePresets();
        
        return view('dashboard.sites.customize', compact('site', 'customization', 'themePresets'));
    }

    /**
     * Save widget customization
     */
    public function saveCustomization(Site $site, Request $request)
    {
        $user = Auth::user();
        $client = $user->client;
        
        if (!$client || $site->client_id !== $client->id) {
            abort(403, 'Unauthorized access to this site.');
        }
        
        $request->validate([
            'colors.primary' => 'required|string',
            'colors.secondary' => 'required|string',
            'colors.background' => 'required|string',
            'colors.text' => 'required|string',
            'colors.accent' => 'required|string',
            'typography.font_family' => 'required|string',
            'typography.font_size' => 'required|string',
            'typography.font_weight' => 'required|string',
            'layout.border_radius' => 'required|string',
            'layout.padding' => 'required|string',
            'layout.margin' => 'required|string',
            'layout.max_width' => 'required|string',
            'effects.box_shadow' => 'required|string',
            'effects.hover_shadow' => 'required|string',
            'effects.animation' => 'required|string',
        ]);

        $customization = $request->only([
            'colors', 'typography', 'layout', 'effects'
        ]);

        $site->update(['widget_customization' => $customization]);

        return redirect()->back()->with('success', 'Widget customization saved successfully!');
    }

    /**
     * Get theme presets
     */
    private function getThemePresets(): array
    {
        return [
            'default' => [
                'name' => 'Default',
                'colors' => [
                    'primary' => '#007bff',
                    'secondary' => '#6c757d',
                    'background' => '#ffffff',
                    'text' => '#333333',
                    'accent' => '#f39c12',
                ],
                'typography' => [
                    'font_family' => 'inherit',
                    'font_size' => '14px',
                    'font_weight' => 'normal',
                ],
                'layout' => [
                    'border_radius' => '12px',
                    'padding' => '20px',
                    'margin' => '10px 0',
                    'max_width' => '800px',
                ],
                'effects' => [
                    'box_shadow' => '0 4px 12px rgba(0,0,0,0.1)',
                    'hover_shadow' => '0 6px 20px rgba(0,0,0,0.15)',
                    'animation' => 'fadeIn 0.3s ease',
                ],
            ],
            'dark' => [
                'name' => 'Dark Theme',
                'colors' => [
                    'primary' => '#0d6efd',
                    'secondary' => '#6c757d',
                    'background' => '#212529',
                    'text' => '#ffffff',
                    'accent' => '#ffc107',
                ],
                'typography' => [
                    'font_family' => 'inherit',
                    'font_size' => '14px',
                    'font_weight' => 'normal',
                ],
                'layout' => [
                    'border_radius' => '12px',
                    'padding' => '20px',
                    'margin' => '10px 0',
                    'max_width' => '800px',
                ],
                'effects' => [
                    'box_shadow' => '0 4px 12px rgba(0,0,0,0.3)',
                    'hover_shadow' => '0 6px 20px rgba(0,0,0,0.4)',
                    'animation' => 'fadeIn 0.3s ease',
                ],
            ],
            'minimal' => [
                'name' => 'Minimal',
                'colors' => [
                    'primary' => '#000000',
                    'secondary' => '#666666',
                    'background' => '#ffffff',
                    'text' => '#000000',
                    'accent' => '#000000',
                ],
                'typography' => [
                    'font_family' => 'inherit',
                    'font_size' => '14px',
                    'font_weight' => 'normal',
                ],
                'layout' => [
                    'border_radius' => '0px',
                    'padding' => '16px',
                    'margin' => '10px 0',
                    'max_width' => '600px',
                ],
                'effects' => [
                    'box_shadow' => 'none',
                    'hover_shadow' => '0 2px 8px rgba(0,0,0,0.1)',
                    'animation' => 'none',
                ],
            ],
            'colorful' => [
                'name' => 'Colorful',
                'colors' => [
                    'primary' => '#e91e63',
                    'secondary' => '#9c27b0',
                    'background' => '#f8f9fa',
                    'text' => '#333333',
                    'accent' => '#ff9800',
                ],
                'typography' => [
                    'font_family' => 'inherit',
                    'font_size' => '16px',
                    'font_weight' => '500',
                ],
                'layout' => [
                    'border_radius' => '20px',
                    'padding' => '24px',
                    'margin' => '10px 0',
                    'max_width' => '900px',
                ],
                'effects' => [
                    'box_shadow' => '0 8px 32px rgba(233,30,99,0.2)',
                    'hover_shadow' => '0 12px 40px rgba(233,30,99,0.3)',
                    'animation' => 'fadeIn 0.5s ease',
                ],
            ],
        ];
    }
}
