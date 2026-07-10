<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\TourBookingResource;
use App\Http\Resources\Api\TourPackageResource;
use App\Http\Resources\Api\TourScheduleResource;
use App\Models\TourBooking;
use App\Models\TourPackage;
use App\Services\TourService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TourController extends Controller
{
    public function __construct(
        private readonly TourService $tourService,
    ) {}

    /**
     * Daftar paket tour tersedia
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'duration', 'min_budget', 'max_budget']);
        $packages = $this->tourService->getAvailablePackages($filters);

        return response()->json([
            'success' => true,
            'message' => 'Daftar paket tour berhasil diambil.',
            'data' => TourPackageResource::collection($packages),
            'meta' => ['total' => $packages->count()],
        ]);
    }

    /**
     * Detail paket tour
     */
    public function show(TourPackage $package): JsonResponse
    {
        $package->load(['agency', 'stops']);
        $schedules = $this->tourService->getPackageSchedules($package);
        $pickupStops = $this->tourService->getAvailablePickupStops($package);

        return response()->json([
            'success' => true,
            'message' => 'Detail paket berhasil diambil.',
            'data' => [
                'package' => new TourPackageResource($package),
                'schedules' => TourScheduleResource::collection($schedules),
                'pickup_stops' => $pickupStops,
            ],
            'meta' => null,
        ]);
    }

    /**
     * Titik jemput yang tersedia
     */
    public function pickupStops(TourPackage $package): JsonResponse
    {
        $stops = $this->tourService->getAvailablePickupStops($package);

        return response()->json([
            'success' => true,
            'message' => 'Daftar titik jemput berhasil diambil.',
            'data' => $stops,
            'meta' => ['total' => count($stops)],
        ]);
    }

    /**
     * Buat booking tour
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'tour_schedule_id' => ['required', 'integer', 'exists:tour_schedules,id'],
            'origin_stop_id' => ['required', 'integer', 'exists:tour_route_stops,id'],
            'pickup_address' => ['required', 'string', 'max:500'],
            'group_name' => ['required', 'string', 'max:200'],
            'participants' => ['required', 'array', 'min:1'],
            'participants.*.name' => ['required', 'string', 'max:100'],
            'participants.*.participant_type' => ['required', 'in:adult,child'],
        ]);

        try {
            $data = $request->all();
            $data['customer_id'] = $request->user()->id;

            $booking = $this->tourService->createBooking($data);

            return response()->json([
                'success' => true,
                'message' => 'Booking tour berhasil dibuat.',
                'data' => new TourBookingResource($booking),
                'meta' => null,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null,
                'meta' => null,
            ], 422);
        }
    }

    /**
     * Riwayat booking tour customer
     */
    public function myBookings(Request $request): JsonResponse
    {
        $bookings = $this->tourService->getCustomerBookings($request->user()->id);

        return response()->json([
            'success' => true,
            'message' => 'Riwayat booking tour berhasil diambil.',
            'data' => TourBookingResource::collection($bookings),
            'meta' => ['total' => $bookings->count()],
        ]);
    }

    /**
     * Detail booking tour
     */
    public function showBooking(TourBooking $booking): JsonResponse
    {
        if ($booking->customer_id !== request()->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses.',
                'data' => null,
                'meta' => null,
            ], 403);
        }

        $booking->load([
            'tourSchedule.tourPackage.agency',
            'tourSchedule.vehicle',
            'participants',
            'payment',
            'originStop',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Detail booking berhasil diambil.',
            'data' => new TourBookingResource($booking),
            'meta' => null,
        ]);
    }

    /**
     * Batalkan booking
     */
    public function cancel(TourBooking $booking): JsonResponse
    {
        if ($booking->customer_id !== request()->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses.',
                'data' => null,
                'meta' => null,
            ], 403);
        }

        try {
            $this->tourService->cancelBooking($booking);

            return response()->json([
                'success' => true,
                'message' => 'Booking berhasil dibatalkan.',
                'data' => new TourBookingResource($booking->fresh()),
                'meta' => null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null,
                'meta' => null,
            ], 422);
        }
    }
}