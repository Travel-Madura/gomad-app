@extends('layouts.admin')

@section('title', 'Settlement')
@section('content')
<div>
    <h1 class="text-2xl font-bold text-[#111111] mb-6">Daftar Settlement</h1>

    <div class="bg-white border border-[#E5E5E5] rounded-[12px] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-[#F5F5F5] border-b border-[#E5E5E5]">
                    <tr>
                        <th class="px-4 py-3 text-left font-mono uppercase tracking-wider text-xs text-gray-500">ID</th>
                        <th class="px-4 py-3 text-left font-mono uppercase tracking-wider text-xs text-gray-500">Warung</th>
                        <th class="px-4 py-3 text-left font-mono uppercase tracking-wider text-xs text-gray-500">Periode</th>
                        <th class="px-4 py-3 text-right font-mono uppercase tracking-wider text-xs text-gray-500">Jumlah</th>
                        <th class="px-4 py-3 text-center font-mono uppercase tracking-wider text-xs text-gray-500">Status</th>
                        <th class="px-4 py-3 text-right font-mono uppercase tracking-wider text-xs text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($settlements as $s)
                    <tr class="border-t border-[#E5E5E5] hover:bg-[#F5F5F5]">
                        <td class="px-4 py-3 font-mono text-[#111111]">#{{ $s->id }}</td>
                        <td class="px-4 py-3 text-[#111111]">{{ $s->paymentAgent->agent_name ?? '-' }}</td>
                        <td class="px-4 py-3 text-[10px] font-mono text-gray-500">{{ $s->period_start->format('d M') }} - {{ $s->period_end->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-right font-medium text-[#111111]">Rp {{ number_format($s->amount_to_settle, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-[10px] font-mono uppercase tracking-wider border
                                @if($s->status == 'verified') bg-green-50 text-green-700 border-green-200
                                @elseif($s->status == 'paid') bg-blue-50 text-blue-700 border-blue-200
                                @else bg-yellow-50 text-yellow-700 border-yellow-200 @endif">
                                {{ $s->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if($s->status == 'paid')
                            <form action="{{ route('admin.settlements.verify', $s) }}" method="POST">
                                @csrf
                                <button class="text-[#C1121F] hover:underline text-xs font-medium">Verifikasi</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500 font-light">Tidak ada settlement.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">{{ $settlements->links() }}</div>
</div>
@endsection