<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TourPromo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'type', 'description',
        'discount_percent', 'max_discount', 'min_purchase',
        'tour_package_id',
        'applicable_payment_methods',
        'start_date', 'end_date',
        'cost_bearer', 'platform_share_percent', 'agency_share_percent',
        'is_active', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'discount_percent' => 'decimal:2',
            'max_discount' => 'decimal:2',
            'min_purchase' => 'decimal:2',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'is_active' => 'boolean',
            'platform_share_percent' => 'decimal:2',
            'agency_share_percent' => 'decimal:2',
        ];
    }

    // ─── Relations ───────────────────────────────────

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tourPackage(): BelongsTo
    {
        return $this->belongsTo(TourPackage::class);
    }

    public function tourSchedules(): BelongsToMany
    {
        return $this->belongsToMany(TourSchedule::class, 'tour_promo_schedule', 'tour_promo_id', 'tour_schedule_id')
            ->withTimestamps();
    }

    public function usages(): HasMany
    {
        return $this->hasMany(TourPromoUsage::class);
    }

    // ─── Scopes ──────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    public function scopeGeneral($query)
    {
        return $query->where('type', 'general');
    }

    public function scopeSelective($query)
    {
        return $query->where('type', 'selective');
    }

    // ─── Accessors ───────────────────────────────────

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'general' => '🌍 General',
            'selective' => '🎯 Selektif',
            default => $this->type,
        };
    }

    public function getCostBearerLabelAttribute(): string
    {
        return match($this->cost_bearer) {
            'platform' => 'Platform (100%)',
            'agency' => 'Agency (100%)',
            'shared' => "Shared (Platform {$this->platform_share_percent}% + Agency {$this->agency_share_percent}%)",
            default => $this->cost_bearer,
        };
    }

    // ─── Methods ─────────────────────────────────────

    public function isActiveNow(): bool
    {
        return $this->is_active
            && now()->gte($this->start_date)
            && now()->lte($this->end_date);
    }

    public function isApplicableFor(string $paymentMethod): bool
    {
        if (empty($this->applicable_payment_methods)) {
            return true;
        }
        $methods = explode(',', $this->applicable_payment_methods);
        return in_array($paymentMethod, $methods);
    }

    public function getApplicablePaymentMethodsArray(): array
    {
        if (empty($this->applicable_payment_methods)) {
            return ['midtrans', 'cash', 'cod'];
        }
        return explode(',', $this->applicable_payment_methods);
    }

    // ─── Mutators ────────────────────────────────────

    public function setApplicablePaymentMethodsAttribute($value): void
    {
        if (is_array($value)) {
            $value = array_filter($value);
            $this->attributes['applicable_payment_methods'] = !empty($value) ? implode(',', $value) : null;
        } elseif (is_string($value)) {
            $this->attributes['applicable_payment_methods'] = !empty($value) ? $value : null;
        } else {
            $this->attributes['applicable_payment_methods'] = null;
        }
    }
}