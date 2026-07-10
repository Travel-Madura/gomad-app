<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\TourPackageResource;
use App\Http\Resources\Api\TourScheduleResource;
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
     * Daftar paket tour publik
     */
    public function index(Request $request): JsonResponse
    {
        $packages = $this->tourService->getPublicPackages($request->limit ?? 12);

        return response()->json([
            'success' => true,
            'message' => 'Daftar paket tour berhasil diambil.',
            'data' => TourPackageResource::collection($packages),
            'meta' => [
                'current_page' => $packages->currentPage(),
                'last_page' => $packages->lastPage(),
                'total' => $packages->total(),
            ],
        ]);
    }

    /**
     * Detail paket tour by slug
     */
    public function show(string $slug): JsonResponse
    {
        $package = $this->tourService->getPublicPackageBySlug($slug);

        if (!$package) {
            return response()->json([
                'success' => false,
                'message' => 'Paket tour tidak ditemukan.',
                'data' => null,
                'meta' => null,
            ], 404);
        }

        $schedules = $this->tourService->getPackageSchedules($package);
        $pickupStops = $this->tourService->getAvailablePickupStops($package);

        return response()->json([
            'success' => true,
            'message' => 'Detail paket tour berhasil diambil.',
            'data' => [
                'package' => new TourPackageResource($package),
                'schedules' => TourScheduleResource::collection($schedules),
                'pickup_stops' => $pickupStops,
            ],
            'meta' => null,
        ]);
    }
}