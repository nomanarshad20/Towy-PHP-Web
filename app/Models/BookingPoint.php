<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingPoint extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function booking()
    {
        return $this-$this->belongsTo(Booking::class,'booking_id');
    }
}
