<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TourScheduleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tour_package_id' => $this->tour_package_id,
            'tour_package_name' => $this->whenLoaded('tourPackage', fn() => $this->tourPackage->name),
            'vehicle' => [
                'id' => $this->vehicle?->id,
                'plate_number' => $this->vehicle?->plate_number,
                'brand' => $this->vehicle?->brand,
                'model' => $this->vehicle?->model,
                'capacity' => $this->vehicle?->capacity,
            ],
            'driver' => $this->when($this->driver_id, [
                'id' => $this->driver?->id,
                'name' => $this->driver?->name,
                'phone' => $this->driver?->phone,
            ]),
            'departure_date' => $this->departure_date->format('Y-m-d'),
            'departure_date_formatted' => $this->departure_date->format('d M Y'),
            'departure_time' => $this->departure_time,
            'return_date' => $this->return_date?->format('Y-m-d'),
            'return_date_formatted' => $this->return_date?->format('d M Y'),
            'return_time' => $this->return_time,
            'pricing' => [
                'base_price' => (float) $this->base_price,
                'base_price_formatted' => 'Rp ' . number_format($this->base_price, 0, ',', '.'),
                'child_price' => $this->child_price ? (float) $this->child_price : null,
                'child_price_formatted' => $this->child_price ? 'Rp ' . number_format($this->child_price, 0, ',', '.') : null,
            ],
            'capacity' => [
                'max_participants' => $this->max_participants,
                'min_participants' => $this->min_participants,
                'available_seats' => $this->available_seats ?? $this->max_participants,
                'is_full' => $this->is_full ?? false,
                'occupancy_rate' => $this->occupancy_rate ?? 0,
            ],
            'pickup_zones' => $this->pickup_zones,
            'is_active' => $this->is_active,
            'started_at' => $this->started_at?->format('Y-m-d H:i:s'),
            'finished_at' => $this->finished_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}