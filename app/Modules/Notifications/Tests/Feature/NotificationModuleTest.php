<?php

namespace app\Modules\Notifications\Tests\Feature;

use app\Modules\Authentication\Database\Factories\UserFactory;
use App\Modules\Authentication\Models\User;
use app\Modules\Notifications\Database\Factories\NotificationFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class NotificationModuleTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = UserFactory::new()->create();
        $this->actingAs($this->user);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_can_get_notifications()
    {
        NotificationFactory::new()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->getJson('/notifications');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'message', 'status', 'created_at']
            ]
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_can_create_notification()
    {
        // Ensure that you have a user to associate the notification with
        $user = UserFactory::new()->create(); // Create a user for association

        // Add all the necessary fields
        $payload = [
            'user_id' => $user->id,   // Add the user_id
            'type' => 'info',         // Add a type (e.g., info)
            'title' => 'Test Notification Title',  // Add a title
            'message' => 'New test notification',  // Add the message
            'status' => 'unread',     // Status should be provided
            'priority' => 'medium',   // Priority (default if not specified)
            'expires_at' => null,     // Optional, null if not provided
            'group_id' => null,       // Optional
            'meta_data' => [],        // Optional, empty array if not provided
        ];

        // Send the request to create the notification
        $response = $this->postJson('/notifications', $payload);

        // Assert that the response status is 201 (Created)
        $response->assertStatus(201)
                 ->assertJsonFragment(['message' => 'New test notification']);  // Check that message exists in the response

        // Assert that the notification has been added to the database
        $this->assertDatabaseHas('notifications', [
            'message' => 'New test notification',
            'user_id' => $user->id,
        ]);

       /*  // Insert a record in the notification_preferences table with the necessary fields
        \App\Modules\Notifications\Models\NotificationPreference::create([
            'user_id' => $user->id,
            'channels' => json_encode(['email' => ['enabled' => true]]),  // Provide a default channel if necessary
            'notification_types' => json_encode(['info' => ['enabled' => true]]),  // Provide some notification types
            'quiet_hours' => json_encode(['enabled' => false]),  // Example of quiet hours, if needed
            'digest_settings' => json_encode(['enabled' => false]),  // Example of digest settings
        ]);

        // Optionally, check if the preference record is created
        $this->assertDatabaseHas('notification_preferences', [
            'user_id' => $user->id,
            'channels' => json_encode(['email' => ['enabled' => true]]),
        ]); */
    }




/*
    #[\PHPUnit\Framework\Attributes\Test]
    public function user_can_update_notification_status()
    {
        $notification = NotificationFactory::new()->create(['user_id' => $this->user->id, 'status' => 'unread']);

        $response = $this->patchJson("/notifications/{$notification->id}/status", ['status' => 'read']);

        $response->assertStatus(200)
                 ->assertJsonFragment(['status' => 'read']);

        $this->assertDatabaseHas('notifications', ['id' => $notification->id, 'status' => 'read']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_can_delete_notification()
    {
        $notification = NotificationFactory::new()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson("/notifications/{$notification->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('notifications', ['id' => $notification->id]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_can_mark_all_notifications_as_read()
    {
        Notification::factory()->count(3)->create(['user_id' => $this->user->id, 'status' => 'unread']);

        $response = $this->postJson('/notifications/mark-all-as-read');

        $response->assertStatus(200);
        $this->assertDatabaseMissing('notifications', ['status' => 'unread']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_can_get_notification_history()
    {
        NotificationFactory::new()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->getJson('/notifications/history');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     '*' => ['id', 'message', 'status', 'created_at']
                 ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_can_get_notification_preferences()
    {
        $preference = NotificationPreferenceFactory::new()->create(['user_id' => $this->user->id]);

        $response = $this->getJson('/preferences/notifications');

        $response->assertStatus(200)
                 ->assertJsonFragment(['email_notifications' => $preference->email_notifications]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_can_update_notification_preferences()
    {
        $preference = NotificationPreferenceFactory::new()->create(['user_id' => $this->user->id]);

        $newData = ['email_notifications' => false, 'push_notifications' => true];

        $response = $this->putJson('/preferences/notifications', $newData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('preferences', [
            'user_id' => $this->user->id,
            'email_notifications' => false,
            'push_notifications' => true,
        ]);
    } */
}
