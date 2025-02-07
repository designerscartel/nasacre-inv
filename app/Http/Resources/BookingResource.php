<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
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
            'email' => $this->email,
            'subscribed' => $this->subscribed,
            'none_subscribed' => $this->none_subscribed,
            'additional_amount' => $this->additional_amount,
            'date' => $this->date,
            'year' => $this->year,
            'from' => $this->from,
            'message' => $this->message,
            'venue' => $this->venue,
            'new' => $this->new,
            'bookings' => \App\Http\Resources\SacreBookingAltResource::collection($this->bookings),
            'subs' => \App\Http\Resources\BookingSubResource::collection($this->subs),
            'memberSubs' => \App\Http\Resources\BookingMemberSubResource::collection($this->memberSubs),
            'delegates' => \App\Http\Resources\BookingDelegateResource::collection($this->delegates),
        ];

    }
}
