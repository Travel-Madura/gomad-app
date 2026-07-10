<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\RentalBooking;
use App\Models\Vehicle;
use App\Services\RentalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RentalController extends Controller
{
    public function __construct(
        private readonly RentalService $rentalService,
    ) {}

    /**
     * Daftar kendaraan tersedia untuk rental
     */
    public function index(Request $request): JsonResponse
    {
        $vehicles = $this->rentalService->searchAvailableVehicles($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Daftar kendaraan tersedia.',
            'data' => $vehicles->map(function ($v) {
                return [
                    'id' => $v->id,
                    'plate_number' => $v->plate_number,
                    'brand' => $v->brand,
                    'model' => $v->model,
                    'year' => $v->year,
                    'capacity' => $v->capacity,
                    'type' => $v->type,
                    'vehicle_image' => $v->vehicle_image ?? null,
                    'status' => $v->status,
                    'status_label' => $v->status_label,
                    'agency' => [
                        'id' => $v->agency->id,
                        'name' => $v->agency->agency_name,
                        'slug' => $v->agency->slug,
                        'rating' => (float) $v->agency->rating,
                        'logo' => $v->agency->logo ?? null,
                    ],
                    'rental' => [
                        'price_per_km' => (float) $v->rental_price_per_km,
                        'min_price' => (float) $v->rental_min_price,
                        'extra_day_price' => (float) $v->rental_extra_day_price,
                        'include_driver' => $v->rental_include_driver,
                        'driver_price_per_day' => (float) $v->rental_driver_price_per_day,
                        'max_passengers' => $v->rental_max_passengers,
                    ],
                ];
            }),
            'meta' => ['total' => $vehicles->count()],
        ]);
    }

    /**
     * Tanggal tersedia untuk kendaraan tertentu
     */
    public function availableDates(Request $request): JsonResponse
    {
        $request->validate([
            'vehicle_id' => ['required', 'integer', 'exists:vehicles,id'],
        ]);

        $vehicle = Vehicle::findOrFail($request->vehicle_id);
        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;

        $dates = $vehicle->getAvailableDates($month, $year);

        return response()->json([
            'success' => true,
            'message' => 'Tanggal tersedia berhasil diambil.',
            'data' => [
                'vehicle_id' => $vehicle->id,
                'plate_number' => $vehicle->plate_number,
                'month' => $month,
                'year' => $year,
                'available_dates' => $dates,
                'total_days' => count($dates),
            ],
            'meta' => null,
        ]);
    }

    /**
     * Hitung jarak antara dua titik
     */
    public function calculateDistance(Request $request): JsonResponse
    {
        $request->validate([
            'from_lat' => ['required', 'numeric', 'between:-90,90'],
            'from_lng' => ['required', 'numeric', 'between:-180,180'],
            'to_lat' => ['required', 'numeric', 'between:-90,90'],
            'to_lng' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $distance = $this->rentalService->calculateDistance(
            (float) $request->from_lat,
            (float) $request->from_lng,
            (float) $request->to_lat,
            (float) $request->to_lng
        );

        return response()->json([
            'success' => true,
            'message' => 'Jarak berhasil dihitung.',
            'data' => [
                'distance_km' => $distance,
                'from' => ['lat' => (float) $request->from_lat, 'lng' => (float) $request->from_lng],
                'to' => ['lat' => (float) $request->to_lat, 'lng' => (float) $request->to_lng],
            ],
            'meta' => null,
        ]);
    }

    /**
     * Buat booking rental
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'vehicle_id' => ['required', 'integer', 'exists:vehicles,id'],
            'purpose' => ['required', 'string', 'max:200'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'pickup_address' => ['required', 'string', 'max:500'],
            'pickup_latitude' => ['nullable', 'numeric'],
            'pickup_longitude' => ['nullable', 'numeric'],
            'destination_address' => ['required', 'string', 'max:500'],
            'destination_latitude' => ['nullable', 'numeric'],
            'destination_longitude' => ['nullable', 'numeric'],
            'estimated_distance_km' => ['nullable', 'numeric'],
            'include_driver' => ['nullable', 'boolean'],
            'max_passengers' => ['required', 'integer', 'min:1'],
            'special_notes' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $data = $request->all();
            $data['customer_id'] = $request->user()->id;

            $booking = $this->rentalService->createBooking($data);

            return response()->json([
                'success' => true,
                'message' => 'Booking rental berhasil dibuat.',
                'data' => [
                    'id' => $booking->id,
                    'booking_code' => $booking->booking_code,
                    'total_price' => (float) $booking->total_price,
                    'total_days' => $booking->total_days,
                    'status' => $booking->status,
                    'created_at' => $booking->created_at->format('Y-m-d H:i:s'),
                ],
                'meta' => null,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat booking: ' . $e->getMessage(),
                'data' => null,
                'meta' => null,
            ], 422);
        }
    }

    /**
     * Detail booking rental
     */
    public function show(RentalBooking $booking): JsonResponse
    {
        if ($booking->customer_id !== request()->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke booking ini.',
                'data' => null,
                'meta' => null,
            ], 403);
        }

        $booking->load(['vehicle.agency', 'payment']);

        return response()->json([
            'success' => true,
            'message' => 'Detail booking berhasil diambil.',
            'data' => [
                'id' => $booking->id,
                'booking_code' => $booking->booking_code,
                'purpose' => $booking->purpose,
                'start_date' => $booking->start_date->format('Y-m-d'),
                'end_date' => $booking->end_date->format('Y-m-d'),
                'total_days' => $booking->total_days,
                'pickup_address' => $booking->pickup_address,
                'destination_address' => $booking->destination_address,
                'estimated_distance_km' => (float) $booking->estimated_distance_km,
                'include_driver' => $booking->include_driver,
                'max_passengers' => $booking->max_passengers,
                'total_price' => (float) $booking->total_price,
                'base_price' => (float) $booking->base_price,
                'extra_days_price' => (float) $booking->extra_days_price,
                'driver_price' => (float) $booking->driver_price,
                'service_fee' => (float) $booking->service_fee,
                'platform_fee' => (float) $booking->platform_fee,
                'discount_amount' => (float) $booking->discount_amount,
                'special_notes' => $booking->special_notes,
                'status' => $booking->status,
                'status_label' => $booking->status_label,
                'can_cancel' => $booking->can_cancel,
                'vehicle' => [
                    'id' => $booking->vehicle->id,
                    'plate_number' => $booking->vehicle->plate_number,
                    'brand' => $booking->vehicle->brand,
                    'model' => $booking->vehicle->model,
                    'vehicle_image' => $booking->vehicle->vehicle_image,
                    'agency' => [
                        'id' => $booking->vehicle->agency->id,
                        'name' => $booking->vehicle->agency->agency_name,
                        'phone' => $booking->vehicle->agency->contact_alternate,
                    ],
                ],
                'payment' => $booking->payment ? [
                    'id' => $booking->payment->id,
                    'amount' => (float) $booking->payment->amount,
                    'payment_type' => $booking->payment->payment_type,
                    'status' => $booking->payment->status,
                    'paid_at' => $booking->payment->paid_at?->format('Y-m-d H:i:s'),
                    'expired_at' => $booking->payment->expired_at?->format('Y-m-d H:i:s'),
                ] : null,
                'cancelled_at' => $booking->cancelled_at?->format('Y-m-d H:i:s'),
                'completed_at' => $booking->completed_at?->format('Y-m-d H:i:s'),
                'created_at' => $booking->created_at->format('Y-m-d H:i:s'),
            ],
            'meta' => null,
        ]);
    }

    /**
     * Riwayat booking rental customer
     */
    public function myBookings(Request $request): JsonResponse
    {
        $bookings = $this->rentalService->getCustomerBookings($request->user()->id);

        return response()->json([
            'success' => true,
            'message' => 'Riwayat booking rental berhasil diambil.',
            'data' => $bookings->map(function ($b) {
                return [
                    'id' => $b->id,
                    'booking_code' => $b->booking_code,
                    'purpose' => $b->purpose,
                    'start_date' => $b->start_date->format('Y-m-d'),
                    'end_date' => $b->end_date->format('Y-m-d'),
                    'total_days' => $b->total_days,
                    'total_price' => (float) $b->total_price,
                    'status' => $b->status,
                    'status_label' => $b->status_label,
                    'vehicle' => [
                        'id' => $b->vehicle->id,
                        'plate_number' => $b->vehicle->plate_number,
                        'brand' => $b->vehicle->brand,
                        'model' => $b->vehicle->model,
                    ],
                    'agency' => [
                        'name' => $b->vehicle->agency->agency_name ?? '-',
                    ],
                    'created_at' => $b->created_at->format('Y-m-d H:i:s'),
                ];
            }),
            'meta' => ['total' => $bookings->count()],
        ]);
    }

    /**
     * Batalkan booking rental
     */
    public function cancel(RentalBooking $booking): JsonResponse
    {
        if ($booking->customer_id !== request()->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses.',
                'data' => null,
                'meta' => null,
            ], 403);
        }

        if (!$booking->can_cancel) {
            return response()->json([
                'success' => false,
                'message' => 'Booking tidak dapat dibatalkan.',
                'data' => null,
                'meta' => null,
            ], 422);
        }

        $booking->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        if ($booking->payment && $booking->payment->status === 'pending') {
            $booking->payment->update(['status' => 'expired']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Booking berhasil dibatalkan.',
            'data' => ['status' => 'cancelled'],
            'meta' => null,
        ]);
    }
}