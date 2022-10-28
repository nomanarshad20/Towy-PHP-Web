<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverService extends Model
{
    use HasFactory;

    protected $table = 'driver_services';
    protected $guarded = [];
}
