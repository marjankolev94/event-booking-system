<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class EventControllerTest extends TestCase
{
    use RefreshDatabase;

    /** 
     * Test event can be created successfully
     */
    public function test_event_can_be_created_successfully()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $payload = [
            'name' => 'Football Match 1',
            'description' => 'This is the first Football Match for this Season',
            'start_date' => '2026-03-20 14:00:00',
            'end_date' => '2026-03-20 17:00:00',
            'capacity' => 1500,
        ];

        $response = $this->postJson('/api/events', $payload);

        $response->assertStatus(201)
        ->assertJson([
            'message' => 'Event created successfully!'
        ]);

        $this->assertDatabaseHas('events', [
            'name' => 'Football Match 1',
            'capacity' => 1500
        ]);
    }

    /** 
     * Test event can be created with invalid data
     */
    public function test_event_creation_fails_with_invalid_data()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $payload = [
            'name' => '', 
            'description' => '', 
            'start_date' => 'invalid-date',
            'end_date' => now()->subDay()->format('Y-m-d H:i:s'),
            'capacity' => 'invalid'
        ];

        $response = $this->postJson('/api/events', $payload);

        $response->assertStatus(422);
    }

    /** 
     * Test created event can be listed
     */
    public function test_list_created_event()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $payload = [
            'name' => 'Football Match 1',
            'description' => 'This is the first Football Match for this Season',
            'start_date' => '2026-03-20 14:00:00',
            'end_date' => '2026-03-20 17:00:00',
            'capacity' => 1500,
        ];

        $response = $this->postJson('/api/events', $payload);

        $createdEvent = $this->getJson('/api/events');

        $createdEvent->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    /** 
     * Test created event can be viewed
     */
    public function test_view_created_event()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $payload = [
            'name' => 'Football Match 1',
            'description' => 'This is the first Football Match for this Season',
            'start_date' => '2026-03-20 14:00:00',
            'end_date' => '2026-03-20 17:00:00',
            'capacity' => 1500,
        ];

        $response = $this->postJson('/api/events', $payload);

        $createdEvent = $this->getJson('/api/events');

        $createdEvent->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    /** 
     * Test event can be deleted
     */
    public function test_event_can_be_deleted()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $payload = [
            'name' => 'Football Match 1',
            'description' => 'This is the first Football Match for this Season',
            'start_date' => '2026-03-20 14:00:00',
            'end_date' => '2026-03-20 17:00:00',
            'capacity' => 1500,
        ];

        $createEvent = $this->postJson('/api/events', $payload);

        $eventId = $createEvent->json('event.id');

        $response = $this->deleteJson("/api/events/{$eventId}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('events', [
            'id' => $eventId
        ]);
    }

    /** 
     * Test Event Booking Progress (Percentage of Booked seats)
     */
    public function test_event_booking_progress()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create event
        $eventPayload = [
            'name' => 'Football Match 1',
            'description' => 'This is the first Football Match for this Season',
            'start_date' => '2026-03-20 14:00:00',
            'end_date' => '2026-03-20 17:00:00',
            'capacity' => 100,
        ];

        $createEvent = $this->postJson('/api/events', $eventPayload);

        $eventId = $createEvent->json('event.id');

        // Create booking
        $bookingPayload = [
            'event_id' => $eventId,
            'email_address' => 'test@test.com',
            'seats_booked' => 50,
            'status' => 'confirmed'
        ];

        $createEvent = $this->postJson("/api/events/{$eventId}/bookings", $bookingPayload);

        $response = $this->getJson("/api/events/{$eventId}/progress");

        $response->assertStatus(200)
                ->assertJson([
                    'progress' => '50 %'
                ]);
    }
    
}