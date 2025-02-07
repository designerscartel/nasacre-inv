<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SacreBookingAltResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'sacre_id' => $this->sacre_id,
            'sacre' => $this->sacre,
            'po_number' => $this->po_number,
            'date' => $this->date,
            'address' => $this->address,
            'confirmed' => $this->confirmed,
            'delegate_one_name' => $this->delegate_one_name,
            'delegate_one_email' => $this->delegate_one_email,
            'delegate_one_diet' => $this->delegate_one_diet,
            'delegate_one_free' => $this->delegate_one_free,
            'delegate_two_name' => $this->delegate_two_name,
            'delegate_two_email' => $this->delegate_two_email,
            'delegate_two_diet' => $this->delegate_two_diet,
            'delegate_two_free' => $this->delegate_two_free,
            'virtual_one_name' => $this->virtual_one_name,
            'virtual_one_email' => $this->virtual_one_email,
            'virtual_one_free' => $this->virtual_one_free,
            'virtual_two_name' => $this->virtual_two_name,
            'virtual_two_email' => $this->virtual_two_email,
            'virtual_two_free' => $this->virtual_two_free,
            'offer' => $this->offer,
            'delegates' => \App\Http\Resources\BookingDelegateResource::collection($this->delegates),
        ];

    }
}
