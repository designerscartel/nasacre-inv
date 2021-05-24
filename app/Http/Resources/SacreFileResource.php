<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SacreFileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {

        $fileYear = new \Carbon\Carbon( $this->year );
        //transforms the resource into an array made up of the attributes to be converted to JSON
        return [
            'id' => $this->id,
            'sacre_id' => $this->sacre_id,
            'filename' => $this->filename,
            'year' => $fileYear->year,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

    }
}
