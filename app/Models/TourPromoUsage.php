<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourPromoUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'tour_promo_id',
        'user_id',
        'tour_booking_id',
        'discount_amount',
    ];

    protected function casts(): array
    {
        return [
            'discount_amount' => 'decimal:2',
        ];
    }

    public function tourPromo(): BelongsTo
    {
        return $this->belongsTo(TourPromo::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tourBooking(): BelongsTo
    {
        return $this->belongsTo(TourBooking::class);
    }
}