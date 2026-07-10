@extends('layouts.customer')

@section('title', 'Booking Tour Saya')
@section('content')

<div class="container-magazine py-8">
    <h1 class="text-2xl font-bold text-[#111111] mb-6">🏝️ Booking Tour Saya</h1>

    @if($bookings->isEmpty())
    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-12 text-center shadow-sm">
        <div class="w-16 h-16 bg-[#F5F5F5] rounded-[12px] flex items-center justify-center mx-auto mb-4 border border-[#E5E5E5]">
            <span class="text-2xl">🏝️</span>
        </div>
        <p class="text-gray-500 text-lg font-light mb-4">Belum ada booking tour.</p>
        <a href="{{ route('customer.tour.index') }}" class="btn-gomad-primary inline-block">Jelajahi Paket Wisata</a>
    </div>
    @else
    <div class="space-y-4">
        @foreach($bookings as $booking)
        <a href="{{ route('customer.tour.detail', $booking) }}" 
           class="block bg-white border border-[#E5E5E5] rounded-[12px] p-5 shadow-sm hover:border-[#C1121F] transition-colors">
            <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <h3 class="font-bold text-[#111111] font-mono text-lg">{{ $booking->booking_code }}</h3>
                        <span class="px-2 py-1 rounded-full text-[10px] font-mono uppercase tracking-wider border
                            @if($booking->status == 'paid') bg-green-50 text-green-700 border-green-200
                            @elseif($booking->status == 'pending') bg-yellow-50 text-yellow-700 border-yellow-200
                            @elseif($booking->status == 'cancelled') bg-red-50 text-red-700 border-red-200
                            @else bg-blue-50 text-blue-700 border-blue-200 @endif">
                            {{ $booking->status_label }}
                        </span>
                    </div>
                    <p class="text-[#111111] font-medium">
                        🏝️ {{ $booking->tourSchedule->tourPackage->name ?? 'Paket Tour' }}
                    </p>
                    <p class="text-sm text-gray-500 mt-1 font-light">
                        👥 <strong>{{ $booking->group_name }}</strong> • {{ $booking->total_participants }} peserta
                    </p>
                    <p class="text-sm text-gray-500 mt-1 font-light">
                        📅 {{ $booking->tourSchedule->departure_date->format('d M Y') }} {{ $booking->tourSchedule->departure_time }}
                        @if($booking->tourSchedule->return_date)
                        → {{ $booking->tourSchedule->return_date->format('d M Y') }}
                        @endif
                    </p>
                    <p class="text-sm text-gray-500 mt-1 font-light">
                        🏢 {{ $booking->tourSchedule->tourPackage->agency->agency_name ?? '-' }}
                    </p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-xl font-bold text-[#C1121F] font-mono">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-400 font-light mt-1">
                        {{ $booking->total_adults }} Dewasa, {{ $booking->total_children }} Anak
                    </p>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    @endif
</div>
@endsection