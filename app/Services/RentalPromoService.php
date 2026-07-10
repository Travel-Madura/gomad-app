<?php

namespace App\Services;

use App\Models\RentalBooking;
use App\Models\RentalPromo;
use App\Models\RentalPromoUsage;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Support\Collection;

class RentalPromoService
{
    /**
     * Dapatkan promo yang tersedia untuk customer di halaman detail rental
     */
    public function getAvailablePromosForCustomer(User $user, int $vehicleId, ?string $paymentMethod = null): Collection
    {
        $vehicle = Vehicle::findOrFail($vehicleId);

        // ID promo yang sudah pernah dipakai customer
        $usedPromoIds = RentalPromoUsage::where('user_id', $user->id)
            ->pluck('rental_promo_id')
            ->toArray();

        // Query promo aktif yang belum pernah dipakai
        $query = RentalPromo::active()
            ->whereNotIn('id', $usedPromoIds)
            ->where(function ($q) use ($vehicle) {
                // General → untuk semua kendaraan
                $q->where('type', 'general');
                // Selektif → target kendaraan ini ATAU sudah dipasang agency
                $q->orWhere(function ($sq) use ($vehicle) {
                    $sq->where('type', 'selective')
                       ->where(function ($ssq) use ($vehicle) {
                           $ssq->where('vehicle_id', $vehicle->id)
                               ->orWhereHas('vehicles', function ($vq) use ($vehicle) {
                                   $vq->where('vehicle_id', $vehicle->id);
                               });
                       });
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
    public function calculateDiscount(RentalPromo $promo, float $basePrice): float
    {
        if ($basePrice < $promo->min_purchase) return 0;
        $discount = $basePrice * ($promo->discount_percent / 100);
        return min($discount, (float) $promo->max_discount);
    }

    /**
     * Cek apakah customer bisa pakai promo ini
     */
    public function canUsePromo(User $user, RentalPromo $promo): bool
    {
        if (!$promo->isActiveNow()) return false;
        return !RentalPromoUsage::where('user_id', $user->id)
            ->where('rental_promo_id', $promo->id)
            ->exists();
    }

    /**
     * Apply promo ke booking rental
     */
    public function applyPromo(RentalBooking $booking, RentalPromo $promo): ?RentalPromoUsage
    {
        if (!$this->canUsePromo($booking->customer, $promo)) return null;

        $discount = $this->calculateDiscount($promo, (float) $booking->base_price);
        if ($discount <= 0) return null;

        // Update total harga
        $newTotal = max(0, (float) $booking->total_price - $discount);
        $booking->update([
            'total_price' => $newTotal,
            'discount_amount' => ((float) ($booking->discount_amount ?? 0)) + $discount,
        ]);

        // Update payment amount jika ada
        if ($booking->payment) {
            $booking->payment->update(['amount' => $newTotal]);
        }

        return RentalPromoUsage::create([
            'rental_promo_id' => $promo->id,
            'user_id' => $booking->customer_id,
            'rental_booking_id' => $booking->id,
            'discount_amount' => $discount,
        ]);
    }

    /**
     * Pasang promo ke kendaraan (oleh agency)
     */
    public function attachPromoToVehicle(int $promoId, int $vehicleId): void
    {
        $vehicle = Vehicle::findOrFail($vehicleId);
        $vehicle->rentalPromos()->syncWithoutDetaching([$promoId]);
    }

    /**
     * Lepas promo dari kendaraan
     */
    public function detachPromoFromVehicle(int $promoId, int $vehicleId): void
    {
        $vehicle = Vehicle::findOrFail($vehicleId);
        $vehicle->rentalPromos()->detach($promoId);
    }

    /**
     * Semua promo (admin)
     */
    public function getAllPromos(): \Illuminate\Pagination\LengthAwarePaginator
    {
        return RentalPromo::with('creator', 'vehicle')
            ->latest()
            ->paginate(15);
    }

    /**
     * Promo selektif yang tersedia untuk agency
     */
    public function getSelectivePromosForAgency(): Collection
    {
        return RentalPromo::active()->selective()->latest()->get();
    }
}