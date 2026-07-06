@extends('layouts.agency')

@section('title', 'Transfer Penumpang')
@section('content')
@php
    $agency = auth()->user()->agency;
    $transferService = app(\App\Services\PassengerTransferService::class);
    $transfers = $transferService->getAgencyTransfers($agency->id);
    $outgoingTransfers = $transfers->where('from_agency_id', $agency->id);
    $incomingTransfers = $transfers->where('to_agency_id', $agency->id);
@endphp

<div>
    <h1 class="text-2xl font-bold text-[#111111] mb-6">🔄 Transfer Penumpang</h1>

    <!-- Info -->
    <div class="bg-[#F5F5F5] border border-blue-200 rounded-[12px] p-4 mb-6 text-sm text-blue-800">
        <p class="font-mono uppercase tracking-wider text-xs font-semibold mb-2">💡 Tentang Transfer Penumpang</p>
        <ul class="list-disc list-inside space-y-1 font-light">
            <li>Transfer penumpang memungkinkan Anda memindahkan booking ke jadwal lain</li>
            <li>Berguna saat mobil sepi untuk menghindari kerugian</li>
            <li>Customer tetap berangkat, hanya pindah mobil/agency</li>
            <li>Biaya transfer ditentukan oleh agency penerima</li>
        </ul>
    </div>

    <!-- Tab -->
    <div class="flex border-b border-[#E5E5E5] mb-6" id="transferTabs">
        <button onclick="showTab('incoming')" class="tab-btn px-4 py-2 text-sm font-semibold border-b-2 border-[#C1121F] text-[#C1121F]">
            📥 Transfer Masuk ({{ $incomingTransfers->count() }})
        </button>
        <button onclick="showTab('outgoing')" class="tab-btn px-4 py-2 text-sm font-semibold border-b-2 border-transparent text-gray-500">
            📤 Transfer Keluar ({{ $outgoingTransfers->count() }})
        </button>
    </div>

    <!-- Transfer Masuk -->
    <div id="tab-incoming" class="tab-content">
        @if($incomingTransfers->isEmpty())
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-8 text-center text-gray-500 shadow-sm">Belum ada transfer masuk.</div>
        @else
        <div class="space-y-4">
            @foreach($incomingTransfers as $transfer)
            <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm hover:border-[#C1121F] transition-colors">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h3 class="font-bold text-lg text-[#111111]">
                            Dari: {{ $transfer->fromAgency->agency_name }}
                        </h3>
                        <p class="text-sm text-gray-500 font-light">
                            {{ $transfer->fromSchedule->route->route_name }} | 
                            {{ $transfer->fromSchedule->departure_date->format('d M Y') }}
                        </p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-[10px] font-mono uppercase tracking-wider bg-{{ $transfer->status_color }}-50 text-{{ $transfer->status_color }}-800 border border-{{ $transfer->status_color }}-200">
                        {{ $transfer->status_label }}
                    </span>
                </div>

                <div class="grid grid-cols-3 gap-4 text-sm mb-4">
                    <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3">
                        <span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Penumpang</span>
                        <p class="font-bold text-lg text-[#111111]">{{ $transfer->total_passengers }}</p>
                    </div>
                    <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3">
                        <span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Biaya Transfer</span>
                        <p class="font-bold text-green-600">Rp {{ number_format($transfer->total_transfer_fee, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3">
                        <span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Nilai Booking</span>
                        <p class="font-bold text-[#C1121F]">Rp {{ number_format($transfer->total_booking_value, 0, ',', '.') }}</p>
                    </div>
                </div>

                @if($transfer->status == 'pending')
                <div class="flex gap-2 border-t border-[#E5E5E5] pt-4">
                    <form action="{{ route('agency.transfers.approve', $transfer) }}" method="POST">
                        @csrf
                        <button class="bg-[#C1121F] text-white px-4 py-2 rounded-[12px] text-sm hover:bg-[#8A0F18]">✅ TERIMA</button>
                    </form>
                    <button onclick="rejectTransfer({{ $transfer->id }})" class="bg-[#C1121F] text-white px-4 py-2 rounded-[12px] text-sm hover:bg-[#8A0F18]">❌ TOLAK</button>
                </div>
                @endif

                @if($transfer->status == 'rejected' && $transfer->rejection_reason)
                <p class="text-sm text-red-600 mt-2 font-light">Alasan: {{ $transfer->rejection_reason }}</p>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <!-- Transfer Keluar -->
    <div id="tab-outgoing" class="tab-content" style="display:none;">
        @if($outgoingTransfers->isEmpty())
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-8 text-center text-gray-500 shadow-sm">
            <p class="font-light">Belum ada transfer keluar.</p>
            <a href="{{ route('agency.schedules.index') }}" class="text-[#C1121F] hover:underline mt-2 inline-block font-medium">Lihat Jadwal</a>
        </div>
        @else
        <div class="space-y-4">
            @foreach($outgoingTransfers as $transfer)
            <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm hover:border-[#C1121F] transition-colors">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h3 class="font-bold text-lg text-[#111111]">
                            Ke: {{ $transfer->toAgency->agency_name }}
                        </h3>
                        <p class="text-sm text-gray-500 font-light">
                            {{ $transfer->toSchedule->route->route_name }} | 
                            {{ $transfer->toSchedule->departure_date->format('d M Y') }}
                        </p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-[10px] font-mono uppercase tracking-wider bg-{{ $transfer->status_color }}-50 text-{{ $transfer->status_color }}-800 border border-{{ $transfer->status_color }}-200">
                        {{ $transfer->status_label }}
                    </span>
                </div>

                <div class="grid grid-cols-3 gap-4 text-sm mb-4">
                    <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3">
                        <span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Penumpang</span>
                        <p class="font-bold text-lg text-[#111111]">{{ $transfer->total_passengers }}</p>
                    </div>
                    <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3">
                        <span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Biaya Transfer</span>
                        <p class="font-bold text-red-600">Rp {{ number_format($transfer->total_transfer_fee, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3">
                        <span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Nilai Booking</span>
                        <p class="font-bold text-[#C1121F]">Rp {{ number_format($transfer->total_booking_value, 0, ',', '.') }}</p>
                    </div>
                </div>

                @if($transfer->status == 'pending')
                <div class="border-t border-[#E5E5E5] pt-4">
                    <form action="{{ route('agency.transfers.cancel', $transfer) }}" method="POST">
                        @csrf
                        <button class="bg-gray-500 text-white px-4 py-2 rounded-[12px] text-sm hover:bg-gray-600">🚫 BATALKAN</button>
                    </form>
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" style="display:none;" class="fixed inset-0 bg-[#111111]/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-[12px] shadow-2xl p-6 max-w-md w-full border border-[#E5E5E5]">
        <h3 class="font-bold text-lg text-[#111111] mb-2">❌ Tolak Transfer</h3>
        <form id="rejectForm" method="POST">
            @csrf
            <textarea name="reason" rows="3" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] mb-4" placeholder="Alasan penolakan..." required></textarea>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-[#C1121F] text-white py-2 rounded-[12px] font-semibold">KIRIM</button>
                <button type="button" onclick="closeRejectModal()" class="flex-1 border border-[#E5E5E5] py-2 rounded-[12px]">BATAL</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function showTab(tab) {
    document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
    document.getElementById('tab-' + tab).style.display = 'block';
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('border-[#C1121F]', 'text-[#C1121F]');
        btn.classList.add('border-transparent', 'text-gray-500');
    });
    event.target.classList.add('border-[#C1121F]', 'text-[#C1121F]');
    event.target.classList.remove('border-transparent', 'text-gray-500');
}

function rejectTransfer(id) {
    document.getElementById('rejectForm').action = '/agency/transfers/' + id + '/reject';
    document.getElementById('rejectModal').style.display = 'flex';
}

function closeRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
}
</script>
@endpush
@endsection