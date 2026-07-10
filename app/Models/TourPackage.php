<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TourPackage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'agency_id', 'name', 'slug', 'description',
        'duration_days', 'duration_nights',
        'itinerary', 'includes', 'excludes',
        'cover_image', 'gallery', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'duration_days' => 'integer',
            'duration_nights' => 'integer',
            'itinerary' => 'json',
            'includes' => 'json',
            'excludes' => 'json',
            'gallery' => 'json',
            'is_active' => 'boolean',
        ];
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function stops(): HasMany
    {
        return $this->hasMany(TourRouteStop::class)->orderBy('stop_order');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(TourSchedule::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByAgency($query, int $agencyId)
    {
        return $query->where('agency_id', $agencyId);
    }
}