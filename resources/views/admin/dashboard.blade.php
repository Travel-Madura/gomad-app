@extends('layouts.admin')

@section('title', 'Dashboard')
@section('content')
@php
    // User stats
    $totalCustomers = \App\Models\User::where('role', 'customer')->count();
    $totalAgencies = \App\Models\Agency::count();
    $verifiedAgencies = \App\Models\Agency::where('is_verified', true)->count();
    $pendingAgencies = \App\Models\Agency::where('is_verified', false)->whereHas('verifications', fn($q) => $q->where('status', 'pending'))->count();
    $totalDrivers = \App\Models\User::where('role', 'driver')->count();
    $totalWarungs = \App\Models\PaymentAgent::where('is_verified', true)->count();
    $pendingWarungs = \App\Models\PaymentAgent::where('is_verified', false)->whereNotNull('agent_name')->count();
    
    // Travel stats
    $totalTravelBookings = \App\Models\Booking::count();
    $monthTravelRevenue = \App\Models\Booking::where('status', 'completed')->whereMonth('completed_at', now()->month)->sum('total_price');
    $pendingTravelWithdrawals = \App\Models\Withdrawal::where('status', 'pending')->count();

    // Tour stats
    $totalTourPackages = \App\Models\TourPackage::where('is_active', true)->count();
    $totalTourBookings = \App\Models\TourBooking::count();
    $monthTourRevenue = \App\Models\TourBooking::where('status', 'completed')->whereMonth('completed_at', now()->month)->sum('total_price');
    $activeTourSchedules = \App\Models\TourSchedule::where('is_active', true)->where('departure_date', '>=', now()->toDateString())->count();

    // Rental stats
    $totalRentalBookings = \App\Models\RentalBooking::count();
    $monthRentalRevenue = \App\Models\RentalBooking::where('status', 'completed')->whereMonth('completed_at', now()->month)->sum('total_price');
    $activeRentalVehicles = \App\Models\Vehicle::availableForRental()->count();

    // Charts - Travel
    $dailyLabels = [];
    $dailyTravelBookings = [];
    $dailyTourBookings = [];
    $dailyRentalBookings = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = now()->subDays($i);
        $dailyLabels[] = $date->translatedFormat('d M');
        $dailyTravelBookings[] = \App\Models\Booking::whereDate('created_at', $date)->count();
        $dailyTourBookings[] = \App\Models\TourBooking::whereDate('created_at', $date)->count();
        $dailyRentalBookings[] = \App\Models\RentalBooking::whereDate('created_at', $date)->count();
    }

    $userDistribution = [$totalCustomers, $totalAgencies, $totalDrivers, $totalWarungs];
    $totalRevenue = $monthTravelRevenue + $monthTourRevenue + $monthRentalRevenue;
@endphp

