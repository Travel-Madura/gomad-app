<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\TourPromo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TourSchedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tour_package_id', 'vehicle_id', 'driver_id',
        'departure_date', 'departure_time',
        'return_date', 'return_time',
        'base_price', 'child_price',
        'max_participants', 'min_participants',
        'pickup_zones', 'is_active',
        'started_at', 'finished_at',
    ];

    protected function casts(): array
    {
        return [
            'departure_date' => 'datetime',
            'return_date' => 'datetime',
            'base_price' => 'decimal:2',
            'child_price' => 'decimal:2',
            'max_participants' => 'integer',
            'min_participants' => 'integer',
            'pickup_zones' => 'json',
            'is_active' => 'boolean',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    protected function availableSeats(): Attribute
    {
        return Attribute::make(
            get: function () {
                $booked = $this->bookings()
                    ->whereNotIn('status', ['cancelled'])
                    ->sum('total_participants');
                return max(0, $this->max_participants - $booked);
            },
        );
    }

    protected function isFull(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->available_seats <= 0,
        );
    }

    protected function occupancyRate(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->max_participants <= 0) return 0;
                $booked = $this->bookings()
                    ->whereNotIn('status', ['cancelled'])
                    ->sum('total_participants');
                return round(($booked / $this->max_participants) * 100, 2);
            },
        );
    }

    public function tourPackage(): BelongsTo
    {
        return $this->belongsTo(TourPackage::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(TourBooking::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('departure_date', '>=', now()->toDateString());
    }

    public function tourPromos(): BelongsToMany
    {
        return $this->belongsToMany(TourPromo::class, 'tour_promo_schedule', 'tour_schedule_id', 'tour_promo_id')
            ->withTimestamps();
    }
}