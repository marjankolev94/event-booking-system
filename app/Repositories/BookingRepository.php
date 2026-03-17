<?php

namespace App\Repositories;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Interfaces\BookingRepositoryInterface;

class BookingRepository implements BookingRepositoryInterface
{
    public function allByEvent(int $eventId): Collection
    {
        return Booking::where('event_id', $eventId)->get();
    }

    public function create(array $data): Booking
    {
        return Booking::create($data);
    }

    public function updateStatus(int $id, string $status): ?Booking
    {
        $booking = Booking::find($id);
        if (!$booking) return null;

        $booking->status = $status;
        $booking->save();

        return $booking;
    }

    public function searchByEmail(string $email): Collection
    {
        return Booking::where('email_address', 'like', "%{$email}%")->get();
    }
}