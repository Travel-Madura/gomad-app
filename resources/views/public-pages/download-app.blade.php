@extends('layouts.public')

@section('title', 'Download App')
@section('meta_description', 'Download aplikasi GoMad di Play Store dan App Store. Booking travel jadi lebih mudah.')
@section('og_image', asset('images/og-download.png'))

@section('content')
<div class="section mb-20 mt-10">
    <div class="container-magazine">
        <div class="max-w-3xl mx-auto text-center">
            <div class="mb-8 mt-[-4rem] md:mt-[-6rem]">
                <h1 class="text-3xl md:text-4xl font-bold text-[#111111] mb-4">Download Aplikasi GoMad</h1>
                <p class="text-gray-500 font-light text-lg">Dapatkan pengalaman terbaik dengan aplikasi GoMad.</p>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center mb-12">
                <a href="#" class="bg-[#111111] text-white px-8 py-4 rounded-[12px] inline-flex items-center gap-4 hover:bg-[#111111]/80 transition justify-center">
                    <span class="text-3xl font-mono">▶</span>
                    <div class="text-left"><div class="text-[10px] font-mono uppercase tracking-wider opacity-80">GET IT ON</div><div class="text-lg font-bold">Google Play</div></div>
                </a>
                <a href="#" class="bg-[#111111] text-white px-8 py-4 rounded-[12px] inline-flex items-center gap-4 hover:bg-[#111111]/80 transition justify-center">
                    <span class="text-3xl font-mono">🍎</span>
                    <div class="text-left"><div class="text-[10px] font-mono uppercase tracking-wider opacity-80">DOWNLOAD ON</div><div class="text-lg font-bold">App Store</div></div>
                </a>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                <div class="card-gomad p-6 border-[#E5E5E5] hover:border-[#C1121F] transition-colors">
                    <div class="w-12 h-12 bg-[#F5F5F5] rounded-[12px] flex items-center justify-center text-xl mx-auto mb-3">🔍</div>
                    <h4 class="font-semibold text-[#111111] text-sm">Cari Jadwal</h4>
                </div>
                <div class="card-gomad p-6 border-[#E5E5E5] hover:border-[#C1121F] transition-colors">
                    <div class="w-12 h-12 bg-[#F5F5F5] rounded-[12px] flex items-center justify-center text-xl mx-auto mb-3">📅</div>
                    <h4 class="font-semibold text-[#111111] text-sm">Booking Mudah</h4>
                </div>
                <div class="card-gomad p-6 border-[#E5E5E5] hover:border-[#C1121F] transition-colors">
                    <div class="w-12 h-12 bg-[#F5F5F5] rounded-[12px] flex items-center justify-center text-xl mx-auto mb-3">💳</div>
                    <h4 class="font-semibold text-[#111111] text-sm">Bayar Online</h4>
                </div>
                <div class="card-gomad p-6 border-[#E5E5E5] hover:border-[#C1121F] transition-colors">
                    <div class="w-12 h-12 bg-[#F5F5F5] rounded-[12px] flex items-center justify-center text-xl mx-auto mb-3">🎫</div>
                    <h4 class="font-semibold text-[#111111] text-sm">E-Ticket</h4>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection