@extends('layouts.driver')

@section('title', 'Jadwal Saya')
@section('content')
@php
    $driverService = app(\App\Services\DriverService::class);
    $todaySchedule = $driverService->getDriverTodaySchedule(auth()->user());
    $upcomingSchedules = $driverService->getDriverUpcomingSchedules(auth()->user(), 7);
@endphp

<div>
    <h1 class="text-2xl font-bold text-[#111111] mb-6">Jadwal Saya</h1>

    {{-- Jadwal Hari Ini --}}
    <div class="mb-8">
        <h2 class="font-bold text-lg text-[#111111] mb-4 border-b border-[#E5E5E5] pb-2">Hari Ini</h2>
        
        @if($todaySchedule)
        <a href="{{ route('driver.schedule.show', $todaySchedule) }}" class="block bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm hover:border-[#C1121F] transition border-l-4 border-[#C1121F]">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
                <div>
                    <h3 class="font-bold text-xl text-[#111111]">{{ $todaySchedule->route->route_name }}</h3>
                    <div class="flex items-center gap-3 text-sm text-gray-500 mt-2 font-light">
                        <span class="font-mono">{{ $todaySchedule->departure_time }}</span>
                        <span>|</span>
                        <span class="font-mono">{{ $todaySchedule->vehicle->plate_number ?? '-' }}</span>
                        <span>|</span>
                        <span>{{ $todaySchedule->vehicle->brand ?? '' }} {{ $todaySchedule->vehicle->model ?? '' }}</span>
                    </div>
                    <p class="text-sm text-gray-500 mt-1 font-light">
                        {{ $todaySchedule->route->origin_city }} → {{ $todaySchedule->route->destination_city }}
                    </p>
                </div>
                <div class="text-right flex-shrink-0">
                    @if(!$todaySchedule->started_at)
                    <span class="px-3 py-1 bg-yellow-50 text-yellow-700 border border-yellow-200 rounded-full text-[10px] font-mono uppercase tracking-wider">Menunggu Agency</span>
                    @elseif(!$todaySchedule->finished_at)
                    <span class="px-3 py-1 bg-blue-50 text-blue-700 border border-blue-200 rounded-full text-[10px] font-mono uppercase tracking-wider">Dalam Perjalanan</span>
                    @else
                    <span class="px-3 py-1 bg-green-50 text-green-700 border border-green-200 rounded-full text-[10px] font-mono uppercase tracking-wider">Selesai</span>
                    @endif
                    
                    @php
                        $totalPassengers = $todaySchedule->bookings->sum('total_passengers');
                        $completedBookings = $todaySchedule->bookings->where('status', 'completed')->count();
                        $totalBookings = $todaySchedule->bookings->count();
                    @endphp
                    <p class="text-sm text-gray-500 mt-2 font-light">{{ $totalPassengers }} penumpang</p>
                    @if($totalBookings > 0)
                    <p class="text-xs text-gray-400 font-light font-mono">{{ $completedBookings }}/{{ $totalBookings }} selesai</p>
                    @endif
                </div>
            </div>
            
            @if($totalBookings > 0)
            <div class="mt-4 bg-[#E5E5E5] rounded-full h-2 overflow-hidden">
                <div class="bg-[#C1121F] h-full rounded-full transition-all" style="width: {{ ($completedBookings / $totalBookings) * 100 }}%"></div>
            </div>
            @endif
        </a>
        @else
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-8 text-center shadow-sm">
            <div class="w-16 h-16 bg-[#C1121F]/5 rounded-[12px] flex items-center justify-center mx-auto mb-3 border border-[#E5E5E5]">
                <span class="text-2xl">📅</span>
            </div>
            <p class="text-gray-500 font-light">Tidak ada jadwal hari ini.</p>
        </div>
        @endif
    </div>

    {{-- Jadwal Mendatang --}}
    <div>
        <h2 class="font-bold text-lg text-[#111111] mb-4 border-b border-[#E5E5E5] pb-2">Jadwal Mendatang</h2>
        
        @if($upcomingSchedules->isNotEmpty())
        <div class="space-y-3">
            @foreach($upcomingSchedules as $schedule)
            <a href="{{ route('driver.schedule.show', $schedule) }}" class="block bg-white border border-[#E5E5E5] rounded-[12px] p-4 shadow-sm hover:border-[#C1121F] transition">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="font-semibold text-[#111111]">{{ $schedule->route->route_name }}</h3>
                        <p class="text-sm text-gray-500 font-light">{{ $schedule->departure_date->format('d M Y') }} {{ $schedule->departure_time }}</p>
                        <p class="text-xs text-gray-500 font-light font-mono">{{ $schedule->vehicle->plate_number ?? '-' }}</p>
                    </div>
                    <span class="text-xs text-gray-400 font-light">{{ $schedule->departure_date->diffForHumans() }}</span>
                </div>
            </a>
            @endforeach
        </div>
        @else
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-8 text-center shadow-sm">
            <p class="text-gray-500 font-light">Tidak ada jadwal mendatang.</p>
        </div>
        @endif
    </div>
</div>
@endsection