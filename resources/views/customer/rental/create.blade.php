@extends('layouts.customer')

@section('title', 'Form Sewa Kendaraan')
@section('content')

@php
    $user = auth()->user();
    
    // Kelompokkan available dates menjadi continuous ranges
    $dateRanges = [];
    $tempStart = null;
    $tempEnd = null;
    
    foreach ($availableDates as $i => $date) {
        $carbon = \Carbon\Carbon::parse($date);
        
        if ($tempStart === null) {
            $tempStart = $carbon;
            $tempEnd = $carbon;
        } elseif ($carbon->diffInDays($tempEnd) == 1) {
            $tempEnd = $carbon;
        } else {
            $dateRanges[] = ['start' => $tempStart, 'end' => $tempEnd];
            $tempStart = $carbon;
            $tempEnd = $carbon;
        }
    }
    
    if ($tempStart !== null) {
        $dateRanges[] = ['start' => $tempStart, 'end' => $tempEnd];
    }
    
    $allDatesJson = json_encode($availableDates);
@endphp

<div class="max-w-4xl mx-auto px-4 py-8">
    <a href="{{ route('customer.rental.index') }}" class="text-[#C1121F] text-sm mb-4 inline-block hover:underline">← Kembali</a>
    <h1 class="text-2xl font-bold text-[#111111] mb-2">Form Sewa Kendaraan</h1>
    <p class="text-gray-500 font-light mb-6">{{ $vehicle->plate_number }} — {{ $vehicle->brand }} {{ $vehicle->model }}</p>

    <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-4 mb-6 text-sm">
        <p><strong>🏢 Agency:</strong> {{ $vehicle->agency->agency_name }} ⭐ {{ number_format($vehicle->agency->rating, 1) }}</p>
        <p><strong>👥 Kapasitas:</strong> {{ $vehicle->capacity }} seat | <strong>Maks Sewa:</strong> {{ $vehicle->rental_max_passengers }} orang</p>
        <p><strong>💰 Harga/KM:</strong> Rp {{ number_format($vehicle->rental_price_per_km, 0, ',', '.') }} | <strong>Min:</strong> Rp {{ number_format($vehicle->rental_min_price, 0, ',', '.') }}/hari</p>
        <p><strong>👨‍✈️ Supir:</strong> {{ $vehicle->rental_include_driver ? 'Tersedia (+Rp '.number_format($vehicle->rental_driver_price_per_day,0,',','.').'/hari)' : 'Tidak termasuk' }}</p>
        <p><strong>📅 Tersedia:</strong> 
            @foreach($dateRanges as $range)
                <span class="bg-green-50 text-green-700 px-2 py-0.5 rounded-full text-xs font-mono border border-green-200">
                    {{ $range['start']->format('d M') }} - {{ $range['end']->format('d M Y') }}
                </span>
                @if(!$loop->last) <span class="text-gray-300 mx-1">|</span> @endif
            @endforeach
        </p>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-[12px] mb-6 text-sm">
        @foreach($errors->all() as $error)<p>• {{ $error }}</p>@endforeach
    </div>
    @endif

    <form action="{{ route('customer.rental.store') }}" method="POST" id="rentalForm">
        @csrf
        <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">
        <input type="hidden" name="pickup_latitude" id="fPickupLat">
        <input type="hidden" name="pickup_longitude" id="fPickupLng">
        <input type="hidden" name="destination_latitude" id="fDestLat">
        <input type="hidden" name="destination_longitude" id="fDestLng">
        <input type="hidden" name="estimated_distance_km" id="fDistance">

        {{-- Keperluan --}}
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-6 shadow-sm">
            <h2 class="font-bold text-lg text-[#111111] mb-4">📋 Informasi Sewa</h2>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Keperluan <span class="text-[#C1121F]">*</span></label>
                    <select name="purpose" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]" required>
                        <option value="">Pilih Keperluan</option>
                        <option value="Acara Keluarga" {{ old('purpose') == 'Acara Keluarga' ? 'selected' : '' }}>Acara Keluarga</option>
                        <option value="Dinas Kantor" {{ old('purpose') == 'Dinas Kantor' ? 'selected' : '' }}>Dinas Kantor</option>
                        <option value="Pernikahan" {{ old('purpose') == 'Pernikahan' ? 'selected' : '' }}>Pernikahan</option>
                        <option value="Wisata" {{ old('purpose') == 'Wisata' ? 'selected' : '' }}>Wisata</option>
                        <option value="Ziarah" {{ old('purpose') == 'Ziarah' ? 'selected' : '' }}>Ziarah</option>
                        <option value="Lainnya" {{ old('purpose') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Jumlah Penumpang <span class="text-[#C1121F]">*</span></label>
                    <input type="number" name="max_passengers" value="{{ old('max_passengers', 1) }}" 
                           class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]"
                           min="1" max="{{ $vehicle->rental_max_passengers }}" required>
                </div>
            </div>

            {{-- TANGGAL — DROPDOWN BERDASARKAN KETERSEDIAAN --}}
            <div class="grid md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Tanggal Mulai <span class="text-[#C1121F]">*</span></label>
                    <select name="start_date" id="startDate" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]" required onchange="updateEndDateOptions()">
                        <option value="">Pilih Tanggal</option>
                        @foreach($availableDates as $date)
                        <option value="{{ $date }}" {{ old('start_date') == $date ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d M Y') }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">
                        Tanggal Selesai <span class="text-[#C1121F]">*</span>
                        <span id="endDateLoading" class="hidden text-blue-500 font-light">⏳ memuat...</span>
                    </label>
                    <select name="end_date" id="endDate" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]" required disabled>
                        <option value="">Pilih Tanggal Mulai Dulu</option>
                    </select>
                    <p id="endDateInfo" class="text-[10px] text-gray-400 mt-1 font-light hidden"></p>
                </div>
            </div>

            {{-- Include Supir --}}
            @if($vehicle->rental_include_driver)
            <div class="mt-4">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="include_driver" value="1" checked class="w-5 h-5 text-[#C1121F] rounded">
                    <span class="text-sm font-medium text-[#111111]">👨‍✈️ Include Supir (+Rp {{ number_format($vehicle->rental_driver_price_per_day, 0, ',', '.') }}/hari)</span>
                </label>
            </div>
            @endif
        </div>

        {{-- Rute — DENGAN TOMBOL BUKA PETA --}}
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-6 shadow-sm">
            <h2 class="font-bold text-lg text-[#111111] mb-4">📍 Titik Jemput & Tujuan</h2>
            <div class="space-y-4">
                {{-- Titik Jemput --}}
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500">Alamat Penjemputan <span class="text-[#C1121F]">*</span></label>
                        <div class="flex gap-2">
                            <button type="button" onclick="getLocation('pickup')" class="text-xs text-[#C1121F] font-medium hover:underline">
                                📍 GPS Saat Ini
                            </button>
                            <button type="button" onclick="openOSMMap('pickup')" class="text-xs text-blue-600 font-medium hover:underline">
                                🗺️ Buka Peta
                            </button>
                        </div>
                    </div>
                    <textarea name="pickup_address" id="pickupAddress" rows="2" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]" 
                        placeholder="Jl. Trunojoyo No. 10, Sumenep" required>{{ old('pickup_address') }}</textarea>
                    <p id="pickupCoords" class="text-[10px] text-gray-400 mt-1 font-light hidden"></p>
                </div>

                {{-- Titik Tujuan --}}
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500">Alamat Tujuan <span class="text-[#C1121F]">*</span></label>
                        <div class="flex gap-2">
                            <button type="button" onclick="getLocation('dest')" class="text-xs text-[#C1121F] font-medium hover:underline">
                                📍 GPS Saat Ini
                            </button>
                            <button type="button" onclick="openOSMMap('dest')" class="text-xs text-blue-600 font-medium hover:underline">
                                🗺️ Buka Peta
                            </button>
                        </div>
                    </div>
                    <textarea name="destination_address" id="destAddress" rows="2" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]" 
                        placeholder="Jl. Ahmad Yani No. 12, Surabaya" required>{{ old('destination_address') }}</textarea>
                    <p id="destCoords" class="text-[10px] text-gray-400 mt-1 font-light hidden"></p>
                </div>

                {{-- Info Jarak --}}
                <div id="distanceInfo" class="bg-blue-50 border border-blue-200 rounded-[12px] p-3 text-sm hidden">
                    📏 Estimasi Jarak: <strong id="distanceText">-</strong> km
                    <button type="button" onclick="calculateDistance()" class="ml-2 text-[#C1121F] text-xs font-medium hover:underline">🔄 Hitung Ulang</button>
                </div>
            </div>
        </div>

        {{-- Catatan --}}
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-6 shadow-sm">
            <h2 class="font-bold text-lg text-[#111111] mb-4">📝 Catatan Khusus</h2>
            <textarea name="special_notes" rows="3" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]" 
                placeholder="Opsional: mohon disediakan kursi bayi, dll...">{{ old('special_notes') }}</textarea>
        </div>

        <button type="submit" class="w-full btn-gomad-primary py-4 rounded-[12px] font-bold text-lg">
            💾 BUAT BOOKING SEWA
        </button>
    </form>
</div>

{{-- MODAL PETA --}}
<div id="mapModal" style="display:none;" class="fixed inset-0 bg-[#111111]/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-[12px] shadow-2xl w-full max-w-3xl max-h-[90vh] border border-[#E5E5E5] flex flex-col">
        <div class="flex justify-between items-center p-4 border-b border-[#E5E5E5]">
            <h3 class="font-bold text-[#111111]">🗺️ Pilih Lokasi — <span id="mapModalTitle">Titik Jemput</span></h3>
            <button type="button" onclick="closeMapModal()" class="text-gray-400 hover:text-[#111111] text-2xl">&times;</button>
        </div>
        <div id="mapContainer" style="height: 450px;" class="flex-1"></div>
        <div class="p-4 border-t border-[#E5E5E5] flex gap-3">
            <div class="flex-1 bg-[#F5F5F5] rounded-[12px] p-3 text-sm" id="selectedAddress">
                <span class="text-gray-400 font-light">Klik pada peta untuk memilih lokasi</span>
            </div>
            <button type="button" onclick="confirmMapSelection()" class="bg-[#C1121F] text-white px-6 py-3 rounded-[12px] font-semibold hover:bg-[#8A0F18] transition">
                ✅ Pilih Lokasi Ini
            </button>
        </div>
    </div>
</div>

@push('styles')
<style>
    #mapContainer {
        width: 100% !important;
        min-height: 400px !important;
        background: #f5f5f5;
    }
    #mapModal > div {
        max-height: 90vh;
        overflow: hidden;
    }
    .leaflet-container {
        z-index: 1;
    }
