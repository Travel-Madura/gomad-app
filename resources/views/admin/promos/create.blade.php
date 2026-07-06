@extends('layouts.admin')

@section('title', 'Buat Promo')
@section('content')
<div class="max-w-3xl">
    <h1 class="text-2xl font-bold text-[#111111] mb-6">Buat Promo Baru</h1>

    <form action="{{ route('admin.promos.store') }}" method="POST" class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm space-y-6">
        @csrf
        
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Nama Promo</label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" placeholder="Flash Sale Lebaran" required>
            </div>
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Jenis Promo</label>
                <select name="type" id="promoType" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" required onchange="togglePromoType()">
                    <option value="general">🌍 General (Semua Customer)</option>
                    <option value="selective">🎯 Selektif (Agency Pilih)</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Deskripsi</label>
            <textarea name="description" rows="2" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition">{{ old('description') }}</textarea>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div><label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Diskon (%)</label><input type="number" name="discount_percent" value="{{ old('discount_percent') }}" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" min="1" max="100" required></div>
            <div><label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Maks Diskon (Rp)</label><input type="number" name="max_discount" value="{{ old('max_discount', 50000) }}" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" required></div>
            <div><label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Min Pembelian (Rp)</label><input type="number" name="min_purchase" value="{{ old('min_purchase', 0) }}" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition"></div>
        </div>

        <div id="selectiveTarget" style="display:none;" class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Target Rute</label>
                <select name="route_id" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition">
                    <option value="">Semua Rute</option>
                    @foreach(\App\Models\Route::where('is_active', true)->get() as $route)
                    <option value="{{ $route->id }}">{{ $route->route_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Target Kelas</label>
                <select name="travel_class" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition">
                    <option value="">Semua</option>
                    <option value="economy">Ekonomi</option>
                    <option value="premium">Premium</option>
                    <option value="charter">Charter</option>
                </select>
            </div>
        </div>

        {{-- Metode Pembayaran --}}
        <div>
            <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-2">Berlaku untuk Metode Pembayaran</label>
            <p class="text-[10px] text-gray-400 mb-3 font-light">Centang metode yang berlaku. Kosongkan semua untuk berlaku di semua metode.</p>
            <div class="grid grid-cols-3 gap-3">
                <label class="flex items-center gap-2 p-3 border border-[#E5E5E5] rounded-[12px] cursor-pointer hover:bg-[#F5F5F5]">
                    <input type="checkbox" name="applicable_payment_methods[]" value="midtrans" class="w-4 h-4 text-[#C1121F] rounded border-[#E5E5E5] focus:ring-[#C1121F]">
                    <div><span class="text-sm font-medium text-[#111111]">💳 Online</span><span class="text-[10px] text-gray-400 block font-light">Midtrans</span></div>
                </label>
                <label class="flex items-center gap-2 p-3 border border-[#E5E5E5] rounded-[12px] cursor-pointer hover:bg-[#F5F5F5]">
                    <input type="checkbox" name="applicable_payment_methods[]" value="cash" class="w-4 h-4 text-[#C1121F] rounded border-[#E5E5E5] focus:ring-[#C1121F]">
                    <div><span class="text-sm font-medium text-[#111111]">🏪 Warung</span><span class="text-[10px] text-gray-400 block font-light">Warung GoMad</span></div>
                </label>
                <label class="flex items-center gap-2 p-3 border border-[#E5E5E5] rounded-[12px] cursor-pointer hover:bg-[#F5F5F5]">
                    <input type="checkbox" name="applicable_payment_methods[]" value="cod" class="w-4 h-4 text-[#C1121F] rounded border-[#E5E5E5] focus:ring-[#C1121F]">
                    <div><span class="text-sm font-medium text-[#111111]">🚗 COD</span><span class="text-[10px] text-gray-400 block font-light">Bayar ke Sopir</span></div>
                </label>
            </div>
            <p class="text-[10px] text-gray-400 mt-2 font-light">Jika tidak ada yang dicentang, promo berlaku untuk semua metode pembayaran.</p>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <div><label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Tanggal Mulai</label><input type="date" name="start_date" value="{{ old('start_date') }}" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" required></div>
            <div><label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Tanggal Selesai</label><input type="date" name="end_date" value="{{ old('end_date') }}" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" required></div>
        </div>

        <div>
            <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Penanggung Biaya</label>
            <select name="cost_bearer" id="costBearer" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" required onchange="toggleShare()">
                <option value="platform">Platform (100%)</option>
                <option value="agency">Agency (100%)</option>
                <option value="shared">Shared</option>
            </select>
        </div>
        <div id="sharePercent" style="display:none;" class="grid grid-cols-2 gap-4">
            <div><label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Platform (%)</label><input type="number" name="platform_share" value="50" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition"></div>
            <div><label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Agency (%)</label><input type="number" name="agency_share" value="50" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition"></div>
        </div>

        <button type="submit" class="btn-gomad-primary">Simpan Promo</button>
    </form>
</div>

@push('scripts')
<script>
function togglePromoType() { document.getElementById('selectiveTarget').style.display = document.getElementById('promoType').value === 'selective' ? 'grid' : 'none'; }
function toggleShare() { document.getElementById('sharePercent').style.display = document.getElementById('costBearer').value === 'shared' ? 'grid' : 'none'; }
</script>
@endpush
@endsection