@extends('layouts.customer')

@section('title', 'Booking Saya')
@section('content')

@php
    $type = request('type', 'travel');
    
    // Counts
    $totalTravel = \App\Models\Booking::where('customer_id', auth()->id())->count();
    $totalTour = \App\Models\TourBooking::where('customer_id', auth()->id())->count();
    $totalRental = \App\Models\RentalBooking::where('customer_id', auth()->id())->count();
    
    // Data berdasarkan tipe
    if ($type === 'tour') {
        $tourService = app(\App\Services\TourService::class);
        $bookings = $tourService->getCustomerBookings(auth()->id());
    } elseif ($type === 'rental') {
        $rentalService = app(\App\Services\RentalService::class);
        $bookings = $rentalService->getCustomerBookings(auth()->id());
    } else {
        $bookingService = app(\App\Services\BookingService::class);
        $bookings = $bookingService->getCustomerBookings(auth()->user());
    }
@endphp

<div class="container-magazine py-8">
    <h1 class="text-2xl font-bold text-[#111111] mb-6">Booking Saya</h1>

    {{-- TABS --}}
    <div class="flex border-b border-[#E5E5E5] mb-6 overflow-x-auto">
        <a href="{{ route('customer.bookings', ['type' => 'travel']) }}" 
           class="px-4 py-3 text-sm font-semibold border-b-2 whitespace-nowrap transition
                  {{ $type === 'travel' ? 'border-[#C1121F] text-[#C1121F]' : 'border-transparent text-gray-500 hover:text-[#111111]' }}">
            🚌 Travel ({{ $totalTravel }})
        </a>
        <a href="{{ route('customer.bookings', ['type' => 'tour']) }}" 
           class="px-4 py-3 text-sm font-semibold border-b-2 whitespace-nowrap transition
                  {{ $type === 'tour' ? 'border-[#C1121F] text-[#C1121F]' : 'border-transparent text-gray-500 hover:text-[#111111]' }}">
            🏝️ Tour ({{ $totalTour }})
        </a>
        <a href="{{ route('customer.bookings', ['type' => 'rental']) }}" 
           class="px-4 py-3 text-sm font-semibold border-b-2 whitespace-nowrap transition
                  {{ $type === 'rental' ? 'border-[#C1121F] text-[#C1121F]' : 'border-transparent text-gray-500 hover:text-[#111111]' }}">
            🚐 Rental ({{ $totalRental }})
        </a>
    </div>

    @if($bookings->isEmpty())
    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-12 text-center shadow-sm">
        <div class="w-16 h-16 bg-[#F5F5F5] rounded-[12px] flex items-center justify-center mx-auto mb-4 border border-[#E5E5E5]">
            <span class="text-2xl">
                @if($type === 'travel') 🎫
                @elseif($type === 'tour') 🏝️
                @else 🚐
                @endif
            </span>
        </div>
        <p class="text-gray-500 text-lg font-light mb-4">Belum ada booking {{ $type === 'travel' ? 'travel' : ($type === 'tour' ? 'tour' : 'rental') }}.</p>
        @if($type === 'tour')
        <a href="{{ route('customer.tour.index') }}" class="btn-gomad-primary inline-block">Jelajahi Paket Wisata</a>
        @elseif($type === 'rental')
        <a href="{{ route('customer.rental.index') }}" class="btn-gomad-primary inline-block">Cari Kendaraan Sewa</a>
        @else
        <a href="{{ route('customer.search') }}" class="btn-gomad-primary inline-block">Cari Jadwal Travel</a>
        @endif
    </div>
    @else
        {{-- RENTAL BOOKINGS --}}
        @if($type === 'rental')
        <div class="space-y-4">
            @foreach($bookings as $booking)
            <a href="{{ route('customer.rental.detail', $booking) }}" 
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
                        <p class="text-[#111111] font-medium">🚐 {{ $booking->vehicle->plate_number }} ({{ $booking->vehicle->brand }} {{ $booking->vehicle->model }})</p>
                        <p class="text-sm text-gray-500 mt-1 font-light">{{ $booking->purpose }}</p>
                        <p class="text-sm text-gray-500 mt-1 font-light">
                            📅 {{ $booking->start_date->format('d M') }} → {{ $booking->end_date->format('d M Y') }} ({{ $booking->total_days }} hari)
                        </p>
                        <p class="text-sm text-gray-500 mt-1 font-light">🏢 {{ $booking->vehicle->agency->agency_name ?? '-' }}</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-xl font-bold text-[#C1121F] font-mono">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        {{-- TOUR BOOKINGS --}}
        @elseif($type === 'tour')
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
                        <p class="text-[#111111] font-medium">🏝️ {{ $booking->tourSchedule->tourPackage->name ?? 'Paket Tour' }}</p>
                        <p class="text-sm text-gray-500 mt-1 font-light">
                            👥 <strong>{{ $booking->group_name }}</strong> • {{ $booking->total_participants }} peserta
                        </p>
                        <p class="text-sm text-gray-500 mt-1 font-light">
                            📅 {{ $booking->tourSchedule->departure_date->format('d M Y') }} {{ $booking->tourSchedule->departure_time }}
                        </p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-xl font-bold text-[#C1121F] font-mono">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        {{-- TRAVEL BOOKINGS --}}
        @else
        <div class="space-y-4">
            @foreach($bookings as $booking)
            <a href="{{ route('customer.booking.show', $booking) }}" 
               class="block bg-white border border-[#E5E5E5] rounded-[12px] p-5 shadow-sm hover:border-[#C1121F] transition-colors">
                <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h3 class="font-bold text-[#111111] font-mono text-lg">{{ $booking->booking_code }}</h3>
                            @if($booking->status == 'paid' || $booking->status == 'on_going')
                            <span class="text-[10px] font-mono uppercase tracking-wider text-green-600 bg-green-50 px-2 py-1 rounded-full border border-green-200">Sudah Dibayar</span>
                            @elseif($booking->status == 'pending')
                            <span class="text-[10px] font-mono uppercase tracking-wider text-yellow-600 bg-yellow-50 px-2 py-1 rounded-full border border-yellow-200">Menunggu</span>
                            @endif
                        </div>
                        @if($booking->originStop && $booking->destinationStop)
                        <p class="text-[#111111] font-medium">{{ $booking->originStop->city_name }} → {{ $booking->destinationStop->city_name }}</p>
                        @endif
                        @if($booking->schedule)
                        <p class="text-sm text-gray-500 mt-1 font-light">
                            📅 {{ $booking->schedule->departure_date->format('d M Y') }} | 🕐 {{ $booking->schedule->departure_time }} | 🏢 {{ $booking->schedule->agency->agency_name ?? '-' }}
                        </p>
                        @endif
                        <p class="text-sm text-gray-500 mt-1 font-light">👥 {{ $booking->total_passengers }} penumpang</p>
                        @php $promoUsage = \App\Models\PromoUsage::where('booking_id', $booking->id)->first(); @endphp
                        @if($promoUsage && $promoUsage->discount_amount > 0)
                        <p class="text-xs text-[#C1121F] font-mono uppercase tracking-wider mt-1">Diskon Rp {{ number_format($promoUsage->discount_amount, 0, ',', '.') }}</p>
                        @endif
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-xl font-bold text-[#C1121F] font-mono">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        @endif
    @endif
</div>
@endsection