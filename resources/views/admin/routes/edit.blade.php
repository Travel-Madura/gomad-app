@extends('layouts.admin')

@section('title', 'Edit Rute')
@section('content')
<div>
    <h1 class="text-lg font-bold text-[#111111] mb-6">Edit Rute</h1>

    <form action="{{ route('admin.routes.update', $route) }}" method="POST" enctype="multipart/form-data" class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm space-y-6">
        @csrf @method('PUT')

        {{-- Foto Rute --}}
        <div>
            <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-2">🖼️ Foto Rute</label>
            <div class="flex items-center gap-4">
                <div class="w-40 h-32 bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] flex items-center justify-center text-4xl overflow-hidden" id="photoPreview">
                    @if($route->photo)
                    <img src="{{ $route->photo }}" alt="{{ $route->route_name }}" class="w-full h-full object-cover">
                    @else
                    <span>🗺️</span>
                    @endif
                </div>
                <div class="flex-1">
                    <input type="file" name="photo" id="photoInput" accept="image/*"
                           class="w-full text-sm" onchange="previewPhoto(event)">
                    <p class="text-[10px] text-gray-400 mt-1 font-light">Biarkan kosong jika tidak ingin mengubah foto</p>
                    @if($route->photo)
                    <p class="text-[10px] text-green-600 mt-1 font-light">✅ Foto sudah diupload</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Informasi Dasar --}}
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Nama Rute <span class="text-[#C1121F]">*</span></label>
                <input type="text" name="route_name" value="{{ old('route_name', $route->route_name) }}"
                       class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" required>
            </div>
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Jarak (km)</label>
                <input type="number" name="distance_km" value="{{ old('distance_km', $route->distance_km) }}" step="0.01"
                       class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition">
            </div>
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Kota Asal <span class="text-[#C1121F]">*</span></label>
                <input type="text" name="origin_city" value="{{ old('origin_city', $route->origin_city) }}"
                       class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" required>
            </div>
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Kota Tujuan <span class="text-[#C1121F]">*</span></label>
                <input type="text" name="destination_city" value="{{ old('destination_city', $route->destination_city) }}"
                       class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" required>
            </div>
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Estimasi Durasi (menit)</label>
                <input type="number" name="estimated_duration" value="{{ old('estimated_duration', $route->estimated_duration) }}"
                       class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition">
            </div>
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Status</label>
                <select name="is_active" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition">
                    <option value="1" {{ $route->is_active ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ !$route->is_active ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
        </div>

        {{-- Harga & COD --}}
        <div class="grid md:grid-cols-3 gap-4">
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Maksimal Harga Tiket (Rp)</label>
                <input type="number" name="max_price" value="{{ old('max_price', $route->max_price) }}"
                    class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition"
                    placeholder="Batas maksimal harga">
            </div>
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Minimal Deposit COD (Rp)</label>
                <input type="number" name="cod_min_deposit" value="{{ old('cod_min_deposit', $route->cod_min_deposit) }}"
                    class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition">
            </div>
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">COD Tersedia?</label>
                <select name="cod_available" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition">
                    <option value="0" {{ !$route->cod_available ? 'selected' : '' }}>Tidak</option>
                    <option value="1" {{ $route->cod_available ? 'selected' : '' }}>Ya</option>
                </select>
            </div>
        </div>

        {{-- Metode Pembayaran --}}
        <div class="border-t border-[#E5E5E5] pt-6 mt-6">
            <h3 class="font-mono uppercase tracking-wider text-xs font-bold text-[#111111] mb-3">💳 Metode Pembayaran yang Tersedia</h3>
            <p class="text-[10px] text-gray-400 mb-4 font-light">Pilih metode pembayaran yang bisa digunakan customer di rute ini.</p>
            
            @php
                $selectedMethods = $route->payment_methods_array;
            @endphp
            
            <div class="grid grid-cols-3 gap-4">
                <label class="flex items-center gap-3 p-4 border-2 border-[#E5E5E5] rounded-[12px] cursor-pointer hover:border-[#C1121F] transition has-[:checked]:border-[#C1121F] has-[:checked]:bg-[#C1121F]/5">
                    <input type="checkbox" name="payment_methods[]" value="midtrans" class="w-5 h-5 text-[#C1121F] rounded border-[#E5E5E5] focus:ring-[#C1121F]" 
                        {{ in_array('midtrans', $selectedMethods) ? 'checked' : '' }}>
                    <div>
                        <span class="text-sm font-semibold text-[#111111]">💳 Online (Midtrans)</span>
                        <span class="text-[10px] text-gray-400 block font-light">Transfer Bank, VA, QRIS, E-Wallet</span>
                    </div>
                </label>
                
                <label class="flex items-center gap-3 p-4 border-2 border-[#E5E5E5] rounded-[12px] cursor-pointer hover:border-[#C1121F] transition has-[:checked]:border-[#C1121F] has-[:checked]:bg-[#C1121F]/5">
                    <input type="checkbox" name="payment_methods[]" value="cash" class="w-5 h-5 text-[#C1121F] rounded border-[#E5E5E5] focus:ring-[#C1121F]"
                        {{ in_array('cash', $selectedMethods) ? 'checked' : '' }}>
                    <div>
                        <span class="text-sm font-semibold text-[#111111]">🏪 Warung GoMad (Cash)</span>
                        <span class="text-[10px] text-gray-400 block font-light">Bayar tunai di warung terdekat</span>
                    </div>
                </label>
                
                <label class="flex items-center gap-3 p-4 border-2 border-[#E5E5E5] rounded-[12px] cursor-pointer hover:border-[#C1121F] transition has-[:checked]:border-[#C1121F] has-[:checked]:bg-[#C1121F]/5">
                    <input type="checkbox" name="payment_methods[]" value="cod" class="w-5 h-5 text-[#C1121F] rounded border-[#E5E5E5] focus:ring-[#C1121F]"
                        {{ in_array('cod', $selectedMethods) ? 'checked' : '' }}>
                    <div>
                        <span class="text-sm font-semibold text-[#111111]">🚗 COD (Bayar ke Sopir)</span>
                        <span class="text-[10px] text-gray-400 block font-light">Bayar tunai saat penjemputan</span>
                    </div>
                </label>
            </div>
            <p class="text-[10px] text-gray-400 mt-2 font-light">Jika tidak ada yang dicentang, semua metode pembayaran akan tersedia.</p>
        </div>

        <div>
            <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Deskripsi Rute</label>
            <textarea name="description" rows="3" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition">{{ old('description', $route->description) }}</textarea>
        </div>

        <button type="submit" class="btn-gomad-primary px-8 py-3 rounded-[12px] font-semibold">
            💾 UPDATE RUTE
        </button>
    </form>
</div>

@push('scripts')
<script>
function previewPhoto(event) {
    var file = event.target.files[0];
    if (file) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('photoPreview').innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-cover">';
        };
        reader.readAsDataURL(file);
    }
}
</script>
@endpush
@endsection