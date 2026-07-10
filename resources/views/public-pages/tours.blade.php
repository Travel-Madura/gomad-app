@extends('layouts.public')

@section('title', 'Paket Wisata')
@section('meta_description', 'Jelajahi paket wisata seru dari agency terpercaya. Tour, wisata, dan liburan jadi mudah dengan GoMad.')
@section('og_image', asset('images/og-default.png'))

@section('content')

@php
    $tourService = app(\App\Services\TourService::class);
    $packages = $tourService->getPublicPackages(9);
@endphp

<div class="section mt-10">
    <div class="container-magazine">
        <div class="mb-8 mt-[-4rem] md:mt-[-6rem]">
            <h1 class="text-3xl md:text-4xl font-bold text-[#111111] mb-2">Paket Wisata</h1>
            <p class="text-gray-500 font-light text-lg">Jelajahi paket tour seru dari agency terpercaya</p>
        </div>

        @if($packages->isEmpty())
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-16 text-center shadow-sm">
            <p class="text-gray-500 text-lg font-light">Belum ada paket wisata tersedia.</p>
        </div>
        @else
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($packages as $package)
            <a href="{{ route('tour.public.show', $package->slug) }}" 
               class="block bg-white border border-[#E5E5E5] rounded-[12px] overflow-hidden shadow-sm hover:border-[#C1121F] transition-colors group">
                <div class="h-48 bg-[#F5F5F5] flex items-center justify-center overflow-hidden">
                    @if($package->cover_image)
                    <img src="{{ $package->cover_image }}" alt="{{ $package->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                    <span class="text-6xl">🏝️</span>
                    @endif
                </div>
                <div class="p-5">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-6 h-6 rounded-full bg-[#F5F5F5] flex items-center justify-center overflow-hidden">
                            @if($package->agency->logo)
                            <img src="{{ $package->agency->logo }}" class="w-full h-full object-cover">
                            @else 🏢 @endif
                        </div>
                        <span class="text-xs text-gray-500 font-light">{{ $package->agency->agency_name }}</span>
                    </div>
                    <h3 class="font-bold text-lg text-[#111111] mb-1 group-hover:text-[#C1121F] transition">{{ $package->name }}</h3>
                    <p class="text-sm text-gray-500 line-clamp-2 font-light mb-3">{{ Str::limit($package->description, 80) }}</p>
                    <div class="flex items-center gap-3 text-xs text-gray-400 font-mono uppercase tracking-wider mb-3">
                        <span>📅 {{ $package->duration_days }}H{{ $package->duration_nights }}M</span>
                        <span>📅 {{ $package->upcoming_schedules_count ?? 0 }} jadwal</span>
                    </div>
                    <div class="border-t border-[#E5E5E5] pt-3 text-right">
                        <span class="text-[#C1121F] group-hover:translate-x-1 transition-transform text-lg inline-block">→</span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        <div class="mt-8">{{ $packages->links() }}</div>
        @endif
    </div>
</div>
@endsection