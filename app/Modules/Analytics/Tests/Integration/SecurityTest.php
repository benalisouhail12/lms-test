<?php
namespace app\Modules\Analytics\Tests\Integration;

use app\Modules\Analytics\Database\Factories\ReportFactory;
use app\Modules\Authentication\Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_requires_authentication()
    {
        // Attempt to access protected endpoints without auth
        $this->getJson('/api/dashboard/metrics')
             ->assertStatus(401);

        $this->getJson('/api/reports')
             ->assertStatus(401);

        $this->getJson('/api/metrics')
             ->assertStatus(401);
    }

    public function test_user_cannot_access_reports_from_other_users()
    {
        // Arrange
        $user1 = UserFactory::new()->create();
        $user2 =UserFactory::new()->create();

        // Create a report owned by user1
        $report = ReportFactory::new()->create([
            'created_by' => $user1->id,
            'title' => 'User 1 Report'
        ]);

        // Act & Assert
        // User1 can access their report
        $this->actingAs($user1)
             ->getJson("/api/reports/{$report->id}")
             ->assertStatus(200);

        // User2 cannot access user1's report (should return 403 Forbidden)
        $this->actingAs($user2)
             ->getJson("/api/reports/{$report->id}")
             ->assertStatus(403);
    }

    public function test_input_validation()
    {
        // Arrange
        $user = UserFactory::new()->create();

        // Test validation for creating a metric
        $invalidMetricData = [
            'name' => '', // Empty name (should be required)
            'value' => 'not-a-number', // Not a number
            'period' => '', // Empty period (should be required)
            'date_recorded' => 'invalid-date' // Invalid date format
        ];

        // Act & Assert
        $this->actingAs($user)
             ->postJson('/api/metrics', $invalidMetricData)
             ->assertStatus(422)
             ->assertJsonValidationErrors(['name', 'value', 'period', 'date_recorded']);
    }

    public function test_csrf_protection()
    {
        // This test ensures CSRF protection is in place
        // But since API routes typically use token auth and not CSRF,
        // we're checking that non-API routes require CSRF tokens

        $user = UserFactory::new()->create();

        // Attempt to make a form POST request without CSRF token
        $response = $this->actingAs($user)
                         ->withHeaders(['Accept' => 'text/html'])
                         ->post('/logout');

        // Laravel should return a 419 (Page Expired) for missing CSRF token
        $response->assertStatus(419);
    }

    public function test_api_rate_limiting()
    {


        $user = UserFactory::new()->create();


        $this->actingAs($user);

        $responses = [];
        for ($i = 0; $i < 60; $i++) {
            $responses[] = $this->getJson('/api/metrics');
        }

    }
}
