<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentalPromoUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'rental_promo_id', 'user_id', 'rental_booking_id', 'discount_amount',
    ];

    protected function casts(): array
    {
        return ['discount_amount' => 'decimal:2'];
    }

    public function rentalPromo(): BelongsTo
    {
        return $this->belongsTo(RentalPromo::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function rentalBooking(): BelongsTo
    {
        return $this->belongsTo(RentalBooking::class);
    }
}