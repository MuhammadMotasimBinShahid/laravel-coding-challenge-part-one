<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Throwable;

class AuthController extends Controller
{
    //User Registration Function
    /**
     * @param Request $request
     * @throws ValidationException
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        try {
            // Validating Request
            $validatedData = $request->validate([
                'name' => 'required|string',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:8',
            ]);

            // Creating a new user
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => bcrypt($validatedData['password']),
            ]);

            // Generating a personal access token for the registered user
            $token = $user->createToken('authToken')->plainTextToken;

            // Return a response with the generated token, user, and message
            return response()->json([
                'user' => $user,
                'token' => $token,
                'message' => 'User created successfully!'
            ], 201);
        } catch (ValidationException $e) {
            // Validation failed
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->validator->getMessageBag()
            ], 422);
        } catch (QueryException $e) {
            // Database error
            return response()->json([
                'message' => 'Failed to create user',
                'error' => 'An error occurred while creating the user.'
            ], 500);
        } catch (\Exception $e) {
            // Other exceptions
            return response()->json([
                'message' => 'An error occurred',
                'error' => 'An unexpected error occurred.'
            ], 500);
        }
    }


    /**
     * User Login Function
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Check if the user exists
        $user = User::where('email', $validatedData['email'])->first();

        if (!$user) {
            return response()->json(['message' => 'User not exits!'], 404);
        }

        // Attempt to authenticate the user
        if (Auth::attempt($validatedData)) {
            // Generating new personal access token
            $token = $user->createToken('authToken')->plainTextToken;

            // Return a response with the generated token, user, and message
            return response()->json([
                'user' => $user,
                'token' => $token,
                'message' => 'User logged in successfully!'
            ], 200);
        }

        // Failed authentication
        return response()->json(['message' => 'Wrong credentials!'], 401);
    }

    // Refresh Token Function
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function refreshToken(Request $request): JsonResponse
    {
        try {
            // Getting the current authenticated user
            $user = Auth::user();

            // Revoking the user's current tokens
            $user->tokens()->delete();

            // Generating new personal access token
            $token = $user->createToken('authToken')->plainTextToken;

            // Return a response with the new token
            return response()->json(['token' => $token, 'message' => 'Token refreshed successfully.'], 200);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Failed to refresh token.'], 500);
        }
    }

    // Logout Function
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        // Revoking the user's access token
        $request->user()->currentAccessToken()->delete();

        // Return a response indicating successful logout
        return response()->json(['message' => 'User Logged out successfully'], 200);
    }
}
