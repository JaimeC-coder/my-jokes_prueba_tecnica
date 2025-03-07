<?php

namespace App\Repositories;

use App\Models\UserCard;
use Stripe\Issuing\Card;
use App\Http\Resources\Card\CardResourse;

class CardRepository
{
    /**
     * Create a new card for a user.
     *
     * @param array $data
     * @return \App\Models\UserCard
     */
    public function create(array $data)
    {
        // If this is the first card, make it default
        $isDefault = !UserCard::where('user_id', $data['user_id'])->exists();

        return UserCard::create([
            'user_id' => $data['user_id'],
            'stripe_payment_method_id' => $data['stripe_payment_method_id'],
            'last_four' => $data['last_four'],
            'brand' => $data['brand'],
            'exp_month' => $data['exp_month'],
            'exp_year' => $data['exp_year'],
            'is_default' => $isDefault,
        ]);
    }

    /**
     * Get cards for a user.
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserCards($userId)
    {
        return UserCard::where('user_id', $userId)->get();
    }

    /**
     * Find card by ID and user ID.
     *
     * @param int $cardId
     * @param int $userId
     * @return \App\Models\UserCard|null
     */
    public function findByIdAndUser($cardId, $userId)
    {
        return UserCard::where('id', $cardId)
            ->where('user_id', $userId)
            ->first();
    }
}
