<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SacreInvoice extends JsonResource
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
            'sacre' => new \App\Http\Resources\SacreResource($this->sacre),
            'invoice' => new \App\Http\Resources\InvoiceResource($this->invoice),
            'po_number' => $this->po_number,
        ];

    }
}
