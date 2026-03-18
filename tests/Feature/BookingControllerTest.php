<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class BookingControllerTest extends TestCase
{
    use RefreshDatabase;

    /** 
     * Test booking can be created successfully
     */
    public function test_booking_can_be_created_successfully()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create event
        $eventPayload = [
            'name' => 'Football Match 2',
            'description' => 'Second match',
            'start_date' => '2026-03-21 14:00:00',
            'end_date' => '2026-03-21 17:00:00',
            'capacity' => 100,
        ];

        $createEvent = $this->postJson('/api/events', $eventPayload);
        $eventId = $createEvent->json('event.id');

        // Create booking
        $bookingPayload = [
            'email_address' => 'marjankolev1994@yahoo.com',
            'seats_booked' => 2
        ];

        $response = $this->postJson("/api/events/{$eventId}/bookings", $bookingPayload);

        $response->assertStatus(201)
                ->assertJson([
                    'message' => 'Booking for the Event created successfully!'
                ]);

        $this->assertDatabaseHas('bookings', [
            'event_id' => $eventId,
            'email_address' => 'marjankolev1994@yahoo.com',
            'seats_booked' => 2
        ]);
    }

    /** 
     * Test created booking can be listed successfully
     */
    public function test_booking_can_be_listed_successfully()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create event
        $eventPayload = [
            'name' => 'Football Match 3',
            'description' => 'Third match',
            'start_date' => '2026-03-22 14:00:00',
            'end_date' => '2026-03-22 17:00:00',
            'capacity' => 100,
        ];

        $createEvent = $this->postJson('/api/events', $eventPayload);
        $eventId = $createEvent->json('event.id');

        // Create booking
        $this->postJson("/api/events/{$eventId}/bookings", [
            'email_address' => 'marjankolev1994@yahoo.com',
            'seats_booked' => 2
        ]);

        $this->postJson("/api/events/{$eventId}/bookings", [
            'email_address' => 'marjankolev1994@yahoo.com',
            'seats_booked' => 3
        ]);

        $response = $this->getJson("/api/events/{$eventId}/bookings");

        $response->assertStatus(200)
                ->assertJsonCount(2);
    }

    /** 
     * Test Booking creation fails because capacity of the seats for the Event is exceeded
     */
    public function test_booking_fails_if_event_capacity_exceeded()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create event
        $eventPayload = [
            'name' => 'Limited Event',
            'description' => 'Event with Limited Seats',
            'start_date' => '2026-03-25 14:00:00',
            'end_date' => '2026-03-25 17:00:00',
            'capacity' => 5,
        ];

        $createEvent = $this->postJson('/api/events', $eventPayload);
        $eventId = $createEvent->json('event.id');

        // Create first booking (with 4 seats)
        $this->postJson("/api/events/{$eventId}/bookings", [
            'email_address' => 'marjankolev1994@yahoo.com',
            'seats_booked' => 4
        ]);

        // Create second booking (exceed capacity: 4 + 2 > 5)
        $response = $this->postJson("/api/events/{$eventId}/bookings", [
            'email_address' => 'marjan_kolev1994@yahoo.com',
            'seats_booked' => 2
        ]);

        $response->assertStatus(400)
                ->assertJson([
                    'error' => 'Booking exceeds available seats.'
                ]);
    }

    /** 
     * Test status change of booking
     */
    public function test_booking_status_change()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create event
        $eventPayload = [
            'name' => 'Football Match 4',
            'description' => 'Fourth match',
            'start_date' => '2026-03-23 14:00:00',
            'end_date' => '2026-03-23 17:00:00',
            'capacity' => 100,
        ];

        $createEvent = $this->postJson('/api/events', $eventPayload);
        $eventId = $createEvent->json('event.id');

        // Create booking
        $createBooking = $this->postJson("/api/events/{$eventId}/bookings", [
            'email_address' => 'marjankolev1994@yahoo.com',
            'seats_booked' => 2
        ]);

        $bookingId = $createBooking->json('booking.id');

        $response = $this->patchJson("/api/bookings/{$bookingId}", [
            'status' => 'confirmed'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Booking status updated successfully!'
                ]);

        $this->assertDatabaseHas('bookings', [
            'id' => $bookingId,
            'status' => 'confirmed'
        ]);
    }

    /** 
     * Test filtering booking by email
     */
    public function test_booking_can_be_filtered_by_email()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create event
        $eventPayload = [
            'name' => 'Football Match 5',
            'description' => 'Fifth match',
            'start_date' => '2026-03-24 14:00:00',
            'end_date' => '2026-03-24 17:00:00',
            'capacity' => 100,
        ];

        $createEvent = $this->postJson('/api/events', $eventPayload);
        $eventId = $createEvent->json('event.id');

        // Create booking
        $this->postJson("/api/events/{$eventId}/bookings", [
            'email_address' => 'test1@test.com',
            'seats_booked' => 2
        ]);

        $this->postJson("/api/events/{$eventId}/bookings", [
            'email_address' => 'marjankolev1994@yahoo.com',
            'seats_booked' => 3
        ]);

        $response = $this->getJson('/api/bookings/search?email=marjankolev1994@yahoo.com');

        $response->assertStatus(200)
                ->assertJsonCount(1)
                ->assertJsonFragment([
                    'email_address' => 'marjankolev1994@yahoo.com'
                ]);
    }
    
}