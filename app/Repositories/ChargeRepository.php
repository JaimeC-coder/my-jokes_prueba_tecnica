<?php
namespace App\Repositories;

use App\Models\Charges;

class ChargeRepository
{
    /**
     * Create a new charge.
     *
     * @param array $data
     * @return \App\Models\Charge
     */
    public function create(array $data)
    {
        return Charges::create([
            'user_id' => $data['user_id'],
            'user_card_id' => $data['user_card_id'],
            'stripe_charge_id' => $data['stripe_charge_id'],
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? 'usd',
            'status' => $data['status'],
            'description' => $data['description'] ?? null,
        ]);
    }
}
