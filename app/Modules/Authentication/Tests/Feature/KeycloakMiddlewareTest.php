<?php

namespace App\Modules\Authentication\Tests\Feature;

use App\Modules\Authentication\Database\Factories\UserFactory;
use App\Modules\Authentication\Models\User;
use App\Modules\Authentication\Models\Tenant;
use App\Modules\Authentication\Services\KeycloakService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Tests\TestCase;

class KeycloakMiddlewareTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $keycloakServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock the Keycloak service
        $this->keycloakServiceMock = Mockery::mock(KeycloakService::class);
        $this->app->instance(KeycloakService::class, $this->keycloakServiceMock);

        // Create a tenant for testing
        Tenant::create([
            'name' => 'Test Tenant',
            'domain' => 'test.example.com',
            'keycloak_realm' => 'test-realm',
            'is_active' => true,
        ]);

        // Define test route with middleware
        $this->app['router']->get('/api/test-keycloak-middleware', function () {
            return response()->json(['message' => 'Success']);
        })->middleware('keycloak');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test successful middleware authentication.
     */
    public function testSuccessfulAuthentication(): void
    {
        // Prepare mock data
        $token = 'valid-token';
        $tokenInfo = [
            'active' => true,
            'sub' => '12345',
        ];
        $userInfo = [
            'sub' => '12345',
            'email' => 'test@example.com',
            'username' => 'testuser',
            'last_name' => 'Test',
            'first_name' => 'User',
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
        $response = $this->withHeaders([
                'Authorization' => "Bearer {$token}",
            ])
            ->getJson('/api/test-keycloak-middleware');

        // Assert response
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Success',
            ]);
    }

    /**
     * Test middleware with invalid token.
     */
    public function testInvalidToken(): void
    {
        // Mock Keycloak service methods
        $this->keycloakServiceMock->shouldReceive('validateToken')
            ->with('invalid-token')
            ->once()
            ->andReturn(null);

        // Make request
        $response = $this->withHeaders([
                'Authorization' => 'Bearer invalid-token',
            ])
            ->getJson('/api/test-keycloak-middleware');

        // Assert response
        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Invalid token',
            ]);
    }

    /**
     * Test middleware with missing token.
     */
    public function testMissingToken(): void
    {
        // Make request without token
        $response = $this->getJson('/api/test-keycloak-middleware');

        // Assert response
        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated',
            ]);
           
    }
}
