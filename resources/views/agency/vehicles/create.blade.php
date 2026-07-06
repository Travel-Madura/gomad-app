@extends('layouts.agency')

@section('title', 'Tambah Kendaraan')
@section('content')
<div>
    <h1 class="text-lg font-bold text-[#111111] mb-6">Tambah Kendaraan</h1>

    <form action="{{ route('agency.vehicles.store') }}" method="POST" enctype="multipart/form-data" class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm space-y-4">
        @csrf

        <div>
            <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Foto Kendaraan</label>
            <div class="flex items-center gap-4">
                <div class="w-32 h-24 bg-[#F5F5F5] rounded-[12px] flex items-center justify-center text-3xl overflow-hidden border border-[#E5E5E5]" id="previewContainer">
                    🚐
                </div>
                <div class="flex-1">
                    <input type="file" name="vehicle_image" id="vehicleImage" accept="image/*"
                           class="w-full text-sm" onchange="previewImage(event)">
                    <p class="text-[10px] text-gray-400 mt-1 font-light">Format: JPG, PNG, WEBP. Max 2MB</p>
                </div>
            </div>
        </div>

        <div>
            <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Nomor Plat <span class="text-[#C1121F]">*</span></label>
            <input type="text" name="plate_number" value="{{ old('plate_number') }}"
                   class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" placeholder="M 1234 AB" required>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Merk <span class="text-[#C1121F]">*</span></label>
                <input type="text" name="brand" value="{{ old('brand') }}"
                       class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" placeholder="Toyota" required>
            </div>
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Model <span class="text-[#C1121F]">*</span></label>
                <input type="text" name="model" value="{{ old('model') }}"
                       class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" placeholder="Hiace Commuter" required>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Tahun</label>
                <input type="number" name="year" value="{{ old('year', date('Y')) }}"
                       class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" min="2000" max="{{ date('Y') + 1 }}">
            </div>
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Kapasitas Seat <span class="text-[#C1121F]">*</span></label>
                <input type="number" name="capacity" value="{{ old('capacity', 8) }}"
                       class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" min="4" max="20" required>
            </div>
        </div>

        <div>
            <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Tipe Kendaraan <span class="text-[#C1121F]">*</span></label>
            <select name="type" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" required>
                <option value="economy" {{ old('type') == 'economy' ? 'selected' : '' }}>Ekonomi</option>
                <option value="premium" {{ old('type') == 'premium' ? 'selected' : '' }}>Premium</option>
            </select>
        </div>

        <button type="submit" class="btn-gomad-primary px-8 py-3 rounded-[12px] font-semibold mt-4">
            💾 SIMPAN KENDARAAN
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