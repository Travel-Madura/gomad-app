@extends('layouts.agency')

@section('title', 'Booking')
@section('content')

@php
    $type = request('type', 'travel');
    $agency = auth()->user()->agency;
    
    // Counts
    $totalTravel = \App\Models\Booking::whereHas('schedule', fn($q) => $q->where('agency_id', $agency->id))->count();
    $totalTour = \App\Models\TourBooking::whereHas('tourSchedule.tourPackage', fn($q) => $q->where('agency_id', $agency->id))->count();
    $totalRental = \App\Models\RentalBooking::whereHas('vehicle', fn($q) => $q->where('agency_id', $agency->id))->count();
    
    // Data berdasarkan tipe
    if ($type === 'tour') {
        $query = \App\Models\TourBooking::with(['tourSchedule.tourPackage', 'customer', 'payment'])
            ->whereHas('tourSchedule.tourPackage', fn($q) => $q->where('agency_id', $agency->id));
        if (request('status')) $query->where('status', request('status'));
        $bookings = $query->orderBy('created_at', 'desc')->paginate(15);
    } elseif ($type === 'rental') {
        $query = \App\Models\RentalBooking::with(['vehicle', 'customer', 'payment'])
            ->whereHas('vehicle', fn($q) => $q->where('agency_id', $agency->id));
        if (request('status')) $query->where('status', request('status'));
        $bookings = $query->orderBy('created_at', 'desc')->paginate(15);
    } else {
        $query = \App\Models\Booking::with(['schedule.route', 'customer', 'originStop', 'destinationStop', 'payment'])
            ->whereHas('schedule', fn($q) => $q->where('agency_id', $agency->id));
        if (request('status')) $query->where('status', request('status'));
        $bookings = $query->orderBy('created_at', 'desc')->paginate(15);
    }
@endphp

