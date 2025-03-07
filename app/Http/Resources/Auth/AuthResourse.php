<?php

namespace App\Http\Resources\Auth;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResourse extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'created_at' => Carbon::parse($this->created_at)->diffForHumans(),
        ];
    }
}
