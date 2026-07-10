<?php

namespace App\Http\Controllers\Web\Agency;

use App\Http\Controllers\Controller;
use App\Models\TourPromo;
use App\Models\TourSchedule;
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
     * Daftar promo selektif yang bisa dipasang ke jadwal tour
     */
    public function index(): View
    {
        $agency = auth()->user()->agency;
        $promos = $this->tourPromoService->getSelectivePromosForAgency();

        // Jadwal tour milik agency yang upcoming
        $schedules = TourSchedule::with(['tourPackage', 'tourPromos'])
            ->whereHas('tourPackage', function ($q) use ($agency) {
                $q->where('agency_id', $agency->id);
            })
            ->where('departure_date', '>=', now()->toDateString())
            ->where('is_active', true)
            ->latest()
            ->get();

        return view('agency.tour-promos.index', compact('promos', 'schedules'));
    }

    /**
     * Pasang promo ke jadwal tour
     */
    public function attach(Request $request): RedirectResponse
    {
        $request->validate([
            'tour_promo_id' => ['required', 'integer', 'exists:tour_promos,id'],
            'tour_schedule_id' => ['required', 'integer', 'exists:tour_schedules,id'],
        ]);

        $this->tourPromoService->attachPromoToSchedule(
            $request->tour_promo_id,
            $request->tour_schedule_id
        );

        return back()->with('success', '✅ Promo berhasil dipasang ke jadwal!');
    }

    /**
     * Lepas promo dari jadwal tour
     */
    public function detach(TourSchedule $schedule, TourPromo $promo): RedirectResponse
    {
        $this->tourPromoService->detachPromoFromSchedule($promo->id, $schedule->id);

        return back()->with('success', 'Promo dilepas dari jadwal.');
    }
}