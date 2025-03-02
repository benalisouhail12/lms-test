<?php

namespace app\Modules\StudentPortal\Tests\Feature;

use App\Modules\Authentication\Database\Factories\UserFactory;
use App\Modules\Authentication\Models\User;
use App\Modules\CourseManagement\Models\Course;
use App\Modules\CourseManagement\Models\LearningPath;
use App\Modules\CourseManagement\Models\Lesson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = UserFactory::new()->create();
    }

    /** @test */
    public function it_can_show_user_profile()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/student/profile');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'first_name',
                    'last_name',
                    'email',
                   /*  'phone',
                    'bio', */

                ]
            ]);
    }
  /** @test */
  public function it_can_update_user_profile()
  {
      $profileData = [
          'first_name' => $this->faker->firstName,
          'last_name' => $this->faker->lastName,
   /*        'phone' => $this->faker->phoneNumber,
          'bio' => $this->faker->paragraph, */
      ];

      $response = $this->actingAs($this->user)
          ->putJson('/api/student/profile', $profileData);

      $response->assertStatus(200)
          ->assertJson([
              'success' => true,
              'data' => $profileData
          ]);
  }

  /** @test */
  public function it_validates_email_on_update()
  {
      // Create another user to test unique email validation
      $anotherUser = UserFactory::new()->create();

      $profileData = [
          'email' => $anotherUser->email, // This should fail validation
      ];

      $response = $this->actingAs($this->user)
          ->putJson('/api/student/profile', $profileData);

      $response->assertStatus(422)
          ->assertJsonValidationErrors('email');
  }

  /** @test */
  public function it_can_get_academic_history()
  {
      $response = $this->actingAs($this->user)
          ->getJson('/api/student/profile/academic-history');

      $response->assertStatus(200)
          ->assertJsonStructure([
              'success',
              'data' // Structure depends on your implementation
          ]);
  }

  /** @test */
  public function it_can_update_avatar()
  {
      Storage::fake('avatars');

      $file = UploadedFile::fake()->image('avatar.jpg');

      $response = $this->actingAs($this->user)
          ->postJson('/api/student/profile/avatar', [
              'avatar' => $file,
          ]);

      $response->assertStatus(200)
          ->assertJson([
              'success' => true,
          ])
          ->assertJsonStructure([
              'success',
              'data' => [
                  'avatar_url',
              ]
          ]);

      // Add assertions to verify the file was stored correctly
      // based on your implementation
  }

  /** @test */
  public function it_validates_avatar_upload()
  {
      $response = $this->actingAs($this->user)
          ->postJson('/api/student/profile/avatar', [
              'avatar' => 'not-a-file',
          ]);

      $response->assertStatus(422)
          ->assertJsonValidationErrors('avatar');
  }

  /** @test */
  public function it_can_update_preferences()
  {
      $preferences = [
          'preferences' => [
              'notifications' => true,
              'theme' => 'dark',
              'language' => 'en',
          ],
      ];

      $response = $this->actingAs($this->user)
          ->putJson('/api/student/profile/preferences', $preferences);

      $response->assertStatus(200)
          ->assertJson([
              'success' => true,
          ])
          ->assertJsonStructure([
              'success',
              'data' => [
                  'preferences',
              ]
          ]);
  }

}








