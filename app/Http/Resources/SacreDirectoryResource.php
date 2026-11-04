<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SacreDirectoryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'member' => $this->member,
            'past_member' => $this->past_member,
            'short_code' => $this->short_code,
            'code' => $this->code,
            'region' => new RegionResource($this->whenLoaded('region')),
        ];
    }
}
