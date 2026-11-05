<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PlaceOfInterestResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'is_place_of_interest' => $this->is_place_of_interest,
            'is_school_speaker' => $this->is_school_speaker,
            'religion' => $this->whenLoaded('religion', fn () => $this->religion?->name),
            'contact_name' => $this->contact_name,
            'organisation_email' => $this->organisation_email,
            'phone' => $this->phone,
            'website' => $this->website,
            'regions' => $this->whenLoaded('regions', fn () => $this->regions->map(fn ($region) => [
                'id' => $region->id,
                'title' => $region->title,
            ])->values()),
        ];
    }
}
