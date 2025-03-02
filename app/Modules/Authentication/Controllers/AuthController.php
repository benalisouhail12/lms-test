<?php

namespace App\Modules\Authentication\Controllers;

use App\Modules\Authentication\Services\KeycloakService;
use App\Modules\Authentication\Requests\LoginCallbackRequest;
use App\Modules\Authentication\Resources\UserResource;
use App\Modules\Authentication\Models\User;
use App\Modules\Authentication\Models\UserSession;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    protected KeycloakService $keycloakService;

    public function __construct(KeycloakService $keycloakService)
    {
        $this->keycloakService = $keycloakService;
    }

    /**
     * @OA\Post(
     *     path="/api/auth/login-callback",
     *     summary="Handle the login callback from Keycloak",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"token"},
     *             @OA\Property(property="token", type="string", example="access_token")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Successful authentication"),
     *     @OA\Response(response=401, description="Invalid token"),
     *     @OA\Response(response=500, description="Authentication failed")
     * )
     */
    public function loginCallback(LoginCallbackRequest $request): JsonResponse
    {
        try {
            $tokenInfo = $this->keycloakService->validateToken($request->token);
            if (!$tokenInfo) {
                return response()->json(['message' => 'Invalid token'], 401);
            }

            $userInfo = $this->keycloakService->getUserInfo($request->token);
            if (!$userInfo) {
                return response()->json(['message' => 'Failed to get user info'], 500);
            }

            $user = $this->keycloakService->syncUser($userInfo);
            $user->last_login_at = now();
            $user->save();

            $session = $this->createUserSession($user, $request);
            Auth::login($user);
            $token = $user->createToken('api-token', [], now()->addHours(8));

            return response()->json([
                'user' => new UserResource($user),
                'token' => $token->plainTextToken,
                'session_id' => $session->id,
                'expires_at' => $session->expires_at->toIso8601String(),
            ]);
        } catch (\Exception $e) {
            Log::error('Login callback error: ' . $e->getMessage());
            return response()->json(['message' => 'Authentication failed'], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/auth/me",
     *     summary="Get the current authenticated user",
     *     tags={"Authentication"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="User data"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        return response()->json(['user' => new UserResource($user)]);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     summary="Logout the user",
     *     tags={"Authentication"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="session_id", type="string", example="session_id"),
     *             @OA\Property(property="refresh_token", type="string", example="refresh_token")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Logged out successfully"),
     *     @OA\Response(response=404, description="No user found")
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user) {
            $user->tokens()->delete();
            if ($request->has('session_id')) {
                UserSession::where('id', $request->session_id)
                    ->where('user_id', $user->id)
                    ->delete();
            } else {
                $user->sessions()->delete();
            }
            if ($request->has('refresh_token')) {
                $this->keycloakService->logout($request->refresh_token);
            }
            return response()->json(['message' => 'Logged out successfully']);
        }
        return response()->json(['message' => 'No user found'], 404);
    }




    /**
     * Sync user roles
     */
    public function syncRoles(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        try {
            $this->keycloakService->syncUserRoles($user);

            return response()->json([
                'message' => 'Roles synchronized successfully',
                'roles' => $user->getRoleNames(),
            ]);
        } catch (\Exception $e) {
            Log::error('Role sync error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to sync roles'], 500);
        }
    }

    /**
     * Create a user session
     */
    private function createUserSession(User $user, Request $request): UserSession
    {
        return UserSession::create([
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'payload' => [
                'tenant_id' => $user->tenant_id,
                'roles' => $user->getRoleNames()->toArray(),
            ],
            'last_activity' => now(),
            'expires_at' => now()->addHours(8),
        ]);
    }
}
