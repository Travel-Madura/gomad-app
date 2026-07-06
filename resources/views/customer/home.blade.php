@extends('layouts.customer')

@section('title', 'Home')
@section('content')
@php
    $cities = \App\Models\RouteStop::select('city_name')->distinct()->orderBy('city_name')->get();
    $popularRoutes = \App\Models\Route::where('is_active', true)->withCount(['schedules' => fn($q) => $q->where('departure_date', '>=', now()->toDateString())->where('is_active', true)])->orderByDesc('schedules_count')->limit(4)->get();
@endphp

<div class="container-magazine py-8">
    {{-- Hero Search --}}
    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 md:p-8 mb-8 shadow-sm">
        <h1 class="text-2xl md:text-3xl font-bold text-[#111111] mb-2">Mau kemana hari ini?</h1>
        <p class="text-gray-500 font-light mb-6">Cari jadwal travel dan booking langsung</p>
        
        <form action="{{ route('customer.search') }}" method="GET" class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Asal</label>
                <select name="origin" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]">
                    <option value="">Semua</option>
                    @foreach($cities as $city)<option value="{{ $city->city_name }}">{{ $city->city_name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Tujuan</label>
                <select name="destination" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]">
                    <option value="">Semua</option>
                    @foreach($cities as $city)<option value="{{ $city->city_name }}">{{ $city->city_name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Tanggal</label>
                <input type="date" name="date" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]">
            </div>
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Kelas</label>
                <select name="travel_class" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]">
                    <option value="">Semua</option>
                    <option value="economy">Ekonomi</option>
                    <option value="premium">Premium</option>
                    <option value="charter">Charter</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full btn-gomad-primary py-2.5 rounded-[12px] font-semibold text-sm">Cari</button>
            </div>
        </form>
    </div>

    {{-- Quick Links --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <a href="{{ route('customer.bookings') }}" class="bg-white border border-[#E5E5E5] rounded-[12px] p-4 text-center hover:border-[#C1121F] transition-colors group">
            <div class="w-10 h-10 bg-[#F5F5F5] rounded-[12px] flex items-center justify-center text-lg mx-auto mb-2 group-hover:bg-[#C1121F]/10 transition-colors border border-[#E5E5E5]">🎫</div>
            <p class="font-semibold text-[#111111] text-sm">Booking Saya</p>
        </a>
        <a href="{{ route('customer.search') }}" class="bg-white border border-[#E5E5E5] rounded-[12px] p-4 text-center hover:border-[#C1121F] transition-colors group">
            <div class="w-10 h-10 bg-[#F5F5F5] rounded-[12px] flex items-center justify-center text-lg mx-auto mb-2 group-hover:bg-[#C1121F]/10 transition-colors border border-[#E5E5E5]">🔍</div>
            <p class="font-semibold text-[#111111] text-sm">Cari Jadwal</p>
        </a>
        <a href="{{ route('listing') }}" class="bg-white border border-[#E5E5E5] rounded-[12px] p-4 text-center hover:border-[#C1121F] transition-colors group">
            <div class="w-10 h-10 bg-[#F5F5F5] rounded-[12px] flex items-center justify-center text-lg mx-auto mb-2 group-hover:bg-[#C1121F]/10 transition-colors border border-[#E5E5E5]">🏢</div>
            <p class="font-semibold text-[#111111] text-sm">Agency</p>
        </a>
        <a href="{{ route('customer.profile') }}" class="bg-white border border-[#E5E5E5] rounded-[12px] p-4 text-center hover:border-[#C1121F] transition-colors group">
            <div class="w-10 h-10 bg-[#F5F5F5] rounded-[12px] flex items-center justify-center text-lg mx-auto mb-2 group-hover:bg-[#C1121F]/10 transition-colors border border-[#E5E5E5]">👤</div>
            <p class="font-semibold text-[#111111] text-sm">Profil</p>
        </a>
    </div>

    {{-- Popular Routes --}}
    @if($popularRoutes->isNotEmpty())
    <h2 class="text-xl font-bold text-[#111111] mb-4">Rute Populer</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach($popularRoutes as $route)
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] overflow-hidden group hover:border-[#C1121F] transition-colors">
            @if($route->photo)
            <div class="h-32 overflow-hidden">
                <img src="{{ $route->photo }}" alt="" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
            </div>
            @else
            <div class="h-32 bg-[#F5F5F5] flex items-center justify-center">
                <span class="text-3xl">🗺️</span>
            </div>
            @endif
            <div class="p-3">
                <p class="font-semibold text-sm text-[#111111]">{{ $route->route_name }}</p>
                <p class="text-xs text-gray-400 font-mono uppercase tracking-wider">{{ $route->schedules_count }} jadwal</p>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection