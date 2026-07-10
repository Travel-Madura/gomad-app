<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\TourBooking;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $type = $request->type ?? 'travel';
        $status = $request->status;
        $search = $request->booking_code;

        if ($type === 'tour') {
            $query = \App\Models\TourBooking::with(['tourSchedule.tourPackage.agency', 'customer', 'payment']);
            if ($status) $query->where('status', $status);
            if ($search) $query->where('booking_code', 'like', '%' . $search . '%');
            $bookings = $query->orderBy('created_at', 'desc')->paginate(20);
        } elseif ($type === 'rental') {
            $query = \App\Models\RentalBooking::with(['vehicle.agency', 'customer', 'payment']);
            if ($status) $query->where('status', $status);
            if ($search) $query->where('booking_code', 'like', '%' . $search . '%');
            $bookings = $query->orderBy('created_at', 'desc')->paginate(20);
        } else {
            $query = \App\Models\Booking::with(['schedule.agency', 'schedule.route', 'customer', 'payment']);
            if ($status) $query->where('status', $status);
            if ($search) $query->where('booking_code', 'like', '%' . $search . '%');
            $bookings = $query->orderBy('created_at', 'desc')->paginate(20);
        }

        $totalTravel = \App\Models\Booking::count();
        $totalTour = \App\Models\TourBooking::count();
        $totalRental = \App\Models\RentalBooking::count();

        return view('admin.bookings.index', compact('bookings', 'type', 'totalTravel', 'totalTour', 'totalRental'));
    }

    public function show($booking, Request $request): View
    {
        $type = $request->type ?? 'travel';

        if ($type === 'tour') {
            $booking = TourBooking::with([
                'tourSchedule.tourPackage.agency',
                'tourSchedule.vehicle',
                'tourSchedule.driver',
                'customer',
                'participants',
                'payment',
                'originStop',
            ])->findOrFail($booking);
            
            return view('admin.bookings.show-tour', compact('booking', 'type'));
        }

        $booking = Booking::with([
            'schedule.agency',
            'schedule.route.stops',
            'schedule.vehicle',
            'schedule.driver',
            'customer',
            'originStop',
            'destinationStop',
            'passengers',
            'payment',
            'cashPayment.paymentAgent',
            'review',
        ])->findOrFail($booking);

        return view('admin.bookings.show', compact('booking', 'type'));
    }

    public function approveRefund(Booking $booking): RedirectResponse
    {
        try {
            $paymentService = app(\App\Services\PaymentService::class);
            $result = $paymentService->approveRefund($booking, auth()->user());
            
            if ($result['success']) {
                return back()->with('success', 'Refund berhasil disetujui dan diproses.');
            }
            
            return back()->with('error', $result['message']);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses refund: ' . $e->getMessage());
        }
    }

    public function rejectRefund(Request $request, Booking $booking): RedirectResponse
    {
        $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);
        
        try {
            $paymentService = app(\App\Services\PaymentService::class);
            $paymentService->rejectRefund($booking, auth()->user(), $request->reason);
            
            return back()->with('success', 'Refund ditolak.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }
}