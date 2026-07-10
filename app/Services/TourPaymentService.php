<?php

namespace App\Services;

use App\Enums\TourBookingStatus;
use App\Models\TourBooking;
use App\Models\TourPayment;
use App\Services\NotificationService;
use App\Services\WalletService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TourPaymentService
{
    public function __construct(
        private readonly WalletService $walletService,
        private readonly NotificationService $notificationService,
    ) {}

    /**
     * Buat payment untuk tour booking
     */
    public function createPayment(TourBooking $booking): TourPayment
    {
        $existingPayment = TourPayment::where('tour_booking_id', $booking->id)->first();
        if ($existingPayment) {
            return $existingPayment;
        }

        $commissionRate = (float) \App\Models\PlatformSetting::getValue('commission_rate', 5);
        $commission = $booking->total_price * ($commissionRate / 100);
        $agencyRevenue = $booking->total_price - $commission;

        $paymentTimeout = (int) \App\Models\PlatformSetting::getValue('payment_timeout', 30);

        return TourPayment::create([
            'tour_booking_id' => $booking->id,
            'amount' => $booking->total_price,
            'commission' => $commission,
            'agency_revenue' => $agencyRevenue,
            'payment_type' => 'midtrans',
            'status' => 'pending',
            'expired_at' => now()->addMinutes($paymentTimeout),
        ]);
    }

    /**
     * Generate Snap Token Midtrans untuk tour booking
     */
    public function getSnapToken(TourBooking $booking): string
    {
        $payment = $this->createPayment($booking);

        $serverKey = config('gomad.midtrans.server_key');
        $isProduction = config('gomad.midtrans.is_production', false);

        $baseUrl = $isProduction
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

        $orderId = 'TOUR-' . $booking->booking_code . '-' . time();

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
                    'id' => 'TOUR-' . $booking->id,
                    'price' => (int) $booking->total_price,
                    'quantity' => 1,
                    'name' => 'Tour: ' . ($booking->tourSchedule->tourPackage->name ?? 'Paket Wisata') . ' - ' . $booking->group_name,
                ],
            ],
            'callbacks' => [
                'finish' => config('app.url') . '/customer/tour/booking/' . $booking->id . '/detail',
            ],
        ];

        try {
            $response = Http::withBasicAuth($serverKey, '')
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($baseUrl, $payload);

            if ($response->successful()) {
                $result = $response->json();

                $payment->update([
                    'payment_detail' => array_merge($payment->payment_detail ?? [], [
                        'snap_request' => $payload,
                        'snap_response' => $result,
                    ]),
                ]);

                return $result['token'] ?? '';
            }

            Log::error('Tour Midtrans Snap Token Error', [
                'booking_code' => $booking->booking_code,
                'response' => $response->body(),
            ]);

            throw new \Exception('Gagal membuat Snap Token: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Tour Midtrans Exception', [
                'booking_code' => $booking->booking_code,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Handle Midtrans callback untuk tour
     */
    public function handleCallback(array $payload): void
    {
        Log::info('Tour Midtrans Callback', $payload);

        $orderId = $payload['order_id'] ?? '';
        $transactionStatus = $payload['transaction_status'] ?? '';
        $fraudStatus = $payload['fraud_status'] ?? '';

        // Parse booking code dari order_id: TOUR-GT-YYYYMMDD-XXXX-timestamp
        preg_match('/^TOUR-(GT-\d{8}-\d{4})-\d+$/', $orderId, $matches);

        if (empty($matches[1])) {
            Log::error('Cannot parse tour booking code from order_id: ' . $orderId);
            return;
        }

        $bookingCode = $matches[1];
        $booking = TourBooking::where('booking_code', $bookingCode)->first();

        if (!$booking) {
            Log::error('Tour booking not found: ' . $bookingCode);
            return;
        }

        $payment = TourPayment::where('tour_booking_id', $booking->id)->first();
        if (!$payment) {
            Log::error('Tour payment not found for booking: ' . $bookingCode);
            return;
        }

        $newStatus = null;

        if ($transactionStatus === 'capture' || $transactionStatus === 'settlement') {
            if ($fraudStatus === 'accept') {
                $newStatus = 'paid';
            } elseif ($fraudStatus === 'challenge') {
                $newStatus = 'pending';
            } else {
                $newStatus = 'failed';
            }
        } elseif ($transactionStatus === 'pending') {
            $newStatus = 'pending';
        } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire'])) {
            $newStatus = 'failed';
        } elseif (in_array($transactionStatus, ['refund', 'partial_refund'])) {
            $newStatus = 'refunded';
        }

        if ($newStatus) {
            DB::transaction(function () use ($payment, $booking, $newStatus, $payload) {
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
                    $booking->update(['status' => TourBookingStatus::PAID->value]);
                    $this->walletService->addPendingBalanceForTour($booking);

                    // Notifikasi
                    $package = $booking->tourSchedule->tourPackage;
                    $this->notificationService->createNotification(
                        $booking->customer_id,
                        '✅ Pembayaran Tour Berhasil',
                        "Pembayaran untuk tour {$booking->booking_code} - {$package->name} telah dikonfirmasi.",
                        ['type' => 'tour_payment_paid', 'booking_id' => $booking->id]
                    );

                    // Proses referral reward
                    try {
                        $promoService = app(\App\Services\PromoService::class);
                        // Bisa ditambahkan referral reward untuk tour juga
                    } catch (\Exception $e) {
                        Log::error('Tour referral processing failed: ' . $e->getMessage());
                    }
                } elseif ($newStatus === 'failed') {
                    $booking->update([
                        'status' => TourBookingStatus::CANCELLED->value,
                        'cancelled_at' => now(),
                    ]);
                }
            });
        }
    }

    /**
     * Verifikasi signature Midtrans
     */
    public function verifySignature(array $payload): bool
    {
        $serverKey = config('gomad.midtrans.server_key');
        $orderId = $payload['order_id'] ?? '';
        $statusCode = $payload['status_code'] ?? '';
        $grossAmount = $payload['gross_amount'] ?? '';

        $rawSignature = $orderId . $statusCode . $grossAmount . $serverKey;
        $calculatedSignature = hash('sha512', $rawSignature);
        $providedSignature = $payload['signature_key'] ?? '';

        return hash_equals($calculatedSignature, $providedSignature);
    }
}