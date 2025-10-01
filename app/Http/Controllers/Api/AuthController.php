<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ClientService;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct(
        private ClientService $clientService
    ) {}

    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:clients',
            'plan' => 'sometimes|string|in:free,basic,premium,enterprise',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $clientData = $request->only(['name', 'email', 'plan']);
            $clientData['plan'] = $clientData['plan'] ?? 'free';
            
            $client = $this->clientService->createClient($clientData);
            
            return response()->json([
                'success' => true,
                'message' => 'Client created successfully',
                'data' => $client->toArray(),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create client',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'api_key' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $client = $this->clientService->getClientByApiKey($request->api_key);
            
            if (!$client) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid API key',
                ], 401);
            }

            if (!$client->isActive) {
                return response()->json([
                    'success' => false,
                    'message' => 'Client account is inactive',
                ], 403);
            }

            return response()->json([
                'success' => true,
                'message' => 'Authentication successful',
                'data' => $client->toArray(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function me(Request $request): JsonResponse
    {
        $client = $request->user();
        
        return response()->json([
            'success' => true,
            'data' => $client->toArray(),
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $client = $request->user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:clients,email,' . $client->id,
            'webhook_url' => 'sometimes|nullable|url',
            'webhook_secret' => 'sometimes|nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $updateData = $request->only(['name', 'email', 'webhook_url', 'webhook_secret']);
            $updatedClient = $this->clientService->updateClient($client, $updateData);
            
            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => $updatedClient->toArray(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function regenerateApiKey(Request $request): JsonResponse
    {
        $client = $request->user();
        
        try {
            $newApiKey = $this->clientService->generateApiKey();
            $this->clientService->updateClient($client, ['api_key' => $newApiKey]);
            
            return response()->json([
                'success' => true,
                'message' => 'API key regenerated successfully',
                'data' => [
                    'api_key' => $newApiKey,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to regenerate API key',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getStats(Request $request): JsonResponse
    {
        $client = $request->user();
        
        try {
            $stats = $this->clientService->getClientStats($client);
            
            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get client stats',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
