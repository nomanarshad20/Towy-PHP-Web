<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingRating extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'booking_ratings';

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function giver()
    {
        return $this->belongsTo(User::class, 'giver_id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }
}
