@extends('layouts.public')

@section('title', 'Sewa Kendaraan')
@section('meta_description', 'Sewa kendaraan + supir untuk keperluan Anda. Harga per KM, fleksibel, tersedia di Madura dan sekitarnya.')
@section('og_image', asset('images/og-rental.jpg'))

@section('content')

@php
    $vehicles = \App\Models\Vehicle::with('agency')
        ->availableForRental()
        ->latest()
        ->limit(9)
        ->get();
    $agencies = \App\Models\Agency::where('is_verified', true)->orderBy('agency_name')->get();
    $months = [];
    for ($m = 1; $m <= 12; $m++) {
        $months[] = ['value' => $m, 'label' => \Carbon\Carbon::create()->month($m)->locale('id')->monthName];
    }
@endphp

<div class="section !pt-24 md:!pt-28">
    <div class="container-magazine">
        
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-[#111111] mb-2">Sewa Kendaraan</h1>
            <p class="text-gray-500 font-light text-lg">Sewa kendaraan + supir untuk keperluan Anda. Harga per KM, fleksibel.</p>
        </div>

        {{-- Search Filter --}}
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-8 shadow-sm">
            <form action="{{ route('customer.rental.index') }}" method="GET" class="grid md:grid-cols-5 gap-4 items-end">
                <div>
                    <label class="block text-xs font-mono uppercase tracking-wider text-gray-500 mb-1">Kapasitas</label>
                    <select name="passengers" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent font-medium text-[#111111]">
                        <option value="">Semua</option>
                        <option value="4">4+ Orang</option>
                        <option value="8">8+ Orang</option>
                        <option value="12">12+ Orang</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-mono uppercase tracking-wider text-gray-500 mb-1">Bulan</label>
                    <select name="month" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent font-medium text-[#111111]">
                        @foreach($months as $m)
                        <option value="{{ $m['value'] }}" {{ now()->month == $m['value'] ? 'selected' : '' }}>{{ $m['label'] }} {{ now()->year }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-mono uppercase tracking-wider text-gray-500 mb-1">Include Supir</label>
                    <select name="include_driver" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent font-medium text-[#111111]">
                        <option value="">Semua</option>
                        <option value="1">Ya, include supir</option>
                        <option value="0">Tidak include</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-mono uppercase tracking-wider text-gray-500 mb-1">Agency</label>
                    <select name="agency_id" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent font-medium text-[#111111]">
                        <option value="">Semua Agency</option>
                        @foreach($agencies as $agency)
                        <option value="{{ $agency->id }}">{{ $agency->agency_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-[#C1121F] text-white py-2.5 rounded-[12px] text-sm font-semibold hover:bg-[#8A0F18] transition">🔍 Cari</button>
                </div>
            </form>
        </div>

        {{-- Results --}}
        @if($vehicles->isEmpty())
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-16 text-center shadow-sm">
            <div class="w-20 h-20 bg-[#F5F5F5] rounded-[12px] flex items-center justify-center mx-auto mb-4 border border-[#E5E5E5]">
                <span class="text-4xl">🚐</span>
            </div>
            <h2 class="text-xl font-bold text-[#111111] mb-2">Belum Ada Kendaraan Tersedia</h2>
            <p class="text-gray-500 font-light">Belum ada kendaraan yang tersedia untuk disewa saat ini.</p>
            @guest
            <a href="{{ route('login') }}" class="btn-gomad-primary mt-4 inline-block">Login untuk Booking</a>
            @endguest
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
                        <div class="w-6 h-6 rounded-full bg-[#F5F5F5] flex items-center justify-center overflow-hidden">
                            @if($vehicle->agency->logo)
                            <img src="{{ $vehicle->agency->logo }}" class="w-full h-full object-cover">
                            @else 🏢 @endif
                        </div>
                        <span class="text-xs text-gray-500 font-light">{{ $vehicle->agency->agency_name }} ⭐ {{ number_format($vehicle->agency->rating, 1) }}</span>
                    </div>
                    <h3 class="font-bold font-mono text-lg text-[#111111] group-hover:text-[#C1121F] transition">{{ $vehicle->plate_number }}</h3>
                    <p class="text-sm text-gray-500 font-light">{{ $vehicle->brand }} {{ $vehicle->model }} • {{ $vehicle->capacity }} seat</p>
                    
                    <div class="grid grid-cols-2 gap-2 text-sm my-3">
                        <div class="bg-[#F5F5F5] rounded-[8px] p-2 text-center">
                            <span class="text-[10px] text-gray-400">Harga/KM</span>
                            <p class="font-bold text-[#C1121F]">Rp {{ number_format($vehicle->rental_price_per_km, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-[#F5F5F5] rounded-[8px] p-2 text-center">
                            <span class="text-[10px] text-gray-400">Min/Hari</span>
                            <p class="font-bold text-[#111111]">Rp {{ number_format($vehicle->rental_min_price, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="border-t border-[#E5E5E5] pt-3 flex justify-between items-center">
                        <span class="text-xs text-gray-400">{{ $vehicle->rental_include_driver ? '✅ Include Supir' : '❌ Tanpa Supir' }}</span>
                        @auth
                        <a href="{{ route('customer.rental.create', ['vehicle_id' => $vehicle->id]) }}" class="bg-[#C1121F] text-white px-4 py-2 rounded-[12px] text-sm font-semibold hover:bg-[#8A0F18] transition">Sewa</a>
                        @else
                        <a href="{{ route('login') }}" class="border border-[#C1121F] text-[#C1121F] px-4 py-2 rounded-[12px] text-sm font-semibold hover:bg-[#C1121F] hover:text-white transition">Login</a>
                        @endauth
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection