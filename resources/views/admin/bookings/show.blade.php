@extends('layouts.admin')

@section('title', 'Detail Booking')
@section('content')
<div class="max-w-4xl mx-auto">
    <a href="{{ route('admin.bookings.index') }}" class="text-[#C1121F] text-sm mb-4 inline-block hover:underline">← Kembali</a>

    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h1 class="text-2xl font-bold font-mono text-[#111111]">{{ $booking->booking_code }}</h1>
                <span class="px-2 py-1 rounded-full text-[10px] font-mono uppercase tracking-wider border
                    @if($booking->status == 'paid') bg-green-50 text-green-700 border-green-200
                    @elseif($booking->status == 'pending') bg-yellow-50 text-yellow-700 border-yellow-200
                    @else bg-[#F5F5F5] text-gray-600 border-[#E5E5E5] @endif">
                    {{ $booking->status_label }}
                </span>
            </div>
            <p class="text-2xl font-bold text-[#C1121F] font-mono">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
        </div>

        <div class="grid md:grid-cols-2 gap-4 text-sm">
            <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3"><span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Customer</span><span class="font-medium text-[#111111]">{{ $booking->customer->name ?? '-' }} ({{ $booking->customer->phone ?? '-' }})</span></div>
            <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3"><span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Agency</span><span class="font-medium text-[#111111]">{{ $booking->schedule->agency->agency_name ?? '-' }}</span></div>
            <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3"><span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Rute</span><span class="font-medium text-[#111111]">{{ $booking->originStop->city_name }} → {{ $booking->destinationStop->city_name }}</span></div>
            <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3"><span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Tanggal</span><span class="font-medium text-[#111111]">{{ $booking->schedule->departure_date->format('d M Y') }} {{ $booking->schedule->departure_time }}</span></div>
            <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3"><span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Kendaraan</span><span class="font-medium text-[#111111]">{{ $booking->schedule->vehicle->plate_number ?? '-' }}</span></div>
            <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3"><span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Driver</span><span class="font-medium text-[#111111]">{{ $booking->schedule->driver->name ?? '-' }}</span></div>
            <div class="col-span-2 bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3"><span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Jemput</span><span class="font-medium text-[#111111]">{{ $booking->pickup_address }}</span></div>
            <div class="col-span-2 bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3"><span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Tujuan</span><span class="font-medium text-[#111111]">{{ $booking->destination_address }}</span></div>
        </div>

        <h3 class="font-mono uppercase tracking-wider text-xs font-bold text-[#111111] mt-6 mb-2">Penumpang ({{ $booking->total_passengers }})</h3>
        @foreach($booking->passengers as $p)
        <div class="flex justify-between text-sm py-1 border-b border-[#F5F5F5] last:border-0">
            <span class="text-[#111111]">{{ $p->passenger_name }} (Seat {{ $p->seat_number }})</span>
            <span class="text-gray-500 font-light">{{ $p->passenger_phone }}</span>
        </div>
        @endforeach
    </div>

    {{-- Refund Status Section --}}
    @if($booking->payment)
        @if($booking->payment->status === 'refund_pending')
        <div class="bg-yellow-50 border-2 border-yellow-300 rounded-[12px] p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-yellow-800 text-lg">💰 Refund Menunggu Approval</h3>
                    @php $refundData = $booking->payment->payment_detail['refund'] ?? []; @endphp
                    <p class="text-sm text-yellow-700 mt-2 font-light">
                        Jumlah refund: <strong>Rp {{ number_format($refundData['amount'] ?? 0, 0, ',', '.') }}</strong>
                    </p>
                    <p class="text-sm text-yellow-700 font-light">
                        Biaya pembatalan: <strong>Rp {{ number_format($refundData['cancellation_fee'] ?? 0, 0, ',', '.') }}</strong>
                    </p>
                    <p class="text-[10px] text-yellow-600 mt-1 font-mono">
                        Diminta: {{ \Carbon\Carbon::parse($refundData['requested_at'] ?? now())->format('d M Y H:i') }}
                    </p>
                </div>
                <div class="flex gap-3">
                    <form action="{{ route('admin.refund.approve', $booking) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-[#C1121F] text-white px-6 py-3 rounded-[12px] font-semibold hover:bg-[#8A0F18] transition">
                            ✅ Setujui Refund
                        </button>
                    </form>
                    <button onclick="openRejectRefundModal()" class="bg-[#C1121F] text-white px-6 py-3 rounded-[12px] font-semibold hover:bg-[#8A0F18] transition">
                        ❌ Tolak Refund
                    </button>
                </div>
            </div>
        </div>

        <div id="rejectRefundModal" style="display:none;" class="fixed inset-0 bg-[#111111]/50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-[12px] shadow-xl p-6 max-w-md w-full border border-[#E5E5E5]">
                <h3 class="font-bold text-lg text-[#111111] mb-2">Tolak Refund</h3>
                <p class="text-sm text-gray-500 font-light mb-4">Tulis alasan penolakan refund untuk booking {{ $booking->booking_code }}</p>
                <form action="{{ route('admin.refund.reject', $booking) }}" method="POST">
                    @csrf
                    <textarea name="reason" rows="3" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition mb-4" placeholder="Alasan penolakan..." required></textarea>
                    <div class="flex gap-3">
                        <button type="submit" class="flex-1 bg-[#C1121F] text-white py-2 rounded-[12px] font-semibold hover:bg-[#8A0F18]">Tolak Refund</button>
                        <button type="button" onclick="document.getElementById('rejectRefundModal').style.display='none'" class="flex-1 border border-[#E5E5E5] py-2 rounded-[12px]">Batal</button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        @if(in_array($booking->payment->status, ['refunded', 'refund_approved', 'refund_rejected']))
        <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-4 mb-6">
            <h3 class="font-mono uppercase tracking-wider text-xs font-bold text-[#111111] mb-3">Status Refund</h3>
            @php $refund = $booking->payment->payment_detail['refund'] ?? []; @endphp
            <div class="space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-gray-400 font-light">Status</span><span class="font-semibold text-[#111111]">{{ $booking->payment->status_label }}</span></div>
                <div class="flex justify-between"><span class="text-gray-400 font-light">Jumlah</span><span class="font-semibold text-[#111111]">Rp {{ number_format($refund['refund_amount'] ?? ($refund['amount'] ?? 0), 0, ',', '.') }}</span></div>
                @if(isset($refund['approved_by_name']))
                <div class="flex justify-between"><span class="text-gray-400 font-light">Disetujui oleh</span><span class="font-semibold text-[#111111]">{{ $refund['approved_by_name'] }}</span></div>
                @endif
                @if(isset($refund['rejected_by_name']))
                <div class="flex justify-between"><span class="text-gray-400 font-light">Ditolak oleh</span><span class="font-semibold text-red-600">{{ $refund['rejected_by_name'] }}</span></div>
                @endif
                @if(isset($refund['rejection_reason']))
                <div class="mt-2 bg-red-50 border border-red-200 rounded-lg p-3">
                    <span class="text-[10px] font-mono uppercase tracking-wider text-red-500">Alasan penolakan:</span>
                    <p class="text-sm text-red-700 font-light">{{ $refund['rejection_reason'] }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif
    @endif
</div>

@push('scripts')
<script>
function openRejectRefundModal() { document.getElementById('rejectRefundModal').style.display = 'flex'; }
document.getElementById('rejectRefundModal')?.addEventListener('click', function(e) { if (e.target === this) this.style.display = 'none'; });
</script>
@endpush
@endsection