@extends('layouts.admin')

@section('title', 'Laporan')
@section('content')

@php
    // Travel Stats
    $totalTravelBookings = \App\Models\Booking::count();
    $totalTravelRevenue = \App\Models\Booking::where('status', 'completed')->sum('total_price');
    $travelPending = \App\Models\Booking::where('status', 'pending')->count();
    $travelPaid = \App\Models\Booking::where('status', 'paid')->count();
    $travelCompleted = \App\Models\Booking::where('status', 'completed')->count();
    $travelCancelled = \App\Models\Booking::where('status', 'cancelled')->count();
    
    // Tour Stats
    $totalTourPackages = \App\Models\TourPackage::count();
    $totalTourBookings = \App\Models\TourBooking::count();
    $totalTourRevenue = \App\Models\TourBooking::where('status', 'completed')->sum('total_price');
    $tourPending = \App\Models\TourBooking::where('status', 'pending')->count();
    $tourPaid = \App\Models\TourBooking::where('status', 'paid')->count();
    $tourCompleted = \App\Models\TourBooking::where('status', 'completed')->count();
    $tourCancelled = \App\Models\TourBooking::where('status', 'cancelled')->count();
    
    // Rental Stats
    $totalRentalBookings = \App\Models\RentalBooking::count();
    $totalRentalRevenue = \App\Models\RentalBooking::where('status', 'completed')->sum('total_price');
    $rentalPending = \App\Models\RentalBooking::where('status', 'pending')->count();
    $rentalPaid = \App\Models\RentalBooking::where('status', 'paid')->count();
    $rentalCompleted = \App\Models\RentalBooking::where('status', 'completed')->count();
    $rentalCancelled = \App\Models\RentalBooking::where('status', 'cancelled')->count();
    
    // Users
    $totalAgencies = \App\Models\Agency::count();
    $totalCustomers = \App\Models\User::where('role', 'customer')->count();
    $totalWarungs = \App\Models\PaymentAgent::count();
    $totalDrivers = \App\Models\User::where('role', 'driver')->count();
    
    // Totals
    $totalRevenue = $totalTravelRevenue + $totalTourRevenue + $totalRentalRevenue;
    $totalBookings = $totalTravelBookings + $totalTourBookings + $totalRentalBookings;

    // Monthly revenue
    $monthlyLabels = [];
    $travelMonthlyRevenue = [];
    $tourMonthlyRevenue = [];
    $rentalMonthlyRevenue = [];
    for ($i = 5; $i >= 0; $i--) {
        $date = now()->subMonths($i);
        $monthlyLabels[] = $date->translatedFormat('M Y');
        $travelMonthlyRevenue[] = \App\Models\Booking::where('status', 'completed')->whereMonth('completed_at', $date->month)->whereYear('completed_at', $date->year)->sum('total_price');
        $tourMonthlyRevenue[] = \App\Models\TourBooking::where('status', 'completed')->whereMonth('completed_at', $date->month)->whereYear('completed_at', $date->year)->sum('total_price');
        $rentalMonthlyRevenue[] = \App\Models\RentalBooking::where('status', 'completed')->whereMonth('completed_at', $date->month)->whereYear('completed_at', $date->year)->sum('total_price');
    }
@endphp

