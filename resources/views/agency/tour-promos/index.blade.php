@extends('layouts.agency')

@section('title', 'Promo Wisata')
@section('content')

<div>
    <h1 class="text-2xl font-bold text-[#111111] mb-6">🎫 Promo Wisata Tersedia</h1>

    {{-- Promo Selektif --}}
    <div class="mb-8">
        <h2 class="font-bold text-lg text-[#111111] mb-4">Promo yang Bisa Dipasang</h2>
        
        @if($promos->isEmpty())
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-8 text-center shadow-sm text-gray-500 font-light">
            Belum ada promo selektif yang tersedia.
        </div>
        @else
        <div class="grid md:grid-cols-2 gap-4 mb-6">
            @foreach($promos as $promo)
            <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-5 border-l-4 border-[#C1121F] shadow-sm">
                <h3 class="font-bold text-lg text-[#111111]">{{ $promo->name }}</h3>
                <p class="text-sm text-gray-500 mb-2 font-light">{{ $promo->description }}</p>
                <div class="grid grid-cols-2 gap-2 text-sm mb-3">
                    <div class="bg-[#C1121F]/5 border border-[#C1121F] rounded-[12px] p-2 text-center">
                        <span class="text-[#C1121F] font-bold text-lg">{{ $promo->discount_percent }}%</span>
                        <span class="text-[10px] block font-mono uppercase tracking-wider">Diskon</span>
                    </div>
                    <div class="bg-[#C1121F]/5 border border-[#C1121F] rounded-[12px] p-2 text-center">
                        <span class="text-[#C1121F] font-bold">Rp {{ number_format($promo->max_discount, 0, ',', '.') }}</span>
                        <span class="text-[10px] block font-mono uppercase tracking-wider">Maks</span>
                    </div>
                </div>
                <p class="text-xs text-gray-500 font-mono tracking-wider">
                    📅 {{ $promo->start_date->format('d M') }} - {{ $promo->end_date->format('d M Y') }}
                </p>
                <p class="text-xs text-gray-500 font-mono tracking-wider">
                    💰 {{ $promo->cost_bearer_label }}
                </p>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Pasang Promo ke Jadwal --}}
    <div>
        <h2 class="font-bold text-lg text-[#111111] mb-4">Pasang Promo ke Jadwal Tour</h2>
        
        @if($schedules->isEmpty())
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-8 text-center shadow-sm text-gray-500 font-light">
            Belum ada jadwal tour.
        </div>
        @else
        <div class="space-y-3">
            @foreach($schedules as $schedule)
            <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-4 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 shadow-sm">
                <div>
                    <span class="font-semibold text-[#111111]">{{ $schedule->tourPackage->name ?? '-' }}</span>
                    <span class="text-sm text-gray-500 ml-2 font-light">
                        {{ $schedule->departure_date->format('d M Y') }} {{ $schedule->departure_time }}
                    </span>
                    @if($schedule->tourPromos->isNotEmpty())
                    <span class="text-[10px] font-mono uppercase tracking-wider bg-green-50 text-green-700 px-2 py-1 rounded-full border border-green-200 ml-2">
                        {{ $schedule->tourPromos->first()->name }}
                    </span>
                    @endif
                </div>
                
                @if($promos->isNotEmpty())
                <form action="{{ route('agency.tour-promos.attach') }}" method="POST" class="flex gap-2">
                    @csrf
                    <input type="hidden" name="tour_schedule_id" value="{{ $schedule->id }}">
                    <select name="tour_promo_id" class="text-xs border border-[#E5E5E5] rounded-[12px] px-2 py-1.5 bg-[#F5F5F5] text-[#111111]">
                        @foreach($promos as $promo)
                        <option value="{{ $promo->id }}">{{ $promo->name }} ({{ $promo->discount_percent }}%)</option>
                        @endforeach
                    </select>
                    <button type="submit" class="bg-[#C1121F] text-white px-3 py-1.5 rounded-[12px] text-[10px] font-medium hover:bg-[#8A0F18] transition">
                        Pasang
                    </button>
                </form>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection