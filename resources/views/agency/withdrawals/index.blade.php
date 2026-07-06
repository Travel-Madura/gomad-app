@extends('layouts.agency')

@section('title', 'Riwayat Penarikan')
@section('content')
<div>
    <div class="flex justify-between items-center mb-6 border-b border-[#E5E5E5] pb-3">
        <h1 class="text-2xl font-bold text-[#111111]">Riwayat Penarikan</h1>
        <a href="{{ route('agency.withdrawals.create') }}" class="bg-[#C1121F] text-white px-4 py-2 rounded-[12px] hover:bg-[#8A0F18]">+ Tarik Dana</a>
    </div>

    <div class="bg-white border border-[#E5E5E5] rounded-[12px] shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-[#F5F5F5] border-b border-[#E5E5E5]">
                <tr>
                    <th class="px-4 py-3 text-left font-mono uppercase tracking-wider text-xs text-gray-500">ID</th>
                    <th class="px-4 py-3 text-right font-mono uppercase tracking-wider text-xs text-gray-500">Jumlah</th>
                    <th class="px-4 py-3 text-left font-mono uppercase tracking-wider text-xs text-gray-500">Bank</th>
                    <th class="px-4 py-3 text-center font-mono uppercase tracking-wider text-xs text-gray-500">Status</th>
                    <th class="px-4 py-3 text-right font-mono uppercase tracking-wider text-xs text-gray-500">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($withdrawals as $w)
                <tr class="border-t border-[#E5E5E5]">
                    <td class="px-4 py-3 font-mono text-xs text-[#111111]">#{{ $w->id }}</td>
                    <td class="px-4 py-3 text-right font-medium text-[#111111]">Rp {{ number_format($w->amount, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-xs text-gray-500">{{ $w->bank_name }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-1 rounded-full text-[10px] font-mono uppercase tracking-wider border
                            @if(in_array($w->status, ['approved','completed'])) bg-green-50 text-green-700 border-green-200
                            @elseif($w->status == 'pending') bg-yellow-50 text-yellow-700 border-yellow-200
                            @elseif($w->status == 'rejected') bg-red-50 text-red-700 border-red-200
                            @else bg-[#F5F5F5] text-gray-600 border-[#E5E5E5] @endif">
                            {{ $w->status }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right text-xs text-gray-500 font-light">{{ $w->created_at->format('d M Y H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500 font-light">Belum ada penarikan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection