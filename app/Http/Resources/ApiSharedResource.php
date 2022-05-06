<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ApiSharedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'sacre_id' => $this->sacre_id,
            'filename' => $this->filename,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'sacre' => $this->sacre,
        ];

    }
}
