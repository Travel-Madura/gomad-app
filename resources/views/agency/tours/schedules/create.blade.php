@extends('layouts.agency')

@section('title', 'Tambah Jadwal Tour')
@section('content')

<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('agency.tours.show', $package) }}" class="text-[#C1121F] hover:underline text-sm">← Kembali</a>
        <h1 class="text-2xl font-bold text-[#111111]">Tambah Jadwal Tour</h1>
    </div>

    <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-4 mb-6">
        <p class="text-sm font-medium text-[#111111]">📋 Paket: {{ $package->name }}</p>
        <p class="text-xs text-gray-500 font-light mt-1">{{ $package->duration_days }}H{{ $package->duration_nights }}M • {{ $package->stops->count() }} stops</p>
    </div>

    <form action="{{ route('agency.tours.schedules.store', $package) }}" method="POST" class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm space-y-5">
        @csrf

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Kendaraan <span class="text-[#C1121F]">*</span></label>
                <select name="vehicle_id" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" required>
                    <option value="">Pilih Kendaraan</option>
                    @foreach($vehicles as $v)
                    <option value="{{ $v->id }}" {{ old('vehicle_id') == $v->id ? 'selected' : '' }}>{{ $v->plate_number }} ({{ $v->capacity }} seat)</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Driver</label>
                <select name="driver_id" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition">
                    <option value="">Pilih Driver</option>
                    @foreach($drivers as $d)
                    <option value="{{ $d->id }}" {{ old('driver_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Tanggal Berangkat <span class="text-[#C1121F]">*</span></label>
                <input type="date" name="departure_date" value="{{ old('departure_date') }}" min="{{ now()->toDateString() }}" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" required>
            </div>
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Jam Berangkat <span class="text-[#C1121F]">*</span></label>
                <input type="time" name="departure_time" value="{{ old('departure_time', '07:00') }}" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" required>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Tanggal Kembali</label>
                <input type="date" name="return_date" value="{{ old('return_date') }}" min="{{ now()->toDateString() }}" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition">
            </div>
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Jam Kembali</label>
                <input type="time" name="return_time" value="{{ old('return_time') }}" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition">
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Harga Dewasa (Rp) <span class="text-[#C1121F]">*</span></label>
                <input type="number" name="base_price" value="{{ old('base_price', 500000) }}" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" min="1000" required>
            </div>
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Harga Anak (Rp)</label>
                <input type="number" name="child_price" value="{{ old('child_price') }}" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" min="0" placeholder="Opsional">
            </div>
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Maks Peserta <span class="text-[#C1121F]">*</span></label>
                <input type="number" name="max_participants" value="{{ old('max_participants', 20) }}" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" min="1" required>
            </div>
        </div>

        <button type="submit" class="btn-gomad-primary w-full py-3 rounded-[12px] font-bold text-lg">
            💾 SIMPAN JADWAL
        </button>
    </form>
</div>
@endsection