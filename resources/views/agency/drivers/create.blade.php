@extends('layouts.agency')

@section('title', 'Tambah Driver')
@section('content')
<div class="max-w-lg mx-auto">
    <h1 class="text-2xl font-bold text-[#111111] mb-6">Tambah Driver</h1>

    <form action="{{ route('agency.drivers.store') }}" method="POST" enctype="multipart/form-data" class="bg-white border border-[#E5E5E5] rounded-[12px] p-6 shadow-sm space-y-4">
        @csrf
        
        <div>
            <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Foto Driver</label>
            <div class="flex items-center gap-4">
                <div class="w-24 h-24 rounded-full bg-[#F5F5F5] flex items-center justify-center text-3xl overflow-hidden border border-[#E5E5E5]" id="previewContainer">
                    👨‍✈️
                </div>
                <div class="flex-1">
                    <input type="file" name="avatar" id="avatarInput" accept="image/*" 
                           class="w-full text-sm" onchange="previewImage(event)">
                    <p class="text-[10px] text-gray-400 mt-1 font-light">Format: JPG, PNG, WEBP. Max 2MB</p>
                </div>
            </div>
        </div>

        <div>
            <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Nama Lengkap <span class="text-[#C1121F]">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" 
                   class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" required>
        </div>
        <div>
            <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Email <span class="text-[#C1121F]">*</span></label>
            <input type="email" name="email" value="{{ old('email') }}" 
                   class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" required>
        </div>
        <div>
            <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Nomor HP <span class="text-[#C1121F]">*</span></label>
            <input type="text" name="phone" value="{{ old('phone') }}" 
                   class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" required>
        </div>
        <div>
            <label class="block text-[10px] font-mono uppercase tracking-wider text-gray-500 mb-1">Password <span class="text-[#C1121F]">*</span></label>
            <input type="password" name="password" class="w-full px-0 py-2 border-b-2 border-[#E5E5E5] focus:border-[#C1121F] outline-none bg-transparent text-[#111111] transition" required>
        </div>
        <button type="submit" class="btn-gomad-primary w-full py-3 rounded-[12px] font-bold">
            💾 SIMPAN DRIVER
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