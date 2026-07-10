@extends('layouts.admin')

@section('title', 'Detail Booking Travel')
@section('content')

<div class="max-w-4xl mx-auto">
    <a href="{{ route('admin.bookings.index', ['type' => 'travel']) }}" class="text-[#C1121F] text-sm mb-4 inline-block hover:underline">← Kembali</a>

    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
        {{-- Header --}}
        <div class="flex justify-between items-start mb-4">
            <div>
                <h1 class="text-2xl font-bold font-mono text-[#111111]">{{ $booking->booking_code }}</h1>
                <p class="text-gray-500 font-light">{{ $booking->customer->name ?? '-' }}</p>
                <span class="px-2 py-1 rounded-full text-[10px] font-mono uppercase tracking-wider border mt-1 inline-block
                    @if($booking->status == 'paid') bg-green-50 text-green-700 border-green-200
                    @elseif($booking->status == 'pending') bg-yellow-50 text-yellow-700 border-yellow-200
                    @elseif($booking->status == 'cancelled') bg-red-50 text-red-700 border-red-200
                    @elseif($booking->status == 'completed') bg-blue-50 text-blue-700 border-blue-200
                    @elseif($booking->status == 'on_going') bg-indigo-50 text-indigo-700 border-indigo-200
                    @else bg-[#F5F5F5] text-gray-600 border-[#E5E5E5] @endif">
                    {{ $booking->status_label }}
                </span>
            </div>
            <p class="text-2xl font-bold text-[#C1121F] font-mono">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
        </div>

        {{-- Detail --}}
        <div class="grid md:grid-cols-2 gap-4 text-sm">
            <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3">
                <span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Customer</span>
                <p class="font-semibold text-[#111111]">{{ $booking->customer->name ?? '-' }}</p>
                <p class="text-xs text-gray-500 font-light">{{ $booking->customer->phone ?? '-' }} | {{ $booking->customer->email ?? '-' }}</p>
            </div>
            <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3">
                <span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Agency</span>
                <p class="font-semibold text-[#111111]">{{ $booking->schedule->agency->agency_name ?? '-' }}</p>
                @if($booking->schedule->agency)
                <p class="text-xs text-gray-500 font-light">⭐ {{ number_format($booking->schedule->agency->rating, 1) }} | {{ $booking->schedule->agency->contact_alternate ?? '-' }}</p>
                @endif
            </div>
            <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3">
                <span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Rute</span>
                <p class="font-semibold text-[#111111]">{{ $booking->originStop->city_name ?? '?' }} → {{ $booking->destinationStop->city_name ?? '?' }}</p>
                <p class="text-xs text-gray-500 font-light">{{ $booking->schedule->route->route_name ?? '-' }}</p>
            </div>
            <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3">
                <span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Jadwal</span>
                <p class="font-semibold text-[#111111]">{{ $booking->schedule->departure_date->format('d M Y') }} {{ $booking->schedule->departure_time }}</p>
                <p class="text-xs text-gray-500 font-light">{{ $booking->schedule->travel_class_label ?? '-' }}</p>
            </div>
            <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3">
                <span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Kendaraan</span>
                <p class="font-semibold text-[#111111] font-mono">{{ $booking->schedule->vehicle->plate_number ?? '-' }}</p>
                <p class="text-xs text-gray-500 font-light">{{ $booking->schedule->vehicle->brand ?? '' }} {{ $booking->schedule->vehicle->model ?? '' }} ({{ $booking->schedule->vehicle->capacity ?? '-' }} seat)</p>
            </div>
            <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3">
                <span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Driver</span>
                <p class="font-semibold text-[#111111]">{{ $booking->schedule->driver->name ?? 'Belum ditugaskan' }}</p>
                @if($booking->schedule->driver)
                <p class="text-xs text-gray-500 font-light">{{ $booking->schedule->driver->phone ?? '-' }}</p>
                @endif
            </div>
            <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3">
                <span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Penumpang</span>
                <p class="font-semibold text-[#111111]">{{ $booking->total_passengers }} orang</p>
                <p class="text-xs text-gray-500 font-light">Bagasi: {{ $booking->passengers->sum('baggage_weight') ?? 0 }} kg</p>
            </div>
            <div class="col-span-2 bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3">
                <span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">📍 Alamat Penjemputan</span>
                <p class="font-medium text-[#111111]">{{ $booking->pickup_address }}</p>
                @if($booking->pickup_maps_link)
                <a href="{{ $booking->pickup_maps_link }}" target="_blank" class="text-xs text-[#C1121F] hover:underline mt-1 inline-block">🗺️ Buka di Google Maps</a>
                @endif
            </div>
            <div class="col-span-2 bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3">
                <span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">🎯 Alamat Tujuan</span>
                <p class="font-medium text-[#111111]">{{ $booking->destination_address }}</p>
                @if($booking->destination_maps_link)
                <a href="{{ $booking->destination_maps_link }}" target="_blank" class="text-xs text-[#C1121F] hover:underline mt-1 inline-block">🗺️ Buka di Google Maps</a>
                @endif
            </div>
            @if($booking->special_notes)
            <div class="col-span-2 bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3">
                <span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">📝 Catatan Khusus</span>
                <p class="font-medium text-[#111111]">{{ $booking->special_notes }}</p>
            </div>
            @endif
        </div>

        {{-- Rincian Harga --}}
        <div class="mt-4 bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-4">
            <h4 class="font-mono uppercase tracking-wider text-xs font-bold mb-3">Rincian Pembayaran</h4>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500 font-light">Harga Tiket ({{ $booking->total_passengers }} × Rp {{ number_format($booking->base_price / max($booking->total_passengers, 1), 0, ',', '.') }})</span>
                    <span class="font-medium text-[#111111]">Rp {{ number_format($booking->base_price, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 font-light">Biaya Layanan</span>
                    <span class="font-medium text-[#111111]">Rp {{ number_format($booking->service_fee, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 font-light">Biaya Platform</span>
                    <span class="font-medium text-[#111111]">Rp {{ number_format($booking->platform_fee, 0, ',', '.') }}</span>
                </div>
                @if($booking->discount_amount > 0)
                <div class="flex justify-between text-[#C1121F]">
                    <span>
                        🎫 Diskon Promo
                        @php
                            $promoUsage = \App\Models\PromoUsage::where('booking_id', $booking->id)->first();
                        @endphp
                        @if($promoUsage && $promoUsage->promo)
                        <span class="text-xs font-light">({{ $promoUsage->promo->name }})</span>
                        @endif
                    </span>
                    <span class="font-bold">-Rp {{ number_format($booking->discount_amount, 0, ',', '.') }}</span>
                </div>
                @endif
                <hr class="border-[#E5E5E5]">
                <div class="flex justify-between text-base font-bold">
                    <span>Total</span>
                    <span class="text-[#C1121F] font-mono">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Daftar Penumpang --}}
        <div class="mt-4">
            <h3 class="font-mono uppercase tracking-wider text-xs font-bold text-[#111111] mb-2">
                Daftar Penumpang ({{ $booking->total_passengers }})
            </h3>
            @if($booking->passengers->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-[#F5F5F5] border-b border-[#E5E5E5]">
                        <tr>
                            <th class="px-3 py-2 text-left font-mono uppercase tracking-wider text-[10px] text-gray-500">#</th>
                            <th class="px-3 py-2 text-left font-mono uppercase tracking-wider text-[10px] text-gray-500">Nama</th>
                            <th class="px-3 py-2 text-left font-mono uppercase tracking-wider text-[10px] text-gray-500">Telepon</th>
                            <th class="px-3 py-2 text-center font-mono uppercase tracking-wider text-[10px] text-gray-500">Seat</th>
                            <th class="px-3 py-2 text-center font-mono uppercase tracking-wider text-[10px] text-gray-500">Bagasi</th>
                            <th class="px-3 py-2 text-center font-mono uppercase tracking-wider text-[10px] text-gray-500">Jemput</th>
                            <th class="px-3 py-2 text-center font-mono uppercase tracking-wider text-[10px] text-gray-500">Turun</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E5E5E5]">
                        @foreach($booking->passengers as $p)
                        <tr class="hover:bg-[#F5F5F5]">
                            <td class="px-3 py-2 text-gray-400 font-mono text-xs">{{ $loop->iteration }}</td>
                            <td class="px-3 py-2 font-medium text-[#111111]">{{ $p->passenger_name }}</td>
                            <td class="px-3 py-2 text-gray-600 font-light">{{ $p->passenger_phone ?? '-' }}</td>
                            <td class="px-3 py-2 text-center font-mono text-xs text-[#111111]">{{ $p->seat_number }}</td>
                            <td class="px-3 py-2 text-center font-mono text-xs text-[#111111]">{{ $p->baggage_weight ?? 0 }} kg</td>
                            <td class="px-3 py-2 text-center">
                                @if($p->picked_up_at)
                                <span class="text-green-600 font-mono text-xs">✅ {{ $p->picked_up_at->format('H:i') }}</span>
                                @else
                                <span class="text-gray-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-center">
                                @if($p->dropped_off_at)
                                <span class="text-green-600 font-mono text-xs">✅ {{ $p->dropped_off_at->format('H:i') }}</span>
                                @else
                                <span class="text-gray-400 text-xs">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-gray-400 text-sm font-light">Tidak ada data penumpang.</p>
            @endif
        </div>

        {{-- Status Pembayaran --}}
        @if($booking->payment)
        <div class="mt-4 p-3 rounded-[12px] text-sm border 
            @if($booking->payment->status == 'paid') bg-green-50 border-green-200
            @elseif($booking->payment->status == 'pending') bg-yellow-50 border-yellow-200
            @elseif(in_array($booking->payment->status, ['failed', 'expired'])) bg-red-50 border-red-200
            @elseif($booking->payment->status == 'refunded') bg-purple-50 border-purple-200
            @elseif($booking->payment->status == 'refund_pending') bg-orange-50 border-orange-200
            @else bg-[#F5F5F5] border-[#E5E5E5] @endif">
            <div class="flex justify-between items-center">
                <div>
                    <span class="font-medium text-[#111111]">Pembayaran:</span>
                    <span class="ml-1">
                        @if($booking->payment->payment_type == 'midtrans') 💳 Online (Midtrans)
                        @elseif($booking->payment->payment_type == 'cash') 🏪 Warung GoMad
                        @elseif($booking->payment->payment_type == 'cod') 🚗 COD
                        @endif
                    </span>
                    @if($booking->payment->payment_channel)
                    <span class="text-xs text-gray-400 ml-1">({{ $booking->payment->payment_channel }})</span>
                    @endif
                </div>
                <span class="px-2 py-1 rounded-full text-[10px] font-mono uppercase tracking-wider border
                    @if($booking->payment->status == 'paid') bg-green-50 text-green-700 border-green-200
                    @elseif($booking->payment->status == 'pending') bg-yellow-50 text-yellow-700 border-yellow-200
                    @elseif($booking->payment->status == 'refunded') bg-purple-50 text-purple-700 border-purple-200
                    @elseif($booking->payment->status == 'refund_pending') bg-orange-50 text-orange-700 border-orange-200
                    @elseif(in_array($booking->payment->status, ['failed', 'expired'])) bg-red-50 text-red-700 border-red-200
                    @else bg-[#F5F5F5] text-gray-600 border-[#E5E5E5] @endif">
                    {{ $booking->payment->status_label ?? $booking->payment->status }}
                </span>
            </div>
            @if($booking->payment->transaction_id)
            <p class="text-xs text-gray-500 font-mono mt-1">ID Transaksi: {{ $booking->payment->transaction_id }}</p>
            @endif
            @if($booking->payment->paid_at)
            <p class="text-xs text-gray-500 font-light mt-1">Dibayar: {{ $booking->payment->paid_at->format('d M Y H:i') }}</p>
            @endif
            @if($booking->payment->expired_at)
            <p class="text-xs text-gray-500 font-light mt-1">Expired: {{ $booking->payment->expired_at->format('d M Y H:i') }}</p>
            @endif

            {{-- Cash Payment Detail --}}
            @if($booking->cashPayment)
            <div class="mt-2 pt-2 border-t border-[#E5E5E5]">
                <p class="text-xs text-gray-500">
                    🏪 <strong>Warung:</strong> {{ $booking->cashPayment->paymentAgent->agent_name ?? '-' }}
                </p>
                <p class="text-xs text-gray-500">
                    📋 <strong>Kode Bayar:</strong> {{ $booking->cashPayment->payment_code }}
                </p>
                <p class="text-xs text-gray-500">
                    💰 <strong>Komisi Warung:</strong> Rp {{ number_format($booking->cashPayment->agent_commission, 0, ',', '.') }}
                </p>
            </div>
            @endif
        </div>
        @else
        <div class="mt-4 p-3 rounded-[12px] text-sm border bg-[#F5F5F5] border-[#E5E5E5]">
            <span class="text-gray-500 font-light">Belum ada pembayaran.</span>
        </div>
        @endif

        {{-- Refund Section --}}
        @if($booking->payment && $booking->payment->status === 'refund_pending')
        <div class="bg-yellow-50 border-2 border-yellow-300 rounded-[12px] p-6 mt-4">
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
                    <form action="{{ route('admin.bookings.refund.approve', $booking) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded-[12px] font-semibold hover:bg-green-700 transition">
                            ✅ Setujui Refund
                        </button>
                    </form>
                    <button onclick="openRejectRefundModal()" class="bg-red-600 text-white px-6 py-3 rounded-[12px] font-semibold hover:bg-red-700 transition">
                        ❌ Tolak Refund
                    </button>
                </div>
            </div>
        </div>

        {{-- Modal Tolak Refund --}}
        <div id="rejectRefundModal" style="display:none;" class="fixed inset-0 bg-[#111111]/50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-[12px] shadow-xl p-6 max-w-md w-full border border-[#E5E5E5]">
                <h3 class="font-bold text-lg text-[#111111] mb-2">Tolak Refund</h3>
                <p class="text-sm text-gray-500 font-light mb-4">Tulis alasan penolakan refund untuk booking {{ $booking->booking_code }}</p>
                <form action="{{ route('admin.bookings.refund.reject', $booking) }}" method="POST">
                    @csrf
                    <textarea name="reason" rows="3" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition mb-4" placeholder="Alasan penolakan..." required></textarea>
                    <div class="flex gap-3">
                        <button type="submit" class="flex-1 bg-red-600 text-white py-2 rounded-[12px] font-semibold hover:bg-red-700">Tolak Refund</button>
                        <button type="button" onclick="document.getElementById('rejectRefundModal').style.display='none'" class="flex-1 border border-[#E5E5E5] py-2 rounded-[12px]">Batal</button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        {{-- Refund Status (jika sudah selesai) --}}
        @if($booking->payment && in_array($booking->payment->status, ['refunded', 'refund_approved', 'refund_rejected']))
        <div class="mt-4 bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-4">
            <h3 class="font-mono uppercase tracking-wider text-xs font-bold text-[#111111] mb-3">Status Refund</h3>
            @php $refund = $booking->payment->payment_detail['refund'] ?? []; @endphp
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-400 font-light">Status</span>
                    <span class="font-semibold text-[#111111]">
                        @if($booking->payment->status == 'refunded') ✅ Refund Selesai
                        @elseif($booking->payment->status == 'refund_approved') ⏳ Refund Disetujui
                        @elseif($booking->payment->status == 'refund_rejected') ❌ Refund Ditolak
                        @endif
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400 font-light">Jumlah</span>
                    <span class="font-semibold text-[#111111]">Rp {{ number_format($refund['refund_amount'] ?? ($refund['amount'] ?? 0), 0, ',', '.') }}</span>
                </div>
                @if(isset($refund['approved_by_name']))
                <div class="flex justify-between">
                    <span class="text-gray-400 font-light">Disetujui oleh</span>
                    <span class="font-semibold text-[#111111]">{{ $refund['approved_by_name'] }}</span>
                </div>
                @endif
                @if(isset($refund['rejected_by_name']))
                <div class="flex justify-between">
                    <span class="text-gray-400 font-light">Ditolak oleh</span>
                    <span class="font-semibold text-red-600">{{ $refund['rejected_by_name'] }}</span>
                </div>
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

        {{-- Review --}}
        @if($booking->review)
        <div class="mt-4 bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-4">
            <h3 class="font-mono uppercase tracking-wider text-xs font-bold text-[#111111] mb-3">⭐ Review Customer</h3>
            <div class="flex items-center gap-2 mb-2">
                <span class="text-yellow-500">
                    @for($i = 1; $i <= 5; $i++){{ $i <= $booking->review->rating ? '⭐' : '☆' }}@endfor
                </span>
                <span class="text-sm font-medium text-[#111111]">{{ $booking->review->rating }}/5</span>
            </div>
            @if($booking->review->review)
            <p class="text-sm text-gray-600 font-light">{{ $booking->review->review }}</p>
            @endif
            <p class="text-xs text-gray-400 mt-2 font-light">Oleh: {{ $booking->review->customer->name ?? 'Customer' }} • {{ $booking->review->created_at->format('d M Y') }}</p>
        </div>
        @endif

        {{-- Timestamps --}}
        <div class="mt-4 text-xs text-gray-400 font-light space-y-1">
            <p>📅 Dibuat: {{ $booking->created_at->format('d M Y H:i') }}</p>
            <p>🔄 Terakhir diupdate: {{ $booking->updated_at->format('d M Y H:i') }}</p>
            @if($booking->cancelled_at)
            <p>❌ Dibatalkan: {{ $booking->cancelled_at->format('d M Y H:i') }}</p>
            @endif
            @if($booking->completed_at)
            <p>✅ Selesai: {{ $booking->completed_at->format('d M Y H:i') }}</p>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function openRejectRefundModal() { 
    document.getElementById('rejectRefundModal').style.display = 'flex'; 
}
document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById('rejectRefundModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) modal.style.display = 'none';
        });
    }
});
</script>
@endpush
@endsection