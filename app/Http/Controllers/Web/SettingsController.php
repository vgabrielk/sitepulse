<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $client = $user->client;
        
        if (!$client) {
            return redirect()->route('login')->with('error', 'Client not found.');
        }

        return view('dashboard.settings.index', compact('user', 'client'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $client = $user->client;
        
        if (!$client) {
            return redirect()->route('login')->with('error', 'Client not found.');
        }

        $request->validate([
            'webhook_url' => 'nullable|url|max:255',
            'webhook_secret' => 'nullable|string|max:255',
            'settings' => 'nullable|array',
            'settings.email_notifications' => 'boolean',
            'settings.weekly_reports' => 'boolean',
            'settings.monthly_reports' => 'boolean',
            'settings.data_retention_days' => 'nullable|integer|min:30|max:365',
        ]);

        try {
            // Update webhook settings
            if ($request->filled('webhook_url')) {
                $client->webhook_url = $request->webhook_url;
            }
            
            if ($request->filled('webhook_secret')) {
                $client->webhook_secret = $request->webhook_secret;
            }
            
            // Update client settings
            $currentSettings = $client->settings ?? [];
            $newSettings = array_merge($currentSettings, $request->settings ?? []);
            $client->settings = $newSettings;
            
            $client->save();
            
            return redirect()->route('settings')->with('success', 'Settings updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update settings: ' . $e->getMessage());
        }
    }
}
