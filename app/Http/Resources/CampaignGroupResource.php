<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CampaignGroupResource extends JsonResource
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
            'title' => $this->title,
            'emails' => \App\Http\Resources\CampaignEmailResource::collection($this->emails),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

    }
}
