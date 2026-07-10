@extends('layouts.agency')

@section('title', $package->name)
@section('content')

@php
    $schedules = $package->schedules()->with(['vehicle', 'driver'])->withCount(['bookings'])->latest()->get();
    $allBookings = \App\Models\TourBooking::whereHas('tourSchedule', fn($q) => $q->where('tour_package_id', $package->id))
        ->with(['customer', 'participants', 'payment', 'tourSchedule'])
        ->latest()
        ->limit(20)
        ->get();
    $totalBookings = \App\Models\TourBooking::whereHas('tourSchedule', fn($q) => $q->where('tour_package_id', $package->id))->count();
    $totalRevenue = \App\Models\TourBooking::whereHas('tourSchedule', fn($q) => $q->where('tour_package_id', $package->id))
        ->where('status', 'paid')->sum('total_price');
@endphp

<div class="max-w-5xl mx-auto">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4 mb-6">
        <div>
            <a href="{{ route('agency.tours.index') }}" class="text-[#C1121F] text-sm mb-2 inline-block hover:underline">← Kembali</a>
            <h1 class="text-2xl font-bold text-[#111111]">{{ $package->name }}</h1>
            <p class="text-sm text-gray-500 font-light mt-1">
                {{ $package->duration_days }} Hari {{ $package->duration_nights }} Malam • 
                {{ $package->stops->count() }} Stops
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('agency.tours.edit', $package) }}" class="btn-gomad-primary text-sm px-4 py-2 rounded-[12px]">Edit Paket</a>
            <a href="{{ route('agency.tours.schedules.create', $package) }}" class="bg-green-600 text-white text-sm px-4 py-2 rounded-[12px] hover:bg-green-700 transition">+ Jadwal</a>
        </div>
    </div>

    {{-- Info Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-4 shadow-sm text-center">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Total Booking</p>
            <p class="text-2xl font-bold text-[#111111]">{{ $totalBookings }}</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-4 shadow-sm text-center">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Jadwal Aktif</p>
            <p class="text-2xl font-bold text-blue-600">{{ $schedules->where('is_active', true)->where('departure_date', '>=', now()->toDateString())->count() }}</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-4 shadow-sm text-center">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Total Revenue</p>
            <p class="text-lg font-bold text-[#C1121F]">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-4 shadow-sm text-center">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Status</p>
            <span class="px-2 py-1 rounded-full text-[10px] font-mono uppercase tracking-wider border {{ $package->is_active ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200' }}">
                {{ $package->is_active ? 'Aktif' : 'Nonaktif' }}
            </span>
        </div>
    </div>

    {{-- Detail Paket --}}
    <div class="grid md:grid-cols-2 gap-6 mb-6">
        {{-- Includes --}}
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
            <h3 class="font-bold text-[#111111] mb-3">✅ Sudah Termasuk</h3>
            @if(!empty($package->includes))
            <ul class="space-y-2 text-sm text-gray-600 font-light list-disc list-inside">
                @foreach((array) $package->includes as $item)
                <li>{{ $item }}</li>
                @endforeach
            </ul>
            @else
            <p class="text-gray-400 text-sm font-light">Belum diisi</p>
            @endif
        </div>
        {{-- Excludes --}}
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
            <h3 class="font-bold text-[#111111] mb-3">❌ Tidak Termasuk</h3>
            @if(!empty($package->excludes))
            <ul class="space-y-2 text-sm text-gray-600 font-light list-disc list-inside">
                @foreach((array) $package->excludes as $item)
                <li>{{ $item }}</li>
                @endforeach
            </ul>
            @else
            <p class="text-gray-400 text-sm font-light">Belum diisi</p>
            @endif
        </div>
    </div>

    {{-- Jadwal --}}
    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-6 shadow-sm">
        <div class="flex justify-between items-center mb-4">
            <h2 class="font-bold text-lg text-[#111111]">📅 Jadwal Keberangkatan</h2>
            <a href="{{ route('agency.tours.schedules.create', $package) }}" class="text-[#C1121F] text-sm hover:underline font-medium">+ Tambah Jadwal</a>
        </div>
        
        @if($schedules->isEmpty())
        <p class="text-gray-500 text-center py-8 font-light">Belum ada jadwal. Tambahkan jadwal keberangkatan.</p>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-[#F5F5F5] border-b border-[#E5E5E5]">
                    <tr>
                        <th class="px-4 py-3 text-left font-mono uppercase tracking-wider text-xs text-gray-500">Tanggal</th>
                        <th class="px-4 py-3 text-left font-mono uppercase tracking-wider text-xs text-gray-500">Kendaraan</th>
                        <th class="px-4 py-3 text-center font-mono uppercase tracking-wider text-xs text-gray-500">Harga</th>
                        <th class="px-4 py-3 text-center font-mono uppercase tracking-wider text-xs text-gray-500">Peserta</th>
                        <th class="px-4 py-3 text-center font-mono uppercase tracking-wider text-xs text-gray-500">Booking</th>
                        <th class="px-4 py-3 text-center font-mono uppercase tracking-wider text-xs text-gray-500">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E5E5]">
                    @foreach($schedules as $schedule)
                    <tr class="hover:bg-[#F5F5F5]">
                        <td class="px-4 py-3">
                            <span class="font-medium text-[#111111]">{{ $schedule->departure_date->format('d M Y') }}</span>
                            <span class="text-xs text-gray-400 ml-1">{{ $schedule->departure_time }}</span>
                        </td>
                        <td class="px-4 py-3 font-mono text-xs text-[#111111]">{{ $schedule->vehicle->plate_number ?? '-' }}</td>
                        <td class="px-4 py-3 text-center font-mono text-[#C1121F]">Rp {{ number_format($schedule->base_price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center font-mono text-[#111111]">{{ $schedule->bookings_count ?? 0 }}/{{ $schedule->max_participants }}</td>
                        <td class="px-4 py-3 text-center font-mono text-[#111111]">{{ $schedule->bookings_count ?? 0 }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-[10px] font-mono uppercase tracking-wider border {{ $schedule->is_active ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200' }}">
                                {{ $schedule->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- Recent Bookings --}}
    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
        <h2 class="font-bold text-lg text-[#111111] mb-4">🎫 Booking Terbaru</h2>
        @if($allBookings->isEmpty())
        <p class="text-gray-500 text-center py-8 font-light">Belum ada booking.</p>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-[#F5F5F5] border-b border-[#E5E5E5]">
                    <tr>
                        <th class="px-4 py-3 text-left font-mono uppercase tracking-wider text-xs text-gray-500">Kode</th>
                        <th class="px-4 py-3 text-left font-mono uppercase tracking-wider text-xs text-gray-500">Rombongan</th>
                        <th class="px-4 py-3 text-left font-mono uppercase tracking-wider text-xs text-gray-500">Customer</th>
                        <th class="px-4 py-3 text-center font-mono uppercase tracking-wider text-xs text-gray-500">Peserta</th>
                        <th class="px-4 py-3 text-right font-mono uppercase tracking-wider text-xs text-gray-500">Total</th>
                        <th class="px-4 py-3 text-center font-mono uppercase tracking-wider text-xs text-gray-500">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E5E5]">
                    @foreach($allBookings as $booking)
                    <tr class="hover:bg-[#F5F5F5]">
                        <td class="px-4 py-3 font-mono text-xs text-[#111111]">{{ $booking->booking_code }}</td>
                        <td class="px-4 py-3 font-medium text-[#111111]">{{ $booking->group_name }}</td>
                        <td class="px-4 py-3 text-gray-600 font-light">{{ $booking->customer->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-center font-mono text-[#111111]">{{ $booking->total_participants }}</td>
                        <td class="px-4 py-3 text-right font-mono text-[#C1121F]">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-[10px] font-mono uppercase tracking-wider border
                                @if($booking->status == 'paid') bg-green-50 text-green-700 border-green-200
                                @elseif($booking->status == 'pending') bg-yellow-50 text-yellow-700 border-yellow-200
                                @elseif($booking->status == 'cancelled') bg-red-50 text-red-700 border-red-200
                                @else bg-blue-50 text-blue-700 border-blue-200 @endif">
                                {{ $booking->status_label }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection