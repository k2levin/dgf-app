<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BookingTicketTest extends TestCase
{
    // to test booking ticket successfully
    public function testBookingTicketSuccess(): void
    {
        $this->artisan('migrate:refresh --seed');

        $event = Event::first();

        $this->assertDatabaseCount('tickets', 1);
        $this->assertDatabaseHas('events', [
            'ticket_used_quantity' => 1,
            'ticket_total_quantity' => 10,
        ]);

        $user = User::where('role', User::ROLE_USER)->first();
        auth()->login($user);

        $response = $this->post('/api/ticket/booking-ticket', [
            'event_id' => $event->id,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'OK');

        $this->assertDatabaseCount('tickets', 2);
        $this->assertDatabaseHas('events', [
            'ticket_used_quantity' => 2,
            'ticket_total_quantity' => 10,
        ]);
    }

    // to test that "ticket_used_quantity" will not more than "ticket_total_quantity"
    public function testBookingTicketByPreventOverBooking(): void
    {
        $this->artisan('migrate:refresh --seed');

        $event = Event::first();
        $event->ticket_total_quantity = 1;
        $event->save();

        $this->assertDatabaseCount('tickets', 1);
        $this->assertDatabaseHas('events', [
            'ticket_used_quantity' => 1,
            'ticket_total_quantity' => 1,
        ]);

        $user = User::where('role', User::ROLE_USER)->first();
        auth()->login($user);

        $response = $this->post('/api/ticket/booking-ticket', [
            'event_id' => $event->id,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Out of stock.');

        $this->assertDatabaseCount('tickets', 1);
        $this->assertDatabaseHas('events', [
            'ticket_used_quantity' => 1,
            'ticket_total_quantity' => 1,
        ]);
    }

    // to test that handle concurrent booking
    public function testBookingTicketByHandleConcurrentBooking(): void
    {
        $this->artisan('migrate:refresh --seed');

        $event = Event::first();
        $event->ticket_total_quantity = 2;
        $event->save();

        $this->assertDatabaseCount('tickets', 1);
        $this->assertDatabaseHas('events', [
            'ticket_used_quantity' => 1,
            'ticket_total_quantity' => 2,
        ]);

        $event = Event::first();

        $userA = User::where('role', User::ROLE_USER)->orderBy('id')->first();
        $loginResponse = Http::acceptJson()->post(config('app.url').'/api/auth/login', [
            'email' => $userA->email,
            'password' => 'testing123',
        ]);
        $userAToken = $loginResponse->json()['token'];

        $userB = User::where('role', User::ROLE_USER)->orderByDesc('id')->first();
        $loginResponse = Http::acceptJson()->post(config('app.url').'/api/auth/login', [
            'email' => $userB->email,
            'password' => 'testing123',
        ]);
        $userBToken = $loginResponse->json()['token'];

        $responses = Http::pool(fn(Pool $pool) => [
            $pool->acceptJson()->withToken($userAToken)->post(config('app.url').'/api/ticket/booking-ticket', ['event_id' => $event->id, 'x_delay_for_testing_only' => 3]),
            $pool->acceptJson()->withToken($userBToken)->post(config('app.url').'/api/ticket/booking-ticket', ['event_id' => $event->id, 'x_delay_for_testing_only' => 1]),
        ]);

        $this->assertEquals(200, $responses[0]->status());
        $this->assertEquals('Server busy, please try again later.', $responses[0]->json('message'));

        $this->assertEquals(200, $responses[1]->status());
        $this->assertEquals('OK', $responses[1]->json('message'));

        $this->assertDatabaseCount('tickets', 2);
        $this->assertDatabaseHas('events', [
            'ticket_used_quantity' => 2,
            'ticket_total_quantity' => 2,
        ]);
    }
}
