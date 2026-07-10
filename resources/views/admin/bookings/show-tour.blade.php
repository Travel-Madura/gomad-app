@extends('layouts.admin')

@section('title', 'Detail Booking Tour')
@section('content')

<div class="max-w-4xl mx-auto">
    <a href="{{ route('admin.bookings.index', ['type' => 'tour']) }}" class="text-[#C1121F] text-sm mb-4 inline-block hover:underline">← Kembali</a>

    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
        {{-- Header --}}
        <div class="flex justify-between items-start mb-4">
            <div>
                <h1 class="text-2xl font-bold font-mono text-[#111111]">{{ $booking->booking_code }}</h1>
                <p class="text-gray-500 font-light">{{ $booking->group_name }}</p>
                <span class="px-2 py-1 rounded-full text-[10px] font-mono uppercase tracking-wider border mt-1 inline-block
                    @if($booking->status == 'paid') bg-green-50 text-green-700 border-green-200
                    @elseif($booking->status == 'pending') bg-yellow-50 text-yellow-700 border-yellow-200
                    @elseif($booking->status == 'cancelled') bg-red-50 text-red-700 border-red-200
                    @elseif($booking->status == 'completed') bg-blue-50 text-blue-700 border-blue-200
                    @else bg-[#F5F5F5] text-gray-600 border-[#E5E5E5] @endif">
                    {{ $booking->status_label }}
                </span>
            </div>
            <p class="text-2xl font-bold text-[#C1121F] font-mono">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
        </div>

        {{-- Detail --}}
        <div class="grid md:grid-cols-2 gap-4 text-sm">
            <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3">
                <span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Paket Tour</span>
                <p class="font-semibold text-[#111111]">{{ $booking->tourSchedule->tourPackage->name ?? '-' }}</p>
            </div>
            <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3">
                <span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Agency</span>
                <p class="font-semibold text-[#111111]">{{ $booking->tourSchedule->tourPackage->agency->agency_name ?? '-' }}</p>
            </div>
            <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3">
                <span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Customer</span>
                <p class="font-semibold text-[#111111]">{{ $booking->customer->name ?? '-' }} ({{ $booking->customer->phone ?? '-' }})</p>
            </div>
            <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3">
                <span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Tanggal</span>
                <p class="font-semibold text-[#111111]">
                    {{ $booking->tourSchedule->departure_date->format('d M Y') }} {{ $booking->tourSchedule->departure_time }}
                    @if($booking->tourSchedule->return_date)
                    → {{ $booking->tourSchedule->return_date->format('d M Y') }}
                    @endif
                </p>
            </div>
            <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3">
                <span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Peserta</span>
                <p class="font-semibold text-[#111111]">
                    {{ $booking->total_participants }} orang 
                    ({{ $booking->total_adults }} Dewasa, {{ $booking->total_children }} Anak)
                </p>
            </div>
            <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3">
                <span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Kendaraan</span>
                <p class="font-semibold text-[#111111] font-mono">{{ $booking->tourSchedule->vehicle->plate_number ?? '-' }}</p>
            </div>
            @if($booking->tourSchedule->driver_id)
            <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3">
                <span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Driver</span>
                <p class="font-semibold text-[#111111]">{{ $booking->tourSchedule->driver->name ?? '-' }}</p>
            </div>
            @endif
            <div class="col-span-2 bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3">
                <span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">📍 Jemput</span>
                <p class="font-medium text-[#111111]">{{ $booking->pickup_address }}</p>
            </div>
            @if($booking->special_requests)
            <div class="col-span-2 bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3">
                <span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">📝 Permintaan Khusus</span>
                <p class="font-medium text-[#111111]">{{ $booking->special_requests }}</p>
            </div>
            @endif
        </div>

        {{-- Rincian Harga --}}
        <div class="mt-4 bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-4">
            <h4 class="font-mono uppercase tracking-wider text-xs font-bold mb-3">Rincian Pembayaran</h4>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500 font-light">Harga Tiket</span>
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
                    <span>Diskon Promo</span>
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

        {{-- Daftar Peserta --}}
        <div class="mt-4">
            <h3 class="font-mono uppercase tracking-wider text-xs font-bold text-[#111111] mb-2">
                Daftar Peserta ({{ $booking->total_participants }})
            </h3>
            @if($booking->participants->isNotEmpty())
            <div class="space-y-1">
                @foreach($booking->participants as $p)
                <div class="flex justify-between items-center text-sm py-1.5 px-3 bg-[#F5F5F5] border border-[#E5E5E5] rounded-lg">
                    <div>
                        <span class="text-[#111111]">{{ $p->participant_name }}</span>
                        <span class="text-xs font-mono ml-2 {{ $p->participant_type == 'adult' ? 'text-blue-600' : 'text-orange-600' }}">
                            {{ $p->participant_type == 'adult' ? 'Dewasa' : 'Anak' }}
                        </span>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-gray-500 text-xs font-light">{{ $p->participant_phone ?? '-' }}</span>
                        <span class="text-gray-400 text-xs font-light">{{ $p->id_number ?? '-' }}</span>
                        <span class="text-xs font-mono text-gray-400">Seat {{ $p->seat_number }}</span>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-gray-400 text-sm font-light">Tidak ada data peserta.</p>
            @endif
        </div>

        {{-- Status Pembayaran --}}
        @if($booking->payment)
        <div class="mt-4 p-3 rounded-[12px] text-sm border 
            @if($booking->payment->status == 'paid') bg-green-50 border-green-200
            @elseif($booking->payment->status == 'pending') bg-yellow-50 border-yellow-200
            @elseif(in_array($booking->payment->status, ['failed', 'expired'])) bg-red-50 border-red-200
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
                </div>
                <span class="px-2 py-1 rounded-full text-[10px] font-mono uppercase tracking-wider border
                    @if($booking->payment->status == 'paid') bg-green-50 text-green-700 border-green-200
                    @elseif($booking->payment->status == 'pending') bg-yellow-50 text-yellow-700 border-yellow-200
                    @elseif($booking->payment->status == 'refunded') bg-purple-50 text-purple-700 border-purple-200
                    @elseif($booking->payment->status == 'refund_pending') bg-orange-50 text-orange-700 border-orange-200
                    @else bg-[#F5F5F5] text-gray-600 border-[#E5E5E5] @endif">
                    {{ $booking->payment->status }}
                </span>
            </div>
            @if($booking->payment->transaction_id)
            <p class="text-xs text-gray-500 font-mono mt-1">ID: {{ $booking->payment->transaction_id }}</p>
            @endif
            @if($booking->payment->paid_at)
            <p class="text-xs text-gray-500 font-light mt-1">Dibayar: {{ $booking->payment->paid_at->format('d M Y H:i') }}</p>
            @endif
        </div>
        @else
        <div class="mt-4 p-3 rounded-[12px] text-sm border bg-[#F5F5F5] border-[#E5E5E5]">
            <span class="text-gray-500 font-light">Belum ada pembayaran.</span>
        </div>
        @endif

        {{-- Refund Status --}}
        @if($booking->payment && in_array($booking->payment->status, ['refunded', 'refund_pending']))
        <div class="mt-4 bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-4">
            <h3 class="font-mono uppercase tracking-wider text-xs font-bold text-[#111111] mb-3">Status Refund</h3>
            @php $refund = $booking->payment->payment_detail['refund'] ?? []; @endphp
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-400 font-light">Status</span>
                    <span class="font-semibold text-[#111111]">
                        @if($booking->payment->status == 'refunded') ✅ Selesai
                        @else ⏳ Menunggu
                        @endif
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400 font-light">Jumlah Refund</span>
                    <span class="font-semibold text-[#111111]">Rp {{ number_format($refund['amount'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400 font-light">Biaya Pembatalan</span>
                    <span class="font-semibold text-[#111111]">Rp {{ number_format($refund['cancellation_fee'] ?? 0, 0, ',', '.') }}</span>
                </div>
                @if(isset($refund['cancellation_percent']))
                <div class="flex justify-between">
                    <span class="text-gray-400 font-light">Persentase Fee</span>
                    <span class="font-semibold text-[#111111]">{{ $refund['cancellation_percent'] }}%</span>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Timestamps --}}
        <div class="mt-4 text-xs text-gray-400 font-light space-y-1">
            <p>Dibuat: {{ $booking->created_at->format('d M Y H:i') }}</p>
            @if($booking->cancelled_at)
            <p>Dibatalkan: {{ $booking->cancelled_at->format('d M Y H:i') }}</p>
            @endif
            @if($booking->completed_at)
            <p>Selesai: {{ $booking->completed_at->format('d M Y H:i') }}</p>
            @endif
        </div>
    </div>
</div>
@endsection