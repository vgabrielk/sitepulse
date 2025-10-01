<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use App\Services\ReviewService;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

class ExportController extends Controller
{
    public function __construct(
        private AnalyticsService $analyticsService,
        private ReviewService $reviewService
    ) {}

    public function index()
    {
        $user = Auth::user();
        $client = $user->client;
        
        if (!$client) {
            return redirect()->route('login')->with('error', 'Client not found.');
        }

        $sites = $client->sites;

        return view('dashboard.exports.index', compact('sites'));
    }

    public function analytics(Request $request)
    {
        $request->validate([
            'site_id' => 'required|exists:sites,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:csv,json,xlsx'
        ]);

        $site = Site::findOrFail($request->site_id);
        
        $user = Auth::user();
        $client = $user->client;
        
        if (!$client || $site->client_id !== $client->id) {
            abort(403, 'Unauthorized access to this site.');
        }

        try {
            $data = $this->analyticsService->getSiteMetrics(
                $site,
                $request->start_date,
                $request->end_date
            );

            $filename = "analytics_{$site->name}_{$request->start_date}_to_{$request->end_date}";

            return $this->exportData($data, $filename, $request->format);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to export analytics: ' . $e->getMessage());
        }
    }

    public function reviews(Request $request)
    {
        $request->validate([
            'site_id' => 'required|exists:sites,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:csv,json,xlsx'
        ]);

        $site = Site::findOrFail($request->site_id);
        
        $user = Auth::user();
        $client = $user->client;
        
        if (!$client || $site->client_id !== $client->id) {
            abort(403, 'Unauthorized access to this site.');
        }

        try {
            $reviews = $this->reviewService->getReviewsByDateRange(
                $site,
                $request->start_date,
                $request->end_date
            );

            $filename = "reviews_{$site->name}_{$request->start_date}_to_{$request->end_date}";

            return $this->exportData($reviews, $filename, $request->format);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to export reviews: ' . $e->getMessage());
        }
    }

    public function events(Request $request)
    {
        $request->validate([
            'site_id' => 'required|exists:sites,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:csv,json,xlsx'
        ]);

        $site = Site::findOrFail($request->site_id);
        
        $user = Auth::user();
        $client = $user->client;
        
        if (!$client || $site->client_id !== $client->id) {
            abort(403, 'Unauthorized access to this site.');
        }

        try {
            $events = $this->analyticsService->getEventStats(
                $site,
                $request->start_date,
                $request->end_date
            );

            $filename = "events_{$site->name}_{$request->start_date}_to_{$request->end_date}";

            return $this->exportData($events, $filename, $request->format);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to export events: ' . $e->getMessage());
        }
    }

    private function exportData($data, $filename, $format)
    {
        switch ($format) {
            case 'csv':
                return $this->exportCsv($data, $filename);
            case 'json':
                return $this->exportJson($data, $filename);
            case 'xlsx':
                return $this->exportXlsx($data, $filename);
            default:
                throw new \InvalidArgumentException('Unsupported export format');
        }
    }

    private function exportCsv($data, $filename)
    {
        $csv = fopen('php://temp', 'w+');
        
        if (!empty($data)) {
            // Write headers
            fputcsv($csv, array_keys($data[0]));
            
            // Write data
            foreach ($data as $row) {
                fputcsv($csv, $row);
            }
        }
        
        rewind($csv);
        $content = stream_get_contents($csv);
        fclose($csv);
        
        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}.csv\"");
    }

    private function exportJson($data, $filename)
    {
        return response()->json($data)
            ->header('Content-Disposition', "attachment; filename=\"{$filename}.json\"");
    }

    private function exportXlsx($data, $filename)
    {
        // For XLSX export, you would typically use a library like PhpSpreadsheet
        // This is a simplified version that returns CSV for now
        return $this->exportCsv($data, $filename);
    }
}
