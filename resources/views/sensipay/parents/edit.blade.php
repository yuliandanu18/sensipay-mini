
@extends('sensipay.layout')

@section('title', 'Atur Akun Orang Tua')

@section('content')
<div class="mb-4">
    <h1 class="text-xl font-semibold">Atur Akun Orang Tua</h1>
    <p class="text-xs text-slate-500">
        Hubungkan / buat akun orang tua untuk siswa berikut, agar bisa login dan melihat tagihan Sensipay.
    </p>
</div>

@if(session('status'))
    <div class="mb-3 p-2 rounded-lg text-[11px] bg-emerald-50 text-emerald-700">
        {{ session('status') }}
    </div>
@endif

@if(session('generated_parent_password'))
    <div class="mb-3 p-2 rounded-lg text-[11px] bg-amber-50 text-amber-800">
        <strong>Password baru akun orang tua:</strong>
        <span class="font-mono">{{ session('generated_parent_password') }}</span><br>
        Mohon dicatat dan dikirim ke orang tua. Password ini tidak akan ditampilkan lagi setelah halaman ditutup / di-refresh.
    </div>
@endif

<div class="bg-white rounded-xl shadow-sm p-4 text-xs space-y-4">
    <div>
        <div class="text-[11px] text-slate-500">Siswa</div>
        <div class="font-semibold text-sm">{{ $student->name }}</div>
        @if($student->school_name)
            <div class="text-[11px] text-slate-500">
                {{ $student->school_name }}
            </div>
        @endif
    </div>

    <form method="post" action="{{ route('sensipay.parents.update', $student) }}" class="space-y-3">
        @csrf

        <div>
            <label class="block text-[11px] text-slate-600 mb-1">
                Nama Orang Tua / Wali
            </label>
            <input type="text"
                   name="parent_name"
                   value="{{ old('parent_name', $parent->name ?? '') }}"
                   class="w-full border rounded-lg px-2 py-1 text-xs @error('parent_name') border-rose-400 @enderror">
            @error('parent_name')
                <div class="text-[11px] text-rose-600 mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label class="block text-[11px] text-slate-600 mb-1">
                Email Akun Orang Tua
            </label>
            <input type="email"
                   name="parent_email"
                   value="{{ old('parent_email', $parent->email ?? '') }}"
                   class="w-full border rounded-lg px-2 py-1 text-xs @error('parent_email') border-rose-400 @enderror">
            @error('parent_email')
                <div class="text-[11px] text-rose-600 mt-1">{{ $message }}</div>
            @enderror
            <p class="mt-1 text-[11px] text-slate-500">
                Jika email belum pernah dipakai, sistem akan otomatis membuat akun baru dengan role <code>parent</code>.
                Jika email sudah ada, user tersebut akan dijadikan akun orang tua untuk siswa ini.
            </p>
        </div>

        <div class="flex items-center justify-between mt-4">
            <a href="{{ route('sensipay.parents.index') }}"
               class="text-[11px] text-slate-600 hover:underline">
               &larr; Kembali ke daftar siswa
            </a>
            <button class="px-3 py-1.5 rounded-lg bg-sky-600 text-white text-xs">
                Simpan Pengaturan Orang Tua
            </button>
        </div>
    </form>
</div>
@endsection