</style>
@endpush

@push('scripts')
<script>
// ═══════════════════════════════════════════════════════
// VARIABLES
// ═══════════════════════════════════════════════════════
var allDates = @json($availableDates);
var mapType = 'pickup'; // 'pickup' or 'dest'
var selectedLat = null;
var selectedLng = null;
var mapInstance = null;
var mapMarker = null;

// ═══════════════════════════════════════════════════════
// TANGGAL — UPDATE END DATE OPTIONS
// ═══════════════════════════════════════════════════════

function updateEndDateOptions() {
    var startVal = document.getElementById('startDate').value;
    var endSelect = document.getElementById('endDate');
    var endInfo = document.getElementById('endDateInfo');
    var loadingEl = document.getElementById('endDateLoading');
    
    if (!startVal) {
        endSelect.innerHTML = '<option value="">Pilih Tanggal Mulai Dulu</option>';
        endSelect.disabled = true;
        endInfo.classList.add('hidden');
        return;
    }
    
    // Show loading
    endSelect.disabled = true;
    endSelect.innerHTML = '<option value="">⏳ Mencari tanggal tersedia...</option>';
    loadingEl.classList.remove('hidden');
    
    setTimeout(function() {
        // Cari tanggal-tanggal setelah start date yang available DAN berurutan
        var startIndex = allDates.indexOf(startVal);
        var validEndDates = [];
        
        if (startIndex >= 0) {
            // Mulai dari start date, cari yang berurutan
            var startDateObj = new Date(startVal);
            
            for (var i = startIndex; i < allDates.length; i++) {
                var currentDate = new Date(allDates[i]);
                
                if (i === startIndex) {
                    validEndDates.push(allDates[i]);
                    continue;
                }
                
                var prevDate = new Date(allDates[i - 1]);
                var diffDays = (currentDate - prevDate) / (1000 * 60 * 60 * 24);
                
                // Hanya tambahkan jika berurutan (selisih 1 hari)
                if (diffDays <= 2) { // toleransi 2 hari untuk jaga-jaga
                    validEndDates.push(allDates[i]);
                } else {
                    break; // Stop jika ada gap (ada jadwal lain)
                }
            }
        }
        
        // Populate end date select
        endSelect.innerHTML = '<option value="">Pilih Tanggal Selesai</option>';
        
        if (validEndDates.length > 0) {
            validEndDates.forEach(function(date) {
                var dateObj = new Date(date);
                var dayName = dateObj.toLocaleDateString('id-ID', { weekday: 'long' });
                var formatted = dateObj.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
                var diffFromStart = Math.floor((dateObj - new Date(startVal)) / (1000 * 60 * 60 * 24)) + 1;
                
                endSelect.innerHTML += '<option value="' + date + '">' + dayName + ', ' + formatted + ' (' + diffFromStart + ' hari)</option>';
            });
            
            endInfo.textContent = 'Maksimal ' + validEndDates.length + ' hari tersedia berturut-turut';
            endInfo.classList.remove('hidden');
        } else {
            endInfo.textContent = 'Tidak ada tanggal tersedia setelah ini';
            endInfo.classList.remove('hidden');
        }
        
        endSelect.disabled = false;
        loadingEl.classList.add('hidden');
        
        // Auto-select end date = start date (1 hari)
        if (validEndDates.length > 0) {
            endSelect.value = startVal;
        }
    }, 100);
}

