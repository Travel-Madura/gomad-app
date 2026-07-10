@extends('layouts.public')

@section('title', 'Beranda')
@section('meta_description', 'GoMad - Solusi transportasi Anda. Booking travel antar kota dengan mudah, dijemput di rumah, dan diantar ke tujuan.')
@section('og_image', asset('images/og-home.jpg'))

@section('content')
@php
    $cities = \App\Models\RouteStop::select('city_name')->distinct()->orderBy('city_name')->get();
    $popularRoutes = \App\Models\Route::withCount('schedules')->orderByDesc('schedules_count')->limit(6)->get();
    
    $mapWarungs = \App\Models\PaymentAgent::where('is_verified', true)
        ->whereNotNull('latitude')
        ->whereNotNull('longitude')
        ->get()
        ->map(function($w) {
            return [
                'agent_name' => $w->agent_name,
                'address' => $w->address,
                'latitude' => (float) $w->latitude,
                'longitude' => (float) $w->longitude,
                'owner_phone' => $w->owner_phone,
                'maps_link' => $w->maps_link,
            ];
        });
@endphp

{{-- HERO SECTION --}}
<section class="relative bg-[#C1121F] overflow-hidden py-12 md:py-20 min-h-[40vh] md:min-h-[60vh] flex items-center justify-center">
    
    <div class="container-magazine relative z-10 w-full flex justify-center">
        <div class="text-white space-y-6 md:space-y-8 max-w-3xl text-center mt-[-4rem] md:mt-[-6rem]">
            
            <h1 class="text-4xl md:text-7xl lg:text-8xl font-bold tracking-tight leading-[0.9]">
                Travel<br>
                <span class="text-white/70 italic">Door to Door</span><br>
                Service
            </h1>
            
            <div class="text-base md:text-lg text-white/100 max-w-md leading-relaxed font-light mx-auto min-h-[3rem] md:min-h-[4rem] flex items-center justify-center"
                x-data="{
                    texts: [
                        'Tak perlu datang ke terminal. Gomad siap menjemput.',
                        '{{ \App\Models\PlatformSetting::getValue('app_tagline', 'Mobilitas antar kota, tanpa batas jarak.') }}',
                        'Pesan sekarang, sampai ke rumah tanpa ribet.'
                    ],
                    currentIndex: 0,
                    interval: null,
                    startRotation() {
                        this.interval = setInterval(() => {
                            this.currentIndex = (this.currentIndex + 1) % this.texts.length;
                        }, 4000);
                    },
                    stopRotation() {
                        clearInterval(this.interval);
                    }
                }"
                x-init="startRotation()"
                @mouseenter="stopRotation()"
                @mouseleave="startRotation()">
                
                <span x-show="true" 
                    x-text="texts[currentIndex]"
                    x-transition:enter="transition ease-in-out duration-500 transform"
                    x-transition:enter-start="opacity-0 translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in-out duration-500 transform"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-2">
                </span>
            </div>
        </div>
    </div>
    
    <div class="absolute bottom-0 left-0 right-0 h-px bg-white/20"></div>
</section>

