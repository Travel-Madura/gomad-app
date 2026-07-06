@extends('layouts.agency')

@section('title', 'Detail Booking')
@section('content')
<div class="max-w-4xl mx-auto">
    <a href="{{ route('agency.bookings.index') }}" class="text-[#C1121F] text-sm mb-4 inline-block hover:underline">← Kembali</a>

    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h1 class="text-2xl font-bold font-mono text-[#111111]">{{ $booking->booking_code }}</h1>
                <span class="px-2 py-1 rounded-full text-[10px] font-mono uppercase tracking-wider border
                    @if($booking->status == 'paid') bg-green-50 text-green-700 border-green-200
                    @elseif($booking->status == 'pending') bg-yellow-50 text-yellow-700 border-yellow-200
                    @else bg-[#F5F5F5] text-gray-600 border-[#E5E5E5] @endif">
                    {{ $booking->status_label }}
                </span>
            </div>
            <p class="text-2xl font-bold text-[#C1121F] font-mono">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
        </div>

        <div class="grid md:grid-cols-2 gap-4 text-sm font-light">
            <div class="bg-[#F5F5F5] rounded-[12px] p-3 border border-[#E5E5E5]"><span class="font-mono uppercase tracking-wider text-[10px] text-gray-400">Customer</span><p class="font-medium text-[#111111]">{{ $booking->customer->name ?? '-' }} ({{ $booking->customer->phone ?? '-' }})</p></div>
            <div class="bg-[#F5F5F5] rounded-[12px] p-3 border border-[#E5E5E5]"><span class="font-mono uppercase tracking-wider text-[10px] text-gray-400">Rute</span><p class="font-medium text-[#111111]">{{ $booking->originStop->city_name }} → {{ $booking->destinationStop->city_name }}</p></div>
            <div class="bg-[#F5F5F5] rounded-[12px] p-3 border border-[#E5E5E5]"><span class="font-mono uppercase tracking-wider text-[10px] text-gray-400">Tanggal</span><p class="font-medium text-[#111111]">{{ $booking->schedule->departure_date->format('d M Y') }} {{ $booking->schedule->departure_time }}</p></div>
            <div class="bg-[#F5F5F5] rounded-[12px] p-3 border border-[#E5E5E5]"><span class="font-mono uppercase tracking-wider text-[10px] text-gray-400">Kendaraan</span><p class="font-medium text-[#111111] font-mono">{{ $booking->schedule->vehicle->plate_number ?? '-' }}</p></div>
            <div class="col-span-2 bg-[#F5F5F5] rounded-[12px] p-3 border border-[#E5E5E5]"><span class="font-mono uppercase tracking-wider text-[10px] text-gray-400">Jemput</span><p class="font-medium text-[#111111]">{{ $booking->pickup_address }}</p></div>
            <div class="col-span-2 bg-[#F5F5F5] rounded-[12px] p-3 border border-[#E5E5E5]"><span class="font-mono uppercase tracking-wider text-[10px] text-gray-400">Tujuan</span><p class="font-medium text-[#111111]">{{ $booking->destination_address }}</p></div>
        </div>

        <h3 class="font-mono uppercase tracking-wider text-xs font-bold text-[#111111] mt-6 mb-2">Penumpang ({{ $booking->total_passengers }})</h3>
        @foreach($booking->passengers as $p)
        <div class="flex justify-between text-sm py-1 border-b border-[#F5F5F5] last:border-0">
            <span class="text-[#111111]">{{ $p->passenger_name }} (Seat {{ $p->seat_number }})</span>
            <span class="text-gray-500 font-light">{{ $p->passenger_phone }}</span>
        </div>
        @endforeach

        @if($booking->payment)
        <div class="mt-4 p-3 rounded-[12px] text-sm border 
            @if($booking->payment->status == 'paid') bg-green-50 border-green-200
            @elseif($booking->payment->status == 'pending') bg-yellow-50 border-yellow-200
            @else bg-[#F5F5F5] border-[#E5E5E5] @endif">
            <span class="font-medium text-[#111111]">Pembayaran:</span> {{ $booking->payment->status_label }} 
            @if($booking->payment->payment_type == 'cash' && $booking->cashPayment)
            (Cash - {{ $booking->cashPayment->payment_code }})
            @endif
        </div>
        @endif
    </div>
</div>
@endsection