// ═══════════════════════════════════════════════════════
// GPS — LOKASI SAAT INI
// ═══════════════════════════════════════════════════════

function getLocation(type) {
    if (!navigator.geolocation) return alert('Geolocation tidak didukung browser.');
    
    navigator.geolocation.getCurrentPosition(function(pos) {
        var lat = pos.coords.latitude;
        var lng = pos.coords.longitude;
        
        setCoords(type, lat, lng);
        reverseGeocode(lat, lng, type);
    }, function(err) {
        alert('Gagal mengambil lokasi. Izinkan akses GPS di pengaturan browser.');
    });
}

function setCoords(type, lat, lng) {
    if (type === 'pickup') {
        document.getElementById('fPickupLat').value = lat;
        document.getElementById('fPickupLng').value = lng;
        document.getElementById('pickupCoords').textContent = '📍 ' + lat.toFixed(6) + ', ' + lng.toFixed(6);
        document.getElementById('pickupCoords').classList.remove('hidden');
    } else {
        document.getElementById('fDestLat').value = lat;
        document.getElementById('fDestLng').value = lng;
        document.getElementById('destCoords').textContent = '📍 ' + lat.toFixed(6) + ', ' + lng.toFixed(6);
        document.getElementById('destCoords').classList.remove('hidden');
    }
}