{{-- ═══════════════════════════════════════════════ --}}
{{-- SEARCH BAR — 3 TAB (RESPONSIVE) --}}
{{-- ═══════════════════════════════════════════════ --}}
<section class="-mt-16 relative z-20 container-magazine px-4 md:px-0">
    <div class="card-gomad shadow-xl border-0 bg-white">
        
        {{-- TAB SWITCHER — Compact di mobile --}}
        <div class="flex border-b border-[#E5E5E5] overflow-x-auto scrollbar-hide" id="searchTabs">
            <button onclick="switchSearchTab('travel')" 
                    class="tab-btn flex-shrink-0 px-3 md:px-4 py-2.5 md:py-3 text-xs md:text-sm font-semibold border-b-2 border-[#C1121F] text-[#C1121F] transition-colors whitespace-nowrap">
                🎫 Travel
            </button>
            <button onclick="switchSearchTab('tour')" 
                    class="tab-btn flex-shrink-0 px-3 md:px-4 py-2.5 md:py-3 text-xs md:text-sm font-semibold border-b-2 border-transparent text-gray-500 hover:text-[#111111] transition-colors whitespace-nowrap">
                🏝️ Wisata
            </button>
            <button onclick="switchSearchTab('rental')" 
                    class="tab-btn flex-shrink-0 px-3 md:px-4 py-2.5 md:py-3 text-xs md:text-sm font-semibold border-b-2 border-transparent text-gray-500 hover:text-[#111111] transition-colors whitespace-nowrap">
                🚐 Sewa
            </button>
        </div>

        <div class="p-3 md:p-6">
            
            {{-- ═══════════════════════════════════════════════ --}}
            {{-- FORM TRAVEL --}}
            {{-- ═══════════════════════════════════════════════ --}}
            <form id="form-travel" action="{{ route('search') }}" method="GET" class="grid grid-cols-2 lg:grid-cols-5 gap-3">
                <div>
                    <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Asal</label>
                    <select name="origin" class="w-full px-0 py-1.5 md:py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-sm font-medium text-[#111111] appearance-none cursor-pointer">
                        <option value="">Semua Kota</option>
                        @foreach($cities as $city)<option value="{{ $city->city_name }}">{{ $city->city_name }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Tujuan</label>
                    <select name="destination" class="w-full px-0 py-1.5 md:py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-sm font-medium text-[#111111] appearance-none cursor-pointer">
                        <option value="">Semua Kota</option>
                        @foreach($cities as $city)<option value="{{ $city->city_name }}">{{ $city->city_name }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Tanggal</label>
                    <input type="date" name="date" class="w-full px-0 py-1.5 md:py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-sm font-medium text-[#111111]">
                </div>
                <div>
                    <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Kelas</label>
                    <select name="travel_class" class="w-full px-0 py-1.5 md:py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-sm font-medium text-[#111111] appearance-none cursor-pointer">
                        <option value="">Semua</option>
                        <option value="economy">Ekonomi</option>
                        <option value="premium">Premium</option>
                        <option value="charter">Charter</option>
                    </select>
                </div>
                <div class="col-span-2 lg:col-span-1 flex items-end">
                    <button type="submit" class="w-full btn-gomad-primary text-center py-2 md:py-2.5 text-sm rounded-[12px]">Cari Jadwal</button>
                </div>
            </form>

            {{-- ═══════════════════════════════════════════════ --}}
            {{-- FORM TOUR (Hidden) --}}
            {{-- ═══════════════════════════════════════════════ --}}
            <form id="form-tour" action="{{ route('tours.public') }}" method="GET" class="grid grid-cols-2 lg:grid-cols-4 gap-3" style="display:none;">
                <div>
                    <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Kata Kunci</label>
                    <input type="text" name="search" placeholder="Nama paket..." class="w-full px-0 py-1.5 md:py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-sm font-medium text-[#111111]">
                </div>
                <div>
                    <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Durasi</label>
                    <select name="duration" class="w-full px-0 py-1.5 md:py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-sm font-medium text-[#111111]">
                        <option value="">Semua</option>
                        <option value="1">1 Hari</option>
                        <option value="2">2 Hari</option>
                        <option value="3">3+ Hari</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Budget</label>
                    <select name="max_budget" class="w-full px-0 py-1.5 md:py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-sm font-medium text-[#111111]">
                        <option value="">Semua</option>
                        <option value="500000">Rp 500rb</option>
                        <option value="1000000">Rp 1jt</option>
                        <option value="2000000">Rp 2jt</option>
                    </select>
                </div>
                <div class="col-span-2 lg:col-span-1 flex items-end">
                    <button type="submit" class="w-full bg-purple-600 text-white text-center py-2 md:py-2.5 text-sm rounded-[12px] hover:bg-purple-700 transition font-semibold">Cari Wisata</button>
                </div>
            </form>

            {{-- ═══════════════════════════════════════════════ --}}
            {{-- FORM RENTAL (Hidden) --}}
            {{-- ═══════════════════════════════════════════════ --}}
            <form id="form-rental" action="{{ route('rental.public') }}" method="GET" class="grid grid-cols-2 lg:grid-cols-5 gap-3" style="display:none;">
                <div>
                    <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Kapasitas</label>
                    <select name="passengers" class="w-full px-0 py-1.5 md:py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-sm font-medium text-[#111111]">
                        <option value="">Semua</option>
                        <option value="4">4+ Orang</option>
                        <option value="8">8+ Orang</option>
                        <option value="12">12+ Orang</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Bulan</label>
                    <select name="month" class="w-full px-0 py-1.5 md:py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-sm font-medium text-[#111111]">
                        @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ now()->month == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->locale('id')->monthName }}
                        </option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Supir</label>
                    <select name="include_driver" class="w-full px-0 py-1.5 md:py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-sm font-medium text-[#111111]">
                        <option value="">Semua</option>
                        <option value="1">Include Supir</option>
                        <option value="0">Tanpa Supir</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Agency</label>
                    <select name="agency_id" class="w-full px-0 py-1.5 md:py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-sm font-medium text-[#111111]">
                        <option value="">Semua</option>
                        @foreach(\App\Models\Agency::where('is_verified', true)->orderBy('agency_name')->get() as $agency)
                        <option value="{{ $agency->id }}">{{ $agency->agency_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-2 lg:col-span-1 flex items-end">
                    <button type="submit" class="w-full bg-green-600 text-white text-center py-2 md:py-2.5 text-sm rounded-[12px] hover:bg-green-700 transition font-semibold">Cari Kendaraan</button>
                </div>
            </form>
        </div>
    </div>
</section>

{{-- RUTE POPULER --}}
<section class="section container-magazine border-b border-[#E5E5E5]">
    <div class="flex items-center gap-4 mb-12">
        <div class="h-px w-12 bg-[#C1121F]"></div>
        <h2 class="text-3xl font-bold tracking-tight text-[#111111]">Rute Populer</h2>
    </div>

    @if(isset($popularRoutes) && $popularRoutes->isNotEmpty())
    <div class="relative w-full overflow-hidden group/slider">
        <div class="flex gap-4 md:gap-6 animate-scroll hover:pause">
            
            @foreach($popularRoutes as $route)
            <div class="card-gomad overflow-hidden flex-shrink-0 flex flex-row w-[calc(100%-1rem)] sm:w-[calc(50%-0.75rem)] lg:w-[calc(33.333%-1rem)] cursor-pointer p-0 h-32 md:h-40 group/card">
                <div class="w-1/3 h-full overflow-hidden bg-[#F5F5F5] flex-shrink-0">
                    @if($route->photo)
                    <img src="{{ $route->photo }}" alt="{{ $route->route_name }}" class="w-full h-full object-cover transition-transform duration-500 group-hover/card:scale-105">
                    @else
                    <div class="w-full h-full bg-gradient-to-br from-[#C1121F]/10 to-[#C1121F]/5 flex items-center justify-center text-3xl">🗺️</div>
                    @endif
                </div>
                
                <div class="w-2/3 p-4 flex flex-col justify-between">
                    <div>
                        <h3 class="font-bold text-[#111111] text-base md:text-lg truncate">{{ $route->route_name }}</h3>
                        <p class="text-xs md:text-sm text-gray-500 truncate">{{ $route->origin_city }} → {{ $route->destination_city }}</p>
                    </div>
                    <div class="flex justify-between items-center border-t border-[#E5E5E5] pt-2 mt-2">
                        <p class="text-[10px] md:text-xs font-mono uppercase tracking-wider text-[#C1121F] font-medium">{{ $route->schedules_count ?? 0 }} jadwal</p>
                        <span class="text-[#C1121F] group-hover/card:translate-x-1 transition-transform">→</span>
                    </div>
                </div>
            </div>
            @endforeach

            @foreach($popularRoutes as $route)
            <div class="card-gomad overflow-hidden flex-shrink-0 flex flex-row w-[calc(100%-1rem)] sm:w-[calc(50%-0.75rem)] lg:w-[calc(33.333%-1rem)] cursor-pointer p-0 h-32 md:h-40 group/card">
                <div class="w-1/3 h-full overflow-hidden bg-[#F5F5F5] flex-shrink-0">
                    @if($route->photo)
                    <img src="{{ $route->photo }}" alt="{{ $route->route_name }}" class="w-full h-full object-cover transition-transform duration-500 group-hover/card:scale-105">
                    @else
                    <div class="w-full h-full bg-gradient-to-br from-[#C1121F]/10 to-[#C1121F]/5 flex items-center justify-center text-3xl">🗺️</div>
                    @endif
                </div>
                <div class="w-2/3 p-4 flex flex-col justify-between">
                    <div>
                        <h3 class="font-bold text-[#111111] text-base md:text-lg truncate">{{ $route->route_name }}</h3>
                        <p class="text-xs md:text-sm text-gray-500 truncate">{{ $route->origin_city }} → {{ $route->destination_city }}</p>
                    </div>
                    <div class="flex justify-between items-center border-t border-[#E5E5E5] pt-2 mt-2">
                        <p class="text-[10px] md:text-xs font-mono uppercase tracking-wider text-[#C1121F] font-medium">{{ $route->schedules_count ?? 0 }} jadwal</p>
                        <span class="text-[#C1121F] group-hover/card:translate-x-1 transition-transform">→</span>
                    </div>
                </div>
            </div>
            @endforeach

        </div>
    </div>
    @else
    <div class="text-center py-12 text-gray-400 font-mono uppercase tracking-widest">Belum ada rute populer saat ini.</div>
    @endif
</section>

{{-- LAYANAN --}}
<section id="services" class="section container-magazine">
    <div class="flex items-center gap-4 mb-12">
        <div class="h-px w-12 bg-[#C1121F]"></div>
        <h2 class="text-3xl font-bold tracking-tight text-[#111111]">Layanan GoMad</h2>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Ekonomi --}}
        <div class="card-gomad flex flex-col gap-4 group">
            <div class="text-4xl text-[#C1121F]">🚐</div>
            <h3 class="text-xl font-bold text-[#111111]">Ekonomi</h3>
            <p class="text-sm text-gray-500 leading-relaxed">Mobil 8 seat, bagasi 15kg. Solusi mobilitas harian terpercaya.</p>
            <div class="mt-auto pt-4 border-t border-[#E5E5E5] group-hover:border-[#C1121F] transition-colors flex justify-between items-center">
                <span class="text-xs font-mono uppercase">Mulai 50k</span>
                <span class="text-[#C1121F] group-hover:translate-x-2 transition-transform">→</span>
            </div>
        </div>
        
        {{-- Premium --}}
        <div class="card-gomad flex flex-col gap-4 group">
            <div class="text-4xl text-[#C1121F]">🚗</div>
            <h3 class="text-xl font-bold text-[#111111]">Premium</h3>
            <p class="text-sm text-gray-500 leading-relaxed">8 seat strict, bagasi 20kg. Kenyamanan ekstra perjalanan bisnis.</p>
            <div class="mt-auto pt-4 border-t border-[#E5E5E5] group-hover:border-[#C1121F] transition-colors flex justify-between items-center">
                <span class="text-xs font-mono uppercase">Mulai 80k</span>
                <span class="text-[#C1121F] group-hover:translate-x-2 transition-transform">→</span>
            </div>
        </div>
        
        {{-- Tour --}}
        <div class="card-gomad flex flex-col gap-4 group">
            <div class="text-4xl text-[#C1121F]">🏝️</div>
            <h3 class="text-xl font-bold text-[#111111]">Paket Wisata</h3>
            <p class="text-sm text-gray-500 leading-relaxed">Tour multi-hari dengan itinerary lengkap. Semua sudah termasuk.</p>
            <div class="mt-auto pt-4 border-t border-[#E5E5E5] group-hover:border-[#C1121F] transition-colors flex justify-between items-center">
                <span class="text-xs font-mono uppercase">Lihat Paket</span>
                <span class="text-[#C1121F] group-hover:translate-x-2 transition-transform">→</span>
            </div>
        </div>
        
        {{-- Rental --}}
        <div class="card-gomad flex flex-col gap-4 group">
            <div class="text-4xl text-[#C1121F]">🚐</div>
            <h3 class="text-xl font-bold text-[#111111]">Sewa Kendaraan</h3>
            <p class="text-sm text-gray-500 leading-relaxed">Sewa kendaraan + supir untuk keperluan Anda. Harga per KM, fleksibel.</p>
            <div class="mt-auto pt-4 border-t border-[#E5E5E5] group-hover:border-[#C1121F] transition-colors flex justify-between items-center">
                <span class="text-xs font-mono uppercase">Cari Kendaraan</span>
                <span class="text-[#C1121F] group-hover:translate-x-2 transition-transform">→</span>
            </div>
        </div>
    </div>
</section>

{{-- PEMBAYARAN & PETA --}}
<section class="section bg-[#F5F5F5]">
    <div class="container-magazine grid md:grid-cols-2 gap-12">
        <div>
            <h2 class="text-3xl font-bold text-[#111111] mb-6">Metode Pembayaran</h2>
            <p class="text-gray-500 mb-8">Didukung oleh sistem pembayaran modern dan ekosistem warung lokal.</p>
            <div class="grid grid-cols-4 gap-4 opacity-70 grayscale hover:grayscale-0 transition-all duration-500">
                <div class="bg-white p-3 rounded-[12px] shadow-sm border border-[#E5E5E5] flex items-center justify-center h-12 font-mono text-sm text-[#111111] font-semibold">BCA</div>
                <div class="bg-white p-3 rounded-[12px] shadow-sm border border-[#E5E5E5] flex items-center justify-center h-12 font-mono text-sm text-[#111111] font-semibold">OVO</div>
                <div class="bg-white p-3 rounded-[12px] shadow-sm border border-[#E5E5E5] flex items-center justify-center h-12 font-mono text-sm text-[#111111] font-semibold">DANA</div>
                <div class="bg-white p-3 rounded-[12px] shadow-sm border border-[#E5E5E5] flex items-center justify-center h-12 font-mono text-sm text-[#111111] font-semibold">QRIS</div>
                <div class="bg-white p-3 rounded-[12px] shadow-sm border border-[#E5E5E5] flex items-center justify-center h-12 font-mono text-sm text-[#111111] font-semibold col-span-2">Transfer Bank</div>
                <div class="bg-white p-3 rounded-[12px] shadow-sm border border-[#E5E5E5] flex items-center justify-center h-12 font-mono text-sm text-[#111111] font-semibold col-span-2">Bayar ke Supir (COD)</div>
            </div>
        </div>
        <div>
            <div id="homeWarungMap" style="height: 300px; z-index: 1;" class="rounded-[12px] border border-[#E5E5E5] overflow-hidden"></div>
            <p class="text-xs text-center mt-2 text-gray-400 font-mono uppercase tracking-wider">{{ $mapWarungs->count() }}+ Warung GoMad tersebar di Madura</p>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
