<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookingDelegateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {

        //transforms the resource into an array made up of the attributes to be converted to JSON
        return [
            'id' => $this->id,
            'booking_id' => $this->booking_id,
            'scare_id' => $this->scare_id,
            'name' => $this->name,
            'email' => $this->email,
            'diet' => $this->diet
        ];

    }
}
