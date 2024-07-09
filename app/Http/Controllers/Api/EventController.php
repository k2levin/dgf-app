<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Events\EventTicketQuantityUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Event\CreateRequest;
use App\Http\Requests\Event\DeleteRequest;
use App\Http\Requests\Event\UpdateRequest;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class EventController extends Controller
{
    public function getAllEvents(): JsonResponse
    {
        $events = Event::all();

        return response()->json($events, 200);
    }

    public function getOneEvent($id): JsonResponse
    {
        $event = Event::find($id);

        return response()->json($event, 200);
    }

    public function createEvent(CreateRequest $request): JsonResponse
    {
        Gate::allowIf(fn(User $user) => $user->isAdministrator());

        $event = Event::create([
            'name' => $request->name,
            'description' => $request->description,
            'ticket_total_quantity' => $request->ticket_total_quantity,
        ]);

        return response()->json(['message' => 'OK'], 200);
    }

    public function updateEvent(UpdateRequest $request): JsonResponse
    {
        Gate::allowIf(fn(User $user) => $user->isAdministrator());

        $updateInputs = array_filter($request->all(), function ($k) {
            return in_array($k, ['name', 'description', 'ticket_used_quantity', 'ticket_total_quantity']);
        }, ARRAY_FILTER_USE_KEY);

        Event::find($request->id)?->update($updateInputs);

        if (array_key_exists('ticket_used_quantity', $updateInputs) || array_key_exists('ticket_total_quantity', $updateInputs)) {
            $event = Event::find($request->id);
            EventTicketQuantityUpdated::dispatch($event);
        }

        return response()->json(['message' => 'OK'], 200);
    }

    public function deleteEvent(DeleteRequest $request): JsonResponse
    {
        Gate::allowIf(fn(User $user) => $user->isAdministrator());

        Event::find($request->id)?->delete();

        return response()->json(['message' => 'OK'], 200);
    }
}
