@extends('layouts.public')

@section('title', 'Daftar')
@section('meta_description', 'Daftar akun GoMad dan mulai perjalanan Anda. Tersedia untuk Customer, Agency, dan Warung.')
@section('og_image', asset('images/og-register.jpg'))

@section('content')
<div class="section">
    <div class="container-custom">
        <div class="max-w-md mx-auto">
            <div class="text-center mb-8">
                <a href="{{ route('home') }}" class="inline-block mb-6">
                    <img src="{{ asset('images/logo.svg') }}" alt="GoMad" class="h-10 mx-auto">
                </a>
                <h1 class="text-2xl font-bold text-secondary mb-2">Daftar GoMad</h1>
                <p class="text-gray-600" id="registerSubtitle">Pilih jenis akun untuk melanjutkan</p>
            </div>

            @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 text-sm">
                @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
                @endforeach
            </div>
            @endif

            <div x-data="{ 
                step: 1,
                role: 'customer',
                showPassword: false, 
                showConfirm: false,
                password: '',
                get passwordStrength() {
                    if (this.password.length === 0) return { text: '', color: '', width: '0%' };
                    if (this.password.length < 6) return { text: 'Lemah', color: 'bg-red-500', width: '25%' };
                    if (this.password.length < 8) return { text: 'Cukup', color: 'bg-yellow-500', width: '50%' };
                    if (this.password.length < 10) return { text: 'Baik', color: 'bg-blue-500', width: '75%' };
                    return { text: 'Kuat', color: 'bg-green-500', width: '100%' };
                },
                selectRole(selected) {
                    this.role = selected;
                    this.step = 2;
                    var titles = {
                        'customer': 'Daftar sebagai Customer',
                        'agency': 'Daftar sebagai Agency',
                        'payment_agent': 'Daftar sebagai Warung GoMad'
                    };
                    document.getElementById('registerSubtitle').textContent = titles[selected];
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },
                backToStep1() {
                    this.step = 1;
                    document.getElementById('registerSubtitle').textContent = 'Pilih jenis akun untuk melanjutkan';
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            }">
                
                {{-- STEP 1: Pilih Role --}}
                <div x-show="step === 1" class="card p-6 md:p-8">
                    <div class="space-y-3">
                        {{-- Customer --}}
                        <button @click="selectRole('customer')" 
                                class="w-full flex items-center gap-4 p-5 border-2 border-gray-200 rounded-2xl hover:border-primary-400 hover:bg-primary-50/50 transition group text-left">
                            <div class="w-12 h-12 bg-primary-50 rounded-xl flex items-center justify-center text-xl group-hover:scale-105 transition-transform flex-shrink-0">
                                <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-secondary">Customer</h3>
                                <p class="text-sm text-gray-500 truncate">Booking travel, cari jadwal, bayar online atau di warung</p>
                            </div>
                            <svg class="w-5 h-5 text-gray-300 group-hover:text-primary-500 transition flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>

                        {{-- Agency --}}
                        <button @click="selectRole('agency')" 
                                class="w-full flex items-center gap-4 p-5 border-2 border-gray-200 rounded-2xl hover:border-blue-400 hover:bg-blue-50/50 transition group text-left">
                            <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-xl group-hover:scale-105 transition-transform flex-shrink-0">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-secondary">Agency</h3>
                                <p class="text-sm text-gray-500 truncate">Kelola jadwal, armada, driver, dan terima booking</p>
                            </div>
                            <svg class="w-5 h-5 text-gray-300 group-hover:text-blue-500 transition flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>

                        {{-- Warung --}}
                        <button @click="selectRole('payment_agent')" 
                                class="w-full flex items-center gap-4 p-5 border-2 border-gray-200 rounded-2xl hover:border-green-400 hover:bg-green-50/50 transition group text-left">
                            <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center text-xl group-hover:scale-105 transition-transform flex-shrink-0">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-secondary">Warung GoMad</h3>
                                <p class="text-sm text-gray-500 truncate">Terima pembayaran cash dari customer, jadi mitra resmi</p>
                            </div>
                            <svg class="w-5 h-5 text-gray-300 group-hover:text-green-500 transition flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>

                    <div class="mt-8 text-center">
                        <p class="text-sm text-gray-500">
                            Sudah punya akun? 
                            <a href="{{ route('login') }}" class="text-primary-600 font-semibold hover:underline">Masuk di sini</a>
                        </p>
                    </div>
                </div>

                {{-- STEP 2: Form Register --}}
                <div x-show="step === 2" class="card p-6 md:p-8">
                    
                    {{-- Back Button --}}
                    <button @click="backToStep1()" class="text-sm text-gray-500 hover:text-primary-600 mb-6 flex items-center gap-1 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Ganti jenis akun
                    </button>

                    <form action="{{ route('register') }}" method="POST">
                        @csrf
                        <input type="hidden" name="role" x-bind:value="role">

                        {{-- Nama --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-secondary mb-1">
                                <span x-text="role === 'payment_agent' ? 'Nama Pemilik Warung' : 'Nama Lengkap'"></span>
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name') }}" 
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-600 focus:border-primary-600 bg-gray-50 transition" 
                                   placeholder="Nama lengkap Anda" required>
                        </div>

                        {{-- Email --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-secondary mb-1">Alamat Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email') }}" 
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-600 focus:border-primary-600 bg-gray-50 transition" 
                                   placeholder="nama@email.com" required>
                        </div>

                        {{-- HP --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-secondary mb-1">
                                <span x-text="role === 'payment_agent' ? 'Nomor HP Pemilik' : 'Nomor HP (WhatsApp)'"></span>
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="phone" value="{{ old('phone') }}" 
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-600 focus:border-primary-600 bg-gray-50 transition" 
                                   placeholder="081234567890" required>
                        </div>

                        {{-- Agency Fields --}}
                        <div x-show="role === 'agency'" x-cloak>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-secondary mb-1">Nama Agency <span class="text-red-500">*</span></label>
                                <input type="text" name="agency_name" value="{{ old('agency_name') }}" 
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-600 focus:border-primary-600 bg-gray-50 transition" 
                                       placeholder="Contoh: Travel Jaya Abadi" required>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-secondary mb-1">Alamat Agency <span class="text-red-500">*</span></label>
                                <textarea name="address" rows="2" 
                                          class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-600 focus:border-primary-600 bg-gray-50 transition" 
                                          placeholder="Alamat lengkap agency" required>{{ old('address') }}</textarea>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-secondary mb-1">Kontak Person <span class="text-red-500">*</span></label>
                                <input type="text" name="contact_person" value="{{ old('contact_person') }}" 
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-600 focus:border-primary-600 bg-gray-50 transition" 
                                       placeholder="Nama kontak person" required>
                            </div>
                        </div>

                        {{-- Warung Fields --}}
                        <div x-show="role === 'payment_agent'" x-cloak>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-secondary mb-1">Nama Warung <span class="text-red-500">*</span></label>
                                <input type="text" name="agent_name" value="{{ old('agent_name') }}" 
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-600 focus:border-primary-600 bg-gray-50 transition" 
                                       placeholder="Contoh: Warung Berkah" required>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-secondary mb-1">Alamat Warung <span class="text-red-500">*</span></label>
                                <textarea name="address" rows="2" 
                                          class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-600 focus:border-primary-600 bg-gray-50 transition" 
                                          placeholder="Alamat lengkap warung" required>{{ old('address') }}</textarea>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-secondary mb-1">PIN Konfirmasi <span class="text-red-500">*</span></label>
                                <input type="password" name="pin" maxlength="6" 
                                       class="w-full px-4 py-3 border border-gray-200 rounded-xl text-center text-lg tracking-widest focus:ring-2 focus:ring-primary-600 focus:border-primary-600 bg-gray-50 transition" 
                                       placeholder="6 digit angka" required>
                                <p class="text-xs text-gray-500 mt-1">PIN 6 digit digunakan untuk konfirmasi setiap pembayaran</p>
                            </div>
                        </div>

                        {{-- Password --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-secondary mb-1">Password <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input :type="showPassword ? 'text' : 'password'" name="password" x-model="password"
                                       class="w-full px-4 py-3 pr-12 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-600 focus:border-primary-600 bg-gray-50 transition" 
                                       placeholder="Minimal 8 karakter" required minlength="8">
                                <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                </button>
                            </div>
                            <div class="mt-2" x-show="password.length > 0">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs text-gray-500">Kekuatan password:</span>
                                    <span class="text-xs font-medium" x-text="passwordStrength.text"></span>
                                </div>
                                <div class="bg-gray-200 rounded-full h-1.5 overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-300" 
                                         :class="passwordStrength.color" 
                                         :style="'width: ' + passwordStrength.width"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Konfirmasi Password --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-secondary mb-1">Konfirmasi Password <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input :type="showConfirm ? 'text' : 'password'" name="password_confirmation"
                                       class="w-full px-4 py-3 pr-12 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-600 focus:border-primary-600 bg-gray-50 transition" 
                                       placeholder="Ulangi password" required>
                                <button type="button" @click="showConfirm = !showConfirm" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <svg x-show="!showConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg x-show="showConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                </button>
                            </div>
                        </div>

                        {{-- Referral (Hanya Customer) --}}
                        <div x-show="role === 'customer'" x-cloak class="mb-4">
                            <label class="block text-sm font-medium text-secondary mb-1">
                                Kode Referral <span class="text-xs text-gray-400">(Opsional)</span>
                            </label>
                            <input type="text" name="referral_code" value="{{ old('referral_code', request('ref')) }}" 
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-gray-50 uppercase transition" 
                                   placeholder="Contoh: BUDI123">
                            <p class="text-xs text-gray-500 mt-1">Dapatkan diskon dengan kode referral dari teman Anda</p>
                        </div>

                        {{-- Google Register (Hanya Customer) --}}
                        <div x-show="role === 'customer'" x-cloak class="mb-6">
                            <div class="relative">
                                <div class="absolute inset-0 flex items-center">
                                    <div class="w-full border-t border-gray-200"></div>
                                </div>
                                <div class="relative flex justify-center text-sm">
                                    <span class="px-3 bg-white text-gray-500">Atau daftar dengan</span>
                                </div>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('google.login') }}"
                                   class="w-full flex items-center justify-center gap-3 px-4 py-3 border border-gray-300 rounded-xl text-gray-700 font-medium hover:bg-gray-50 transition">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24">
                                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/>
                                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                    </svg>
                                    Daftar dengan Google
                                </a>
                            </div>
                        </div>

                        <button type="submit" class="w-full btn-primary mt-2 text-base py-3">
                            Daftar Sekarang
                        </button>
                    </form>

                    <div class="mt-6 text-center">
                        <p class="text-sm text-gray-600">
                            Sudah punya akun? 
                            <a href="{{ route('login') }}" class="text-primary-600 font-semibold hover:underline">Masuk di sini</a>
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection