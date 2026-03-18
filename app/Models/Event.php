<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'capacity',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function progress(): float
    {
        $bookedSeats = $this->bookings()
            ->whereIn('status', ['pending', 'confirmed'])
            ->sum('seats_booked');
        
        if ($this->capacity == 0) {
            return 0;
        }

        return round(($bookedSeats / $this->capacity) * 100, 2);
    }
}
