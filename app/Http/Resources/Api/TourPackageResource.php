<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TourPackageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'agency' => [
                'id' => $this->agency?->id,
                'name' => $this->agency?->agency_name,
                'slug' => $this->agency?->slug,
                'logo' => $this->agency?->logo ?? null,
                'is_verified' => $this->agency?->is_verified,
                'rating' => (float) ($this->agency?->rating ?? 0),
            ],
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'duration' => [
                'days' => $this->duration_days,
                'nights' => $this->duration_nights,
                'label' => $this->duration_days . ' Hari ' . $this->duration_nights . ' Malam',
            ],
            'itinerary' => $this->when($this->itinerary, $this->itinerary),
            'includes' => $this->when($this->includes, $this->includes),
            'excludes' => $this->when($this->excludes, $this->excludes),
            'cover_image' => $this->cover_image ?? null,
            'gallery' => $this->when($this->gallery, function () {
                return collect($this->gallery)->values()->toArray();
            }),
            'is_active' => $this->is_active,
            'stops' => TourRouteStopResource::collection($this->whenLoaded('stops')),
            'schedules' => TourScheduleResource::collection($this->whenLoaded('schedules')),
            'upcoming_schedules_count' => $this->when(isset($this->upcoming_schedules_count), $this->upcoming_schedules_count),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}