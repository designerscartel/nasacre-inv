<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookingSubResource extends JsonResource
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
            'sub' => $this->sub,
        ];

    }
}
