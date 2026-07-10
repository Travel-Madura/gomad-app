<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TourRouteStopResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tour_package_id' => $this->tour_package_id,
            'city_name' => $this->city_name,
            'stop_order' => $this->stop_order,
            'latitude' => $this->latitude ? (float) $this->latitude : null,
            'longitude' => $this->longitude ? (float) $this->longitude : null,
            'is_pickup_available' => $this->is_pickup_available,
            'is_dropoff_available' => $this->is_dropoff_available,
            'estimated_arrival' => $this->estimated_arrival,
            'notes' => $this->notes,
        ];
    }
}