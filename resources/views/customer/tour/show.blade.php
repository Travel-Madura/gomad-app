@extends('layouts.customer')

@section('title', $package->name)
@section('content')

@php
    $itinerary = $package->itinerary ?? [];
    $includes = $package->includes ?? [];
    $excludes = $package->excludes ?? [];
@endphp

<div class="max-w-4xl mx-auto px-4 py-8">
    {{-- Breadcrumb --}}
    <a href="{{ route('customer.tour.index') }}" class="text-[#C1121F] text-sm mb-4 inline-block hover:underline">← Semua Paket Wisata</a>

    {{-- Cover --}}
    <div class="h-64 md:h-80 bg-[#F5F5F5] rounded-[12px] overflow-hidden mb-6 border border-[#E5E5E5]">
        @if($package->cover_image)
        <img src="{{ $package->cover_image }}" alt="{{ $package->name }}" class="w-full h-full object-cover">
        @else
        <div class="w-full h-full flex items-center justify-center text-6xl">🏝️</div>
        @endif
    </div>

    {{-- Header Info --}}
    <div class="mb-8">
        <div class="flex items-center gap-2 mb-2">
            <span class="px-2 py-1 rounded-full text-[10px] font-mono uppercase tracking-wider bg-green-50 text-green-700 border border-green-200">
                {{ $package->duration_days }}H{{ $package->duration_nights }}M
            </span>
            <span class="px-2 py-1 rounded-full text-[10px] font-mono uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-200">
                {{ $package->stops->count() }} Destinasi
            </span>
        </div>
        <h1 class="text-3xl font-bold text-[#111111] mb-3">{{ $package->name }}</h1>
        
        {{-- Agency --}}
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-[#F5F5F5] flex items-center justify-center overflow-hidden border border-[#E5E5E5]">
                @if($package->agency->logo)
                <img src="{{ $package->agency->logo }}" class="w-full h-full object-cover">
                @else 🏢 @endif
            </div>
            <div>
                <p class="font-semibold text-[#111111] text-sm">{{ $package->agency->agency_name }}</p>
                <p class="text-xs text-gray-500 font-light">⭐ {{ number_format($package->agency->rating, 1) }} • {{ $package->agency->total_bookings }} booking</p>
            </div>
        </div>
        
        <p class="text-gray-600 font-light leading-relaxed">{{ $package->description }}</p>
    </div>

    {{-- Includes & Excludes --}}
    <div class="grid md:grid-cols-2 gap-6 mb-8">
        @if(!empty($includes))
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
            <h3 class="font-bold text-[#111111] mb-3">✅ Sudah Termasuk</h3>
            <ul class="space-y-2 text-sm text-gray-600 font-light">
                @foreach((array) $includes as $item)
                <li class="flex items-start gap-2"><span class="text-green-500 mt-0.5">✓</span> {{ $item }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        @if(!empty($excludes))
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
            <h3 class="font-bold text-[#111111] mb-3">❌ Tidak Termasuk</h3>
            <ul class="space-y-2 text-sm text-gray-600 font-light">
                @foreach((array) $excludes as $item)
                <li class="flex items-start gap-2"><span class="text-red-500 mt-0.5">✗</span> {{ $item }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>

    {{-- Itinerary --}}
    @if(!empty($itinerary))
    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-8 shadow-sm">
        <h2 class="font-bold text-xl text-[#111111] mb-6">🗺️ Itinerary Perjalanan</h2>
        <div class="space-y-6">
            @foreach($itinerary as $day)
            <div class="border-l-4 border-[#C1121F] pl-4">
                <h4 class="font-bold text-[#111111]">
                    Hari {{ $day['day'] ?? '' }}
                    @if(!empty($day['title']))
                    : {{ $day['title'] }}
                    @endif
                </h4>
                @if(!empty($day['activities']))
                <ul class="mt-2 space-y-1 text-sm text-gray-600 font-light">
                    @foreach($day['activities'] as $activity)
                    <li class="flex items-start gap-2"><span class="text-[#C1121F] mt-1">•</span> {{ $activity }}</li>
                    @endforeach
                </ul>
                @endif
                @if(!empty($day['meals']))
                <div class="mt-2 flex flex-wrap gap-1">
                    @foreach($day['meals'] as $meal)
                    <span class="text-[10px] font-mono uppercase tracking-wider bg-orange-50 text-orange-700 px-2 py-0.5 rounded-full border border-orange-200">{{ $meal }}</span>
                    @endforeach
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Schedules --}}
    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
        <h2 class="font-bold text-xl text-[#111111] mb-6">📅 Jadwal Keberangkatan</h2>
        
        @if($schedules->isEmpty())
        <p class="text-gray-500 text-center py-8 font-light">Belum ada jadwal keberangkatan yang tersedia.</p>
        @else
        <div class="space-y-4">
            @foreach($schedules as $schedule)
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 p-5 border border-[#E5E5E5] rounded-[12px] hover:border-[#C1121F] transition-colors">
                <div class="flex-1">
                    <div class="flex flex-wrap items-center gap-3 mb-2">
                        <span class="font-bold text-[#111111] font-mono">{{ $schedule->departure_date->format('d M Y') }}</span>
                        <span class="text-gray-500 font-mono">{{ $schedule->departure_time }}</span>
                        @if($schedule->return_date)
                        <span class="text-gray-400">→</span>
                        <span class="text-gray-500 font-light">{{ $schedule->return_date->format('d M Y') }}</span>
                        @endif
                        <span class="text-xs text-gray-400 font-mono">🚐 {{ $schedule->vehicle->plate_number ?? '-' }}</span>
                    </div>
                    
                    @if($schedule->is_full)
                    <span class="px-2 py-1 rounded-full text-[10px] font-mono uppercase tracking-wider bg-red-50 text-red-700 border border-red-200">
                        Penuh ({{ $schedule->booked_seats ?? 0 }}/{{ $schedule->max_participants }})
                    </span>
                    @else
                    <span class="px-2 py-1 rounded-full text-[10px] font-mono uppercase tracking-wider bg-green-50 text-green-700 border border-green-200">
                        Tersedia {{ $schedule->available_seats ?? $schedule->max_participants }} kursi
                    </span>
                    @endif
                </div>
                
                <div class="flex items-center gap-4 flex-shrink-0">
                    <div class="text-right">
                        <p class="text-xs text-gray-400 font-light">Harga per orang</p>
                        <p class="font-bold text-[#C1121F] font-mono text-xl">Rp {{ number_format($schedule->base_price, 0, ',', '.') }}</p>
                        @if($schedule->child_price)
                        <p class="text-xs text-gray-500 font-light">Anak: Rp {{ number_format($schedule->child_price, 0, ',', '.') }}</p>
                        @endif
                    </div>
                    
                    @if(!$schedule->is_full)
                    <a href="{{ route('customer.tour.booking.create', ['schedule_id' => $schedule->id]) }}" 
                       class="btn-gomad-primary px-6 py-3 rounded-[12px] text-sm font-semibold whitespace-nowrap">
                        Booking
                    </a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection