@extends('layouts.customer')

@section('title', 'Detail Booking Tour')
@section('content')

<div class="max-w-3xl mx-auto px-4 py-8">
    @if(isset($booking))
    
    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- STATUS BANNER --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div class="rounded-[12px] p-4 mb-6 text-center border
        @if(in_array($booking->status, ['paid', 'on_going', 'completed'])) bg-green-50 border-green-200
        @elseif(in_array($booking->status, ['pending', 'confirmed'])) bg-yellow-50 border-yellow-200
        @elseif($booking->status == 'cancelled') bg-red-50 border-red-200
        @else bg-[#F5F5F5] border-[#E5E5E5] @endif">
        <div class="text-4xl mb-2">
            @if(in_array($booking->status, ['paid', 'on_going', 'completed'])) ✅
            @elseif(in_array($booking->status, ['pending', 'confirmed'])) ⏳
            @elseif($booking->status == 'cancelled') ❌
            @else 🏝️
            @endif
        </div>
        <h2 class="text-xl font-bold text-[#111111]">{{ $booking->status_label }}</h2>
        <p class="text-sm mt-1 font-light text-gray-600">
            @if($booking->status == 'pending' && !$booking->payment)
                Silakan pilih promo dan metode pembayaran di bawah ini.
            @elseif($booking->status == 'confirmed' && $booking->payment && $booking->payment->payment_type == 'cod')
                🚗 Pembayaran COD - Bayar tunai ke sopir saat penjemputan.
            @elseif($booking->payment && $booking->payment->payment_type == 'midtrans' && $booking->payment->status == 'pending')
                Silakan selesaikan pembayaran online.
            @elseif($booking->status == 'paid')
                Booking sudah dikonfirmasi. Selamat menikmati perjalanan!
            @elseif($booking->status == 'completed')
                Tour telah selesai. Terima kasih!
            @elseif($booking->status == 'cancelled')
                Booking ini telah dibatalkan.
            @endif
        </p>
    </div>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- BOOKING INFO CARD --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-6 shadow-sm">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h1 class="text-2xl font-bold font-mono text-[#111111]">{{ $booking->booking_code }}</h1>
                <p class="text-gray-500 font-light">{{ $booking->group_name }}</p>
            </div>
            <p class="text-2xl font-bold text-[#C1121F] font-mono">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
        </div>

        <div class="border-t border-[#E5E5E5] pt-4">
            <h3 class="font-mono uppercase tracking-wider text-xs font-bold text-[#111111] mb-3">Detail Booking</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3">
                    <span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Paket Tour</span>
                    <p class="font-semibold text-[#111111]">{{ $booking->tourSchedule->tourPackage->name ?? '-' }}</p>
                </div>
                <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3">
                    <span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Tanggal</span>
                    <p class="font-semibold text-[#111111]">{{ $booking->tourSchedule->departure_date->format('d M Y') }} {{ $booking->tourSchedule->departure_time }}</p>
                </div>
                <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3">
                    <span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Agency</span>
                    <p class="font-semibold text-[#111111]">{{ $booking->tourSchedule->tourPackage->agency->agency_name ?? '-' }}</p>
                </div>
                <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3">
                    <span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Peserta</span>
                    <p class="font-semibold text-[#111111]">{{ $booking->total_participants }} orang ({{ $booking->total_adults }} Dewasa, {{ $booking->total_children }} Anak)</p>
                </div>
                <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3">
                    <span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">📍 Jemput</span>
                    <p class="font-medium text-xs text-[#111111]">{{ $booking->pickup_address }}</p>
                </div>
                <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3">
                    <span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">🚐 Kendaraan</span>
                    <p class="font-semibold text-[#111111]">{{ $booking->tourSchedule->vehicle->plate_number ?? '-' }}</p>
                </div>
                @if($booking->tourSchedule->driver_id)
                <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3">
                    <span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">👨‍✈️ Driver</span>
                    <p class="font-semibold text-[#111111]">{{ $booking->tourSchedule->driver->name ?? '-' }}</p>
                </div>
                @endif
                @if($booking->tourSchedule->return_date)
                <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3">
                    <span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">↩️ Kembali</span>
                    <p class="font-semibold text-[#111111]">{{ $booking->tourSchedule->return_date->format('d M Y') }} {{ $booking->tourSchedule->return_time ?? '' }}</p>
                </div>
                @endif
            </div>

            {{-- RINCIAN HARGA --}}
            <div class="mt-4 bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-4">
                <h4 class="font-mono uppercase tracking-wider text-xs font-bold mb-3">Rincian Pembayaran</h4>
                <div class="space-y-2 text-sm">
                    
                    {{-- Harga Tiket Dewasa --}}
                    <div class="flex justify-between">
                        <span class="text-gray-500 font-light">
                            Harga Tiket ({{ $booking->total_adults }} × Rp {{ number_format($booking->tourSchedule->base_price, 0, ',', '.') }})
                        </span>
                        <span class="font-medium text-[#111111]">
                            Rp {{ number_format($booking->total_adults * $booking->tourSchedule->base_price, 0, ',', '.') }}
                        </span>
                    </div>
                    
                    {{-- Harga Tiket Anak --}}
                    @if($booking->total_children > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-500 font-light">
                            Harga Tiket Anak ({{ $booking->total_children }} × Rp {{ number_format($booking->tourSchedule->child_price ?? $booking->tourSchedule->base_price, 0, ',', '.') }})
                        </span>
                        <span class="font-medium text-[#111111]">
                            Rp {{ number_format($booking->total_children * ($booking->tourSchedule->child_price ?? $booking->tourSchedule->base_price), 0, ',', '.') }}
                        </span>
                    </div>
                    @endif
                    
                    {{-- Subtotal --}}
                    <div class="flex justify-between border-t border-[#E5E5E5] pt-2">
                        <span class="text-gray-500 font-light">Subtotal Harga Tiket</span>
                        <span class="font-semibold text-[#111111]">Rp {{ number_format($booking->base_price, 0, ',', '.') }}</span>
                    </div>
                    
                    {{-- Biaya Layanan --}}
                    <div class="flex justify-between">
                        <span class="text-gray-500 font-light">Biaya Layanan</span>
                        <span class="font-medium text-[#111111]">Rp {{ number_format($booking->service_fee, 0, ',', '.') }}</span>
                    </div>
                    
                    {{-- Biaya Platform --}}
                    <div class="flex justify-between">
                        <span class="text-gray-500 font-light">
                            Biaya Platform ({{ \App\Models\PlatformSetting::getValue('platform_fee_percent', 3) }}%)
                        </span>
                        <span class="font-medium text-[#111111]">Rp {{ number_format($booking->platform_fee, 0, ',', '.') }}</span>
                    </div>
                    
                    {{-- Diskon Promo --}}
                    @if($booking->discount_amount > 0)
                    <div class="flex justify-between text-[#C1121F] font-medium">
                        <span>
                            🎫 Diskon Promo
                            @php
                                $promoUsage = \App\Models\TourPromoUsage::where('tour_booking_id', $booking->id)->first();
                            @endphp
                            @if($promoUsage && $promoUsage->tourPromo)
                            <span class="text-xs font-light">({{ $promoUsage->tourPromo->name }})</span>
                            @endif
                        </span>
                        <span class="font-bold">-Rp {{ number_format($booking->discount_amount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    
                    <hr class="border-[#E5E5E5] my-2">
                    
                    {{-- TOTAL --}}
                    <div class="flex justify-between text-base font-bold">
                        <span>TOTAL</span>
                        <span class="text-[#C1121F] font-mono text-lg">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- DAFTAR PESERTA --}}
            <div class="mt-4">
                <h4 class="font-mono uppercase tracking-wider text-xs font-semibold text-[#111111] mb-2">Daftar Peserta</h4>
                <div class="space-y-1">
                    @foreach($booking->participants as $p)
                    <div class="flex justify-between items-center text-sm py-1.5 px-3 bg-[#F5F5F5] border border-[#E5E5E5] rounded-lg">
                        <div>
                            <span class="text-[#111111]">{{ $p->participant_name }}</span>
                            <span class="text-xs font-mono ml-2 {{ $p->participant_type == 'adult' ? 'text-blue-600' : 'text-orange-600' }}">
                                {{ $p->participant_type == 'adult' ? 'Dewasa' : 'Anak' }}
                            </span>
                        </div>
                        <span class="text-gray-400 text-xs font-light">{{ $p->id_number ?? '-' }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- SPECIAL REQUESTS --}}
            @if($booking->special_requests)
            <div class="mt-4 bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-4">
                <h4 class="font-mono uppercase tracking-wider text-xs font-semibold text-[#111111] mb-2">📝 Permintaan Khusus</h4>
                <p class="text-sm text-gray-600 font-light">{{ $booking->special_requests }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- PILIH PROMO — SEBELUM MEMILIH METODE PEMBAYARAN --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    @if($booking->status == 'pending' && !$booking->payment)
    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-6 shadow-sm">
        <h2 class="text-lg font-bold text-[#111111] mb-2">🎫 Pilih Promo (Opsional)</h2>
        <p class="text-sm text-gray-500 font-light mb-4">Pilih promo terlebih dahulu. Metode pembayaran akan menyesuaikan dengan promo yang dipilih.</p>
        
        @php
            $tourPromoService = app(\App\Services\TourPromoService::class);
            $availablePromos = $tourPromoService->getAvailablePromosForCustomer(auth()->user(), $booking->tour_schedule_id);
            $currentPromoUsage = \App\Models\TourPromoUsage::where('tour_booking_id', $booking->id)->first();
            $currentPromoId = $currentPromoUsage ? $currentPromoUsage->tour_promo_id : null;
        @endphp

        <form id="promoForm" action="{{ route('customer.tour.apply-promo', $booking) }}" method="POST">
            @csrf
            
            @if($availablePromos->isNotEmpty())
            <div class="space-y-3 mb-4">
                {{-- Tanpa Promo --}}
                <label class="flex items-center gap-4 p-4 border-2 rounded-[12px] cursor-pointer transition
                    {{ !$currentPromoId ? 'border-[#C1121F] bg-[#C1121F]/5' : 'border-[#E5E5E5] hover:border-[#C1121F]' }}">
                    <input type="radio" name="promo_id" value="" class="w-5 h-5 text-[#C1121F] focus:ring-[#C1121F]"
                        {{ !$currentPromoId ? 'checked' : '' }} onchange="this.form.submit()">
                    <div class="flex-1">
                        <span class="font-semibold text-[#111111] block">Tanpa Promo</span>
                        <span class="text-xs text-gray-500 font-light">Harga normal tanpa diskon</span>
                    </div>
                </label>

                @foreach($availablePromos as $promo)
                @php
                    $promoMethods = $promo->getApplicablePaymentMethodsArray();
                    $methodLabels = [];
                    if (in_array('midtrans', $promoMethods)) $methodLabels[] = '💳 Online';
                    if (in_array('cash', $promoMethods)) $methodLabels[] = '🏪 Warung';
                    if (in_array('cod', $promoMethods)) $methodLabels[] = '🚗 COD';
                    $allMethods = count($promoMethods) >= 3;
                @endphp
                <label class="flex items-center gap-4 p-4 border-2 rounded-[12px] cursor-pointer transition
                    {{ $currentPromoId == $promo->id ? 'border-[#C1121F] bg-[#C1121F]/5' : 'border-[#E5E5E5] hover:border-[#C1121F]' }}">
                    <input type="radio" name="promo_id" value="{{ $promo->id }}" class="w-5 h-5 text-[#C1121F] focus:ring-[#C1121F]"
                        {{ $currentPromoId == $promo->id ? 'checked' : '' }} onchange="this.form.submit()">
                    <div class="flex-1">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="font-semibold text-[#111111] block">
                                    {{ $promo->name }}
                                    <span class="text-[10px] font-mono uppercase tracking-wider ml-2 px-2 py-0.5 rounded-full border
                                        @if($promo->type == 'general') bg-blue-50 text-blue-700 border-blue-200
                                        @else bg-purple-50 text-purple-700 border-purple-200 @endif">
                                        {{ $promo->type_label }}
                                    </span>
                                </span>
                                <span class="text-sm text-[#C1121F] font-bold">
                                    Diskon {{ $promo->discount_percent }}% 
                                    (Maks Rp {{ number_format($promo->max_discount, 0, ',', '.') }})
                                </span>
                                @if($promo->min_purchase > 0)
                                <span class="text-xs text-gray-500 font-light block mt-1">
                                    Min pembelian: Rp {{ number_format($promo->min_purchase, 0, ',', '.') }}
                                </span>
                                @endif
                            </div>
                        </div>
                        <span class="text-xs text-gray-400 font-mono mt-1 block">
                            📅 {{ $promo->start_date->format('d M') }} - {{ $promo->end_date->format('d M Y') }}
                        </span>
                        @if(!$allMethods)
                        <span class="text-[10px] text-orange-600 font-light mt-1 block">
                            ⚠️ Hanya untuk: {{ implode(', ', $methodLabels) }}
                        </span>
                        @endif
                    </div>
                </label>
                @endforeach
            </div>
            @else
            <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-4 text-center text-sm text-gray-500 font-light mb-4">
                Tidak ada promo tersedia saat ini.
            </div>
            @endif

            {{-- Info Diskon Saat Ini --}}
            @if($currentPromoUsage && $currentPromoUsage->tourPromo)
            <div class="bg-green-50 border border-green-200 rounded-[12px] p-4">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm font-medium text-green-800">
                            ✅ Promo <strong>{{ $currentPromoUsage->tourPromo->name }}</strong> aktif
                        </p>
                        <p class="text-xs text-green-600 font-light">Klik promo lain untuk mengganti, atau "Tanpa Promo" untuk menghapus</p>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold text-[#C1121F]">-Rp {{ number_format($booking->discount_amount, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            @endif
        </form>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- PILIH METODE PEMBAYARAN — HANYA JIKA STATUS PENDING & BELUM ADA PAYMENT --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    @if($booking->status == 'pending' && !$booking->payment)
    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-6 shadow-sm">
        <h2 class="text-lg font-bold text-[#111111] mb-2">💳 Metode Pembayaran</h2>
        <p class="text-sm text-gray-500 font-light mb-4">Pilih metode pembayaran untuk menyelesaikan booking tour Anda.</p>
        
        @php
            // Cek metode yang didukung promo aktif
            $disabledMethods = [];
            $currentPromoUsage = \App\Models\TourPromoUsage::where('tour_booking_id', $booking->id)->first();
            if ($currentPromoUsage && $currentPromoUsage->tourPromo) {
                $activePromoMethods = $currentPromoUsage->tourPromo->getApplicablePaymentMethodsArray();
                if (!in_array('midtrans', $activePromoMethods)) $disabledMethods[] = 'midtrans';
                if (!in_array('cash', $activePromoMethods)) $disabledMethods[] = 'cash';
                if (!in_array('cod', $activePromoMethods)) $disabledMethods[] = 'cod';
            }
            $hasActivePromo = $currentPromoUsage && $currentPromoUsage->tourPromo;
            $promoName = $hasActivePromo ? $currentPromoUsage->tourPromo->name : '';
        @endphp
        
        @if($hasActivePromo && count($disabledMethods) > 0)
        <div class="bg-yellow-50 border border-yellow-200 rounded-[12px] p-4 mb-4 text-sm">
            <p class="font-medium text-yellow-800">
                ⚠️ Promo <strong>{{ $promoName }}</strong> aktif. Beberapa metode pembayaran mungkin tidak tersedia.
            </p>
        </div>
        @endif
        
        <form id="payForm" action="{{ route('customer.tour.pay-process', $booking) }}" method="POST">
            @csrf
            
            <div class="space-y-3 mb-6">
                {{-- Midtrans --}}
                <label class="flex items-center gap-4 p-4 border-2 rounded-[12px] transition
                    {{ in_array('midtrans', $disabledMethods) ? 'opacity-50 cursor-not-allowed border-[#E5E5E5] bg-[#F5F5F5]' : 'border-[#E5E5E5] cursor-pointer hover:border-[#C1121F] has-[:checked]:border-[#C1121F] has-[:checked]:bg-[#C1121F]/5' }}">
                    <input type="radio" name="payment_method" value="midtrans" class="w-5 h-5 text-[#C1121F] focus:ring-[#C1121F]"
                        {{ in_array('midtrans', $disabledMethods) ? 'disabled' : '' }} required>
                    <div class="flex-1">
                        <span class="font-semibold text-[#111111] block">
                            💳 Bayar Online (Midtrans)
                            @if(in_array('midtrans', $disabledMethods))
                            <span class="text-[10px] text-red-500 font-normal ml-2">— Promo tidak berlaku</span>
                            @endif
                        </span>
                        <span class="text-xs text-gray-500 font-light">Transfer Bank, Virtual Account, QRIS, E-Wallet</span>
                    </div>
                </label>
                
                {{-- Cash --}}
                <label class="flex items-center gap-4 p-4 border-2 rounded-[12px] transition
                    {{ in_array('cash', $disabledMethods) ? 'opacity-50 cursor-not-allowed border-[#E5E5E5] bg-[#F5F5F5]' : 'border-[#E5E5E5] cursor-pointer hover:border-[#C1121F] has-[:checked]:border-[#C1121F] has-[:checked]:bg-[#C1121F]/5' }}">
                    <input type="radio" name="payment_method" value="cash" class="w-5 h-5 text-[#C1121F] focus:ring-[#C1121F]"
                        {{ in_array('cash', $disabledMethods) ? 'disabled' : '' }}>
                    <div class="flex-1">
                        <span class="font-semibold text-[#111111] block">
                            🏪 Bayar di Warung GoMad (Cash)
                            @if(in_array('cash', $disabledMethods))
                            <span class="text-[10px] text-red-500 font-normal ml-2">— Promo tidak berlaku</span>
                            @endif
                        </span>
                        <span class="text-xs text-gray-500 font-light">Dapatkan kode bayar dan tunjukkan ke warung terdekat</span>
                    </div>
                </label>
                
                {{-- COD --}}
                <label class="flex items-center gap-4 p-4 border-2 rounded-[12px] transition
                    {{ in_array('cod', $disabledMethods) ? 'opacity-50 cursor-not-allowed border-[#E5E5E5] bg-[#F5F5F5]' : 'border-[#E5E5E5] cursor-pointer hover:border-[#C1121F] has-[:checked]:border-[#C1121F] has-[:checked]:bg-[#C1121F]/5' }}">
                    <input type="radio" name="payment_method" value="cod" class="w-5 h-5 text-[#C1121F] focus:ring-[#C1121F]"
                        {{ in_array('cod', $disabledMethods) ? 'disabled' : '' }}>
                    <div class="flex-1">
                        <span class="font-semibold text-[#111111] block">
                            🚗 COD (Bayar ke Sopir)
                            @if(in_array('cod', $disabledMethods))
                            <span class="text-[10px] text-red-500 font-normal ml-2">— Promo tidak berlaku</span>
                            @endif
                        </span>
                        <span class="text-xs text-gray-500 font-light">Bayar tunai saat penjemputan oleh driver</span>
                    </div>
                </label>
            </div>

            <div id="paymentInfo" class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-4 mb-4 text-sm hidden">
                <p class="font-medium text-[#111111]" id="paymentInfoText"></p>
            </div>

            <button type="submit" id="btnPay" disabled
                    class="w-full bg-[#E5E5E5] text-gray-500 py-4 rounded-[12px] font-bold text-lg cursor-not-allowed transition">
                💳 BAYAR SEKARANG
            </button>
        </form>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- MIDTRANS SNAP BUTTON --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    @if($booking->payment && $booking->payment->payment_type == 'midtrans' && $booking->payment->status == 'pending')
    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-6 shadow-sm">
        <h2 class="text-lg font-bold text-[#111111] mb-4">💳 Pembayaran Online</h2>
        <p class="text-sm text-gray-500 font-light mb-4">Silakan selesaikan pembayaran melalui Midtrans.</p>
        
        @php
            $tourPaymentService = app(\App\Services\TourPaymentService::class);
            try { $snapToken = $tourPaymentService->getSnapToken($booking); } 
            catch (\Exception $e) { $snapToken = null; }
        @endphp
        
        @if(isset($snapToken) && $snapToken)
        <button id="tour-pay-button" class="w-full bg-[#C1121F] text-white py-4 rounded-[12px] font-bold text-lg hover:bg-[#8A0F18] transition">
            💳 BAYAR SEKARANG (MIDTRANS)
        </button>
        @else
        <div class="bg-yellow-50 border border-yellow-200 rounded-[12px] p-4 text-center text-sm text-yellow-800 font-light">
            Menghubungkan ke gateway pembayaran...
            <a href="{{ route('customer.tour.detail', $booking) }}" class="text-[#C1121F] underline font-medium ml-2">Muat Ulang</a>
        </div>
        @endif
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- CASH PAYMENT --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    @if($booking->payment && $booking->payment->payment_type == 'cash' && $booking->payment->status == 'pending')
    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-6 shadow-sm">
        <h2 class="text-lg font-bold text-[#111111] mb-4">🏪 Pembayaran di Warung GoMad</h2>
        @php $paymentDetail = $booking->payment->payment_detail ?? []; $paymentCode = $paymentDetail['payment_code'] ?? $booking->booking_code; @endphp
        <div class="bg-green-50 border-2 border-green-300 rounded-[12px] p-6 text-center mb-4">
            <p class="text-sm text-green-700 font-mono uppercase tracking-wider mb-2">Tunjukkan kode ini ke Warung GoMad terdekat</p>
            <p class="text-4xl font-mono font-bold text-[#C1121F] tracking-widest mb-2">{{ $paymentCode }}</p>
            <p class="text-xs text-gray-500 font-light">Expired: {{ $booking->payment->expired_at ? $booking->payment->expired_at->format('d M Y H:i') : '-' }}</p>
            <button onclick="copyPaymentCode('{{ $paymentCode }}')" class="mt-3 bg-[#C1121F] text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-[#8A0F18] transition">📋 Salin Kode</button>
        </div>
        <div class="text-center text-sm text-gray-500 font-light">
            <p>💰 Total: <strong class="text-[#111111]">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</strong></p>
            <p class="mt-1">📍 Cari Warung GoMad terdekat dan tunjukkan kode di atas</p>
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- COD PAYMENT --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    @if($booking->payment && $booking->payment->payment_type == 'cod' && $booking->payment->status == 'cod_pending')
    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-6 shadow-sm">
        <h2 class="text-lg font-bold text-[#111111] mb-4">🚗 Pembayaran COD (Bayar ke Sopir)</h2>
        <div class="bg-orange-50 border-2 border-orange-300 rounded-[12px] p-6 text-center">
            <div class="text-4xl mb-3">🚗</div>
            <p class="font-bold text-orange-800 text-lg mb-2">Bayar ke Sopir saat Penjemputan</p>
            <p class="text-sm text-orange-700 mb-4 font-light">Siapkan uang tunai sejumlah <strong>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</strong></p>
            <div class="bg-white rounded-[12px] p-4 text-left text-sm space-y-2 border border-orange-200">
                <p><strong class="font-mono uppercase tracking-wider text-xs">Sopir:</strong> {{ $booking->tourSchedule->driver->name ?? 'Akan ditentukan' }}</p>
                <p><strong class="font-mono uppercase tracking-wider text-xs">Kendaraan:</strong> {{ $booking->tourSchedule->vehicle->plate_number ?? '-' }}</p>
                <p><strong class="font-mono uppercase tracking-wider text-xs">Jemput:</strong> {{ $booking->tourSchedule->departure_date->format('d M Y') }} {{ $booking->tourSchedule->departure_time }}</p>
                <p><strong class="font-mono uppercase tracking-wider text-xs">Alamat:</strong> {{ $booking->pickup_address }}</p>
            </div>
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- PAYMENT SUCCESS --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    @if($booking->payment && in_array($booking->payment->status, ['paid', 'cod_confirmed']))
    <div class="bg-green-50 border border-green-200 rounded-[12px] p-4 mb-6 text-center">
        <p class="font-bold text-green-800 text-lg">✅ Pembayaran Berhasil</p>
        <p class="text-sm text-green-600 font-light mt-1">
            Metode: @if($booking->payment->payment_type == 'midtrans') Online @elseif($booking->payment->payment_type == 'cash') Warung GoMad @else COD @endif
        </p>
        <p class="text-xs text-green-500 font-mono mt-1">Transaksi: {{ $booking->payment->transaction_id ?? '-' }}</p>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- PAYMENT FAILED/EXPIRED --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    @if($booking->payment && in_array($booking->payment->status, ['failed', 'expired']))
    <div class="bg-red-50 border border-red-200 rounded-[12px] p-4 mb-6 text-center">
        <p class="font-bold text-red-800 text-lg">
            @if($booking->payment->status == 'failed') ❌ Pembayaran Gagal
            @else ⏰ Pembayaran Kadaluarsa
            @endif
        </p>
        <p class="text-sm text-red-600 font-light mt-1">Silakan buat booking baru atau hubungi agency.</p>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- ACTIONS --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div class="space-y-3">
        {{-- Tombol Batalkan --}}
        @if($booking->can_cancel)
            @if($booking->status === 'paid')
            <div class="bg-red-50 border border-red-200 rounded-[12px] p-4 mb-3">
                <h4 class="font-mono uppercase tracking-wider text-xs font-semibold text-red-800 mb-2">⚠️ Kebijakan Pembatalan Tour</h4>
                @php
                    $departureDateTime = \Carbon\Carbon::parse(
                        $booking->tourSchedule->departure_date->format('Y-m-d') . ' ' . $booking->tourSchedule->departure_time
                    );
                    $hoursUntilDeparture = now()->diffInHours($departureDateTime, false);
                @endphp
                <div class="text-sm text-red-700 space-y-1 font-light">
                    <p>• Biaya pembatalan: <strong>Rp {{ number_format($booking->cancellation_fee, 0, ',', '.') }}</strong></p>
                    <p>• Dana dikembalikan: <strong>Rp {{ number_format($booking->cancellation_refund, 0, ',', '.') }}</strong></p>
                    @if($hoursUntilDeparture > 24 && $hoursUntilDeparture <= 168)
                    <p>• ⏰ Batas pembatalan: <strong>{{ round($hoursUntilDeparture) }} jam lagi</strong> ({{ round($hoursUntilDeparture / 24, 1) }} hari)</p>
                    @endif
                </div>
            </div>
            @endif

            <form action="{{ route('customer.tour.cancel', $booking) }}" method="POST" onsubmit="return confirmCancel()">
                @csrf
                <button type="submit" class="w-full border border-red-500 text-red-600 py-3 rounded-[12px] font-semibold hover:bg-red-50 transition">
                    @if($booking->status === 'paid')
                        ❌ Batalkan Booking (Biaya Rp {{ number_format($booking->cancellation_fee, 0, ',', '.') }})
                    @else
                        ❌ Batalkan Booking
                    @endif
                </button>
            </form>
        @elseif($booking->status === 'paid')
            <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-4 text-center">
                <p class="text-gray-600 text-sm font-medium">🔒 Pembatalan tidak tersedia</p>
                <p class="text-gray-500 text-xs mt-1 font-light">
                    @php
                        $departureDateTime = \Carbon\Carbon::parse(
                            $booking->tourSchedule->departure_date->format('Y-m-d') . ' ' . $booking->tourSchedule->departure_time
                        );
                    @endphp
                    @if(now()->diffInHours($departureDateTime, false) <= 24)
                        Kurang dari 24 jam sebelum keberangkatan.
                    @endif
                    Hubungi agency untuk bantuan.
                </p>
                @if($booking->tourSchedule->tourPackage->agency)
                <p class="text-gray-500 text-xs mt-1 font-light">
                    📞 {{ $booking->tourSchedule->tourPackage->agency->contact_alternate ?? $booking->tourSchedule->tourPackage->agency->user->phone ?? '-' }}
                </p>
                @endif
            </div>
        @endif

        {{-- Tombol Ganti Pembayaran --}}
        @if(($booking->status == 'pending' || ($booking->payment && $booking->payment->status == 'cod_pending')) && $booking->payment)
        <button onclick="document.getElementById('changePaymentModal').style.display='flex'" 
                class="w-full border border-[#E5E5E5] text-gray-700 py-3 rounded-[12px] font-semibold hover:bg-[#F5F5F5] transition">
            🔄 Ganti Metode Pembayaran
        </button>
        @endif

        {{-- Kembali --}}
        <a href="{{ route('customer.tour.bookings') }}" class="block w-full text-center border border-[#E5E5E5] text-gray-700 py-3 rounded-[12px] font-semibold hover:bg-[#F5F5F5] transition">
            ← Kembali ke Riwayat Booking
        </a>
    </div>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- MODAL GANTI PEMBAYARAN --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    @if(($booking->status == 'pending' || ($booking->payment && $booking->payment->status == 'cod_pending')) && $booking->payment)
    <div id="changePaymentModal" style="display:none;" class="fixed inset-0 bg-[#111111]/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-[12px] shadow-xl p-6 max-w-sm w-full border border-[#E5E5E5]">
            <h3 class="font-bold text-lg text-[#111111] mb-4">Ganti Metode Pembayaran</h3>
            <form action="{{ route('customer.tour.pay-process', $booking) }}" method="POST" class="space-y-3">
                @csrf
                <label class="flex items-center gap-3 p-3 border border-[#E5E5E5] rounded-[12px] cursor-pointer hover:bg-[#F5F5F5] transition">
                    <input type="radio" name="payment_method" value="midtrans" class="text-[#C1121F] focus:ring-[#C1121F]"> 💳 Online
                </label>
                <label class="flex items-center gap-3 p-3 border border-[#E5E5E5] rounded-[12px] cursor-pointer hover:bg-[#F5F5F5] transition">
                    <input type="radio" name="payment_method" value="cash" class="text-[#C1121F] focus:ring-[#C1121F]"> 🏪 Warung
                </label>
                <label class="flex items-center gap-3 p-3 border border-[#E5E5E5] rounded-[12px] cursor-pointer hover:bg-[#F5F5F5] transition">
                    <input type="radio" name="payment_method" value="cod" class="text-[#C1121F] focus:ring-[#C1121F]"> 🚗 COD
                </label>
                <div class="flex gap-3 mt-4">
                    <button type="submit" class="flex-1 btn-gomad-primary">Ganti</button>
                    <button type="button" onclick="document.getElementById('changePaymentModal').style.display='none'" 
                            class="flex-1 border border-[#E5E5E5] py-2 rounded-[12px] font-medium hover:bg-[#F5F5F5] transition">Batal</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    @endif
</div>

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- SCRIPTS --}}
{{-- ═══════════════════════════════════════════════════════ --}}

{{-- Midtrans Snap --}}
@if(isset($snapToken) && $snapToken && $booking->payment && $booking->payment->payment_type == 'midtrans' && $booking->payment->status == 'pending')
@push('scripts')
<script src="{{ config('gomad.midtrans.snap_url') }}" data-client-key="{{ config('gomad.midtrans.client_key') }}"></script>
<script>
document.getElementById('tour-pay-button').addEventListener('click', function() {
    this.disabled = true;
    this.textContent = '⏳ Memproses...';
    snap.pay('{{ $snapToken }}', {
        onSuccess: function() { window.location.reload(); },
        onPending: function() { 
            alert('Menunggu pembayaran...'); 
            document.getElementById('tour-pay-button').disabled = false;
            document.getElementById('tour-pay-button').textContent = '💳 BAYAR SEKARANG (MIDTRANS)';
        },
        onError: function() { 
            alert('Pembayaran gagal. Silakan coba lagi.'); 
            document.getElementById('tour-pay-button').disabled = false;
            document.getElementById('tour-pay-button').textContent = '💳 BAYAR SEKARANG (MIDTRANS)';
        }
    });
});
</script>
@endpush
@endif

{{-- Payment Method Script --}}
@if($booking->status == 'pending' && !$booking->payment)
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('input[name="payment_method"]:not([disabled])').forEach(function(radio) {
        radio.addEventListener('change', function() {
            var m = this.value;
            var btn = document.getElementById('btnPay');
            var info = document.getElementById('paymentInfo');
            var infoText = document.getElementById('paymentInfoText');
            
            btn.disabled = false;
            btn.className = 'w-full bg-[#C1121F] text-white py-4 rounded-[12px] font-bold text-lg hover:bg-[#8A0F18] cursor-pointer transition';
            info.classList.remove('hidden');

            if (m === 'midtrans') { 
                btn.textContent = '💳 BAYAR ONLINE (MIDTRANS)'; 
                infoText.innerHTML = 'Anda akan diarahkan ke halaman pembayaran Midtrans.<br><span class="text-xs text-gray-400">Tersedia: Transfer Bank, Virtual Account, QRIS, E-Wallet</span>'; 
            } else if (m === 'cash') { 
                btn.textContent = '🏪 BAYAR DI WARUNG'; 
                infoText.innerHTML = 'Anda akan mendapatkan kode bayar.<br><span class="text-xs text-gray-400">Tunjukkan kode tersebut ke Warung GoMad terdekat.</span>'; 
            } else if (m === 'cod') { 
                btn.textContent = '🚗 BAYAR KE SOPIR'; 
                infoText.innerHTML = 'Siapkan uang tunai <strong>Rp {{ number_format($booking->total_price, 0, ",", ".") }}</strong> dan bayarkan langsung ke sopir saat penjemputan.'; 
            }
        });
    });
});

function copyPaymentCode(code) {
    navigator.clipboard.writeText(code).then(function() {
        alert('Kode bayar berhasil disalin!');
    }).catch(function() {
        alert('Gagal menyalin kode. Silakan salin manual.');
    });
}

function confirmCancel() {
    @if($booking->status === 'paid')
    return confirm(
        '⚠️ KONFIRMASI PEMBATALAN TOUR\n\n' +
        'Biaya pembatalan: Rp {{ number_format($booking->cancellation_fee, 0, ",", ".") }}\n' +
        'Dana dikembalikan: Rp {{ number_format($booking->cancellation_refund, 0, ",", ".") }}\n\n' +
        'Apakah Anda yakin ingin membatalkan?'
    );
    @else
    return confirm('Apakah Anda yakin ingin membatalkan booking ini?');
    @endif
}
</script>
@endpush
@endif

{{-- Modal Close --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById('changePaymentModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) modal.style.display = 'none';
        });
    }
});
</script>
@endpush
@endsection