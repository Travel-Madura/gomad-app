@extends('layouts.admin')

@section('title', 'Detail Rute')
@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6 border-b border-[#E5E5E5] pb-3">
        <a href="{{ route('admin.routes.index') }}" class="text-[#C1121F] text-sm hover:underline">← Kembali</a>
        <div class="flex gap-2">
            <a href="{{ route('admin.routes.edit', $route) }}" class="bg-[#C1121F] text-white px-4 py-2 rounded-[12px] text-sm hover:bg-[#8A0F18]">Edit</a>
            <form action="{{ route('admin.routes.destroy', $route) }}" method="POST" onsubmit="return confirm('Nonaktifkan rute?')">
                @csrf @method('DELETE')
                <button class="bg-[#C1121F] text-white px-4 py-2 rounded-[12px] text-sm hover:bg-[#8A0F18]">Nonaktifkan</button>
            </form>
        </div>
    </div>

    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-6 shadow-sm">
        <h1 class="text-2xl font-bold text-[#111111] mb-4">{{ $route->route_name }}</h1>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3"><span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Kota Asal</span><p class="font-semibold text-[#111111]">{{ $route->origin_city }}</p></div>
            <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3"><span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Kota Tujuan</span><p class="font-semibold text-[#111111]">{{ $route->destination_city }}</p></div>
            <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3"><span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Jarak</span><p class="font-semibold text-[#111111]">{{ $route->distance_km ?? '-' }} km</p></div>
            <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3"><span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Estimasi</span><p class="font-semibold text-[#111111]">{{ $route->estimated_duration ?? '-' }} menit</p></div>
            <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3"><span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Status</span><p><span class="px-2 py-1 rounded-full text-[10px] font-mono uppercase tracking-wider border {{ $route->is_active ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200' }}">
                    {{ $route->is_active ? 'Aktif' : 'Nonaktif' }}
                </span></p></div>
        </div>
    </div>

    <!-- Stops -->
    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
        <div class="flex justify-between items-center mb-4 border-b border-[#E5E5E5] pb-3">
            <h2 class="text-lg font-bold text-[#111111]">Stops ({{ $route->stops->count() }})</h2>
            <button onclick="document.getElementById('addStopForm').classList.toggle('hidden')" 
                    class="bg-[#C1121F] text-white px-3 py-1 rounded-[12px] text-sm hover:bg-[#8A0F18]">
                + Tambah Stop
            </button>
        </div>

        <!-- Add Stop Form -->
        <form id="addStopForm" action="{{ route('admin.routes.stops.add', $route) }}" method="POST" class="hidden bg-[#F5F5F5] border border-[#E5E5E5] p-4 rounded-[12px] mb-4">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <input type="text" name="city_name" placeholder="Nama Kota" class="px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]" required>
                <input type="number" name="stop_order" placeholder="Urutan" class="px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]">
                <input type="number" name="latitude" step="0.0000001" placeholder="Latitude" class="px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]">
                <input type="number" name="longitude" step="0.0000001" placeholder="Longitude" class="px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]">
            </div>
            <button type="submit" class="mt-3 bg-[#C1121F] text-white px-4 py-2 rounded-[12px] text-sm">Simpan Stop</button>
        </form>

        <!-- Stops List -->
        <div class="space-y-2">
            @foreach($route->stops as $stop)
            <div class="flex justify-between items-center bg-[#F5F5F5] border border-[#E5E5E5] px-4 py-3 rounded-[12px]">
                <div>
                    <span class="font-medium text-[#111111]">{{ $stop->city_name }}</span>
                    <span class="text-[10px] font-mono text-gray-400 ml-2">Order: {{ $stop->stop_order }}</span>
                    @if($stop->latitude && $stop->longitude)
                    <span class="text-[10px] font-mono text-gray-400 ml-2">({{ $stop->latitude }}, {{ $stop->longitude }})</span>
                    @endif
                </div>
                <form action="{{ route('admin.routes.stops.remove', [$route, $stop]) }}" method="POST" onsubmit="return confirm('Hapus stop?')">
                    @csrf @method('DELETE')
                    <button class="text-[#C1121F] text-sm hover:underline">Hapus</button>
                </form>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection