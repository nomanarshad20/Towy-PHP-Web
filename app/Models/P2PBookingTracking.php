<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class P2PBookingTracking extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'p2p_booking_tracking';
}
