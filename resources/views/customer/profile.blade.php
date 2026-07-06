@extends('layouts.customer')

@section('title', 'Profil')
@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-[#111111] mb-6">Profil Saya</h1>

    {{-- Referral Card --}}
    @php
        $referralCode = \App\Models\ReferralCode::where('user_id', auth()->id())->first();
        if (!$referralCode) { $referralCode = app(\App\Services\PromoService::class)->generateReferralCode(auth()->user()); }
    @endphp
    <div class="bg-[#C1121F] rounded-[12px] border border-[#C1121F] p-6 mb-6 text-white shadow-sm">
        <h2 class="text-xl font-bold mb-2">Ajak Teman, Dapat Diskon!</h2>
        <p class="text-sm text-white/80 font-light mb-4">Bagikan kode referral Anda. Setiap teman yang daftar dan transaksi, Anda dapat diskon s/d 50%!</p>
        
        <div class="bg-white/10 backdrop-blur rounded-[12px] p-4 mb-4 text-center border border-white/10">
            <p class="text-[10px] font-mono uppercase tracking-wider text-white/70 mb-1">Kode Referral Anda</p>
            <p class="text-3xl font-mono font-bold tracking-widest">{{ $referralCode->code }}</p>
        </div>
        
        <div class="bg-white/10 backdrop-blur rounded-[12px] p-3 mb-4 border border-white/10">
            <p class="text-[10px] font-mono uppercase tracking-wider text-white/70 mb-1">Atau bagikan link:</p>
            <div class="flex items-center gap-2">
                <input type="text" id="referralLink" readonly value="{{ route('register', ['ref' => $referralCode->code]) }}" class="flex-1 px-3 py-2 rounded-lg text-[#111111] text-sm bg-white">
                <button onclick="copyReferralLink()" class="bg-white text-[#C1121F] px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-100 transition">Copy</button>
            </div>
        </div>
        
        <div class="flex gap-2">
            <a href="https://wa.me/?text={{ urlencode('Daftar GoMad pakai kode referral saya: ' . $referralCode->code . ' 🎁\nDaftar di: ' . route('register', ['ref' => $referralCode->code])) }}" target="_blank" class="flex-1 bg-green-500 text-white py-2.5 rounded-[12px] text-sm font-semibold text-center hover:bg-green-600 transition">💬 Share WhatsApp</a>
            <button onclick="copyReferralCode()" class="bg-white/20 text-white py-2.5 px-4 rounded-[12px] text-sm font-semibold hover:bg-white/30 transition">Copy Kode</button>
        </div>
        
        <div class="grid grid-cols-2 gap-3 mt-4">
            <div class="bg-white/10 rounded-[12px] p-3 text-center border border-white/10">
                <p class="text-[10px] font-mono uppercase tracking-wider text-white/70">Total Mengajak</p>
                <p class="text-2xl font-bold">{{ $referralCode->total_referred }}</p>
            </div>
            <div class="bg-white/10 rounded-[12px] p-3 text-center border border-white/10">
                <p class="text-[10px] font-mono uppercase tracking-wider text-white/70">Berhasil</p>
                <p class="text-2xl font-bold">{{ $referralCode->successful_referrals }}</p>
            </div>
        </div>
    </div>

    {{-- Form Profil --}}
    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
        <h3 class="font-mono uppercase tracking-wider text-xs font-bold text-[#111111] mb-4">Informasi Akun</h3>
        <form action="{{ route('customer.profile.update') }}" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Nama <span class="text-[#C1121F]">*</span></label>
                <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" required>
            </div>
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Email</label>
                <input type="email" value="{{ auth()->user()->email }}" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] bg-transparent text-gray-400 cursor-not-allowed" disabled>
            </div>
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Nomor HP <span class="text-[#C1121F]">*</span></label>
                <input type="text" name="phone" value="{{ old('phone', auth()->user()->phone) }}" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" required>
            </div>
            <button type="submit" class="btn-gomad-primary w-full py-3 rounded-[12px] font-semibold mt-2">Simpan</button>
        </form>
    </div>
</div>

@push('scripts')
<script>
function copyReferralLink() { var input = document.getElementById('referralLink'); input.select(); document.execCommand('copy'); alert('Link referral dicopy!'); }
function copyReferralCode() { navigator.clipboard.writeText('{{ $referralCode->code }}'); alert('Kode referral dicopy!'); }
</script>
@endpush
@endsection