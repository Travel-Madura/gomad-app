@extends('layouts.agency')

@section('title', 'Kendaraan')
@section('content')
<div>
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6 border-b border-[#E5E5E5] pb-3">
        <div>
            <h1 class="text-2xl font-bold text-[#111111]">🚐 Kendaraan</h1>
            <p class="text-sm text-gray-500 font-light mt-1">Kelola kendaraan, status, dan setting rental</p>
        </div>
        <a href="{{ route('agency.vehicles.create') }}" class="btn-gomad-primary text-sm inline-flex items-center gap-2 self-start rounded-[12px] px-4 py-2.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Kendaraan
        </a>
    </div>

    {{-- Stats --}}
    @php
        $totalVehicles = $vehicles->count();
        $activeVehicles = $vehicles->where('status', 'active')->count();
        $maintenanceVehicles = $vehicles->where('status', 'maintenance')->count();
        $inactiveVehicles = $vehicles->where('status', 'inactive')->count();
        $rentalVehicles = $vehicles->where('is_rental_available', true)->count();
    @endphp

    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-4 text-center shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Total</p>
            <p class="text-2xl font-bold text-[#111111] mt-1">{{ $totalVehicles }}</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-4 text-center shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">🟢 Aktif</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $activeVehicles }}</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-4 text-center shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">🟡 Maintenance</p>
            <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $maintenanceVehicles }}</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-4 text-center shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">🔴 Nonaktif</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $inactiveVehicles }}</p>
        </div>
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-4 text-center shadow-sm">
            <p class="text-[10px] font-mono uppercase tracking-wider text-gray-400">🚐 Rental</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $rentalVehicles }}</p>
        </div>
    </div>

    {{-- Filter Status --}}
    <div class="flex gap-2 mb-6 overflow-x-auto pb-2">
        <a href="{{ route('agency.vehicles.index') }}" 
           class="px-4 py-2 rounded-[12px] text-sm font-medium whitespace-nowrap border transition
                  {{ !request('status') ? 'bg-[#C1121F] text-white border-[#C1121F]' : 'bg-white text-gray-600 border-[#E5E5E5] hover:bg-[#F5F5F5]' }}">
            Semua ({{ $totalVehicles }})
        </a>
        <a href="{{ route('agency.vehicles.index', ['status' => 'active']) }}" 
           class="px-4 py-2 rounded-[12px] text-sm font-medium whitespace-nowrap border transition
                  {{ request('status') == 'active' ? 'bg-green-600 text-white border-green-600' : 'bg-white text-gray-600 border-[#E5E5E5] hover:bg-[#F5F5F5]' }}">
            🟢 Aktif ({{ $activeVehicles }})
        </a>
        <a href="{{ route('agency.vehicles.index', ['status' => 'maintenance']) }}" 
           class="px-4 py-2 rounded-[12px] text-sm font-medium whitespace-nowrap border transition
                  {{ request('status') == 'maintenance' ? 'bg-yellow-600 text-white border-yellow-600' : 'bg-white text-gray-600 border-[#E5E5E5] hover:bg-[#F5F5F5]' }}">
            🟡 Maintenance ({{ $maintenanceVehicles }})
        </a>
        <a href="{{ route('agency.vehicles.index', ['status' => 'inactive']) }}" 
           class="px-4 py-2 rounded-[12px] text-sm font-medium whitespace-nowrap border transition
                  {{ request('status') == 'inactive' ? 'bg-red-600 text-white border-red-600' : 'bg-white text-gray-600 border-[#E5E5E5] hover:bg-[#F5F5F5]' }}">
            🔴 Nonaktif ({{ $inactiveVehicles }})
        </a>
        <a href="{{ route('agency.vehicles.index', ['rental' => 1]) }}" 
           class="px-4 py-2 rounded-[12px] text-sm font-medium whitespace-nowrap border transition
                  {{ request('rental') ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-[#E5E5E5] hover:bg-[#F5F5F5]' }}">
            🚐 Rental ({{ $rentalVehicles }})
        </a>
    </div>

    {{-- Vehicle Cards --}}
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($vehicles as $vehicle)
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] overflow-hidden shadow-sm hover:border-[#C1121F] transition-colors group">
            {{-- Vehicle Image --}}
            <div class="h-40 bg-[#F5F5F5] flex items-center justify-center overflow-hidden border-b border-[#E5E5E5] relative">
                @if($vehicle->vehicle_image)
                <img src="{{ $vehicle->vehicle_image }}" alt="{{ $vehicle->plate_number }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                @else
                <span class="text-5xl text-gray-300">🚐</span>
                @endif
                
                {{-- Status Badge Overlay --}}
                <div class="absolute top-3 right-3 flex flex-wrap gap-1">
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-mono uppercase tracking-wider border
                        @if($vehicle->status == 'active') bg-green-50 text-green-700 border-green-200
                        @elseif($vehicle->status == 'maintenance') bg-yellow-50 text-yellow-700 border-yellow-200
                        @else bg-red-50 text-red-700 border-red-200 @endif">
                        @if($vehicle->status == 'active') 🟢
                        @elseif($vehicle->status == 'maintenance') 🟡
                        @else 🔴
                        @endif
                        {{ $vehicle->status_label }}
                    </span>
                    
                    @if($vehicle->is_rental_available)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-mono uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-200">
                        🚐 Rental
                    </span>
                    @endif
                </div>
            </div>
            
            <div class="p-5">
                {{-- Vehicle Info --}}
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h3 class="font-bold text-lg text-[#111111] font-mono">{{ $vehicle->plate_number }}</h3>
                        <p class="text-sm text-gray-500 font-light">{{ $vehicle->brand }} {{ $vehicle->model }} ({{ $vehicle->year }})</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-3 text-sm text-gray-500 font-mono uppercase tracking-wider mb-4">
                    <span>💺 {{ $vehicle->capacity }} seat</span>
                    <span>|</span>
                    <span class="capitalize">{{ $vehicle->type }}</span>
                </div>

                {{-- Rental Info (jika tersedia) --}}
                @if($vehicle->is_rental_available && $vehicle->status == 'active')
                <div class="bg-blue-50 border border-blue-200 rounded-[12px] p-3 mb-4">
                    <p class="text-[10px] font-mono uppercase tracking-wider text-blue-600 font-semibold mb-1">🚐 Info Rental</p>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <span class="text-blue-500 font-light">Per KM</span>
                            <p class="font-bold text-blue-800">Rp {{ number_format($vehicle->rental_price_per_km, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <span class="text-blue-500 font-light">Min/Hari</span>
                            <p class="font-bold text-blue-800">Rp {{ number_format($vehicle->rental_min_price, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <span class="text-blue-500 font-light">Supir</span>
                            <p class="font-bold text-blue-800">{{ $vehicle->rental_include_driver ? '✅ +Rp '.number_format($vehicle->rental_driver_price_per_day,0,',','.') : '❌ Tidak' }}</p>
                        </div>
                        <div>
                            <span class="text-blue-500 font-light">Maks Sewa</span>
                            <p class="font-bold text-blue-800">{{ $vehicle->rental_max_passengers }} orang</p>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Actions --}}
                <div class="flex gap-2 border-t border-[#E5E5E5] pt-4">
                    <a href="{{ route('agency.vehicles.edit', $vehicle) }}" 
                       class="flex-1 text-center border border-[#E5E5E5] text-[#111111] py-2 rounded-[12px] text-sm font-medium hover:bg-[#F5F5F5] transition">
                        ✏️ Edit
                    </a>
                    <form action="{{ route('agency.vehicles.destroy', $vehicle) }}" method="POST" class="flex-1" 
                          onsubmit="return confirm('Hapus kendaraan {{ $vehicle->plate_number }}?\n\nKendaraan akan dinonaktifkan dan dihapus.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-full border border-red-300 text-red-600 py-2 rounded-[12px] text-sm font-medium hover:bg-red-50 transition">
                            🗑️ Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white border border-[#E5E5E5] rounded-[12px] p-16 text-center shadow-sm">
            <div class="w-20 h-20 bg-[#F5F5F5] rounded-[12px] flex items-center justify-center mx-auto mb-4 border border-[#E5E5E5]">
                <span class="text-4xl">🚐</span>
            </div>
            <h2 class="text-xl font-bold text-[#111111] mb-2">Belum Ada Kendaraan</h2>
            <p class="text-gray-500 font-light mb-6 max-w-md mx-auto">
                Tambahkan kendaraan pertama Anda. Kendaraan bisa digunakan untuk travel, tour, dan disewakan.
            </p>
            <a href="{{ route('agency.vehicles.create') }}" class="btn-gomad-primary inline-flex items-center gap-2">
                <span>+</span> Tambah Kendaraan
            </a>
        </div>
        @endforelse
    </div>
</div>
@endsection