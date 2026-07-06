@extends('layouts.agency')

@section('title', 'Dashboard')
@section('content')
@php
    $agency = auth()->user()->agency;
@endphp

@if(!$agency)
<div class="text-center py-12">
    <div class="w-20 h-20 bg-[#C1121F]/5 rounded-[12px] flex items-center justify-center mx-auto mb-4 border border-[#E5E5E5]">
        <span class="text-3xl">🏢</span>
    </div>
    <h2 class="text-xl font-bold text-[#111111] mb-2">Setup Agency</h2>
    <p class="text-gray-600 font-light mb-6">Lengkapi data agency Anda untuk mulai beroperasi.</p>
    <a href="{{ route('agency.setup') }}" class="btn-gomad-primary">Setup Sekarang</a>
</div>

@elseif(!$agency->is_verified)
<div class="text-center py-12">
    <div class="w-20 h-20 bg-yellow-50 border border-yellow-200 rounded-[12px] flex items-center justify-center mx-auto mb-4">
        <span class="text-3xl">⏳</span>
    </div>
    <h2 class="text-xl font-bold text-[#111111] mb-2">Menunggu Verifikasi</h2>
    <p class="text-gray-600 max-w-md mx-auto mb-6 font-light">Agency Anda sedang dalam proses verifikasi. Lengkapi profil dan ajukan verifikasi untuk mengakses semua fitur.</p>
    <a href="{{ route('agency.profile.edit') }}" class="btn-gomad-primary">Lengkapi Profil</a>
</div>

@else
@php
    $today = \Carbon\Carbon::today();
    $walletService = app(\App\Services\WalletService::class);
    $balance = $walletService->getBalance($agency);
    $todaySchedules = $agency->schedules()->where('departure_date', $today)->where('is_active', true)->count();
    $monthBookings = \App\Models\Booking::whereHas('schedule', fn($q) => $q->where('agency_id', $agency->id))->whereMonth('created_at', now()->month)->count();
    $monthRevenue = \App\Models\Booking::whereHas('schedule', fn($q) => $q->where('agency_id', $agency->id))->where('status', 'completed')->whereMonth('completed_at', now()->month)->sum('total_price');
    $activeVehicles = $agency->vehicles()->where('is_active', true)->count();
    $activeDrivers = $agency->drivers()->where('is_active', true)->count();

    $dailyBookings = [];
    $dailyLabels = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = now()->subDays($i);
        $dailyLabels[] = $date->translatedFormat('d M');
        $dailyBookings[] = \App\Models\Booking::whereHas('schedule', fn($q) => $q->where('agency_id', $agency->id))
            ->whereDate('created_at', $date)->count();
    }

    $monthlyRevenue = [];
    $monthlyLabels = [];
    for ($i = 5; $i >= 0; $i--) {
        $date = now()->subMonths($i);
        $monthlyLabels[] = $date->translatedFormat('M Y');
        $monthlyRevenue[] = \App\Models\Booking::whereHas('schedule', fn($q) => $q->where('agency_id', $agency->id))
            ->where('status', 'completed')
            ->whereMonth('completed_at', $date->month)
            ->whereYear('completed_at', $date->year)
            ->sum('total_price');
    }
@endphp

<div>
    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-5 shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Saldo Tersedia</p>
            <p class="text-xl font-bold text-[#C1121F] mt-1">Rp {{ number_format($balance['available_balance'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-5 shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Jadwal Hari Ini</p>
            <p class="text-2xl font-bold text-[#111111] mt-1">{{ $todaySchedules }}</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-5 shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Booking Bulan Ini</p>
            <p class="text-2xl font-bold text-[#111111] mt-1">{{ $monthBookings }}</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-5 shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Revenue Bulan Ini</p>
            <p class="text-lg font-bold text-green-600 mt-1">Rp {{ number_format($monthRevenue, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Charts --}}
    <div class="grid md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
            <h3 class="font-bold text-[#111111] mb-4">📈 Booking 7 Hari Terakhir</h3>
            <div class="relative" style="height: 280px;">
                <canvas id="agencyDailyChart"></canvas>
            </div>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
            <h3 class="font-bold text-[#111111] mb-4">💰 Revenue 6 Bulan Terakhir</h3>
            <div class="relative" style="height: 280px;">
                <canvas id="agencyRevenueChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('agency.schedules.create') }}" class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 text-center shadow-sm hover:border-[#C1121F] transition-colors group">
            <div class="w-12 h-12 bg-[#C1121F]/5 rounded-[12px] flex items-center justify-center text-xl mx-auto mb-3 group-hover:scale-110 transition-transform border border-[#E5E5E5]">📅</div>
            <p class="font-semibold text-[#111111] text-sm">Buat Jadwal</p>
        </a>
        <a href="{{ route('agency.bookings.index') }}" class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 text-center shadow-sm hover:border-[#C1121F] transition-colors group">
            <div class="w-12 h-12 bg-[#C1121F]/5 rounded-[12px] flex items-center justify-center text-xl mx-auto mb-3 group-hover:scale-110 transition-transform border border-[#E5E5E5]">🎫</div>
            <p class="font-semibold text-[#111111] text-sm">Lihat Booking</p>
        </a>
        <a href="{{ route('agency.wallet.index') }}" class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 text-center shadow-sm hover:border-[#C1121F] transition-colors group">
            <div class="w-12 h-12 bg-[#C1121F]/5 rounded-[12px] flex items-center justify-center text-xl mx-auto mb-3 group-hover:scale-110 transition-transform border border-[#E5E5E5]">💰</div>
            <p class="font-semibold text-[#111111] text-sm">Dompet</p>
        </a>
        <a href="{{ route('agency.transfers.index') }}" class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 text-center shadow-sm hover:border-[#C1121F] transition-colors group">
            <div class="w-12 h-12 bg-[#C1121F]/5 rounded-[12px] flex items-center justify-center text-xl mx-auto mb-3 group-hover:scale-110 transition-transform border border-[#E5E5E5]">🔄</div>
            <p class="font-semibold text-[#111111] text-sm">Transfer</p>
        </a>
    </div>
</div>
@endif
@endsection

@push('scripts')
@if($agency && $agency->is_verified)
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dailyCtx = document.getElementById('agencyDailyChart');
    if (dailyCtx) {
        new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: @json($dailyLabels),
                datasets: [{
                    label: 'Booking',
                    data: @json($dailyBookings),
                    borderColor: '#C1121F',
                    backgroundColor: 'rgba(193, 18, 31, 0.05)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#C1121F',
                    pointBorderColor: '#FFFFFF',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { 
                        display: false,
                        labels: { color: '#111111' } // Pastikan teks legend gelap
                    } 
                },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        ticks: { 
                            stepSize: 1, 
                            font: { family: 'Geist Sans', size: 11 }, 
                            color: '#111111' // Warna teks sumbu Y
                        }, 
                        grid: { color: '#E5E5E5' } // Warna grid
                    },
                    x: { 
                        ticks: { font: { family: 'Geist Sans', size: 11 }, color: '#111111' }, 
                        grid: { display: false } 
                    },
                },
            },
        });
    }

    const revCtx = document.getElementById('agencyRevenueChart');
    if (revCtx) {
        new Chart(revCtx, {
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
                    legend: { 
                        display: false,
                        labels: { color: '#111111' }
                    },
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
                        ticks: { font: { family: 'Geist Sans', size: 11 }, color: '#111111' },
                        grid: { display: false },
                    },
                },
            },
        });
    }
});
</script>
@endif
@endpush