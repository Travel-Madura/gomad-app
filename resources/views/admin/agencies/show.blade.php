@extends('layouts.admin')

@section('title', 'Detail Agency')
@section('content')

@php
    $totalSchedules = \App\Models\Schedule::where('agency_id', $agency->id)->count();
    $activeSchedules = \App\Models\Schedule::where('agency_id', $agency->id)
        ->where('is_active', true)
        ->where('departure_date', '>=', now()->toDateString())
        ->count();
    $totalTravelBookings = \App\Models\Booking::whereHas('schedule', fn($q) => $q->where('agency_id', $agency->id))->count();
    $travelRevenue = \App\Models\Booking::whereHas('schedule', fn($q) => $q->where('agency_id', $agency->id))
        ->where('status', 'completed')
        ->sum('total_price');
    $totalTourPackages = \App\Models\TourPackage::where('agency_id', $agency->id)->count();
    $activeTourSchedules = \App\Models\TourSchedule::whereHas('tourPackage', fn($q) => $q->where('agency_id', $agency->id))
        ->where('is_active', true)
        ->where('departure_date', '>=', now()->toDateString())
        ->count();
    $totalTourBookings = \App\Models\TourBooking::whereHas('tourSchedule.tourPackage', fn($q) => $q->where('agency_id', $agency->id))->count();
    $tourRevenue = \App\Models\TourBooking::whereHas('tourSchedule.tourPackage', fn($q) => $q->where('agency_id', $agency->id))
        ->where('status', 'completed')
        ->sum('total_price');
    $totalVehicles = \App\Models\Vehicle::where('agency_id', $agency->id)->count();
    $activeVehicles = \App\Models\Vehicle::where('agency_id', $agency->id)->where('is_active', true)->count();
    $totalDrivers = \App\Models\User::where('agency_id', $agency->id)->where('role', 'driver')->count();
    $activeDrivers = \App\Models\User::where('agency_id', $agency->id)->where('role', 'driver')->where('is_active', true)->count();
    $wallet = $agency->wallet;
    $recentTravelBookings = \App\Models\Booking::whereHas('schedule', fn($q) => $q->where('agency_id', $agency->id))
        ->with(['schedule.route', 'customer', 'payment'])
        ->latest()
        ->limit(5)
        ->get();
    $recentTourBookings = \App\Models\TourBooking::whereHas('tourSchedule.tourPackage', fn($q) => $q->where('agency_id', $agency->id))
        ->with(['tourSchedule.tourPackage', 'customer', 'payment'])
        ->latest()
        ->limit(5)
        ->get();
    $services = is_array($agency->services) ? $agency->services : (is_string($agency->services) ? json_decode($agency->services, true) ?? [] : []);
    $socialMedia = is_array($agency->social_media) ? $agency->social_media : (is_string($agency->social_media) ? json_decode($agency->social_media, true) ?? [] : []);
    $businessHours = is_array($agency->business_hours) ? $agency->business_hours : (is_string($agency->business_hours) ? json_decode($agency->business_hours, true) ?? [] : []);
    $zoneCoverage = is_array($agency->zone_coverage) ? $agency->zone_coverage : (is_string($agency->zone_coverage) ? json_decode($agency->zone_coverage, true) ?? [] : []);
    $gallery = is_array($agency->gallery) ? $agency->gallery : (is_string($agency->gallery) ? json_decode($agency->gallery, true) ?? [] : []);
@endphp

