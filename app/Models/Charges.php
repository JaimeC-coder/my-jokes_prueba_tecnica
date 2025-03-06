<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Charges extends Model
{
    //
    protected $fillable = [
        'user_id', 'user_card_id', 'stripe_charge_id', 'amount', 'currency', 'status', 'description'
    ];

    /**
     * Get the user that owns the charge.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the card that was used for the charge.
     */
    public function card()
    {
        return $this->belongsTo(UserCard::class, 'user_card_id');
    }
}
