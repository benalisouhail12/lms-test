<?php

namespace App\Modules\Authentication\Services;

use App\Modules\Authentication\Models\User;
use App\Modules\Authentication\Models\Tenant;
use App\Modules\Authentication\Events\UserRoleSyncEvent;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class KeycloakService
{
    private Client $client;
    private string $baseUrl;
    private string $realm;
    private string $clientId;
    private string $clientSecret;

    public function __construct(string $baseUrl, string $realm, string $clientId, string $clientSecret)
    {
        $this->baseUrl = $baseUrl;
        $this->realm = $realm;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => 10.0,
        ]);
    }

    /**
     * Get admin token for Keycloak API access
     */
    public function getAdminToken(): ?string
    {
        $cacheKey = "keycloak_admin_token_{$this->realm}_{$this->clientId}";

        return Cache::remember($cacheKey, 58 * 60, function () {
            try {
                $response = $this->client->post("/auth/realms/{$this->realm}/protocol/openid-connect/token", [
                    'form_params' => [
                        'grant_type' => 'client_credentials',
                        'client_id' => $this->clientId,
                        'client_secret' => $this->clientSecret,
                    ],
                ]);

                $result = json_decode($response->getBody()->getContents(), true);
                return $result['access_token'] ?? null;
            } catch (GuzzleException $e) {
                Log::error("Keycloak admin token error: " . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Validate token from Keycloak
     */
    public function validateToken(string $token): ?array
    {
        try {
            $response = $this->client->post("/auth/realms/{$this->realm}/protocol/openid-connect/token/introspect", [
                'form_params' => [
                    'token' => $token,
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                ],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            if (!($result['active'] ?? false)) {
                return null;
            }

            return $result;
        } catch (GuzzleException $e) {
            Log::error("Token validation error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get user info from Keycloak
     */
    public function getUserInfo(string $token): ?array
    {
        try {
            $response = $this->client->get("/auth/realms/{$this->realm}/protocol/openid-connect/userinfo", [
                'headers' => [
                    'Authorization' => "Bearer {$token}",
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            Log::error("Get user info error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get user from Keycloak by ID
     */
    public function getKeycloakUser(string $userId): ?array
    {
        $token = $this->getAdminToken();

        if (!$token) {
            return null;
        }

        try {
            $response = $this->client->get("/auth/admin/realms/{$this->realm}/users/{$userId}", [
                'headers' => [
                    'Authorization' => "Bearer {$token}",
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            Log::error("Get Keycloak user error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get user roles from Keycloak
     */
    public function getUserRoles(string $userId): array
    {
        $token = $this->getAdminToken();

        if (!$token) {
            return [];
        }

        try {
            $response = $this->client->get("/auth/admin/realms/{$this->realm}/users/{$userId}/role-mappings/realm", [
                'headers' => [
                    'Authorization' => "Bearer {$token}",
                ],
            ]);

            $roles = json_decode($response->getBody()->getContents(), true);
            return array_map(function ($role) {
                return $role['name'];
            }, $roles);
        } catch (GuzzleException $e) {
            Log::error("Get user roles error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Sync user from Keycloak data
     */
    public function syncUser(array $keycloakData, ?Tenant $tenant = null): User
    {
        $tenantId = $tenant ? $tenant->id : $this->getTenantIdFromRealm($this->realm);

        $user = User::updateOrCreate(
            ['keycloak_id' => $keycloakData['sub']],
            [
                'username' => $keycloakData['preferred_username'] ?? $keycloakData['email'],
                'email' => $keycloakData['email'],
                'first_name' => $keycloakData['given_name'] ?? '',
                'last_name' => $keycloakData['family_name'] ?? '',
                'tenant_id' => $tenantId,
                'is_active' => true,
            ]
        );

        // Sync roles
        $this->syncUserRoles($user);

        return $user;
    }

    /**
     * Sync user roles from Keycloak
     */
    public function syncUserRoles(User $user): void
    {
        $keycloakRoles = $this->getUserRoles($user->keycloak_id);

        // Map Keycloak roles to local roles
        $rolesToAssign = $this->mapKeycloakRolesToLocalRoles($keycloakRoles, $user->tenant_id);

        // Get current user roles
        $currentRoles = $user->getRoleNames()->toArray();

        // Find roles to add and remove
        $rolesToAdd = array_diff($rolesToAssign, $currentRoles);
        $rolesToRemove = array_diff($currentRoles, $rolesToAssign);

        // Apply role changes
        if (!empty($rolesToAdd) || !empty($rolesToRemove)) {
            // Remove old roles
            foreach ($rolesToRemove as $role) {
                $user->removeRole($role);
            }

            // Add new roles
            foreach ($rolesToAdd as $role) {
                $user->assignRole($role);
            }

            // Trigger role sync event
            event(new UserRoleSyncEvent($user, $rolesToAdd, $rolesToRemove));
        }
    }

    /**
     * Map Keycloak roles to local roles
     */
    private function mapKeycloakRolesToLocalRoles(array $keycloakRoles, int $tenantId): array
    {
        // Basic mapping - in a real app, you might want to store this mapping in the database
        $roleMapping = [
            'student' => 'student',
            'teacher' => 'teacher',
            'department_head' => 'department_head',
            'administrator' => 'administrator',
            'guest' => 'guest',
        ];

        $mappedRoles = [];
        foreach ($keycloakRoles as $role) {
            if (isset($roleMapping[$role])) {
                $mappedRoles[] = $roleMapping[$role];
            }
        }

        return $mappedRoles;
    }

    /**
     * Get tenant ID from realm
     */
    private function getTenantIdFromRealm(string $realm): int
    {
        $tenant = Tenant::where('keycloak_realm', $realm)->first();

        if (!$tenant) {
            // Create default tenant if it doesn't exist
            $tenant = Tenant::create([
                'name' => Str::title($realm),
                'domain' => Str::slug($realm) . '.' . config('app.domain'),
                'keycloak_realm' => $realm,
                'is_active' => true,
            ]);
        }

        return $tenant->id;
    }

    /**
     * Logout from Keycloak
     */
    public function logout(string $refreshToken): bool
    {
        try {
            $this->client->post("/auth/realms/{$this->realm}/protocol/openid-connect/logout", [
                'form_params' => [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'refresh_token' => $refreshToken,
                ],
            ]);

            return true;
        } catch (GuzzleException $e) {
            Log::error("Logout error: " . $e->getMessage());
            return false;
        }
    }
}
