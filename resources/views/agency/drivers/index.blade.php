@extends('layouts.agency')

@section('title', 'Driver')
@section('content')
<div>
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6 border-b border-[#E5E5E5] pb-3">
        <h1 class="text-2xl font-bold text-[#111111]">Driver</h1>
        <a href="{{ route('agency.drivers.create') }}" class="btn-gomad-primary text-sm inline-flex items-center gap-2 self-start rounded-[12px]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Driver
        </a>
    </div>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($drivers as $driver)
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] overflow-hidden shadow-sm hover:border-[#C1121F] transition-colors">
            <div class="bg-[#F5F5F5] p-6 text-center border-b border-[#E5E5E5]">
                <div class="w-24 h-24 rounded-full mx-auto overflow-hidden border-4 border-white shadow-sm">
                    @if($driver->avatar_url)
                    <img src="{{ $driver->avatar_url }}" alt="{{ $driver->name }}" class="w-full h-full object-cover">
                    @else
                    <div class="w-full h-full bg-[#E5E5E5] flex items-center justify-center text-3xl">👨‍✈️</div>
                    @endif
                </div>
                <h3 class="font-bold text-lg text-[#111111] mt-3">{{ $driver->name }}</h3>
            </div>
            <div class="p-5">
                <div class="space-y-2 text-sm mb-4 font-light">
                    <div class="flex items-center gap-2"><span class="font-mono">📧</span><span class="text-gray-600">{{ $driver->email }}</span></div>
                    <div class="flex items-center gap-2"><span class="font-mono">📞</span><span class="text-gray-600">{{ $driver->phone ?? '-' }}</span></div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-mono uppercase tracking-wider border {{ $driver->is_active ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200' }}">{{ $driver->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                </div>
                <div class="flex gap-2 border-t border-[#E5E5E5] pt-4">
                    <a href="{{ route('agency.drivers.edit', $driver) }}" class="flex-1 text-center border border-[#E5E5E5] text-[#111111] py-2 rounded-[12px] text-sm font-medium hover:bg-[#F5F5F5] transition">Edit</a>
                    <form action="{{ route('agency.drivers.destroy', $driver) }}" method="POST" class="flex-1" onsubmit="return confirm('Hapus?')">
                        @csrf @method('DELETE')
                        <button class="w-full border border-red-500 text-red-600 py-2 rounded-[12px] text-sm font-medium hover:bg-red-50 transition">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white border border-[#E5E5E5] rounded-[12px] p-12 text-center text-gray-500 shadow-sm">
            <p class="text-lg font-light">Belum ada driver.</p>
            <a href="{{ route('agency.drivers.create') }}" class="text-[#C1121F] hover:underline mt-2 inline-block font-medium">+ Tambah Driver</a>
        </div>
        @endforelse
    </div>
</div>
@endsection