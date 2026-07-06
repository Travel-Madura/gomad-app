@extends('layouts.admin')

@section('title', 'Rute')
@section('content')
<div>
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6 border-b border-[#E5E5E5] pb-3">
        <h1 class="text-2xl font-bold text-[#111111]">Daftar Rute</h1>
        <a href="{{ route('admin.routes.create') }}" class="btn-gomad-primary text-sm inline-flex items-center gap-2 self-start rounded-[12px]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Rute
        </a>
    </div>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($routes as $route)
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] overflow-hidden shadow-sm hover:border-[#C1121F] transition-colors group">
            <div class="h-40 bg-[#F5F5F5] flex items-center justify-center overflow-hidden border-b border-[#E5E5E5]">
                @if($route->photo)
                <img src="{{ $route->photo }}" alt="{{ $route->route_name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                @else
                <div class="text-center text-gray-400"><span class="text-5xl block mb-2">🗺️</span><span class="text-sm font-light">Belum ada foto</span></div>
                @endif
            </div>
            <div class="p-5">
                <div class="flex justify-between items-start mb-2">
                    <h3 class="font-bold text-[#111111]">{{ $route->route_name }}</h3>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-mono uppercase tracking-wider border {{ $route->is_active ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200' }}">
                        {{ $route->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-500 font-light mb-3">
                    <span>{{ $route->origin_city }}</span><span>→</span><span>{{ $route->destination_city }}</span>
                </div>
                <div class="flex items-center gap-4 text-[10px] text-gray-400 font-mono uppercase tracking-wider mb-4">
                    <span>🛑 {{ $route->stops_count }} stops</span>
                    @if($route->distance_km)<span>📏 {{ $route->distance_km }} km</span>@endif
                </div>
                <div class="flex gap-2 border-t border-[#E5E5E5] pt-4">
                    <a href="{{ route('admin.routes.show', $route) }}" class="flex-1 text-center border border-[#E5E5E5] text-[#111111] py-2 rounded-[12px] text-sm font-medium hover:bg-[#F5F5F5] transition">Detail</a>
                    <a href="{{ route('admin.routes.edit', $route) }}" class="flex-1 text-center bg-[#C1121F] text-white py-2 rounded-[12px] text-sm font-medium hover:bg-[#8A0F18] transition">Edit</a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white border border-[#E5E5E5] rounded-[12px] p-12 text-center text-gray-500 shadow-sm">
            <p class="text-lg font-light">Belum ada rute.</p>
            <a href="{{ route('admin.routes.create') }}" class="text-[#C1121F] hover:underline mt-2 inline-block font-medium">+ Tambah Rute</a>
        </div>
        @endforelse
    </div>
</div>
@endsection