function reverseGeocode(lat, lng, type) {
    var fieldId = type === 'pickup' ? 'pickupAddress' : 'destAddress';
    var coordsId = type === 'pickup' ? 'pickupCoords' : 'destCoords';
    
    document.getElementById(coordsId).textContent = '⏳ Mencari alamat...';
    document.getElementById(coordsId).classList.remove('hidden');
    
    fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + lat + '&lon=' + lng + '&accept-language=id')
        .then(function(r) { return r.json(); })
        .then(function(data) {
            document.getElementById(fieldId).value = data.display_name || '';
            document.getElementById(coordsId).textContent = '📍 ' + lat.toFixed(6) + ', ' + lng.toFixed(6);
            calculateDistance();
        })
        .catch(function() {
            document.getElementById(coordsId).textContent = '⚠️ Koordinat: ' + lat.toFixed(6) + ', ' + lng.toFixed(6) + ' (isi alamat manual)';
        });
}

// ═══════════════════════════════════════════════════════
// OPENSTREETMAP — MODAL PETA
// ═══════════════════════════════════════════════════════

// ═══════════════════════════════════════════════════════
// OPENSTREETMAP — MODAL PETA (FIXED)
// ═══════════════════════════════════════════════════════

function openOSMMap(type) {
    mapType = type;
    document.getElementById('mapModalTitle').textContent = type === 'pickup' ? 'Titik Jemput' : 'Titik Tujuan';
    document.getElementById('mapModal').style.display = 'flex';
    document.getElementById('selectedAddress').innerHTML = '<span class="text-gray-400 font-light">Klik pada peta untuk memilih lokasi</span>';
    
    // Default center: Sumenep
    var defaultLat = -7.0089;
    var defaultLng = 113.8586;
    
    // Cek apakah sudah ada koordinat sebelumnya
    if (type === 'pickup') {
        var existLat = document.getElementById('fPickupLat').value;
        var existLng = document.getElementById('fPickupLng').value;
        if (existLat && existLng) {
            defaultLat = parseFloat(existLat);
            defaultLng = parseFloat(existLng);
        }
    } else {
        var existLat = document.getElementById('fDestLat').value;
        var existLng = document.getElementById('fDestLng').value;
        if (existLat && existLng) {
            defaultLat = parseFloat(existLat);
            defaultLng = parseFloat(existLng);
        }
    }
    
    selectedLat = defaultLat;
    selectedLng = defaultLng;
    
    // Hapus map lama jika ada
    if (mapInstance) {
        mapInstance.remove();
        mapInstance = null;
    }
    
    // Init map — delay untuk pastikan container sudah visible
    setTimeout(function() {
        var mapContainer = document.getElementById('mapContainer');
        
        // Pastikan container punya height
        mapContainer.style.height = '450px';
        mapContainer.style.width = '100%';
        
        mapInstance = L.map('mapContainer', {
            center: [defaultLat, defaultLng],
            zoom: 13,
            zoomControl: true,
        });
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            maxZoom: 19,
        }).addTo(mapInstance);
        
        // Custom red marker
        var customIcon = L.divIcon({
            html: '<div style="background:#C1121F;color:white;width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:20px;border:3px solid white;box-shadow:0 3px 12px rgba(0,0,0,0.4);">📍</div>',
            className: '',
            iconSize: [40, 40],
            iconAnchor: [20, 40],
            popupAnchor: [0, -40],
        });
        
        // Tambah marker
        mapMarker = L.marker([defaultLat, defaultLng], { 
            draggable: true,
            icon: customIcon 
        }).addTo(mapInstance);
        
        // Bind popup
        mapMarker.bindPopup('<b>Geser marker</b><br>atau klik di peta untuk pindah').openPopup();
        
        // Klik pada peta = pindah marker
        mapInstance.on('click', function(e) {
            var lat = e.latlng.lat;
            var lng = e.latlng.lng;
            
            mapMarker.setLatLng([lat, lng]);
            selectedLat = lat;
            selectedLng = lng;
            
            reverseGeocodeForModal(lat, lng);
        });
        
        // Drag marker
        mapMarker.on('dragend', function(e) {
            var pos = mapMarker.getLatLng();
            selectedLat = pos.lat;
            selectedLng = pos.lng;
            reverseGeocodeForModal(pos.lat, pos.lng);
        });
        
        // Invalidate size setelah render
        setTimeout(function() {
            mapInstance.invalidateSize();
        }, 100);
        
        // Reverse geocode lokasi default
        reverseGeocodeForModal(defaultLat, defaultLng);
        
    }, 400); // delay 400ms
}

function reverseGeocodeForModal(lat, lng) {
    document.getElementById('selectedAddress').innerHTML = 
        '<span class="text-gray-400">⏳ Mencari alamat...</span>' +
        '<br><span class="text-xs text-gray-400 font-mono">' + lat.toFixed(6) + ', ' + lng.toFixed(6) + '</span>';
    
    fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + lat + '&lon=' + lng + '&accept-language=id')
        .then(function(r) { return r.json(); })
        .then(function(data) {
            var address = data.display_name || 'Lokasi dipilih';
            document.getElementById('selectedAddress').innerHTML = 
                '<strong class="text-[#111111]">📍 ' + address + '</strong>' +
                '<br><span class="text-xs text-gray-400 font-mono">' + lat.toFixed(6) + ', ' + lng.toFixed(6) + '</span>';
        })
        .catch(function() {
            document.getElementById('selectedAddress').innerHTML = 
                '<strong class="text-[#111111]">📍 Lokasi dipilih</strong>' +
                '<br><span class="text-xs text-gray-400 font-mono">' + lat.toFixed(6) + ', ' + lng.toFixed(6) + '</span>';
        });
}

function confirmMapSelection() {
    if (!selectedLat || !selectedLng) {
        if (mapMarker) {
            var pos = mapMarker.getLatLng();
            selectedLat = pos.lat;
            selectedLng = pos.lng;
        } else {
            alert('Silakan klik pada peta untuk memilih lokasi.');
            return;
        }
    }
    
    setCoords(mapType, selectedLat, selectedLng);
    reverseGeocode(selectedLat, selectedLng, mapType);
    closeMapModal();
}

