@extends('layouts.payment-agent')

@section('title', 'Dashboard')
@section('content')
@php $agent = auth()->user()->paymentAgent; @endphp

@if(!$agent)
<div class="text-center py-12">
    <div class="w-20 h-20 bg-[#C1121F]/5 rounded-[12px] flex items-center justify-center mx-auto mb-4 border border-[#E5E5E5]">
        <span class="text-3xl">🏪</span>
    </div>
    <h2 class="text-xl font-bold text-[#111111] mb-2">Setup Warung</h2>
    <p class="text-gray-600 font-light mb-6">Lengkapi data warung Anda untuk mulai menerima pembayaran.</p>
    <a href="{{ route('payment-agent.setup') }}" class="btn-gomad-primary">Setup Sekarang</a>
</div>

@elseif(!$agent->is_verified)
<div class="text-center py-12">
    <div class="w-20 h-20 bg-yellow-50 border border-yellow-200 rounded-[12px] flex items-center justify-center mx-auto mb-4">
        <span class="text-3xl">⏳</span>
    </div>
    <h2 class="text-xl font-bold text-[#111111] mb-2">Menunggu Verifikasi</h2>
    <p class="text-gray-600 max-w-md mx-auto mb-4 font-light">Warung <strong>{{ $agent->agent_name }}</strong> sedang dalam proses verifikasi.</p>
    
    @if($agent->rejection_reason)
    <div class="bg-red-50 border border-red-200 rounded-[12px] p-4 max-w-md mx-auto mb-4 text-left">
        <p class="text-sm font-medium text-red-700 font-mono uppercase tracking-wider text-xs">Alasan Penolakan:</p>
        <p class="text-sm text-red-600 mt-1 font-light">{{ $agent->rejection_reason }}</p>
        <a href="{{ route('payment-agent.setup', ['reset' => 1]) }}" class="inline-block mt-3 bg-[#C1121F] text-white px-4 py-2 rounded-[12px] text-sm font-medium hover:bg-[#8A0F18]">Setup Ulang</a>
    </div>
    @endif
    
    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 max-w-md mx-auto text-left shadow-sm">
        <h3 class="font-mono uppercase tracking-wider text-xs font-bold text-[#111111] mb-3">Data Warung Anda</h3>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between border-b border-[#F5F5F5] pb-1"><span class="text-gray-500 font-light">Nama</span><span class="font-semibold text-[#111111]">{{ $agent->agent_name }}</span></div>
            <div class="flex justify-between border-b border-[#F5F5F5] pb-1"><span class="text-gray-500 font-light">Pemilik</span><span class="font-semibold text-[#111111]">{{ $agent->owner_name }}</span></div>
            <div class="flex justify-between border-b border-[#F5F5F5] pb-1"><span class="text-gray-500 font-light">Alamat</span><span class="font-semibold text-right max-w-[200px] text-[#111111]">{{ $agent->address }}</span></div>
        </div>
    </div>
</div>

@else
@php
    $agentService = app(\App\Services\PaymentAgentService::class);
    $stats = $agentService->getAgentStats($agent);
@endphp

<div>
    <div class="mb-6 border-b border-[#E5E5E5] pb-3">
        <h1 class="text-2xl font-bold text-[#111111]">{{ $agent->agent_name }}</h1>
        <p class="text-gray-500 text-sm font-light">{{ $agent->address }} • Kec. {{ $agent->kecamatan ?? '-' }}</p>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-5 shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Transaksi Hari Ini</p>
            <p class="text-2xl font-bold text-[#111111] mt-1">{{ $stats['today_transactions'] }}</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-5 shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Komisi Hari Ini</p>
            <p class="text-lg font-bold text-[#C1121F] mt-1">Rp {{ number_format($stats['today_commission'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-5 shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Bulan Ini</p>
            <p class="text-2xl font-bold text-[#111111] mt-1">{{ $stats['month_transactions'] }} transaksi</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-5 shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Harus Disetor</p>
            <p class="text-lg font-bold text-yellow-600 mt-1">Rp {{ number_format($stats['balance_to_settle'], 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Konfirmasi Pembayaran --}}
    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-8 shadow-sm">
        <h2 class="font-mono uppercase tracking-wider text-xs font-bold text-[#111111] mb-4">Konfirmasi Pembayaran</h2>
        <form action="{{ route('payment-agent.payments.confirm') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Kode Bayar</label>
                    <input type="text" name="payment_code" placeholder="WM-YYYYMMDD-XXXXXX" 
                           class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent font-mono uppercase tracking-wider text-[#111111] transition" required>
                </div>
                <div>
                    <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">PIN Konfirmasi</label>
                    <input type="password" name="pin" placeholder="******" maxlength="6" 
                           class="w-full md:w-48 px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-lg text-center tracking-widest text-[#111111] transition" required>
                </div>
            </div>
            <button type="submit" class="bg-[#C1121F] text-white px-8 py-3 rounded-[12px] font-bold hover:bg-[#8A0F18] transition">Konfirmasi Pembayaran</button>
        </form>
    </div>

    {{-- Informasi --}}
    <div class="grid md:grid-cols-2 gap-6">
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
            <h3 class="font-mono uppercase tracking-wider text-xs font-bold text-[#111111] mb-3">Informasi Warung</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between border-b border-[#F5F5F5] pb-1"><span class="text-gray-500 font-light">Status</span><span class="font-medium text-green-600 font-mono uppercase tracking-wider text-xs">Terverifikasi</span></div>
                <div class="flex justify-between border-b border-[#F5F5F5] pb-1"><span class="text-gray-500 font-light">Komisi</span><span class="font-medium text-[#111111]">{{ $agent->commission_rate }}%</span></div>
                <div class="flex justify-between border-b border-[#F5F5F5] pb-1"><span class="text-gray-500 font-light">Total Transaksi</span><span class="font-medium text-[#111111]">{{ $agent->total_transactions }}</span></div>
            </div>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
            <h3 class="font-mono uppercase tracking-wider text-xs font-bold text-[#111111] mb-3">Panduan</h3>
            <ol class="space-y-2 text-sm text-gray-600 list-decimal list-inside font-light">
                <li>Customer datang dengan kode bayar</li>
                <li>Masukkan kode bayar + PIN</li>
                <li>Terima uang CASH dari customer</li>
                <li>Klik Konfirmasi</li>
                <li>Setiap Senin, cek Settlement</li>
            </ol>
        </div>
    </div>
</div>
@endif
@endsection