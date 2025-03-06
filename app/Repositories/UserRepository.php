<?php

namespace App\Repositories;

use App\Models\User ;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserRepository
{
    /**
     * Create a new user.
     *
     * @param array $data
     * @return \App\Models\User
     */
    public function create(array $data)
    {
        return User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone_number' => $data['phone_number'],
            'api_token' => Str::random(60),
        ]);
    }

    /**
     * Find user by email.
     *
     * @param string $email
     * @return \App\Models\User|null
     */
    public function findByEmail($email)
    {
        return User::where('email', $email)->first();
    }

    /**
     * Find user by token.
     *
     * @param string $token
     * @return \App\Models\User|null
     */
    public function findByToken($token)
    {
        return User::where('api_token', $token)->first();
    }

    /**
     * Update user Stripe customer ID.
     *
     * @param \App\Models\User $user
     * @param string $customerId
     * @return \App\Models\User
     */
    public function updateStripeCustomerId(User $user, $customerId)
    {
        $user->stripe_customer_id = $customerId;
        $user->save();
        return $user;
    }
}
