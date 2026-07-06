@extends('layouts.admin')

@section('title', 'Laporan')
@section('content')

@php
    $totalBookings = \App\Models\Booking::count();
    $totalRevenue = \App\Models\Booking::where('status', 'completed')->sum('total_price');
    $totalAgencies = \App\Models\Agency::count();
    $totalCustomers = \App\Models\User::where('role', 'customer')->count();
    $totalWarungs = \App\Models\PaymentAgent::count();

    $monthlyRevenue = [];
    $monthlyLabels = [];
    for ($i = 5; $i >= 0; $i--) {
        $date = now()->subMonths($i);
        $monthlyLabels[] = $date->translatedFormat('M Y');
        $monthlyRevenue[] = \App\Models\Booking::where('status', 'completed')
            ->whereMonth('completed_at', $date->month)
            ->whereYear('completed_at', $date->year)
            ->sum('total_price');
    }

    $topRoutes = \App\Models\Route::withCount(['schedules as bookings_count' => function($q) {
            $q->whereHas('bookings');
        }])
        ->orderByDesc('bookings_count')
        ->limit(5)
        ->get();
    $routeLabels = $topRoutes->pluck('route_name')->toArray();
    $routeCounts = $topRoutes->pluck('bookings_count')->toArray();
@endphp

<div>
    <h1 class="text-lg font-bold text-[#111111] mb-6">Laporan</h1>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-5 shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Total Booking</p>
            <p class="text-2xl font-bold text-[#111111] mt-1">{{ $totalBookings }}</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-5 shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Total Revenue</p>
            <p class="text-lg font-bold text-[#C1121F] mt-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-5 shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Agency</p>
            <p class="text-2xl font-bold text-[#111111] mt-1">{{ $totalAgencies }}</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-5 shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Customer</p>
            <p class="text-2xl font-bold text-[#111111] mt-1">{{ $totalCustomers }}</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-5 shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Warung</p>
            <p class="text-2xl font-bold text-[#111111] mt-1">{{ $totalWarungs }}</p>
        </div>
    </div>

    {{-- Charts --}}
    <div class="grid md:grid-cols-2 gap-6">
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
            <h3 class="font-bold text-[#111111] mb-4">💰 Revenue 6 Bulan Terakhir</h3>
            <div class="relative" style="height: 320px;">
                <canvas id="monthlyRevenueChart"></canvas>
            </div>
        </div>

        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
            <h3 class="font-bold text-[#111111] mb-4">🎫 Booking per Rute (Top 5)</h3>
            <div class="relative" style="height: 320px;">
                <canvas id="routeBookingChart"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const revenueCtx = document.getElementById('monthlyRevenueChart');
    if (revenueCtx) {
        new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels: @json($monthlyLabels),
                datasets: [{
                    label: 'Revenue',
                    data: @json($monthlyRevenue),
                    backgroundColor: 'rgba(193, 18, 31, 0.8)',
                    borderColor: '#C1121F',
                    borderWidth: 1,
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw);
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
                        ticks: {
                            font: { family: 'Geist Sans', size: 11 },
                            color: '#111111',
                        },
                        grid: { display: false },
                    },
                },
            },
        });
    }

    const routeCtx = document.getElementById('routeBookingChart');
    if (routeCtx) {
        new Chart(routeCtx, {
            type: 'bar',
            data: {
                labels: @json($routeLabels),
                datasets: [{
                    label: 'Booking',
                    data: @json($routeCounts),
                    backgroundColor: ['#C1121F', '#F59E0B', '#3B82F6', '#10B981', '#8B5CF6'],
                    borderWidth: 0,
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            font: { family: 'Geist Sans', size: 11 },
                            color: '#111111',
                        },
                        grid: { color: '#E5E5E5' },
                    },
                    y: {
                        ticks: {
                            font: { family: 'Geist Sans', size: 11 },
                            color: '#4B5563',
                        },
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