<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FlightClassResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'class_type' => $this->class_type,
            'price' => $this->price,
            'total_seats' => $this->total_seats,
            'facilities' => FacilityResource::collection($this->facilities),
        ];
    }
}
