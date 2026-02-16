<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FlightResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $segments = $this->segments->sortBy('sequence');
        $departure = $segments->first();
        $arrival = $segments->last();

        return [
            'id' => $this->id,
            'flight_number' => $this->flight_number,
            'airline_name' => $this->airline->name ?? 'Unknown Airline',
            'airline_logo' => $this->airline->logo ?? null,
            'airline' => $this->airline,

            // Times and Cities
            'departure_time' => $departure?->time,
            'arrival_time' => $arrival?->time,
            'origin_city' => $departure?->airport->city,
            'origin_airport_name' => $departure?->airport->name,
            'origin_airport_code' => $departure?->airport->iata_code,
            'destination_city' => $arrival?->airport->city,
            'destination_airport_name' => $arrival?->airport->name,
            'destination_airport_code' => $arrival?->airport->iata_code,

            'duration' => $this->duration ?? '2h 30m',
            'stops' => max(0, $segments->count() - 2) == 0 ? 'Direct' : (max(0, $segments->count() - 2) . ' Stop(s)'),

            // Nested data using specialized resources
            'segments' => $this->segments,
            'classes' => FlightClassResource::collection($this->whenLoaded('classes')),
            'seats' => FlightSeatResource::collection($this->whenLoaded('seats')),

            // Facilites from first class if available for summary
            'facilities' => $this->classes->first()?->facilities->pluck('name') ?? [],
            'base_price' => $this->classes->min('price') ?? 0,
        ];
    }
}
