<?php

namespace App\Http\Requests\Card;

use Illuminate\Foundation\Http\FormRequest;

class CardChargeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'card_id' => 'required|integer|exists:user_cards,id',
            'amount' => 'required|numeric|min:0.5',
            'currency' => 'sometimes|string|size:3',
            'description' => 'sometimes|string',
        ];
    }
}
