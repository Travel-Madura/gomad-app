@extends('layouts.admin')

@section('title', 'Detail Warung')
@section('content')

@php
    $todayStart = now()->startOfDay();
    $todayEnd = now()->endOfDay();
    $thisMonthStart = now()->startOfMonth();
    $thisMonthEnd = now()->endOfMonth();
    
    $todayTransactions = $agent->cashPayments()->where('status', 'confirmed')->whereBetween('confirmed_at', [$todayStart, $todayEnd])->count();
    $todayAmount = $agent->cashPayments()->where('status', 'confirmed')->whereBetween('confirmed_at', [$todayStart, $todayEnd])->sum('amount');
    $todayCommission = $agent->cashPayments()->where('status', 'confirmed')->whereBetween('confirmed_at', [$todayStart, $todayEnd])->sum('agent_commission');
    
    $monthTransactions = $agent->cashPayments()->where('status', 'confirmed')->whereBetween('confirmed_at', [$thisMonthStart, $thisMonthEnd])->count();
    $monthAmount = $agent->cashPayments()->where('status', 'confirmed')->whereBetween('confirmed_at', [$thisMonthStart, $thisMonthEnd])->sum('amount');
    $monthCommission = $agent->cashPayments()->where('status', 'confirmed')->whereBetween('confirmed_at', [$thisMonthStart, $thisMonthEnd])->sum('agent_commission');
    
    $pendingSettlement = $agent->settlements()->where('status', 'pending')->sum('amount_to_settle');
    $recentPayments = $agent->cashPayments()->with(['booking.customer', 'booking.originStop', 'booking.destinationStop'])->latest()->limit(10)->get();
    $recentSettlements = $agent->settlements()->latest()->limit(5)->get();
@endphp

