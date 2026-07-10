@extends('layouts.admin')

@section('title', 'Buat Promo Rental')
@section('content')

<div class="max-w-3xl">
    <h1 class="text-2xl font-bold text-[#111111] mb-6">Buat Promo Rental Baru</h1>

    <form action="{{ route('admin.rental-promos.store') }}" method="POST" class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm space-y-6">
        @csrf

        <div class="grid md:grid-cols-2 gap-4">
            <div><label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Nama <span class="text-[#C1121F]">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]" required></div>
            <div><label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Jenis <span class="text-[#C1121F]">*</span></label>
                <select name="type" id="promoType" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]" required onchange="togglePromoType()">
                    <option value="general">🌍 General</option>
                    <option value="selective">🎯 Selektif</option>
                </select></div>
        </div>

        <div><label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Deskripsi</label>
            <textarea name="description" rows="2" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]">{{ old('description') }}</textarea></div>

        <div class="grid grid-cols-3 gap-4">
            <div><label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Diskon (%)</label>
                <input type="number" name="discount_percent" value="{{ old('discount_percent') }}" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]" min="1" max="100" required></div>
            <div><label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Maks Diskon (Rp)</label>
                <input type="number" name="max_discount" value="{{ old('max_discount', 50000) }}" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]" required></div>
            <div><label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Min Pembelian (Rp)</label>
                <input type="number" name="min_purchase" value="{{ old('min_purchase', 0) }}" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]"></div>
        </div>

        <div id="selectiveTarget" style="display:none;">
            <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Target Kendaraan</label>
            <select name="vehicle_id" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]">
                <option value="">Pilih Kendaraan</option>
                @foreach($vehicles as $v)
                <option value="{{ $v->id }}" {{ old('vehicle_id') == $v->id ? 'selected' : '' }}>{{ $v->plate_number }} ({{ $v->agency->agency_name }})</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-2">Metode Pembayaran</label>
            <div class="grid grid-cols-3 gap-3">
                <label class="flex items-center gap-2 p-3 border border-[#E5E5E5] rounded-[12px] cursor-pointer"><input type="checkbox" name="applicable_payment_methods[]" value="midtrans" class="w-4 h-4 text-[#C1121F] rounded"> 💳 Online</label>
                <label class="flex items-center gap-2 p-3 border border-[#E5E5E5] rounded-[12px] cursor-pointer"><input type="checkbox" name="applicable_payment_methods[]" value="cash" class="w-4 h-4 text-[#C1121F] rounded"> 🏪 Warung</label>
                <label class="flex items-center gap-2 p-3 border border-[#E5E5E5] rounded-[12px] cursor-pointer"><input type="checkbox" name="applicable_payment_methods[]" value="cod" class="w-4 h-4 text-[#C1121F] rounded"> 🚗 COD</label>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <div><label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Tgl Mulai</label><input type="date" name="start_date" value="{{ old('start_date') }}" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]" required></div>
            <div><label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Tgl Selesai</label><input type="date" name="end_date" value="{{ old('end_date') }}" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]" required></div>
        </div>

        <div>
            <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Penanggung Biaya</label>
            <select name="cost_bearer" id="costBearer" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]" required onchange="toggleShare()">
                <option value="platform">Platform (100%)</option>
                <option value="agency">Agency (100%)</option>
                <option value="shared">Shared</option>
            </select>
        </div>
        <div id="sharePercent" style="display:none;" class="grid grid-cols-2 gap-4">
            <div><label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Platform (%)</label><input type="number" name="platform_share" value="50" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]"></div>
            <div><label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Agency (%)</label><input type="number" name="agency_share" value="50" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]"></div>
        </div>

        <button type="submit" class="btn-gomad-primary px-8 py-3 rounded-[12px] font-semibold">💾 SIMPAN</button>
    </form>
</div>

@push('scripts')
<script>
function togglePromoType() { document.getElementById('selectiveTarget').style.display = document.getElementById('promoType').value === 'selective' ? 'block' : 'none'; }
function toggleShare() { document.getElementById('sharePercent').style.display = document.getElementById('costBearer').value === 'shared' ? 'grid' : 'none'; }
</script>
@endpush
@endsection