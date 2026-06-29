<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Agency;
use App\Models\AgencyWallet;
use App\Models\User;
use App\Models\Withdrawal;
use Faker\Factory;

class WithdrawalSeeder extends Seeder
{
    protected $faker;

    public function __construct()
    {
        $this->faker = Factory::create('id_ID');
    }

    public function run(): void
    {
        echo "💰 GENERATING WITHDRAWAL DATA...\n";
        echo "═══════════════════════════════════════════\n\n";

        $adminId = User::where('email', 'admin@gomad.id')->first()?->id ?? 1;
        $agencies = Agency::where('is_verified', true)->get();

        if ($agencies->isEmpty()) {
            echo "⚠️  Tidak ada agency verified untuk withdrawal\n";
            return;
        }

        $totalWithdrawals = 0;
        $banks = ['BCA', 'BRI', 'BNI', 'Mandiri', 'CIMB Niaga', 'BTN', 'BJB'];

        foreach ($agencies as $index => $agency) {
            $wallet = AgencyWallet::where('agency_id', $agency->id)->first();

            if (!$wallet || $wallet->total_earned <= 0) {
                continue;
            }

            // Completed
            for ($i = 0; $i < 2; $i++) {
                $bank = $this->faker->randomElement($banks);
                $amount = rand(500000, 3000000);
                $adminFee = 5000;
                $netAmount = $amount - $adminFee;

                Withdrawal::create([
                    'agency_id' => $agency->id,
                    'amount' => $amount,
                    'admin_fee' => $adminFee,
                    'net_amount' => $netAmount,
                    'bank_name' => $bank,
                    'bank_account_number' => $this->faker->numerify('##############'),
                    'bank_account_name' => $agency->contact_person ?? $this->faker->name(),
                    'status' => 'completed',
                    'approved_by' => $adminId,
                    'approved_at' => now()->subDays(rand(7, 60)),
                    'transaction_id' => 'WD-' . strtoupper(substr(md5($agency->id . $i . 'completed'), 0, 10)),
                    'payment_detail' => json_encode([
                        'method' => 'bank_transfer',
                        'bank' => $bank,
                        'reference' => 'REF' . rand(100000, 999999),
                        'processed_at' => now()->subDays(rand(6, 59))->toDateTimeString(),
                    ]),
                    'completed_at' => now()->subDays(rand(6, 59)),
                ]);

                $totalWithdrawals++;
            }

            // Processing
            $bank = $this->faker->randomElement($banks);
            $amount = rand(300000, 1500000);
            $adminFee = 5000;
            $netAmount = $amount - $adminFee;

            Withdrawal::create([
                'agency_id' => $agency->id,
                'amount' => $amount,
                'admin_fee' => $adminFee,
                'net_amount' => $netAmount,
                'bank_name' => $bank,
                'bank_account_number' => $this->faker->numerify('##############'),
                'bank_account_name' => $agency->contact_person ?? $this->faker->name(),
                'status' => 'processing',
                'approved_by' => $adminId,
                'approved_at' => now()->subHours(rand(1, 24)),
                'transaction_id' => 'WD-' . strtoupper(substr(md5($agency->id . 'processing'), 0, 10)),
                'payment_detail' => json_encode([
                    'method' => 'bank_transfer',
                    'bank' => $bank,
                    'reference' => 'REF' . rand(100000, 999999),
                    'processed_at' => now()->subHours(rand(1, 23))->toDateTimeString(),
                ]),
            ]);

            $totalWithdrawals++;

            // Pending
            $bank = $this->faker->randomElement($banks);
            $amount = rand(200000, 2000000);
            $adminFee = 5000;
            $netAmount = $amount - $adminFee;

            Withdrawal::create([
                'agency_id' => $agency->id,
                'amount' => $amount,
                'admin_fee' => $adminFee,
                'net_amount' => $netAmount,
                'bank_name' => $bank,
                'bank_account_number' => $this->faker->numerify('##############'),
                'bank_account_name' => $agency->contact_person ?? $this->faker->name(),
                'status' => 'pending',
            ]);

            $totalWithdrawals++;

            // Rejected
            if ($index % 3 === 0) {
                $bank = $this->faker->randomElement($banks);
                $amount = rand(500000, 2500000);
                $adminFee = 5000;
                $netAmount = $amount - $adminFee;

                $rejectedReasons = [
                    'Saldo tidak mencukupi untuk penarikan ini.',
                    'Dokumen verifikasi belum lengkap.',
                    'Nomor rekening tidak valid.',
                    'Nama rekening tidak sesuai dengan data agency.',
                    'Batas maksimal penarikan harian terlampaui.',
                    'Akun bank sedang dalam proses verifikasi.',
                ];

                Withdrawal::create([
                    'agency_id' => $agency->id,
                    'amount' => $amount,
                    'admin_fee' => $adminFee,
                    'net_amount' => $netAmount,
                    'bank_name' => $bank,
                    'bank_account_number' => $this->faker->numerify('##############'),
                    'bank_account_name' => $agency->contact_person ?? $this->faker->name(),
                    'status' => 'rejected',
                    'approved_by' => $adminId,
                    'approved_at' => now()->subDays(rand(3, 14)),
                    'rejected_reason' => $this->faker->randomElement($rejectedReasons),
                    'transaction_id' => 'WD-' . strtoupper(substr(md5($agency->id . 'rejected'), 0, 10)),
                ]);

                $totalWithdrawals++;
            }

            // Failed
            if ($index % 5 === 0) {
                $bank = $this->faker->randomElement($banks);
                $amount = rand(500000, 2000000);
                $adminFee = 5000;
                $netAmount = $amount - $adminFee;

                $failedReasons = [
                    'Transfer gagal - rekening tujuan tidak aktif.',
                    'Gangguan jaringan bank.',
                    'Batas waktu transfer terlampaui.',
                    'Kesalahan sistem - mohon coba lagi.',
                ];

                Withdrawal::create([
                    'agency_id' => $agency->id,
                    'amount' => $amount,
                    'admin_fee' => $adminFee,
                    'net_amount' => $netAmount,
                    'bank_name' => $bank,
                    'bank_account_number' => $this->faker->numerify('##############'),
                    'bank_account_name' => $agency->contact_person ?? $this->faker->name(),
                    'status' => 'failed',
                    'approved_by' => $adminId,
                    'approved_at' => now()->subDays(rand(1, 7)),
                    'rejected_reason' => $this->faker->randomElement($failedReasons),
                    'transaction_id' => 'WD-' . strtoupper(substr(md5($agency->id . 'failed'), 0, 10)),
                    'payment_detail' => json_encode([
                        'method' => 'bank_transfer',
                        'bank' => $bank,
                        'reference' => 'REF' . rand(100000, 999999),
                        'error' => $this->faker->randomElement($failedReasons),
                    ]),
                ]);

                $totalWithdrawals++;
            }
        }

        echo "\n═══════════════════════════════════════════\n";
        echo "✅ WITHDRAWAL DATA GENERATED!\n";
        echo "═══════════════════════════════════════════\n";
        echo "📊 Total Withdrawals: {$totalWithdrawals}\n";
        echo "🏢 Agencies: {$agencies->count()}\n\n";
        echo "📋 STATUS BREAKDOWN:\n";
        echo "──────────────────────────────────────────────\n";
        echo "✅ Completed: " . Withdrawal::where('status', 'completed')->count() . "\n";
        echo "🔄 Processing: " . Withdrawal::where('status', 'processing')->count() . "\n";
        echo "⏳ Pending: " . Withdrawal::where('status', 'pending')->count() . "\n";
        echo "❌ Rejected: " . Withdrawal::where('status', 'rejected')->count() . "\n";
        echo "⚠️  Failed: " . Withdrawal::where('status', 'failed')->count() . "\n";
        echo "──────────────────────────────────────────────\n";
    }
}