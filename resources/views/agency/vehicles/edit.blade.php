@extends('layouts.agency')

@section('title', 'Edit Kendaraan')
@section('content')
<div>
    <h1 class="text-2xl font-bold text-[#111111] mb-6">Edit Kendaraan</h1>

    <form action="{{ route('agency.vehicles.update', $vehicle) }}" method="POST" enctype="multipart/form-data" class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm space-y-4">
        @csrf @method('PUT')
        
        <div>
            <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Foto Kendaraan</label>
            <div class="flex items-center gap-4">
                <div class="w-32 h-24 rounded-[12px] flex items-center justify-center text-3xl overflow-hidden border border-[#E5E5E5]" id="previewContainer">
                    @if($vehicle->vehicle_image)
                    <img src="{{ $vehicle->vehicle_image }}" class="w-full h-full object-cover">
                    @else
                    <div class="w-full h-full bg-[#F5F5F5] flex items-center justify-center">🚐</div>
                    @endif
                </div>
                <div class="flex-1">
                    <input type="file" name="vehicle_image" id="vehicleImage" accept="image/*" 
                           class="w-full text-sm" onchange="previewImage(event)">
                    <p class="text-[10px] text-gray-400 mt-1 font-light">Biarkan kosong jika tidak ingin mengubah foto</p>
                </div>
            </div>
        </div>

        <div>
            <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Nomor Plat <span class="text-[#C1121F]">*</span></label>
            <input type="text" name="plate_number" value="{{ old('plate_number', $vehicle->plate_number) }}" 
                   class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" required>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Merk <span class="text-[#C1121F]">*</span></label>
                <input type="text" name="brand" value="{{ old('brand', $vehicle->brand) }}" 
                       class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" required>
            </div>
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Model <span class="text-[#C1121F]">*</span></label>
                <input type="text" name="model" value="{{ old('model', $vehicle->model) }}" 
                       class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" required>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Tahun</label>
                <input type="number" name="year" value="{{ old('year', $vehicle->year) }}" 
                       class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" min="2000">
            </div>
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Kapasitas <span class="text-[#C1121F]">*</span></label>
                <input type="number" name="capacity" value="{{ old('capacity', $vehicle->capacity) }}" 
                       class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" min="4" max="20" required>
            </div>
        </div>

        <div>
            <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Tipe <span class="text-[#C1121F]">*</span></label>
            <select name="type" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" required>
                <option value="economy" {{ $vehicle->type == 'economy' ? 'selected' : '' }}>Ekonomi</option>
                <option value="premium" {{ $vehicle->type == 'premium' ? 'selected' : '' }}>Premium</option>
            </select>
        </div>

        {{-- STATUS KENDARAAN --}}
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-6 shadow-sm">
            <h3 class="font-mono uppercase tracking-wider text-xs font-bold text-[#111111] mb-4">📊 Status Kendaraan</h3>
            <div class="grid grid-cols-3 gap-4">
                <label class="flex items-center gap-3 p-4 border-2 rounded-[12px] cursor-pointer transition
                    {{ $vehicle->status == 'active' ? 'border-[#C1121F] bg-[#C1121F]/5' : 'border-[#E5E5E5] hover:border-[#C1121F]' }}">
                    <input type="radio" name="status" value="active" {{ $vehicle->status == 'active' ? 'checked' : '' }} class="w-5 h-5 text-[#C1121F]">
                    <div>
                        <span class="font-semibold text-[#111111] block">🟢 Aktif</span>
                        <span class="text-xs text-gray-500 font-light">Bisa jadwal travel, tour & rental</span>
                    </div>
                </label>
                <label class="flex items-center gap-3 p-4 border-2 rounded-[12px] cursor-pointer transition
                    {{ $vehicle->status == 'maintenance' ? 'border-[#C1121F] bg-[#C1121F]/5' : 'border-[#E5E5E5] hover:border-[#C1121F]' }}">
                    <input type="radio" name="status" value="maintenance" {{ $vehicle->status == 'maintenance' ? 'checked' : '' }} class="w-5 h-5 text-[#C1121F]">
                    <div>
                        <span class="font-semibold text-[#111111] block">🟡 Maintenance</span>
                        <span class="text-xs text-gray-500 font-light">Tidak tersedia sama sekali</span>
                    </div>
                </label>
                <label class="flex items-center gap-3 p-4 border-2 rounded-[12px] cursor-pointer transition
                    {{ $vehicle->status == 'inactive' ? 'border-[#C1121F] bg-[#C1121F]/5' : 'border-[#E5E5E5] hover:border-[#C1121F]' }}">
                    <input type="radio" name="status" value="inactive" {{ $vehicle->status == 'inactive' ? 'checked' : '' }} class="w-5 h-5 text-[#C1121F]">
                    <div>
                        <span class="font-semibold text-[#111111] block">🔴 Nonaktif</span>
                        <span class="text-xs text-gray-500 font-light">Tidak dioperasikan</span>
                    </div>
                </label>
            </div>
        </div>

        {{-- SETTING RENTAL --}}
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-6 shadow-sm" id="rentalSection" style="{{ $vehicle->status != 'active' ? 'display:none;' : '' }}">
            <h3 class="font-mono uppercase tracking-wider text-xs font-bold text-[#111111] mb-4">🚐 Setting Rental/Sewa</h3>
            
            <div class="mb-4">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_rental_available" value="1" {{ $vehicle->is_rental_available ? 'checked' : '' }} class="w-5 h-5 text-[#C1121F] rounded">
                    <span class="font-semibold text-[#111111]">☑ Tersedia untuk Disewakan (Rental)</span>
                </label>
                <p class="text-xs text-gray-400 mt-1 font-light ml-8">Centang jika kendaraan ini bisa disewa saat tidak ada jadwal</p>
            </div>

            <div id="rentalFields" style="{{ !$vehicle->is_rental_available ? 'display:none;' : '' }}">
                <div class="grid md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Harga per KM (Rp)</label>
                        <input type="number" name="rental_price_per_km" value="{{ old('rental_price_per_km', $vehicle->rental_price_per_km) }}"
                            class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]" min="0">
                    </div>
                    <div>
                        <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Minimal Charge/Hari (Rp)</label>
                        <input type="number" name="rental_min_price" value="{{ old('rental_min_price', $vehicle->rental_min_price) }}"
                            class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]" min="0">
                    </div>
                    <div>
                        <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Harga Hari Tambahan (Rp)</label>
                        <input type="number" name="rental_extra_day_price" value="{{ old('rental_extra_day_price', $vehicle->rental_extra_day_price) }}"
                            class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]" min="0">
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="flex items-center gap-2 cursor-pointer mb-2">
                            <input type="checkbox" name="rental_include_driver" value="1" {{ $vehicle->rental_include_driver ? 'checked' : '' }} class="w-5 h-5 text-[#C1121F] rounded">
                            <span class="text-sm font-medium text-[#111111]">Include Supir</span>
                        </label>
                        <input type="number" name="rental_driver_price_per_day" value="{{ old('rental_driver_price_per_day', $vehicle->rental_driver_price_per_day) }}"
                            class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]"
                            placeholder="Biaya supir per hari (Rp)" min="0">
                    </div>
                    <div>
                        <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Maks Penumpang Sewa</label>
                        <input type="number" name="rental_max_passengers" value="{{ old('rental_max_passengers', $vehicle->rental_max_passengers) }}"
                            class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]" min="1" max="{{ $vehicle->capacity }}">
                    </div>
                </div>
            </div>
        </div>

        @push('scripts')
        <script>
        // Toggle rental section based on status
        document.querySelectorAll('input[name="status"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                var rentalSection = document.getElementById('rentalSection');
                if (this.value === 'active') {
                    rentalSection.style.display = 'block';
                } else {
                    rentalSection.style.display = 'none';
                    document.querySelector('input[name="is_rental_available"]').checked = false;
                    document.getElementById('rentalFields').style.display = 'none';
                }
            });
        });

        // Toggle rental fields
        document.querySelector('input[name="is_rental_available"]').addEventListener('change', function() {
            document.getElementById('rentalFields').style.display = this.checked ? 'block' : 'none';
        });
        </script>
        @endpush

        <button type="submit" class="btn-gomad-primary w-full py-3 rounded-[12px] font-bold mt-2">
            💾 UPDATE KENDARAAN
        </button>
    </form>
</div>

@push('scripts')
<script>
function previewImage(event) {
    var file = event.target.files[0];
    if (file) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var container = document.getElementById('previewContainer');
            container.innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-cover">';
        };
        reader.readAsDataURL(file);
    }
}
</script>
@endpush
@endsection