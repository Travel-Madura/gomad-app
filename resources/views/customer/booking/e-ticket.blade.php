@extends('layouts.customer')

@section('title', 'E-Ticket')
@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">
    <a href="{{ route('customer.booking.show', $booking) }}" class="text-[#C1121F] text-sm font-medium mb-4 inline-block hover:underline">← Kembali</a>

    <div class="bg-white border-2 border-[#C1121F] rounded-[12px] p-6 shadow-sm">
        <!-- Header -->
        <div class="text-center border-b-2 border-dashed border-[#E5E5E5] pb-4 mb-4">
            <div class="flex items-center justify-center gap-1 mb-2">
                <span class="text-2xl font-bold tracking-tighter text-[#111111]">GO</span>
                <span class="text-[#C1121F] text-2xl font-bold tracking-tighter">MAD</span>
            </div>
            <p class="text-sm text-gray-400 font-mono tracking-wider">E-Ticket - {{ $booking->booking_code }}</p>
        </div>

        <!-- Info Utama -->
        <div class="space-y-3 text-sm">
            <div class="flex justify-between border-b border-[#F5F5F5] pb-1">
                <span class="text-gray-400 font-mono uppercase tracking-wider text-xs">Kode Booking</span>
                <span class="font-bold text-[#111111] font-mono">{{ $booking->booking_code }}</span>
            </div>
            <div class="flex justify-between border-b border-[#F5F5F5] pb-1">
                <span class="text-gray-400 font-mono uppercase tracking-wider text-xs">Rute</span>
                <span class="font-semibold text-[#111111]">{{ $booking->originStop->city_name }} → {{ $booking->destinationStop->city_name }}</span>
            </div>
            <div class="flex justify-between border-b border-[#F5F5F5] pb-1">
                <span class="text-gray-400 font-mono uppercase tracking-wider text-xs">Tanggal</span>
                <span class="font-semibold text-[#111111]">{{ $booking->schedule->departure_date->format('d M Y') }}</span>
            </div>
            <div class="flex justify-between border-b border-[#F5F5F5] pb-1">
                <span class="text-gray-400 font-mono uppercase tracking-wider text-xs">Jam</span>
                <span class="font-semibold text-[#111111]">{{ $booking->schedule->departure_time }}</span>
            </div>
            <div class="flex justify-between border-b border-[#F5F5F5] pb-1">
                <span class="text-gray-400 font-mono uppercase tracking-wider text-xs">Agency</span>
                <span class="font-semibold text-[#111111]">{{ $booking->schedule->agency->agency_name }}</span>
            </div>
            <div class="flex justify-between border-b border-[#F5F5F5] pb-1">
                <span class="text-gray-400 font-mono uppercase tracking-wider text-xs">Kendaraan</span>
                <span class="font-semibold text-[#111111] font-mono">{{ $booking->schedule->vehicle->plate_number }}</span>
            </div>
            <div class="flex justify-between pt-2">
                <span class="text-gray-400 font-mono uppercase tracking-wider text-xs">Total</span>
                <span class="font-bold text-[#C1121F] font-mono text-base">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Penumpang -->
        <div class="border-t border-dashed border-[#E5E5E5] pt-4 mt-4">
            <h4 class="font-mono uppercase tracking-wider text-xs font-bold mb-2">Penumpang</h4>
            @foreach($booking->passengers as $p)
            <div class="flex justify-between text-sm py-1 border-b border-[#F5F5F5] last:border-0">
                <span class="text-[#111111]">{{ $p->passenger_name }}</span>
                <span class="text-gray-400 font-mono text-xs">Seat {{ $p->seat_number }}</span>
            </div>
            @endforeach
        </div>

        <!-- Alamat -->
        <div class="border-t border-dashed border-[#E5E5E5] pt-4 mt-4">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-400 font-mono uppercase tracking-wider text-[10px]">📍 Jemput</span>
                    <p class="font-medium text-[#111111] mt-1">{{ $booking->pickup_address }}</p>
                </div>
                <div>
                    <span class="text-gray-400 font-mono uppercase tracking-wider text-[10px]">🎯 Tujuan</span>
                    <p class="font-medium text-[#111111] mt-1">{{ $booking->destination_address }}</p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="border-t-2 border-dashed border-[#E5E5E5] pt-4 mt-4 text-center">
            <p class="text-[10px] font-mono uppercase tracking-widest text-gray-400">E-Ticket resmi GoMad</p>
            <p class="text-[10px] font-mono text-gray-400">Dicetak: {{ now()->format('d M Y H:i') }}</p>
        </div>
    </div>

    <div class="text-center mt-6">
        <button onclick="window.print()" class="btn-gomad-primary px-8 py-3 rounded-[12px]">
            🖨️ CETAK E-TICKET
        </button>
    </div>
</div>
@endsection