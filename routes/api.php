<?php

Route::post('auth/register', [\App\Http\Controllers\Api\AuthController::class, 'register'])->name('auth.register');
Route::post('auth/login', [\App\Http\Controllers\Api\AuthController::class, 'login'])->name('auth.login');

Route::get('events', [\App\Http\Controllers\Api\EventController::class, 'getAllEvents'])->name('event.get.all.events');
Route::get('event/{id}', [\App\Http\Controllers\Api\EventController::class, 'getOneEvent'])->name('event.get.one.event');
Route::get('ticket/{id}', [\App\Http\Controllers\Api\TicketController::class, 'getOneTicket'])->name('ticket.get.one.ticket');
Route::get('event/{event_id}/tickets', [\App\Http\Controllers\Api\TicketController::class, 'getAllTickets'])->name('ticket.get.all.tickets');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('event', [\App\Http\Controllers\Api\EventController::class, 'createEvent'])->name('event.create.event');
    Route::put('event', [\App\Http\Controllers\Api\EventController::class, 'updateEvent'])->name('event.update.event');
    Route::delete('event', [\App\Http\Controllers\Api\EventController::class, 'deleteEvent'])->name('event.delete.event');

    Route::post('ticket', [\App\Http\Controllers\Api\TicketController::class, 'createTicket'])->name('ticket.create.ticket');
    Route::put('ticket', [\App\Http\Controllers\Api\TicketController::class, 'updateTicket'])->name('ticket.update.ticket');
    Route::delete('ticket', [\App\Http\Controllers\Api\TicketController::class, 'deleteTicket'])->name('ticket.delete.ticket');

    Route::post('ticket/booking-ticket', [\App\Http\Controllers\Api\TicketController::class, 'bookingTicket'])->name('ticket.booking.ticket');
});
