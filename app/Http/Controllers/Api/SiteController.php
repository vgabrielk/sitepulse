<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SiteService;
use App\Services\ClientService;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class SiteController extends Controller
{
    public function __construct(
        private SiteService $siteService,
        private ClientService $clientService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $client = $request->user();
        
        try {
            $sites = $this->siteService->getSitesByClient($client);
            
            return response()->json([
                'success' => true,
                'data' => $sites,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get sites',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $client = $request->user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255',
            'anonymize_ips' => 'sometimes|boolean',
            'track_events' => 'sometimes|boolean',
            'collect_feedback' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Check site limit
        if (!$this->siteService->checkSiteLimit($client)) {
            return response()->json([
                'success' => false,
                'message' => 'Site limit reached for your plan',
            ], 403);
        }

        // Validate domain
        if (!$this->siteService->validateDomain($request->domain)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid domain format',
            ], 422);
        }

        try {
            $siteData = $request->only(['name', 'domain', 'anonymize_ips', 'track_events', 'collect_feedback']);
            $site = $this->siteService->createSite($client, $siteData);
            
            return response()->json([
                'success' => true,
                'message' => 'Site created successfully',
                'data' => $site->toArray(),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create site',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $client = $request->user();
        
        try {
            $site = Site::where('id', $id)
                ->where('client_id', $client->id)
                ->first();
            
            if (!$site) {
                return response()->json([
                    'success' => false,
                    'message' => 'Site not found',
                ], 404);
            }
            
            $siteDto = $this->siteService->getSiteById($id);
            
            return response()->json([
                'success' => true,
                'data' => $siteDto->toArray(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get site',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $client = $request->user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'domain' => 'sometimes|string|max:255',
            'anonymize_ips' => 'sometimes|boolean',
            'track_events' => 'sometimes|boolean',
            'collect_feedback' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $site = Site::where('id', $id)
                ->where('client_id', $client->id)
                ->first();
            
            if (!$site) {
                return response()->json([
                    'success' => false,
                    'message' => 'Site not found',
                ], 404);
            }
            
            $updateData = $request->only(['name', 'domain', 'anonymize_ips', 'track_events', 'collect_feedback']);
            
            if (isset($updateData['domain']) && !$this->siteService->validateDomain($updateData['domain'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid domain format',
                ], 422);
            }
            
            $updatedSite = $this->siteService->updateSite($site, $updateData);
            
            return response()->json([
                'success' => true,
                'message' => 'Site updated successfully',
                'data' => $updatedSite->toArray(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update site',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $client = $request->user();
        
        try {
            $site = Site::where('id', $id)
                ->where('client_id', $client->id)
                ->first();
            
            if (!$site) {
                return response()->json([
                    'success' => false,
                    'message' => 'Site not found',
                ], 404);
            }
            
            $this->siteService->deleteSite($site);
            
            return response()->json([
                'success' => true,
                'message' => 'Site deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete site',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getStats(Request $request, int $id): JsonResponse
    {
        $client = $request->user();
        
        try {
            $site = Site::where('id', $id)
                ->where('client_id', $client->id)
                ->first();
            
            if (!$site) {
                return response()->json([
                    'success' => false,
                    'message' => 'Site not found',
                ], 404);
            }
            
            $stats = $this->siteService->getSiteStats($site);
            
            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get site stats',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getMetrics(Request $request, int $id): JsonResponse
    {
        $client = $request->user();
        
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $site = Site::where('id', $id)
                ->where('client_id', $client->id)
                ->first();
            
            if (!$site) {
                return response()->json([
                    'success' => false,
                    'message' => 'Site not found',
                ], 404);
            }
            
            $metrics = $this->siteService->getSiteMetrics(
                $site,
                $request->start_date,
                $request->end_date
            );
            
            return response()->json([
                'success' => true,
                'data' => $metrics,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get site metrics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getWidgetCode(Request $request, int $id): JsonResponse
    {
        $client = $request->user();
        
        try {
            $site = Site::where('id', $id)
                ->where('client_id', $client->id)
                ->first();
            
            if (!$site) {
                return response()->json([
                    'success' => false,
                    'message' => 'Site not found',
                ], 404);
            }
            
            $embedCode = $this->siteService->getWidgetEmbedCode($site);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'embed_code' => $embedCode,
                    'widget_id' => $site->widget_id,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get widget code',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
