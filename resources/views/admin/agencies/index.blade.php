@extends('layouts.admin')

@section('title', 'Agency')
@section('content')

@php
    $agencies = \App\Models\Agency::with('user')->latest()->paginate(15);
@endphp

<div>
    <h1 class="text-2xl font-bold text-[#111111] mb-6">Daftar Agency</h1>

    <div class="bg-white border border-[#E5E5E5] rounded-[12px] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-[#F5F5F5] border-b border-[#E5E5E5]">
                    <tr>
                        <th class="px-4 py-3 text-left font-mono uppercase tracking-wider text-xs text-gray-500">Nama Agency</th>
                        <th class="px-4 py-3 text-left font-mono uppercase tracking-wider text-xs text-gray-500">Pemilik</th>
                        <th class="px-4 py-3 text-center font-mono uppercase tracking-wider text-xs text-gray-500">Status</th>
                        <th class="px-4 py-3 text-center font-mono uppercase tracking-wider text-xs text-gray-500">Rating</th>
                        <th class="px-4 py-3 text-right font-mono uppercase tracking-wider text-xs text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($agencies as $agency)
                    <tr class="border-t border-[#E5E5E5] hover:bg-[#F5F5F5]">
                        <td class="px-4 py-3 font-medium text-[#111111]">{{ $agency->agency_name }}</td>
                        <td class="px-4 py-3 text-gray-600 font-light">{{ $agency->user->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-[10px] font-mono uppercase tracking-wider border
                                @if($agency->is_verified) bg-green-50 text-green-700 border-green-200
                                @else bg-yellow-50 text-yellow-700 border-yellow-200 @endif">
                                {{ $agency->is_verified ? 'Verified' : 'Pending' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center font-mono text-[#111111]">⭐ {{ number_format($agency->rating, 1) }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.agencies.show', $agency) }}" class="text-[#C1121F] hover:underline text-sm font-medium">Detail</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">{{ $agencies->links() }}</div>
</div>
@endsection