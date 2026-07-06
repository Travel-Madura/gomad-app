@extends('layouts.agency')

@section('title', 'Transfer Penumpang')
@section('content')
@php
    $availableSchedules = $availableSchedules ?? collect();
    $selectedBookings = session('selectedBookings') ?? $selectedBookings ?? [];
    $selectedBookingModels = !empty($selectedBookings) 
        ? \App\Models\Booking::whereIn('id', $selectedBookings)->with(['originStop', 'destinationStop', 'passengers', 'customer'])->get()
        : collect();
@endphp

<div class="max-w-5xl mx-auto">
    <a href="{{ route('agency.transfers.index') }}" class="text-[#C1121F] text-sm mb-4 inline-block hover:underline">← Kembali ke Transfer</a>
    
    <h1 class="text-2xl font-bold text-[#111111] mb-2">Transfer Penumpang</h1>
    <p class="text-gray-500 font-light mb-6">Pindahkan penumpang dari jadwal yang sepi ke jadwal lain</p>

    <!-- Jadwal Asal -->
    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-6 shadow-sm">
        <h2 class="font-bold text-lg text-[#111111] mb-3">📤 Jadwal Asal</h2>
        <div class="bg-red-50 border border-red-200 rounded-[12px] p-4">
            <div class="flex justify-between">
                <div>
                    <p class="font-bold text-lg text-[#111111]">{{ $schedule->route->route_name }}</p>
                    <p class="text-sm text-gray-600 font-light">{{ $schedule->departure_date->format('d M Y') }} | {{ $schedule->departure_time }}</p>
                    <p class="text-sm text-gray-600 font-light">{{ $schedule->vehicle->plate_number ?? '-' }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500 font-mono uppercase tracking-wider">Terisi</p>
                    <p class="text-2xl font-bold {{ $schedule->occupancy_rate < 50 ? 'text-red-600' : 'text-green-600' }}">
                        {{ $schedule->occupancy_rate }}%
                    </p>
                    <p class="text-xs text-gray-500 font-mono">{{ $schedule->bookings->whereNotIn('status', ['cancelled'])->sum('total_passengers') }}/{{ $schedule->max_capacity }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Pilih Booking -->
    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-6 shadow-sm">
        <h2 class="font-bold text-lg text-[#111111] mb-3">👥 Pilih Penumpang yang Akan Ditransfer</h2>
        
        <form action="{{ route('agency.schedules.transfer.search', $schedule) }}" method="POST">
            @csrf
            <div class="space-y-3 mb-4">
                @foreach($bookings as $booking)
                <label class="flex items-center p-3 border border-[#E5E5E5] rounded-[12px] cursor-pointer hover:bg-[#F5F5F5] transition">
                    <input type="checkbox" name="booking_ids[]" value="{{ $booking->id }}" 
                           class="w-5 h-5 text-[#C1121F] rounded border-[#E5E5E5] focus:ring-[#C1121F] mr-3"
                           {{ in_array($booking->id, old('booking_ids', $selectedBookings)) ? 'checked' : '' }}>
                    <div class="flex-1">
                        <div class="flex justify-between">
                            <span class="font-semibold text-[#111111] font-mono">{{ $booking->booking_code }}</span>
                            <span class="text-sm text-gray-500 font-light">{{ $booking->total_passengers }} pax</span>
                        </div>
                        <p class="text-sm text-gray-600 font-light">{{ $booking->originStop->city_name }} → {{ $booking->destinationStop->city_name }}</p>
                        <p class="text-xs text-gray-500 font-light">{{ $booking->customer->name }} • Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
                    </div>
                </label>
                @endforeach
            </div>
            
            <button type="submit" class="btn-gomad-primary px-6 py-2 rounded-[12px] font-semibold">
                🔍 CARI MOBIL TUJUAN
            </button>
        </form>
    </div>

    <!-- Hasil Pencarian Mobil Tujuan -->
    @if($availableSchedules->isNotEmpty())
    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-6 shadow-sm">
        <h2 class="font-bold text-lg text-[#111111] mb-3">🚐 Mobil Tersedia untuk Transfer</h2>
        <p class="text-sm text-gray-500 font-light mb-4">Ditemukan {{ $availableSchedules->count() }} mobil yang bisa menerima transfer</p>
        
        <div class="space-y-4">
            @foreach($availableSchedules as $targetSchedule)
            <div class="border border-[#E5E5E5] rounded-[12px] p-4 hover:border-[#C1121F] transition-colors">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="font-bold text-[#111111]">{{ $targetSchedule->agency->agency_name }}</h3>
                        <p class="text-sm text-gray-600 font-light">{{ $targetSchedule->route->route_name }}</p>
                        <p class="text-sm text-gray-500 font-light">
                            🕐 {{ $targetSchedule->departure_time }} | 
                            🚐 {{ $targetSchedule->vehicle->plate_number ?? '-' }} |
                            💺 {{ $targetSchedule->available_seats }} kursi kosong
                        </p>
                        <p class="text-sm mt-1 font-light">
                            💰 Biaya transfer: <strong class="text-[#C1121F]">Rp {{ number_format($targetSchedule->transfer_fee_per_passenger, 0, ',', '.') }}/penumpang</strong>
                        </p>
                    </div>
                    <div class="text-right">
                        <span class="text-gray-500 font-mono text-sm">⭐ {{ number_format($targetSchedule->agency->rating, 1) }}</span>
                        
                        <form action="{{ route('agency.schedules.transfer.request') }}" method="POST" class="mt-2">
                            @csrf
                            <input type="hidden" name="from_schedule_id" value="{{ $schedule->id }}">
                            <input type="hidden" name="to_schedule_id" value="{{ $targetSchedule->id }}">
                            @foreach($selectedBookings as $bid)
                            <input type="hidden" name="booking_ids[]" value="{{ $bid }}">
                            @endforeach
                            <button type="submit" class="bg-[#C1121F] text-white px-4 py-2 rounded-[12px] text-sm hover:bg-[#8A0F18]">
                                ✅ PILIH MOBIL INI
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @elseif(request()->isMethod('post') || session('availableSchedules'))
    <div class="bg-yellow-50 border border-yellow-200 rounded-[12px] p-6 text-center">
        <p class="text-yellow-800 font-medium">Tidak ada mobil tersedia untuk transfer.</p>
        <p class="text-sm text-yellow-600 font-light">Pastikan ada jadwal lain yang searah, tanggal sama, dan masih ada kursi kosong.</p>
    </div>
    @endif
</div>
@endsection