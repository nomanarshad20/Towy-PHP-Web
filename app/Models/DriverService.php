<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverService extends Model
{
    use HasFactory;

    protected $table = 'driver_services';
    protected $guarded = [];

    public function service(){
        return $this->belongsTo(Service::class,'service_id');
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
}
