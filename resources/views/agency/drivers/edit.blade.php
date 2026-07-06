@extends('layouts.agency')

@section('title', 'Edit Driver')
@section('content')
<div class="max-w-lg mx-auto">
    <h1 class="text-2xl font-bold text-[#111111] mb-6">Edit Driver</h1>

    <form action="{{ route('agency.drivers.update', $user) }}" method="POST" enctype="multipart/form-data" class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm space-y-4">
        @csrf @method('PUT')
        
        <div>
            <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Foto Driver</label>
            <div class="flex items-center gap-4">
                <div class="w-24 h-24 rounded-full overflow-hidden flex items-center justify-center text-3xl border border-[#E5E5E5]" id="previewContainer">
                    @if($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" class="w-full h-full object-cover">
                    @else
                    <div class="w-full h-full bg-[#F5F5F5] flex items-center justify-center">👨‍✈️</div>
                    @endif
                </div>
                <div class="flex-1">
                    <input type="file" name="avatar" id="avatarInput" accept="image/*" 
                           class="w-full text-sm" onchange="previewImage(event)">
                    <p class="text-[10px] text-gray-400 mt-1 font-light">Biarkan kosong jika tidak ingin mengubah foto</p>
                </div>
            </div>
        </div>

        <div>
            <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Nama Lengkap <span class="text-[#C1121F]">*</span></label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                   class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" required>
        </div>
        <div>
            <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Email <span class="text-[#C1121F]">*</span></label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                   class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" required>
        </div>
        <div>
            <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Nomor HP <span class="text-[#C1121F]">*</span></label>
            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" 
                   class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" required>
        </div>
        <div>
            <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Password Baru (kosongkan jika tidak ingin mengubah)</label>
            <input type="password" name="password" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition">
        </div>
        <div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ $user->is_active ? 'checked' : '' }} class="w-5 h-5 rounded border-[#E5E5E5] text-[#C1121F] focus:ring-[#C1121F]">
                <span class="text-sm font-medium text-[#111111]">Driver Aktif</span>
            </label>
        </div>
        <button type="submit" class="btn-gomad-primary w-full py-3 rounded-[12px] font-bold">
            💾 UPDATE DRIVER
        </button>
    </form>
</div>

@push('scripts')
<script>
function previewImage(event) {
    var file = event.target.files[0];
    if (file) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var container = document.getElementById('previewContainer');
            container.innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-cover">';
        };
        reader.readAsDataURL(file);
    }
}
</script>
@endpush
@endsection