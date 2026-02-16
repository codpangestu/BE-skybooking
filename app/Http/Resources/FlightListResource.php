<?php

// namespace App\Http\Resources;

// use Illuminate\Http\Resources\Json\JsonResource;

// class FlightListResource extends JsonResource
// {
//     public function toArray($request)
//     {
//         $departure = $this->segments->where('sequence', 1)->first();
//         $arrival = $this->segments->sortByDesc('sequence')->first();
//         $cheapest = $this->classes->sortBy('price')->first();

//         return [
//             'id' => $this->id,
//             'flight_number' => $this->flight_number,
//             'airline_name' => $this->airline->name,
//             'airline_logo' => $this->airline->logo,
//             'departure_time' => $departure?->time,
//             'arrival_time' => $arrival?->time,
//             'origin_airport_code' => $departure?->airport->iata_code,
//             'destination_airport_code' => $arrival?->airport->iata_code,
//             'base_price' => $cheapest?->price,
//             'facilities' => $cheapest?->facilities->pluck('name'),
//             'stops' => max(0, $this->segments->count() - 2),
//             'duration' => '—',
//         ];
//     }
// }