// ─── SEARCH TAB SWITCHER ─────────────────────────────
function switchSearchTab(type) {
    // Update tab buttons
    document.querySelectorAll('#searchTabs .tab-btn').forEach(btn => {
        btn.classList.remove('border-[#C1121F]', 'text-[#C1121F]');
        btn.classList.add('border-transparent', 'text-gray-500');
    });
    event.target.classList.add('border-[#C1121F]', 'text-[#C1121F]');
    event.target.classList.remove('border-transparent', 'text-gray-500');
    
    // Show/hide forms
    document.getElementById('form-travel').style.display = type === 'travel' ? 'grid' : 'none';
    document.getElementById('form-tour').style.display = type === 'tour' ? 'grid' : 'none';
    document.getElementById('form-rental').style.display = type === 'rental' ? 'grid' : 'none';
}

// ─── WARUNG MAP ───────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    var mapEl = document.getElementById('homeWarungMap');
    if (!mapEl) return;

    var map = L.map('homeWarungMap').setView([-7.1, 113.2], 8);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap',
        maxZoom: 18,
    }).addTo(map);

    var warungs = @json($mapWarungs);
    if (warungs.length === 0) return;

    var bounds = L.latLngBounds();
    
    warungs.forEach(function(w) {
        var lat = parseFloat(w.latitude);
        var lng = parseFloat(w.longitude);
        if (isNaN(lat) || isNaN(lng)) return;
        
        var warungIcon = L.divIcon({
            html: '<div style="background:#C1121F;color:white;width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:16px;border:3px solid white;box-shadow:0 2px 6px rgba(0,0,0,0.3);">🏪</div>',
            className: '',
            iconSize: [32, 32],
            iconAnchor: [16, 16],
        });
        
        L.marker([lat, lng], { icon: warungIcon })
            .addTo(map)
            .bindPopup(
                '<div style="min-width:160px;font-family:Geist Sans, sans-serif;">' +
                    '<strong style="color:#111111;">' + (w.agent_name || '') + '</strong><br>' +
                    '<span style="font-size:12px;color:#666;">' + (w.address || '') + '</span><br>' +
                    '<span style="font-size:12px;">📞 ' + (w.owner_phone || '-') + '</span><br>' +
                    '<a href="' + (w.maps_link || 'https://www.google.com/maps?q=' + lat + ',' + lng) + '" target="_blank" style="display:inline-block;margin-top:6px;background:#C1121F;color:white;padding:6px 12px;border-radius:8px;text-decoration:none;font-size:12px;font-weight:600;">🗺️ Google Maps</a>' +
                '</div>'
            );
        
        bounds.extend([lat, lng]);
    });
    
    map.fitBounds(bounds, { padding: [30, 30], maxZoom: 12 });
});
</script>
@endpush