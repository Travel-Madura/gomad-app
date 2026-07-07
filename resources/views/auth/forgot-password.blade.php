@extends('layouts.public')

@section('title', 'Lupa Password')
@section('meta_description', 'Reset password akun GoMad Anda.')
@section('og_image', asset('images/og-login.jpg'))

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-20 px-6 bg-[#F5F5F5]">
    <div class="w-full max-w-md">
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-[#111111] mb-2">Lupa Password</h1>
            <p class="text-gray-500 font-light text-sm">Masukkan email Anda, kami akan kirim link reset password</p>
        </div>

        @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-[12px] mb-6 text-sm">
            {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-[12px] mb-6 text-sm">
            {{ $errors->first() }}
        </div>
        @endif

        <div class="bg-white p-8 shadow-sm border border-[#E5E5E5] rounded-[12px]">
            <form action="{{ route('password.email') }}" method="POST">
                @csrf

                <div class="mb-6">
                    <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Alamat Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition"
                           placeholder="nama@email.com" required>
                </div>

                <button type="submit" class="w-full btn-gomad-primary text-base py-3 rounded-[12px]">
                    Kirim Link Reset Password
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="text-sm text-[#C1121F] hover:underline font-medium">
                    Kembali ke halaman login
                </a>
            </div>
        </div>
    </div>
</div>
@endsection