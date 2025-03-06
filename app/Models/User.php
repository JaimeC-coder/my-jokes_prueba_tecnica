<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone_number',
        'stripe_customer_id',
        'api_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'api_token',
        'stripe_customer_id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the cards for the user.
     */
    public function cards()
    {
        return $this->hasMany(UserCard::class);
    }

    /**
     * Get the charges for the user.
     */
    public function charges()
    {
        return $this->hasMany(Charges::class);
    }

    /**
     * Check if user has any cards.
     */
    public function hasCards()
    {
        return $this->cards()->exists();
    }
}
