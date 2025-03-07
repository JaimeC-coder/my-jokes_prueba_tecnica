<?php

namespace App\Http\Resources\Card;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class CardResourse extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) : array
    {

        return [
            'id' => $this->id,
            'last_four' => $this->last_four,
            'brand' => $this->brand,
            'exp_month' => $this->exp_month,
            'exp_year' => $this->exp_year,
            'is_default' => $this->is_default,
            'created_at' => Carbon::parse($this->created_at)->diffForHumans()
        ];
    }
}
