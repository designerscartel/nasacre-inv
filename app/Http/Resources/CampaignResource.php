<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CampaignResource extends JsonResource
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
            'campaign_group_id' => $this->campaign_group_id,
            'group' => new \App\Http\Resources\CampaignGroupResource($this->group),
            'email' => $this->email,
            'subject' => $this->subject,
            'url' => $this->url,
            'message' => $this->message,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

    }
}
