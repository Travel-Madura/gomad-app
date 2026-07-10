<?php

namespace App\Models;

use App\Enums\RentalBookingStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Casts\Attribute;

class RentalBooking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_code', 'vehicle_id', 'customer_id',
        'purpose', 'start_date', 'end_date', 'total_days',
        'pickup_address', 'pickup_latitude', 'pickup_longitude',
        'destination_address', 'destination_latitude', 'destination_longitude',
        'estimated_distance_km', 'include_driver', 'max_passengers',
        'total_price', 'base_price', 'extra_days_price',
        'driver_price', 'service_fee', 'platform_fee', 'discount_amount',
        'special_notes', 'status',
        'cancelled_at', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'total_days' => 'integer',
            'pickup_latitude' => 'decimal:7',
            'pickup_longitude' => 'decimal:7',
            'destination_latitude' => 'decimal:7',
            'destination_longitude' => 'decimal:7',
            'estimated_distance_km' => 'decimal:2',
            'include_driver' => 'boolean',
            'max_passengers' => 'integer',
            'total_price' => 'decimal:2',
            'base_price' => 'decimal:2',
            'extra_days_price' => 'decimal:2',
            'driver_price' => 'decimal:2',
            'service_fee' => 'decimal:2',
            'platform_fee' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'cancelled_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => RentalBookingStatus::tryFrom($this->status)?->label() ?? $this->status,
        );
    }

    protected function statusColor(): Attribute
    {
        return Attribute::make(
            get: fn () => RentalBookingStatus::tryFrom($this->status)?->color() ?? 'gray',
        );
    }

    // Update accessor cancellationFee()
    protected function cancellationFee(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: function () {
                if ($this->status !== 'paid') return 0;

                $startDateTime = $this->start_date->setTime(0, 0, 0);
                $hoursUntilStart = now()->diffInHours($startDateTime, false);

                $hours10 = (int) \App\Models\PlatformSetting::getValue('rental_cancel_hours_10', 168);
                $hours25 = (int) \App\Models\PlatformSetting::getValue('rental_cancel_hours_25', 72);
                $hours50 = (int) \App\Models\PlatformSetting::getValue('rental_cancel_hours_50', 24);

                // < 24 jam = 100% fee (tidak bisa refund)
                if ($hoursUntilStart <= $hours50) {
                    return (int) $this->total_price;
                }

                // 1-3 hari = 50%
                if ($hoursUntilStart <= $hours25) {
                    $pct = (float) \App\Models\PlatformSetting::getValue('rental_cancel_fee_50_percent', 50);
                    return (int) round($this->total_price * ($pct / 100));
                }

                // 3-7 hari = 25%
                if ($hoursUntilStart <= $hours10) {
                    $pct = (float) \App\Models\PlatformSetting::getValue('rental_cancel_fee_25_percent', 25);
                    return (int) round($this->total_price * ($pct / 100));
                }

                // > 7 hari = 10%
                $pct = (float) \App\Models\PlatformSetting::getValue('rental_cancel_fee_10_percent', 10);
                return (int) round($this->total_price * ($pct / 100));
            },
        );
    }

    // Update accessor canCancel()
    protected function canCancel(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: function () {
                if (in_array($this->status, ['cancelled', 'completed', 'on_going'])) return false;
                if (in_array($this->status, ['pending', 'confirmed'])) return true;
                if ($this->status === 'paid') {
                    $hours50 = (int) \App\Models\PlatformSetting::getValue('rental_cancel_hours_50', 24);
                    $startDateTime = $this->start_date->setTime(0, 0, 0);
                    return now()->diffInHours($startDateTime, false) > $hours50;
                }
                return false;
            },
        );
    }

    protected function cancellationRefund(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: function () {
                return max(0, (int) $this->total_price - $this->cancellation_fee);
            },
        );
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(RentalPayment::class);
    }
}