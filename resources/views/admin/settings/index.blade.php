@extends('layouts.admin')

@section('title', 'Pengaturan')
@section('content')

@php
    $settings = \App\Models\PlatformSetting::getAllSettings();
@endphp

<div>
    <h1 class="text-lg font-bold text-[#111111] mb-6">Pengaturan Platform</h1>

    <form action="{{ route('admin.settings.update') }}" method="POST" class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm">
        @csrf
        @method('PUT')

        <div class="grid md:grid-cols-2 gap-4">
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
                <p class="text-[10px] text-gray-400 mt-1 font-light">Persentase komisi untuk warung pembayaran</p>
            </div>

            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Timeout Pembayaran (menit)</label>
                <input type="number" name="payment_timeout" value="{{ $settings['payment_timeout'] ?? '30' }}"
                       class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition"
                       min="1">
                <p class="text-[10px] text-gray-400 mt-1 font-light">Batas waktu pembayaran sebelum booking expired</p>
            </div>

            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Minimal Hari Jadwal</label>
                <input type="number" name="schedule_min_days" value="{{ $settings['schedule_min_days'] ?? '30' }}"
                       class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition"
                       min="1">
                <p class="text-[10px] text-gray-400 mt-1 font-light">Jumlah hari jadwal ditampilkan ke depan</p>
            </div>

            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Minimal Withdrawal (Rp)</label>
                <input type="number" name="minimal_withdrawal" value="{{ $settings['minimal_withdrawal'] ?? '100000' }}"
                       class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition"
                       min="0">
                <p class="text-[10px] text-gray-400 mt-1 font-light">Saldo minimal yang bisa ditarik</p>
            </div>

            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Biaya Admin Withdrawal (Rp)</label>
                <input type="number" name="withdrawal_admin_fee" value="{{ $settings['withdrawal_admin_fee'] ?? '5000' }}"
                       class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition"
                       min="0">
                <p class="text-[10px] text-gray-400 mt-1 font-light">Biaya potongan setiap withdrawal</p>
            </div>

            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Auto-Approve Limit (Rp)</label>
                <input type="number" name="auto_approve_limit" value="{{ $settings['auto_approve_limit'] ?? '5000000' }}"
                       class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition"
                       min="0">
                <p class="text-[10px] text-gray-400 mt-1 font-light">Batas nominal withdrawal yang auto-approve</p>
            </div>

            <div>
                <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Nomor Support</label>
                <input type="text" name="support_phone" value="{{ $settings['support_phone'] ?? '081234567890' }}"
                       class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition"
                       placeholder="0812-3456-7890">
                <p class="text-[10px] text-gray-400 mt-1 font-light">Nomor yang ditampilkan di footer & kontak</p>
            </div>
        </div>

        <div class="mt-6 pt-6 border-t border-[#E5E5E5]">
            <button type="submit" class="btn-gomad-primary px-8 py-3 rounded-[12px] font-semibold">
                💾 SIMPAN PENGATURAN
            </button>
        </div>
    </form>
</div>
@endsection