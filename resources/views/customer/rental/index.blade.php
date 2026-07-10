@extends('layouts.customer')

@section('title', 'Sewa Kendaraan')
@section('content')

@php
    $month = $filters['month'] ?? now()->month;
    $year = $filters['year'] ?? now()->year;
    $months = [];
    for ($m = 1; $m <= 12; $m++) {
        $months[] = ['value' => $m, 'label' => \Carbon\Carbon::create()->month($m)->locale('id')->monthName];
    }
@endphp

<div class="container-magazine py-8">
    <h1 class="text-2xl font-bold text-[#111111] mb-2">🚐 Sewa Kendaraan</h1>
    <p class="text-gray-500 font-light mb-6">Sewa kendaraan + supir untuk keperluan Anda</p>

    {{-- FILTER --}}
    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-8 shadow-sm">
        <form action="{{ route('customer.rental.index') }}" method="GET" class="grid md:grid-cols-5 gap-4 items-end">
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Kapasitas</label>
                <select name="passengers" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]">
                    <option value="">Semua</option>
                    <option value="4" {{ request('passengers') == 4 ? 'selected' : '' }}>4+ Orang</option>
                    <option value="8" {{ request('passengers') == 8 ? 'selected' : '' }}>8+ Orang</option>
                    <option value="12" {{ request('passengers') == 12 ? 'selected' : '' }}>12+ Orang</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Bulan</label>
                <select name="month" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]">
                    @foreach($months as $m)
                    <option value="{{ $m['value'] }}" {{ ($month == $m['value']) ? 'selected' : '' }}>{{ $m['label'] }} {{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Include Supir</label>
                <select name="include_driver" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]">
                    <option value="">Semua</option>
                    <option value="1" {{ request('include_driver') == '1' ? 'selected' : '' }}>Ya</option>
                    <option value="0" {{ request('include_driver') == '0' ? 'selected' : '' }}>Tidak</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Agency</label>
                <select name="agency_id" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]">
                    <option value="">Semua</option>
                    @foreach($agencies as $a)
                    <option value="{{ $a->id }}" {{ request('agency_id') == $a->id ? 'selected' : '' }}>{{ $a->agency_name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-gomad-primary py-2.5 rounded-[12px] font-semibold">🔍 Cari</button>
        </form>
    </div>

    {{-- RESULTS --}}
    @if($vehicles->isEmpty())
    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-12 text-center shadow-sm text-gray-500 font-light">
        Tidak ada kendaraan tersedia untuk filter ini.
    </div>
    @else
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($vehicles as $vehicle)
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] overflow-hidden shadow-sm hover:border-[#C1121F] transition group">
            <div class="h-40 bg-[#F5F5F5] flex items-center justify-center border-b border-[#E5E5E5]">
                @if($vehicle->vehicle_image)
                <img src="{{ $vehicle->vehicle_image }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                @else
                <span class="text-6xl">🚐</span>
                @endif
            </div>
            <div class="p-5">
                <div class="flex items-center gap-2 mb-2">
                    <span class="font-bold font-mono text-lg text-[#111111]">{{ $vehicle->plate_number }}</span>
                    <span class="text-xs text-gray-500">{{ $vehicle->brand }} {{ $vehicle->model }}</span>
                </div>
                <p class="text-sm text-gray-500 mb-3">{{ $vehicle->agency->agency_name }} ⭐ {{ number_format($vehicle->agency->rating, 1) }}</p>
                
                <div class="grid grid-cols-2 gap-2 text-sm mb-4">
                    <div class="bg-[#F5F5F5] rounded-[8px] p-2 text-center">
                        <span class="text-[10px] text-gray-400">Kapasitas</span>
                        <p class="font-bold text-[#111111]">{{ $vehicle->capacity }} seat</p>
                    </div>
                    <div class="bg-[#F5F5F5] rounded-[8px] p-2 text-center">
                        <span class="text-[10px] text-gray-400">Harga/KM</span>
                        <p class="font-bold text-[#C1121F]">Rp {{ number_format($vehicle->rental_price_per_km, 0, ',', '.') }}</p>
                    </div>
                </div>

                <div class="border-t border-[#E5E5E5] pt-3 flex justify-between items-center">
                    <div>
                        <p class="text-xs text-gray-400">Min charge</p>
                        <p class="font-bold text-[#111111]">Rp {{ number_format($vehicle->rental_min_price, 0, ',', '.') }}</p>
                    </div>
                    <a href="{{ route('customer.rental.create', ['vehicle_id' => $vehicle->id, 'month' => $month, 'year' => $year]) }}" 
                       class="bg-[#C1121F] text-white px-4 py-2 rounded-[12px] text-sm font-semibold hover:bg-[#8A0F18] transition">
                        Sewa
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection