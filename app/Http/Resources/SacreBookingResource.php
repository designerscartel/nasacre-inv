<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SacreBookingResource extends JsonResource
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
            'sacre_id' => $this->sacre_id,
            'booking' => new \App\Http\Resources\BookingResource($this->booking),
            'po_number' => $this->po_number,
            'date' => $this->date,
            'address' => $this->address,
            'delegate_one_name' => $this->delegate_one_name,
            'delegate_one_email' => $this->delegate_one_email,
            'delegate_two_name' => $this->delegate_two_name,
            'delegate_two_email' => $this->delegate_two_email,
            'virtual_one_name' => $this->virtual_one_name,
            'virtual_one_email' => $this->virtual_one_email,
            'virtual_two_name' => $this->virtual_two_name,
            'virtual_two_email' => $this->virtual_two_email
        ];

    }
}
