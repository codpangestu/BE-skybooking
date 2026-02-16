<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            'code' => $this->code,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'number_of_passengers' => $this->number_of_passengers,
            'payment_status' => $this->payment_status,
            'status' => $this->payment_status === 'paid' ? 'success' : $this->payment_status,
            'payment_method' => $this->payment_method,
            'subtotal' => $this->subtotal,
            'grandtotal' => $this->grandtotal,
            'total_price' => $this->grandtotal, // Map for frontend convenience
            'created_at' => $this->created_at,

            // Nested relations using standardized resources
            'flight' => new FlightResource($this->whenLoaded('flight')),
            'flight_class' => new FlightClassResource($this->whenLoaded('flightClass')),
            'passengers' => $this->whenLoaded('passengers'),
        ];
    }
}
