<?php

namespace App\Modules\Authentication\Tests\Unit;

use App\Modules\Authentication\Models\User;
use App\Modules\Authentication\Models\Tenant;
use App\Modules\Authentication\Services\KeycloakService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Mockery;
use Tests\TestCase;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class KeycloakServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $client;
    protected $mockHandler;
    protected $keycloakService;


    protected function setUp(): void
    {
        parent::setUp();

        // Create roles with a specific guard
        if (!Role::where('name', 'student')->where('guard_name', 'web')->exists()) {
            Role::create(['name' => 'student', 'guard_name' => 'web']);
        }

        if (!Role::where('name', 'guest')->where('guard_name', 'web')->exists()) {
            Role::create(['name' => 'guest', 'guard_name' => 'web']);
        }

        // Create mock HTTP client
        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);
        $this->client = new Client(['handler' => $handlerStack]);

        // Create a tenant for testing
        $tenant = Tenant::create([
            'name' => 'Test Tenant',
            'domain' => 'test.example.com',
            'keycloak_realm' => 'test-realm',
            'is_active' => true,
        ]);

        // Create real service but with mocked HTTP client
        $this->keycloakService = new KeycloakService(
            'https://keycloak.test',
            'test-realm',
            'test-client',
            'test-secret'
        );
        $this->app->instance(KeycloakService::class, $this->keycloakService);

        // Replace private client property with our mock
        $reflection = new \ReflectionClass($this->keycloakService);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($this->keycloakService, $this->client);
    }
    /**
     * Test getting admin token.
     */
    public function testGetAdminToken(): void
    {
        // Mock response
        $this->mockHandler->append(
            new Response(200, [], json_encode([
                'access_token' => 'admin-token',
                'expires_in' => 300,
            ]))
        );

        // Clear cache to ensure fresh request
        Cache::flush();

        // Call method
        $token = $this->keycloakService->getAdminToken();

        // Assert result
        $this->assertEquals('admin-token', $token);
    }

    /**
     * Test validating token.
     */
    public function testValidateToken(): void
    {
        // Mock response
        $this->mockHandler->append(
            new Response(200, [], json_encode([
                'active' => true,
                'sub' => '12345',
                'exp' => time() + 3600,
            ]))
        );

        // Call method
        $result = $this->keycloakService->validateToken('test-token');

        // Assert result
        $this->assertTrue($result['active']);
        $this->assertEquals('12345', $result['sub']);
    }

    /**
     * Test getting user info.
     */
    public function testGetUserInfo(): void
    {
        // Mock response
        $this->mockHandler->append(
            new Response(200, [], json_encode([
                'sub' => '12345',
                'email' => 'test@example.com',
                'preferred_username' => 'testuser',
                'given_name' => 'Test',
                'family_name' => 'User',
            ]))
        );

        // Call method
        $userInfo = $this->keycloakService->getUserInfo('test-token');

        // Assert result
        $this->assertEquals('12345', $userInfo['sub']);
        $this->assertEquals('test@example.com', $userInfo['email']);
        $this->assertEquals('testuser', $userInfo['preferred_username']);
    }

    /**
     * Test syncing user from Keycloak data.
     */
    public function testSyncUser(): void
    {
        // Mock responses for getting roles
        $this->mockHandler->append(
            new Response(200, [], json_encode([
                ['name' => 'student'],
                ['name' => 'guest'],
            ]))
        );

        // Prepare test data
        $keycloakData = [
            'sub' => '12345',
            'email' => 'test@example.com',
            'preferred_username' => 'testuser',
            'given_name' => 'Test',
            'family_name' => 'User',
        ];

        // Get tenant
        $tenant = Tenant::first();

        // Check if roles already exist, if not, create them
        if (!Role::where('name', 'student')->where('guard_name', 'web')->exists()) {
            Role::create(['name' => 'student', 'guard_name' => 'web']);
        }

        if (!Role::where('name', 'guest')->where('guard_name', 'web')->exists()) {
            Role::create(['name' => 'guest', 'guard_name' => 'web']);
        }

        // Call the method to sync the user
        $user = $this->keycloakService->syncUser($keycloakData, $tenant);

        // Assert user was created correctly
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('12345', $user->keycloak_id);
        $this->assertEquals('test@example.com', $user->email);
        $this->assertEquals('testuser', $user->username);
        $this->assertEquals('Test', $user->first_name);
        $this->assertEquals('User', $user->last_name);
        $this->assertEquals($tenant->id, $user->tenant_id);

        // Assert that roles were assigned correctly with the 'api' guard
        $this->assertTrue($user->hasRole('student', 'web'));
        $this->assertTrue($user->hasRole('guest', 'web'));
    }



}

