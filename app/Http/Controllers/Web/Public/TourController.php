<?php

namespace App\Http\Controllers\Web\Public;

use App\Http\Controllers\Controller;
use App\Models\TourBooking;
use App\Models\TourPackage;
use App\Services\TourService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TourController extends Controller
{
    public function __construct(
        private readonly TourService $tourService,
    ) {}

    /**
     * Landing page wisata publik
     */
    public function index(): View
    {
        $packages = $this->tourService->getPublicPackages(9);
        return view('public-pages.tours', compact('packages'));
    }

    /**
     * Detail paket tour publik (by slug)
     */
    public function show(string $slug): View
    {
        $package = $this->tourService->getPublicPackageBySlug($slug);

        if (!$package) {
            abort(404, 'Paket tour tidak ditemukan.');
        }

        $schedules = $this->tourService->getPackageSchedules($package);
        $pickupStops = $this->tourService->getAvailablePickupStops($package);

        // Redirect ke halaman customer jika user sudah login
        if (auth()->check() && auth()->user()->role === 'customer') {
            return view('customer.tour.show', compact('package', 'schedules', 'pickupStops'));
        }

        return view('public-pages.tour-detail', compact('package', 'schedules', 'pickupStops'));
    }

    /**
     * Cek E-Ticket Tour (Public)
     */
    public function eTicketPage(): View
    {
        return view('public-pages.tour-e-ticket');
    }

    /**
     * Cek E-Ticket Tour berdasarkan kode booking
     */
    public function checkETicket(Request $request): View|RedirectResponse
    {
        $request->validate([
            'booking_code' => ['required', 'string', 'max:50'],
        ]);

        $booking = TourBooking::where('booking_code', $request->booking_code)
            ->with([
                'tourSchedule.tourPackage.agency',
                'tourSchedule.vehicle',
                'participants',
                'payment',
                'originStop',
            ])
            ->first();

        if (!$booking) {
            return back()->with('error', 'Kode booking tidak ditemukan.');
        }

        if ($booking->status === 'cancelled') {
            return back()->with('error', 'Booking ini telah dibatalkan.');
        }

        return view('public-pages.tour-e-ticket', compact('booking'));
    }
}