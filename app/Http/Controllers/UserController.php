<?php

namespace App\Http\Controllers;

use App\Http\Services\UserConnectionService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class UserController extends Controller
{
    protected UserConnectionService $userConnectionService;

    public function __construct(UserConnectionService $userConnectionService)
    {
        $this->userConnectionService = $userConnectionService;
    }

    /**
     * Get a list of suggestions (users not yet connected to the current user).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $limit = $request->input('limit', 10);
            $result = $this->userConnectionService->getSuggestions(Auth::id(), $limit);
            $suggestions = $result['suggestions'];
            $totalCount = $result['total_count'];

            return response()->json([
                'suggestions' => $suggestions,
                'total_count' => $totalCount,
                'message' => 'Suggestions retrieved successfully'
            ], 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Send a connection request to a user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $userId = $request->input('user_id');
            $this->userConnectionService->sendConnectionRequest($userId, Auth::user());
            return response()->json(['message' => 'Connection request sent successfully'], 200);
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Recipient user not found.'], 404);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get the sent connection requests for the current user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request): JsonResponse
    {
        try {
            $limit = $request->input('limit', 10);
            $results = $this->userConnectionService->getSentRequests(Auth::user(), $limit);
            $sentRequests = $results['sentRequests'];
            $totalCount = $results['total_count'];

            return response()->json(['sent_requests' => $sentRequests, 'total_count' => $totalCount, 'message' => 'Sent connection requests retrieved successfully'], 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Withdraw a connection request sent by the current user.
     *
     * @param int $requestId
     * @return JsonResponse
     */
    public function destroy(int $requestId): JsonResponse
    {
        try {
            $this->userConnectionService->withdrawRequest($requestId, Auth::user());
            return response()->json(['message' => 'Connection request withdrawn successfully'], 200);
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            if ($e->getCode() === 404) {
                return response()->json(['error' => $e->getMessage()], 404);
            } else {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }
    }

    /**
     * Accept a connection request and add the user to the network.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        try {
            $requestId = $request->input('request_id', null);

            if ($requestId === null) {
                throw new InvalidArgumentException('Request ID is required.', 400);
            }

            $this->userConnectionService->acceptRequest((int) $requestId, Auth::user());

            return response()->json(['message' => 'Connection request accepted successfully'], 200);
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Connection request not found.'], 404);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get connections.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getConnections(Request $request): JsonResponse
    {
        try {
            $limit = $request->input('limit', 10);
            $results = $this->userConnectionService->getAllConnections(Auth::user(), $limit);
            $connections = $results['connections'];
            $totalCount = $results['total_count'];
            return response()->json(['connections' => $connections, 'total_count' => $totalCount, 'message' => 'Connections retrieved successfully'], 200);
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove a connection from the network.
     *
     * @param int $connectionId
     * @return JsonResponse
     */
    public function destroyConnection(int $connectionId): JsonResponse
    {
        try {
            $this->userConnectionService->removeConnection($connectionId, Auth::user());
            return response()->json(['message' => 'Connection removed successfully'], 200);
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get a list of connections in common with a user.
     *
     * @param int $userId
     * @param Request $request
     * @return JsonResponse
     */
    public function connectionsInCommon(int $userId, Request $request): JsonResponse
    {
        try {
            $limit = $request->input('limit', 10);
            $results = $this->userConnectionService->getConnectionsInCommon($userId, Auth::user(), $limit);
            $connectionsInCommon = $results['connectionsInCommon'];
            $totalCount = $results['total_count'];
            return response()->json(['connections_in_common' => $connectionsInCommon, 'total_count' => $totalCount, 'message' => 'Connections in common retrieved successfully'], 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get the received connection requests for the current user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function receivedRequests(Request $request): JsonResponse
    {
        try {
            $limit = $request->input('limit', 10);
            $results = $this->userConnectionService->getReceivedRequests(Auth::user(), $limit);
            $receivedRequests = $results['receivedRequests'];
            $totalCount = $results['total_count'];
            return response()->json(['received_requests' => $receivedRequests, 'total_count' => $totalCount, 'message' => 'Received connection requests retrieved successfully'], 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
