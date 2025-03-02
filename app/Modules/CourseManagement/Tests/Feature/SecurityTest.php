<?php
namespace App\Modules\CourseManagement\Tests\Feature;

use Tests\TestCase;

class SecurityTest extends TestCase
{
    public function testXSSProtection()
    {
        $payload = '<script>alert("XSS Attack")</script>';
        $response = $this->post('api/course/courses', ['name' => $payload]);
        $response->assertStatus(201);
        $this->assertStringNotContainsString('<script>', $response->getContent());
    }
}
