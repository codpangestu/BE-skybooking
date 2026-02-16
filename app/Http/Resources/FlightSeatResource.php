<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FlightSeatResource extends JsonResource
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
            'name' => $this->name ?? ($this->row . $this->column),
            'row' => $this->row,
            'column' => $this->column,
            'status' => $this->is_available ? 'available' : 'booked',
            'is_available' => (bool)$this->is_available,
            'class_type' => $this->class_type,
        ];
    }
}
