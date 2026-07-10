@extends('layouts.admin')

@section('title', 'Booking')
@section('content')

@php
    $type = $type ?? 'travel';
    $totalTravel = $totalTravel ?? 0;
    $totalTour = $totalTour ?? 0;
    $totalRental = $totalRental ?? 0;
@endphp

<div>
    <h1 class="text-2xl font-bold text-[#111111] mb-6">Daftar Booking</h1>

    {{-- TABS --}}
    <div class="flex border-b border-[#E5E5E5] mb-6 overflow-x-auto">
        <a href="{{ route('admin.bookings.index', ['type' => 'travel']) }}" 
           class="px-4 py-3 text-sm font-semibold border-b-2 whitespace-nowrap transition
                  {{ $type === 'travel' ? 'border-[#C1121F] text-[#C1121F]' : 'border-transparent text-gray-500 hover:text-[#111111]' }}">
            🚌 Travel ({{ $totalTravel }})
        </a>
        <a href="{{ route('admin.bookings.index', ['type' => 'tour']) }}" 
           class="px-4 py-3 text-sm font-semibold border-b-2 whitespace-nowrap transition
                  {{ $type === 'tour' ? 'border-[#C1121F] text-[#C1121F]' : 'border-transparent text-gray-500 hover:text-[#111111]' }}">
            🏝️ Tour ({{ $totalTour }})
        </a>
        <a href="{{ route('admin.bookings.index', ['type' => 'rental']) }}" 
           class="px-4 py-3 text-sm font-semibold border-b-2 whitespace-nowrap transition
                  {{ $type === 'rental' ? 'border-[#C1121F] text-[#C1121F]' : 'border-transparent text-gray-500 hover:text-[#111111]' }}">
            🚐 Rental ({{ $totalRental }})
        </a>
    </div>

    {{-- STATS CARDS --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        @php
            if ($type === 'travel') {
                $totalAll = \App\Models\Booking::count();
                $pending = \App\Models\Booking::where('status', 'pending')->count();
                $paid = \App\Models\Booking::where('status', 'paid')->count();
                $cancelled = \App\Models\Booking::where('status', 'cancelled')->count();
            } elseif ($type === 'tour') {
                $totalAll = \App\Models\TourBooking::count();
                $pending = \App\Models\TourBooking::where('status', 'pending')->count();
                $paid = \App\Models\TourBooking::where('status', 'paid')->count();
                $cancelled = \App\Models\TourBooking::where('status', 'cancelled')->count();
            } else {
                $totalAll = \App\Models\RentalBooking::count();
                $pending = \App\Models\RentalBooking::where('status', 'pending')->count();
                $paid = \App\Models\RentalBooking::where('status', 'paid')->count();
                $cancelled = \App\Models\RentalBooking::where('status', 'cancelled')->count();
            }
        @endphp
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-4 text-center shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Total</p>
            <p class="text-2xl font-bold text-[#111111] mt-1">{{ $totalAll }}</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-4 text-center shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Pending</p>
            <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $pending }}</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-4 text-center shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Paid</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $paid }}</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-4 text-center shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Cancelled</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $cancelled }}</p>
        </div>
    </div>

    {{-- FILTER --}}
    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-4 mb-6 shadow-sm">
        <form action="{{ route('admin.bookings.index') }}" method="GET" class="flex flex-wrap gap-3 items-end">
            <input type="hidden" name="type" value="{{ $type }}">
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Status</label>
                <select name="status" class="px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] text-sm">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>📋 Confirmed</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>✅ Paid</option>
                    <option value="on_going" {{ request('status') == 'on_going' ? 'selected' : '' }}>🚗 On Going</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>🎉 Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>❌ Cancelled</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Kode Booking</label>
                <input type="text" name="booking_code" value="{{ request('booking_code') }}" 
                       placeholder="GM-... / GT-... / GR-..." 
                       class="px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] text-sm">
            </div>
            <button type="submit" class="bg-[#C1121F] text-white px-4 py-2 rounded-[12px] text-sm hover:bg-[#8A0F18] transition">Filter</button>
            @if(request()->anyFilled(['status', 'booking_code']))
            <a href="{{ route('admin.bookings.index', ['type' => $type]) }}" class="border border-[#E5E5E5] px-4 py-2 rounded-[12px] text-sm text-gray-600 hover:bg-[#F5F5F5] transition">Reset</a>
            @endif
        </form>
    </div>

    {{-- ═══════════════════════════════════════════════ --}}
    {{-- TRAVEL BOOKINGS TABLE --}}
    {{-- ═══════════════════════════════════════════════ --}}
    @if($type === 'travel')
    <div class="bg-white border border-[#E5E5E5] rounded-[12px] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-[#F5F5F5] border-b border-[#E5E5E5]">
                    <tr>
                        <th class="px-4 py-3 text-left font-mono uppercase tracking-wider text-xs text-gray-500">Kode</th>
                        <th class="px-4 py-3 text-left font-mono uppercase tracking-wider text-xs text-gray-500">Customer</th>
                        <th class="px-4 py-3 text-left font-mono uppercase tracking-wider text-xs text-gray-500">Rute</th>
                        <th class="px-4 py-3 text-left font-mono uppercase tracking-wider text-xs text-gray-500">Agency</th>
                        <th class="px-4 py-3 text-center font-mono uppercase tracking-wider text-xs text-gray-500">Status</th>
                        <th class="px-4 py-3 text-right font-mono uppercase tracking-wider text-xs text-gray-500">Total</th>
                        <th class="px-4 py-3 text-right font-mono uppercase tracking-wider text-xs text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                    <tr class="border-t border-[#E5E5E5] hover:bg-[#F5F5F5]">
                        <td class="px-4 py-3 font-mono text-xs text-[#111111]">{{ $booking->booking_code }}</td>
                        <td class="px-4 py-3 text-[#111111]">{{ $booking->customer->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-[10px] font-mono text-gray-500">{{ $booking->originStop->city_name ?? '?' }} → {{ $booking->destinationStop->city_name ?? '?' }}</td>
                        <td class="px-4 py-3 text-[10px] font-mono text-gray-500">{{ $booking->schedule->agency->agency_name ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-[10px] font-mono uppercase tracking-wider border
                                @if($booking->status == 'paid') bg-green-50 text-green-700 border-green-200
                                @elseif($booking->status == 'pending') bg-yellow-50 text-yellow-700 border-yellow-200
                                @elseif($booking->status == 'cancelled') bg-red-50 text-red-700 border-red-200
                                @elseif($booking->status == 'completed') bg-blue-50 text-blue-700 border-blue-200
                                @else bg-[#F5F5F5] text-gray-600 border-[#E5E5E5] @endif">
                                {{ $booking->status_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-mono text-[#C1121F]">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.bookings.show', ['booking' => $booking->id, 'type' => 'travel']) }}" 
                               class="text-[#C1121F] hover:underline text-sm font-medium">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500 font-light">Tidak ada booking travel.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════ --}}
    {{-- TOUR BOOKINGS TABLE --}}
    {{-- ═══════════════════════════════════════════════ --}}
    @if($type === 'tour')
    <div class="bg-white border border-[#E5E5E5] rounded-[12px] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-[#F5F5F5] border-b border-[#E5E5E5]">
                    <tr>
                        <th class="px-4 py-3 text-left font-mono uppercase tracking-wider text-xs text-gray-500">Kode</th>
                        <th class="px-4 py-3 text-left font-mono uppercase tracking-wider text-xs text-gray-500">Rombongan</th>
                        <th class="px-4 py-3 text-left font-mono uppercase tracking-wider text-xs text-gray-500">Paket</th>
                        <th class="px-4 py-3 text-left font-mono uppercase tracking-wider text-xs text-gray-500">Agency</th>
                        <th class="px-4 py-3 text-center font-mono uppercase tracking-wider text-xs text-gray-500">Peserta</th>
                        <th class="px-4 py-3 text-center font-mono uppercase tracking-wider text-xs text-gray-500">Status</th>
                        <th class="px-4 py-3 text-right font-mono uppercase tracking-wider text-xs text-gray-500">Total</th>
                        <th class="px-4 py-3 text-right font-mono uppercase tracking-wider text-xs text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                    <tr class="border-t border-[#E5E5E5] hover:bg-[#F5F5F5]">
                        <td class="px-4 py-3 font-mono text-xs text-[#111111]">{{ $booking->booking_code }}</td>
                        <td class="px-4 py-3 text-[#111111] font-medium">{{ $booking->group_name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 font-light">{{ $booking->tourSchedule->tourPackage->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-[10px] font-mono text-gray-500">{{ $booking->tourSchedule->tourPackage->agency->agency_name ?? '-' }}</td>
                        <td class="px-4 py-3 text-center font-mono text-[#111111]">{{ $booking->total_participants }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-[10px] font-mono uppercase tracking-wider border
                                @if($booking->status == 'paid') bg-green-50 text-green-700 border-green-200
                                @elseif($booking->status == 'pending') bg-yellow-50 text-yellow-700 border-yellow-200
                                @elseif($booking->status == 'cancelled') bg-red-50 text-red-700 border-red-200
                                @else bg-blue-50 text-blue-700 border-blue-200 @endif">
                                {{ $booking->status_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-mono text-[#C1121F]">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.bookings.show', ['booking' => $booking->id, 'type' => 'tour']) }}" 
                               class="text-[#C1121F] hover:underline text-sm font-medium">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-4 py-8 text-center text-gray-500 font-light">Tidak ada booking tour.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════ --}}
    {{-- RENTAL BOOKINGS TABLE --}}
    {{-- ═══════════════════════════════════════════════ --}}
    @if($type === 'rental')
    <div class="bg-white border border-[#E5E5E5] rounded-[12px] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-[#F5F5F5] border-b border-[#E5E5E5]">
                    <tr>
                        <th class="px-4 py-3 text-left font-mono uppercase tracking-wider text-xs text-gray-500">Kode</th>
                        <th class="px-4 py-3 text-left font-mono uppercase tracking-wider text-xs text-gray-500">Customer</th>
                        <th class="px-4 py-3 text-left font-mono uppercase tracking-wider text-xs text-gray-500">Keperluan</th>
                        <th class="px-4 py-3 text-left font-mono uppercase tracking-wider text-xs text-gray-500">Kendaraan</th>
                        <th class="px-4 py-3 text-left font-mono uppercase tracking-wider text-xs text-gray-500">Agency</th>
                        <th class="px-4 py-3 text-center font-mono uppercase tracking-wider text-xs text-gray-500">Tanggal</th>
                        <th class="px-4 py-3 text-center font-mono uppercase tracking-wider text-xs text-gray-500">Status</th>
                        <th class="px-4 py-3 text-right font-mono uppercase tracking-wider text-xs text-gray-500">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $b)
                    <tr class="border-t border-[#E5E5E5] hover:bg-[#F5F5F5]">
                        <td class="px-4 py-3 font-mono text-xs text-[#111111]">{{ $b->booking_code }}</td>
                        <td class="px-4 py-3 text-[#111111]">{{ $b->customer->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 font-light">{{ $b->purpose }}</td>
                        <td class="px-4 py-3 font-mono text-xs text-[#111111]">{{ $b->vehicle->plate_number ?? '-' }}</td>
                        <td class="px-4 py-3 text-[10px] font-mono text-gray-500">{{ $b->vehicle->agency->agency_name ?? '-' }}</td>
                        <td class="px-4 py-3 text-center text-xs text-[#111111]">
                            {{ $b->start_date->format('d M') }} → {{ $b->end_date->format('d M Y') }}
                            <br><span class="text-[10px] text-gray-400">{{ $b->total_days }} hari</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-[10px] font-mono uppercase tracking-wider border
                                @if($b->status == 'paid') bg-green-50 text-green-700 border-green-200
                                @elseif($b->status == 'pending') bg-yellow-50 text-yellow-700 border-yellow-200
                                @elseif($b->status == 'cancelled') bg-red-50 text-red-700 border-red-200
                                @else bg-[#F5F5F5] text-gray-600 border-[#E5E5E5] @endif">{{ $b->status_label }}</span>
                        </td>
                        <td class="px-4 py-3 text-right font-mono text-[#C1121F]">Rp {{ number_format($b->total_price, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-4 py-8 text-center text-gray-500 font-light">Tidak ada booking rental.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif
    
    <div class="mt-4">{{ $bookings->appends(request()->query())->links() }}</div>
</div>
@endsection