<div>
    <div class="mb-6 border-b border-[#E5E5E5] pb-3">
        <h1 class="text-2xl font-bold text-[#111111]">📈 Laporan</h1>
        <p class="text-sm text-gray-500 font-light mt-1">Ringkasan data Travel, Tour & Rental</p>
    </div>

    {{-- ═══════════════════════════════════════════════ --}}
    {{-- OVERVIEW CARDS --}}
    {{-- ═══════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-8">
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-5 shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Total Booking</p>
            <p class="text-2xl font-bold text-[#111111] mt-1">{{ $totalBookings }}</p>
            <p class="text-[10px] text-gray-400 font-light">{{ $totalTravelBookings }} T + {{ $totalTourBookings }} W + {{ $totalRentalBookings }} R</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-5 shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Total Revenue</p>
            <p class="text-lg font-bold text-[#C1121F] mt-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-5 shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">🚌 Travel Revenue</p>
            <p class="text-lg font-bold text-[#C1121F] mt-1">Rp {{ number_format($totalTravelRevenue, 0, ',', '.') }}</p>
            <p class="text-[10px] text-gray-400 font-light">{{ $totalTravelBookings }} booking</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-5 shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">🏝️ Tour Revenue</p>
            <p class="text-lg font-bold text-green-600 mt-1">Rp {{ number_format($totalTourRevenue, 0, ',', '.') }}</p>
            <p class="text-[10px] text-gray-400 font-light">{{ $totalTourBookings }} booking</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-5 shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">🚐 Rental Revenue</p>
            <p class="text-lg font-bold text-blue-600 mt-1">Rp {{ number_format($totalRentalRevenue, 0, ',', '.') }}</p>
            <p class="text-[10px] text-gray-400 font-light">{{ $totalRentalBookings }} booking</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-5 shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Pending All</p>
            <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $travelPending + $tourPending + $rentalPending }}</p>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════ --}}
    {{-- REVENUE CHART — ALL SERVICES --}}
    {{-- ═══════════════════════════════════════════════ --}}
    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-6 shadow-sm">
        <h3 class="font-bold text-[#111111] mb-4">💰 Revenue 6 Bulan Terakhir — Semua Layanan</h3>
        <div class="relative" style="height: 350px;">
            <canvas id="allRevenueChart"></canvas>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════ --}}
    {{-- SUMMARY TABLE --}}
    {{-- ═══════════════════════════════════════════════ --}}
    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
        <h3 class="font-bold text-[#111111] mb-4">📊 Ringkasan Perbandingan</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-[#F5F5F5] border-b border-[#E5E5E5]">
                    <tr>
                        <th class="px-4 py-3 text-left font-mono uppercase tracking-wider text-xs text-gray-500">Metrik</th>
                        <th class="px-4 py-3 text-center font-mono uppercase tracking-wider text-xs text-gray-500">🚌 Travel</th>
                        <th class="px-4 py-3 text-center font-mono uppercase tracking-wider text-xs text-gray-500">🏝️ Tour</th>
                        <th class="px-4 py-3 text-center font-mono uppercase tracking-wider text-xs text-gray-500">🚐 Rental</th>
                        <th class="px-4 py-3 text-center font-mono uppercase tracking-wider text-xs text-gray-500">📊 Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E5E5]">
                    <tr class="hover:bg-[#F5F5F5]">
                        <td class="px-4 py-3 font-medium text-[#111111]">Total Booking</td>
                        <td class="px-4 py-3 text-center font-mono text-[#111111]">{{ $totalTravelBookings }}</td>
                        <td class="px-4 py-3 text-center font-mono text-[#111111]">{{ $totalTourBookings }}</td>
                        <td class="px-4 py-3 text-center font-mono text-[#111111]">{{ $totalRentalBookings }}</td>
                        <td class="px-4 py-3 text-center font-bold text-[#111111]">{{ $totalBookings }}</td>
                    </tr>
                    <tr class="hover:bg-[#F5F5F5]">
                        <td class="px-4 py-3 font-medium text-[#111111]">Total Revenue</td>
                        <td class="px-4 py-3 text-center font-mono text-[#C1121F]">Rp {{ number_format($totalTravelRevenue, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center font-mono text-green-600">Rp {{ number_format($totalTourRevenue, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center font-mono text-blue-600">Rp {{ number_format($totalRentalRevenue, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center font-bold text-[#C1121F]">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="hover:bg-[#F5F5F5]">
                        <td class="px-4 py-3 font-medium text-[#111111]">Pending</td>
                        <td class="px-4 py-3 text-center font-mono text-yellow-600">{{ $travelPending }}</td>
                        <td class="px-4 py-3 text-center font-mono text-yellow-600">{{ $tourPending }}</td>
                        <td class="px-4 py-3 text-center font-mono text-yellow-600">{{ $rentalPending }}</td>
                        <td class="px-4 py-3 text-center font-bold">{{ $travelPending + $tourPending + $rentalPending }}</td>
                    </tr>
                    <tr class="hover:bg-[#F5F5F5]">
                        <td class="px-4 py-3 font-medium text-[#111111]">Paid</td>
                        <td class="px-4 py-3 text-center font-mono text-green-600">{{ $travelPaid }}</td>
                        <td class="px-4 py-3 text-center font-mono text-green-600">{{ $tourPaid }}</td>
                        <td class="px-4 py-3 text-center font-mono text-green-600">{{ $rentalPaid }}</td>
                        <td class="px-4 py-3 text-center font-bold">{{ $travelPaid + $tourPaid + $rentalPaid }}</td>
                    </tr>
                    <tr class="hover:bg-[#F5F5F5]">
                        <td class="px-4 py-3 font-medium text-[#111111]">Completed</td>
                        <td class="px-4 py-3 text-center font-mono text-blue-600">{{ $travelCompleted }}</td>
                        <td class="px-4 py-3 text-center font-mono text-blue-600">{{ $tourCompleted }}</td>
                        <td class="px-4 py-3 text-center font-mono text-blue-600">{{ $rentalCompleted }}</td>
                        <td class="px-4 py-3 text-center font-bold">{{ $travelCompleted + $tourCompleted + $rentalCompleted }}</td>
                    </tr>
                    <tr class="hover:bg-[#F5F5F5]">
                        <td class="px-4 py-3 font-medium text-[#111111]">Cancelled</td>
                        <td class="px-4 py-3 text-center font-mono text-red-600">{{ $travelCancelled }}</td>
                        <td class="px-4 py-3 text-center font-mono text-red-600">{{ $tourCancelled }}</td>
                        <td class="px-4 py-3 text-center font-mono text-red-600">{{ $rentalCancelled }}</td>
                        <td class="px-4 py-3 text-center font-bold">{{ $travelCancelled + $tourCancelled + $rentalCancelled }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('allRevenueChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($monthlyLabels),
                datasets: [
                    {
                        label: 'Travel',
                        data: @json($travelMonthlyRevenue),
                        backgroundColor: 'rgba(193, 18, 31, 0.8)',
                        borderColor: '#C1121F',
                        borderWidth: 1,
                        borderRadius: 6,
                    },
                    {
                        label: 'Tour',
                        data: @json($tourMonthlyRevenue),
                        backgroundColor: 'rgba(16, 185, 129, 0.8)',
                        borderColor: '#10B981',
                        borderWidth: 1,
                        borderRadius: 6,
                    },
                    {
                        label: 'Rental',
                        data: @json($rentalMonthlyRevenue),
                        backgroundColor: 'rgba(59, 130, 246, 0.8)',
                        borderColor: '#3B82F6',
                        borderWidth: 1,
                        borderRadius: 6,
                    },
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: { family: 'Geist Sans', size: 12 },
                            color: '#111111',
                        },
                    },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                return ctx.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            font: { family: 'Geist Sans', size: 11 },
                            color: '#111111',
                            callback: function(val) {
                                if (val >= 1000000) return (val / 1000000).toFixed(1) + 'M';
                                if (val >= 1000) return (val / 1000).toFixed(0) + 'K';
                                return val;
                            }
                        },
                        grid: { color: '#E5E5E5' },
                    },
                    x: {
                        ticks: { font: { family: 'Geist Sans', size: 11 }, color: '#111111' },
                        grid: { display: false },
                    },
                },
            },
        });
    }
});
</script>
@endpush
@endsection