@extends('layouts.customer')

@section('title', 'Riwayat Sewa Kendaraan')
@section('content')

<div class="container-magazine py-8">
    <h1 class="text-2xl font-bold text-[#111111] mb-6">🚐 Riwayat Sewa Kendaraan</h1>

    @if($bookings->isEmpty())
    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-12 text-center shadow-sm">
        <p class="text-gray-500 text-lg font-light mb-4">Belum ada riwayat sewa.</p>
        <a href="{{ route('customer.rental.index') }}" class="btn-gomad-primary inline-block">Cari Kendaraan</a>
    </div>
    @else
    <div class="space-y-4">
        @foreach($bookings as $b)
        <a href="{{ route('customer.rental.detail', $b) }}" class="block bg-white border border-[#E5E5E5] rounded-[12px] p-5 shadow-sm hover:border-[#C1121F] transition">
            <div class="flex flex-col md:flex-row md:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="font-bold font-mono text-[#111111]">{{ $b->booking_code }}</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-mono border
                            @if($b->status == 'paid') bg-green-50 text-green-700 border-green-200
                            @elseif($b->status == 'pending') bg-yellow-50 text-yellow-700 border-yellow-200
                            @else bg-[#F5F5F5] text-gray-600 border-[#E5E5E5] @endif">{{ $b->status_label }}</span>
                    </div>
                    <p class="font-medium text-[#111111]">🚐 {{ $b->vehicle->plate_number }} ({{ $b->vehicle->brand }} {{ $b->vehicle->model }})</p>
                    <p class="text-sm text-gray-500 font-light">{{ $b->purpose }}</p>
                    <p class="text-sm text-gray-500 font-light">📅 {{ $b->start_date->format('d M') }} → {{ $b->end_date->format('d M Y') }} ({{ $b->total_days }} hari)</p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-xl font-bold text-[#C1121F] font-mono">Rp {{ number_format($b->total_price, 0, ',', '.') }}</p>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    @endif
</div>
@endsection