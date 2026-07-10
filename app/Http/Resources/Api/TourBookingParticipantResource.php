<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TourBookingParticipantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'participant_name' => $this->participant_name,
            'participant_phone' => $this->participant_phone,
            'participant_type' => $this->participant_type,
            'participant_type_label' => $this->participant_type === 'adult' ? 'Dewasa' : 'Anak',
            'id_number' => $this->id_number,
            'seat_number' => $this->seat_number,
            'picked_up_at' => $this->picked_up_at?->format('Y-m-d H:i:s'),
            'dropped_off_at' => $this->dropped_off_at?->format('Y-m-d H:i:s'),
            'is_picked_up' => !is_null($this->picked_up_at),
            'is_dropped_off' => !is_null($this->dropped_off_at),
        ];
    }
}