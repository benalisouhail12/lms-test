<?php

namespace App\Modules\Authentication\Tests\Feature;

use App\Modules\Authentication\Database\Factories\UserFactory ;
use App\Modules\Authentication\Models\Tenant;
use App\Modules\Authentication\Models\User;
use App\Modules\Authentication\Services\KeycloakService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $keycloakServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a role for testing
        Role::create(['name' => 'student']);

        // Mock the Keycloak service
        $this->keycloakServiceMock = \Mockery::mock(KeycloakService::class);

        $this->app->instance(KeycloakService::class, $this->keycloakServiceMock);

        // Create a tenant for testing
        Tenant::create([
            'name' => 'Test Tenant',
            'domain' => 'test.example.com',
            'keycloak_realm' => 'test-realm',
            'is_active' => true,
        ]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test successful login callback.
     */
    public function testLoginCallback(): void
    {
        // Prepare mock data
        $token = 'fake-token';
        $tokenInfo = [
            'active' => true,
            'sub' => '12345',
        ];
        $userInfo = [
            'sub' => '12345',
            'email' => 'test@example.com',
            'preferred_username' => 'testuser',
            'given_name' => 'Test',
            'family_name' => 'User',
        ];

        // Mock Keycloak service methods
        $this->keycloakServiceMock->shouldReceive('validateToken')
            ->with($token)
            ->once()
            ->andReturn($tokenInfo);

        $this->keycloakServiceMock->shouldReceive('getUserInfo')
            ->with($token)
            ->once()
            ->andReturn($userInfo);

        $this->keycloakServiceMock->shouldReceive('syncUser')
            ->once()
            ->andReturn(UserFactory::new()->create([
                'keycloak_id' => '12345',
                'email' => 'test@example.com',
            ]));

        // Make request
        $response = $this->postJson('/api/auth/login/callback', [
            'token' => $token,
        ]);

        // Assert response
        $response->assertStatus(200)
            ->assertJsonStructure([
                'user',
                'token',
                'session_id',
                'expires_at',
            ]);


    }
 /**
     * Test invalid token in login callback.
     */
    public function testLoginCallbackWithInvalidToken(): void
    {
        // Mock Keycloak service methods
        $this->keycloakServiceMock->shouldReceive('validateToken')
            ->with('invalid-token')
            ->once()
            ->andReturn(null);

        // Make request
        $response = $this->postJson('/api/auth/login/callback', [
            'token' => 'invalid-token',
        ]);

        // Assert response
        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Invalid token',
            ]);
    }
/**
     * Test me endpoint with authenticated user.
     */
    public function testMeWithAuthenticatedUser(): void
    {
        // Create and authenticate a user
        $user = UserFactory::new()->create();

        // Make request as authenticated user
        $response = $this->actingAs($user)->getJson('/api/auth/me');

        // Assert response
        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'username',
                    'email',
                    'first_name',
                    'last_name',
                ],
            ]);
    }
 /**
     * Test me endpoint without authentication.
     */
    public function testMeWithoutAuthentication(): void
    {
        // Make request without authentication
        $response = $this->getJson('/api/auth/me');

        // Assert response
        $response->assertStatus(401);
    }
      /**
     * Test logout endpoint.
     */
    public function testLogout(): void
    {
        // Create and authenticate a user
        $user = UserFactory::new()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        // Mock Keycloak service methods
        $this->keycloakServiceMock->shouldReceive('logout')
            ->once()
            ->andReturn(true);

        // Make request
        $response = $this->withHeaders([
                'Authorization' => "Bearer {$token}",
            ])
            ->postJson('/api/auth/logout', [
                'refresh_token' => 'test-refresh-token',
            ]);

        // Assert response
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Logged out successfully',
            ]);

        // Assert user tokens are deleted
        $user = $user->fresh(); // Get the updated user from DB
        $this->assertCount(0, $user->tokens); // Verify no tokens exist

        // Optionally, assert that the Keycloak logout method was called with the correct token
        $this->keycloakServiceMock->shouldHaveReceived('logout')
            ->with('test-refresh-token')
            ->once();
    }
       /**
     * Test role synchronization.
     */
    public function testSyncRoles(): void
    {
        // Create and authenticate a user
        $user = UserFactory::new()->create();

        // Mock Keycloak service methods
        $this->keycloakServiceMock->shouldReceive('syncUserRoles')
            ->with($user)
            ->once();

        // Make request as authenticated user
        $response = $this->actingAs($user)->postJson('/api/auth/sync-roles');

        // Assert response
        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'roles',
            ]);
    }





}
