<?php

namespace App\Http\Controllers\Web\Public;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\Agency;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RentalController extends Controller
{
    /**
     * Halaman publik sewa kendaraan
     */
    public function index(Request $request): View
    {
        $query = Vehicle::with('agency')
            ->availableForRental();

        // Filter kapasitas
        if ($request->passengers) {
            $query->where('capacity', '>=', $request->passengers)
                ->where('rental_max_passengers', '>=', $request->passengers);
        }

        // Filter include driver
        if ($request->include_driver == '1') {
            $query->where('rental_include_driver', true);
        }

        // Filter agency
        if ($request->agency_id) {
            $query->where('agency_id', $request->agency_id);
        }

        // Filter bulan (ketersediaan)
        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;

        $vehicles = $query->latest()->get();

        // Filter kendaraan yang punya tanggal tersedia di bulan ini
        if ($request->month) {
            $vehicles = $vehicles->filter(function ($vehicle) use ($month, $year) {
                $availableDates = $vehicle->getAvailableDates($month, $year);
                return !empty($availableDates);
            })->values();
        }

        $agencies = Agency::where('is_verified', true)->orderBy('agency_name')->get();

        // Jika user sudah login sebagai customer, redirect ke halaman customer
        if (auth()->check() && auth()->user()->role === 'customer') {
            return redirect()->route('customer.rental.index', $request->query());
        }

        return view('public-pages.rental', compact('vehicles', 'agencies'));
    }
}