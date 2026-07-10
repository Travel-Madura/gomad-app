<?php

namespace App\Services;

use App\Enums\RentalBookingStatus;
use App\Models\RentalBooking;
use App\Models\RentalPayment;
use App\Models\Vehicle;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class RentalService
{
    public function __construct(
        private readonly NotificationService $notificationService,
        private readonly WalletService $walletService,
    ) {}

    // ═══════════════════════════════════════════════════════
    // PENCARIAN
    // ═══════════════════════════════════════════════════════

    public function searchAvailableVehicles(?array $filters = []): Collection
    {
        $query = Vehicle::with(['agency'])
            ->availableForRental();

        if (!empty($filters['passengers'])) {
            $query->where('capacity', '>=', $filters['passengers'])
                ->where('rental_max_passengers', '>=', $filters['passengers']);
        }

        if (!empty($filters['include_driver'])) {
            $query->where('rental_include_driver', true);
        }

        if (!empty($filters['agency_id'])) {
            $query->where('agency_id', $filters['agency_id']);
        }

        if (!empty($filters['max_price'])) {
            $query->where('rental_min_price', '<=', $filters['max_price']);
        }

        $vehicles = $query->get();

        // Filter by month availability
        if (!empty($filters['month']) && !empty($filters['year'])) {
            $month = $filters['month'];
            $year = $filters['year'];

            $vehicles = $vehicles->filter(function ($vehicle) use ($month, $year) {
                $availableDates = $vehicle->getAvailableDates($month, $year);
                return !empty($availableDates);
            });
        }

        return $vehicles->values();
    }

    // ═══════════════════════════════════════════════════════
    // DISTANCE CALCULATION (OpenStreetMap OSRM)
    // ═══════════════════════════════════════════════════════

    public function calculateDistance(float $fromLat, float $fromLng, float $toLat, float $toLng): ?float
    {
        $url = "https://router.project-osrm.org/route/v1/driving/{$fromLng},{$fromLat};{$toLng},{$toLat}?overview=false";

        try {
            $response = Http::timeout(10)->get($url);
            if ($response->successful()) {
                $data = $response->json();
                $distanceMeters = $data['routes'][0]['distance'] ?? 0;
                return round($distanceMeters / 1000, 1);
            }
        } catch (\Exception $e) {
            \Log::error('OSRM Distance Error: ' . $e->getMessage());
        }

        // Fallback: Haversine formula (straight line)
        return $this->haversineDistance($fromLat, $fromLng, $toLat, $toLng);
    }

    private function haversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng / 2) * sin($dLng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return round($earthRadius * $c, 1);
    }

    // ═══════════════════════════════════════════════════════
    // BOOKING
    // ═══════════════════════════════════════════════════════

    public function createBooking(array $data): RentalBooking
    {
        return DB::transaction(function () use ($data) {
            $vehicle = Vehicle::findOrFail($data['vehicle_id']);

            if (!$vehicle->is_rental_available || $vehicle->status !== 'active') {
                throw new \Exception('Kendaraan tidak tersedia untuk disewa.');
            }

            $startDate = Carbon::parse($data['start_date']);
            $endDate = Carbon::parse($data['end_date']);
            $totalDays = max(1, (int) $startDate->diffInDays($endDate) + 1);

            // Validasi ketersediaan
            if (!$this->isVehicleAvailable($vehicle, $startDate, $endDate)) {
                throw new \Exception('Kendaraan tidak tersedia di tanggal yang dipilih.');
            }

            // Calculate distance
            $distance = $data['estimated_distance_km'] ?? 0;
            if (!empty($data['pickup_latitude']) && !empty($data['destination_latitude'])) {
                $distance = $this->calculateDistance(
                    (float) $data['pickup_latitude'],
                    (float) $data['pickup_longitude'],
                    (float) $data['destination_latitude'],
                    (float) $data['destination_longitude']
                );
            }

            // Calculate prices
            $baseCharge = max(
                (float) $vehicle->rental_min_price,
                $distance * (float) $vehicle->rental_price_per_km
            );

            $extraDays = max(0, $totalDays - 1);
            $extraCharge = $extraDays * (float) $vehicle->rental_extra_day_price;

            $includeDriver = $data['include_driver'] ?? $vehicle->rental_include_driver;
            $driverCharge = 0;
            if ($includeDriver) {
                $driverCharge = $totalDays * (float) $vehicle->rental_driver_price_per_day;
            }

            $subtotal = $baseCharge + $extraCharge + $driverCharge;
            $serviceFee = (float) \App\Models\PlatformSetting::getValue('rental_service_fee', 5000);
            $platformFeePercent = (float) \App\Models\PlatformSetting::getValue('rental_platform_fee_percent', 3);
            $platformFee = $subtotal * ($platformFeePercent / 100);

            // Generate booking code
            $todayCount = RentalBooking::whereDate('created_at', now()->toDateString())->count();
            $bookingCode = 'GR-' . now()->format('Ymd') . '-' . str_pad($todayCount + 1, 4, '0', STR_PAD_LEFT);

            $booking = RentalBooking::create([
                'booking_code' => $bookingCode,
                'vehicle_id' => $vehicle->id,
                'customer_id' => $data['customer_id'],
                'purpose' => $data['purpose'],
                'start_date' => $startDate,
                'end_date' => $endDate,
                'total_days' => $totalDays,
                'pickup_address' => $data['pickup_address'],
                'pickup_latitude' => $data['pickup_latitude'] ?? null,
                'pickup_longitude' => $data['pickup_longitude'] ?? null,
                'destination_address' => $data['destination_address'],
                'destination_latitude' => $data['destination_latitude'] ?? null,
                'destination_longitude' => $data['destination_longitude'] ?? null,
                'estimated_distance_km' => $distance,
                'include_driver' => $includeDriver,
                'max_passengers' => $data['max_passengers'] ?? 1,
                'total_price' => $totalPrice,
                'base_price' => $baseCharge + $extraCharge + $driverCharge,
                'extra_days_price' => $extraCharge,
                'driver_price' => $driverCharge,
                'service_fee' => $serviceFee,
                'platform_fee' => $platformFee,
                'special_notes' => $data['special_notes'] ?? null,
                'status' => RentalBookingStatus::PENDING->value,
            ]);

            // Notifikasi
            $agency = $vehicle->agency;
            if ($agency && $agency->user) {
                $this->notificationService->createNotification(
                    $agency->user->id,
                    '📋 Rental Booking Baru',
                    "Booking {$booking->booking_code} — {$vehicle->plate_number} — {$totalDays} hari — Rp " . number_format($totalPrice, 0, ',', '.'),
                    ['type' => 'new_rental_booking', 'booking_id' => $booking->id]
                );
            }

            return $booking->load(['vehicle.agency', 'customer']);
        });
    }

    public function isVehicleAvailable(Vehicle $vehicle, Carbon $start, Carbon $end): bool
    {
        // Cek rental bookings
        $conflictingRentals = RentalBooking::where('vehicle_id', $vehicle->id)
            ->whereNotIn('status', ['cancelled'])
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_date', [$start, $end])
                    ->orWhereBetween('end_date', [$start, $end])
                    ->orWhere(function ($sq) use ($start, $end) {
                        $sq->where('start_date', '<=', $start)
                            ->where('end_date', '>=', $end);
                    });
            })
            ->exists();

        if ($conflictingRentals) return false;

        // Cek travel schedules
        $conflictingTravel = Schedule::where('vehicle_id', $vehicle->id)
            ->where('is_active', true)
            ->whereBetween('departure_date', [$start->toDateString(), $end->toDateString()])
            ->exists();

        if ($conflictingTravel) return false;

        // Cek tour schedules
        $conflictingTour = \App\Models\TourSchedule::where('vehicle_id', $vehicle->id)
            ->where('is_active', true)
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('departure_date', [$start->toDateString(), $end->toDateString()])
                    ->orWhereBetween('return_date', [$start->toDateString(), $end->toDateString()]);
            })
            ->exists();

        if ($conflictingTour) return false;

        return true;
    }

    public function getCustomerBookings(int $customerId): Collection
    {
        return RentalBooking::with(['vehicle.agency', 'payment'])
            ->where('customer_id', $customerId)
            ->latest()
            ->get();
    }

    /**
     * Batalkan booking rental dengan refund logic
     */
    public function cancelBooking(RentalBooking $booking): void
    {
        if (!$booking->can_cancel) {
            if ($booking->status === 'paid') {
                $hoursUntil = now()->diffInHours($booking->start_date->setTime(0, 0, 0), false);
                if ($hoursUntil <= 24) {
                    throw new \Exception(
                        'Booking tidak dapat dibatalkan karena kurang dari 24 jam sebelum mulai sewa. ' .
                        'Hubungi agency untuk bantuan.'
                    );
                }
            }
            throw new \Exception('Booking tidak dapat dibatalkan pada status ini.');
        }

        DB::transaction(function () use ($booking) {
            $oldStatus = $booking->status;

            // Update status booking
            $booking->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            // Handle refund jika sudah PAID
            if ($oldStatus === 'paid' && $booking->payment) {
                $payment = $booking->payment;

                if ($payment->payment_type === 'midtrans') {
                    $refundAmount = $booking->cancellation_refund;

                    $payment->update([
                        'status' => 'refund_pending',
                        'payment_detail' => array_merge($payment->payment_detail ?? [], [
                            'refund' => [
                                'amount' => $refundAmount,
                                'cancellation_fee' => $booking->cancellation_fee,
                                'total_price' => $booking->total_price,
                                'requested_at' => now()->toIso8601String(),
                                'status' => 'pending',
                            ],
                        ]),
                    ]);

                    \Log::info('Rental refund pending', [
                        'booking_code' => $booking->booking_code,
                        'total' => $booking->total_price,
                        'fee' => $booking->cancellation_fee,
                        'refund' => $refundAmount,
                    ]);

                } elseif ($payment->payment_type === 'cash') {
                    $payment->update([
                        'status' => 'refund_pending',
                        'payment_detail' => array_merge($payment->payment_detail ?? [], [
                            'refund' => [
                                'amount' => $booking->cancellation_refund,
                                'cancellation_fee' => $booking->cancellation_fee,
                                'requested_at' => now()->toIso8601String(),
                                'status' => 'pending',
                                'note' => 'Customer harus datang ke warung untuk refund',
                            ],
                        ]),
                    ]);

                } elseif ($payment->payment_type === 'cod') {
                    $payment->update(['status' => 'expired']);
                }

                // Kurangi pending_balance agency
                if ((float) $payment->agency_revenue > 0) {
                    $agency = $booking->vehicle->agency;
                    $wallet = $this->walletService->getOrCreateWallet($agency);
                    $wallet->update([
                        'pending_balance' => max(0, (float) $wallet->pending_balance - (float) $payment->agency_revenue),
                    ]);
                }
            } else {
                // Pending/Confirmed — expire payment jika ada
                if ($booking->payment) {
                    $booking->payment->update(['status' => 'expired']);
                }
            }

            // Notifikasi ke customer
            if ($booking->customer) {
                $refundInfo = '';
                if ($oldStatus === 'paid') {
                    $refundInfo = "\n\nBiaya pembatalan: Rp " . number_format($booking->cancellation_fee, 0, ',', '.') .
                                "\nDana dikembalikan: Rp " . number_format($booking->cancellation_refund, 0, ',', '.');
                }

                $this->notificationService->createNotification(
                    $booking->customer->id,
                    '❌ Booking Rental Dibatalkan',
                    "Booking {$booking->booking_code} telah dibatalkan." . $refundInfo,
                    ['type' => 'rental_cancelled', 'booking_id' => $booking->id]
                );
            }

            // Notifikasi ke agency
            $agency = $booking->vehicle->agency;
            if ($agency && $agency->user) {
                $this->notificationService->createNotification(
                    $agency->user->id,
                    '❌ Booking Rental Dibatalkan',
                    "Booking {$booking->booking_code} — {$booking->vehicle->plate_number} dibatalkan oleh customer.",
                    ['type' => 'rental_cancelled', 'booking_id' => $booking->id]
                );
            }
        });
    }
}