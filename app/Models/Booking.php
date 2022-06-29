<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $hidden = ['created_at', 'updated_at'];

    public function bookingDetail()
    {
        return $this->hasOne(BookingDetail::class,'booking_id');
    }

    public function passenger()
    {
        return $this->belongsTo(User::class,'passenger_id');
    }

    public function driver()
    {
        return $this->belongsTo(User::class,'driver_id');
    }

    public function frachise()
    {
        return $this->belongsTo(User::class,'franchise_id');
    }

    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class,'vehicle_type_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class,'passenger_id');
    }
}
