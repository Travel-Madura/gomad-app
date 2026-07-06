@extends('layouts.admin')

@section('title', 'Customer')
@section('content')
<div>
    <h1 class="text-2xl font-bold text-[#111111] mb-6">Daftar Customer</h1>

    <form action="{{ route('admin.customers.index') }}" method="GET" class="mb-6">
        <div class="relative max-w-md">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari customer..." 
                   class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition">
        </div>
    </form>

    <div class="bg-white border border-[#E5E5E5] rounded-[12px] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-[#F5F5F5] border-b border-[#E5E5E5]">
                    <tr>
                        <th class="px-4 py-3 text-left font-mono uppercase tracking-wider text-xs text-gray-500">Nama</th>
                        <th class="px-4 py-3 text-left font-mono uppercase tracking-wider text-xs text-gray-500">Email</th>
                        <th class="px-4 py-3 text-left font-mono uppercase tracking-wider text-xs text-gray-500">HP</th>
                        <th class="px-4 py-3 text-center font-mono uppercase tracking-wider text-xs text-gray-500">Status</th>
                        <th class="px-4 py-3 text-right font-mono uppercase tracking-wider text-xs text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                    <tr class="border-t border-[#E5E5E5] hover:bg-[#F5F5F5]">
                        <td class="px-4 py-3 font-medium text-[#111111]">{{ $customer->name }}</td>
                        <td class="px-4 py-3 text-gray-600 font-light">{{ $customer->email }}</td>
                        <td class="px-4 py-3 text-gray-600 font-light">{{ $customer->phone }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-[10px] font-mono uppercase tracking-wider border
                                @if($customer->is_active) bg-green-50 text-green-700 border-green-200
                                @else bg-red-50 text-red-700 border-red-200 @endif">
                                {{ $customer->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.customers.show', $customer) }}" class="text-[#C1121F] hover:underline text-sm font-medium">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500 font-light">Tidak ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">{{ $customers->links() }}</div>
</div>
@endsection