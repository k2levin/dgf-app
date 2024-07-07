<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ticket\BookingTicketRequest;
use App\Http\Requests\Ticket\CreateRequest;
use App\Http\Requests\Ticket\DeleteRequest;
use App\Http\Requests\Ticket\UpdateRequest;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class TicketController extends Controller
{
    public function getAllTickets($eventId): JsonResponse
    {
        $tickets = Ticket::where('event_id', $eventId)->get();

        return response()->json($tickets, 200);
    }

    public function getOneTicket($id): JsonResponse
    {
        $ticket = Ticket::find($id);

        return response()->json($ticket, 200);
    }

    public function createTicket(CreateRequest $request): JsonResponse
    {
        Gate::allowIf(fn(User $user) => $user->isAdministrator());

        $ticket = Ticket::create([
            'serial_number' => Ticket::generateSerialNumber(),
            'booked_by_user_id' => $request->booked_by_user_id,
            'event_id' => $request->event_id,
        ]);

        return response()->json(['message' => 'OK'], 200);
    }

    public function updateTicket(UpdateRequest $request): JsonResponse
    {
        Gate::allowIf(fn(User $user) => $user->isAdministrator());

        $updateInputs = array_filter($request->all(), function ($k) {
            return in_array($k, ['serial_number', 'booked_by_user_id', 'event_id']);
        }, ARRAY_FILTER_USE_KEY);

        Ticket::find($request->id)?->update($updateInputs);

        return response()->json(['message' => 'OK'], 200);
    }

    public function deleteTicket(DeleteRequest $request): JsonResponse
    {
        Gate::allowIf(fn(User $user) => $user->isAdministrator());

        Ticket::find($request->id)?->delete();

        return response()->json(['message' => 'OK'], 200);
    }

    public function bookingTicket(BookingTicketRequest $request): JsonResponse
    {
        $event = Event::where('id', $request->event_id)->first();

        if ($event->ticket_used_quantity >= $event->ticket_total_quantity) {
            abort(response()->json(['message' => 'Out of stock.'], 200));
        }

        $ticket = $event->tickets()->create([
            'serial_number' => Ticket::generateSerialNumber(),
            'booked_by_user_id' => auth()->user()->id,
        ]);

        $event->incrementTicketUsedQuantity($ticket);

        return response()->json(['message' => 'OK'], 200);
    }
}
