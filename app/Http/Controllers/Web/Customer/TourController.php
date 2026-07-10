<?php

namespace App\Http\Controllers\Web\Customer;

use App\Http\Controllers\Controller;
use App\Models\TourBooking;
use App\Models\TourPackage;
use App\Models\TourSchedule;
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
     * Browse paket tour
     */
    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'duration', 'min_budget', 'max_budget']);
        $packages = $this->tourService->getAvailablePackages($filters);

        return view('customer.tour.index', compact('packages'));
    }

    /**
     * Detail paket tour
     */
    public function show(TourPackage $package): View
    {
        $package->load(['agency', 'stops']);
        $schedules = $this->tourService->getPackageSchedules($package);
        $pickupStops = $this->tourService->getAvailablePickupStops($package);

        return view('customer.tour.show', compact('package', 'schedules', 'pickupStops'));
    }

    /**
     * Form booking tour
     */
    public function createBooking(Request $request): View
    {
        $scheduleId = $request->schedule_id;
        $schedule = TourSchedule::with(['tourPackage.stops', 'vehicle'])->findOrFail($scheduleId);
        $package = $schedule->tourPackage;
        $pickupStops = $this->tourService->getAvailablePickupStops($package);

        return view('customer.tour.create', compact('schedule', 'package', 'pickupStops'));
    }

    /**
     * Simpan booking tour
     */
    public function storeBooking(Request $request): RedirectResponse
    {
        $request->validate([
            'tour_schedule_id' => ['required', 'integer', 'exists:tour_schedules,id'],
            'origin_stop_id' => ['required', 'integer', 'exists:tour_route_stops,id'],
            'pickup_address' => ['required', 'string', 'max:500'],
            'group_name' => ['required', 'string', 'max:200'],
            'participants' => ['required', 'array', 'min:1', 'max:100'],
            'participants.*.name' => ['required', 'string', 'max:100'],
            'participants.*.participant_type' => ['required', 'in:adult,child'],
            'special_requests' => ['nullable', 'string', 'max:1000'],
        ], [
            'group_name.required' => 'Nama instansi/lembaga/rombongan wajib diisi.',
            'participants.min' => 'Minimal 1 peserta.',
        ]);

        try {
            $data = $request->all();
            $data['customer_id'] = auth()->id();

            $booking = $this->tourService->createBooking($data);

            return redirect()->route('customer.tour.detail', $booking)
                ->with('success', '✅ Booking tour berhasil! Silakan pilih metode pembayaran.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat booking: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Detail booking tour
     */
    public function detail(TourBooking $booking): View
    {
        if ($booking->customer_id !== auth()->id()) {
            abort(403);
        }

        $booking->load([
            'tourSchedule.tourPackage.agency',
            'tourSchedule.vehicle',
            'tourSchedule.driver',
            'participants',
            'payment',
            'originStop',
        ]);

        return view('customer.tour.detail', compact('booking'));
    }

    /**
     * Riwayat booking tour
     */
    public function myBookings(): View
    {
        $bookings = $this->tourService->getCustomerBookings(auth()->id());
        return view('customer.tour.my-bookings', compact('bookings'));
    }

    /**
     * Batalkan booking
     */
    public function cancel(TourBooking $booking): RedirectResponse
    {
        if ($booking->customer_id !== auth()->id()) {
            abort(403);
        }

        try {
            $this->tourService->cancelBooking($booking);
            return back()->with('success', 'Booking dibatalkan.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Proses pembayaran tour
     */
    public function payProcess(Request $request, TourBooking $booking): RedirectResponse
    {
        if ($booking->customer_id !== auth()->id()) {
            abort(403);
        }

        if ($booking->status !== 'pending') {
            return back()->with('error', 'Booking sudah tidak dalam status pending.');
        }

        $request->validate([
            'payment_method' => ['required', 'in:midtrans,cash,cod'],
        ]);

        $method = $request->payment_method;

        try {
            $tourPaymentService = app(\App\Services\TourPaymentService::class);

            if ($method === 'midtrans') {
                $payment = $tourPaymentService->createPayment($booking);
                $payment->update(['payment_type' => 'midtrans']);
                
                return redirect()->route('customer.tour.detail', $booking)
                    ->with('success', 'Silakan klik tombol BAYAR SEKARANG untuk menyelesaikan pembayaran.');
            } 
            elseif ($method === 'cash') {
                $payment = $tourPaymentService->createPayment($booking);
                $payment->update([
                    'payment_type' => 'cash',
                    'payment_detail' => [
                        'payment_code' => 'WT-' . now()->format('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(6)),
                    ],
                ]);
                
                return redirect()->route('customer.tour.detail', $booking)
                    ->with('success', 'Kode bayar berhasil dibuat! Tunjukkan ke Warung GoMad terdekat.');
            }
            elseif ($method === 'cod') {
                $payment = $tourPaymentService->createPayment($booking);
                $payment->update([
                    'payment_type' => 'cod',
                    'status' => 'cod_pending',
                ]);
                
                $booking->update(['status' => \App\Enums\TourBookingStatus::CONFIRMED->value]);
                
                return redirect()->route('customer.tour.detail', $booking)
                    ->with('success', 'Pembayaran COD dipilih. Bayar tunai ke sopir saat penjemputan.');
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }

        return back()->with('error', 'Metode pembayaran tidak valid.');
    }

    /**
     * Apply promo ke booking (dari halaman detail)
     */
    public function applyPromo(Request $request, TourBooking $booking): RedirectResponse
    {
        if ($booking->customer_id !== auth()->id()) {
            abort(403);
        }

        // Hanya bisa apply promo jika belum bayar
        if ($booking->payment && $booking->payment->status !== 'pending') {
            return back()->with('error', 'Tidak dapat mengubah promo setelah pembayaran.');
        }

        $request->validate([
            'promo_id' => ['nullable', 'integer', 'exists:tour_promos,id'],
        ]);

        try {
            $tourPromoService = app(\App\Services\TourPromoService::class);
            $paymentMethod = $booking->payment->payment_type ?? null;

            // Reset diskon dulu
            $booking->update([
                'total_price' => (float) $booking->base_price + (float) $booking->service_fee + (float) $booking->platform_fee,
                'discount_amount' => 0,
            ]);

            // Hapus usage promo lama
            \App\Models\TourPromoUsage::where('tour_booking_id', $booking->id)->delete();

            // Jika pilih promo baru
            if ($request->filled('promo_id')) {
                $promo = \App\Models\TourPromo::find($request->promo_id);

                if (!$promo || !$promo->isActiveNow()) {
                    return back()->with('error', 'Promo tidak valid atau sudah kadaluarsa.');
                }

                // Cek apakah customer sudah pernah pakai
                if (!$tourPromoService->canUsePromo(auth()->user(), $promo)) {
                    return back()->with('error', 'Anda sudah pernah menggunakan promo ini.');
                }

                // Cek metode pembayaran
                if ($paymentMethod && !$promo->isApplicableFor($paymentMethod)) {
                    return back()->with('error', 
                        'Promo "' . $promo->name . '" tidak berlaku untuk metode pembayaran yang dipilih.'
                    );
                }

                // Hitung diskon
                $discount = $tourPromoService->calculateDiscount($promo, (float) $booking->base_price);

                if ($discount > 0) {
                    // Update total harga
                    $newTotal = max(0, (float) $booking->total_price - $discount);
                    $booking->update([
                        'total_price' => $newTotal,
                        'discount_amount' => $discount,
                    ]);

                    // Catat pemakaian
                    \App\Models\TourPromoUsage::create([
                        'tour_promo_id' => $promo->id,
                        'user_id' => auth()->id(),
                        'tour_booking_id' => $booking->id,
                        'discount_amount' => $discount,
                    ]);

                    return back()->with('success', 
                        '✅ Promo "' . $promo->name . '" berhasil diterapkan! Diskon: Rp ' . number_format($discount, 0, ',', '.')
                    );
                } else {
                    return back()->with('error', 'Total pembelian belum memenuhi minimal pembelian promo.');
                }
            }

            return back()->with('success', 'Promo dihapus. Harga kembali normal.');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menerapkan promo: ' . $e->getMessage());
        }
    }

}