<?php

namespace App\Models;

use App\Enums\TourBookingStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Casts\Attribute;

class TourBooking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_code', 'tour_schedule_id', 'customer_id',
        'origin_stop_id', 'pickup_address', 'pickup_maps_link',
        'pickup_latitude', 'pickup_longitude',
        'group_name', 'total_participants',
        'total_adults', 'total_children',
        'total_price', 'base_price', 'discount_amount',
        'service_fee', 'platform_fee',
        'special_requests', 'status', 'e_ticket_url',
        'cancelled_at', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'total_participants' => 'integer',
            'total_adults' => 'integer',
            'total_children' => 'integer',
            'total_price' => 'decimal:2',
            'base_price' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'service_fee' => 'decimal:2',
            'platform_fee' => 'decimal:2',
            'pickup_latitude' => 'decimal:7',
            'pickup_longitude' => 'decimal:7',
            'cancelled_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    // ─── ACCESORS ───────────────────────────────────────

    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => TourBookingStatus::tryFrom($this->status)?->label() ?? $this->status,
        );
    }

    protected function statusColor(): Attribute
    {
        return Attribute::make(
            get: fn () => TourBookingStatus::tryFrom($this->status)?->color() ?? 'gray',
        );
    }

    protected function canCancel(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (in_array($this->status, ['cancelled', 'completed', 'on_going'])) {
                    return false;
                }
                
                if (in_array($this->status, ['pending', 'confirmed'])) {
                    return true;
                }
                
                if ($this->status === 'paid') {
                    $cancelHours50 = (int) \App\Models\PlatformSetting::getValue('tour_cancel_hours_50', 24);
                    $departureDateTime = \Carbon\Carbon::parse(
                        $this->tourSchedule->departure_date->format('Y-m-d') . ' ' . $this->tourSchedule->departure_time
                    );
                    return now()->diffInHours($departureDateTime, false) > $cancelHours50;
                }
                
                return false;
            },
        );
    }

    protected function cancellationFee(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->status !== 'paid') return 0;

                $departureDateTime = \Carbon\Carbon::parse(
                    $this->tourSchedule->departure_date->format('Y-m-d') . ' ' . $this->tourSchedule->departure_time
                );
                $hoursUntilDeparture = now()->diffInHours($departureDateTime, false);

                $hours15 = (int) \App\Models\PlatformSetting::getValue('tour_cancel_hours_15', 168);
                $hours30 = (int) \App\Models\PlatformSetting::getValue('tour_cancel_hours_30', 72);
                $hours50 = (int) \App\Models\PlatformSetting::getValue('tour_cancel_hours_50', 24);

                // < 24 jam = 100% (tidak bisa refund)
                if ($hoursUntilDeparture <= $hours50) {
                    return (int) $this->total_price;
                }
                
                // 1-3 hari = 50%
                if ($hoursUntilDeparture <= $hours30) {
                    $pct = (float) \App\Models\PlatformSetting::getValue('tour_cancel_fee_50_percent', 50);
                    return (int) round($this->total_price * ($pct / 100));
                }
                
                // 3-7 hari = 30%
                if ($hoursUntilDeparture <= $hours15) {
                    $pct = (float) \App\Models\PlatformSetting::getValue('tour_cancel_fee_30_percent', 30);
                    return (int) round($this->total_price * ($pct / 100));
                }
                
                // > 7 hari = 15%
                $pct = (float) \App\Models\PlatformSetting::getValue('tour_cancel_fee_15_percent', 15);
                return (int) round($this->total_price * ($pct / 100));
            },
        );
    }

    protected function cancellationRefund(): Attribute
    {
        return Attribute::make(
            get: function () {
                return max(0, (int) $this->total_price - $this->cancellation_fee);
            },
        );
    }

    // ─── RELATIONS ──────────────────────────────────────

    public function tourSchedule(): BelongsTo
    {
        return $this->belongsTo(TourSchedule::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function originStop(): BelongsTo
    {
        return $this->belongsTo(TourRouteStop::class, 'origin_stop_id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(TourBookingParticipant::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(TourPayment::class);
    }
}