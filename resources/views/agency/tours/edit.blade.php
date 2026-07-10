@extends('layouts.agency')

@section('title', 'Edit Paket Tour')
@section('content')

<div class="max-w-4xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('agency.tours.show', $package) }}" class="text-[#C1121F] hover:underline text-sm">← Kembali</a>
        <h1 class="text-2xl font-bold text-[#111111]">Edit Paket Tour</h1>
    </div>

    <form action="{{ route('agency.tours.update', $package) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')

        {{-- Informasi Dasar --}}
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-6 shadow-sm">
            <h2 class="font-bold text-lg text-[#111111] mb-4 border-b border-[#E5E5E5] pb-3">📋 Informasi Dasar</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Nama Paket</label>
                    <input type="text" name="name" value="{{ old('name', $package->name) }}" 
                           class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition text-lg font-bold" required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Durasi (Hari)</label>
                        <input type="number" name="duration_days" value="{{ old('duration_days', $package->duration_days) }}" 
                               class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" min="1" required>
                    </div>
                    <div>
                        <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Durasi (Malam)</label>
                        <input type="number" name="duration_nights" value="{{ old('duration_nights', $package->duration_nights) }}" 
                               class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" min="0" required>
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Deskripsi</label>
                    <textarea name="description" rows="5" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" required>{{ old('description', $package->description) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Cover --}}
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-6 shadow-sm">
            <h2 class="font-bold text-lg text-[#111111] mb-4 border-b border-[#E5E5E5] pb-3">🖼️ Foto Sampul</h2>
            <div class="flex items-center gap-4">
                <div class="w-40 h-32 bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] flex items-center justify-center overflow-hidden" id="coverPreview">
                    @if($package->cover_image)
                    <img src="{{ $package->cover_image }}" alt="Cover" class="w-full h-full object-cover">
                    @else
                    <span class="text-4xl">🏝️</span>
                    @endif
                </div>
                <div class="flex-1">
                    <input type="file" name="cover_image" id="coverImageInput" accept="image/*" class="w-full text-sm" onchange="previewCover(event)">
                    <p class="text-[10px] text-gray-400 mt-1 font-light">Biarkan kosong jika tidak ingin mengubah</p>
                </div>
            </div>
        </div>

        {{-- Includes & Excludes --}}
        <div class="grid grid-cols-2 gap-6 mb-6">
            <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
                <h2 class="font-bold text-lg text-[#111111] mb-4 border-b border-[#E5E5E5] pb-3">✅ Sudah Termasuk</h2>
                <textarea name="includes" rows="6" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition">{{ old('includes', is_array($package->includes) ? implode("\n", $package->includes) : '') }}</textarea>
            </div>
            <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
                <h2 class="font-bold text-lg text-[#111111] mb-4 border-b border-[#E5E5E5] pb-3">❌ Tidak Termasuk</h2>
                <textarea name="excludes" rows="6" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition">{{ old('excludes', is_array($package->excludes) ? implode("\n", $package->excludes) : '') }}</textarea>
            </div>
        </div>

        {{-- Stops --}}
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-6 shadow-sm">
            <div class="flex justify-between items-center mb-4 border-b border-[#E5E5E5] pb-3">
                <h2 class="font-bold text-lg text-[#111111]">🛑 Titik Pemberhentian</h2>
                <span class="text-xs text-gray-400 font-mono uppercase tracking-wider">Minimal 2</span>
            </div>
            <div id="stopsContainer" class="space-y-3">
                @foreach($package->stops as $idx => $stop)
                <div class="stop-item flex flex-col sm:flex-row gap-3 items-start sm:items-center bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-4">
                    <div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-3 w-full">
                        <input type="text" name="stops[{{ $idx }}][city_name]" value="{{ old('stops.'.$idx.'.city_name', $stop->city_name) }}" 
                               placeholder="Nama Kota" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]" required>
                        <input type="time" name="stops[{{ $idx }}][estimated_arrival]" value="{{ old('stops.'.$idx.'.estimated_arrival', $stop->estimated_arrival) }}" 
                               class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]">
                        <input type="text" name="stops[{{ $idx }}][notes]" value="{{ old('stops.'.$idx.'.notes', $stop->notes) }}" 
                               placeholder="Catatan" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]">
                    </div>
                    @if(!$loop->first && !$loop->last)
                    <button type="button" onclick="this.closest('.stop-item').remove(); updateStopOrder();" 
                            class="text-[#C1121F] hover:bg-red-50 px-3 py-2 rounded-[12px] text-sm">✕</button>
                    @endif
                </div>
                @endforeach
            </div>
            <button type="button" onclick="addStop()" class="mt-4 text-[#C1121F] text-sm font-medium hover:underline">+ Tambah Stop</button>
        </div>

        <div class="flex gap-4">
            <button type="submit" class="btn-gomad-primary px-8 py-3 rounded-[12px] font-semibold text-lg">💾 UPDATE</button>
            <a href="{{ route('agency.tours.show', $package) }}" class="border border-[#E5E5E5] text-gray-700 px-8 py-3 rounded-[12px] font-semibold text-lg hover:bg-[#F5F5F5] transition">Batal</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
function previewCover(event) {
    var file = event.target.files[0];
    if (file) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('coverPreview').innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-cover">';
        };
        reader.readAsDataURL(file);
    }
}

var stopCount = {{ $package->stops->count() }};
function addStop() {
    var container = document.getElementById('stopsContainer');
    var div = document.createElement('div');
    div.className = 'stop-item flex flex-col sm:flex-row gap-3 items-start sm:items-center bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-4';
    div.innerHTML = 
        '<div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-3 w-full">' +
            '<input type="text" name="stops[' + stopCount + '][city_name]" placeholder="Nama Kota" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]" required>' +
            '<input type="time" name="stops[' + stopCount + '][estimated_arrival]" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]">' +
            '<input type="text" name="stops[' + stopCount + '][notes]" placeholder="Catatan" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]">' +
        '</div>' +
        '<button type="button" onclick="this.closest(\'.stop-item\').remove(); updateStopOrder();" class="text-[#C1121F] hover:bg-red-50 px-3 py-2 rounded-[12px] text-sm">✕</button>';
    container.appendChild(div);
    stopCount++;
    updateStopOrder();
}
function updateStopOrder() {
    var items = document.querySelectorAll('#stopsContainer .stop-item');
    items.forEach(function(item, index) {
        item.querySelectorAll('input').forEach(function(input) {
            var name = input.getAttribute('name');
            if (name) input.setAttribute('name', name.replace(/stops\[\d+\]/, 'stops[' + index + ']'));
        });
    });
    stopCount = items.length;
}
</script>
@endpush
@endsection