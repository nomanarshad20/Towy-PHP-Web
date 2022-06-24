<?php

namespace App\Models;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laratrust\Traits\LaratrustUserTrait;
use App\Notifications\ForgotPasswordNotificationAdmin;

class User extends Authenticatable
{
    use LaratrustUserTrait;
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function sendPasswordResetNotificationAdmin($token)
    {
        $this->notify(new ForgotPasswordNotificationAdmin($token));
    }

    public function driver()
    {
        return $this->hasOne(Driver::class);
    }

    public function franchise()
    {
        return $this->hasOne(Franchise::class);
    }

    public function driverCoordinate()
    {
        return $this->hasOne(DriversCoordinate::class,'driver_id');
    }

    public function transactions()
    {
        return $this->hasMany(UserWallet::class);
    }

    public function validTransactions()
    {
        return $this->transactions()->where('status', 1);
    }

    public function credit()
    {
        return $this->validTransactions()
            ->where('type', 'credit')
            ->sum('amount');
    }

    public function debit()
    {
        return $this->validTransactions()
            ->where('type', 'debit')
            ->sum('amount');
    }

    public function balance()
    {
        return $this->credit() - $this->debit();
    }

    /*public function allowWithdraw($amount) : bool
    {
        return $this->balance() >= $amount;
    }*/

}
