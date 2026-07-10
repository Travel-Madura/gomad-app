<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourRouteStop extends Model
{
    use HasFactory;

    protected $fillable = [
        'tour_package_id', 'city_name', 'stop_order',
        'latitude', 'longitude',
        'is_pickup_available', 'is_dropoff_available',
        'estimated_arrival', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'stop_order' => 'integer',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_pickup_available' => 'boolean',
            'is_dropoff_available' => 'boolean',
        ];
    }

    public function tourPackage(): BelongsTo
    {
        return $this->belongsTo(TourPackage::class);
    }
}