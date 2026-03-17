<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'event_id',
        'email_address',
        'seats_booked',
        'status',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
