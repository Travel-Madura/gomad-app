<?php

namespace App\Services;

use App\Models\TourBooking;
use App\Models\TourPromo;
use App\Models\TourPromoUsage;
use App\Models\TourSchedule;
use App\Models\User;
use Illuminate\Support\Collection;

class TourPromoService
{
    /**
     * Dapatkan promo yang tersedia untuk customer
     */
    public function getAvailablePromosForCustomer(User $user, int $tourScheduleId, ?string $paymentMethod = null): Collection
    {
        $schedule = TourSchedule::with('tourPackage')->findOrFail($tourScheduleId);
        $package = $schedule->tourPackage;

        // ID promo yang sudah pernah dipakai customer
        $usedPromoIds = TourPromoUsage::where('user_id', $user->id)
            ->pluck('tour_promo_id')
            ->toArray();

        // Query promo aktif yang belum pernah dipakai
        $query = TourPromo::active()
            ->whereNotIn('id', $usedPromoIds)
            ->where(function ($q) use ($package) {
                // General → untuk semua paket
                $q->where('type', 'general');
                // Selektif → target paket ini
                $q->orWhere(function ($sq) use ($package) {
                    $sq->where('type', 'selective')
                       ->where('tour_package_id', $package->id);
                });
            });

        // Filter metode pembayaran
        if ($paymentMethod) {
            $query->where(function ($q) use ($paymentMethod) {
                $q->whereNull('applicable_payment_methods')
                  ->orWhere('applicable_payment_methods', '')
                  ->orWhere('applicable_payment_methods', 'like', "%{$paymentMethod}%");
            });
        }

        return $query->get();
    }

    /**
     * Hitung diskon dari promo
     */
    public function calculateDiscount(TourPromo $promo, float $basePrice): float
    {
        if ($basePrice < $promo->min_purchase) {
            return 0;
        }

        $discount = $basePrice * ($promo->discount_percent / 100);
        return min($discount, (float) $promo->max_discount);
    }

    /**
     * Cek apakah customer bisa pakai promo ini
     */
    public function canUsePromo(User $user, TourPromo $promo): bool
    {
        if (!$promo->isActiveNow()) {
            return false;
        }

        $alreadyUsed = TourPromoUsage::where('user_id', $user->id)
            ->where('tour_promo_id', $promo->id)
            ->exists();

        return !$alreadyUsed;
    }

    /**
     * Gunakan promo untuk booking
     */
    public function applyPromo(TourBooking $booking, TourPromo $promo): ?TourPromoUsage
    {
        if (!$this->canUsePromo($booking->customer, $promo)) {
            return null;
        }

        $discount = $this->calculateDiscount($promo, (float) $booking->base_price);

        if ($discount <= 0) {
            return null;
        }

        // Update total harga booking
        $newTotal = max(0, (float) $booking->total_price - $discount);
        $booking->update([
            'total_price' => $newTotal,
            'discount_amount' => ((float) ($booking->discount_amount ?? 0)) + $discount,
        ]);

        // Catat pemakaian promo
        return TourPromoUsage::create([
            'tour_promo_id' => $promo->id,
            'user_id' => $booking->customer_id,
            'tour_booking_id' => $booking->id,
            'discount_amount' => $discount,
        ]);
    }

    /**
     * Pasang promo ke jadwal tour (oleh agency)
     */
    public function attachPromoToSchedule(int $promoId, int $tourScheduleId): void
    {
        $schedule = TourSchedule::findOrFail($tourScheduleId);
        $schedule->tourPromos()->syncWithoutDetaching([$promoId]);
    }

    /**
     * Lepas promo dari jadwal tour
     */
    public function detachPromoFromSchedule(int $promoId, int $tourScheduleId): void
    {
        $schedule = TourSchedule::findOrFail($tourScheduleId);
        $schedule->tourPromos()->detach($promoId);
    }

    /**
     * Dapatkan semua promo (untuk admin)
     */
    public function getAllPromos(): \Illuminate\Pagination\LengthAwarePaginator
    {
        return TourPromo::with('creator', 'tourPackage')
            ->latest()
            ->paginate(15);
    }

    /**
     * Dapatkan promo selektif yang tersedia (untuk agency)
     */
    public function getSelectivePromosForAgency(): Collection
    {
        return TourPromo::active()
            ->selective()
            ->latest()
            ->get();
    }

    /**
     * Dapatkan riwayat pemakaian promo oleh customer
     */
    public function getCustomerPromoHistory(User $user): Collection
    {
        return TourPromoUsage::where('user_id', $user->id)
            ->with(['tourPromo', 'tourBooking'])
            ->latest()
            ->get();
    }
}