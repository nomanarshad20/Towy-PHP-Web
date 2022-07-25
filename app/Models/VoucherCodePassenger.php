<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherCodePassenger extends Model
{
    use HasFactory;

    protected $table = 'voucher_code_passengers';

    protected $guarded = [];

    public function voucherCode()
    {
        return $this->belongsTo(VoucherCode::class,'voucher_code_id');
    }

    public function passenger()
    {
        return $this->belongsTo(User::class,'passenger_id');
    }
}
