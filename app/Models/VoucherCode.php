<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherCode extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'voucher_codes';

    public function voucherCodePassenger()
    {
        return $this->hasMany(VoucherCodePassenger::class,'voucher_code_id');
    }

}
