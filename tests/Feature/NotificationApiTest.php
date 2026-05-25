<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_api_crud_operations_work_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/notifications')
            ->assertOk()
            ->assertExactJson([]);

        $createResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/notifications', [
                'message' => 'Nouvelle notification',
                'type' => 'info',
            ]);

        $createResponse->assertCreated()
            ->assertJsonPath('user_id', $user->id)
            ->assertJsonPath('message', 'Nouvelle notification')
            ->assertJsonPath('type', 'info');

        $notification = Notification::query()->where('user_id', $user->id)->latest('id')->firstOrFail();

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/notifications')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment([
                'id' => $notification->id,
                'user_id' => $user->id,
                'message' => 'Nouvelle notification',
                'type' => 'info',
            ]);

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/notifications/' . $notification->id . '/read')
            ->assertOk()
            ->assertJson(['message' => 'Notification marquée comme lue']);

        $this->assertNotNull(
            Notification::query()->whereKey($notification->id)->value('read_at')
        );

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/notifications/' . $notification->id)
            ->assertOk()
            ->assertJson(['message' => 'Notification supprimée']);

        $this->assertDatabaseMissing('notifications', [
            'id' => $notification->id,
        ]);
    }
}
