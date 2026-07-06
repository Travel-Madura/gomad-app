@extends('layouts.driver')

@section('title', 'Profil')
@section('content')
@php $user = auth()->user(); @endphp

<div class="max-w-lg mx-auto">
    <h1 class="text-2xl font-bold text-[#111111] mb-6">Profil Driver</h1>

    {{-- Info Agency --}}
    @if($user->agency)
    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-5 mb-6 shadow-sm">
        <h3 class="font-mono uppercase tracking-wider text-xs font-bold text-[#111111] mb-2">Agency</h3>
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-full bg-[#F5F5F5] border border-[#E5E5E5] flex items-center justify-center overflow-hidden">
                @if($user->agency->logo)
                <img src="{{ $user->agency->logo }}" alt="" class="w-full h-full object-cover">
                @else
                <span class="text-xl">🏢</span>
                @endif
            </div>
            <div>
                <p class="font-semibold text-[#111111]">{{ $user->agency->agency_name }}</p>
                <p class="text-xs text-gray-500 font-light">{{ $user->agency->contact_person ?? '-' }} • {{ $user->agency->contact_alternate ?? '-' }}</p>
            </div>
        </div>
    </div>
    @endif

    {{-- Form Profil --}}
    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
        <h3 class="font-mono uppercase tracking-wider text-xs font-bold text-[#111111] mb-4">Informasi Akun</h3>
        <form action="{{ route('driver.profile.update') }}" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Nama</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" required>
            </div>
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Email</label>
                <input type="email" value="{{ $user->email }}" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] bg-transparent text-gray-400 cursor-not-allowed" disabled>
            </div>
            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Nomor HP</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition">
            </div>
            <button type="submit" class="btn-gomad-primary w-full py-3 rounded-[12px]">Simpan</button>
        </form>
    </div>
</div>
@endsection