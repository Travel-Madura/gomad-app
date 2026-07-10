<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'agency_id',
        'plate_number',
        'brand',
        'model',
        'year',
        'capacity',
        'type',
        'status',                    // 👈 BARU
        'is_rental_available',       // 👈 BARU
        'rental_price_per_km',       // 👈 BARU
        'rental_min_price',          // 👈 BARU
        'rental_extra_day_price',    // 👈 BARU
        'rental_include_driver',     // 👈 BARU
        'rental_driver_price_per_day', // 👈 BARU
        'rental_max_passengers',     // 👈 BARU
        'vehicle_image',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'capacity' => 'integer',
            'is_active' => 'boolean',
            'is_rental_available' => 'boolean',      // 👈 BARU
            'rental_include_driver' => 'boolean',     // 👈 BARU
            'rental_price_per_km' => 'decimal:2',     // 👈 BARU
            'rental_min_price' => 'decimal:2',        // 👈 BARU
            'rental_extra_day_price' => 'decimal:2',  // 👈 BARU
            'rental_driver_price_per_day' => 'decimal:2', // 👈 BARU
            'rental_max_passengers' => 'integer',     // 👈 BARU
        ];
    }

    // ─── RELATIONS ──────────────────────────────────────

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function tourSchedules(): HasMany
    {
        return $this->hasMany(\App\Models\TourSchedule::class);
    }

    // 👇 BARU
    public function rentalBookings(): HasMany
    {
        return $this->hasMany(\App\Models\RentalBooking::class);
    }

    // ─── SCOPES ─────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->where('status', 'active');
    }

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeByAgency(Builder $query, int $agencyId): Builder
    {
        return $query->where('agency_id', $agencyId);
    }

    // 👇 BARU
    public function scopeAvailableForRental(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where('status', 'active')
            ->where('is_rental_available', true);
    }

    // ─── ACCESSORS ──────────────────────────────────────

    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->status) {
                'active' => '🟢 Aktif',
                'maintenance' => '🟡 Maintenance',
                'inactive' => '🔴 Nonaktif',
                default => $this->status,
            },
        );
    }

    // ─── METHODS ────────────────────────────────────────

    /**
     * Cek ketersediaan kendaraan untuk rental di bulan tertentu
     */
    public function getAvailableDates(int $month, int $year): array
    {
        if ($this->status !== 'active' || !$this->is_rental_available) {
            return [];
        }

        $blockedDates = [];

        // Travel schedules
        $travelDates = Schedule::where('vehicle_id', $this->id)
            ->where('is_active', true)
            ->whereMonth('departure_date', $month)
            ->whereYear('departure_date', $year)
            ->pluck('departure_date');

        foreach ($travelDates as $date) {
            $blockedDates[$date->toDateString()] = true;
        }

        // Tour schedules
        $tourDates = \App\Models\TourSchedule::where('vehicle_id', $this->id)
            ->where('is_active', true)
            ->whereMonth('departure_date', $month)
            ->whereYear('departure_date', $year)
            ->get();

        foreach ($tourDates as $ts) {
            $start = $ts->departure_date;
            $end = $ts->return_date ?? $ts->departure_date;
            for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
                $blockedDates[$d->toDateString()] = true;
            }
        }

        // Rental bookings
        $rentals = \App\Models\RentalBooking::where('vehicle_id', $this->id)
            ->whereNotIn('status', ['cancelled'])
            ->where(function ($q) use ($month, $year) {
                $q->whereMonth('start_date', $month)->whereYear('start_date', $year);
                $q->orWhereMonth('end_date', $month)->whereYear('end_date', $year);
            })
            ->get();

        foreach ($rentals as $r) {
            $start = Carbon::parse($r->start_date);
            $end = Carbon::parse($r->end_date);
            for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
                $blockedDates[$d->toDateString()] = true;
            }
        }

        $available = [];
        $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::create($year, $month, $day);
            if ($date->isPast()) continue;
            if (!isset($blockedDates[$date->toDateString()])) {
                $available[] = $date->toDateString();
            }
        }

        return $available;
    }

    public function rentalPromos(): BelongsToMany
    {
        return $this->belongsToMany(RentalPromo::class, 'rental_promo_vehicle', 'vehicle_id', 'rental_promo_id')
            ->withTimestamps();
    }
}