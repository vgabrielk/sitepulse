<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $client = $user->client;
        
        if (!$client) {
            return redirect()->route('login')->with('error', 'Client not found.');
        }

        $planLimits = $client->getPlanLimits();
        $currentUsage = $this->getCurrentUsage($client);

        return view('dashboard.billing.index', compact('client', 'planLimits', 'currentUsage'));
    }

    public function upgrade(Request $request)
    {
        $user = Auth::user();
        $client = $user->client;
        
        if (!$client) {
            return redirect()->route('login')->with('error', 'Client not found.');
        }

        $request->validate([
            'plan' => 'required|in:free,basic,premium,enterprise'
        ]);

        try {
            // In a real application, you would integrate with a payment processor
            // For now, we'll just update the plan
            $client->plan = $request->plan;
            $client->plan_limits = null; // Reset to use default limits for the new plan
            $client->save();
            
            return redirect()->route('billing')->with('success', 'Plan upgraded successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to upgrade plan: ' . $e->getMessage());
        }
    }

    private function getCurrentUsage($client)
    {
        // This would typically query the database for actual usage
        // For now, return mock data
        return [
            'monthly_visits' => 0,
            'monthly_events' => 0,
            'sites' => $client->sites()->count(),
            'reviews' => 0,
            'exports' => 0,
        ];
    }
}