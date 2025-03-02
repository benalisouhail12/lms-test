<?php

namespace App\Modules\Authentication\Middleware;

use App\Modules\Authentication\Services\KeycloakService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class KeycloakAuthentication
{
    protected KeycloakService $keycloakService;

    public function __construct(KeycloakService $keycloakService)
    {
        $this->keycloakService = $keycloakService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Skip if already authenticated
        if (Auth::check()) {
            return $next($request);
        }

        // Get token from Authorization header
        $token = $this->getTokenFromRequest($request);

        if (!$token) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        try {
            // Validate the token
            $tokenInfo = $this->keycloakService->validateToken($token);

            if (!$tokenInfo) {
                return response()->json(['message' => 'Invalid token'], 401);
            }

            // Get user info
            $userInfo = $this->keycloakService->getUserInfo($token);

            if (!$userInfo) {
                return response()->json(['message' => 'Failed to get user info'], 500);
            }

            // Sync user data
            $user = $this->keycloakService->syncUser($userInfo);

            // Login user
            Auth::login($user);

            return $next($request);
        } catch (\Exception $e) {
            Log::error('Keycloak authentication error: ' . $e->getMessage());
            return response()->json(['message' => 'Authentication failed'], 500);
        }
    }

    /**
     * Get the token from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function getTokenFromRequest(Request $request): ?string
    {
        $header = $request->header('Authorization');

        if (!$header || !str_starts_with($header, 'Bearer ')) {
            return null;
        }

        return trim(substr($header, 7));
    }
}





