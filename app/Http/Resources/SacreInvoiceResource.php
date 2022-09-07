<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SacreInvoiceResource extends JsonResource
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
            'batch' => $this->batch,
            'invoice' => new \App\Http\Resources\InvoiceResource($this->invoice),
            'po_number' => $this->po_number,
            'virtual_training' => $this->virtual_training,
            'date' => $this->date,
            'address' => $this->address
        ];

    }
}
