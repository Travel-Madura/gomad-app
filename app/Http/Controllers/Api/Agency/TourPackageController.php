<?php

namespace App\Http\Controllers\Api\Agency;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\TourPackageResource;
use App\Http\Resources\Api\TourScheduleResource;
use App\Models\TourPackage;
use App\Models\TourSchedule;
use App\Services\TourService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TourPackageController extends Controller
{
    public function __construct(
        private readonly TourService $tourService,
    ) {}

    /**
     * Daftar paket tour agency
     */
    public function index(Request $request): JsonResponse
    {
        $packages = $this->tourService->getAgencyPackages($request->user()->agency->id);

        return response()->json([
            'success' => true,
            'message' => 'Daftar paket tour berhasil diambil.',
            'data' => TourPackageResource::collection($packages),
            'meta' => ['total' => $packages->count()],
        ]);
    }

    /**
     * Buat paket tour
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:5000'],
            'duration_days' => ['required', 'integer', 'min:1', 'max:30'],
            'duration_nights' => ['required', 'integer', 'min:0', 'max:30'],
            'stops' => ['required', 'array', 'min:2'],
            'stops.*.city_name' => ['required', 'string', 'max:100'],
        ]);

        try {
            $data = $request->all();
            $data['agency_id'] = $request->user()->agency->id;

            $package = $this->tourService->createPackage($data);

            return response()->json([
                'success' => true,
                'message' => 'Paket tour berhasil dibuat.',
                'data' => new TourPackageResource($package),
                'meta' => null,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat paket: ' . $e->getMessage(),
                'data' => null,
                'meta' => null,
            ], 422);
        }
    }

    /**
     * Detail paket tour
     */
    public function show(TourPackage $package): JsonResponse
    {
        $package = $this->tourService->getPackageDetail($package);

        return response()->json([
            'success' => true,
            'message' => 'Detail paket berhasil diambil.',
            'data' => new TourPackageResource($package),
            'meta' => null,
        ]);
    }

    /**
     * Update paket tour
     */
    public function update(Request $request, TourPackage $package): JsonResponse
    {
        try {
            $package = $this->tourService->updatePackage($package, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Paket berhasil diupdate.',
                'data' => new TourPackageResource($package),
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

    /**
     * Hapus paket tour
     */
    public function destroy(TourPackage $package): JsonResponse
    {
        $package->update(['is_active' => false]);
        $package->delete();

        return response()->json([
            'success' => true,
            'message' => 'Paket dinonaktifkan.',
            'data' => null,
            'meta' => null,
        ]);
    }

    /**
     * Buat jadwal tour
     */
    public function storeSchedule(Request $request, TourPackage $package): JsonResponse
    {
        $request->validate([
            'vehicle_id' => ['required', 'integer', 'exists:vehicles,id'],
            'departure_date' => ['required', 'date', 'after_or_equal:today'],
            'departure_time' => ['required', 'date_format:H:i'],
            'base_price' => ['required', 'numeric', 'min:1000'],
            'max_participants' => ['required', 'integer', 'min:1'],
        ]);

        try {
            $data = $request->all();
            $data['tour_package_id'] = $package->id;

            $schedule = $this->tourService->createSchedule($data);

            return response()->json([
                'success' => true,
                'message' => 'Jadwal tour berhasil dibuat.',
                'data' => new TourScheduleResource($schedule),
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
}