<div>
    {{-- ═══════════════════════════════════════════════ --}}
    {{-- STATS CARDS — ALL SERVICES --}}
    {{-- ═══════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-5 shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Customer</p>
            <p class="text-2xl font-bold text-[#111111] mt-1">{{ $totalCustomers }}</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-5 shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Agency</p>
            <p class="text-2xl font-bold text-[#111111] mt-1">{{ $totalAgencies }}</p>
            <p class="text-[10px] font-mono text-green-600 mt-1">{{ $verifiedAgencies }} verified</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-5 shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">🚌 Travel Revenue</p>
            <p class="text-lg font-bold text-[#C1121F] mt-1">Rp {{ number_format($monthTravelRevenue, 0, ',', '.') }}</p>
            <p class="text-[10px] text-gray-400 font-light">{{ $totalTravelBookings }} booking</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-5 shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">🏝️ Tour Revenue</p>
            <p class="text-lg font-bold text-green-600 mt-1">Rp {{ number_format($monthTourRevenue, 0, ',', '.') }}</p>
            <p class="text-[10px] text-gray-400 font-light">{{ $totalTourBookings }} booking</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-5 shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">🚐 Rental Revenue</p>
            <p class="text-lg font-bold text-blue-600 mt-1">Rp {{ number_format($monthRentalRevenue, 0, ',', '.') }}</p>
            <p class="text-[10px] text-gray-400 font-light">{{ $totalRentalBookings }} booking</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-5 shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Total Revenue</p>
            <p class="text-lg font-bold text-purple-600 mt-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
            <p class="text-[10px] text-gray-400 font-light">Bulan ini</p>
        </div>
    </div>

    {{-- MORE STATS --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-4 shadow-sm text-center">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Driver</p>
            <p class="text-2xl font-bold text-[#111111]">{{ $totalDrivers }}</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-4 shadow-sm text-center">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Warung</p>
            <p class="text-2xl font-bold text-[#111111]">{{ $totalWarungs }}</p>
            <p class="text-[10px] text-yellow-600">{{ $pendingWarungs }} pending</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-4 shadow-sm text-center">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">🏝️ Paket Tour</p>
            <p class="text-2xl font-bold text-purple-600">{{ $totalTourPackages }}</p>
            <p class="text-[10px] text-gray-400">{{ $activeTourSchedules }} jadwal aktif</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-4 shadow-sm text-center">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">🚐 Kendaraan Rental</p>
            <p class="text-2xl font-bold text-blue-600">{{ $activeRentalVehicles }}</p>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════ --}}
    {{-- CHARTS — ALL SERVICES --}}
    {{-- ═══════════════════════════════════════════════ --}}
    <div class="grid md:grid-cols-3 gap-6 mb-8">
        {{-- Travel Bookings --}}
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
            <h3 class="font-bold text-[#111111] mb-4">🚌 Booking Travel 7 Hari</h3>
            <div class="relative" style="height: 250px;">
                <canvas id="dailyTravelChart"></canvas>
            </div>
        </div>

        {{-- Tour Bookings --}}
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
            <h3 class="font-bold text-[#111111] mb-4">🏝️ Booking Tour 7 Hari</h3>
            <div class="relative" style="height: 250px;">
                <canvas id="dailyTourChart"></canvas>
            </div>
        </div>

        {{-- Rental Bookings --}}
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
            <h3 class="font-bold text-[#111111] mb-4">🚐 Booking Rental 7 Hari</h3>
            <div class="relative" style="height: 250px;">
                <canvas id="dailyRentalChart"></canvas>
            </div>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6 mb-8">
        {{-- User Distribution --}}
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
            <h3 class="font-bold text-[#111111] mb-4">👥 Distribusi Pengguna</h3>
            <div class="relative flex justify-center" style="height: 280px;">
                <canvas id="userDistributionChart"></canvas>
            </div>
        </div>

        {{-- Revenue Comparison --}}
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
            <h3 class="font-bold text-[#111111] mb-4">💰 Perbandingan Revenue Bulan Ini</h3>
            <div class="relative" style="height: 280px;">
                <canvas id="revenueComparisonChart"></canvas>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════ --}}
    {{-- QUICK ACTIONS --}}
    {{-- ═══════════════════════════════════════════════ --}}
    <div class="grid md:grid-cols-3 gap-6">
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
            <h3 class="font-bold text-[#111111] mb-4">Verifikasi Agency</h3>
            @if($pendingAgencies > 0)
            <p class="text-sm text-yellow-600 mb-3 font-light">{{ $pendingAgencies }} menunggu verifikasi</p>
            <a href="{{ route('admin.agencies.index') }}" class="text-[#C1121F] text-sm font-medium hover:underline">Lihat Semua →</a>
            @else
            <p class="text-sm text-gray-500 font-light">Tidak ada yang pending.</p>
            @endif
        </div>

        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
            <h3 class="font-bold text-[#111111] mb-4">Withdrawal Pending</h3>
            @if($pendingTravelWithdrawals > 0)
            <p class="text-sm text-yellow-600 mb-3 font-light">{{ $pendingTravelWithdrawals }} menunggu approval</p>
            <a href="{{ route('admin.withdrawals.index') }}" class="text-[#C1121F] text-sm font-medium hover:underline">Lihat Semua →</a>
            @else
            <p class="text-sm text-gray-500 font-light">Tidak ada yang pending.</p>
            @endif
        </div>

        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
            <h3 class="font-bold text-[#111111] mb-4">Aksi Cepat</h3>
            <div class="space-y-2">
                <a href="{{ route('admin.routes.create') }}" class="block text-sm text-[#C1121F] font-medium hover:underline">+ Tambah Rute Travel</a>
                <a href="{{ route('admin.promos.create') }}" class="block text-sm text-[#C1121F] font-medium hover:underline">+ Buat Promo Travel</a>
                <a href="{{ route('admin.tour-promos.create') }}" class="block text-sm text-[#C1121F] font-medium hover:underline">+ Buat Promo Tour</a>
                <a href="{{ route('admin.bookings.index', ['type' => 'rental']) }}" class="block text-sm text-[#C1121F] font-medium hover:underline">Lihat Booking Rental</a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Travel Chart
    const travelCtx = document.getElementById('dailyTravelChart');
    if (travelCtx) {
        new Chart(travelCtx, {
            type: 'line',
            data: {
                labels: @json($dailyLabels),
                datasets: [{
                    label: 'Travel', data: @json($dailyTravelBookings),
                    borderColor: '#C1121F', backgroundColor: 'rgba(193, 18, 31, 0.05)',
                    borderWidth: 2, fill: true, tension: 0.4,
                    pointBackgroundColor: '#C1121F', pointBorderColor: '#FFFFFF',
                    pointBorderWidth: 2, pointRadius: 5, pointHoverRadius: 7,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1, font: { family: 'Geist Sans', size: 11 }, color: '#111111' }, grid: { color: '#E5E5E5' } },
                    x: { ticks: { font: { family: 'Geist Sans', size: 11 }, color: '#111111' }, grid: { display: false } },
                },
            },
        });
    }

    // Tour Chart
    const tourCtx = document.getElementById('dailyTourChart');
    if (tourCtx) {
        new Chart(tourCtx, {
            type: 'line',
            data: {
                labels: @json($dailyLabels),
                datasets: [{
                    label: 'Tour', data: @json($dailyTourBookings),
                    borderColor: '#10B981', backgroundColor: 'rgba(16, 185, 129, 0.05)',
                    borderWidth: 2, fill: true, tension: 0.4,
                    pointBackgroundColor: '#10B981', pointBorderColor: '#FFFFFF',
                    pointBorderWidth: 2, pointRadius: 5, pointHoverRadius: 7,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1, font: { family: 'Geist Sans', size: 11 }, color: '#111111' }, grid: { color: '#E5E5E5' } },
                    x: { ticks: { font: { family: 'Geist Sans', size: 11 }, color: '#111111' }, grid: { display: false } },
                },
            },
        });
    }

    // Rental Chart
    const rentalCtx = document.getElementById('dailyRentalChart');
    if (rentalCtx) {
        new Chart(rentalCtx, {
            type: 'line',
            data: {
                labels: @json($dailyLabels),
                datasets: [{
                    label: 'Rental', data: @json($dailyRentalBookings),
                    borderColor: '#3B82F6', backgroundColor: 'rgba(59, 130, 246, 0.05)',
                    borderWidth: 2, fill: true, tension: 0.4,
                    pointBackgroundColor: '#3B82F6', pointBorderColor: '#FFFFFF',
                    pointBorderWidth: 2, pointRadius: 5, pointHoverRadius: 7,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1, font: { family: 'Geist Sans', size: 11 }, color: '#111111' }, grid: { color: '#E5E5E5' } },
                    x: { ticks: { font: { family: 'Geist Sans', size: 11 }, color: '#111111' }, grid: { display: false } },
                },
            },
        });
    }

    // User Distribution
    const distCtx = document.getElementById('userDistributionChart');
    if (distCtx) {
        new Chart(distCtx, {
            type: 'doughnut',
            data: {
                labels: ['Customer', 'Agency', 'Driver', 'Warung'],
                datasets: [{
                    data: @json($userDistribution),
                    backgroundColor: ['#C1121F', '#F59E0B', '#3B82F6', '#10B981'],
                    borderColor: '#FFFFFF', borderWidth: 3, hoverBorderWidth: 4,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { padding: 20, usePointStyle: true, pointStyleWidth: 10, font: { family: 'Geist Sans', size: 12 }, color: '#111111' } } },
            },
        });
    }

    // Revenue Comparison
    const revCtx = document.getElementById('revenueComparisonChart');
    if (revCtx) {
        new Chart(revCtx, {
            type: 'bar',
            data: {
                labels: ['Travel', 'Tour', 'Rental'],
                datasets: [{
                    label: 'Revenue',
                    data: [{{ $monthTravelRevenue }}, {{ $monthTourRevenue }}, {{ $monthRentalRevenue }}],
                    backgroundColor: ['#C1121F', '#10B981', '#3B82F6'],
                    borderRadius: 8, borderSkipped: false,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { 
                    legend: { display: false },
                    tooltip: { callbacks: { label: ctx => 'Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw) } }
                },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        ticks: { font: { family: 'Geist Sans', size: 11 }, color: '#111111', callback: v => v >= 1000000 ? (v/1000000).toFixed(1)+'M' : v >= 1000 ? (v/1000).toFixed(0)+'K' : v }, 
                        grid: { color: '#E5E5E5' } 
                    },
                    x: { ticks: { font: { family: 'Geist Sans', size: 11 }, color: '#111111' }, grid: { display: false } },
                },
            },
        });
    }
});
</script>
@endpush
@endsection