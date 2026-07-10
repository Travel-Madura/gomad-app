@extends('layouts.customer')

@section('title', 'Detail Sewa Kendaraan')
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
        <div class="text-4xl mb-2">🚐</div>
        <h2 class="text-xl font-bold text-[#111111]">{{ $booking->status_label }}</h2>
        <p class="text-sm mt-1 font-light text-gray-600">
            @if($booking->status == 'pending' && !$booking->payment)
                Silakan pilih promo dan metode pembayaran di bawah ini.
            @elseif($booking->status == 'confirmed' && $booking->payment && $booking->payment->payment_type == 'cod')
                🚗 Pembayaran COD — Bayar tunai ke sopir saat penjemputan.
            @elseif($booking->payment && $booking->payment->payment_type == 'midtrans' && $booking->payment->status == 'pending')
                Silakan selesaikan pembayaran online di bawah ini.
            @elseif($booking->payment && $booking->payment->payment_type == 'cash' && $booking->payment->status == 'pending')
                Tunjukkan kode bayar ke Warung GoMad terdekat.
            @elseif($booking->status == 'paid')
                Booking sudah dikonfirmasi. Kendaraan siap digunakan!
            @elseif($booking->status == 'completed')
                Sewa telah selesai. Terima kasih!
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
                <p class="text-gray-500 font-light">{{ $booking->purpose }}</p>
            </div>
            <p class="text-2xl font-bold text-[#C1121F] font-mono">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
        </div>

        <div class="border-t border-[#E5E5E5] pt-4">
            <h3 class="font-mono uppercase tracking-wider text-xs font-bold text-[#111111] mb-3">Detail Sewa</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3">
                    <span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Kendaraan</span>
                    <p class="font-semibold text-[#111111] font-mono">{{ $booking->vehicle->plate_number ?? '-' }}</p>
                    <p class="text-xs text-gray-500 font-light">{{ $booking->vehicle->brand ?? '' }} {{ $booking->vehicle->model ?? '' }}</p>
                </div>
                <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3">
                    <span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Agency</span>
                    <p class="font-semibold text-[#111111]">{{ $booking->vehicle->agency->agency_name ?? '-' }}</p>
                    <p class="text-xs text-gray-500 font-light">⭐ {{ number_format($booking->vehicle->agency->rating ?? 0, 1) }}</p>
                </div>
                <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3">
                    <span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Tanggal Sewa</span>
                    <p class="font-semibold text-[#111111]">{{ $booking->start_date->format('d M Y') }} → {{ $booking->end_date->format('d M Y') }}</p>
                    <p class="text-xs text-gray-500 font-light">{{ $booking->total_days }} hari</p>
                </div>
                <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3">
                    <span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">Supir</span>
                    <p class="font-semibold text-[#111111]">{{ $booking->include_driver ? '✅ Include Supir' : '❌ Tanpa Supir' }}</p>
                    @if($booking->include_driver && $booking->driver_price > 0)
                    <p class="text-xs text-gray-500 font-light">Rp {{ number_format($booking->driver_price, 0, ',', '.') }}</p>
                    @endif
                </div>
                <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3">
                    <span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">📍 Jemput</span>
                    <p class="font-medium text-xs text-[#111111]">{{ $booking->pickup_address }}</p>
                </div>
                <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3">
                    <span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">🎯 Tujuan</span>
                    <p class="font-medium text-xs text-[#111111]">{{ $booking->destination_address }}</p>
                </div>
                <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3">
                    <span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">📏 Jarak</span>
                    <p class="font-semibold text-[#111111]">{{ $booking->estimated_distance_km ?? '-' }} km</p>
                </div>
                <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-3">
                    <span class="text-[10px] font-mono uppercase tracking-wider text-gray-400">👥 Penumpang</span>
                    <p class="font-semibold text-[#111111]">{{ $booking->max_passengers }} orang</p>
                </div>
            </div>

            @if($booking->special_notes)
            <div class="mt-4 bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-4">
                <h4 class="font-mono uppercase tracking-wider text-xs font-semibold text-[#111111] mb-2">📝 Catatan Khusus</h4>
                <p class="text-sm text-gray-600 font-light">{{ $booking->special_notes }}</p>
            </div>
            @endif

            {{-- Rincian Biaya --}}
            <div class="mt-4 bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-4">
                <h4 class="font-mono uppercase tracking-wider text-xs font-bold mb-3">Rincian Biaya</h4>
                <div class="space-y-2 text-sm">
                    @php
                        $baseChargeOnly = $booking->base_price - $booking->extra_days_price - $booking->driver_price;
                    @endphp
                    <div class="flex justify-between">
                        <span class="text-gray-500 font-light">Base Charge ({{ $booking->estimated_distance_km ?? 0 }} km × Rp {{ number_format($booking->vehicle->rental_price_per_km ?? 0, 0, ',', '.') }})</span>
                        <span class="font-medium text-[#111111]">Rp {{ number_format($baseChargeOnly, 0, ',', '.') }}</span>
                    </div>
                    @if($booking->extra_days_price > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-500 font-light">Hari Tambahan ({{ max(0, $booking->total_days - 1) }} hari)</span>
                        <span class="font-medium text-[#111111]">Rp {{ number_format($booking->extra_days_price, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    @if($booking->driver_price > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-500 font-light">Biaya Supir ({{ $booking->total_days }} hari)</span>
                        <span class="font-medium text-[#111111]">Rp {{ number_format($booking->driver_price, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between border-t border-[#E5E5E5] pt-2">
                        <span class="text-gray-500 font-light">Subtotal</span>
                        <span class="font-semibold text-[#111111]">Rp {{ number_format($booking->base_price, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 font-light">Biaya Layanan</span>
                        <span class="font-medium text-[#111111]">Rp {{ number_format($booking->service_fee, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 font-light">Biaya Platform (3%)</span>
                        <span class="font-medium text-[#111111]">Rp {{ number_format($booking->platform_fee, 0, ',', '.') }}</span>
                    </div>
                    @if($booking->discount_amount > 0)
                    <div class="flex justify-between text-[#C1121F]">
                        <span>
                            🎫 Diskon Promo
                            @php
                                $promoUsage = \App\Models\RentalPromoUsage::where('rental_booking_id', $booking->id)->first();
                            @endphp
                            @if($promoUsage && $promoUsage->rentalPromo)
                            <span class="text-xs font-light">({{ $promoUsage->rentalPromo->name }})</span>
                            @endif
                        </span>
                        <span class="font-bold">-Rp {{ number_format($booking->discount_amount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <hr class="border-[#E5E5E5]">
                    <div class="flex justify-between text-base font-bold">
                        <span>TOTAL</span>
                        <span class="text-[#C1121F] font-mono text-lg">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
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
            $rentalPromoService = app(\App\Services\RentalPromoService::class);
            $availablePromos = $rentalPromoService->getAvailablePromosForCustomer(auth()->user(), $booking->vehicle_id);
            $currentPromoUsage = \App\Models\RentalPromoUsage::where('rental_booking_id', $booking->id)->first();
            $currentPromoId = $currentPromoUsage ? $currentPromoUsage->rental_promo_id : null;
        @endphp

        <form id="promoForm" action="{{ route('customer.rental.apply-promo', $booking) }}" method="POST">
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
                            ⚠️ Hanya berlaku untuk: {{ implode(', ', $methodLabels) }}
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
            @if($currentPromoUsage && $currentPromoUsage->rentalPromo)
            <div class="bg-green-50 border border-green-200 rounded-[12px] p-4">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm font-medium text-green-800">
                            ✅ Promo <strong>{{ $currentPromoUsage->rentalPromo->name }}</strong> aktif
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
    {{-- PILIH METODE PEMBAYARAN — JIKA PENDING & BELUM ADA PAYMENT --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    @if($booking->status == 'pending' && !$booking->payment)
    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-6 shadow-sm">
        <h2 class="text-lg font-bold text-[#111111] mb-2">💳 Metode Pembayaran</h2>
        <p class="text-sm text-gray-500 font-light mb-4">Pilih metode pembayaran untuk menyelesaikan sewa kendaraan.</p>
        
        @php
            $disabledMethods = [];
            $currentPromoUsage = \App\Models\RentalPromoUsage::where('rental_booking_id', $booking->id)->first();
            if ($currentPromoUsage && $currentPromoUsage->rentalPromo) {
                $activePromoMethods = $currentPromoUsage->rentalPromo->getApplicablePaymentMethodsArray();
                if (!in_array('midtrans', $activePromoMethods)) $disabledMethods[] = 'midtrans';
                if (!in_array('cash', $activePromoMethods)) $disabledMethods[] = 'cash';
                if (!in_array('cod', $activePromoMethods)) $disabledMethods[] = 'cod';
            }
            $hasActivePromo = $currentPromoUsage && $currentPromoUsage->rentalPromo;
            $promoName = $hasActivePromo ? $currentPromoUsage->rentalPromo->name : '';
        @endphp
        
        @if($hasActivePromo && count($disabledMethods) > 0)
        <div class="bg-yellow-50 border border-yellow-200 rounded-[12px] p-4 mb-4 text-sm">
            <p class="font-medium text-yellow-800">
                ⚠️ Promo <strong>{{ $promoName }}</strong> aktif. Beberapa metode pembayaran mungkin tidak tersedia.
            </p>
        </div>
        @endif
        
        <form id="payForm" action="{{ route('customer.rental.pay-process', $booking) }}" method="POST">
            @csrf
            
            <div class="space-y-3 mb-6">
                <label class="flex items-center gap-4 p-4 border-2 rounded-[12px] transition
                    {{ in_array('midtrans', $disabledMethods) ? 'opacity-50 cursor-not-allowed border-[#E5E5E5] bg-[#F5F5F5]' : 'border-[#E5E5E5] cursor-pointer hover:border-[#C1121F] has-[:checked]:border-[#C1121F] has-[:checked]:bg-[#C1121F]/5' }}">
                    <input type="radio" name="payment_method" value="midtrans" class="w-5 h-5 text-[#C1121F] focus:ring-[#C1121F]"
                        {{ in_array('midtrans', $disabledMethods) ? 'disabled' : '' }}>
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
                        <span class="text-xs text-gray-500 font-light">Bayar tunai saat penjemputan</span>
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
        <h2 class="text-lg font-bold text-[#111111] mb-4">💳 Pembayaran Online (Midtrans)</h2>
        <p class="text-sm text-gray-500 font-light mb-4">Klik tombol di bawah untuk menyelesaikan pembayaran.</p>
        
        <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-4 mb-4 text-sm">
            <p>💳 <strong>Midtrans</strong> — Transfer Bank, Virtual Account, QRIS, E-Wallet</p>
            <p class="text-xs text-gray-500 mt-1">Total: <strong class="text-[#C1121F]">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</strong></p>
        </div>
        
        <button id="rental-pay-button" 
                class="w-full bg-[#C1121F] text-white py-4 rounded-[12px] font-bold text-lg hover:bg-[#8A0F18] transition">
            💳 BAYAR SEKARANG (MIDTRANS)
        </button>
    </div>

    @push('scripts')
    <script src="{{ config('gomad.midtrans.snap_url') }}" data-client-key="{{ config('gomad.midtrans.client_key') }}"></script>
    <script>
    document.getElementById('rental-pay-button').addEventListener('click', function() {
        var btn = this;
        btn.disabled = true;
        btn.textContent = '⏳ Menghubungkan...';
        
        fetch('{{ route('customer.rental.midtrans-token', $booking) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (data.success && data.data.snap_token) {
                snap.pay(data.data.snap_token, {
                    onSuccess: function(result) { window.location.reload(); },
                    onPending: function(result) {
                        alert('Menunggu pembayaran...');
                        btn.disabled = false;
                        btn.textContent = '💳 BAYAR SEKARANG (MIDTRANS)';
                    },
                    onError: function(result) {
                        alert('Pembayaran gagal. Silakan coba lagi.');
                        btn.disabled = false;
                        btn.textContent = '💳 BAYAR SEKARANG (MIDTRANS)';
                    },
                    onClose: function() {
                        btn.disabled = false;
                        btn.textContent = '💳 BAYAR SEKARANG (MIDTRANS)';
                    }
                });
            } else {
                alert('Gagal: ' + (data.message || 'Error'));
                btn.disabled = false;
                btn.textContent = '💳 BAYAR SEKARANG (MIDTRANS)';
            }
        })
        .catch(function(err) {
            console.error('Fetch error:', err);
            alert('Gagal menghubungi server.');
            btn.disabled = false;
            btn.textContent = '💳 BAYAR SEKARANG (MIDTRANS)';
        });
    });
    </script>
    @endpush
    @endif

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- CASH PAYMENT — KODE BAYAR --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    @if($booking->payment && $booking->payment->payment_type == 'cash' && $booking->payment->status == 'pending')
    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-6 shadow-sm">
        <h2 class="text-lg font-bold text-[#111111] mb-4">🏪 Pembayaran di Warung GoMad</h2>
        
        @php
            $paymentDetail = $booking->payment->payment_detail ?? [];
            $paymentCode = $paymentDetail['payment_code'] ?? $booking->booking_code;
        @endphp
        
        <div class="bg-green-50 border-2 border-green-300 rounded-[12px] p-6 text-center mb-4">
            <p class="text-sm text-green-700 font-mono uppercase tracking-wider mb-2">Tunjukkan kode ini ke Warung GoMad terdekat</p>
            <p class="text-4xl font-mono font-bold text-[#C1121F] tracking-widest mb-2">{{ $paymentCode }}</p>
            <p class="text-xs text-gray-500 font-light">
                Expired: {{ $booking->payment->expired_at ? $booking->payment->expired_at->format('d M Y H:i') : '-' }}
            </p>
            <button onclick="copyPaymentCode('{{ $paymentCode }}')" 
                    class="mt-3 bg-[#C1121F] text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-[#8A0F18] transition">
                📋 Salin Kode
            </button>
        </div>
        
        <div class="text-center text-sm text-gray-500 font-light">
            <p>💰 Total: <strong class="text-[#111111]">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</strong></p>
            <p class="mt-1">📍 Cari Warung GoMad terdekat dan tunjukkan kode di atas</p>
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- COD PAYMENT — INFO --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    @if($booking->payment && $booking->payment->payment_type == 'cod')
    <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-6 shadow-sm">
        <h2 class="text-lg font-bold text-[#111111] mb-4">🚗 Pembayaran COD (Bayar ke Sopir)</h2>
        
        @if($booking->payment->status == 'cod_pending')
        <div class="bg-orange-50 border-2 border-orange-300 rounded-[12px] p-6 text-center">
            <div class="text-4xl mb-3">🚗</div>
            <p class="font-bold text-orange-800 text-lg mb-2">Bayar ke Sopir saat Penjemputan</p>
            <p class="text-sm text-orange-700 mb-4 font-light">
                Siapkan uang tunai sejumlah <strong>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</strong>
            </p>
            <div class="bg-white rounded-[12px] p-4 text-left text-sm space-y-2 border border-orange-200">
                <p><strong>Kendaraan:</strong> {{ $booking->vehicle->plate_number }} ({{ $booking->vehicle->brand }} {{ $booking->vehicle->model }})</p>
                <p><strong>Jemput:</strong> {{ $booking->start_date->format('d M Y') }}</p>
                <p><strong>Alamat:</strong> {{ $booking->pickup_address }}</p>
            </div>
        </div>
        @elseif($booking->payment->status == 'cod_confirmed')
        <div class="bg-green-50 border-2 border-green-300 rounded-[12px] p-6 text-center">
            <div class="text-4xl mb-3">✅</div>
            <p class="font-bold text-green-800 text-lg mb-2">Pembayaran COD Dikonfirmasi</p>
            <p class="text-sm text-green-700 font-light">Pembayaran telah diterima oleh sopir.</p>
        </div>
        @endif
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
    {{-- PAYMENT FAILED / EXPIRED --}}
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
        @if($booking->can_cancel)
            @if($booking->status === 'paid')
            <div class="bg-red-50 border border-red-200 rounded-[12px] p-4 mb-3">
                <h4 class="font-mono uppercase tracking-wider text-xs font-semibold text-red-800 mb-2">⚠️ Kebijakan Pembatalan Rental</h4>
                @php
                    $startDateTime = $booking->start_date->setTime(0, 0, 0);
                    $hoursUntilStart = now()->diffInHours($startDateTime, false);
                @endphp
                <div class="text-sm text-red-700 space-y-1 font-light">
                    <p>• Biaya pembatalan: <strong>Rp {{ number_format($booking->cancellation_fee, 0, ',', '.') }}</strong></p>
                    <p>• Dana dikembalikan: <strong>Rp {{ number_format($booking->cancellation_refund, 0, ',', '.') }}</strong></p>
                    @if($hoursUntilStart > 24 && $hoursUntilStart <= 168)
                    <p>• ⏰ Batas pembatalan: <strong>{{ round($hoursUntilStart) }} jam lagi</strong> ({{ round($hoursUntilStart / 24, 1) }} hari)</p>
                    @endif
                </div>
            </div>
            @endif

            <form action="{{ route('customer.rental.cancel', $booking) }}" method="POST" onsubmit="return confirmCancel()">
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
                    @if(now()->diffInHours($booking->start_date->setTime(0, 0, 0), false) <= 24)
                        Kurang dari 24 jam sebelum mulai sewa.
                    @endif
                    Hubungi agency untuk bantuan.
                </p>
                @if($booking->vehicle->agency)
                <p class="text-gray-500 text-xs mt-1 font-light">
                    📞 {{ $booking->vehicle->agency->contact_alternate ?? $booking->vehicle->agency->user->phone ?? '-' }}
                </p>
                @endif
            </div>
        @endif

        @if(($booking->status == 'pending' || ($booking->payment && $booking->payment->status == 'cod_pending')) && $booking->payment)
        <button onclick="document.getElementById('changePaymentModal').style.display='flex'" 
                class="w-full border border-[#E5E5E5] text-gray-700 py-3 rounded-[12px] font-semibold hover:bg-[#F5F5F5] transition">
            🔄 Ganti Metode Pembayaran
        </button>
        @endif

        <a href="{{ route('customer.rental.bookings') }}" class="block w-full text-center border border-[#E5E5E5] text-gray-700 py-3 rounded-[12px] font-semibold hover:bg-[#F5F5F5] transition">
            ← Kembali ke Riwayat Sewa
        </a>
    </div>

    {{-- MODAL GANTI PEMBAYARAN --}}
    @if(($booking->status == 'pending' || ($booking->payment && $booking->payment->status == 'cod_pending')) && $booking->payment)
    <div id="changePaymentModal" style="display:none;" class="fixed inset-0 bg-[#111111]/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-[12px] shadow-xl p-6 max-w-sm w-full border border-[#E5E5E5]">
            <h3 class="font-bold text-lg text-[#111111] mb-4">Ganti Metode Pembayaran</h3>
            <form action="{{ route('customer.rental.pay-process', $booking) }}" method="POST" class="space-y-3">
                @csrf
                <label class="flex items-center gap-3 p-3 border border-[#E5E5E5] rounded-[12px] cursor-pointer hover:bg-[#F5F5F5]"><input type="radio" name="payment_method" value="midtrans" class="text-[#C1121F]"> 💳 Online</label>
                <label class="flex items-center gap-3 p-3 border border-[#E5E5E5] rounded-[12px] cursor-pointer hover:bg-[#F5F5F5]"><input type="radio" name="payment_method" value="cash" class="text-[#C1121F]"> 🏪 Warung</label>
                <label class="flex items-center gap-3 p-3 border border-[#E5E5E5] rounded-[12px] cursor-pointer hover:bg-[#F5F5F5]"><input type="radio" name="payment_method" value="cod" class="text-[#C1121F]"> 🚗 COD</label>
                <div class="flex gap-3 mt-4">
                    <button type="submit" class="flex-1 btn-gomad-primary">Ganti</button>
                    <button type="button" onclick="document.getElementById('changePaymentModal').style.display='none'" class="flex-1 border border-[#E5E5E5] py-2 rounded-[12px]">Batal</button>
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

{{-- Payment Method Selection --}}
@if($booking->status == 'pending' && !$booking->payment)
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('input[name="payment_method"]:not([disabled])').forEach(function(radio) {
        radio.addEventListener('change', function() {
            var method = this.value;
            var btn = document.getElementById('btnPay');
            var info = document.getElementById('paymentInfo');
            var infoText = document.getElementById('paymentInfoText');
            
            btn.disabled = false;
            btn.className = 'w-full bg-[#C1121F] text-white py-4 rounded-[12px] font-bold text-lg hover:bg-[#8A0F18] cursor-pointer transition';
            info.classList.remove('hidden');

            if (method === 'midtrans') {
                btn.textContent = '💳 BAYAR ONLINE (MIDTRANS)';
                infoText.innerHTML = 'Anda akan diarahkan ke halaman pembayaran Midtrans.<br><span class="text-xs text-gray-400">Tersedia: Transfer Bank, Virtual Account, QRIS, E-Wallet</span>';
            } else if (method === 'cash') {
                btn.textContent = '🏪 BAYAR DI WARUNG';
                infoText.innerHTML = 'Anda akan mendapatkan kode bayar.<br><span class="text-xs text-gray-400">Tunjukkan kode tersebut ke Warung GoMad terdekat.</span>';
            } else if (method === 'cod') {
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
        alert('Gagal menyalin kode.');
    });
}

function confirmCancel() {
    @if($booking->status === 'paid')
    return confirm(
        '⚠️ KONFIRMASI PEMBATALAN RENTAL\n\n' +
        'Biaya pembatalan: Rp {{ number_format($booking->cancellation_fee, 0, ",", ".") }}\n' +
        'Dana dikembalikan: Rp {{ number_format($booking->cancellation_refund, 0, ",", ".") }}\n\n' +
        'Apakah Anda yakin ingin membatalkan?'
    );
    @else
    return confirm('Apakah Anda yakin ingin membatalkan booking sewa ini?');
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
    if (modal) modal.addEventListener('click', function(e) { if (e.target === modal) modal.style.display = 'none'; });
});
</script>
@endpush
@endsection