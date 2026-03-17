<?php

namespace App\Repositories\Interfaces;

use App\Models\Booking;

interface BookingRepositoryInterface
{
    public function allByEvent(int $eventId);
    public function create(array $data): Booking;
    public function updateStatus(int $id, string $status): ?Booking;
}