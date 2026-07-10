<?php

namespace App\Http\Controllers\Web\Customer;

use App\Http\Controllers\Controller;
use App\Models\RentalBooking;
use App\Models\RentalPayment;
use App\Models\Vehicle;
use App\Services\RentalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Http;

class RentalController extends Controller
{
    public function __construct(
        private readonly RentalService $rentalService,
    ) {}

    // ═══════════════════════════════════════════════════════
    // BROWSE
    // ═══════════════════════════════════════════════════════

    /**
     * Browse kendaraan tersedia untuk rental
     */
    public function index(Request $request): View
    {
        $filters = $request->only(['passengers', 'include_driver', 'agency_id', 'max_price', 'month', 'year']);
        $filters['month'] = $request->month ?? now()->month;
        $filters['year'] = $request->year ?? now()->year;

        $vehicles = $this->rentalService->searchAvailableVehicles($filters);
        $agencies = \App\Models\Agency::where('is_verified', true)->orderBy('agency_name')->get();

        return view('customer.rental.index', compact('vehicles', 'agencies', 'filters'));
    }

    /**
     * Form booking rental
     */
    public function create(Request $request): View
    {
        $vehicle = Vehicle::with('agency')->findOrFail($request->vehicle_id);

        // Validasi kendaraan tersedia untuk rental
        if (!$vehicle->is_rental_available || $vehicle->status !== 'active') {
            abort(404, 'Kendaraan tidak tersedia untuk disewa.');
        }

        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;

        $availableDates = $vehicle->getAvailableDates($month, $year);

        if (empty($availableDates)) {
            return back()->with('error', 'Kendaraan tidak tersedia di bulan ini.');
        }

        return view('customer.rental.create', compact('vehicle', 'availableDates'));
    }

    /**
     * Simpan booking rental
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'purpose' => ['required', 'string', 'max:200'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'pickup_address' => ['required', 'string', 'max:500'],
            'destination_address' => ['required', 'string', 'max:500'],
            'max_passengers' => ['required', 'integer', 'min:1'],
            'special_notes' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $data = $request->all();
            $data['customer_id'] = auth()->id();

            $booking = $this->rentalService->createBooking($data);

            return redirect()->route('customer.rental.detail', $booking)
                ->with('success', '✅ Booking rental berhasil! Silakan pilih metode pembayaran.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat booking: ' . $e->getMessage())->withInput();
        }
    }

    // ═══════════════════════════════════════════════════════
    // DETAIL & PAYMENT
    // ═══════════════════════════════════════════════════════

    /**
     * Detail booking rental
     */
    public function detail(RentalBooking $booking): View
    {
        if ($booking->customer_id !== auth()->id()) {
            abort(403);
        }

        $booking->load(['vehicle.agency', 'payment']);

        return view('customer.rental.detail', compact('booking'));
    }

    /**
     * Proses pembayaran — pilih metode
     */
    public function payProcess(Request $request, RentalBooking $booking): RedirectResponse
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

        // Hapus payment lama jika ada
        if ($booking->payment) {
            $booking->payment->delete();
        }

        // Hitung komisi
        $commission = $booking->total_price * 0.05;
        $agencyRevenue = $booking->total_price - $commission;

