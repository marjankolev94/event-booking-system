<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Repositories\Interfaces\EventRepositoryInterface;
use App\Http\Requests\EventRequest;
use Illuminate\Http\JsonResponse;


class EventController extends Controller
{
    protected $eventRepository;

    public function __construct(EventRepositoryInterface $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function index(): JsonResponse
    {
        $events = $this->eventRepository->paginate(5);

        return response()->json($events);
    }

    public function show(int $id): JsonResponse
    {
        $event = $this->eventRepository->find($id);

        if (!$event) {
            return response()->json(['error' => 'Event not found'], 404);
        }

        return response()->json($event);
    }

    public function store(EventRequest $request)
    {
        $event = $this->eventRepository->create([
            'name' => $request->name,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'capacity' => $request->capacity
        ]);

        return response()->json(['message' => 'Event created successfully!', 'event' => $event], 201);
    }

    public function destroy(int $id): JsonResponse
    {
        $event = $this->eventRepository->find($id);

        if (!$event) {
            return response()->json(['error' => 'Event not found'], 404);
        }
        
        $this->eventRepository->delete($id);

        return response()->json(['message' => 'Event deleted successfully!'], 200);
    }

    public function progress(int $id): JsonResponse
    {
        $event = $this->eventRepository->find($id);

        return response()->json([
            'event_id' => $event->id,
            'event_name' => $event->name,
            'progress' => $event->progress() . ' %'
        ]);
    }


}
