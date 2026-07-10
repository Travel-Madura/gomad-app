<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourBookingParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'tour_booking_id', 'participant_name', 'participant_phone',
        'participant_type', 'id_number', 'seat_number',
        'picked_up_at', 'dropped_off_at',
    ];

    protected function casts(): array
    {
        return [
            'seat_number' => 'integer',
            'picked_up_at' => 'datetime',
            'dropped_off_at' => 'datetime',
        ];
    }

    public function tourBooking(): BelongsTo
    {
        return $this->belongsTo(TourBooking::class);
    }
}