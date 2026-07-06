@extends('layouts.admin')

@section('title', 'Warung GoMad')
@section('content')
<div>
    <div class="flex justify-between items-center mb-6 border-b border-[#E5E5E5] pb-3">
        <h1 class="text-2xl font-bold text-[#111111]">Daftar Warung GoMad</h1>
    </div>

    <!-- Filter -->
    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-4 mb-6 shadow-sm">
        <form action="{{ route('admin.payment-agents.index') }}" method="GET" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama warung, pemilik, alamat..." 
                   class="flex-1 min-w-[200px] px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] text-sm">
            <select name="status" class="px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] text-sm">
                <option value="">Semua Status</option>
                <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>✅ Verified</option>
                <option value="unverified" {{ request('status') == 'unverified' ? 'selected' : '' }}>⏳ Unverified</option>
            </select>
            <button type="submit" class="bg-[#C1121F] text-white px-4 py-2 rounded-[12px] text-sm hover:bg-[#8A0F18]">Filter</button>
            @if(request()->anyFilled(['search', 'status']))
            <a href="{{ route('admin.payment-agents.index') }}" class="border border-[#E5E5E5] px-4 py-2 rounded-[12px] text-sm text-gray-600 hover:bg-[#F5F5F5]">Reset</a>
            @endif
        </form>
    </div>

    <!-- Statistik -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        @php
            $totalAgents = \App\Models\PaymentAgent::count();
            $verifiedAgents = \App\Models\PaymentAgent::where('is_verified', true)->count();
            $pendingAgents = \App\Models\PaymentAgent::where('is_verified', false)->whereNotNull('agent_name')->count();
            $totalTransactions = \App\Models\CashPayment::where('status', 'confirmed')->count();
        @endphp
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-4 text-center shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Total Warung</p>
            <p class="text-2xl font-bold text-[#111111]">{{ $totalAgents }}</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-4 text-center shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Terverifikasi</p>
            <p class="text-2xl font-bold text-green-600">{{ $verifiedAgents }}</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-4 text-center shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Menunggu Verifikasi</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $pendingAgents }}</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-4 text-center shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Total Transaksi Cash</p>
            <p class="text-2xl font-bold text-blue-600">{{ $totalTransactions }}</p>
        </div>
    </div>

    <!-- Tabel -->
    <div class="bg-white border border-[#E5E5E5] rounded-[12px] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-[#F5F5F5] border-b border-[#E5E5E5]">
                    <tr>
                        <th class="px-4 py-3 text-left font-mono uppercase tracking-wider text-xs text-gray-500">Nama Warung</th>
                        <th class="px-4 py-3 text-left font-mono uppercase tracking-wider text-xs text-gray-500">Pemilik</th>
                        <th class="px-4 py-3 text-left font-mono uppercase tracking-wider text-xs text-gray-500">Alamat</th>
                        <th class="px-4 py-3 text-center font-mono uppercase tracking-wider text-xs text-gray-500">Status</th>
                        <th class="px-4 py-3 text-center font-mono uppercase tracking-wider text-xs text-gray-500">Transaksi</th>
                        <th class="px-4 py-3 text-right font-mono uppercase tracking-wider text-xs text-gray-500">Komisi</th>
                        <th class="px-4 py-3 text-right font-mono uppercase tracking-wider text-xs text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($agents as $agent)
                    <tr class="border-t border-[#E5E5E5] hover:bg-[#F5F5F5]">
                        <td class="px-4 py-3 font-medium text-[#111111]">
                            {{ $agent->agent_name }}
                            @if($agent->rejection_reason)
                            <span class="block text-[10px] text-[#C1121F] mt-0.5 font-light" title="Alasan penolakan: {{ $agent->rejection_reason }}">❌ Ditolak</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600 font-light">{{ $agent->owner_name }}<br><span class="text-[10px]">{{ $agent->owner_phone }}</span></td>
                        <td class="px-4 py-3 text-gray-600 font-light text-[10px] max-w-[200px] truncate">{{ $agent->address }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-[10px] font-mono uppercase tracking-wider border 
                                @if($agent->is_verified) bg-green-50 text-green-700 border-green-200
                                @elseif($agent->agent_name) bg-yellow-50 text-yellow-700 border-yellow-200
                                @else bg-[#F5F5F5] text-gray-600 border-[#E5E5E5] @endif">
                                @if($agent->is_verified) Verified
                                @elseif($agent->agent_name) Pending
                                @else Belum Setup
                                @endif
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center font-mono text-[#111111]">{{ $agent->total_transactions }}</td>
                        <td class="px-4 py-3 text-right text-[#C1121F] font-medium">Rp {{ number_format($agent->total_commission, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.payment-agents.show', $agent) }}" class="text-[#C1121F] hover:underline text-sm font-medium">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500 font-light">Tidak ada data warung.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">{{ $agents->links() }}</div>
</div>
@endsection