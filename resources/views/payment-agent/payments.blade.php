@extends('layouts.payment-agent')

@section('title', 'Riwayat Pembayaran')
@section('content')
@php
    $agent = auth()->user()->paymentAgent;
    $payments = \App\Models\CashPayment::with(['booking.schedule.route', 'booking.originStop', 'booking.destinationStop', 'booking.customer'])
        ->where('payment_agent_id', $agent->id)
        ->latest()
        ->paginate(15);
@endphp

<div>
    <h1 class="text-2xl font-bold text-[#111111] mb-6">Riwayat Pembayaran</h1>

    @if($payments->isEmpty())
    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-12 text-center shadow-sm">
        <div class="w-16 h-16 bg-[#C1121F]/5 rounded-[12px] flex items-center justify-center mx-auto mb-4 border border-[#E5E5E5]">
            <span class="text-2xl">💰</span>
        </div>
        <p class="text-gray-500 text-lg font-light">Belum ada transaksi.</p>
    </div>
    @else
    <div class="bg-white border border-[#E5E5E5] rounded-[12px] shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-[#F5F5F5] border-b border-[#E5E5E5]">
                    <tr>
                        <th class="px-4 py-3 text-left font-mono uppercase tracking-wider text-xs text-gray-500">Kode Bayar</th>
                        <th class="px-4 py-3 text-left font-mono uppercase tracking-wider text-xs text-gray-500">Booking</th>
                        <th class="px-4 py-3 text-left font-mono uppercase tracking-wider text-xs text-gray-500">Customer</th>
                        <th class="px-4 py-3 text-right font-mono uppercase tracking-wider text-xs text-gray-500">Jumlah</th>
                        <th class="px-4 py-3 text-right font-mono uppercase tracking-wider text-xs text-gray-500">Komisi</th>
                        <th class="px-4 py-3 text-center font-mono uppercase tracking-wider text-xs text-gray-500">Status</th>
                        <th class="px-4 py-3 text-right font-mono uppercase tracking-wider text-xs text-gray-500">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E5E5E5]">
                    @foreach($payments as $p)
                    <tr class="hover:bg-[#F5F5F5]">
                        <td class="px-4 py-3 font-mono text-xs text-[#111111]">{{ $p->payment_code }}</td>
                        <td class="px-4 py-3 text-xs">
                            @if($p->booking)
                            <span class="font-medium text-[#111111]">{{ $p->booking->booking_code }}</span>
                            <br><span class="text-gray-500 font-light">{{ $p->booking->originStop->city_name ?? '?' }} → {{ $p->booking->destinationStop->city_name ?? '?' }}</span>
                            @else <span class="text-gray-400">-</span> @endif
                        </td>
                        <td class="px-4 py-3 text-[#111111]">{{ $p->booking->customer->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-right font-medium text-[#111111]">Rp {{ number_format($p->amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-[#C1121F] font-medium">Rp {{ number_format($p->agent_commission, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-[10px] font-mono uppercase tracking-wider border
                                @if($p->status == 'confirmed') bg-green-50 text-green-700 border-green-200
                                @elseif($p->status == 'settled') bg-blue-50 text-blue-700 border-blue-200
                                @elseif($p->status == 'pending') bg-yellow-50 text-yellow-700 border-yellow-200
                                @else bg-[#F5F5F5] text-gray-600 border-[#E5E5E5] @endif">{{ $p->status }}</span>
                        </td>
                        <td class="px-4 py-3 text-right text-xs text-gray-500 font-light">{{ $p->created_at->format('d M H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="mb-6">{{ $payments->links() }}</div>

    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
        <h3 class="font-mono uppercase tracking-wider text-xs font-bold text-[#111111] mb-3">Ringkasan</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3 text-center"><span class="text-[10px] text-gray-400 font-mono uppercase tracking-wider">Total Transaksi</span><p class="font-bold text-lg text-[#111111]">{{ $payments->total() }}</p></div>
            <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3 text-center"><span class="text-[10px] text-gray-400 font-mono uppercase tracking-wider">Total Diterima</span><p class="font-bold text-lg text-[#111111]">Rp {{ number_format($payments->sum('amount'), 0, ',', '.') }}</p></div>
            <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3 text-center"><span class="text-[10px] text-gray-400 font-mono uppercase tracking-wider">Total Komisi</span><p class="font-bold text-lg text-[#C1121F]">Rp {{ number_format($payments->sum('agent_commission'), 0, ',', '.') }}</p></div>
            <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3 text-center"><span class="text-[10px] text-gray-400 font-mono uppercase tracking-wider">Harus Disetor</span><p class="font-bold text-lg text-yellow-600">Rp {{ number_format($payments->sum('amount') - $payments->sum('agent_commission'), 0, ',', '.') }}</p></div>
        </div>
    </div>
    @endif
</div>
@endsection