@extends('layouts.agency')

@section('title', 'Jadwal')
@section('content')
<div>
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6 border-b border-[#E5E5E5] pb-3">
        <h1 class="text-2xl font-bold text-[#111111]">Daftar Jadwal</h1>
        <a href="{{ route('agency.schedules.create') }}" class="btn-gomad-primary text-sm inline-flex items-center gap-2 self-start rounded-[12px]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Buat Jadwal
        </a>
    </div>

    @if($schedules->isEmpty())
    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-12 text-center shadow-sm">
        <div class="w-16 h-16 bg-[#C1121F]/5 rounded-[12px] flex items-center justify-center mx-auto mb-4 border border-[#E5E5E5]">
            <span class="text-2xl">📅</span>
        </div>
        <p class="text-gray-500 text-lg font-light mb-4">Belum ada jadwal.</p>
        <a href="{{ route('agency.schedules.create') }}" class="btn-gomad-primary">Buat Jadwal Pertama</a>
    </div>
    @else
    <div class="bg-white border border-[#E5E5E5] rounded-[12px] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-[#F5F5F5] border-b border-[#E5E5E5]">
                    <tr>
                        <th class="px-4 py-3 text-left font-mono uppercase tracking-wider text-gray-500 text-xs">Rute</th>
                        <th class="px-4 py-3 text-left font-mono uppercase tracking-wider text-gray-500 text-xs">Tanggal</th>
                        <th class="px-4 py-3 text-left font-mono uppercase tracking-wider text-gray-500 text-xs">Kendaraan</th>
                        <th class="px-4 py-3 text-left font-mono uppercase tracking-wider text-gray-500 text-xs">Driver</th>
                        <th class="px-4 py-3 text-center font-mono uppercase tracking-wider text-gray-500 text-xs">Okupansi</th>
                        <th class="px-4 py-3 text-right font-mono uppercase tracking-wider text-gray-500 text-xs">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E5E5]">
                    @foreach($schedules as $schedule)
                    @php
                        $rate = $schedule->occupancy_rate;
                        $colorClass = $rate >= 80 ? 'text-red-600 bg-red-50 border-red-200' : ($rate >= 50 ? 'text-yellow-600 bg-yellow-50 border-yellow-200' : 'text-green-600 bg-green-50 border-green-200');
                        $canTransfer = app(\App\Services\PassengerTransferService::class)->canTransfer($schedule);
                    @endphp
                    <tr class="hover:bg-[#F5F5F5]">
                        <td class="px-4 py-3 font-medium text-[#111111]">{{ $schedule->route->route_name }}</td>
                        <td class="px-4 py-3 text-xs font-mono text-[#111111]">{{ $schedule->departure_date->format('d M Y') }} <span class="text-gray-400">{{ $schedule->departure_time }}</span></td>
                        <td class="px-4 py-3 text-xs font-mono text-gray-500">{{ $schedule->vehicle->plate_number ?? '-' }}</td>
                        <td class="px-4 py-3 text-xs font-mono text-gray-500">{{ $schedule->driver->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-[10px] font-mono uppercase tracking-wider border {{ $colorClass }}">{{ $rate }}%</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('agency.schedules.show', $schedule) }}" class="text-[#C1121F] hover:underline text-xs font-medium">Detail</a>
                                @if($canTransfer && $schedule->departure_date->isFuture())
                                <a href="{{ route('agency.schedules.transfer', $schedule) }}" class="text-orange-500 hover:underline text-xs font-medium">Transfer</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">{{ $schedules->links() }}</div>
    @endif
</div>
@endsection