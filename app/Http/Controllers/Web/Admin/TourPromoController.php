<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\TourPackage;
use App\Models\TourPromo;
use App\Services\TourPromoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TourPromoController extends Controller
{
    public function __construct(
        private readonly TourPromoService $tourPromoService,
    ) {}

    /**
     * Daftar promo wisata
     */
    public function index(): View
    {
        $promos = $this->tourPromoService->getAllPromos();
        $activeCount = TourPromo::active()->count();

        return view('admin.tour-promos.index', compact('promos', 'activeCount'));
    }

    /**
     * Form buat promo wisata
     */
    public function create(): View
    {
        $packages = TourPackage::where('is_active', true)->get();
        return view('admin.tour-promos.create', compact('packages'));
    }

    /**
     * Simpan promo wisata
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'in:general,selective'],
            'description' => ['nullable', 'string', 'max:500'],
            'discount_percent' => ['required', 'numeric', 'min:1', 'max:100'],
            'max_discount' => ['required', 'numeric', 'min:0'],
            'min_purchase' => ['nullable', 'numeric', 'min:0'],
            'tour_package_id' => ['nullable', 'integer', 'exists:tour_packages,id'],
            'applicable_payment_methods' => ['nullable', 'array'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'cost_bearer' => ['required', 'in:platform,agency,shared'],
        ]);

        $data = $request->all();
        $data['created_by'] = auth()->id();
        $data['is_active'] = true;

        // Proses applicable_payment_methods
        if ($request->has('applicable_payment_methods') && !empty($request->applicable_payment_methods)) {
            $data['applicable_payment_methods'] = implode(',', $request->applicable_payment_methods);
        } else {
            $data['applicable_payment_methods'] = null;
        }

        // Set share percentages
        if ($data['cost_bearer'] === 'platform') {
            $data['platform_share_percent'] = 100;
            $data['agency_share_percent'] = 0;
        } elseif ($data['cost_bearer'] === 'agency') {
            $data['platform_share_percent'] = 0;
            $data['agency_share_percent'] = 100;
        } elseif ($data['cost_bearer'] === 'shared') {
            $data['platform_share_percent'] = $request->platform_share ?? 50;
            $data['agency_share_percent'] = $request->agency_share ?? 50;
        }

        TourPromo::create($data);

        return redirect()->route('admin.tour-promos.index')
            ->with('success', '✅ Promo wisata berhasil dibuat!');
    }

    /**
     * Form edit promo wisata
     */
    public function edit(TourPromo $promo): View
    {
        $packages = TourPackage::where('is_active', true)->get();
        return view('admin.tour-promos.edit', compact('promo', 'packages'));
    }

    /**
     * Update promo wisata
     */
    public function update(Request $request, TourPromo $promo): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
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

        return redirect()->route('admin.tour-promos.index')
            ->with('success', '✅ Promo wisata berhasil diupdate!');
    }

    /**
     * Hapus promo wisata
     */
    public function destroy(TourPromo $promo): RedirectResponse
    {
        $promo->delete();
        return back()->with('success', 'Promo wisata berhasil dihapus.');
    }
}