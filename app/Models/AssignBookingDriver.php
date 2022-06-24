<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignBookingDriver extends Model
{
    use HasFactory;

    protected $table = 'assign_booking_drivers';

    protected $guarded = [];

    public function driver()
    {
        return $this->belongsTo(User::class,'driver_id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class,'booking_id');
    }
}
