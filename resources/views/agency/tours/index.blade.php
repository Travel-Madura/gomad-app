@extends('layouts.agency')

@section('title', 'Paket Wisata')
@section('content')

@php
    $activeCount = $activeCount ?? 0;
    $totalSchedules = $totalSchedules ?? 0;
@endphp

<div>
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6 border-b border-[#E5E5E5] pb-3">
        <div>
            <h1 class="text-2xl font-bold text-[#111111]">🏝️ Paket Wisata</h1>
            <p class="text-sm text-gray-500 font-light mt-1">Kelola paket tour dan jadwal keberangkatan</p>
        </div>
        <a href="{{ route('agency.tours.create') }}" 
           class="btn-gomad-primary text-sm inline-flex items-center gap-2 self-start rounded-[12px] px-4 py-2.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Buat Paket Tour
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-5 shadow-sm text-center">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Total Paket</p>
            <p class="text-2xl font-bold text-[#111111] mt-1">{{ $packages->count() }}</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-5 shadow-sm text-center">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Paket Aktif</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $activeCount }}</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-5 shadow-sm text-center">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Jadwal Aktif</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $totalSchedules }}</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-5 shadow-sm text-center">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Total Booking</p>
            @php
                $totalTourBookings = \App\Models\TourBooking::whereHas('tourSchedule.tourPackage', 
                    fn($q) => $q->where('agency_id', auth()->user()->agency->id)
                )->count();
            @endphp
            <p class="text-2xl font-bold text-purple-600 mt-1">{{ $totalTourBookings }}</p>
        </div>
    </div>

    {{-- Paket List --}}
    @if($packages->isEmpty())
    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-16 text-center shadow-sm">
        <div class="w-20 h-20 bg-[#C1121F]/5 rounded-[12px] flex items-center justify-center mx-auto mb-4 border border-[#E5E5E5]">
            <span class="text-4xl">🏝️</span>
        </div>
        <h2 class="text-xl font-bold text-[#111111] mb-2">Belum Ada Paket Tour</h2>
        <p class="text-gray-500 font-light mb-6 max-w-md mx-auto">
            Buat paket wisata pertama Anda. Tawarkan pengalaman tour yang menarik kepada customer.
        </p>
        <a href="{{ route('agency.tours.create') }}" class="btn-gomad-primary inline-flex items-center gap-2">
            <span>+</span> Buat Paket Tour
        </a>
    </div>
    @else
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($packages as $package)
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] overflow-hidden shadow-sm hover:border-[#C1121F] transition-colors group">
            {{-- Cover Image --}}
            <div class="h-48 bg-[#F5F5F5] flex items-center justify-center overflow-hidden border-b border-[#E5E5E5] relative">
                @if($package->cover_image)
                <img src="{{ $package->cover_image }}" alt="{{ $package->name }}" 
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                @else
                <div class="text-center text-gray-400">
                    <span class="text-6xl block mb-2">🏝️</span>
                    <span class="text-sm font-light">Belum ada foto</span>
                </div>
                @endif
                
                {{-- Status Badge --}}
                <span class="absolute top-3 right-3 px-2 py-1 rounded-full text-[10px] font-mono uppercase tracking-wider border
                    {{ $package->is_active ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200' }}">
                    {{ $package->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>
            
            <div class="p-5">
                <h3 class="font-bold text-lg text-[#111111] mb-1 group-hover:text-[#C1121F] transition">
                    {{ $package->name }}
                </h3>
                
                <div class="flex items-center gap-3 text-sm text-gray-500 font-light mb-3">
                    <span>📅 {{ $package->duration_days }}H{{ $package->duration_nights }}M</span>
                    <span>|</span>
                    <span>🛑 {{ $package->stops->count() }} stops</span>
                </div>
                
                <p class="text-sm text-gray-500 line-clamp-2 font-light mb-4">
                    {{ Str::limit($package->description, 120) }}
                </p>

                {{-- Schedule Info --}}
                @php
                    $upcomingSchedules = $package->schedules->where('departure_date', '>=', now()->toDateString())->where('is_active', true);
                    $totalBookings = $package->schedules->sum(function($s) { 
                        return $s->bookings ? $s->bookings->where('status', '!=', 'cancelled')->count() : 0; 
                    });
                @endphp
                
                <div class="flex items-center gap-4 text-xs text-gray-400 font-mono uppercase tracking-wider mb-4">
                    <span>📅 {{ $upcomingSchedules->count() }} jadwal</span>
                    <span>🎫 {{ $totalBookings }} booking</span>
                </div>

                <div class="flex gap-2 border-t border-[#E5E5E5] pt-4">
                    <a href="{{ route('agency.tours.show', $package) }}" 
                       class="flex-1 text-center border border-[#E5E5E5] text-[#111111] py-2 rounded-[12px] text-sm font-medium hover:bg-[#F5F5F5] transition">
                        Detail
                    </a>
                    <a href="{{ route('agency.tours.edit', $package) }}" 
                       class="flex-1 text-center bg-[#C1121F] text-white py-2 rounded-[12px] text-sm font-medium hover:bg-[#8A0F18] transition">
                        Edit
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection