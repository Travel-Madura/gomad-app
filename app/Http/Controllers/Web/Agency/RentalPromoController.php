<?php

namespace App\Http\Controllers\Web\Agency;

use App\Http\Controllers\Controller;
use App\Models\RentalPromo;
use App\Models\Vehicle;
use App\Services\RentalPromoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RentalPromoController extends Controller
{
    public function __construct(
        private readonly RentalPromoService $rentalPromoService,
    ) {}

    public function index(): View
    {
        $agency = auth()->user()->agency;
        $promos = $this->rentalPromoService->getSelectivePromosForAgency();

        $vehicles = Vehicle::where('agency_id', $agency->id)
            ->where('is_rental_available', true)
            ->where('status', 'active')
            ->with('rentalPromos')
            ->get();

        return view('agency.rental-promos.index', compact('promos', 'vehicles'));
    }

    public function attach(Request $request): RedirectResponse
    {
        $request->validate([
            'rental_promo_id' => ['required', 'exists:rental_promos,id'],
            'vehicle_id' => ['required', 'exists:vehicles,id'],
        ]);

        $this->rentalPromoService->attachPromoToVehicle($request->rental_promo_id, $request->vehicle_id);
        return back()->with('success', '✅ Promo dipasang ke kendaraan!');
    }

    public function detach(Vehicle $vehicle, RentalPromo $promo): RedirectResponse
    {
        $this->rentalPromoService->detachPromoFromVehicle($promo->id, $vehicle->id);
        return back()->with('success', 'Promo dilepas.');
    }
}