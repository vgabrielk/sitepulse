<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminClientController extends Controller
{
    /**
     * Display a listing of clients
     */
    public function index(Request $request)
    {
        $query = Client::select(['id', 'name', 'email', 'plan', 'is_active', 'created_at', 'updated_at'])
            ->withCount(['sites'])
            ->with(['user:id,name,email']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by plan
        if ($request->filled('plan')) {
            $query->where('plan', $request->plan);
        }

        // Filter by status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        $allowedSorts = ['name', 'email', 'plan', 'is_active', 'created_at', 'sites_count'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $clients = $query->paginate(20)->withQueryString();

        // Get statistics for filters
        $stats = [
            'total_clients' => Client::count(),
            'active_clients' => Client::where('is_active', true)->count(),
            'trial_clients' => Client::whereNotNull('trial_ends_at')
                ->where('trial_ends_at', '>', now())->count(),
            'plan_distribution' => Client::select('plan', DB::raw('count(*) as count'))
                ->groupBy('plan')
                ->pluck('count', 'plan')
                ->toArray(),
        ];

        $filters = [
            'search' => $request->search,
            'plan' => $request->plan,
            'is_active' => $request->is_active,
            'sort' => $sortBy,
            'order' => $sortOrder,
        ];

        return view('admin.clients.index', compact('clients', 'stats', 'filters'));
    }

    /**
     * Show the form for creating a new client
     */
    public function create()
    {
        $plans = ['basic', 'premium', 'enterprise'];
        return view('admin.clients.create', compact('plans'));
    }

    /**
     * Store a newly created client
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email',
            'plan' => 'required|in:basic,premium,enterprise',
            'is_active' => 'boolean',
            'trial_ends_at' => 'nullable|date|after:now',
        ]);

        $client = Client::create([
            'name' => $request->name,
            'email' => $request->email,
            'plan' => $request->plan,
            'is_active' => $request->boolean('is_active', true),
            'trial_ends_at' => $request->trial_ends_at,
            'api_key' => $this->generateApiKey(),
        ]);

        return redirect()->route('admin.clients.index')
            ->with('success', 'Cliente criado com sucesso!');
    }

    /**
     * Display the specified client
     */
    public function show(Client $client)
    {
        $client->load(['sites', 'user']);
        
        // Get statistics for the client
        $stats = [
            'total_sites' => $client->sites->count(),
            'active_sites' => $client->sites->where('is_active', true)->count(),
            'total_sessions' => $client->sites->sum(function($site) {
                return $site->sessions()->count();
            }),
            'total_events' => $client->sites->sum(function($site) {
                return DB::table('events')
                    ->join('visits', 'events.visit_id', '=', 'visits.id')
                    ->join('analytics_sessions', 'visits.session_id', '=', 'analytics_sessions.id')
                    ->where('analytics_sessions.site_id', $site->id)
                    ->count();
            }),
            'last_activity' => $client->updated_at,
        ];
        
        // Get usage statistics
        $planLimits = $client->getPlanLimits();
        $usageStats = [
            'limits' => $planLimits,
            'usage' => [
                'sessions' => $stats['total_sessions'],
                'events' => $stats['total_events'],
                'sites' => $stats['total_sites'],
            ],
            'percentage' => [
                'sessions' => $planLimits['monthly_sessions'] === -1 ? 0 : min(100, ($stats['total_sessions'] / $planLimits['monthly_sessions']) * 100),
                'events' => $planLimits['monthly_events'] === -1 ? 0 : min(100, ($stats['total_events'] / $planLimits['monthly_events']) * 100),
                'sites' => $planLimits['sites'] === -1 ? 0 : min(100, ($stats['total_sites'] / $planLimits['sites']) * 100),
            ]
        ];
        
        // Get recent activity (placeholder)
        $recentActivity = collect([
            [
                'type' => 'site_created',
                'message' => 'Novo site adicionado',
                'date' => $client->created_at
            ]
        ]);
        
        return view('admin.clients.show', compact('client', 'stats', 'usageStats', 'recentActivity'));
    }

    /**
     * Show the form for editing the specified client
     */
    public function edit(Client $client)
    {
        $plans = ['basic', 'premium', 'enterprise'];
        return view('admin.clients.edit', compact('client', 'plans'));
    }

    /**
     * Update the specified client
     */
    public function update(Request $request, Client $client)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email,' . $client->id,
            'plan' => 'required|in:basic,premium,enterprise',
            'is_active' => 'boolean',
            'trial_ends_at' => 'nullable|date|after:now',
        ]);

        $client->update([
            'name' => $request->name,
            'email' => $request->email,
            'plan' => $request->plan,
            'is_active' => $request->boolean('is_active'),
            'trial_ends_at' => $request->trial_ends_at,
        ]);

        return redirect()->route('admin.clients.index')
            ->with('success', 'Cliente atualizado com sucesso!');
    }

    /**
     * Remove the specified client
     */
    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()->route('admin.clients.index')
            ->with('success', 'Cliente removido com sucesso!');
    }

    /**
     * Toggle client status
     */
    public function toggleStatus(Client $client)
    {
        $client->update(['is_active' => !$client->is_active]);
        return redirect()->back()
            ->with('success', 'Status do cliente atualizado!');
    }

    /**
     * Update client plan
     */
    public function updatePlan(Request $request, Client $client)
    {
        $request->validate([
            'plan' => 'required|in:basic,premium,enterprise',
        ]);

        $client->update(['plan' => $request->plan]);
        return redirect()->back()
            ->with('success', 'Plano do cliente atualizado!');
    }

    /**
     * Regenerate API key
     */
    public function regenerateApiKey(Client $client)
    {
        $client->update(['api_key' => $this->generateApiKey()]);
        return redirect()->back()
            ->with('success', 'API key regenerada com sucesso!');
    }

    /**
     * Reset client password
     */
    public function resetPassword(Client $client)
    {
        // Generate temporary password
        $tempPassword = Str::random(12);
        
        // Update user password if exists
        $user = $client->user;
        if ($user) {
            $user->update(['password' => Hash::make($tempPassword)]);
        }

        return redirect()->back()
            ->with('success', "Senha temporária gerada: {$tempPassword}");
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'client_ids' => 'required|array|min:1',
        ]);

        $clients = Client::whereIn('id', $request->client_ids);

        switch ($request->action) {
            case 'activate':
                $clients->update(['is_active' => true]);
                $message = 'Clientes ativados com sucesso!';
                break;
            case 'deactivate':
                $clients->update(['is_active' => false]);
                $message = 'Clientes desativados com sucesso!';
                break;
            case 'delete':
                $clients->delete();
                $message = 'Clientes removidos com sucesso!';
                break;
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Export clients
     */
    public function export(Request $request)
    {
        $clients = Client::with(['sites', 'user'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->plan, function ($query, $plan) {
                $query->where('plan', $plan);
            })
            ->when($request->is_active !== null, function ($query, $isActive) {
                $query->where('is_active', $isActive);
            })
            ->get();

        $csvData = [];
        $csvData[] = ['Nome', 'Email', 'Plano', 'Status', 'Sites', 'Data de Criação'];

        foreach ($clients as $client) {
            $csvData[] = [
                $client->name,
                $client->email,
                $client->plan,
                $client->is_active ? 'Ativo' : 'Inativo',
                $client->sites->count(),
                $client->created_at->format('d/m/Y H:i'),
            ];
        }

        $filename = 'clients_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Generate unique API key
     */
    private function generateApiKey(): string
    {
        do {
            $apiKey = 'sp_' . bin2hex(random_bytes(32));
        } while (Client::where('api_key', $apiKey)->exists());

        return $apiKey;
    }
}