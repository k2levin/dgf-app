<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'ticket_used_quantity',
        'ticket_total_quantity',
        'version',
    ];

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    // using Optimistic Locking to handle booking conflict
    // reference: https://stackoverflow.com/a/58952004
    public function incrementTicketUsedQuantity(Ticket $ticket): void
    {
        $v1 = $this->version;

        if (request()->has('x_delay_for_testing_only') && request()->x_delay_for_testing_only > 0) {
            sleep(request()->x_delay_for_testing_only);
        }

        $this->refresh(); // get the latest record

        $v2 = $this->version;

        if ($v1 !== $v2) {
            $ticket->delete();
            abort(response()->json(['message' => 'Server busy, please try again later.'], 200));
        }

        $this->ticket_used_quantity++;
        $this->version++;
        $this->save();
    }
}
