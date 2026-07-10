<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourPayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tour_booking_id', 'amount', 'commission', 'agency_revenue',
        'payment_type', 'status', 'payment_method',
        'transaction_id', 'paid_at', 'expired_at', 'payment_detail',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'commission' => 'decimal:2',
            'agency_revenue' => 'decimal:2',
            'paid_at' => 'datetime',
            'expired_at' => 'datetime',
            'payment_detail' => 'json',
        ];
    }

    public function tourBooking(): BelongsTo
    {
        return $this->belongsTo(TourBooking::class);
    }
}