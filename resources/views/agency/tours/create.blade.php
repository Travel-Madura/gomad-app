@extends('layouts.agency')

@section('title', 'Buat Paket Tour')
@section('content')

<div class="max-w-4xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('agency.tours.index') }}" class="text-[#C1121F] hover:underline text-sm">← Kembali</a>
        <h1 class="text-2xl font-bold text-[#111111]">Buat Paket Tour Baru</h1>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-[12px] mb-6 text-sm">
        @foreach($errors->all() as $error)<p>• {{ $error }}</p>@endforeach
    </div>
    @endif

    <form action="{{ route('agency.tours.store') }}" method="POST" enctype="multipart/form-data" id="tourPackageForm">
        @csrf

        {{-- Informasi Dasar --}}
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-6 shadow-sm">
            <h2 class="font-bold text-lg text-[#111111] mb-4 border-b border-[#E5E5E5] pb-3">
                📋 Informasi Dasar Paket
            </h2>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">
                        Nama Paket <span class="text-[#C1121F]">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" 
                           class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition text-lg font-bold"
                           placeholder="Contoh: Paket Wisata Bromo 3 Hari 2 Malam" required>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">
                            Durasi (Hari) <span class="text-[#C1121F]">*</span>
                        </label>
                        <input type="number" name="duration_days" value="{{ old('duration_days', 1) }}" 
                               class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition"
                               min="1" max="30" required>
                    </div>
                    <div>
                        <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">
                            Durasi (Malam) <span class="text-[#C1121F]">*</span>
                        </label>
                        <input type="number" name="duration_nights" value="{{ old('duration_nights', 0) }}" 
                               class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition"
                               min="0" max="30" required>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">
                        Deskripsi Paket <span class="text-[#C1121F]">*</span>
                    </label>
                    <textarea name="description" rows="5" 
                              class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition"
                              placeholder="Deskripsikan paket tour Anda secara detail. Apa yang menarik? Apa saja yang akan dikunjungi?" required>{{ old('description') }}</textarea>
                    <p class="text-[10px] text-gray-400 mt-1 font-light">Minimal 50 karakter. Maksimal 5000 karakter.</p>
                </div>
            </div>
        </div>

        {{-- Cover & Gallery --}}
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-6 shadow-sm">
            <h2 class="font-bold text-lg text-[#111111] mb-4 border-b border-[#E5E5E5] pb-3">
                🖼️ Foto Paket
            </h2>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-2">
                        Foto Sampul (Cover)
                    </label>
                    <div class="flex items-center gap-4">
                        <div class="w-40 h-32 bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] flex items-center justify-center text-4xl overflow-hidden" id="coverPreview">
                            <span>🏝️</span>
                        </div>
                        <div class="flex-1">
                            <input type="file" name="cover_image" id="coverImageInput" accept="image/*"
                                   class="w-full text-sm" onchange="previewCover(event)">
                            <p class="text-[10px] text-gray-400 mt-1 font-light">
                                Format: JPG, PNG, WEBP. Max 5MB. Rekomendasi: 1200x800px
                            </p>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-2">
                        Galeri Foto (Opsional, max 10)
                    </label>
                    <input type="file" name="gallery_photos[]" accept="image/*" multiple
                           class="w-full text-sm">
                    <p class="text-[10px] text-gray-400 mt-1 font-light">
                        Bisa upload beberapa foto sekaligus. Max 2MB per foto.
                    </p>
                </div>
            </div>
        </div>

        {{-- Itinerary --}}
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-6 shadow-sm">
            <h2 class="font-bold text-lg text-[#111111] mb-4 border-b border-[#E5E5E5] pb-3">
                🗺️ Itinerary Perjalanan
            </h2>
            <p class="text-sm text-gray-500 font-light mb-4">
                Isi rencana perjalanan per hari. Klik <strong>Tambah Hari</strong> untuk menambah hari.
            </p>
            
            <div id="itineraryContainer" class="space-y-4">
                {{-- Diisi oleh JavaScript --}}
            </div>
            
            <button type="button" onclick="addItineraryDay()" 
                    class="mt-4 text-[#C1121F] text-sm font-medium hover:underline flex items-center gap-1">
                <span>+</span> Tambah Hari
            </button>
            
            <input type="hidden" name="itinerary" id="itineraryInput" value="{{ old('itinerary', '[]') }}">
        </div>

        {{-- Includes & Excludes --}}
        <div class="grid grid-cols-2 gap-6 mb-6">
            <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
                <h2 class="font-bold text-lg text-[#111111] mb-4 border-b border-[#E5E5E5] pb-3">
                    ✅ Sudah Termasuk
                </h2>
                <textarea name="includes" rows="6" 
                          class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition"
                          placeholder="Tulis satu per baris:&#10;Transportasi PP&#10;Makan 3x sehari&#10;Tiket masuk wisata&#10;Guide lokal&#10;Dokumentasi">{{ old('includes') }}</textarea>
                <p class="text-[10px] text-gray-400 mt-1 font-light">Satu item per baris</p>
            </div>
            
            <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
                <h2 class="font-bold text-lg text-[#111111] mb-4 border-b border-[#E5E5E5] pb-3">
                    ❌ Tidak Termasuk
                </h2>
                <textarea name="excludes" rows="6" 
                          class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition"
                          placeholder="Tulis satu per baris:&#10;Pengeluaran pribadi&#10;Asuransi perjalanan&#10;Tips guide">{{ old('excludes') }}</textarea>
                <p class="text-[10px] text-gray-400 mt-1 font-light">Satu item per baris</p>
            </div>
        </div>

        {{-- Route Stops --}}
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-6 shadow-sm">
            <div class="flex justify-between items-center mb-4 border-b border-[#E5E5E5] pb-3">
                <h2 class="font-bold text-lg text-[#111111]">🛑 Titik Pemberhentian (Stops)</h2>
                <span class="text-xs text-gray-400 font-mono uppercase tracking-wider">Minimal 2</span>
            </div>
            
            <p class="text-sm text-gray-500 font-light mb-4">
                Tentukan kota-kota yang menjadi titik penjemputan dan pemberhentian selama tour.
                Stop pertama = titik penjemputan utama. Stop terakhir = titik penurunan.
            </p>
            
            <div id="stopsContainer" class="space-y-3">
                @if(old('stops'))
                    @foreach(old('stops') as $idx => $stop)
                    <div class="stop-item flex flex-col sm:flex-row gap-3 items-start sm:items-center bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-4">
                        <div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-3 w-full">
                            <input type="text" name="stops[{{ $idx }}][city_name]" value="{{ $stop['city_name'] ?? '' }}" 
                                   placeholder="Nama Kota" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]" required>
                            <input type="time" name="stops[{{ $idx }}][estimated_arrival]" value="{{ $stop['estimated_arrival'] ?? '' }}" 
                                   placeholder="Estimasi Tiba" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]">
                            <input type="text" name="stops[{{ $idx }}][notes]" value="{{ $stop['notes'] ?? '' }}" 
                                   placeholder="Catatan (opsional)" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]">
                        </div>
                        <button type="button" onclick="this.closest('.stop-item').remove(); updateStopOrder();" 
                                class="text-[#C1121F] hover:bg-red-50 px-3 py-2 rounded-[12px] text-sm flex-shrink-0">
                            ✕ Hapus
                        </button>
                    </div>
                    @endforeach
                @else
                    <div class="stop-item flex flex-col sm:flex-row gap-3 items-start sm:items-center bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-4">
                        <div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-3 w-full">
                            <input type="text" name="stops[0][city_name]" placeholder="Nama Kota (contoh: Sumenep)" 
                                   class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]" required>
                            <input type="time" name="stops[0][estimated_arrival]" placeholder="Estimasi Tiba" 
                                   class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]">
                            <input type="text" name="stops[0][notes]" placeholder="Catatan (opsional)" 
                                   class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]">
                        </div>
                        <span class="text-[10px] font-mono uppercase tracking-wider text-green-600 bg-green-50 px-2 py-1 rounded-full border border-green-200 flex-shrink-0">
                            Titik Jemput
                        </span>
                    </div>
                    <div class="stop-item flex flex-col sm:flex-row gap-3 items-start sm:items-center bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-4">
                        <div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-3 w-full">
                            <input type="text" name="stops[1][city_name]" placeholder="Nama Kota (contoh: Bromo)" 
                                   class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]" required>
                            <input type="time" name="stops[1][estimated_arrival]" placeholder="Estimasi Tiba" 
                                   class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]">
                            <input type="text" name="stops[1][notes]" placeholder="Catatan (opsional)" 
                                   class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]">
                        </div>
                        <span class="text-[10px] font-mono uppercase tracking-wider text-red-600 bg-red-50 px-2 py-1 rounded-full border border-red-200 flex-shrink-0">
                            Titik Turun
                        </span>
                    </div>
                @endif
            </div>
            
            <button type="button" onclick="addStop()" 
                    class="mt-4 text-[#C1121F] text-sm font-medium hover:underline flex items-center gap-1">
                <span>+</span> Tambah Stop
            </button>
        </div>

        {{-- Submit --}}
        <div class="flex gap-4">
            <button type="submit" class="btn-gomad-primary px-8 py-3 rounded-[12px] font-semibold text-lg">
                💾 SIMPAN PAKET TOUR
            </button>
            <a href="{{ route('agency.tours.index') }}" 
               class="border border-[#E5E5E5] text-gray-700 px-8 py-3 rounded-[12px] font-semibold text-lg hover:bg-[#F5F5F5] transition">
                Batal
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
// Preview cover image
function previewCover(event) {
    var file = event.target.files[0];
    if (file) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('coverPreview').innerHTML = 
                '<img src="' + e.target.result + '" class="w-full h-full object-cover">';
        };
        reader.readAsDataURL(file);
    }
}

// Itinerary
var itineraryDays = @json(old('itinerary') ? json_decode(old('itinerary'), true) : []);
var daysCount = itineraryDays.length > 0 ? itineraryDays.length : 
    (parseInt('{{ old('duration_days', 1) }}') || 1);

function initItinerary() {
    var container = document.getElementById('itineraryContainer');
    container.innerHTML = '';
    
    if (itineraryDays.length === 0) {
        for (var i = 1; i <= daysCount; i++) {
            itineraryDays.push({ day: i, title: '', activities: [], meals: [] });
        }
    }
    
    itineraryDays.forEach(function(day, idx) {
        addItineraryDayElement(day, idx);
    });
    
    updateItineraryInput();
}

function addItineraryDay() {
    var newDay = itineraryDays.length + 1;
    itineraryDays.push({ day: newDay, title: '', activities: [], meals: [] });
    addItineraryDayElement(itineraryDays[itineraryDays.length - 1], itineraryDays.length - 1);
    updateItineraryInput();
}

function addItineraryDayElement(day, idx) {
    var container = document.getElementById('itineraryContainer');
    var div = document.createElement('div');
    div.className = 'bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-4';
    div.innerHTML = 
        '<div class="flex justify-between items-center mb-3">' +
            '<h4 class="font-bold text-[#111111]">📅 Hari ke-' + day.day + '</h4>' +
            (idx >= 1 ? '<button type="button" onclick="removeItineraryDay(' + idx + ')" class="text-[#C1121F] text-sm hover:underline">Hapus</button>' : '') +
        '</div>' +
        '<div class="space-y-3">' +
            '<input type="text" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-white rounded-lg px-3 text-sm" ' +
                'placeholder="Judul hari (contoh: Perjalanan ke Bromo)" value="' + (day.title || '') + '" ' +
                'onchange="updateItineraryField(' + idx + ', \'title\', this.value)">' +
            '<textarea class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-white rounded-lg px-3 text-sm" rows="3" ' +
                'placeholder="Aktivitas (satu per baris)" onchange="updateItineraryField(' + idx + ', \'activities_text\', this.value)">' + 
                (day.activities ? day.activities.join('\n') : '') + '</textarea>' +
            '<input type="text" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-white rounded-lg px-3 text-sm" ' +
                'placeholder="Makan (contoh: Sarapan, Makan Siang, Makan Malam)" value="' + (day.meals ? day.meals.join(', ') : '') + '" ' +
                'onchange="updateItineraryField(' + idx + ', \'meals_text\', this.value)">' +
        '</div>';
    container.appendChild(div);
}

function updateItineraryField(idx, field, value) {
    if (field === 'activities_text') {
        itineraryDays[idx].activities = value.split('\n').filter(function(v) { return v.trim() !== ''; });
    } else if (field === 'meals_text') {
        itineraryDays[idx].meals = value.split(',').map(function(v) { return v.trim(); }).filter(function(v) { return v !== ''; });
    } else {
        itineraryDays[idx][field] = value;
    }
    updateItineraryInput();
}

function removeItineraryDay(idx) {
    itineraryDays.splice(idx, 1);
    itineraryDays.forEach(function(day, i) { day.day = i + 1; });
    initItinerary();
}

function updateItineraryInput() {
    document.getElementById('itineraryInput').value = JSON.stringify(itineraryDays);
}

// Stops
var stopCount = document.querySelectorAll('#stopsContainer .stop-item').length;

function addStop() {
    var container = document.getElementById('stopsContainer');
    var div = document.createElement('div');
    div.className = 'stop-item flex flex-col sm:flex-row gap-3 items-start sm:items-center bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-4';
    div.innerHTML = 
        '<div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-3 w-full">' +
            '<input type="text" name="stops[' + stopCount + '][city_name]" placeholder="Nama Kota" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]" required>' +
            '<input type="time" name="stops[' + stopCount + '][estimated_arrival]" placeholder="Estimasi Tiba" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]">' +
            '<input type="text" name="stops[' + stopCount + '][notes]" placeholder="Catatan (opsional)" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]">' +
        '</div>' +
        '<button type="button" onclick="this.closest(\'.stop-item\').remove(); updateStopOrder();" class="text-[#C1121F] hover:bg-red-50 px-3 py-2 rounded-[12px] text-sm flex-shrink-0">✕ Hapus</button>';
    container.appendChild(div);
    stopCount++;
    updateStopOrder();
}

function updateStopOrder() {
    var items = document.querySelectorAll('#stopsContainer .stop-item');
    items.forEach(function(item, index) {
        var inputs = item.querySelectorAll('input');
        inputs.forEach(function(input) {
            var name = input.getAttribute('name');
            if (name) {
                input.setAttribute('name', name.replace(/stops\[\d+\]/, 'stops[' + index + ']'));
            }
        });
        
        // Update badge
        var badge = item.querySelector('span.bg-green-50, span.bg-red-50, span.bg-blue-50');
        if (badge) {
            if (index === 0) {
                badge.className = 'text-[10px] font-mono uppercase tracking-wider text-green-600 bg-green-50 px-2 py-1 rounded-full border border-green-200 flex-shrink-0';
                badge.textContent = 'Titik Jemput';
            } else if (index === items.length - 1) {
                badge.className = 'text-[10px] font-mono uppercase tracking-wider text-red-600 bg-red-50 px-2 py-1 rounded-full border border-red-200 flex-shrink-0';
                badge.textContent = 'Titik Turun';
            } else {
                badge.className = 'text-[10px] font-mono uppercase tracking-wider text-blue-600 bg-blue-50 px-2 py-1 rounded-full border border-blue-200 flex-shrink-0';
                badge.textContent = 'Stop ' + (index + 1);
            }
        }
    });
    stopCount = items.length;
}

// Init
document.addEventListener('DOMContentLoaded', function() {
    initItinerary();
});
</script>
@endpush
@endsection