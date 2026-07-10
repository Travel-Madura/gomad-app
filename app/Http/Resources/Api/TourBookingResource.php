<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TourBookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'booking_code' => $this->booking_code,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'status_color' => $this->status_color,
            'tour_schedule' => new TourScheduleResource($this->whenLoaded('tourSchedule')),
            'tour_package' => $this->whenLoaded('tourSchedule.tourPackage', function () {
                return [
                    'id' => $this->tourSchedule->tourPackage->id,
                    'name' => $this->tourSchedule->tourPackage->name,
                    'cover_image' => $this->tourSchedule->tourPackage->cover_image,
                ];
            }),
            'customer' => [
                'id' => $this->customer?->id,
                'name' => $this->customer?->name,
                'phone' => $this->customer?->phone,
            ],
            'origin_stop' => [
                'id' => $this->originStop?->id,
                'city_name' => $this->originStop?->city_name,
            ],
            'pickup_address' => $this->pickup_address,
            'pickup_maps_link' => $this->pickup_maps_link,
            'pickup_latitude' => $this->pickup_latitude ? (float) $this->pickup_latitude : null,
            'pickup_longitude' => $this->pickup_longitude ? (float) $this->pickup_longitude : null,
            'group_name' => $this->group_name,
            'participants_summary' => [
                'total' => $this->total_participants,
                'adults' => $this->total_adults,
                'children' => $this->total_children,
            ],
            'pricing' => [
                'base_price' => (float) $this->base_price,
                'base_price_formatted' => 'Rp ' . number_format($this->base_price, 0, ',', '.'),
                'service_fee' => (float) $this->service_fee,
                'service_fee_formatted' => 'Rp ' . number_format($this->service_fee, 0, ',', '.'),
                'platform_fee' => (float) $this->platform_fee,        // 👈 TAMBAHKAN
                'platform_fee_formatted' => 'Rp ' . number_format($this->platform_fee, 0, ',', '.'),  // 👈 TAMBAHKAN
                'discount_amount' => (float) $this->discount_amount,
                'discount_amount_formatted' => $this->discount_amount > 0 ? '-Rp ' . number_format($this->discount_amount, 0, ',', '.') : null,
                'total_price' => (float) $this->total_price,
                'total_price_formatted' => 'Rp ' . number_format($this->total_price, 0, ',', '.'),
            ],
            'participants' => TourBookingParticipantResource::collection($this->whenLoaded('participants')),
            'payment' => $this->whenLoaded('payment', function () {
                return [
                    'id' => $this->payment->id,
                    'amount' => (float) $this->payment->amount,
                    'payment_type' => $this->payment->payment_type,
                    'status' => $this->payment->status,
                    'paid_at' => $this->payment->paid_at?->format('Y-m-d H:i:s'),
                ];
            }),
            'special_requests' => $this->special_requests,
            'can_cancel' => $this->can_cancel,
            'e_ticket_url' => $this->e_ticket_url,
            'cancelled_at' => $this->cancelled_at?->format('Y-m-d H:i:s'),
            'completed_at' => $this->completed_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}