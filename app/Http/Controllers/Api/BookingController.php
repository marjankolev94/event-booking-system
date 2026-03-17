<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Repositories\Interfaces\BookingRepositoryInterface;
use App\Repositories\Interfaces\EventRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use Illuminate\Http\JsonResponse;


class BookingController extends Controller
{
    protected $bookingRepository;
    protected $eventRepository;

    public function __construct(BookingRepositoryInterface $bookingRepository, EventRepositoryInterface $eventRepository)
    {
        $this->bookingRepository = $bookingRepository;
        $this->eventRepository = $eventRepository;
    }

    public function index(int $id): JsonResponse
    {
        $bookings = $this->bookingRepository->allByEvent($id);

        return response()->json($bookings);
    }

    public function store(StoreBookingRequest $request, int $id)
    {
        $check = $this->checkAvailableNumberOfSeats($id, $request->seats_booked);

        if (!$check['success']) {
            return response()->json([
                'error' => $check['error'],
                'available_seats' => $check['available_seats']
            ], 400);
        }

        $booking = $this->bookingRepository->create([
            'event_id' => $id,
            'email_address' => $request->email_address,
            'seats_booked' => $request->seats_booked,
            'status' => Booking::STATUS_PENDING,
        ]);

        return response()->json([
            'message' => 'Booking for the Event created successfully!',
            'booking' => $booking
        ], 201);
    }

    public function updateStatus(UpdateBookingRequest $request, int $id): JsonResponse
    {
        $booking = $this->bookingRepository->updateStatus($id, $request->status);

        if (!$booking) {
            return response()->json(['error' => 'Booking not found.'], 404);
        }

        return response()->json([
            'message' => 'Booking status updated successfully!',
            'booking' => $booking
        ], 200);
    }

    protected function checkAvailableNumberOfSeats(int $eventId, int $requestedSeats): array
    {
        $event = $this->eventRepository->find($eventId);

        if (!$event) {
            return [
                'success' => false,
                'error' => 'Event not found.',
                'available_seats' => 0
            ];
        }

        $seatsBooked = $event->bookings()->sum('seats_booked');
        $availableSeats = $event->capacity - $seatsBooked;

        if ($requestedSeats > $availableSeats) {
            return [
                'success' => false,
                'error' => 'Booking exceeds available seats.',
                'available_seats' => $availableSeats
            ];
        }

        return [
            'success' => true,
            'available_seats' => $availableSeats
        ];
    }

    public function searchByEmail(Request $request): JsonResponse
    {
        $email = $request->query('email');

        if (!$email) {
            return response()->json(['error' => 'Email query parameter is required.'], 400);
        }

        $bookings = $this->bookingRepository->searchByEmail($email);

        return response()->json($bookings);
    }
}
