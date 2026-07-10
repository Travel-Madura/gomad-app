@extends('layouts.admin')

@section('title', 'Buat Promo Wisata')
@section('content')

<div class="max-w-3xl">
    <h1 class="text-2xl font-bold text-[#111111] mb-6">Buat Promo Wisata Baru</h1>

    <form action="{{ route('admin.tour-promos.store') }}" method="POST" 
          class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm space-y-6">
        @csrf

        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Nama Promo <span class="text-[#C1121F]">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" 
                       class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" required>
            </div>
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Jenis Promo <span class="text-[#C1121F]">*</span></label>
                <select name="type" id="promoType" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" required onchange="togglePromoType()">
                    <option value="general">🌍 General (Semua Paket)</option>
                    <option value="selective">🎯 Selektif (Paket Tertentu)</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Deskripsi</label>
            <textarea name="description" rows="2" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition">{{ old('description') }}</textarea>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Diskon (%) <span class="text-[#C1121F]">*</span></label>
                <input type="number" name="discount_percent" value="{{ old('discount_percent') }}" 
                       class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" min="1" max="100" required>
            </div>
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Maks Diskon (Rp) <span class="text-[#C1121F]">*</span></label>
                <input type="number" name="max_discount" value="{{ old('max_discount', 50000) }}" 
                       class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" required>
            </div>
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Min Pembelian (Rp)</label>
                <input type="number" name="min_purchase" value="{{ old('min_purchase', 0) }}" 
                       class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition">
            </div>
        </div>

        {{-- Target Paket (Selektif) --}}
        <div id="selectiveTarget" style="display:none;">
            <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Target Paket Wisata</label>
            <select name="tour_package_id" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition">
                <option value="">Pilih Paket</option>
                @foreach($packages as $pkg)
                <option value="{{ $pkg->id }}" {{ old('tour_package_id') == $pkg->id ? 'selected' : '' }}>{{ $pkg->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Metode Pembayaran --}}
        <div>
            <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-2">Berlaku untuk Metode Pembayaran</label>
            <div class="grid grid-cols-3 gap-3">
                <label class="flex items-center gap-2 p-3 border border-[#E5E5E5] rounded-[12px] cursor-pointer hover:bg-[#F5F5F5]">
                    <input type="checkbox" name="applicable_payment_methods[]" value="midtrans" class="w-4 h-4 text-[#C1121F] rounded">
                    <span class="text-sm font-medium">💳 Online</span>
                </label>
                <label class="flex items-center gap-2 p-3 border border-[#E5E5E5] rounded-[12px] cursor-pointer hover:bg-[#F5F5F5]">
                    <input type="checkbox" name="applicable_payment_methods[]" value="cash" class="w-4 h-4 text-[#C1121F] rounded">
                    <span class="text-sm font-medium">🏪 Warung</span>
                </label>
                <label class="flex items-center gap-2 p-3 border border-[#E5E5E5] rounded-[12px] cursor-pointer hover:bg-[#F5F5F5]">
                    <input type="checkbox" name="applicable_payment_methods[]" value="cod" class="w-4 h-4 text-[#C1121F] rounded">
                    <span class="text-sm font-medium">🚗 COD</span>
                </label>
            </div>
            <p class="text-[10px] text-gray-400 mt-2 font-light">Kosongkan semua = berlaku untuk semua metode.</p>
        </div>

        {{-- Periode --}}
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Tanggal Mulai <span class="text-[#C1121F]">*</span></label>
                <input type="date" name="start_date" value="{{ old('start_date') }}" 
                       class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" required>
            </div>
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Tanggal Selesai <span class="text-[#C1121F]">*</span></label>
                <input type="date" name="end_date" value="{{ old('end_date') }}" 
                       class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" required>
            </div>
        </div>

        {{-- Penanggung Biaya --}}
        <div>
            <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Penanggung Biaya <span class="text-[#C1121F]">*</span></label>
            <select name="cost_bearer" id="costBearer" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" required onchange="toggleShare()">
                <option value="platform">Platform (100%)</option>
                <option value="agency">Agency (100%)</option>
                <option value="shared">Shared</option>
            </select>
        </div>
        <div id="sharePercent" style="display:none;" class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Platform (%)</label>
                <input type="number" name="platform_share" value="50" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition">
            </div>
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Agency (%)</label>
                <input type="number" name="agency_share" value="50" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition">
            </div>
        </div>

        <button type="submit" class="btn-gomad-primary px-8 py-3 rounded-[12px] font-semibold">
            💾 SIMPAN PROMO
        </button>
    </form>
</div>

@push('scripts')
<script>
function togglePromoType() {
    document.getElementById('selectiveTarget').style.display = 
        document.getElementById('promoType').value === 'selective' ? 'block' : 'none';
}
function toggleShare() {
    document.getElementById('sharePercent').style.display = 
        document.getElementById('costBearer').value === 'shared' ? 'grid' : 'none';
}
</script>
@endpush
@endsection