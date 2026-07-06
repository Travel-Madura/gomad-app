@extends('layouts.agency')

@section('title', 'Top Up Saldo')
@section('content')
@php
    $walletService = app(\App\Services\WalletService::class);
    $agency = auth()->user()->agency;
    $balance = $walletService->getBalance($agency);
    $depositBalance = (float) ($agency->wallet->deposit_balance ?? 0);
    $codHold = (float) ($agency->wallet->cod_hold_balance ?? 0);
    $availableDeposit = $depositBalance - $codHold;
@endphp

<div class="max-w-lg mx-auto">
    <h1 class="text-2xl font-bold text-[#111111] mb-6">Top Up Saldo Deposit</h1>

    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-6 shadow-sm">
        <h3 class="font-bold text-[#111111] mb-4">Informasi Saldo</h3>
        <div class="space-y-3 text-sm font-light">
            <div class="flex justify-between">
                <span class="text-gray-500">Saldo Deposit</span>
                <span class="font-bold text-[#111111]">Rp {{ number_format($depositBalance, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">COD Hold</span>
                <span class="font-bold text-orange-600">Rp {{ number_format($codHold, 0, ',', '.') }}</span>
            </div>
            <hr class="border-[#E5E5E5]">
            <div class="flex justify-between">
                <span class="text-gray-500">Tersedia</span>
                <span class="font-bold text-green-600">Rp {{ number_format($availableDeposit, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
        <h3 class="font-bold text-[#111111] mb-4">Isi Saldo</h3>
        
        <form action="{{ route('agency.wallet.topup.process') }}" method="POST" id="topupForm">
            @csrf
            <div class="mb-4">
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-2">Nominal Top Up</label>
                <div class="grid grid-cols-3 gap-3 mb-3">
                    @foreach([100000, 500000, 1000000, 2000000, 5000000] as $nominal)
                    <button type="button" onclick="setNominal({{ $nominal }})" 
                            class="border-2 border-[#E5E5E5] rounded-[12px] py-3 text-sm font-semibold hover:border-[#C1121F] hover:bg-[#C1121F]/5 transition text-[#111111]">
                        Rp {{ number_format($nominal / 1000, 0, ',', '.') }}K
                    </button>
                    @endforeach
                </div>
                <input type="number" name="amount" id="amountInput" 
                       class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-lg font-bold text-[#111111] transition" 
                       placeholder="Atau masukkan nominal sendiri" min="10000" required>
            </div>
            
            <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-4 mb-4 text-sm text-[#111111]">
                <p class="font-mono uppercase tracking-wider text-xs font-medium mb-1">Informasi:</p>
                <ul class="list-disc list-inside space-y-1 font-light">
                    <li>Biaya admin top up: <strong>Rp {{ number_format(\App\Models\PlatformSetting::getValue('topup_admin_fee', 3500), 0, ',', '.') }}</strong> per transaksi</li>
                    <li>Minimal top up: <strong>Rp {{ number_format(\App\Models\PlatformSetting::getValue('topup_min_amount', 50000), 0, ',', '.') }}</strong></li>
                    <li>Saldo deposit digunakan sebagai jaminan untuk fitur COD</li>
                    <li>Saldo tidak hangus dan bisa digunakan kapan saja</li>
                    <li>Bisa juga transfer dari Saldo Tersedia (tanpa biaya)</li>
                </ul>
            </div>

            <div id="totalDisplay" class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-4 mb-4 hidden">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500 font-light">Nominal Top Up</span>
                    <span class="font-medium text-[#111111]" id="nominalDisplay">-</span>
                </div>
                <div class="flex justify-between text-sm mt-1">
                    <span class="text-gray-500 font-light">Biaya Admin</span>
                    <span class="font-medium text-[#111111]" id="adminFeeDisplay">-</span>
                </div>
                <hr class="border-[#E5E5E5]">
                <div class="flex justify-between text-sm font-bold">
                    <span>Total Dibayar</span>
                    <span class="text-[#C1121F]" id="totalDisplay2">-</span>
                </div>
            </div>
            
            <button type="submit" class="w-full btn-gomad-primary text-lg py-3 rounded-[12px]">Top Up Sekarang</button>
        </form>
    </div>
</div>

@push('scripts')
<script>
function setNominal(amount) {
    document.getElementById('amountInput').value = amount;
    document.getElementById('amountInput').focus();
}

const adminFee = {{ \App\Models\PlatformSetting::getValue('topup_admin_fee', 3500) }};
const amountInput = document.getElementById('amountInput');
const totalDisplay = document.getElementById('totalDisplay');

function updateTotal() {
    const amount = parseInt(amountInput.value) || 0;
    if (amount > 0) {
        totalDisplay.classList.remove('hidden');
        document.getElementById('nominalDisplay').textContent = 'Rp ' + formatRupiah(amount);
        document.getElementById('adminFeeDisplay').textContent = 'Rp ' + formatRupiah(adminFee);
        document.getElementById('totalDisplay2').textContent = 'Rp ' + formatRupiah(amount + adminFee);
    } else {
        totalDisplay.classList.add('hidden');
    }
}

amountInput.addEventListener('input', updateTotal);
</script>
@endpush
@endsection