<?php

namespace App\Services;

use App\Enums\TourBookingStatus;
use App\Models\User;
use App\Models\TourBooking;
use App\Models\TourBookingParticipant;
use App\Models\TourPackage;
use App\Models\TourPayment;
use App\Models\TourRouteStop;
use App\Models\TourSchedule;
use App\Services\CloudinaryService;
use App\Services\NotificationService;
use App\Services\PricingService;
use App\Services\WalletService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TourService
{
    public function __construct(
        private readonly NotificationService $notificationService,
        private readonly WalletService $walletService,
        private readonly PricingService $pricingService,
    ) {}

    // ═══════════════════════════════════════════════════════
    // AGENCY — PACKAGE MANAGEMENT
    // ═══════════════════════════════════════════════════════

    /**
     * Buat paket tour baru beserta stops-nya
     */
    public function createPackage(array $data): TourPackage
    {
        $data['slug'] = Str::slug($data['name']) . '-' . Str::random(6);

        return DB::transaction(function () use ($data) {
            // Handle cover image upload
            if (isset($data['cover_image']) && $data['cover_image'] instanceof \Illuminate\Http\UploadedFile) {
                $cloudinary = app(\App\Services\CloudinaryService::class);
                $result = $cloudinary->upload($data['cover_image'], 'tour-packages');
                $data['cover_image'] = $result['url'];
            }

            // Handle gallery
            if (isset($data['gallery_photos']) && is_array($data['gallery_photos'])) {
                $gallery = [];
                $cloudinary = app(\App\Services\CloudinaryService::class);
                foreach ($data['gallery_photos'] as $photo) {
                    if ($photo instanceof \Illuminate\Http\UploadedFile) {
                        $result = $cloudinary->upload($photo, 'tour-packages/gallery');
                        $gallery[] = $result['url'];
                    }
                }
                $data['gallery'] = $gallery;
            }

            // Process itinerary JSON
            if (isset($data['itinerary']) && is_string($data['itinerary'])) {
                $data['itinerary'] = json_decode($data['itinerary'], true);
            }

            // Process includes/excludes
            if (isset($data['includes']) && is_string($data['includes'])) {
                $data['includes'] = array_filter(explode("\n", $data['includes']));
            }
            if (isset($data['excludes']) && is_string($data['excludes'])) {
                $data['excludes'] = array_filter(explode("\n", $data['excludes']));
            }

            $package = TourPackage::create([
                'agency_id' => $data['agency_id'],
                'name' => $data['name'],
                'slug' => $data['slug'],
                'description' => $data['description'] ?? null,
                'duration_days' => $data['duration_days'] ?? 1,
                'duration_nights' => $data['duration_nights'] ?? 0,
                'itinerary' => $data['itinerary'] ?? null,
                'includes' => $data['includes'] ?? null,
                'excludes' => $data['excludes'] ?? null,
                'cover_image' => $data['cover_image'] ?? null,
                'gallery' => $data['gallery'] ?? null,
                'is_active' => true,
            ]);

            // Create route stops
            if (!empty($data['stops']) && is_array($data['stops'])) {
                foreach ($data['stops'] as $index => $stopData) {
                    TourRouteStop::create([
                        'tour_package_id' => $package->id,
                        'city_name' => $stopData['city_name'],
                        'stop_order' => $stopData['stop_order'] ?? ($index + 1),
                        'latitude' => $stopData['latitude'] ?? null,
                        'longitude' => $stopData['longitude'] ?? null,
                        'is_pickup_available' => $stopData['is_pickup_available'] ?? ($index === 0),
                        'is_dropoff_available' => $stopData['is_dropoff_available'] ?? ($index === (count($data['stops']) - 1)),
                        'estimated_arrival' => $stopData['estimated_arrival'] ?? null,
                        'notes' => $stopData['notes'] ?? null,
                    ]);
                }
            }

            return $package->load('stops');
        });
    }

    /**
     * Update paket tour
     */
    public function updatePackage(TourPackage $package, array $data): TourPackage
    {
        return DB::transaction(function () use ($package, $data) {
            // Handle cover image
            if (isset($data['cover_image']) && $data['cover_image'] instanceof \Illuminate\Http\UploadedFile) {
                $cloudinary = app(\App\Services\CloudinaryService::class);
                // Delete old image
                if ($package->cover_image) {
                    $publicId = $this->extractCloudinaryPublicId($package->cover_image);
                    if ($publicId) $cloudinary->delete($publicId);
                }
                $result = $cloudinary->upload($data['cover_image'], 'tour-packages');
                $data['cover_image'] = $result['url'];
            }

            // Handle itinerary JSON
            if (isset($data['itinerary']) && is_string($data['itinerary'])) {
                $data['itinerary'] = json_decode($data['itinerary'], true);
            }

            // Handle includes/excludes
            if (isset($data['includes']) && is_string($data['includes'])) {
                $data['includes'] = array_filter(explode("\n", $data['includes']));
            }
            if (isset($data['excludes']) && is_string($data['excludes'])) {
                $data['excludes'] = array_filter(explode("\n", $data['excludes']));
            }

            $updateFields = [
                'name', 'description', 'duration_days', 'duration_nights',
                'itinerary', 'includes', 'excludes', 'cover_image', 'is_active',
            ];

            $package->update(array_intersect_key($data, array_flip($updateFields)));

            // Update stops if provided
            if (!empty($data['stops']) && is_array($data['stops'])) {
                // Delete existing stops and recreate
                $package->stops()->delete();
                foreach ($data['stops'] as $index => $stopData) {
                    TourRouteStop::create([
                        'tour_package_id' => $package->id,
                        'city_name' => $stopData['city_name'],
                        'stop_order' => $stopData['stop_order'] ?? ($index + 1),
                        'latitude' => $stopData['latitude'] ?? null,
                        'longitude' => $stopData['longitude'] ?? null,
                        'is_pickup_available' => $stopData['is_pickup_available'] ?? ($index === 0),
                        'is_dropoff_available' => $stopData['is_dropoff_available'] ?? ($index === (count($data['stops']) - 1)),
                        'estimated_arrival' => $stopData['estimated_arrival'] ?? null,
                        'notes' => $stopData['notes'] ?? null,
                    ]);
                }
            }

            return $package->fresh()->load('stops');
        });
    }

    // ═══════════════════════════════════════════════════════
    // AGENCY — SCHEDULE MANAGEMENT
    // ═══════════════════════════════════════════════════════

    /**
     * Buat jadwal keberangkatan untuk paket tour
     */
    public function createSchedule(array $data): TourSchedule
    {
        return DB::transaction(function () use ($data) {
            $schedule = TourSchedule::create([
                'tour_package_id' => $data['tour_package_id'],
                'vehicle_id' => $data['vehicle_id'],
                'driver_id' => $data['driver_id'] ?? null,
                'departure_date' => $data['departure_date'],
                'departure_time' => $data['departure_time'],
                'return_date' => $data['return_date'] ?? null,
                'return_time' => $data['return_time'] ?? null,
                'base_price' => $data['base_price'],
                'child_price' => $data['child_price'] ?? null,
                'max_participants' => $data['max_participants'] ?? 20,
                'min_participants' => $data['min_participants'] ?? 5,
                'pickup_zones' => $data['pickup_zones'] ?? null,
                'is_active' => true,
            ]);

            // Notifikasi ke driver jika ada
            if (!empty($data['driver_id'])) {
                $driver = \App\Models\User::find($data['driver_id']);
                if ($driver) {
                    $package = TourPackage::find($data['tour_package_id']);
                    $this->notificationService->createNotification(
                        $driver->id,
                        '📅 Jadwal Tour Baru',
                        "Anda ditugaskan untuk tour {$package->name} pada {$data['departure_date']}",
                        ['type' => 'tour_schedule', 'schedule_id' => $schedule->id]
                    );
                }
            }

            return $schedule->load('tourPackage');
        });
    }

    /**
     * Update jadwal tour
     */
    public function updateSchedule(TourSchedule $schedule, array $data): TourSchedule
    {
        $updateFields = [
            'vehicle_id', 'driver_id', 'departure_date', 'departure_time',
            'return_date', 'return_time', 'base_price', 'child_price',
            'max_participants', 'min_participants', 'pickup_zones', 'is_active',
        ];

        $schedule->update(array_intersect_key($data, array_flip($updateFields)));
        return $schedule->fresh();
    }

    /**
     * Dapatkan semua paket tour milik agency
     */
    public function getAgencyPackages(int $agencyId): Collection
    {
        return TourPackage::with(['stops', 'schedules' => function ($q) {
            $q->withCount('bookings')->latest();
        }])
        ->where('agency_id', $agencyId)
        ->latest()
        ->get();
    }

    /**
     * Dapatkan detail paket dengan booking untuk agency
     */
    public function getPackageDetail(TourPackage $package): TourPackage
    {
        return $package->load([
            'stops',
            'schedules' => function ($q) {
                $q->with(['vehicle', 'driver', 'bookings' => function ($bq) {
                    $bq->with(['customer', 'participants', 'payment'])->latest();
                }]);
            },
        ]);
    }

    /**
     * Dapatkan daftar booking untuk schedule tertentu
     */
    public function getScheduleBookings(TourSchedule $schedule): Collection
    {
        return $schedule->bookings()
            ->with(['customer', 'participants', 'payment', 'originStop'])
            ->latest()
            ->get();
    }

    // ═══════════════════════════════════════════════════════
    // CUSTOMER — BROWSING & BOOKING
    // ═══════════════════════════════════════════════════════

    /**
     * Dapatkan semua paket tour yang tersedia untuk customer
     */
    public function getAvailablePackages(?array $filters = []): Collection
    {
        $query = TourPackage::with(['agency', 'stops'])
            ->where('is_active', true)
            ->whereHas('agency', function ($q) {
                $q->where('is_verified', true);
            })
            ->whereHas('schedules', function ($q) {
                $q->where('is_active', true)
                    ->where('departure_date', '>=', now()->toDateString());
            });

        // Filter durasi
        if (!empty($filters['duration'])) {
            $query->where('duration_days', $filters['duration']);
        }

        // Filter budget
        if (!empty($filters['min_budget']) && !empty($filters['max_budget'])) {
            $query->whereHas('schedules', function ($q) use ($filters) {
                $q->whereBetween('base_price', [$filters['min_budget'], $filters['max_budget']]);
            });
        }

        // Search
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->latest()->get();
    }

    /**
     * Dapatkan jadwal yang tersedia untuk paket tour tertentu
     */
    public function getPackageSchedules(TourPackage $package): Collection
    {
        return $package->schedules()
            ->with(['vehicle', 'driver'])
            ->withCount(['bookings as booked_seats' => function ($q) {
                $q->whereNotIn('status', ['cancelled']);
            }])
            ->where('is_active', true)
            ->where('departure_date', '>=', now()->toDateString())
            ->orderBy('departure_date')
            ->get()
            ->map(function ($schedule) {
                $schedule->available_seats = $schedule->max_participants - $schedule->booked_seats;
                $schedule->is_full = $schedule->available_seats <= 0;
                return $schedule;
            });
    }

    /**
     * Dapatkan titik penjemputan yang tersedia
     */
    public function getAvailablePickupStops(TourPackage $package): array
    {
        return $package->stops()
            ->where('is_pickup_available', true)
            ->orderBy('stop_order')
            ->get()
            ->map(function ($stop) {
                return [
                    'id' => $stop->id,
                    'city_name' => $stop->city_name,
                    'stop_order' => $stop->stop_order,
                    'latitude' => $stop->latitude ? (float) $stop->latitude : null,
                    'longitude' => $stop->longitude ? (float) $stop->longitude : null,
                ];
            })
            ->toArray();
    }

    public function createBooking(array $data): TourBooking
    {
        return DB::transaction(function () use ($data) {
            $schedule = TourSchedule::with('tourPackage')->findOrFail($data['tour_schedule_id']);
            $package = $schedule->tourPackage;
            $customer = User::findOrFail($data['customer_id']);

            // ─── VALIDASI KAPASITAS ──────────────────────────
            $currentBooked = $schedule->bookings()
                ->whereNotIn('status', ['cancelled'])
                ->sum('total_participants');

            $participants = $data['participants'] ?? [];
            $requestedTotal = count($participants);

            if (($currentBooked + $requestedTotal) > $schedule->max_participants) {
                $available = $schedule->max_participants - $currentBooked;
                throw new \Exception("Jadwal hanya tersisa {$available} kursi. Anda memesan {$requestedTotal}.");
            }

            if ($requestedTotal < 1) {
                throw new \Exception('Minimal 1 peserta.');
            }

            // ─── HITUNG JUMLAH DEWASA & ANAK ─────────────────
            $totalAdults = collect($participants)->where('participant_type', 'adult')->count();
            $totalChildren = collect($participants)->where('participant_type', 'child')->count();

            // ═══════════════════════════════════════════════════════
            // PERHITUNGAN BIAYA (TANPA PROMO — promo di halaman detail)
            // ═══════════════════════════════════════════════════════

            // 1. Harga Tiket (base price)
            $adultPrice = $totalAdults * $schedule->base_price;
            $childPrice = $totalChildren * ($schedule->child_price ?? $schedule->base_price);
            $basePrice = $adultPrice + $childPrice;

            // 2. Biaya Layanan (flat)
            $serviceFee = (float) \App\Models\PlatformSetting::getValue('tour_service_fee', 5000);

            // 3. Biaya Platform (persentase dari base price)
            $platformFeePercent = (float) \App\Models\PlatformSetting::getValue('tour_platform_fee_percent', 3);
            $platformFee = $basePrice * ($platformFeePercent / 100);

            // 4. Total (tanpa diskon — diskon ditambahkan di halaman detail via applyPromo)
            $discountAmount = 0;
            $totalPrice = $basePrice + $serviceFee + $platformFee;

            // ─── GENERATE BOOKING CODE ────────────────────────
            $todayCount = TourBooking::whereDate('created_at', now()->toDateString())->count();
            $bookingCode = 'GT-' . now()->format('Ymd') . '-' . str_pad($todayCount + 1, 4, '0', STR_PAD_LEFT);

            // Ensure uniqueness
            while (TourBooking::where('booking_code', $bookingCode)->exists()) {
                $todayCount++;
                $bookingCode = 'GT-' . now()->format('Ymd') . '-' . str_pad($todayCount + 1, 4, '0', STR_PAD_LEFT);
            }

            // ─── CREATE BOOKING ──────────────────────────────
            $booking = TourBooking::create([
                'booking_code' => $bookingCode,
                'tour_schedule_id' => $schedule->id,
                'customer_id' => $data['customer_id'],
                'origin_stop_id' => $data['origin_stop_id'],
                'pickup_address' => $data['pickup_address'],
                'pickup_maps_link' => $data['pickup_maps_link'] ?? null,
                'pickup_latitude' => $data['pickup_latitude'] ?? null,
                'pickup_longitude' => $data['pickup_longitude'] ?? null,
                'group_name' => $data['group_name'],
                'total_participants' => $requestedTotal,
                'total_adults' => $totalAdults,
                'total_children' => $totalChildren,
                'total_price' => $totalPrice,
                'base_price' => $basePrice,
                'service_fee' => $serviceFee,
                'platform_fee' => $platformFee,
                'discount_amount' => $discountAmount,
                'special_requests' => $data['special_requests'] ?? null,
                'status' => TourBookingStatus::PENDING->value,
            ]);

            // ─── CREATE PARTICIPANTS ─────────────────────────
            foreach ($participants as $index => $participant) {
                TourBookingParticipant::create([
                    'tour_booking_id' => $booking->id,
                    'participant_name' => $participant['name'],
                    'participant_phone' => $participant['phone'] ?? null,
                    'participant_type' => $participant['participant_type'] ?? 'adult',
                    'id_number' => $participant['id_number'] ?? null,
                    'seat_number' => $index + 1,
                ]);
            }

            // ─── NOTIFIKASI KE AGENCY ────────────────────────
            $agency = $package->agency;
            if ($agency && $agency->user) {
                $this->notificationService->createNotification(
                    $agency->user->id,
                    '📋 Booking Tour Baru',
                    "Booking {$booking->booking_code}\n" .
                    "Paket: {$package->name}\n" .
                    "Rombongan: {$booking->group_name}\n" .
                    "Peserta: {$requestedTotal} orang\n" .
                    "Total: Rp " . number_format($booking->total_price, 0, ',', '.'),
                    [
                        'type' => 'new_tour_booking',
                        'booking_id' => $booking->id,
                        'booking_code' => $booking->booking_code,
                    ]
                );

                // WhatsApp ke agency
                $this->notificationService->sendWhatsApp(
                    $agency->user->phone,
                    "📋 *Booking Tour Baru!*\n\n" .
                    "Kode: *{$booking->booking_code}*\n" .
                    "Paket: {$package->name}\n" .
                    "Rombongan: {$booking->group_name}\n" .
                    "Customer: " . ($customer->name ?? '-') . "\n" .
                    "Peserta: {$requestedTotal} orang\n" .
                    "Dewasa: {$totalAdults}, Anak: {$totalChildren}\n" .
                    "Total: Rp " . number_format($booking->total_price, 0, ',', '.') . "\n" .
                    "Status: *Menunggu Pembayaran*\n\n" .
                    "Cek dashboard untuk detail."
                );
            }

            // ─── NOTIFIKASI KE CUSTOMER ──────────────────────
            if ($customer) {
                $this->notificationService->createNotification(
                    $customer->id,
                    '✅ Booking Tour Berhasil',
                    "Booking {$booking->booking_code} untuk {$package->name} berhasil dibuat. Silakan lakukan pembayaran.",
                    ['type' => 'tour_booking_created', 'booking_id' => $booking->id]
                );

                $waMessage = "✅ *Booking Tour Berhasil!*\n\n" .
                    "Halo {$customer->name},\n\n" .
                    "Booking tour Anda berhasil dibuat:\n" .
                    "📋 Kode: *{$booking->booking_code}*\n" .
                    "🏝️ Paket: {$package->name}\n" .
                    "📅 Tanggal: " . $schedule->departure_date->format('d M Y') . "\n" .
                    "🕐 Jam: {$schedule->departure_time}\n" .
                    "👥 Peserta: {$requestedTotal} orang\n" .
                    "💰 Total: Rp " . number_format($booking->total_price, 0, ',', '.') . "\n\n" .
                    "Segera lakukan pembayaran untuk konfirmasi.";

                $this->notificationService->sendWhatsApp($customer->phone, $waMessage);
            }

            return $booking->load(['tourSchedule.tourPackage', 'participants', 'originStop', 'customer']);
        });
    }

    /**
     * Dapatkan booking customer
     */
    public function getCustomerBookings(int $customerId): Collection
    {
        return TourBooking::with([
            'tourSchedule.tourPackage.agency',
            'participants',
            'payment',
            'originStop',
        ])
        ->where('customer_id', $customerId)
        ->latest()
        ->get();
    }

    /**
     * Batalkan booking tour
     */
    public function cancelBooking(TourBooking $booking): void
    {
        if (!$booking->can_cancel) {
            // Cek kenapa tidak bisa
            if ($booking->status === 'paid') {
                $departureDateTime = \Carbon\Carbon::parse(
                    $booking->tourSchedule->departure_date->format('Y-m-d') . ' ' . $booking->tourSchedule->departure_time
                );
                $hoursUntilDeparture = now()->diffInHours($departureDateTime, false);
                
                if ($hoursUntilDeparture <= 24) {
                    throw new \Exception(
                        'Booking tidak dapat dibatalkan karena kurang dari 24 jam sebelum keberangkatan. ' .
                        'Hubungi agency untuk bantuan.'
                    );
                }
            }
            
            throw new \Exception('Booking tidak dapat dibatalkan pada status ini.');
        }

        DB::transaction(function () use ($booking) {
            $oldStatus = $booking->status;

            // 1. Update status booking
            $booking->update([
                'status' => TourBookingStatus::CANCELLED->value,
                'cancelled_at' => now(),
            ]);

            // 2. Handle refund jika sudah PAID
            if ($oldStatus === TourBookingStatus::PAID->value && $booking->payment) {
                $payment = $booking->payment;
                
                if ($payment->payment_type === 'midtrans') {
                    // Hitung refund amount
                    $refundAmount = $booking->cancellation_refund;
                    
                    // Update payment status
                    $payment->update([
                        'status' => 'refund_pending',
                        'payment_detail' => array_merge($payment->payment_detail ?? [], [
                            'refund' => [
                                'amount' => $refundAmount,
                                'cancellation_fee' => $booking->cancellation_fee,
                                'requested_at' => now()->toIso8601String(),
                                'status' => 'pending',
                            ],
                        ]),
                    ]);
                    
                    \Log::info('Tour refund pending', [
                        'booking_code' => $booking->booking_code,
                        'total' => $booking->total_price,
                        'fee' => $booking->cancellation_fee,
                        'refund' => $refundAmount,
                    ]);
                    
                } elseif ($payment->payment_type === 'cash') {
                    // Cash: tidak ada refund otomatis — customer harus ke warung
                    $payment->update([
                        'status' => 'refund_pending',
                        'payment_detail' => array_merge($payment->payment_detail ?? [], [
                            'refund' => [
                                'amount' => $booking->total_price,
                                'cancellation_fee' => $booking->cancellation_fee,
                                'refund_amount' => $booking->cancellation_refund,
                                'requested_at' => now()->toIso8601String(),
                                'status' => 'pending',
                                'note' => 'Customer harus datang ke warung untuk refund',
                            ],
                        ]),
                    ]);
                    
                } elseif ($payment->payment_type === 'cod') {
                    // COD: expire payment
                    $payment->update(['status' => 'expired']);
                }

                // 3. Kurangi pending_balance agency
                if ((float) $payment->agency_revenue > 0) {
                    $agency = $booking->tourSchedule->tourPackage->agency;
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

            // 4. Notifikasi ke customer
            if ($booking->customer) {
                $refundInfo = '';
                if ($oldStatus === TourBookingStatus::PAID->value) {
                    $refundInfo = "\n\nBiaya pembatalan: Rp " . number_format($booking->cancellation_fee, 0, ',', '.') .
                                "\nDana dikembalikan: Rp " . number_format($booking->cancellation_refund, 0, ',', '.');
                }
                
                $this->notificationService->createNotification(
                    $booking->customer->id,
                    '❌ Booking Tour Dibatalkan',
                    "Booking {$booking->booking_code} telah dibatalkan." . $refundInfo,
                    ['type' => 'tour_booking_cancelled', 'booking_id' => $booking->id]
                );

                $this->notificationService->sendWhatsApp(
                    $booking->customer->phone,
                    "❌ *Booking Tour Dibatalkan*\n\n" .
                    "Kode: {$booking->booking_code}\n" .
                    "Paket: " . ($booking->tourSchedule->tourPackage->name ?? '-') . "\n" .
                    "Rombongan: {$booking->group_name}" .
                    $refundInfo . "\n\n" .
                    "Terima kasih."
                );
            }

            // 5. Notifikasi ke agency
            $agency = $booking->tourSchedule->tourPackage->agency;
            if ($agency && $agency->user) {
                $this->notificationService->createNotification(
                    $agency->user->id,
                    '❌ Booking Tour Dibatalkan',
                    "Booking {$booking->booking_code} - {$booking->group_name} dibatalkan oleh customer.",
                    ['type' => 'tour_booking_cancelled', 'booking_id' => $booking->id]
                );
            }
        });
    }

    /**
     * Selesaikan booking tour (setelah perjalanan selesai)
     */
    public function completeBooking(TourBooking $booking): void
    {
        DB::transaction(function () use ($booking) {
            if ($booking->status !== TourBookingStatus::ON_GOING->value) {
                throw new \Exception('Booking harus dalam status On Going untuk diselesaikan.');
            }

            $booking->update([
                'status' => TourBookingStatus::COMPLETED->value,
                'completed_at' => now(),
            ]);

            // Release funds ke agency
            if ($booking->payment && $booking->payment->agency_revenue > 0) {
                $agency = $booking->tourSchedule->tourPackage->agency;
                $this->walletService->releaseFundsForTour($booking);
            }

            // Update agency counter
            $booking->tourSchedule->tourPackage->agency->increment('total_bookings');

            // Notifikasi
            if ($booking->customer) {
                $this->notificationService->createNotification(
                    $booking->customer->id,
                    '🎉 Tour Selesai',
                    "Tour {$booking->booking_code} telah selesai. Terima kasih!",
                    ['type' => 'tour_completed', 'booking_id' => $booking->id]
                );
            }
        });
    }

    // ═══════════════════════════════════════════════════════
    // PUBLIC
    // ═══════════════════════════════════════════════════════

    /**
     * Dapatkan paket tour untuk halaman publik
     */
    public function getPublicPackages(int $limit = 12): \Illuminate\Pagination\LengthAwarePaginator
    {
        return TourPackage::with(['agency', 'stops'])
            ->where('is_active', true)
            ->whereHas('agency', function ($q) {
                $q->where('is_verified', true);
            })
            ->whereHas('schedules', function ($q) {        // ← INI MASALAHNYA
                $q->where('is_active', true)                // Harus ada jadwal
                    ->where('departure_date', '>=', now()->toDateString());  // Harus upcoming
            })
            ->withCount(['schedules as upcoming_schedules' => function ($q) {
                $q->where('is_active', true)
                    ->where('departure_date', '>=', now()->toDateString());
            }])
            ->latest()
            ->paginate($limit);
    }

    /**
     * Dapatkan paket tour by slug untuk halaman publik
     */
    public function getPublicPackageBySlug(string $slug): ?TourPackage
    {
        return TourPackage::with(['agency', 'stops', 'schedules' => function ($q) {
            $q->with(['vehicle'])
                ->where('is_active', true)
                ->where('departure_date', '>=', now()->toDateString())
                ->orderBy('departure_date');
        }])
        ->where('slug', $slug)
        ->where('is_active', true)
        ->first();
    }

    // ═══════════════════════════════════════════════════════
    // HELPER
    // ═══════════════════════════════════════════════════════

    /**
     * Extract Cloudinary public ID dari URL
     */
    private function extractCloudinaryPublicId(string $url): ?string
    {
        $pattern = '/\/upload\/(?:v\d+\/)?(.+?)\.\w+$/';
        if (preg_match($pattern, $url, $matches)) {
            return $matches[1];
        }
        return null;
    }
}