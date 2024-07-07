<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use App\Models\Ticket;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin A',
            'email' => 'admin-a@email.com',
            'password' => Hash::make('testing123'),
            'role' => '1',
        ]);

        $user1 = User::create([
            'name' => 'User A',
            'email' => 'user-a@email.com',
            'password' => Hash::make('testing123'),
            'role' => '2',
        ]);

        $user2 = User::create([
            'name' => 'User B',
            'email' => 'user-b@email.com',
            'password' => Hash::make('testing123'),
            'role' => '2',
        ]);

        $event = Event::create([
            'name' => 'Andy Lau Concert',
            'description' => "This World Tour offers a breathtaking new production, meticulously crafted by an experienced world-class production team. Featuring state-of-the-art lighting technology and mesmerizing visual effects, this concert promises an unparalleled audiovisual feast with impeccable attention to detail, providing audiences with the ultimate performance experience",
            'ticket_used_quantity' => 0,
            'ticket_total_quantity' => 10,
            'version' => 0,
        ]);

        $ticket = Ticket::create([
            'serial_number' => 'ABCDEFGXYZ',
            'booked_by_user_id' => $user1->id,
            'event_id' => $event->id,
        ]);

        $event->ticket_used_quantity++;
        $event->save();
    }
}
