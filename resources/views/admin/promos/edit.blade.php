@extends('layouts.admin')

@section('title', 'Edit Promo')
@section('content')
<div class="max-w-3xl">
    <h1 class="text-2xl font-bold text-[#111111] mb-6">Edit Promo</h1>

    @php $selectedMethods = $promo->applicable_payment_methods ? explode(',', $promo->applicable_payment_methods) : []; @endphp

    <form action="{{ route('admin.promos.update', $promo) }}" method="POST" class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm space-y-6">
        @csrf @method('PUT')
        
        <div class="grid md:grid-cols-2 gap-4">
            <div><label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Nama Promo</label><input type="text" name="name" value="{{ old('name', $promo->name) }}" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" required></div>
            <div><label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Jenis Promo</label>
                <select name="type" id="promoType" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" required onchange="togglePromoType()">
                    <option value="general" {{ $promo->type == 'general' ? 'selected' : '' }}>🌍 General</option>
                    <option value="selective" {{ $promo->type == 'selective' ? 'selected' : '' }}>🎯 Selektif</option>
                </select>
            </div>
        </div>
        <div><label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Deskripsi</label><textarea name="description" rows="2" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition">{{ old('description', $promo->description) }}</textarea></div>
        <div class="grid grid-cols-3 gap-4">
            <div><label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Diskon (%)</label><input type="number" name="discount_percent" value="{{ old('discount_percent', $promo->discount_percent) }}" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" min="1" max="100" required></div>
            <div><label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Maks Diskon (Rp)</label><input type="number" name="max_discount" value="{{ old('max_discount', $promo->max_discount) }}" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" required></div>
            <div><label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Min Pembelian (Rp)</label><input type="number" name="min_purchase" value="{{ old('min_purchase', $promo->min_purchase) }}" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition"></div>
        </div>

        <div id="selectiveTarget" style="display: {{ $promo->type == 'selective' ? 'grid' : 'none' }};" class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Target Rute</label>
                <select name="route_id" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition">
                    <option value="">Semua Rute</option>
                    @foreach(\App\Models\Route::where('is_active', true)->get() as $route)
                    <option value="{{ $route->id }}" {{ $promo->route_id == $route->id ? 'selected' : '' }}>{{ $route->route_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Target Kelas</label>
                <select name="travel_class" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition">
                    <option value="">Semua</option>
                    <option value="economy" {{ $promo->travel_class == 'economy' ? 'selected' : '' }}>Ekonomi</option>
                    <option value="premium" {{ $promo->travel_class == 'premium' ? 'selected' : '' }}>Premium</option>
                    <option value="charter" {{ $promo->travel_class == 'charter' ? 'selected' : '' }}>Charter</option>
                </select>
            </div>
        </div>

        {{-- Metode Pembayaran --}}
        <div>
            <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-2">Berlaku untuk Metode Pembayaran</label>
            <div class="grid grid-cols-3 gap-3">
                <label class="flex items-center gap-2 p-3 border border-[#E5E5E5] rounded-[12px] cursor-pointer hover:bg-[#F5F5F5]">
                    <input type="checkbox" name="applicable_payment_methods[]" value="midtrans" class="w-4 h-4 text-[#C1121F] rounded border-[#E5E5E5] focus:ring-[#C1121F]" {{ in_array('midtrans', $selectedMethods) ? 'checked' : '' }}>
                    <div><span class="text-sm font-medium text-[#111111]">💳 Online</span><span class="text-[10px] text-gray-400 block font-light">Midtrans</span></div>
                </label>
                <label class="flex items-center gap-2 p-3 border border-[#E5E5E5] rounded-[12px] cursor-pointer hover:bg-[#F5F5F5]">
                    <input type="checkbox" name="applicable_payment_methods[]" value="cash" class="w-4 h-4 text-[#C1121F] rounded border-[#E5E5E5] focus:ring-[#C1121F]" {{ in_array('cash', $selectedMethods) ? 'checked' : '' }}>
                    <div><span class="text-sm font-medium text-[#111111]">🏪 Warung</span><span class="text-[10px] text-gray-400 block font-light">Warung GoMad</span></div>
                </label>
                <label class="flex items-center gap-2 p-3 border border-[#E5E5E5] rounded-[12px] cursor-pointer hover:bg-[#F5F5F5]">
                    <input type="checkbox" name="applicable_payment_methods[]" value="cod" class="w-4 h-4 text-[#C1121F] rounded border-[#E5E5E5] focus:ring-[#C1121F]" {{ in_array('cod', $selectedMethods) ? 'checked' : '' }}>
                    <div><span class="text-sm font-medium text-[#111111]">🚗 COD</span><span class="text-[10px] text-gray-400 block font-light">Bayar ke Sopir</span></div>
                </label>
            </div>
            <p class="text-[10px] text-gray-400 mt-2 font-light">Jika tidak ada yang dicentang, promo berlaku untuk semua metode pembayaran.</p>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <div><label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Tanggal Mulai</label><input type="date" name="start_date" value="{{ old('start_date', $promo->start_date->format('Y-m-d')) }}" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" required></div>
            <div><label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Tanggal Selesai</label><input type="date" name="end_date" value="{{ old('end_date', $promo->end_date->format('Y-m-d')) }}" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" required></div>
        </div>
        <div><label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Status</label><select name="is_active" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition"><option value="1" {{ $promo->is_active ? 'selected' : '' }}>Aktif</option><option value="0" {{ !$promo->is_active ? 'selected' : '' }}>Nonaktif</option></select></div>

        <button type="submit" class="btn-gomad-primary">Update Promo</button>
    </form>
</div>
@push('scripts')
<script>
function togglePromoType() { document.getElementById('selectiveTarget').style.display = document.getElementById('promoType').value === 'selective' ? 'grid' : 'none'; }
</script>
@endpush
@endsection