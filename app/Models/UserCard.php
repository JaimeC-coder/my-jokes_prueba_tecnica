<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCard extends Model
{
    //
    protected $fillable = [
        'user_id',
        'stripe_payment_method_id',
        'last_four',
        'brand',
        'exp_month',
        'exp_year',
        'is_default'
    ];

    /**
     * Get the user that owns the card.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the charges for the card.
     */
    public function charges()
    {
        return $this->hasMany(Charges::class);
    }
}
