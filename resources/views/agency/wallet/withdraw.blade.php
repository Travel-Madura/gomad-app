@extends('layouts.agency')

@section('title', 'Tarik Dana')
@section('content')

@php
    $walletService = app(\App\Services\WalletService::class);
    $balance = $walletService->getBalance(auth()->user()->agency);
@endphp

<div>
    <h1 class="text-lg font-bold text-[#111111] mb-6">Tarik Dana</h1>

    <div class="bg-[#C1121F]/5 border border-[#C1121F] rounded-[12px] p-5 mb-6">
        <p class="text-[10px] font-mono uppercase tracking-wider text-gray-600 font-medium mb-1">Saldo Tersedia</p>
        <p class="text-2xl font-bold text-[#C1121F]">Rp {{ number_format($balance['available_balance'], 0, ',', '.') }}</p>
    </div>

    <form action="{{ route('agency.withdrawals.store') }}" method="POST" class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm space-y-4">
        @csrf
        <div>
            <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Jumlah (Rp) <span class="text-[#C1121F]">*</span></label>
            <input type="number" name="amount"
                   class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" 
                   min="{{ config('gomad.minimal_withdrawal', 100000) }}" 
                   max="{{ (int) $balance['available_balance'] }}" required>
            <p class="text-[10px] text-gray-400 mt-1 font-light">Minimal: Rp {{ number_format(config('gomad.minimal_withdrawal', 100000), 0, ',', '.') }}</p>
        </div>
        <div>
            <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Nama Bank <span class="text-[#C1121F]">*</span></label>
            <input type="text" name="bank_name"
                   class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition"
                   placeholder="Contoh: BCA, BNI, BRI" required>
        </div>
        <div>
            <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Nomor Rekening <span class="text-[#C1121F]">*</span></label>
            <input type="text" name="bank_account_number"
                   class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" required>
        </div>
        <div>
            <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Nama Pemilik Rekening <span class="text-[#C1121F]">*</span></label>
            <input type="text" name="bank_account_name"
                   class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" required>
        </div>
        <button type="submit" class="w-full btn-gomad-primary py-3 rounded-[12px] font-semibold">
            AJUKAN PENARIKAN
        </button>
    </form>
</div>
@endsection