        try {
            if ($method === 'midtrans') {
                RentalPayment::create([
                    'rental_booking_id' => $booking->id,
                    'amount' => $booking->total_price,
                    'commission' => $commission,
                    'agency_revenue' => $agencyRevenue,
                    'payment_type' => 'midtrans',
                    'status' => 'pending',
                    'expired_at' => now()->addMinutes(30),
                ]);

                return redirect()->route('customer.rental.detail', $booking)
                    ->with('success', 'Silakan klik tombol BAYAR SEKARANG untuk menyelesaikan pembayaran.');
            }
            elseif ($method === 'cash') {
                $paymentCode = 'WR-' . now()->format('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(6));

                RentalPayment::create([
                    'rental_booking_id' => $booking->id,
                    'amount' => $booking->total_price,
                    'commission' => $commission,
                    'agency_revenue' => $agencyRevenue,
                    'payment_type' => 'cash',
                    'status' => 'pending',
                    'payment_detail' => json_encode(['payment_code' => $paymentCode]),
                    'expired_at' => now()->addHours(24),
                ]);

                return redirect()->route('customer.rental.detail', $booking)
                    ->with('success', 'Kode bayar berhasil dibuat! Tunjukkan ke Warung GoMad terdekat.');
            }
            elseif ($method === 'cod') {
                RentalPayment::create([
                    'rental_booking_id' => $booking->id,
                    'amount' => $booking->total_price,
                    'commission' => $commission,
                    'agency_revenue' => $agencyRevenue,
                    'payment_type' => 'cod',
                    'status' => 'cod_pending',
                ]);

                $booking->update(['status' => \App\Enums\RentalBookingStatus::CONFIRMED->value]);

                return redirect()->route('customer.rental.detail', $booking)
                    ->with('success', 'Pembayaran COD dipilih. Bayar tunai ke sopir saat penjemputan.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }

        return back()->with('error', 'Metode pembayaran tidak valid.');
    }

    /**
     * Generate Midtrans Snap Token (AJAX)
     */
    public function getMidtransToken(RentalBooking $booking): JsonResponse
    {
        if ($booking->customer_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if (!$booking->payment || $booking->payment->payment_type !== 'midtrans' || $booking->payment->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Pembayaran tidak tersedia'], 400);
        }

        try {
            $serverKey = config('gomad.midtrans.server_key');
            $isProduction = config('gomad.midtrans.is_production', false);

            $baseUrl = $isProduction
                ? 'https://app.midtrans.com/snap/v1/transactions'
                : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

            $orderId = 'RENT-' . $booking->booking_code . '-' . time();

            $payload = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int) $booking->total_price,
                ],
                'customer_details' => [
                    'first_name' => $booking->customer->name ?? 'Customer',
                    'email' => $booking->customer->email ?? 'customer@gomad.id',
                    'phone' => $booking->customer->phone ?? '',
                ],
                'item_details' => [
                    [
                        'id' => 'RENTAL-' . $booking->id,
                        'price' => (int) $booking->total_price,
                        'quantity' => 1,
                        'name' => 'Sewa ' . ($booking->vehicle->plate_number ?? 'Kendaraan') . ' — ' . $booking->total_days . ' hari',
                    ],
                ],
                'callbacks' => [
                    'finish' => route('customer.rental.detail', $booking),
                ],
            ];

            $response = Http::withBasicAuth($serverKey, '')
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($baseUrl, $payload);

            if ($response->successful()) {
                $result = $response->json();

                // Simpan snap response
                $booking->payment->update([
                    'payment_detail' => array_merge($booking->payment->payment_detail ?? [], [
                        'snap_response' => $result,
                    ]),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Snap token berhasil dibuat.',
                    'data' => ['snap_token' => $result['token'] ?? ''],
                ]);
            }

            \Log::error('Rental Midtrans Error', ['response' => $response->body()]);
            return response()->json(['success' => false, 'message' => 'Gagal membuat token pembayaran'], 500);

        } catch (\Exception $e) {
            \Log::error('Rental Midtrans Exception', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Midtrans Callback (dari webhook)
     */
    public function midtransCallback(Request $request): JsonResponse
    {
        $payload = $request->all();
        \Log::info('Rental Midtrans Callback', $payload);

        $orderId = $payload['order_id'] ?? '';
        $transactionStatus = $payload['transaction_status'] ?? '';
        $fraudStatus = $payload['fraud_status'] ?? '';

        // Parse: RENT-GR-YYYYMMDD-XXXX-timestamp
        preg_match('/^RENT-(GR-\d{8}-\d{4})-\d+$/', $orderId, $matches);

        if (empty($matches[1])) {
            return response()->json(['success' => false, 'message' => 'Invalid order ID'], 400);
        }

        $bookingCode = $matches[1];
        $booking = RentalBooking::where('booking_code', $bookingCode)->first();

        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Booking not found'], 404);
        }

        $payment = $booking->payment;
        if (!$payment) {
            return response()->json(['success' => false, 'message' => 'Payment not found'], 404);
        }

        $newStatus = null;

        if ($transactionStatus === 'capture' || $transactionStatus === 'settlement') {
            if ($fraudStatus === 'accept') {
                $newStatus = 'paid';
            }
        } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire'])) {
            $newStatus = 'failed';
        } elseif ($transactionStatus === 'pending') {
            $newStatus = 'pending';
        }

        if ($newStatus) {
            $payment->update([
                'status' => $newStatus,
                'transaction_id' => $payload['transaction_id'] ?? null,
                'payment_method' => $payload['payment_type'] ?? null,
                'paid_at' => $newStatus === 'paid' ? now() : null,
                'payment_detail' => array_merge($payment->payment_detail ?? [], [
                    'callback' => $payload,
                ]),
            ]);

            if ($newStatus === 'paid') {
                $booking->update(['status' => \App\Enums\RentalBookingStatus::PAID->value]);

                // Add pending balance ke agency
                $agency = $booking->vehicle->agency;
                if ($agency) {
                    $walletService = app(\App\Services\WalletService::class);
                    $wallet = $walletService->getOrCreateWallet($agency);
                    $wallet->update([
                        'pending_balance' => (float) $wallet->pending_balance + (float) $payment->agency_revenue,
                    ]);
                }
            }
        }

        return response()->json(['success' => true]);
    }

    // ═══════════════════════════════════════════════════════
    // RIWAYAT & CANCEL
    // ═══════════════════════════════════════════════════════

    /**
     * Riwayat booking rental customer
     */
    public function myBookings(): View
    {
        $bookings = $this->rentalService->getCustomerBookings(auth()->id());

        return view('customer.rental.my-bookings', compact('bookings'));
    }
    
    /**
     * Batalkan booking rental dengan refund
     */
    public function cancel(RentalBooking $booking): RedirectResponse
    {
        if ($booking->customer_id !== auth()->id()) {
            abort(403);
        }

        try {
            $this->rentalService->cancelBooking($booking);
            return redirect()->route('customer.rental.bookings')
                ->with('success', 'Booking rental berhasil dibatalkan.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Apply promo ke booking rental (dari halaman detail)
     */
    public function applyPromo(Request $request, RentalBooking $booking): RedirectResponse
    {
        if ($booking->customer_id !== auth()->id()) abort(403);

        if ($booking->payment && !in_array($booking->payment->status, ['pending', 'cod_pending'])) {
            return back()->with('error', 'Tidak dapat mengubah promo setelah pembayaran.');
        }

        $request->validate([
            'promo_id' => ['nullable', 'integer', 'exists:rental_promos,id'],
        ]);

        try {
            $rentalPromoService = app(\App\Services\RentalPromoService::class);
            $paymentMethod = $booking->payment->payment_type ?? null;

            // Reset diskon
            $booking->update([
                'total_price' => (float) $booking->base_price + (float) $booking->service_fee + (float) $booking->platform_fee,
                'discount_amount' => 0,
            ]);

            // Hapus usage lama
            \App\Models\RentalPromoUsage::where('rental_booking_id', $booking->id)->delete();

            // Update payment amount
            if ($booking->payment) {
                $booking->payment->update(['amount' => $booking->total_price]);
            }

            // Jika pilih promo baru
            if ($request->filled('promo_id')) {
                $promo = \App\Models\RentalPromo::find($request->promo_id);

                if (!$promo || !$promo->isActiveNow()) {
                    return back()->with('error', 'Promo tidak valid atau sudah kadaluarsa.');
                }

                if (!$rentalPromoService->canUsePromo(auth()->user(), $promo)) {
                    return back()->with('error', 'Anda sudah pernah menggunakan promo ini.');
                }

                if ($paymentMethod && !$promo->isApplicableFor($paymentMethod)) {
                    return back()->with('error', 'Promo tidak berlaku untuk metode pembayaran yang dipilih.');
                }

                if ((float) $booking->base_price < $promo->min_purchase) {
                    return back()->with('error', 'Total sewa belum memenuhi minimal pembelian promo.');
                }

                $discount = $rentalPromoService->calculateDiscount($promo, (float) $booking->base_price);

                if ($discount > 0) {
                    $newTotal = max(0, (float) $booking->total_price - $discount);
                    $booking->update([
                        'total_price' => $newTotal,
                        'discount_amount' => $discount,
                    ]);

                    if ($booking->payment) {
                        $booking->payment->update(['amount' => $newTotal]);
                    }

                    \App\Models\RentalPromoUsage::create([
                        'rental_promo_id' => $promo->id,
                        'user_id' => auth()->id(),
                        'rental_booking_id' => $booking->id,
                        'discount_amount' => $discount,
                    ]);

                    return back()->with('success', '✅ Promo diterapkan! Diskon: Rp ' . number_format($discount, 0, ',', '.'));
                }
            }

            return back()->with('success', 'Promo dihapus. Harga kembali normal.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }
}