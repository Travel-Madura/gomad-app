@extends('layouts.admin')

@section('title', 'Edit Promo Rental')
@section('content')

@php $selectedMethods = $promo->applicable_payment_methods ? explode(',', $promo->applicable_payment_methods) : []; @endphp

<div class="max-w-3xl">
    <h1 class="text-2xl font-bold text-[#111111] mb-6">Edit Promo Rental</h1>

    <form action="{{ route('admin.rental-promos.update', $promo) }}" method="POST" class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm space-y-6">
        @csrf @method('PUT')

        <div class="grid md:grid-cols-2 gap-4">
            <div><label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Nama</label><input type="text" name="name" value="{{ old('name', $promo->name) }}" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]" required></div>
            <div><label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Jenis</label>
                <select name="type" id="promoType" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]" required onchange="togglePromoType()">
                    <option value="general" {{ $promo->type == 'general' ? 'selected' : '' }}>🌍 General</option>
                    <option value="selective" {{ $promo->type == 'selective' ? 'selected' : '' }}>🎯 Selektif</option>
                </select></div>
        </div>

        <div><label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Deskripsi</label><textarea name="description" rows="2" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]">{{ old('description', $promo->description) }}</textarea></div>

        <div class="grid grid-cols-3 gap-4">
            <div><label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Diskon (%)</label><input type="number" name="discount_percent" value="{{ old('discount_percent', $promo->discount_percent) }}" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]" min="1" max="100" required></div>
            <div><label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Maks Diskon</label><input type="number" name="max_discount" value="{{ old('max_discount', $promo->max_discount) }}" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]" required></div>
            <div><label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Min Pembelian</label><input type="number" name="min_purchase" value="{{ old('min_purchase', $promo->min_purchase) }}" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]"></div>
        </div>

        <div id="selectiveTarget" style="display: {{ $promo->type == 'selective' ? 'block' : 'none' }};">
            <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Target Kendaraan</label>
            <select name="vehicle_id" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]">
                <option value="">Pilih</option>
                @foreach($vehicles as $v)
                <option value="{{ $v->id }}" {{ $promo->vehicle_id == $v->id ? 'selected' : '' }}>{{ $v->plate_number }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-2">Metode Pembayaran</label>
            <div class="grid grid-cols-3 gap-3">
                <label class="flex items-center gap-2 p-3 border border-[#E5E5E5] rounded-[12px] cursor-pointer"><input type="checkbox" name="applicable_payment_methods[]" value="midtrans" class="w-4 h-4 text-[#C1121F] rounded" {{ in_array('midtrans', $selectedMethods) ? 'checked' : '' }}> 💳 Online</label>
                <label class="flex items-center gap-2 p-3 border border-[#E5E5E5] rounded-[12px] cursor-pointer"><input type="checkbox" name="applicable_payment_methods[]" value="cash" class="w-4 h-4 text-[#C1121F] rounded" {{ in_array('cash', $selectedMethods) ? 'checked' : '' }}> 🏪 Warung</label>
                <label class="flex items-center gap-2 p-3 border border-[#E5E5E5] rounded-[12px] cursor-pointer"><input type="checkbox" name="applicable_payment_methods[]" value="cod" class="w-4 h-4 text-[#C1121F] rounded" {{ in_array('cod', $selectedMethods) ? 'checked' : '' }}> 🚗 COD</label>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <div><label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Tgl Mulai</label><input type="date" name="start_date" value="{{ old('start_date', $promo->start_date->format('Y-m-d')) }}" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]" required></div>
            <div><label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Tgl Selesai</label><input type="date" name="end_date" value="{{ old('end_date', $promo->end_date->format('Y-m-d')) }}" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]" required></div>
        </div>

        <div>
            <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Status</label>
            <select name="is_active" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111]">
                <option value="1" {{ $promo->is_active ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ !$promo->is_active ? 'selected' : '' }}>Nonaktif</option>
            </select>
        </div>

        <button type="submit" class="btn-gomad-primary px-8 py-3 rounded-[12px] font-semibold">💾 UPDATE</button>
    </form>
</div>

@push('scripts')
<script>
function togglePromoType() { document.getElementById('selectiveTarget').style.display = document.getElementById('promoType').value === 'selective' ? 'block' : 'none'; }
</script>
@endpush
@endsection