<div>
    <h1 class="text-2xl font-bold text-[#111111] mb-6">Daftar Booking</h1>

    {{-- STATS --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-4 text-center shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">🚌 Travel</p>
            <p class="text-2xl font-bold text-[#111111] mt-1">{{ $totalTravel }}</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-4 text-center shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">🏝️ Tour</p>
            <p class="text-2xl font-bold text-purple-600 mt-1">{{ $totalTour }}</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-4 text-center shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">🚐 Rental</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $totalRental }}</p>
        </div>
    </div>

    {{-- TABS --}}
    <div class="flex border-b border-[#E5E5E5] mb-6 overflow-x-auto">
        <a href="{{ route('agency.bookings.index', ['type' => 'travel']) }}" 
           class="px-4 py-3 text-sm font-semibold border-b-2 whitespace-nowrap transition
                  {{ $type === 'travel' ? 'border-[#C1121F] text-[#C1121F]' : 'border-transparent text-gray-500 hover:text-[#111111]' }}">
            🚌 Travel ({{ $totalTravel }})
        </a>
        <a href="{{ route('agency.bookings.index', ['type' => 'tour']) }}" 
           class="px-4 py-3 text-sm font-semibold border-b-2 whitespace-nowrap transition
                  {{ $type === 'tour' ? 'border-[#C1121F] text-[#C1121F]' : 'border-transparent text-gray-500 hover:text-[#111111]' }}">
            🏝️ Tour ({{ $totalTour }})
        </a>
        <a href="{{ route('agency.bookings.index', ['type' => 'rental']) }}" 
           class="px-4 py-3 text-sm font-semibold border-b-2 whitespace-nowrap transition
                  {{ $type === 'rental' ? 'border-[#C1121F] text-[#C1121F]' : 'border-transparent text-gray-500 hover:text-[#111111]' }}">
            🚐 Rental ({{ $totalRental }})
        </a>
    </div>

    {{-- FILTER STATUS --}}
    <div class="flex gap-2 mb-6 overflow-x-auto pb-2">
        <a href="{{ route('agency.bookings.index', ['type' => $type]) }}" 
           class="px-3 py-1.5 rounded-[8px] text-[10px] font-mono uppercase tracking-wider font-medium whitespace-nowrap border
                  {{ !request('status') ? 'bg-[#C1121F] text-white border-[#C1121F]' : 'bg-white text-gray-600 border-[#E5E5E5] hover:bg-[#F5F5F5]' }}">
            Semua
        </a>
        <a href="{{ route('agency.bookings.index', ['type' => $type, 'status' => 'pending']) }}" 
           class="px-3 py-1.5 rounded-[8px] text-[10px] font-mono uppercase tracking-wider font-medium whitespace-nowrap border
                  {{ request('status') == 'pending' ? 'bg-yellow-500 text-white border-yellow-500' : 'bg-white text-gray-600 border-[#E5E5E5] hover:bg-[#F5F5F5]' }}">
            ⏳ Pending
        </a>
        <a href="{{ route('agency.bookings.index', ['type' => $type, 'status' => 'confirmed']) }}" 
           class="px-3 py-1.5 rounded-[8px] text-[10px] font-mono uppercase tracking-wider font-medium whitespace-nowrap border
                  {{ request('status') == 'confirmed' ? 'bg-blue-500 text-white border-blue-500' : 'bg-white text-gray-600 border-[#E5E5E5] hover:bg-[#F5F5F5]' }}">
            📋 Confirmed
        </a>
        <a href="{{ route('agency.bookings.index', ['type' => $type, 'status' => 'paid']) }}" 
           class="px-3 py-1.5 rounded-[8px] text-[10px] font-mono uppercase tracking-wider font-medium whitespace-nowrap border
                  {{ request('status') == 'paid' ? 'bg-green-500 text-white border-green-500' : 'bg-white text-gray-600 border-[#E5E5E5] hover:bg-[#F5F5F5]' }}">
            ✅ Paid
        </a>
        <a href="{{ route('agency.bookings.index', ['type' => $type, 'status' => 'on_going']) }}" 
           class="px-3 py-1.5 rounded-[8px] text-[10px] font-mono uppercase tracking-wider font-medium whitespace-nowrap border
                  {{ request('status') == 'on_going' ? 'bg-indigo-500 text-white border-indigo-500' : 'bg-white text-gray-600 border-[#E5E5E5] hover:bg-[#F5F5F5]' }}">
            🚗 On Going
        </a>
        <a href="{{ route('agency.bookings.index', ['type' => $type, 'status' => 'completed']) }}" 
           class="px-3 py-1.5 rounded-[8px] text-[10px] font-mono uppercase tracking-wider font-medium whitespace-nowrap border
                  {{ request('status') == 'completed' ? 'bg-purple-500 text-white border-purple-500' : 'bg-white text-gray-600 border-[#E5E5E5] hover:bg-[#F5F5F5]' }}">
            🎉 Completed
        </a>
        <a href="{{ route('agency.bookings.index', ['type' => $type, 'status' => 'cancelled']) }}" 
           class="px-3 py-1.5 rounded-[8px] text-[10px] font-mono uppercase tracking-wider font-medium whitespace-nowrap border
                  {{ request('status') == 'cancelled' ? 'bg-red-500 text-white border-red-500' : 'bg-white text-gray-600 border-[#E5E5E5] hover:bg-[#F5F5F5]' }}">
            ❌ Cancelled
        </a>
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
                        <th class="px-4 py-3 text-center font-mono uppercase tracking-wider text-xs text-gray-500">Penumpang</th>
                        <th class="px-4 py-3 text-center font-mono uppercase tracking-wider text-xs text-gray-500">Status</th>
                        <th class="px-4 py-3 text-right font-mono uppercase tracking-wider text-xs text-gray-500">Total</th>
                        <th class="px-4 py-3 text-right font-mono uppercase tracking-wider text-xs text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                    <tr class="border-t border-[#E5E5E5] hover:bg-[#F5F5F5]">
                        <td class="px-4 py-3 font-mono text-xs text-[#111111]">{{ $booking->booking_code }}</td>
                        <td class="px-4 py-3 text-sm text-[#111111]">{{ $booking->customer->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 font-light">{{ $booking->originStop->city_name ?? '?' }} → {{ $booking->destinationStop->city_name ?? '?' }}</td>
                        <td class="px-4 py-3 text-center font-mono text-[#111111]">{{ $booking->total_passengers }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-[10px] font-mono uppercase tracking-wider border
                                @if($booking->status == 'paid') bg-green-50 text-green-700 border-green-200
                                @elseif($booking->status == 'pending') bg-yellow-50 text-yellow-700 border-yellow-200
                                @elseif($booking->status == 'cancelled') bg-red-50 text-red-700 border-red-200
                                @elseif($booking->status == 'completed') bg-blue-50 text-blue-700 border-blue-200
                                @elseif($booking->status == 'on_going') bg-indigo-50 text-indigo-700 border-indigo-200
                                @else bg-[#F5F5F5] text-gray-600 border-[#E5E5E5] @endif">
                                {{ $booking->status_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-mono text-[#C1121F]">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('agency.bookings.show', $booking) }}" class="text-[#C1121F] hover:underline text-sm font-medium">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500 font-light">Belum ada booking travel.</td></tr>
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
                        <th class="px-4 py-3 text-center font-mono uppercase tracking-wider text-xs text-gray-500">Peserta</th>
                        <th class="px-4 py-3 text-center font-mono uppercase tracking-wider text-xs text-gray-500">Status</th>
                        <th class="px-4 py-3 text-right font-mono uppercase tracking-wider text-xs text-gray-500">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                    <tr class="border-t border-[#E5E5E5] hover:bg-[#F5F5F5]">
                        <td class="px-4 py-3 font-mono text-xs text-[#111111]">{{ $booking->booking_code }}</td>
                        <td class="px-4 py-3 text-[#111111] font-medium">{{ $booking->group_name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 font-light">{{ $booking->tourSchedule->tourPackage->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-center font-mono text-[#111111]">{{ $booking->total_participants }}</td>
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
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500 font-light">Belum ada booking tour.</td></tr>
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
                        <th class="px-4 py-3 text-center font-mono uppercase tracking-wider text-xs text-gray-500">Tanggal</th>
                        <th class="px-4 py-3 text-center font-mono uppercase tracking-wider text-xs text-gray-500">Status</th>
                        <th class="px-4 py-3 text-right font-mono uppercase tracking-wider text-xs text-gray-500">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                    <tr class="border-t border-[#E5E5E5] hover:bg-[#F5F5F5]">
                        <td class="px-4 py-3 font-mono text-xs text-[#111111]">{{ $booking->booking_code }}</td>
                        <td class="px-4 py-3 text-[#111111]">{{ $booking->customer->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 font-light">{{ $booking->purpose }}</td>
                        <td class="px-4 py-3 font-mono text-xs text-[#111111]">{{ $booking->vehicle->plate_number ?? '-' }}</td>
                        <td class="px-4 py-3 text-center text-xs text-[#111111]">
                            {{ \Carbon\Carbon::parse($booking->start_date)->format('d M') }} → {{ \Carbon\Carbon::parse($booking->end_date)->format('d M Y') }}
                            <br><span class="text-[10px] text-gray-400">{{ $booking->total_days }} hari</span>
                        </td>
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
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500 font-light">Belum ada booking rental.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div class="mt-4">{{ $bookings->appends(request()->query())->links() }}</div>
</div>
@endsection