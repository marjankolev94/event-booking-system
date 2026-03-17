<?php

namespace App\Repositories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Interfaces\EventRepositoryInterface;

class EventRepository implements EventRepositoryInterface
{
    public function paginate(int $perPage = 10)
    {
        return Event::paginate($perPage);
    }

    public function find(int $id): ?Event
    {
        return Event::find($id);
    }

    public function create(array $data): Event
    {
        return Event::create($data);
    }

    public function delete(int $id): bool
    {
        $event = Event::find($id);

        if (!$event) {
            return false;
        }

        return $event->delete();
    }
}