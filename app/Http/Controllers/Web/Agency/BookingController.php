<?php
// File: app/Http/Controllers/Web/Agency/BookingController.php
// Deskripsi: Web Controller untuk manajemen booking oleh agency

namespace App\Http\Controllers\Web\Agency;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $agency = auth()->user()->agency;
        $type = $request->type ?? 'travel';

        if ($type === 'tour') {
            $query = \App\Models\TourBooking::with(['tourSchedule.tourPackage', 'customer', 'payment'])
                ->whereHas('tourSchedule.tourPackage', function ($q) use ($agency) {
                    $q->where('agency_id', $agency->id);
                });
        } elseif ($type === 'rental') {
            $query = \App\Models\RentalBooking::with(['vehicle', 'customer', 'payment'])
                ->whereHas('vehicle', function ($q) use ($agency) {
                    $q->where('agency_id', $agency->id);
                });
        } else {
            $query = Booking::with(['schedule.route', 'customer', 'originStop', 'destinationStop', 'payment'])
                ->whereHas('schedule', function ($q) use ($agency) {
                    $q->where('agency_id', $agency->id);
                });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('agency.bookings.index', compact('bookings', 'type'));
    }

    public function show($booking, Request $request): View
    {
        $type = $request->type ?? 'travel';
        $agency = auth()->user()->agency;

        if ($type === 'tour') {
            $booking = \App\Models\TourBooking::with([
                'tourSchedule.tourPackage', 'customer', 'participants', 'payment', 'originStop'
            ])->findOrFail($booking);
        } elseif ($type === 'rental') {
            $booking = \App\Models\RentalBooking::with([
                'vehicle.agency', 'customer', 'payment'
            ])->findOrFail($booking);
            
            return view('agency.bookings.show-rental', compact('booking', 'type'));
        } else {
            $booking = Booking::with([
                'schedule.route.stops', 'customer', 'originStop', 'destinationStop',
                'passengers', 'payment', 'cashPayment', 'review',
            ])->findOrFail($booking);
        }

        return view('agency.bookings.show', compact('booking', 'type'));
    }

    public function updateStatus(Request $request, Booking $booking): RedirectResponse
    {
        $agency = auth()->user()->agency;
        
        if ($booking->schedule->agency_id !== $agency->id) {
            abort(403);
        }

        $request->validate([
            'status' => ['required', 'in:confirmed,on_going,completed,cancelled'],
        ]);

        $newStatus = $request->status;
        $updateData = ['status' => $newStatus];

        if ($newStatus === 'cancelled') {
            $updateData['cancelled_at'] = now();
        }

        if ($newStatus === 'completed') {
            $updateData['completed_at'] = now();
            app(\App\Services\WalletService::class)->releaseFunds($booking);
            $agency->increment('total_bookings');
        }

        $booking->update($updateData);

        return back()->with('success', 'Status diupdate.');
    }

}

// End of file