<div class="max-w-6xl mx-auto">
    <a href="{{ route('admin.agencies.index') }}" class="text-[#C1121F] text-sm mb-4 inline-block hover:underline">← Kembali ke Daftar Agency</a>

    {{-- ═══════════════════════════════════════════════ --}}
    {{-- HEADER --}}
    {{-- ═══════════════════════════════════════════════ --}}
    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-6 shadow-sm">
        <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4">
            <div class="flex items-start gap-4">
                {{-- Logo --}}
                <div class="w-20 h-20 rounded-[12px] bg-[#F5F5F5] border border-[#E5E5E5] flex items-center justify-center overflow-hidden flex-shrink-0">
                    @if($agency->logo)
                    <img src="{{ $agency->logo }}" alt="{{ $agency->agency_name }}" class="w-full h-full object-cover">
                    @else
                    <span class="text-3xl">🏢</span>
                    @endif
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-[#111111]">{{ $agency->agency_name }}</h1>
                    <p class="text-sm text-gray-500 font-mono">{{ $agency->slug }}</p>
                    <div class="flex flex-wrap items-center gap-2 mt-2">
                        @if($agency->is_verified)
                        <span class="px-2 py-0.5 bg-green-50 text-green-700 text-[10px] font-mono uppercase tracking-wider rounded-full border border-green-200">✅ Terverifikasi</span>
                        @else
                        <span class="px-2 py-0.5 bg-yellow-50 text-yellow-700 text-[10px] font-mono uppercase tracking-wider rounded-full border border-yellow-200">⏳ Pending</span>
                        @endif
                        <span class="text-sm text-gray-500">⭐ {{ number_format($agency->rating, 1) }}</span>
                        <span class="text-sm text-gray-400">|</span>
                        <span class="text-sm text-gray-500">{{ $agency->total_bookings }} booking</span>
                        <span class="text-sm text-gray-400">|</span>
                        <span class="text-sm text-gray-500">Berdiri {{ $agency->founded_year }}</span>
                    </div>
                </div>
            </div>
            
            {{-- Actions --}}
            <div class="flex gap-2 flex-shrink-0">
                @if(!$agency->is_verified)
                <form action="{{ route('admin.agencies.verify', $agency) }}" method="POST">
                    @csrf
                    <button class="bg-green-600 text-white px-4 py-2.5 rounded-[12px] text-sm font-semibold hover:bg-green-700 transition">
                        ✅ Verifikasi
                    </button>
                </form>
                <button onclick="openRejectModal()" class="bg-red-600 text-white px-4 py-2.5 rounded-[12px] text-sm font-semibold hover:bg-red-700 transition">
                    ❌ Tolak
                </button>
                @endif
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════ --}}
    {{-- STATS CARDS --}}
    {{-- ═══════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-4 text-center shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">🚌 Jadwal Travel</p>
            <p class="text-2xl font-bold text-[#111111] mt-1">{{ $activeSchedules }}</p>
            <p class="text-[10px] text-gray-400 font-light">{{ $totalSchedules }} total</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-4 text-center shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">🎫 Booking Travel</p>
            <p class="text-2xl font-bold text-[#111111] mt-1">{{ $totalTravelBookings }}</p>
            <p class="text-[10px] text-gray-400 font-light">Rp {{ number_format($travelRevenue, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-4 text-center shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">🏝️ Paket Tour</p>
            <p class="text-2xl font-bold text-purple-600 mt-1">{{ $totalTourPackages }}</p>
            <p class="text-[10px] text-gray-400 font-light">{{ $activeTourSchedules }} jadwal aktif</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-4 text-center shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">🎫 Booking Tour</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $totalTourBookings }}</p>
            <p class="text-[10px] text-gray-400 font-light">Rp {{ number_format($tourRevenue, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-4 text-center shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">🚐 Kendaraan</p>
            <p class="text-2xl font-bold text-[#111111] mt-1">{{ $activeVehicles }}</p>
            <p class="text-[10px] text-gray-400 font-light">{{ $totalVehicles }} total</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-4 text-center shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">👨‍✈️ Driver</p>
            <p class="text-2xl font-bold text-[#111111] mt-1">{{ $activeDrivers }}</p>
            <p class="text-[10px] text-gray-400 font-light">{{ $totalDrivers }} total</p>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════ --}}
    {{-- INFO DETAIL — 2 KOLOM --}}
    {{-- ═══════════════════════════════════════════════ --}}
    <div class="grid md:grid-cols-2 gap-6 mb-6">
        {{-- Informasi Agency --}}
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
            <h2 class="font-bold text-lg text-[#111111] mb-4 border-b border-[#E5E5E5] pb-3">📋 Informasi Agency</h2>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between border-b border-[#F5F5F5] pb-2">
                    <span class="text-gray-500 font-light">Nama Agency</span>
                    <span class="font-medium text-[#111111] text-right max-w-[60%]">{{ $agency->agency_name }}</span>
                </div>
                <div class="flex justify-between border-b border-[#F5F5F5] pb-2">
                    <span class="text-gray-500 font-light">Pemilik</span>
                    <span class="font-medium text-[#111111]">{{ $agency->user->name ?? '-' }}</span>
                </div>
                <div class="flex justify-between border-b border-[#F5F5F5] pb-2">
                    <span class="text-gray-500 font-light">Email</span>
                    <span class="font-medium text-[#111111]">{{ $agency->user->email ?? '-' }}</span>
                </div>
                <div class="flex justify-between border-b border-[#F5F5F5] pb-2">
                    <span class="text-gray-500 font-light">Kontak Person</span>
                    <span class="font-medium text-[#111111]">{{ $agency->contact_person ?? '-' }}</span>
                </div>
                <div class="flex justify-between border-b border-[#F5F5F5] pb-2">
                    <span class="text-gray-500 font-light">HP</span>
                    <span class="font-medium text-[#111111]">{{ $agency->contact_alternate ?? '-' }}</span>
                </div>
                <div class="flex justify-between border-b border-[#F5F5F5] pb-2">
                    <span class="text-gray-500 font-light">Email Bisnis</span>
                    <span class="font-medium text-[#111111]">{{ $agency->email_alternate ?? '-' }}</span>
                </div>
                <div class="flex justify-between border-b border-[#F5F5F5] pb-2">
                    <span class="text-gray-500 font-light">Tahun Berdiri</span>
                    <span class="font-medium text-[#111111]">{{ $agency->founded_year ?? '-' }}</span>
                </div>
                <div class="flex justify-between border-b border-[#F5F5F5] pb-2">
                    <span class="text-gray-500 font-light">Alamat</span>
                    <span class="font-medium text-[#111111] text-right max-w-[60%]">{{ $agency->address ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-gray-500 font-light block mb-1">Deskripsi</span>
                    <p class="text-sm text-[#111111] font-light">{{ $agency->description ?? 'Belum ada deskripsi.' }}</p>
                </div>
            </div>
        </div>

        {{-- Dompet & Keuangan --}}
        <div class="space-y-6">
            <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
                <h2 class="font-bold text-lg text-[#111111] mb-4 border-b border-[#E5E5E5] pb-3">💰 Dompet Agency</h2>
                @if($wallet)
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div class="bg-green-50 border border-green-200 rounded-[12px] p-3 text-center">
                        <span class="text-[10px] font-mono uppercase tracking-wider text-gray-500">Saldo Tersedia</span>
                        <p class="font-bold text-green-700 text-lg">Rp {{ number_format($wallet->available_balance, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-[12px] p-3 text-center">
                        <span class="text-[10px] font-mono uppercase tracking-wider text-gray-500">Saldo Pending</span>
                        <p class="font-bold text-yellow-700 text-lg">Rp {{ number_format($wallet->pending_balance, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-blue-50 border border-blue-200 rounded-[12px] p-3 text-center">
                        <span class="text-[10px] font-mono uppercase tracking-wider text-gray-500">Saldo Deposit</span>
                        <p class="font-bold text-blue-700 text-lg">Rp {{ number_format($wallet->deposit_balance ?? 0, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-orange-50 border border-orange-200 rounded-[12px] p-3 text-center">
                        <span class="text-[10px] font-mono uppercase tracking-wider text-gray-500">COD Hold</span>
                        <p class="font-bold text-orange-700 text-lg">Rp {{ number_format($wallet->cod_hold_balance ?? 0, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-purple-50 border border-purple-200 rounded-[12px] p-3 text-center">
                        <span class="text-[10px] font-mono uppercase tracking-wider text-gray-500">Total Diterima</span>
                        <p class="font-bold text-purple-700 text-lg">Rp {{ number_format($wallet->total_earned, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-red-50 border border-red-200 rounded-[12px] p-3 text-center">
                        <span class="text-[10px] font-mono uppercase tracking-wider text-gray-500">Total Ditarik</span>
                        <p class="font-bold text-red-700 text-lg">Rp {{ number_format($wallet->total_withdrawn, 0, ',', '.') }}</p>
                    </div>
                </div>
                @else
                <p class="text-gray-400 text-sm font-light">Belum ada data dompet.</p>
                @endif
            </div>

            {{-- Layanan --}}
            @if(!empty($services))
            <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
                <h2 class="font-bold text-lg text-[#111111] mb-4 border-b border-[#E5E5E5] pb-3">🛠️ Layanan</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach($services as $key => $value)
                    @if($value)
                    <span class="px-3 py-1.5 bg-[#F5F5F5] border border-[#E5E5E5] rounded-full text-xs text-[#111111] capitalize">
                        ✅ {{ str_replace('_', ' ', $key) }}
                    </span>
                    @endif
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Jam Operasional --}}
            @if(!empty($businessHours))
            <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
                <h2 class="font-bold text-lg text-[#111111] mb-4 border-b border-[#E5E5E5] pb-3">🕐 Jam Operasional</h2>
                <div class="space-y-1 text-sm">
                    @foreach($businessHours as $day => $hours)
                    <div class="flex justify-between py-1 border-b border-[#F5F5F5] last:border-0">
                        <span class="capitalize text-gray-600 font-light">{{ $day }}</span>
                        <span class="font-medium text-[#111111]">{{ $hours }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Sosial Media --}}
            @if(!empty($socialMedia))
            <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
                <h2 class="font-bold text-lg text-[#111111] mb-4 border-b border-[#E5E5E5] pb-3">🌐 Sosial Media</h2>
                <div class="space-y-2">
                    @foreach($socialMedia as $platform => $link)
                    @if($link)
                    <a href="{{ $link }}" target="_blank" class="flex items-center gap-2 text-sm text-[#C1121F] hover:underline capitalize">
                        <span>{{ $platform }}</span>
                        <span class="text-gray-400">→</span>
                    </a>
                    @endif
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Zona Coverage --}}
            @if(!empty($zoneCoverage))
            <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
                <h2 class="font-bold text-lg text-[#111111] mb-4 border-b border-[#E5E5E5] pb-3">📍 Zona Coverage</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach($zoneCoverage as $zone)
                    <span class="px-3 py-1.5 bg-blue-50 border border-blue-200 rounded-full text-xs text-blue-700">
                        📍 {{ $zone }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════ --}}
    {{-- KENDARAAN --}}
    {{-- ═══════════════════════════════════════════════ --}}
    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-6 shadow-sm">
        <h2 class="font-bold text-lg text-[#111111] mb-4 border-b border-[#E5E5E5] pb-3">🚐 Kendaraan ({{ $totalVehicles }})</h2>
        @php $vehicles = \App\Models\Vehicle::where('agency_id', $agency->id)->get(); @endphp
        @if($vehicles->isNotEmpty())
        <div class="grid md:grid-cols-3 gap-4">
            @foreach($vehicles as $v)
            <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-bold font-mono text-[#111111]">{{ $v->plate_number }}</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-mono uppercase tracking-wider border {{ $v->is_active ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200' }}">
                        {{ $v->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
                <p class="text-sm text-gray-500 font-light">{{ $v->brand }} {{ $v->model }} ({{ $v->year }})</p>
                <p class="text-xs text-gray-400 font-mono">{{ $v->capacity }} seat | {{ ucfirst($v->type) }}</p>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-gray-400 text-sm font-light">Belum ada kendaraan.</p>
        @endif
    </div>

    {{-- ═══════════════════════════════════════════════ --}}
    {{-- DRIVER --}}
    {{-- ═══════════════════════════════════════════════ --}}
    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-6 shadow-sm">
        <h2 class="font-bold text-lg text-[#111111] mb-4 border-b border-[#E5E5E5] pb-3">👨‍✈️ Driver ({{ $totalDrivers }})</h2>
        @php $drivers = \App\Models\User::where('agency_id', $agency->id)->where('role', 'driver')->get(); @endphp
        @if($drivers->isNotEmpty())
        <div class="grid md:grid-cols-3 gap-4">
            @foreach($drivers as $d)
            <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-4 flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-white border border-[#E5E5E5] flex items-center justify-center overflow-hidden flex-shrink-0">
                    @if($d->avatar_url)
                    <img src="{{ $d->avatar_url }}" class="w-full h-full object-cover">
                    @else
                    <span class="text-xl">👨‍✈️</span>
                    @endif
                </div>
                <div>
                    <p class="font-semibold text-[#111111] text-sm">{{ $d->name }}</p>
                    <p class="text-xs text-gray-500 font-light">{{ $d->phone ?? '-' }}</p>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-mono uppercase tracking-wider border {{ $d->is_active ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200' }}">
                        {{ $d->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-gray-400 text-sm font-light">Belum ada driver.</p>
        @endif
    </div>

    {{-- ═══════════════════════════════════════════════ --}}
    {{-- BOOKING TERBARU --}}
    {{-- ═══════════════════════════════════════════════ --}}
    <div class="grid md:grid-cols-2 gap-6 mb-6">
        {{-- Travel Bookings --}}
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
            <h2 class="font-bold text-lg text-[#111111] mb-4 border-b border-[#E5E5E5] pb-3">🚌 Booking Travel Terbaru</h2>
            @if($recentTravelBookings->isNotEmpty())
            <div class="space-y-3">
                @foreach($recentTravelBookings as $b)
                <a href="{{ route('admin.bookings.show', ['booking' => $b->id, 'type' => 'travel']) }}" class="block bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3 hover:border-[#C1121F] transition">
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="font-mono font-semibold text-[#111111] text-sm">{{ $b->booking_code }}</span>
                            <span class="text-xs text-gray-500 ml-2 font-light">{{ $b->customer->name ?? '-' }}</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-mono uppercase tracking-wider border
                            @if($b->status == 'paid') bg-green-50 text-green-700 border-green-200
                            @elseif($b->status == 'pending') bg-yellow-50 text-yellow-700 border-yellow-200
                            @else bg-[#F5F5F5] text-gray-600 border-[#E5E5E5] @endif">
                            {{ $b->status_label }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 font-mono mt-1">{{ $b->originStop->city_name ?? '?' }} → {{ $b->destinationStop->city_name ?? '?' }}</p>
                    <p class="text-xs text-[#C1121F] font-mono mt-1">Rp {{ number_format($b->total_price, 0, ',', '.') }}</p>
                </a>
                @endforeach
            </div>
            @else
            <p class="text-gray-400 text-sm font-light">Belum ada booking travel.</p>
            @endif
        </div>

        {{-- Tour Bookings --}}
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
            <h2 class="font-bold text-lg text-[#111111] mb-4 border-b border-[#E5E5E5] pb-3">🏝️ Booking Tour Terbaru</h2>
            @if($recentTourBookings->isNotEmpty())
            <div class="space-y-3">
                @foreach($recentTourBookings as $b)
                <a href="{{ route('admin.bookings.show', ['booking' => $b->id, 'type' => 'tour']) }}" class="block bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3 hover:border-[#C1121F] transition">
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="font-mono font-semibold text-[#111111] text-sm">{{ $b->booking_code }}</span>
                            <span class="text-xs text-gray-500 ml-2 font-light">{{ $b->group_name }}</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-mono uppercase tracking-wider border
                            @if($b->status == 'paid') bg-green-50 text-green-700 border-green-200
                            @elseif($b->status == 'pending') bg-yellow-50 text-yellow-700 border-yellow-200
                            @else bg-[#F5F5F5] text-gray-600 border-[#E5E5E5] @endif">
                            {{ $b->status_label }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 font-light mt-1">{{ $b->tourSchedule->tourPackage->name ?? '-' }}</p>
                    <p class="text-xs text-[#C1121F] font-mono mt-1">Rp {{ number_format($b->total_price, 0, ',', '.') }}</p>
                </a>
                @endforeach
            </div>
            @else
            <p class="text-gray-400 text-sm font-light">Belum ada booking tour.</p>
            @endif
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════ --}}
    {{-- DOKUMEN & GALERI --}}
    {{-- ═══════════════════════════════════════════════ --}}
    <div class="grid md:grid-cols-2 gap-6 mb-6">
        {{-- Dokumen --}}
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
            <h2 class="font-bold text-lg text-[#111111] mb-4 border-b border-[#E5E5E5] pb-3">📄 Dokumen</h2>
            @if($agency->business_license)
            <a href="{{ $agency->business_license }}" target="_blank" class="flex items-center gap-3 p-4 bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] hover:border-[#C1121F] transition">
                <span class="text-2xl">📄</span>
                <div>
                    <p class="font-medium text-[#111111] text-sm">Surat Izin Usaha</p>
                    <p class="text-xs text-[#C1121F] font-light">Klik untuk melihat dokumen</p>
                </div>
            </a>
            @else
            <p class="text-gray-400 text-sm font-light">Belum ada dokumen diupload.</p>
            @endif
        </div>

        {{-- Galeri --}}
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
            <h2 class="font-bold text-lg text-[#111111] mb-4 border-b border-[#E5E5E5] pb-3">📸 Galeri</h2>
            @if(!empty($gallery))
            <div class="grid grid-cols-3 gap-2">
                @foreach($gallery as $photo)
                <img src="{{ $photo }}" alt="Galeri {{ $agency->agency_name }}" class="w-full h-24 object-cover rounded-[8px] border border-[#E5E5E5] cursor-pointer" onclick="window.open('{{ $photo }}', '_blank')">
                @endforeach
            </div>
            @else
            <p class="text-gray-400 text-sm font-light">Belum ada foto galeri.</p>
            @endif
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════ --}}
    {{-- RIWAYAT VERIFIKASI --}}
    {{-- ═══════════════════════════════════════════════ --}}
    @php $verifications = $agency->verifications()->latest()->get(); @endphp
    @if($verifications->isNotEmpty())
    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
        <h2 class="font-bold text-lg text-[#111111] mb-4 border-b border-[#E5E5E5] pb-3">📝 Riwayat Verifikasi</h2>
        <div class="space-y-2">
            @foreach($verifications as $v)
            <div class="text-sm p-3 rounded-[12px] border
                @if($v->status == 'approved') bg-green-50 border-green-200
                @elseif($v->status == 'rejected') bg-red-50 border-red-200
                @else bg-yellow-50 border-yellow-200 @endif">
                <div class="flex justify-between">
                    <span class="font-medium text-[#111111]">
                        @if($v->status == 'approved') ✅ Disetujui
                        @elseif($v->status == 'rejected') ❌ Ditolak
                        @else ⏳ Pending @endif
                    </span>
                    <span class="text-gray-500 font-light">{{ $v->created_at->format('d M Y H:i') }}</span>
                </div>
                @if($v->verifier)
                <p class="text-xs text-gray-500 mt-1">Oleh: {{ $v->verifier->name }}</p>
                @endif
                @if($v->rejection_reason)
                <p class="text-red-600 mt-1 font-light text-xs">Alasan: {{ $v->rejection_reason }}</p>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

{{-- MODAL REJECT --}}
<div id="rejectModal" style="display:none;" class="fixed inset-0 bg-[#111111]/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-[12px] shadow-xl p-6 max-w-md w-full border border-[#E5E5E5]">
        <h3 class="font-bold text-lg text-[#111111] mb-2">Tolak Pengajuan Agency</h3>
        <p class="text-sm text-gray-500 font-light mb-4">Tulis alasan penolakan untuk {{ $agency->agency_name }}</p>
        <form action="{{ route('admin.agencies.reject', $agency) }}" method="POST">
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