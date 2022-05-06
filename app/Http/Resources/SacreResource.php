<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SacreResource extends JsonResource
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
            'region_id' => $this->region_id,
            'title' => $this->title,
            'member' => $this->member,
            'address' => $this->address,
            'short_code' => $this->short_code,
            'code' => $this->code,
            'po' => $this->po,
            'finance' => $this->finance,
            'region' => new \App\Http\Resources\RegionResource($this->region),
            'contacts' => \App\Http\Resources\SacreContactResource::collection($this->contacts),
            'invoices' => \App\Http\Resources\SacreInvoiceResource::collection($this->invoices),
            'files' => \App\Http\Resources\SacreFileResource::collection($this->files),
            'shared' => \App\Http\Resources\SharedFileResource::collection($this->shared),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

    }
}
