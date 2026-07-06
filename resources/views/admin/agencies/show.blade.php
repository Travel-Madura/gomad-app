@extends('layouts.admin')

@section('title', 'Detail Agency')
@section('content')
<div class="max-w-5xl">
    <a href="{{ route('admin.agencies.index') }}" class="text-[#C1121F] text-sm mb-4 inline-block hover:underline">← Kembali</a>

    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-6 shadow-sm">
        <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4 mb-4">
            <div>
                <h1 class="text-2xl font-bold text-[#111111]">{{ $agency->agency_name }}</h1>
                <p class="text-gray-500 text-sm font-light font-mono">{{ $agency->slug }}</p>
                @if($agency->is_verified)
                <span class="inline-block mt-1 px-2 py-0.5 bg-green-50 text-green-700 text-[10px] font-mono uppercase tracking-wider rounded-full border border-green-200">Terverifikasi</span>
                @else
                <span class="inline-block mt-1 px-2 py-0.5 bg-yellow-50 text-yellow-700 text-[10px] font-mono uppercase tracking-wider rounded-full border border-yellow-200">Pending</span>
                @endif
            </div>
            <div class="flex gap-2">
                @if(!$agency->is_verified)
                <form action="{{ route('admin.agencies.verify', $agency) }}" method="POST">
                    @csrf
                    <button class="bg-[#C1121F] text-white px-4 py-2 rounded-[12px] text-sm font-medium hover:bg-[#8A0F18] transition">Verifikasi</button>
                </form>
                <button onclick="openRejectModal()" class="bg-[#C1121F] text-white px-4 py-2 rounded-[12px] text-sm font-medium hover:bg-[#8A0F18] transition">Tolak</button>
                @endif
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-4 text-sm">
            <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3"><span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Pemilik</span><span class="font-medium text-[#111111]">{{ $agency->user->name ?? '-' }}</span></div>
            <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3"><span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Email</span><span class="font-medium text-[#111111]">{{ $agency->user->email ?? '-' }}</span></div>
            <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3"><span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Alamat</span><span class="font-medium text-[#111111]">{{ $agency->address ?? '-' }}</span></div>
            <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3"><span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Tahun Berdiri</span><span class="font-medium text-[#111111]">{{ $agency->founded_year ?? '-' }}</span></div>
            <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3"><span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Rating</span><span class="font-medium text-[#111111]">⭐ {{ number_format((float)$agency->rating, 1) }}</span></div>
            <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3"><span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Total Booking</span><span class="font-medium text-[#111111]">{{ $agency->total_bookings }}</span></div>
        </div>

        @if($agency->business_license)
        <div class="mt-4 p-4 bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px]">
            <a href="{{ $agency->business_license }}" target="_blank" class="text-[#C1121F] text-sm hover:underline font-medium">Lihat Dokumen PDF</a>
        </div>
        @endif

        @php $verifications = $agency->verifications()->latest()->get(); @endphp
        @if($verifications->isNotEmpty())
        <div class="mt-4">
            <h3 class="font-mono uppercase tracking-wider text-xs font-semibold text-[#111111] mb-2">Riwayat Verifikasi</h3>
            <div class="space-y-2">
                @foreach($verifications as $v)
                <div class="text-sm p-3 rounded-[12px] border
                    @if($v->status == 'approved') bg-green-50 border-green-200
                    @elseif($v->status == 'rejected') bg-red-50 border-red-200
                    @else bg-yellow-50 border-yellow-200 @endif">
                    <div class="flex justify-between">
                        <span class="font-medium text-[#111111]">
                            @if($v->status == 'approved') ✅ Disetujui
                            @elseif($v->status == 'rejected') ❌ Ditolak
                            @else ⏳ Pending @endif
                        </span>
                        <span class="text-gray-500 font-light">{{ $v->created_at->format('d M Y H:i') }}</span>
                    </div>
                    @if($v->rejection_reason)<p class="text-[#C1121F] mt-1 font-light">Alasan: {{ $v->rejection_reason }}</p>@endif
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

{{-- MODAL REJECT --}}
<div id="rejectModal" style="display:none;" class="fixed inset-0 bg-[#111111]/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-[12px] shadow-xl p-6 max-w-md w-full border border-[#E5E5E5]">
        <h3 class="font-bold text-lg text-[#111111] mb-2">Tolak Pengajuan</h3>
        <p class="text-sm text-gray-500 font-light mb-4">Tulis alasan penolakan untuk {{ $agency->agency_name }}</p>
        <form action="{{ route('admin.agencies.reject', $agency) }}" method="POST">
            @csrf
            <textarea name="reason" rows="3" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition mb-4" placeholder="Alasan penolakan..." required></textarea>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-[#C1121F] text-white py-2 rounded-[12px] font-semibold hover:bg-[#8A0F18]">Kirim</button>
                <button type="button" onclick="closeRejectModal()" class="flex-1 border border-[#E5E5E5] py-2 rounded-[12px]">Batal</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openRejectModal() { document.getElementById('rejectModal').style.display = 'flex'; }
function closeRejectModal() { document.getElementById('rejectModal').style.display = 'none'; }
document.getElementById('rejectModal').addEventListener('click', function(e) { if (e.target === this) closeRejectModal(); });
</script>
@endpush
@endsection