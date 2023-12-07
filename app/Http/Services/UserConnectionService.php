<?php

namespace App\Http\Services;

use App\Models\Request;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserConnectionService
{
    /**
     * Get a list of suggestions (users not yet connected to the current user).
     *
     * @param $currentUserId
     * @param $limit
     * @return array
     * @throws Exception if unable to retrieve suggestions
     */
    public function getSuggestions($currentUserId, $limit): array
    {
        try {

            // Getting all users except the current user and their existing connections
            $suggestionsQuery = User::where('id', '!=', Auth::id())
                ->whereDoesntHave('connections', function ($query) {
                    $query->where('connected_user_id', Auth::id());
                });

            $totalCount = $suggestionsQuery->count();

            $suggestions = $suggestionsQuery->limit($limit)->get();

            return [
                'suggestions' => $suggestions,
                'total_count' => $totalCount
            ];
        } catch (Exception $e) {
            throw new Exception('Failed to retrieve suggestions: ' . $e->getMessage());
        }
    }

    /**
     * Send a connection request to a user.
     *
     * @param int $userId
     * @param $currentUser
     * @return void
     * @throws Exception if unable to send connection request
     */
    public function sendConnectionRequest(int $userId, $currentUser): void
    {
        try {
            // Find the recipient user by ID
            $recipientUser = User::findOrFail($userId);

            // Check if the connection request already exists
            $existingRequest = Request::where('sender_id', $currentUser->id)
                ->where('receiver_id', $recipientUser->id)
                ->first();

            if ($existingRequest) {
                throw new Exception('Connection request already sent.', 409);
            }

            // Creating a new connection request record in the database
            Request::create([
                'sender_id' => $currentUser->id,
                'receiver_id' => $recipientUser->id,
            ]);
        } catch (ModelNotFoundException $e) {
            throw new Exception('Recipient user not found.');
        } catch (Exception $e) {
            throw new Exception('Failed to send connection request: ' . $e->getMessage());
        }
    }

    /**
     * Get the sent connection requests for the current user.
     *
     * @param $currentUser
     * @param int $limit
     * @return array
     * @throws Exception if unable to retrieve sent connection requests
     */
    public function getSentRequests($currentUser, int $limit): array
    {
        try {
            // Getting the sent requests for the current user
            $query = $currentUser->sentRequests()->with('receiver');

            $totalCount = $query->count();
            $sentRequests = $query->limit($limit)->get();

            return [
                'sentRequests' => $sentRequests,
                'total_count' => $totalCount
            ];
        } catch (Exception $e) {
            throw new Exception('Failed to retrieve sent connection requests: ' . $e->getMessage());
        }
    }

    /**
     * Withdraw a connection request sent by the current user.
     *
     * @param int $requestId
     * @param $currentUser
     * @return void
     * @throws Exception if unable to withdraw connection request
     */
    public function withdrawRequest(int $requestId, $currentUser): void
    {
        try {
            // Removing the connection request record from the database
            $deleted = Request::where('id', $requestId)
                ->where('sender_id', $currentUser->id)
                ->delete();

            if (!$deleted) {
                throw new Exception('Connection request not found or does not belong to the current user.', 404);
            }
        } catch (Exception $e) {
            throw new Exception('Failed to withdraw connection request: ' . $e->getMessage());
        }
    }

    /**
     * Accept a connection request and add the user to the network.
     *
     * @param int $requestId
     * @param $currentUser
     * @return void
     * @throws Exception if unable to accept connection request
     */
    public function acceptRequest(int $requestId, $currentUser): void
    {
        DB::transaction(function () use ($requestId, $currentUser) {
            try {
                // Retrieve the received request within the transaction
                $receivedRequest = $currentUser->receivedRequests()->findOrFail($requestId);

                // Ensure that the request belongs to the current user
                if ($receivedRequest->receiver_id !== $currentUser->id) {
                    throw new Exception('Connection request does not belong to the current user.', 404);
                }

                // Add the connection and delete the request within the same transaction
                $currentUser->connections()->attach($receivedRequest->sender_id, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $receivedRequest->delete();
            } catch (ModelNotFoundException $e) {
                throw new Exception('Connection request not found.', 404);
            } catch (Exception $e) {
                throw new Exception('Failed to accept connection request: ' . $e->getMessage());
            }
        });
    }

    /**
     * Get all connections.
     *
     * @param $currentUser
     * @param int $limit
     * @return array
     * @throws Exception if unable to remove connection
     */
    public function getAllConnections($currentUser, int $limit): array
    {
        try {
            //Getting all connections
            $query = $currentUser->connections()
                ->with('commonConnections');

            $totalCount = $query->count();
            $connections = $query->limit($limit)->get();

            return [
                'connections' => $connections,
                'total_count' => $totalCount
            ];
        } catch (Exception $e) {
            throw new Exception('Failed to retrieve connections: ' . $e->getMessage());
        }
    }

    /**
     * Remove a connection from the network.
     *
     * @param int $connectionId
     * @param $currentUser
     * @return void
     * @throws Exception if unable to remove connection
     */
    public function removeConnection(int $connectionId, $currentUser): void
    {
        try {
            // Removing the connection record from the database
            $currentUser->connections()->detach($connectionId);
        } catch (Exception $e) {
            throw new Exception('Failed to remove connection: ' . $e->getMessage());
        }
    }

    /**
     * Get a list of connections in common with a user.
     *
     * @param int $userId
     * @param $currentUser
     * @param int $limit
     * @return array
     * @throws Exception if unable to retrieve connections in common
     */
    public function getConnectionsInCommon(int $userId, $currentUser, int $limit): array
    {
        try {

            // Getting the connections in common with the specified user
            $query = $currentUser->connections()
                ->with('commonConnections')
                ->whereHas('commonConnections', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                });

            $totalCount = $query->count();
            $connectionsInCommon = $query->limit($limit)->get();

            return [
                'connectionsInCommon' => $connectionsInCommon,
                'total_count' => $totalCount
            ];
        } catch (Exception $e) {
            throw new Exception('Failed to retrieve connections in common: ' . $e->getMessage());
        }
    }

    /**
     * *
     * @param $currentUser
     * @param int $limit
     * @return array
     * @throws Exception
     */
    public function getReceivedRequests($currentUser, int $limit): array
    {
        try {
            // Getting the received request for the current user
            $query = $currentUser->receivedRequests()->with('sender');

            $totalCount = $query->count();
            $receivedRequests = $query->limit($limit)->get();

            return [
                'receivedRequests' => $receivedRequests,
                'total_count' => $totalCount
            ];

        } catch (Exception $e) {
            throw new Exception('Failed to retrieve sent connection requests: ' . $e->getMessage());
        }
    }
}
