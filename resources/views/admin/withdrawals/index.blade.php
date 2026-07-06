@extends('layouts.admin')

@section('title', 'Withdrawal')
@section('content')
<div>
    <h1 class="text-2xl font-bold text-[#111111] mb-6">Daftar Withdrawal</h1>

    <div class="bg-white border border-[#E5E5E5] rounded-[12px] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-[#F5F5F5] border-b border-[#E5E5E5]">
                    <tr>
                        <th class="px-4 py-3 text-left font-mono uppercase tracking-wider text-xs text-gray-500">ID</th>
                        <th class="px-4 py-3 text-left font-mono uppercase tracking-wider text-xs text-gray-500">Agency</th>
                        <th class="px-4 py-3 text-right font-mono uppercase tracking-wider text-xs text-gray-500">Jumlah</th>
                        <th class="px-4 py-3 text-left font-mono uppercase tracking-wider text-xs text-gray-500">Bank</th>
                        <th class="px-4 py-3 text-center font-mono uppercase tracking-wider text-xs text-gray-500">Status</th>
                        <th class="px-4 py-3 text-right font-mono uppercase tracking-wider text-xs text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($withdrawals as $w)
                    <tr class="border-t border-[#E5E5E5] hover:bg-[#F5F5F5]">
                        <td class="px-4 py-3 font-mono text-[#111111]">#{{ $w->id }}</td>
                        <td class="px-4 py-3 text-[#111111]">{{ $w->agency->agency_name ?? '-' }}</td>
                        <td class="px-4 py-3 text-right font-medium text-[#111111]">Rp {{ number_format($w->amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-[10px] font-mono text-gray-500">{{ $w->bank_name }} - {{ substr($w->bank_account_number, -4) }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-[10px] font-mono uppercase tracking-wider border
                                @if($w->status == 'approved' || $w->status == 'completed') bg-green-50 text-green-700 border-green-200
                                @elseif($w->status == 'pending') bg-yellow-50 text-yellow-700 border-yellow-200
                                @else bg-red-50 text-red-700 border-red-200 @endif">
                                {{ $w->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if($w->status == 'pending')
                            <form action="{{ route('admin.withdrawals.approve', $w) }}" method="POST" class="inline">
                                @csrf
                                <button class="text-[#C1121F] hover:underline text-xs font-medium mr-2">Approve</button>
                            </form>
                            <form action="{{ route('admin.withdrawals.reject', $w) }}" method="POST" class="inline" onsubmit="return prompt('Alasan:') != null">
                                @csrf
                                <input type="hidden" name="reason" id="rejectReason{{ $w->id }}">
                                <button class="text-[#C1121F] hover:underline text-xs font-medium" 
                                        onclick="document.getElementById('rejectReason{{ $w->id }}').value = prompt('Alasan:'); return document.getElementById('rejectReason{{ $w->id }}').value != null;">
                                    Reject
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500 font-light">Tidak ada withdrawal.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">{{ $withdrawals->links() }}</div>
</div>
@endsection