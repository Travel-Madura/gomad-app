<?php
// File: app/Http/Controllers/Web/Agency/VehicleController.php
// Deskripsi: Web Controller untuk manajemen kendaraan agency (Cloudinary)

namespace App\Http\Controllers\Web\Agency;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Services\CloudinaryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VehicleController extends Controller
{
    public function __construct(
        private readonly CloudinaryService $cloudinaryService,
    ) {}

    public function index(): View
    {
        $vehicles = Vehicle::where('agency_id', auth()->user()->agency->id)
            ->latest()
            ->get();
        return view('agency.vehicles.index', compact('vehicles'));
    }

    public function create(): View
    {
        return view('agency.vehicles.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'plate_number' => ['required', 'string', 'max:20', 'unique:vehicles,plate_number'],
            'brand' => ['required', 'string', 'max:50'],
            'model' => ['required', 'string', 'max:50'],
            'year' => ['nullable', 'integer', 'min:2000', 'max:' . (date('Y') + 1)],
            'capacity' => ['required', 'integer', 'min:4', 'max:20'],
            'type' => ['required', 'in:economy,premium'],
            'status' => ['required', 'in:active,maintenance,inactive'],           // 👈 TAMBAHKAN
            'is_rental_available' => ['nullable', 'boolean'],                      // 👈 TAMBAHKAN
            'rental_price_per_km' => ['nullable', 'numeric', 'min:0'],             // 👈 TAMBAHKAN
            'rental_min_price' => ['nullable', 'numeric', 'min:0'],                // 👈 TAMBAHKAN
            'rental_extra_day_price' => ['nullable', 'numeric', 'min:0'],          // 👈 TAMBAHKAN
            'rental_include_driver' => ['nullable', 'boolean'],                    // 👈 TAMBAHKAN
            'rental_driver_price_per_day' => ['nullable', 'numeric', 'min:0'],     // 👈 TAMBAHKAN
            'rental_max_passengers' => ['nullable', 'integer', 'min:1'],           // 👈 TAMBAHKAN
            'vehicle_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $data = $request->only([
            'plate_number', 'brand', 'model', 'year', 'capacity', 'type',
            'status', 'is_rental_available', 'rental_price_per_km',       // 👈 TAMBAHKAN
            'rental_min_price', 'rental_extra_day_price',                   // 👈 TAMBAHKAN
            'rental_include_driver', 'rental_driver_price_per_day',         // 👈 TAMBAHKAN
            'rental_max_passengers',                                        // 👈 TAMBAHKAN
        ]);

        $data['agency_id'] = auth()->user()->agency->id;
        $data['is_active'] = true;

        // Upload foto kendaraan via Cloudinary
        if ($request->hasFile('vehicle_image')) {
            $result = $this->cloudinaryService->upload($request->file('vehicle_image'), 'vehicles');
            $data['vehicle_image'] = $result['url'];
        }

        // Set boolean checkbox
        $data['is_rental_available'] = $request->has('is_rental_available');
        $data['rental_include_driver'] = $request->has('rental_include_driver');

        Vehicle::create($data);
        auth()->user()->agency->increment('fleet_size');

        return redirect()->route('agency.vehicles.index')
            ->with('success', 'Kendaraan berhasil ditambahkan!');
    }

    public function edit(Vehicle $vehicle): View
    {
        if ($vehicle->agency_id !== auth()->user()->agency->id) abort(403);
        return view('agency.vehicles.edit', compact('vehicle'));
    }

    public function update(Request $request, Vehicle $vehicle): RedirectResponse
    {
        if ($vehicle->agency_id !== auth()->user()->agency->id) abort(403);

        $request->validate([
            'plate_number' => ['required', 'string', 'max:20', 'unique:vehicles,plate_number,' . $vehicle->id],
            'brand' => ['required', 'string', 'max:50'],
            'model' => ['required', 'string', 'max:50'],
            'year' => ['nullable', 'integer', 'min:2000', 'max:' . (date('Y') + 1)],
            'capacity' => ['required', 'integer', 'min:4', 'max:20'],
            'type' => ['required', 'in:economy,premium'],
            'status' => ['required', 'in:active,maintenance,inactive'],
            'is_rental_available' => ['nullable'],
            'rental_price_per_km' => ['nullable', 'numeric', 'min:0'],
            'rental_min_price' => ['nullable', 'numeric', 'min:0'],
            'rental_extra_day_price' => ['nullable', 'numeric', 'min:0'],
            'rental_include_driver' => ['nullable'],
            'rental_driver_price_per_day' => ['nullable', 'numeric', 'min:0'],
            'rental_max_passengers' => ['nullable', 'integer', 'min:1'],
            'vehicle_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        // 👇 AMBIL FIELD DASAR + RENTAL
        $data = $request->only([
            'plate_number', 'brand', 'model', 'year', 'capacity', 'type',
            'status',
            'is_rental_available', 'rental_price_per_km', 'rental_min_price',
            'rental_extra_day_price', 'rental_include_driver',
            'rental_driver_price_per_day', 'rental_max_passengers',
        ]);

        // 👇 HANDLE CHECKBOX MANUAL
        $data['is_rental_available'] = $request->has('is_rental_available') && $request->is_rental_available == '1';
        $data['rental_include_driver'] = $request->has('rental_include_driver') && $request->rental_include_driver == '1';

        // 👇 JIKA STATUS BUKAN ACTIVE, MATIKAN RENTAL
        if ($data['status'] !== 'active') {
            $data['is_rental_available'] = false;
        }

        // Upload foto baru via Cloudinary
        if ($request->hasFile('vehicle_image')) {
            if ($vehicle->vehicle_image && str_starts_with($vehicle->vehicle_image, 'http')) {
                $publicId = $this->extractCloudinaryPublicId($vehicle->vehicle_image);
                if ($publicId) $this->cloudinaryService->delete($publicId);
            }
            $result = $this->cloudinaryService->upload($request->file('vehicle_image'), 'vehicles');
            $data['vehicle_image'] = $result['url'];
        }

        $vehicle->update($data);

        return redirect()->route('agency.vehicles.index')
            ->with('success', 'Kendaraan berhasil diupdate!');
    }

    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        if ($vehicle->agency_id !== auth()->user()->agency->id) abort(403);

        $hasActiveSchedules = $vehicle->schedules()
            ->where('departure_date', '>=', now()->toDateString())
            ->where('is_active', true)
            ->exists();

        if ($hasActiveSchedules) {
            return back()->with('error', 'Kendaraan masih memiliki jadwal aktif.');
        }

        // Hapus foto dari Cloudinary
        if ($vehicle->vehicle_image && str_starts_with($vehicle->vehicle_image, 'http')) {
            $publicId = $this->extractCloudinaryPublicId($vehicle->vehicle_image);
            if ($publicId) $this->cloudinaryService->delete($publicId);
        }

        $vehicle->update(['is_active' => false]);
        $vehicle->delete();
        auth()->user()->agency->decrement('fleet_size');

        return back()->with('success', 'Kendaraan berhasil dihapus.');
    }

    /**
     * Extract public_id dari Cloudinary URL
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
