@extends('layouts.admin')

@section('title', 'Pengaturan')
@section('content')

@php
    $settings = \App\Models\PlatformSetting::getAllSettings();
@endphp

<div>
    <div class="mb-6 border-b border-[#E5E5E5] pb-3">
        <h1 class="text-2xl font-bold text-[#111111]">⚙️ Pengaturan Platform</h1>
        <p class="text-sm text-gray-500 font-light mt-1">Kelola semua pengaturan aplikasi GoMad. Perubahan disimpan otomatis.</p>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-[12px] mb-6 text-sm">
        ✅ {{ session('success') }}
    </div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        @method('PUT')

        {{-- ═══════════════════════════════════════════════ --}}
        {{-- KOMISI & BIAYA --}}
        {{-- ═══════════════════════════════════════════════ --}}
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-6 shadow-sm">
            <div class="flex items-center gap-2 mb-4 border-b border-[#E5E5E5] pb-3">
                <span class="text-xl">📊</span>
                <h2 class="font-bold text-lg text-[#111111]">Komisi & Biaya</h2>
            </div>
            <div class="grid md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Komisi Platform (%)</label>
                    <input type="number" name="commission_rate" value="{{ $settings['commission_rate'] ?? '5' }}"
                           class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition"
                           step="0.01" min="0" max="100">
                    <p class="text-[10px] text-gray-400 mt-1 font-light">Persentase komisi dari setiap transaksi</p>
                </div>
                <div>
                    <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Komisi Warung (%)</label>
                    <input type="number" name="warung_commission_rate" value="{{ $settings['warung_commission_rate'] ?? '2' }}"
                           class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition"
                           step="0.01" min="0" max="100">
                    <p class="text-[10px] text-gray-400 mt-1 font-light">Komisi untuk warung pembayaran</p>
                </div>
                <div>
                    <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Biaya Admin Top Up (Rp)</label>
                    <input type="number" name="topup_admin_fee" value="{{ $settings['topup_admin_fee'] ?? '3500' }}"
                           class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition"
                           min="0">
                    <p class="text-[10px] text-gray-400 mt-1 font-light">Biaya admin setiap top up saldo</p>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════ --}}
        {{-- TRAVEL REGULER --}}
        {{-- ═══════════════════════════════════════════════ --}}
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-6 shadow-sm">
            <div class="flex items-center gap-2 mb-4 border-b border-[#E5E5E5] pb-3">
                <span class="text-xl">🚌</span>
                <h2 class="font-bold text-lg text-[#111111]">Travel Reguler</h2>
            </div>
            
            <h3 class="font-mono uppercase tracking-wider text-xs font-bold text-[#111111] mb-3">Biaya Travel</h3>
            <div class="grid md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Biaya Layanan Travel (Rp)</label>
                    <input type="number" name="service_fee" value="{{ $settings['service_fee'] ?? '5000' }}"
                           class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" min="0">
                    <p class="text-[10px] text-gray-400 mt-1 font-light">Biaya layanan per booking travel</p>
                </div>
                <div>
                    <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Biaya Platform Travel (%)</label>
                    <input type="number" name="platform_fee_percent" value="{{ $settings['platform_fee_percent'] ?? '3' }}"
                           class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition"
                           step="0.01" min="0" max="100">
                    <p class="text-[10px] text-gray-400 mt-1 font-light">Persentase biaya platform travel</p>
                </div>
            </div>

            <h3 class="font-mono uppercase tracking-wider text-xs font-bold text-[#111111] mb-3">Pengaturan Umum</h3>
            <div class="grid md:grid-cols-3 gap-4 mb-6">
                <div>
                    <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Timeout Pembayaran (menit)</label>
                    <input type="number" name="payment_timeout" value="{{ $settings['payment_timeout'] ?? '30' }}"
                           class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" min="1">
                    <p class="text-[10px] text-gray-400 mt-1 font-light">Batas waktu pembayaran sebelum expired</p>
                </div>
                <div>
                    <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Min Hari Jadwal</label>
                    <input type="number" name="schedule_min_days" value="{{ $settings['schedule_min_days'] ?? '1' }}"
                           class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" min="1">
                    <p class="text-[10px] text-gray-400 mt-1 font-light">Minimal hari sebelum keberangkatan</p>
                </div>
                <div>
                    <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Expiry Kode Bayar (jam)</label>
                    <input type="number" name="payment_code_expiry_hours" value="{{ $settings['payment_code_expiry_hours'] ?? '24' }}"
                           class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" min="1">
                    <p class="text-[10px] text-gray-400 mt-1 font-light">Masa berlaku kode bayar warung</p>
                </div>
            </div>

            <div class="border-t border-[#E5E5E5] pt-4">
                <h3 class="font-mono uppercase tracking-wider text-xs font-bold text-[#111111] mb-3">🔒 Kebijakan Pembatalan Travel</h3>
                <div class="grid md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Fee Pembatalan (%)</label>
                        <input type="number" name="travel_cancellation_percent" value="{{ $settings['travel_cancellation_percent'] ?? '25' }}"
                               class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" min="0" max="100">
                        <p class="text-[10px] text-gray-400 mt-1 font-light">Persentase biaya pembatalan dari total</p>
                    </div>
                    <div>
                        <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Batas Cancel (jam)</label>
                        <input type="number" name="travel_cancellation_hours" value="{{ $settings['travel_cancellation_hours'] ?? '24' }}"
                               class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" min="1">
                        <p class="text-[10px] text-gray-400 mt-1 font-light">Batas waktu cancel sebelum berangkat</p>
                    </div>
                    <div>
                        <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Refund Perlu Approval (Rp)</label>
                        <input type="number" name="travel_refund_approval_limit" value="{{ $settings['travel_refund_approval_limit'] ?? '100000' }}"
                               class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" min="0">
                        <p class="text-[10px] text-gray-400 mt-1 font-light">Batas nominal refund yang perlu approval admin</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════ --}}
        {{-- TOUR / WISATA --}}
        {{-- ═══════════════════════════════════════════════ --}}
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-6 shadow-sm">
            <div class="flex items-center gap-2 mb-4 border-b border-[#E5E5E5] pb-3">
                <span class="text-xl">🏝️</span>
                <h2 class="font-bold text-lg text-[#111111]">Tour / Wisata</h2>
            </div>
            
            <h3 class="font-mono uppercase tracking-wider text-xs font-bold text-[#111111] mb-3">Biaya Tour</h3>
            <div class="grid md:grid-cols-3 gap-4 mb-6">
                <div>
                    <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Biaya Layanan Tour (Rp)</label>
                    <input type="number" name="tour_service_fee" value="{{ $settings['tour_service_fee'] ?? '5000' }}"
                           class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" min="0">
                    <p class="text-[10px] text-gray-400 mt-1 font-light">Biaya layanan per booking tour</p>
                </div>
                <div>
                    <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Biaya Platform Tour (%)</label>
                    <input type="number" name="tour_platform_fee_percent" value="{{ $settings['tour_platform_fee_percent'] ?? '3' }}"
                           class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition"
                           step="0.01" min="0" max="100">
                    <p class="text-[10px] text-gray-400 mt-1 font-light">Persentase biaya platform tour</p>
                </div>
                <div>
                    <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Default Min Peserta</label>
                    <input type="number" name="tour_min_participants" value="{{ $settings['tour_min_participants'] ?? '5' }}"
                           class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" min="1">
                    <p class="text-[10px] text-gray-400 mt-1 font-light">Default minimal peserta tour</p>
                </div>
            </div>

            <div class="border-t border-[#E5E5E5] pt-4">
                <h3 class="font-mono uppercase tracking-wider text-xs font-bold text-[#111111] mb-3">🔒 Kebijakan Pembatalan Tour</h3>
                
                <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-4 mb-3">
                    <p class="font-semibold text-sm text-[#111111] mb-2">🟢 Pembatalan Lebih dari 7 Hari</p>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Fee Pembatalan (%)</label>
                            <input type="number" name="tour_cancel_fee_15_percent" value="{{ $settings['tour_cancel_fee_15_percent'] ?? '15' }}"
                                   class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-white rounded-lg px-3 text-sm" min="0" max="100">
                        </div>
                        <div>
                            <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Batas Minimal (jam)</label>
                            <input type="number" name="tour_cancel_hours_15" value="{{ $settings['tour_cancel_hours_15'] ?? '168' }}"
                                   class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-white rounded-lg px-3 text-sm" min="1">
                            <p class="text-[10px] text-gray-400 mt-1 font-light">{{ ($settings['tour_cancel_hours_15'] ?? 168) / 24 }} hari</p>
                        </div>
                    </div>
                </div>

                <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-4 mb-3">
                    <p class="font-semibold text-sm text-[#111111] mb-2">🟡 Pembatalan 3-7 Hari</p>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Fee Pembatalan (%)</label>
                            <input type="number" name="tour_cancel_fee_30_percent" value="{{ $settings['tour_cancel_fee_30_percent'] ?? '30' }}"
                                   class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-white rounded-lg px-3 text-sm" min="0" max="100">
                        </div>
                        <div>
                            <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Batas Minimal (jam)</label>
                            <input type="number" name="tour_cancel_hours_30" value="{{ $settings['tour_cancel_hours_30'] ?? '72' }}"
                                   class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-white rounded-lg px-3 text-sm" min="1">
                            <p class="text-[10px] text-gray-400 mt-1 font-light">{{ ($settings['tour_cancel_hours_30'] ?? 72) / 24 }} hari</p>
                        </div>
                    </div>
                </div>

                <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-4">
                    <p class="font-semibold text-sm text-[#111111] mb-2">🔴 Pembatalan 1-3 Hari</p>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Fee Pembatalan (%)</label>
                            <input type="number" name="tour_cancel_fee_50_percent" value="{{ $settings['tour_cancel_fee_50_percent'] ?? '50' }}"
                                   class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-white rounded-lg px-3 text-sm" min="0" max="100">
                        </div>
                        <div>
                            <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Batas Minimal (jam)</label>
                            <input type="number" name="tour_cancel_hours_50" value="{{ $settings['tour_cancel_hours_50'] ?? '24' }}"
                                   class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-white rounded-lg px-3 text-sm" min="1">
                            <p class="text-[10px] text-gray-400 mt-1 font-light">{{ ($settings['tour_cancel_hours_50'] ?? 24) / 24 }} hari</p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-3 bg-red-50 border border-red-200 rounded-[12px] p-3 text-sm text-red-700 font-light">
                    ⚠️ Pembatalan kurang dari {{ ($settings['tour_cancel_hours_50'] ?? 24) / 24 }} hari = <strong>tidak dapat refund (100%)</strong>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════ --}}
        {{-- RENTAL / SEWA KENDARAAN --}}
        {{-- ═══════════════════════════════════════════════ --}}
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-6 shadow-sm">
            <div class="flex items-center gap-2 mb-4 border-b border-[#E5E5E5] pb-3">
                <span class="text-xl">🚐</span>
                <h2 class="font-bold text-lg text-[#111111]">Rental / Sewa Kendaraan</h2>
            </div>
            
            <h3 class="font-mono uppercase tracking-wider text-xs font-bold text-[#111111] mb-3">Biaya Rental</h3>
            <div class="grid md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Biaya Layanan Rental (Rp)</label>
                    <input type="number" name="rental_service_fee" value="{{ $settings['rental_service_fee'] ?? '5000' }}"
                           class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" min="0">
                    <p class="text-[10px] text-gray-400 mt-1 font-light">Biaya layanan per booking rental</p>
                </div>
                <div>
                    <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Biaya Platform Rental (%)</label>
                    <input type="number" name="rental_platform_fee_percent" value="{{ $settings['rental_platform_fee_percent'] ?? '3' }}"
                           class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition"
                           step="0.01" min="0" max="100">
                    <p class="text-[10px] text-gray-400 mt-1 font-light">Persentase biaya platform rental</p>
                </div>
            </div>

            <div class="border-t border-[#E5E5E5] pt-4">
                <h3 class="font-mono uppercase tracking-wider text-xs font-bold text-[#111111] mb-3">🔒 Kebijakan Pembatalan Rental</h3>
                <p class="text-[10px] text-gray-400 mb-4 font-light">Biaya pembatalan dihitung berdasarkan jarak waktu dari tanggal mulai sewa.</p>
                
                <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-4 mb-3">
                    <p class="font-semibold text-sm text-[#111111] mb-2">🟢 Pembatalan Lebih dari 7 Hari</p>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Fee Pembatalan (%)</label>
                            <input type="number" name="rental_cancel_fee_10_percent" value="{{ $settings['rental_cancel_fee_10_percent'] ?? '10' }}"
                                   class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-white rounded-lg px-3 text-sm" min="0" max="100">
                        </div>
                        <div>
                            <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Batas Minimal (jam)</label>
                            <input type="number" name="rental_cancel_hours_10" value="{{ $settings['rental_cancel_hours_10'] ?? '168' }}"
                                   class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-white rounded-lg px-3 text-sm" min="1">
                            <p class="text-[10px] text-gray-400 mt-1 font-light">{{ ($settings['rental_cancel_hours_10'] ?? 168) / 24 }} hari</p>
                        </div>
                    </div>
                </div>

                <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-4 mb-3">
                    <p class="font-semibold text-sm text-[#111111] mb-2">🟡 Pembatalan 3-7 Hari</p>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Fee Pembatalan (%)</label>
                            <input type="number" name="rental_cancel_fee_25_percent" value="{{ $settings['rental_cancel_fee_25_percent'] ?? '25' }}"
                                   class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-white rounded-lg px-3 text-sm" min="0" max="100">
                        </div>
                        <div>
                            <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Batas Minimal (jam)</label>
                            <input type="number" name="rental_cancel_hours_25" value="{{ $settings['rental_cancel_hours_25'] ?? '72' }}"
                                   class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-white rounded-lg px-3 text-sm" min="1">
                            <p class="text-[10px] text-gray-400 mt-1 font-light">{{ ($settings['rental_cancel_hours_25'] ?? 72) / 24 }} hari</p>
                        </div>
                    </div>
                </div>

                <div class="bg-[#F5F5F5] border border-[#E5E5E5] rounded-[12px] p-4">
                    <p class="font-semibold text-sm text-[#111111] mb-2">🔴 Pembatalan 1-3 Hari</p>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Fee Pembatalan (%)</label>
                            <input type="number" name="rental_cancel_fee_50_percent" value="{{ $settings['rental_cancel_fee_50_percent'] ?? '50' }}"
                                   class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-white rounded-lg px-3 text-sm" min="0" max="100">
                        </div>
                        <div>
                            <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Batas Minimal (jam)</label>
                            <input type="number" name="rental_cancel_hours_50" value="{{ $settings['rental_cancel_hours_50'] ?? '24' }}"
                                   class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-white rounded-lg px-3 text-sm" min="1">
                            <p class="text-[10px] text-gray-400 mt-1 font-light">{{ ($settings['rental_cancel_hours_50'] ?? 24) / 24 }} hari</p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-3 bg-red-50 border border-red-200 rounded-[12px] p-3 text-sm text-red-700 font-light">
                    ⚠️ Pembatalan kurang dari {{ ($settings['rental_cancel_hours_50'] ?? 24) / 24 }} hari sebelum mulai sewa = <strong>tidak dapat refund (100%)</strong>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════ --}}
        {{-- WITHDRAWAL --}}
        {{-- ═══════════════════════════════════════════════ --}}
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-6 shadow-sm">
            <div class="flex items-center gap-2 mb-4 border-b border-[#E5E5E5] pb-3">
                <span class="text-xl">💰</span>
                <h2 class="font-bold text-lg text-[#111111]">Withdrawal / Penarikan</h2>
            </div>
            <div class="grid md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Minimal Withdrawal (Rp)</label>
                    <input type="number" name="minimal_withdrawal" value="{{ $settings['minimal_withdrawal'] ?? '100000' }}"
                           class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" min="0">
                    <p class="text-[10px] text-gray-400 mt-1 font-light">Saldo minimal yang bisa ditarik</p>
                </div>
                <div>
                    <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Biaya Admin Withdrawal (Rp)</label>
                    <input type="number" name="withdrawal_admin_fee" value="{{ $settings['withdrawal_admin_fee'] ?? '5000' }}"
                           class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" min="0">
                    <p class="text-[10px] text-gray-400 mt-1 font-light">Biaya potongan setiap withdrawal</p>
                </div>
                <div>
                    <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Auto-Approve Limit (Rp)</label>
                    <input type="number" name="auto_approve_limit" value="{{ $settings['auto_approve_limit'] ?? '5000000' }}"
                           class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" min="0">
                    <p class="text-[10px] text-gray-400 mt-1 font-light">Batas nominal withdrawal auto-approve</p>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════ --}}
        {{-- REFERRAL --}}
        {{-- ═══════════════════════════════════════════════ --}}
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-6 shadow-sm">
            <div class="flex items-center gap-2 mb-4 border-b border-[#E5E5E5] pb-3">
                <span class="text-xl">🎁</span>
                <h2 class="font-bold text-lg text-[#111111]">Program Referral</h2>
            </div>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Diskon Referral (%)</label>
                    <input type="number" name="referral_discount_percent" value="{{ $settings['referral_discount_percent'] ?? '20' }}"
                           class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" min="0" max="100">
                    <p class="text-[10px] text-gray-400 mt-1 font-light">Persentase diskon untuk pengajak (referrer)</p>
                </div>
                <div>
                    <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Maks Diskon Referral (Rp)</label>
                    <input type="number" name="referral_discount_max" value="{{ $settings['referral_discount_max'] ?? '30000' }}"
                           class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" min="0">
                    <p class="text-[10px] text-gray-400 mt-1 font-light">Batas maksimal diskon referral</p>
                </div>
            </div>
            <div class="mt-3 bg-blue-50 border border-blue-200 rounded-[12px] p-3 text-sm text-blue-700 font-light">
                💡 Customer yang mengajak teman akan mendapatkan promo diskon setelah temannya berhasil bertransaksi pertama kali.
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════ --}}
        {{-- KONTAK & UMUM --}}
        {{-- ═══════════════════════════════════════════════ --}}
        <div class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 mb-6 shadow-sm">
            <div class="flex items-center gap-2 mb-4 border-b border-[#E5E5E5] pb-3">
                <span class="text-xl">📞</span>
                <h2 class="font-bold text-lg text-[#111111]">Kontak & Umum</h2>
            </div>
            <div class="grid md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Nomor WhatsApp Support</label>
                    <input type="text" name="support_phone" value="{{ $settings['support_phone'] ?? '081234567890' }}"
                           class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" placeholder="081234567890">
                    <p class="text-[10px] text-gray-400 mt-1 font-light">Ditampilkan di footer dan kontak support</p>
                </div>
                <div>
                    <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Email Support</label>
                    <input type="email" name="support_email" value="{{ $settings['support_email'] ?? 'support@gomad.id' }}"
                           class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" placeholder="support@gomad.id">
                    <p class="text-[10px] text-gray-400 mt-1 font-light">Email customer service resmi</p>
                </div>
                <div>
                    <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Default Deposit COD (Rp)</label>
                    <input type="number" name="cod_min_deposit_default" value="{{ $settings['cod_min_deposit_default'] ?? '500000' }}"
                           class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" min="0">
                    <p class="text-[10px] text-gray-400 mt-1 font-light">Default minimal deposit untuk fitur COD</p>
                </div>
            </div>
            <div class="grid md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Nama Aplikasi</label>
                    <input type="text" name="app_name" value="{{ $settings['app_name'] ?? 'GoMad' }}"
                           class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" placeholder="GoMad">
                    <p class="text-[10px] text-gray-400 mt-1 font-light">Nama brand yang ditampilkan di aplikasi</p>
                </div>
                <div>
                    <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Tagline</label>
                    <input type="text" name="app_tagline" value="{{ $settings['app_tagline'] ?? 'Mobilitas orèng Madhurâ' }}"
                           class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" placeholder="Mobilitas orèng Madhurâ">
                    <p class="text-[10px] text-gray-400 mt-1 font-light">Tagline yang ditampilkan di hero section & footer</p>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════ --}}
        {{-- SUBMIT BUTTON --}}
        {{-- ═══════════════════════════════════════════════ --}}
        <div class="flex gap-4 mb-8">
            <button type="submit" class="btn-gomad-primary px-8 py-3.5 rounded-[12px] font-bold text-lg flex items-center gap-2">
                <span>💾</span> SIMPAN SEMUA PENGATURAN
            </button>
            <a href="{{ route('admin.dashboard') }}" class="border border-[#E5E5E5] text-gray-700 px-8 py-3.5 rounded-[12px] font-semibold text-lg hover:bg-[#F5F5F5] transition">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection