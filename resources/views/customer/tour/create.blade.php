@extends('layouts.customer')

@section('title', 'Booking Tour - ' . $package->name)
@section('content')

@php
    $user = auth()->user();
    $tourPromoService = app(\App\Services\TourPromoService::class);
    $availablePromos = $tourPromoService->getAvailablePromosForCustomer($user, $schedule->id);
@endphp

<div class="max-w-4xl mx-auto px-4 py-8" id="tourBookingApp">
    {{-- Breadcrumb --}}
    <a href="{{ route('customer.tour.show', $package) }}" class="text-[#C1121F] text-sm mb-4 inline-block hover:underline">← Kembali</a>

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-[#111111] mb-2">Booking Tour</h1>
        <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-4 text-sm">
            <p><strong class="text-[#111111]">🏝️ Paket:</strong> {{ $package->name }}</p>
            <p><strong class="text-[#111111]">📅 Tanggal:</strong> {{ $schedule->departure_date->format('d M Y') }} {{ $schedule->departure_time }}</p>
            <p><strong class="text-[#111111]">🚐 Kendaraan:</strong> {{ $schedule->vehicle->plate_number ?? '-' }} ({{ $schedule->vehicle->brand ?? '' }} {{ $schedule->vehicle->model ?? '' }})</p>
            <p><strong class="text-[#111111]">💰 Harga Dewasa:</strong> Rp {{ number_format($schedule->base_price, 0, ',', '.') }} 
               @if($schedule->child_price) | <strong>Anak:</strong> Rp {{ number_format($schedule->child_price, 0, ',', '.') }} @endif</p>
            @if($schedule->return_date)
            <p><strong class="text-[#111111]">↩️ Kembali:</strong> {{ $schedule->return_date->format('d M Y') }} {{ $schedule->return_time ?? '' }}</p>
            @endif
            <p><strong class="text-[#111111]">👥 Kapasitas:</strong> {{ $schedule->available_seats ?? $schedule->max_participants }} kursi tersedia dari {{ $schedule->max_participants }}</p>
        </div>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-[12px] mb-6 text-sm">
        @foreach($errors->all() as $error)<p>• {{ $error }}</p>@endforeach
    </div>
    @endif

    <form action="{{ route('customer.tour.booking.store') }}" method="POST" id="tourBookingForm">
        @csrf
        <input type="hidden" name="tour_schedule_id" value="{{ $schedule->id }}">
        <input type="hidden" name="pickup_latitude" id="fLatitude">
        <input type="hidden" name="pickup_longitude" id="fLongitude">

        {{-- Nama Rombongan --}}
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-6 shadow-sm">
            <h2 class="font-bold text-lg text-[#111111] mb-4">👥 Informasi Rombongan</h2>
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">
                    Nama Instansi/Lembaga/Rombongan <span class="text-[#C1121F]">*</span>
                </label>
                <input type="text" name="group_name" value="{{ old('group_name') }}" 
                       class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition text-lg font-semibold"
                       placeholder="Contoh: SMAN 1 Sumenep / Kantor Bupati / Keluarga H. Ahmad" required>
                <p class="text-[10px] text-gray-400 mt-1 font-light">Nama ini akan muncul di E-Ticket dan daftar peserta</p>
            </div>
        </div>

        {{-- Titik Penjemputan --}}
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-6 shadow-sm">
            <h2 class="font-bold text-lg text-[#111111] mb-4">📍 Titik Penjemputan</h2>
            
            <div class="mb-4">
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Kota Penjemputan <span class="text-[#C1121F]">*</span></label>
                <select name="origin_stop_id" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" required>
                    <option value="">Pilih Kota</option>
                    @foreach($pickupStops as $stop)
                    <option value="{{ $stop['id'] }}" {{ old('origin_stop_id') == $stop['id'] ? 'selected' : '' }}>{{ $stop['city_name'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-2">
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Alamat Lengkap Penjemputan <span class="text-[#C1121F]">*</span></label>
                <textarea name="pickup_address" rows="3" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" 
                    placeholder="Jl. Trunojoyo No. 10, RT/RW, Kelurahan, Kecamatan, Kabupaten" required>{{ old('pickup_address') }}</textarea>
            </div>

            <button type="button" onclick="getCurrentLocation()" 
                    class="text-sm text-[#C1121F] hover:text-[#111111] font-medium flex items-center gap-1 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Gunakan Lokasi Saat Ini
            </button>
            <p id="locationStatus" class="text-xs mt-1 hidden font-light"></p>
            
            <input type="url" name="pickup_maps_link" id="pickupMapsLink" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition mt-2" 
                   placeholder="Link Google Maps (terisi otomatis)" readonly>
        </div>

        {{-- Data Peserta --}}
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-6 shadow-sm">
            <div class="flex justify-between items-center mb-4">
                <h2 class="font-bold text-lg text-[#111111]">👥 Data Peserta</h2>
                <span class="text-xs text-gray-400 font-mono">Minimal 1 peserta</span>
            </div>
            
            <div id="participantsList" class="space-y-3">
                {{-- Diisi JavaScript --}}
            </div>
            
            <div class="flex gap-2 mt-4">
                <button type="button" onclick="addParticipant('adult')" 
                        class="text-sm border border-[#E5E5E5] text-[#111111] px-4 py-2 rounded-[12px] hover:bg-[#F5F5F5] transition font-medium">
                    + Tambah Dewasa
                </button>
                @if($schedule->child_price)
                <button type="button" onclick="addParticipant('child')" 
                        class="text-sm border border-[#E5E5E5] text-[#111111] px-4 py-2 rounded-[12px] hover:bg-[#F5F5F5] transition font-medium">
                    + Tambah Anak
                </button>
                @endif
            </div>
            
            <p class="text-[10px] text-gray-400 mt-2 font-light">Maksimal {{ $schedule->available_seats ?? $schedule->max_participants }} peserta</p>
        </div>

        {{-- Special Requests --}}
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-6 shadow-sm">
            <h2 class="font-bold text-lg text-[#111111] mb-4">📝 Permintaan Khusus</h2>
            <textarea name="special_requests" rows="3" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition"
                placeholder="Contoh: Mohon disediakan kursi roda, ada peserta lansia, vegetarian, dll.">{{ old('special_requests') }}</textarea>
            <p class="text-[10px] text-gray-400 mt-1 font-light">Opsional. Isi jika ada kebutuhan khusus.</p>
        </div>

        {{-- Submit --}}
        <button type="submit" id="submitBtn" class="w-full bg-[#E5E5E5] text-gray-500 py-4 rounded-[12px] font-bold text-lg cursor-not-allowed transition" disabled>
            💾 Buat Booking Tour
        </button>
    </form>
</div>

@push('scripts')
<script>
// ─── GLOBAL VARIABLES ────────────────────────────────
var participantCount = 0;
var adultPrice = {{ $schedule->base_price }};
var childPrice = {{ $schedule->child_price ?? $schedule->base_price }};
var serviceFee = {{ \App\Models\PlatformSetting::getValue('service_fee', 5000) }};
var platformFeePercent = {{ \App\Models\PlatformSetting::getValue('platform_fee_percent', 3) }};
var maxParticipants = {{ $schedule->available_seats ?? $schedule->max_participants }};

// ─── PARTICIPANTS ────────────────────────────────────

function addParticipant(type) {
    var list = document.getElementById('participantsList');
    var currentCount = list.querySelectorAll('.participant-item').length;
    
    if (currentCount >= maxParticipants) {
        alert('Maksimal ' + maxParticipants + ' peserta untuk jadwal ini.');
        return;
    }
    
    var div = document.createElement('div');
    div.className = 'participant-item bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-4';
    div.innerHTML = 
        '<div class="flex justify-between items-start mb-3">' +
            '<span class="text-[10px] font-mono uppercase tracking-wider ' + (type === 'adult' ? 'text-blue-600' : 'text-orange-600') + '">' +
                (type === 'adult' ? '👨 Dewasa' : '👶 Anak') + ' #' + (participantCount + 1) +
            '</span>' +
            '<button type="button" onclick="removeParticipant(this)" class="text-[#C1121F] text-xs hover:underline">Hapus</button>' +
        '</div>' +
        '<div class="grid grid-cols-2 gap-3">' +
            '<input type="text" name="participants[' + participantCount + '][name]" class="participant-name px-3 py-2 border border-[#E5E5E5] rounded-[8px] bg-white text-[#111111] text-sm" placeholder="Nama lengkap" required>' +
            '<input type="text" name="participants[' + participantCount + '][phone]" class="px-3 py-2 border border-[#E5E5E5] rounded-[8px] bg-white text-[#111111] text-sm" placeholder="No. HP">' +
            '<input type="text" name="participants[' + participantCount + '][id_number]" class="px-3 py-2 border border-[#E5E5E5] rounded-[8px] bg-white text-[#111111] text-sm" placeholder="No. KTP/Identitas">' +
            '<input type="hidden" name="participants[' + participantCount + '][participant_type]" value="' + type + '">' +
        '</div>';
    list.appendChild(div);
    participantCount++;
    updateParticipants();
    updateSubmitButton();
    updatePromoSelection(document.querySelector('input[name="promo_id"]:checked')?.value || '');
}

function removeParticipant(btn) {
    btn.closest('.participant-item').remove();
    updateParticipants();
    updateSubmitButton();
    updatePromoSelection(document.querySelector('input[name="promo_id"]:checked')?.value || '');
}

function updateParticipants() {
    var items = document.querySelectorAll('#participantsList .participant-item');
    items.forEach(function(item, index) {
        item.querySelectorAll('input').forEach(function(input) {
            var name = input.getAttribute('name');
            if (name) input.setAttribute('name', name.replace(/participants\[\d+\]/, 'participants[' + index + ']'));
        });
        var badge = item.querySelector('.font-mono.uppercase.tracking-wider');
        if (badge) {
            var typeInput = item.querySelector('input[value="adult"], input[value="child"]');
            var type = typeInput ? typeInput.value : 'adult';
            badge.textContent = (type === 'adult' ? '👨 Dewasa' : '👶 Anak') + ' #' + (index + 1);
        }
    });
    participantCount = items.length;
}

function updateSubmitButton() {
    var btn = document.getElementById('submitBtn');
    if (participantCount > 0) {
        btn.disabled = false;
        btn.className = 'w-full bg-[#C1121F] text-white py-4 rounded-[12px] font-bold text-lg hover:bg-[#8A0F18] cursor-pointer transition';
    } else {
        btn.disabled = true;
        btn.className = 'w-full bg-[#E5E5E5] text-gray-500 py-4 rounded-[12px] font-bold text-lg cursor-not-allowed transition';
    }
}

// ─── GEOLOCATION ─────────────────────────────────────

function getCurrentLocation() {
    if (!navigator.geolocation) return alert('Browser tidak mendukung geolocation.');
    var statusEl = document.getElementById('locationStatus');
    statusEl.textContent = '⏳ Mengambil lokasi...';
    statusEl.classList.remove('hidden');
    statusEl.className = 'text-xs mt-1 text-blue-600 font-light';

    navigator.geolocation.getCurrentPosition(
        function(position) {
            var lat = position.coords.latitude;
            var lng = position.coords.longitude;
            document.getElementById('fLatitude').value = lat;
            document.getElementById('fLongitude').value = lng;
            document.getElementById('pickupMapsLink').value = 'https://www.google.com/maps?q=' + lat + ',' + lng;
            
            fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + lat + '&lon=' + lng + '&accept-language=id')
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    var addressField = document.querySelector('textarea[name="pickup_address"]');
                    if (addressField) addressField.value = data.display_name || '';
                    statusEl.textContent = '✅ Lokasi berhasil: ' + lat.toFixed(6) + ', ' + lng.toFixed(6);
                    statusEl.className = 'text-xs mt-1 text-green-600 font-light';
                })
                .catch(function() {
                    statusEl.textContent = '⚠️ Koordinat didapat, isi alamat manual.';
                    statusEl.className = 'text-xs mt-1 text-yellow-600 font-light';
                });
        },
        function(error) {
            statusEl.textContent = '❌ Gagal mengambil lokasi. Isi manual.';
            statusEl.className = 'text-xs mt-1 text-red-600 font-light';
        }
    );
}

// ─── HELPERS ─────────────────────────────────────────

function formatRupiah(num) {
    return new Intl.NumberFormat('id-ID').format(num || 0);
}

// ─── INIT ────────────────────────────────────────────

document.addEventListener('DOMContentLoaded', function() {
    addParticipant('adult');
    
    document.getElementById('tourBookingForm').addEventListener('submit', function(e) {
        if (participantCount === 0) {
            e.preventDefault();
            alert('Tambahkan minimal 1 peserta!');
        }
    });
});
</script>
@endpush
@endsection