<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResendRequest extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'resend_requests';

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
