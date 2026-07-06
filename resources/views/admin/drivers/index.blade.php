@extends('layouts.admin')

@section('title', 'Driver')
@section('content')
<div>
    <h1 class="text-2xl font-bold text-[#111111] mb-6">Daftar Driver</h1>

    <form action="{{ route('admin.drivers.index') }}" method="GET" class="mb-6">
        <div class="relative max-w-md">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari driver..." 
                   class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition">
        </div>
    </form>

    <div class="bg-white border border-[#E5E5E5] rounded-[12px] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-[#F5F5F5] border-b border-[#E5E5E5]">
                    <tr>
                        <th class="px-4 py-3 text-left font-mono uppercase tracking-wider text-xs text-gray-500">Nama</th>
                        <th class="px-4 py-3 text-left font-mono uppercase tracking-wider text-xs text-gray-500">Email</th>
                        <th class="px-4 py-3 text-left font-mono uppercase tracking-wider text-xs text-gray-500">HP</th>
                        <th class="px-4 py-3 text-left font-mono uppercase tracking-wider text-xs text-gray-500">Agency</th>
                        <th class="px-4 py-3 text-center font-mono uppercase tracking-wider text-xs text-gray-500">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($drivers as $driver)
                    <tr class="border-t border-[#E5E5E5] hover:bg-[#F5F5F5]">
                        <td class="px-4 py-3 font-medium text-[#111111]">{{ $driver->name }}</td>
                        <td class="px-4 py-3 text-gray-600 font-light">{{ $driver->email }}</td>
                        <td class="px-4 py-3 text-gray-600 font-light">{{ $driver->phone }}</td>
                        <td class="px-4 py-3 text-gray-600 font-light">{{ $driver->driverAgency->agency_name ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-[10px] font-mono uppercase tracking-wider border
                                @if($driver->is_active) bg-green-50 text-green-700 border-green-200
                                @else bg-red-50 text-red-700 border-red-200 @endif">
                                {{ $driver->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500 font-light">Tidak ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">{{ $drivers->links() }}</div>
</div>
@endsection