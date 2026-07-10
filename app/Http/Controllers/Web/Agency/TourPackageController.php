<?php

namespace App\Http\Controllers\Web\Agency;

use App\Http\Controllers\Controller;
use App\Models\TourPackage;
use App\Models\TourSchedule;
use App\Services\TourService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TourPackageController extends Controller
{
    public function __construct(
        private readonly TourService $tourService,
    ) {}

    // ═══════════════════════════════════════════════════════
    // PACKAGES
    // ═══════════════════════════════════════════════════════

    /**
     * Daftar paket tour agency
     */
    public function index(): View
    {
        $packages = $this->tourService->getAgencyPackages(auth()->user()->agency->id);
        $activeCount = $packages->where('is_active', true)->count();
        $totalSchedules = TourSchedule::whereIn('tour_package_id', $packages->pluck('id'))
            ->where('departure_date', '>=', now()->toDateString())
            ->count();

        return view('agency.tours.index', compact('packages', 'activeCount', 'totalSchedules'));
    }

    /**
     * Form buat paket tour baru
     */
    public function create(): View
    {
        return view('agency.tours.create');
    }

    /**
     * Simpan paket tour baru
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:5000'],
            'duration_days' => ['required', 'integer', 'min:1', 'max:30'],
            'duration_nights' => ['required', 'integer', 'min:0', 'max:30'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'gallery_photos.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'stops' => ['required', 'array', 'min:2'],
            'stops.*.city_name' => ['required', 'string', 'max:100'],
            'itinerary' => ['nullable', 'json'],
            'includes' => ['nullable', 'string'],
            'excludes' => ['nullable', 'string'],
        ], [
            'stops.min' => 'Minimal 2 titik pemberhentian (penjemputan dan tujuan).',
        ]);

        try {
            $data = $request->all();
            $data['agency_id'] = auth()->user()->agency->id;

            $this->tourService->createPackage($data);

            return redirect()->route('agency.tours.index')
                ->with('success', '✅ Paket tour berhasil dibuat! Sekarang tambahkan jadwal keberangkatan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat paket: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Detail paket tour
     */
    public function show(TourPackage $package): View
    {
        if ($package->agency_id !== auth()->user()->agency->id) {
            abort(403);
        }

        $package = $this->tourService->getPackageDetail($package);
        $vehicles = auth()->user()->agency->vehicles()->where('is_active', true)->get();
        $drivers = auth()->user()->agency->drivers()->where('is_active', true)->get();

        return view('agency.tours.show', compact('package', 'vehicles', 'drivers'));
    }

    /**
     * Form edit paket tour
     */
    public function edit(TourPackage $package): View
    {
        if ($package->agency_id !== auth()->user()->agency->id) {
            abort(403);
        }

        $package->load('stops');
        return view('agency.tours.edit', compact('package'));
    }

    /**
     * Update paket tour
     */
    public function update(Request $request, TourPackage $package): RedirectResponse
    {
        if ($package->agency_id !== auth()->user()->agency->id) {
            abort(403);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:5000'],
            'duration_days' => ['required', 'integer', 'min:1', 'max:30'],
            'duration_nights' => ['required', 'integer', 'min:0', 'max:30'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'stops' => ['nullable', 'array', 'min:2'],
            'stops.*.city_name' => ['required', 'string', 'max:100'],
        ]);

        try {
            $this->tourService->updatePackage($package, $request->all());
            return redirect()->route('agency.tours.show', $package)
                ->with('success', '✅ Paket tour berhasil diupdate!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal update: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Hapus/nonaktifkan paket tour
     */
    public function destroy(TourPackage $package): RedirectResponse
    {
        if ($package->agency_id !== auth()->user()->agency->id) {
            abort(403);
        }

        $hasActiveSchedules = $package->schedules()
            ->where('departure_date', '>=', now()->toDateString())
            ->where('is_active', true)
            ->exists();

        if ($hasActiveSchedules) {
            return back()->with('error', 'Paket masih memiliki jadwal aktif. Nonaktifkan jadwal terlebih dahulu.');
        }

        $package->update(['is_active' => false]);
        $package->delete();

        return redirect()->route('agency.tours.index')
            ->with('success', 'Paket tour dinonaktifkan.');
    }

    // ═══════════════════════════════════════════════════════
    // SCHEDULES
    // ═══════════════════════════════════════════════════════

    /**
     * Form buat jadwal tour
     */
    public function createSchedule(TourPackage $package): View
    {
        if ($package->agency_id !== auth()->user()->agency->id) {
            abort(403);
        }

        $vehicles = auth()->user()->agency->vehicles()->where('is_active', true)->get();
        $drivers = auth()->user()->agency->drivers()->where('is_active', true)->get();

        return view('agency.tours.schedules.create', compact('package', 'vehicles', 'drivers'));
    }

    /**
     * Simpan jadwal tour
     */
    public function storeSchedule(Request $request, TourPackage $package): RedirectResponse
    {
        if ($package->agency_id !== auth()->user()->agency->id) {
            abort(403);
        }

        $request->validate([
            'vehicle_id' => ['required', 'integer', 'exists:vehicles,id'],
            'driver_id' => ['nullable', 'integer', 'exists:users,id'],
            'departure_date' => ['required', 'date', 'after_or_equal:today'],
            'departure_time' => ['required', 'date_format:H:i'],
            'return_date' => ['nullable', 'date', 'after_or_equal:departure_date'],
            'return_time' => ['nullable', 'date_format:H:i'],
            'base_price' => ['required', 'numeric', 'min:1000'],
            'child_price' => ['nullable', 'numeric', 'min:0'],
            'max_participants' => ['required', 'integer', 'min:1', 'max:100'],
            'min_participants' => ['nullable', 'integer', 'min:1'],
        ]);

        try {
            $data = $request->all();
            $data['tour_package_id'] = $package->id;

            $this->tourService->createSchedule($data);

            return redirect()->route('agency.tours.show', $package)
                ->with('success', '✅ Jadwal tour berhasil dibuat!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat jadwal: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Update jadwal tour
     */
    public function updateSchedule(Request $request, TourSchedule $schedule): RedirectResponse
    {
        $package = $schedule->tourPackage;
        if ($package->agency_id !== auth()->user()->agency->id) {
            abort(403);
        }

        $request->validate([
            'vehicle_id' => ['required', 'integer', 'exists:vehicles,id'],
            'base_price' => ['required', 'numeric', 'min:1000'],
            'max_participants' => ['required', 'integer', 'min:1'],
        ]);

        try {
            $this->tourService->updateSchedule($schedule, $request->all());
            return redirect()->route('agency.tours.show', $package)
                ->with('success', 'Jadwal diupdate.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Nonaktifkan jadwal
     */
    public function destroySchedule(TourSchedule $schedule): RedirectResponse
    {
        $package = $schedule->tourPackage;
        if ($package->agency_id !== auth()->user()->agency->id) {
            abort(403);
        }

        $hasBookings = $schedule->bookings()->whereNotIn('status', ['cancelled'])->exists();
        if ($hasBookings) {
            return back()->with('error', 'Jadwal memiliki booking aktif.');
        }

        $schedule->update(['is_active' => false]);
        $schedule->delete();

        return redirect()->route('agency.tours.show', $package)
            ->with('success', 'Jadwal dihapus.');
    }
}