<div class="max-w-6xl mx-auto">
    <a href="{{ route('admin.payment-agents.index') }}" class="text-[#C1121F] text-sm mb-4 inline-block hover:underline">← Kembali ke Daftar Warung</a>

    {{-- ═══════════════════════════════════════════════ --}}
    {{-- HEADER --}}
    {{-- ═══════════════════════════════════════════════ --}}
    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-6 shadow-sm">
        <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4">
            <div class="flex items-start gap-4">
                <div class="w-20 h-20 rounded-[12px] bg-[#F5F5F5] border border-[#E5E5E5] flex items-center justify-center overflow-hidden flex-shrink-0">
                    @if($agent->photo_warung)
                    <img src="{{ $agent->photo_warung }}" alt="{{ $agent->agent_name }}" class="w-full h-full object-cover">
                    @else
                    <span class="text-3xl">🏪</span>
                    @endif
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-[#111111]">{{ $agent->agent_name }}</h1>
                    <p class="text-sm text-gray-500 font-light">{{ $agent->address }}</p>
                    <div class="flex flex-wrap items-center gap-2 mt-2">
                        @if($agent->is_verified)
                        <span class="px-2 py-0.5 bg-green-50 text-green-700 text-[10px] font-mono uppercase tracking-wider rounded-full border border-green-200">✅ Terverifikasi</span>
                        @else
                        <span class="px-2 py-0.5 bg-yellow-50 text-yellow-700 text-[10px] font-mono uppercase tracking-wider rounded-full border border-yellow-200">⏳ Pending</span>
                        @endif
                        @if(!$agent->is_active)
                        <span class="px-2 py-0.5 bg-red-50 text-red-700 text-[10px] font-mono uppercase tracking-wider rounded-full border border-red-200">Nonaktif</span>
                        @endif
                        <span class="text-sm text-gray-400">|</span>
                        <span class="text-sm text-gray-500">Komisi: {{ $agent->commission_rate }}%</span>
                    </div>
                </div>
            </div>
            
            {{-- Actions --}}
            <div class="flex gap-2 flex-shrink-0">
                @if(!$agent->is_verified)
                <form action="{{ route('admin.payment-agents.verify', $agent) }}" method="POST">
                    @csrf
                    <button class="bg-green-600 text-white px-4 py-2.5 rounded-[12px] text-sm font-semibold hover:bg-green-700 transition">✅ Verifikasi</button>
                </form>
                <button onclick="openRejectModal()" class="bg-red-600 text-white px-4 py-2.5 rounded-[12px] text-sm font-semibold hover:bg-red-700 transition">❌ Tolak</button>
                @endif
                <form action="{{ route('admin.payment-agents.toggle-active', $agent) }}" method="POST">
                    @csrf @method('PUT')
                    <button class="px-4 py-2.5 rounded-[12px] text-sm font-semibold border transition {{ $agent->is_active ? 'border-red-500 text-red-600 hover:bg-red-50' : 'border-green-500 text-green-600 hover:bg-green-50' }}">
                        {{ $agent->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════ --}}
    {{-- STATS CARDS --}}
    {{-- ═══════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-4 text-center shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Transaksi Hari Ini</p>
            <p class="text-2xl font-bold text-[#111111] mt-1">{{ $todayTransactions }}</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-4 text-center shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Diterima Hari Ini</p>
            <p class="text-lg font-bold text-[#111111] mt-1">Rp {{ number_format($todayAmount, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-4 text-center shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Komisi Hari Ini</p>
            <p class="text-lg font-bold text-[#C1121F] mt-1">Rp {{ number_format($todayCommission, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-4 text-center shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Transaksi Bulan Ini</p>
            <p class="text-2xl font-bold text-[#111111] mt-1">{{ $monthTransactions }}</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-4 text-center shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Total Transaksi</p>
            <p class="text-2xl font-bold text-[#111111] mt-1">{{ $agent->total_transactions }}</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-4 text-center shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Harus Disetor</p>
            <p class="text-lg font-bold text-yellow-600 mt-1">Rp {{ number_format($agent->balance_to_settle, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-4 text-center shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Total Komisi</p>
            <p class="text-lg font-bold text-[#C1121F] mt-1">Rp {{ number_format($agent->total_commission, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-4 text-center shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Pending Settlement</p>
            <p class="text-lg font-bold text-orange-600 mt-1">Rp {{ number_format($pendingSettlement, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════ --}}
    {{-- INFO DETAIL — 2 KOLOM --}}
    {{-- ═══════════════════════════════════════════════ --}}
    <div class="grid md:grid-cols-2 gap-6 mb-6">
        {{-- Informasi Warung --}}
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
            <h2 class="font-bold text-lg text-[#111111] mb-4 border-b border-[#E5E5E5] pb-3">📋 Informasi Warung</h2>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between border-b border-[#F5F5F5] pb-2">
                    <span class="text-gray-500 font-light">Nama Warung</span>
                    <span class="font-medium text-[#111111]">{{ $agent->agent_name }}</span>
                </div>
                <div class="flex justify-between border-b border-[#F5F5F5] pb-2">
                    <span class="text-gray-500 font-light">Pemilik</span>
                    <span class="font-medium text-[#111111]">{{ $agent->owner_name }}</span>
                </div>
                <div class="flex justify-between border-b border-[#F5F5F5] pb-2">
                    <span class="text-gray-500 font-light">HP Pemilik</span>
                    <span class="font-medium text-[#111111]">{{ $agent->owner_phone }}</span>
                </div>
                @if($agent->guard_name)
                <div class="flex justify-between border-b border-[#F5F5F5] pb-2">
                    <span class="text-gray-500 font-light">Penjaga</span>
                    <span class="font-medium text-[#111111]">{{ $agent->guard_name }}</span>
                </div>
                <div class="flex justify-between border-b border-[#F5F5F5] pb-2">
                    <span class="text-gray-500 font-light">HP Penjaga</span>
                    <span class="font-medium text-[#111111]">{{ $agent->guard_phone }}</span>
                </div>
                @endif
                <div class="flex justify-between border-b border-[#F5F5F5] pb-2">
                    <span class="text-gray-500 font-light">Email</span>
                    <span class="font-medium text-[#111111]">{{ $agent->user->email ?? '-' }}</span>
                </div>
                <div class="flex justify-between border-b border-[#F5F5F5] pb-2">
                    <span class="text-gray-500 font-light">Kecamatan</span>
                    <span class="font-medium text-[#111111]">{{ $agent->kecamatan ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-gray-500 font-light block mb-1">Alamat Lengkap</span>
                    <p class="text-sm text-[#111111] font-light">{{ $agent->address }}</p>
                </div>
                @if($agent->maps_link)
                <a href="{{ $agent->maps_link }}" target="_blank" class="flex items-center gap-2 text-sm text-[#C1121F] hover:underline mt-2">
                    <span>🗺️</span> Buka di Google Maps
                </a>
                @endif
            </div>
        </div>

        {{-- Info Tambahan --}}
        <div class="space-y-6">
            {{-- Koordinat --}}
            @if($agent->latitude && $agent->longitude)
            <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
                <h2 class="font-bold text-lg text-[#111111] mb-4 border-b border-[#E5E5E5] pb-3">📍 Lokasi</h2>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3 text-center">
                        <span class="text-[10px] font-mono uppercase tracking-wider text-gray-500">Latitude</span>
                        <p class="font-mono text-[#111111]">{{ $agent->latitude }}</p>
                    </div>
                    <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3 text-center">
                        <span class="text-[10px] font-mono uppercase tracking-wider text-gray-500">Longitude</span>
                        <p class="font-mono text-[#111111]">{{ $agent->longitude }}</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Foto --}}
            <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
                <h2 class="font-bold text-lg text-[#111111] mb-4 border-b border-[#E5E5E5] pb-3">📸 Foto</h2>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <p class="text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-2">Foto Warung</p>
                        @if($agent->photo_warung)
                        <img src="{{ $agent->photo_warung }}" alt="Foto Warung" class="w-full h-32 object-cover rounded-[12px] border border-[#E5E5E5] cursor-pointer" onclick="window.open('{{ $agent->photo_warung }}', '_blank')">
                        @else
                        <div class="w-full h-32 bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] flex items-center justify-center text-gray-400 text-sm font-light">Belum ada foto</div>
                        @endif
                    </div>
                    <div>
                        <p class="text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-2">KTP Pemilik</p>
                        @if($agent->photo_ktp_owner)
                        <img src="{{ $agent->photo_ktp_owner }}" alt="KTP Pemilik" class="w-full h-32 object-cover rounded-[12px] border border-[#E5E5E5] cursor-pointer" onclick="window.open('{{ $agent->photo_ktp_owner }}', '_blank')">
                        @else
                        <div class="w-full h-32 bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] flex items-center justify-center text-gray-400 text-sm font-light">Belum ada foto</div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Verifikasi Info --}}
            <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
                <h2 class="font-bold text-lg text-[#111111] mb-4 border-b border-[#E5E5E5] pb-3">📝 Status Verifikasi</h2>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500 font-light">Status</span>
                        <span class="font-semibold {{ $agent->is_verified ? 'text-green-600' : 'text-yellow-600' }}">
                            {{ $agent->is_verified ? '✅ Terverifikasi' : '⏳ Pending' }}
                        </span>
                    </div>
                    @if($agent->verified_at)
                    <div class="flex justify-between">
                        <span class="text-gray-500 font-light">Diverifikasi</span>
                        <span class="font-medium text-[#111111]">{{ $agent->verified_at->format('d M Y H:i') }}</span>
                    </div>
                    @endif
                    @if($agent->verifier)
                    <div class="flex justify-between">
                        <span class="text-gray-500 font-light">Oleh</span>
                        <span class="font-medium text-[#111111]">{{ $agent->verifier->name }}</span>
                    </div>
                    @endif
                    @if($agent->last_settlement_at)
                    <div class="flex justify-between">
                        <span class="text-gray-500 font-light">Settlement Terakhir</span>
                        <span class="font-medium text-[#111111]">{{ $agent->last_settlement_at->format('d M Y') }}</span>
                    </div>
                    @endif
                </div>
                @if($agent->rejection_reason)
                <div class="mt-3 bg-red-50 border border-red-200 rounded-[12px] p-3 text-sm text-red-700 font-light">
                    <strong class="font-medium">Alasan Penolakan:</strong> {{ $agent->rejection_reason }}
                </div>
                @endif
            </div>

            {{-- Ringkasan Keuangan --}}
            <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
                <h2 class="font-bold text-lg text-[#111111] mb-4 border-b border-[#E5E5E5] pb-3">💰 Ringkasan Keuangan</h2>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between border-b border-[#F5F5F5] pb-2">
                        <span class="text-gray-500 font-light">Total Transaksi</span>
                        <span class="font-semibold text-[#111111]">{{ $agent->total_transactions }}</span>
                    </div>
                    <div class="flex justify-between border-b border-[#F5F5F5] pb-2">
                        <span class="text-gray-500 font-light">Total Komisi</span>
                        <span class="font-semibold text-[#C1121F]">Rp {{ number_format($agent->total_commission, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between border-b border-[#F5F5F5] pb-2">
                        <span class="text-gray-500 font-light">Sisa Settlement</span>
                        <span class="font-semibold text-yellow-600">Rp {{ number_format($agent->balance_to_settle, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 font-light">Pending Settlement</span>
                        <span class="font-semibold text-orange-600">Rp {{ number_format($pendingSettlement, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════ --}}
    {{-- TRANSAKSI TERBARU --}}
    {{-- ═══════════════════════════════════════════════ --}}
    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-6 shadow-sm">
        <h2 class="font-bold text-lg text-[#111111] mb-4 border-b border-[#E5E5E5] pb-3">💳 Transaksi Terbaru</h2>
        @if($recentPayments->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-[#F5F5F5] border-b border-[#E5E5E5]">
                    <tr>
                        <th class="px-4 py-3 text-left font-mono uppercase tracking-wider text-xs text-gray-500">Kode Bayar</th>
                        <th class="px-4 py-3 text-left font-mono uppercase tracking-wider text-xs text-gray-500">Booking</th>
                        <th class="px-4 py-3 text-left font-mono uppercase tracking-wider text-xs text-gray-500">Customer</th>
                        <th class="px-4 py-3 text-right font-mono uppercase tracking-wider text-xs text-gray-500">Jumlah</th>
                        <th class="px-4 py-3 text-right font-mono uppercase tracking-wider text-xs text-gray-500">Komisi</th>
                        <th class="px-4 py-3 text-center font-mono uppercase tracking-wider text-xs text-gray-500">Status</th>
                        <th class="px-4 py-3 text-right font-mono uppercase tracking-wider text-xs text-gray-500">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E5E5]">
                    @foreach($recentPayments as $p)
                    <tr class="hover:bg-[#F5F5F5]">
                        <td class="px-4 py-3 font-mono text-xs text-[#111111]">{{ $p->payment_code }}</td>
                        <td class="px-4 py-3 text-xs">
                            @if($p->booking)
                            <span class="font-medium text-[#111111]">{{ $p->booking->booking_code }}</span>
                            <br><span class="text-gray-500 font-light">{{ $p->booking->originStop->city_name ?? '?' }} → {{ $p->booking->destinationStop->city_name ?? '?' }}</span>
                            @else <span class="text-gray-400">-</span> @endif
                        </td>
                        <td class="px-4 py-3 text-[#111111]">{{ $p->booking->customer->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-right font-medium text-[#111111]">Rp {{ number_format($p->amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-[#C1121F] font-medium">Rp {{ number_format($p->agent_commission, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-[10px] font-mono uppercase tracking-wider border
                                @if($p->status == 'confirmed') bg-green-50 text-green-700 border-green-200
                                @elseif($p->status == 'settled') bg-blue-50 text-blue-700 border-blue-200
                                @elseif($p->status == 'pending') bg-yellow-50 text-yellow-700 border-yellow-200
                                @else bg-[#F5F5F5] text-gray-600 border-[#E5E5E5] @endif">{{ $p->status }}</span>
                        </td>
                        <td class="px-4 py-3 text-right text-xs text-gray-500 font-light">{{ $p->created_at->format('d M H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-gray-400 text-sm font-light py-4 text-center">Belum ada transaksi.</p>
        @endif
    </div>

    {{-- ═══════════════════════════════════════════════ --}}
    {{-- RIWAYAT SETTLEMENT --}}
    {{-- ═══════════════════════════════════════════════ --}}
    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
        <h2 class="font-bold text-lg text-[#111111] mb-4 border-b border-[#E5E5E5] pb-3">📋 Riwayat Settlement</h2>
        @if($recentSettlements->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-[#F5F5F5] border-b border-[#E5E5E5]">
                    <tr>
                        <th class="px-4 py-3 text-left font-mono uppercase tracking-wider text-xs text-gray-500">Periode</th>
                        <th class="px-4 py-3 text-center font-mono uppercase tracking-wider text-xs text-gray-500">Transaksi</th>
                        <th class="px-4 py-3 text-right font-mono uppercase tracking-wider text-xs text-gray-500">Total</th>
                        <th class="px-4 py-3 text-right font-mono uppercase tracking-wider text-xs text-gray-500">Komisi</th>
                        <th class="px-4 py-3 text-right font-mono uppercase tracking-wider text-xs text-gray-500">Disetor</th>
                        <th class="px-4 py-3 text-center font-mono uppercase tracking-wider text-xs text-gray-500">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E5E5]">
                    @foreach($recentSettlements as $s)
                    <tr class="hover:bg-[#F5F5F5]">
                        <td class="px-4 py-3 font-mono text-xs text-[#111111]">{{ $s->period_start->format('d M') }} - {{ $s->period_end->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-center font-mono text-[#111111]">{{ $s->total_transactions }}</td>
                        <td class="px-4 py-3 text-right font-medium text-[#111111]">Rp {{ number_format($s->total_amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-[#C1121F]">Rp {{ number_format($s->total_commission, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right font-bold text-[#C1121F]">Rp {{ number_format($s->amount_to_settle, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-[10px] font-mono uppercase tracking-wider border
                                @if($s->status == 'verified') bg-green-50 text-green-700 border-green-200
                                @elseif($s->status == 'paid') bg-blue-50 text-blue-700 border-blue-200
                                @elseif($s->status == 'pending') bg-yellow-50 text-yellow-700 border-yellow-200
                                @else bg-red-50 text-red-700 border-red-200 @endif">
                                {{ $s->status_label ?? $s->status }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-gray-400 text-sm font-light py-4 text-center">Belum ada settlement.</p>
        @endif
    </div>
</div>

{{-- MODAL REJECT --}}
<div id="rejectModal" style="display:none;" class="fixed inset-0 bg-[#111111]/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-[12px] shadow-xl p-6 max-w-md w-full border border-[#E5E5E5]">
        <h3 class="font-bold text-lg text-[#111111] mb-2">Tolak Pengajuan Warung</h3>
        <p class="text-sm text-gray-500 font-light mb-4">Tulis alasan penolakan untuk {{ $agent->agent_name }}</p>
        <form action="{{ route('admin.payment-agents.reject', $agent) }}" method="POST">
            @csrf
            <textarea name="reason" rows="3" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition mb-4" placeholder="Alasan penolakan..." required></textarea>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-red-600 text-white py-2 rounded-[12px] font-semibold hover:bg-red-700">Tolak</button>
                <button type="button" onclick="closeRejectModal()" class="flex-1 border border-[#E5E5E5] py-2 rounded-[12px]">Batal</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openRejectModal() { document.getElementById('rejectModal').style.display = 'flex'; }
function closeRejectModal() { document.getElementById('rejectModal').style.display = 'none'; }
document.getElementById('rejectModal').addEventListener('click', function(e) { if (e.target === this) closeRejectModal(); });
</script>
@endpush
@endsection