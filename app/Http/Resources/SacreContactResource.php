<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SacreContactResource extends JsonResource
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
            'position_id' => $this->position_id,
            'position' => new \App\Http\Resources\ContactPositionResource($this->position),
            'name' => $this->name,
            'email' => $this->email,
            'tel' => $this->tel,
            'role_dscpn' => $this->role_dscpn,
            'default' => $this->default,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

    }
}
