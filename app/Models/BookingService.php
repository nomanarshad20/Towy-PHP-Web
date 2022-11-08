<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingService extends Model
{
    use HasFactory;

    protected $table = 'booking_services';

    protected $guarded = [];

    public function service()
    {
        return $this->belongsTo(Service::class,'service_id');
    }
}