function closeMapModal() {
    document.getElementById('mapModal').style.display = 'none';
    if (mapInstance) {
        mapInstance.remove();
        mapInstance = null;
    }
    mapMarker = null;
}

// ═══════════════════════════════════════════════════════
// DISTANCE CALCULATION (OSRM)
// ═══════════════════════════════════════════════════════

function calculateDistance() {
    var pLat = parseFloat(document.getElementById('fPickupLat').value);
    var pLng = parseFloat(document.getElementById('fPickupLng').value);
    var dLat = parseFloat(document.getElementById('fDestLat').value);
    var dLng = parseFloat(document.getElementById('fDestLng').value);
    
    if (isNaN(pLat) || isNaN(dLat)) {
        document.getElementById('distanceInfo').classList.add('hidden');
        return;
    }
    
    document.getElementById('distanceText').textContent = '...';
    document.getElementById('distanceInfo').classList.remove('hidden');
    
    var url = 'https://router.project-osrm.org/route/v1/driving/' + pLng + ',' + pLat + ';' + dLng + ',' + dLat + '?overview=false';
    
    fetch(url)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.routes && data.routes[0]) {
                var km = ((data.routes[0].distance || 0) / 1000).toFixed(1);
                document.getElementById('fDistance').value = km;
                document.getElementById('distanceText').textContent = km;
            }
        })
        .catch(function() {
            // Haversine fallback
            var R = 6371;
            var dLat2 = (dLat - pLat) * Math.PI / 180;
            var dLng2 = (dLng - pLng) * Math.PI / 180;
            var a = Math.sin(dLat2/2) * Math.sin(dLat2/2) + 
                    Math.cos(pLat * Math.PI/180) * Math.cos(dLat * Math.PI/180) * 
                    Math.sin(dLng2/2) * Math.sin(dLng2/2);
            var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            var km = (R * c).toFixed(1);
            document.getElementById('fDistance').value = km;
            document.getElementById('distanceText').textContent = km + ' (estimasi)';
        });
}

// ═══════════════════════════════════════════════════════
// INIT
// ═══════════════════════════════════════════════════════

document.addEventListener('DOMContentLoaded', function() {
    // Jika ada old('start_date'), trigger update end date
    @if(old('start_date'))
    setTimeout(function() {
        updateEndDateOptions();
    }, 500);
    @endif
    
    // Jika ada old koordinat, hitung jarak
    @if(old('pickup_latitude') && old('destination_latitude'))
    setTimeout(function() {
        calculateDistance();
    }, 1000);
    @endif
    
    // Close modal on background click
    document.getElementById('mapModal').addEventListener('click', function(e) {
        if (e.target === this) closeMapModal();
    });
    
    // ESC to close modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeMapModal();
    });
});
</script>
@endpush
@endsections