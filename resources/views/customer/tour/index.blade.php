@extends('layouts.customer')

@section('title', 'Paket Wisata')
@section('content')

<div class="container-magazine py-8">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl font-bold text-[#111111] mb-2">🏝️ Paket Wisata</h1>
        <p class="text-gray-500 font-light">Jelajahi paket tour seru dari agency terpercaya</p>
    </div>

    {{-- Filter --}}
    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-4 mb-8 shadow-sm">
        <form action="{{ route('customer.tour.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama paket atau destinasi..." 
                       class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition">
            </div>
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Durasi</label>
                <select name="duration" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition min-w-[120px]">
                    <option value="">Semua</option>
                    <option value="1" {{ request('duration') == 1 ? 'selected' : '' }}>1 Hari</option>
                    <option value="2" {{ request('duration') == 2 ? 'selected' : '' }}>2 Hari</option>
                    <option value="3" {{ request('duration') == 3 ? 'selected' : '' }}>3 Hari</option>
                    <option value="5" {{ request('duration') == 5 ? 'selected' : '' }}>5+ Hari</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Budget Maks</label>
                <select name="max_budget" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition min-w-[140px]">
                    <option value="">Semua</option>
                    <option value="500000" {{ request('max_budget') == 500000 ? 'selected' : '' }}>Rp 500rb</option>
                    <option value="1000000" {{ request('max_budget') == 1000000 ? 'selected' : '' }}>Rp 1jt</option>
                    <option value="2000000" {{ request('max_budget') == 2000000 ? 'selected' : '' }}>Rp 2jt</option>
                    <option value="5000000" {{ request('max_budget') == 5000000 ? 'selected' : '' }}>Rp 5jt</option>
                </select>
            </div>
            <button type="submit" class="btn-gomad-primary text-sm py-2.5 px-6 rounded-[12px]">Filter</button>
        </form>
    </div>

    {{-- Package Cards --}}
    @if($packages->isEmpty())
    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-16 text-center shadow-sm">
        <div class="w-20 h-20 bg-[#F5F5F5] rounded-[12px] flex items-center justify-center mx-auto mb-4 border border-[#E5E5E5]">
            <span class="text-4xl">🏝️</span>
        </div>
        <h2 class="text-xl font-bold text-[#111111] mb-2">Belum Ada Paket Tour</h2>
        <p class="text-gray-500 font-light">Belum ada paket wisata yang tersedia saat ini. Silakan cek lagi nanti.</p>
    </div>
    @else
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($packages as $package)
        @php
            $minPrice = $package->schedules->where('is_active', true)->where('departure_date', '>=', now()->toDateString())->min('base_price');
            $upcomingCount = $package->schedules->where('is_active', true)->where('departure_date', '>=', now()->toDateString())->count();
        @endphp
        <a href="{{ route('customer.tour.show', $package) }}" class="block bg-white border border-[#E5E5E5] rounded-[12px] overflow-hidden shadow-sm hover:border-[#C1121F] transition-colors group">
            {{-- Cover --}}
            <div class="h-48 bg-[#F5F5F5] flex items-center justify-center overflow-hidden">
                @if($package->cover_image)
                <img src="{{ $package->cover_image }}" alt="{{ $package->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                @else
                <span class="text-6xl">🏝️</span>
                @endif
            </div>
            
            <div class="p-5">
                {{-- Agency --}}
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-6 h-6 rounded-full bg-[#F5F5F5] flex items-center justify-center overflow-hidden border border-[#E5E5E5]">
                        @if($package->agency->logo)
                        <img src="{{ $package->agency->logo }}" class="w-full h-full object-cover">
                        @else 🏢 @endif
                    </div>
                    <span class="text-xs text-gray-500 font-light">{{ $package->agency->agency_name }}</span>
                    <span class="text-xs text-yellow-500">⭐ {{ number_format($package->agency->rating, 1) }}</span>
                </div>

                <h3 class="font-bold text-lg text-[#111111] mb-1 group-hover:text-[#C1121F] transition">{{ $package->name }}</h3>
                <p class="text-sm text-gray-500 line-clamp-2 font-light mb-3">{{ Str::limit($package->description, 100) }}</p>
                
                <div class="flex items-center gap-4 text-xs text-gray-400 font-mono uppercase tracking-wider mb-4">
                    <span>📅 {{ $package->duration_days }}H{{ $package->duration_nights }}M</span>
                    <span>🛑 {{ $package->stops->count() }} stops</span>
                    @if($upcomingCount > 0)
                    <span>📅 {{ $upcomingCount }} jadwal</span>
                    @endif
                </div>

                <div class="flex justify-between items-center border-t border-[#E5E5E5] pt-3">
                    <div>
                        @if($minPrice)
                        <p class="text-xs text-gray-400 font-light">Mulai dari</p>
                        <p class="font-bold text-[#C1121F] font-mono">Rp {{ number_format($minPrice, 0, ',', '.') }}</p>
                        @else
                        <p class="text-sm text-gray-400 font-light">Belum ada jadwal</p>
                        @endif
                    </div>
                    <span class="text-[#C1121F] group-hover:translate-x-1 transition-transform text-lg">→</span>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    @endif
</div>
@endsection