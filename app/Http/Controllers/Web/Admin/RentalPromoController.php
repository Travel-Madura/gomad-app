<?php

namespace App\Http\Controllers\Web\Admin;

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
        $promos = $this->rentalPromoService->getAllPromos();
        $activeCount = RentalPromo::active()->count();
        return view('admin.rental-promos.index', compact('promos', 'activeCount'));
    }

    public function create(): View
    {
        $vehicles = Vehicle::where('is_rental_available', true)->where('status', 'active')->get();
        return view('admin.rental-promos.create', compact('vehicles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'in:general,selective'],
            'discount_percent' => ['required', 'numeric', 'min:1', 'max:100'],
            'max_discount' => ['required', 'numeric', 'min:0'],
            'min_purchase' => ['nullable', 'numeric', 'min:0'],
            'vehicle_id' => ['nullable', 'exists:vehicles,id'],
            'applicable_payment_methods' => ['nullable', 'array'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'cost_bearer' => ['required', 'in:platform,agency,shared'],
        ]);

        $data = $request->all();
        $data['created_by'] = auth()->id();
        $data['is_active'] = true;

        if ($request->has('applicable_payment_methods') && !empty($request->applicable_payment_methods)) {
            $data['applicable_payment_methods'] = implode(',', $request->applicable_payment_methods);
        } else {
            $data['applicable_payment_methods'] = null;
        }

        if ($data['cost_bearer'] === 'platform') {
            $data['platform_share_percent'] = 100; $data['agency_share_percent'] = 0;
        } elseif ($data['cost_bearer'] === 'agency') {
            $data['platform_share_percent'] = 0; $data['agency_share_percent'] = 100;
        } elseif ($data['cost_bearer'] === 'shared') {
            $data['platform_share_percent'] = $request->platform_share ?? 50;
            $data['agency_share_percent'] = $request->agency_share ?? 50;
        }

        RentalPromo::create($data);

        return redirect()->route('admin.rental-promos.index')
            ->with('success', '✅ Promo rental berhasil dibuat!');
    }

    public function edit(RentalPromo $promo): View
    {
        $vehicles = Vehicle::where('is_rental_available', true)->where('status', 'active')->get();
        return view('admin.rental-promos.edit', compact('promo', 'vehicles'));
    }

    public function update(Request $request, RentalPromo $promo): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'discount_percent' => ['required', 'numeric', 'min:1', 'max:100'],
            'max_discount' => ['required', 'numeric', 'min:0'],
            'min_purchase' => ['nullable', 'numeric', 'min:0'],
            'applicable_payment_methods' => ['nullable', 'array'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data = $request->all();
        if ($request->has('applicable_payment_methods') && !empty($request->applicable_payment_methods)) {
            $data['applicable_payment_methods'] = implode(',', $request->applicable_payment_methods);
        } else {
            $data['applicable_payment_methods'] = null;
        }

        $promo->update($data);
        return redirect()->route('admin.rental-promos.index')->with('success', '✅ Promo diupdate!');
    }

    public function destroy(RentalPromo $promo): RedirectResponse
    {
        $promo->delete();
        return back()->with('success', 'Promo dihapus.');
    }
}