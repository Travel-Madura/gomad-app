@extends('layouts.public')

@section('title', $package->name ?? 'Detail Paket Wisata')
@section('meta_description', Str::limit($package->description ?? 'Paket wisata dari ' . $package->agency->agency_name, 160))
@section('og_image', $package->cover_image ?? asset('images/og-default.png'))

@section('content')

@php
    // Ambil data mentah dari model (bisa null, string JSON, atau sudah array)
    $rawItinerary = $package->itinerary ?? [];
    $rawIncludes = $package->includes ?? [];
    $rawExcludes = $package->excludes ?? [];
    $rawGallery = $package->gallery ?? [];
    
    // Gunakan helper global ensure_array() dari app/Helpers/helpers.php
    $itinerary = ensure_array($rawItinerary);
    $includes = ensure_array($rawIncludes);
    $excludes = ensure_array($rawExcludes);
    $gallery = ensure_array($rawGallery);
    
    // Social media agency
    $socialMedia = ensure_array($package->agency->social_media ?? []);
    
    // Jadwal upcoming
    $upcomingSchedulesCount = $schedules
        ->where('is_active', true)
        ->where('departure_date', '>=', now()->toDateString())
        ->count();
@endphp

<div class="section !pt-24 md:!pt-28">
    <div class="container-magazine">
        
        {{-- Breadcrumb --}}
        <a href="{{ route('tours.public') }}" class="text-[#C1121F] text-sm mb-4 inline-block hover:underline font-medium">
            ← Semua Paket Wisata
        </a>

        {{-- Cover Image --}}
        <div class="h-64 md:h-96 bg-[#F5F5F5] rounded-[12px] overflow-hidden mb-8 border border-[#E5E5E5]">
            @if($package->cover_image)
            <img src="{{ $package->cover_image }}" alt="{{ $package->name }}" class="w-full h-full object-cover">
            @else
            <div class="w-full h-full flex items-center justify-center text-8xl">🏝️</div>
            @endif
        </div>

        <div class="grid lg:grid-cols-3 gap-8">
            
            {{-- Main Content --}}
            <div class="lg:col-span-2">
                
                {{-- Header --}}
                <div class="mb-8">
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        <span class="px-3 py-1 rounded-full text-[10px] font-mono uppercase tracking-wider bg-green-50 text-green-700 border border-green-200">
                            {{ $package->duration_days }} Hari {{ $package->duration_nights }} Malam
                        </span>
                        <span class="px-3 py-1 rounded-full text-[10px] font-mono uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-200">
                            {{ $package->stops->count() }} Destinasi
                        </span>
                        @php
                            $upcomingSchedulesCount = $package->schedules
                                ->where('is_active', true)
                                ->where('departure_date', '>=', now()->toDateString())
                                ->count();
                        @endphp
                        @if($upcomingSchedulesCount > 0)
                        <span class="px-3 py-1 rounded-full text-[10px] font-mono uppercase tracking-wider bg-purple-50 text-purple-700 border border-purple-200">
                            {{ $upcomingSchedulesCount }} Jadwal Tersedia
                        </span>
                        @endif
                    </div>
                    
                    <h1 class="text-3xl md:text-4xl font-bold text-[#111111] mb-4">{{ $package->name }}</h1>
                    
                    {{-- Agency Info --}}
                    <div class="flex items-center gap-4 p-4 bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px]">
                        <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center overflow-hidden border border-[#E5E5E5]">
                            @if($package->agency->logo)
                            <img src="{{ $package->agency->logo }}" alt="{{ $package->agency->agency_name }}" class="w-full h-full object-cover">
                            @else
                            <span class="text-xl">🏢</span>
                            @endif
                        </div>
                        <div>
                            <p class="font-semibold text-[#111111]">{{ $package->agency->agency_name }}</p>
                            <div class="flex items-center gap-2 text-sm">
                                <span class="text-yellow-500">⭐ {{ number_format($package->agency->rating, 1) }}</span>
                                <span class="text-gray-300">|</span>
                                <span class="text-gray-500 font-light">{{ $package->agency->total_bookings }} booking</span>
                                @if($package->agency->is_verified)
                                <span class="text-[#C1121F] font-mono uppercase tracking-wider text-xs">✓ Terverifikasi</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Deskripsi --}}
                <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-8 shadow-sm">
                    <h2 class="font-bold text-xl text-[#111111] mb-4">📋 Deskripsi Paket</h2>
                    <p class="text-gray-600 font-light leading-relaxed">{{ $package->description }}</p>
                </div>

                {{-- Includes & Excludes --}}
                <div class="grid md:grid-cols-2 gap-6 mb-8">
                    @if(!empty($includes))
                    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
                        <h3 class="font-bold text-lg text-[#111111] mb-4 flex items-center gap-2">
                            <span class="text-green-500">✅</span> Sudah Termasuk
                        </h3>
                        <ul class="space-y-2 text-sm text-gray-600 font-light">
                            @foreach($includes as $item)
                            <li class="flex items-start gap-2">
                                <span class="text-green-500 mt-1">✓</span> {{ $item }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    
                    @if(!empty($excludes))
                    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
                        <h3 class="font-bold text-lg text-[#111111] mb-4 flex items-center gap-2">
                            <span class="text-red-500">❌</span> Tidak Termasuk
                        </h3>
                        <ul class="space-y-2 text-sm text-gray-600 font-light">
                            @foreach($excludes as $item)
                            <li class="flex items-start gap-2">
                                <span class="text-red-500 mt-1">✗</span> {{ $item }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>

                {{-- Itinerary --}}
                @if(!empty($itinerary))
                <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-8 shadow-sm">
                    <h2 class="font-bold text-xl text-[#111111] mb-6">🗺️ Itinerary Perjalanan</h2>
                    <div class="space-y-6">
                        @foreach($itinerary as $day)
                        <div class="border-l-4 border-[#C1121F] pl-4">
                            <h4 class="font-bold text-[#111111] text-lg">
                                Hari {{ $day['day'] ?? '' }}
                                @if(!empty($day['title']))
                                <span class="text-gray-500 font-normal">— {{ $day['title'] }}</span>
                                @endif
                            </h4>
                            @if(!empty($day['activities']))
                            <ul class="mt-3 space-y-2 text-sm text-gray-600 font-light">
                                @foreach($day['activities'] as $activity)
                                <li class="flex items-start gap-2">
                                    <span class="text-[#C1121F] mt-1">•</span> {{ $activity }}
                                </li>
                                @endforeach
                            </ul>
                            @endif
                            @if(!empty($day['meals']))
                            <div class="mt-3 flex flex-wrap gap-1">
                                @foreach($day['meals'] as $meal)
                                <span class="text-[10px] font-mono uppercase tracking-wider bg-orange-50 text-orange-700 px-2 py-1 rounded-full border border-orange-200">
                                    🍽️ {{ $meal }}
                                </span>
                                @endforeach
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Gallery --}}
                @if(!empty($gallery))
                <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-8 shadow-sm">
                    <h2 class="font-bold text-xl text-[#111111] mb-6">📸 Galeri</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach($gallery as $photo)
                        <img src="{{ $photo }}" alt="Galeri {{ $package->name }}" 
                             class="w-full h-48 object-cover rounded-[12px] border border-[#E5E5E5] hover:shadow-md transition-shadow cursor-pointer"
                             onclick="window.open('{{ $photo }}', '_blank')">
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Stops / Rute --}}
                <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-8 shadow-sm">
                    <h2 class="font-bold text-xl text-[#111111] mb-6">🛑 Titik Pemberhentian</h2>
                    <div class="space-y-3">
                        @foreach($package->stops as $index => $stop)
                        <div class="flex items-center gap-4 p-4 bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px]">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0
                                {{ $index === 0 ? 'bg-green-500' : ($index === $package->stops->count() - 1 ? 'bg-red-500' : 'bg-blue-500') }}">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-[#111111]">{{ $stop->city_name }}</p>
                                @if($stop->estimated_arrival)
                                <p class="text-xs text-gray-500 font-mono">🕐 Estimasi tiba: {{ $stop->estimated_arrival }}</p>
                                @endif
                                @if($stop->notes)
                                <p class="text-xs text-gray-500 font-light mt-1">{{ $stop->notes }}</p>
                                @endif
                            </div>
                            <div class="text-right flex-shrink-0">
                                @if($index === 0)
                                <span class="text-[10px] font-mono uppercase tracking-wider text-green-600 bg-green-50 px-2 py-1 rounded-full border border-green-200">Jemput</span>
                                @elseif($index === $package->stops->count() - 1)
                                <span class="text-[10px] font-mono uppercase tracking-wider text-red-600 bg-red-50 px-2 py-1 rounded-full border border-red-200">Turun</span>
                                @else
                                <span class="text-[10px] font-mono uppercase tracking-wider text-blue-600 bg-blue-50 px-2 py-1 rounded-full border border-blue-200">Stop</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="lg:col-span-1">
                <div class="sticky top-24 space-y-6">
                    
                    {{-- Schedules Card --}}
                    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
                        <h3 class="font-bold text-lg text-[#111111] mb-4">📅 Jadwal Tersedia</h3>
                        
                        @if($schedules->isEmpty())
                        <div class="text-center py-6">
                            <div class="text-4xl mb-3">📅</div>
                            <p class="text-gray-500 font-light text-sm">Belum ada jadwal keberangkatan.</p>
                            <p class="text-gray-400 text-xs mt-1 font-light">Jadwal akan muncul saat agency menambahkannya.</p>
                        </div>
                        @else
                        <div class="space-y-3">
                            @foreach($schedules->take(5) as $schedule)
                            <div class="border border-[#E5E5E5] rounded-[12px] p-4 hover:border-[#C1121F] transition-colors">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <p class="font-bold text-[#111111] font-mono">{{ $schedule->departure_date->format('d M Y') }}</p>
                                        <p class="text-sm text-gray-500 font-mono">{{ $schedule->departure_time }}</p>
                                    </div>
                                    <span class="text-[10px] font-mono uppercase tracking-wider px-2 py-1 rounded-full border
                                        {{ $schedule->is_full ? 'bg-red-50 text-red-700 border-red-200' : 'bg-green-50 text-green-700 border-green-200' }}">
                                        {{ $schedule->is_full ? 'Penuh' : ($schedule->available_seats ?? $schedule->max_participants) . ' kursi' }}
                                    </span>
                                </div>
                                
                                <p class="text-xs text-gray-500 font-light mb-2">
                                    🚐 {{ $schedule->vehicle->plate_number ?? '-' }}
                                    @if($schedule->return_date)
                                    <br>↩️ Kembali: {{ $schedule->return_date->format('d M Y') }}
                                    @endif
                                </p>
                                
                                <div class="flex justify-between items-center border-t border-[#E5E5E5] pt-2 mt-2">
                                    <div>
                                        <p class="text-xs text-gray-400 font-light">Mulai</p>
                                        <p class="font-bold text-[#C1121F] font-mono">Rp {{ number_format($schedule->base_price, 0, ',', '.') }}</p>
                                        @if($schedule->child_price)
                                        <p class="text-[10px] text-gray-400 font-light">Anak: Rp {{ number_format($schedule->child_price, 0, ',', '.') }}</p>
                                        @endif
                                    </div>
                                    
                                    @auth
                                        @if(!$schedule->is_full)
                                        <a href="{{ route('customer.tour.booking.create', ['schedule_id' => $schedule->id]) }}" 
                                           class="bg-[#C1121F] text-white text-xs px-4 py-2 rounded-[12px] font-semibold hover:bg-[#8A0F18] transition">
                                            Booking
                                        </a>
                                        @endif
                                    @else
                                        <a href="{{ route('login') }}" 
                                           class="border border-[#C1121F] text-[#C1121F] text-xs px-4 py-2 rounded-[12px] font-semibold hover:bg-[#C1121F] hover:text-white transition">
                                            Login
                                        </a>
                                    @endauth
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>

                    {{-- CTA untuk Agency --}}
                    <div class="bg-[#C1121F] rounded-[12px] p-6 text-white text-center">
                        <div class="text-4xl mb-3">🏢</div>
                        <h3 class="font-bold text-lg mb-2">Punya Armada Travel?</h3>
                        <p class="text-sm text-white/80 font-light mb-4">
                            Daftar sebagai agency dan tawarkan paket wisata Anda di GoMad.
                        </p>
                        <a href="{{ route('register') }}" class="inline-block bg-white text-[#C1121F] px-6 py-2.5 rounded-[12px] font-semibold hover:bg-gray-100 transition">
                            Daftar Agency
                        </a>
                    </div>

                    {{-- Info Kontak Agency --}}
                    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
                        <h3 class="font-bold text-lg text-[#111111] mb-4">📞 Kontak Agency</h3>
                        <div class="space-y-3 text-sm">
                            @if($package->agency->contact_person)
                            <div class="flex items-center gap-2">
                                <span class="text-gray-400">👤</span>
                                <span class="text-[#111111] font-medium">{{ $package->agency->contact_person }}</span>
                            </div>
                            @endif
                            @if($package->agency->contact_alternate)
                            <div class="flex items-center gap-2">
                                <span class="text-gray-400">📞</span>
                                <span class="text-[#111111]">{{ $package->agency->contact_alternate }}</span>
                            </div>
                            @endif
                            @if($package->agency->email_alternate)
                            <div class="flex items-center gap-2">
                                <span class="text-gray-400">✉️</span>
                                <span class="text-[#111111]">{{ $package->agency->email_alternate }}</span>
                            </div>
                            @endif
                            @if($package->agency->address)
                            <div class="flex items-start gap-2">
                                <span class="text-gray-400 mt-0.5">📍</span>
                                <span class="text-[#111111] font-light">{{ $package->agency->address }}</span>
                            </div>
                            @endif
                        </div>
                        
                        {{-- Social Media --}}
                        @php
                            $socialMedia = ensure_array($package->agency->social_media ?? []);
                        @endphp
                        @if(!empty($socialMedia))
                        <div class="mt-4 pt-4 border-t border-[#E5E5E5]">
                            <p class="text-xs font-mono uppercase tracking-wider text-gray-400 mb-2">Sosial Media</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($socialMedia as $platform => $link)
                                @if($link)
                                <a href="{{ $link }}" target="_blank" 
                                   class="text-xs bg-[#F5F5F5] border border-[#E5E5E5] px-3 py-1.5 rounded-[12px] hover:border-[#C1121F] transition font-medium capitalize">
                                    {{ $platform }}
                                </a>
                                @endif
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection