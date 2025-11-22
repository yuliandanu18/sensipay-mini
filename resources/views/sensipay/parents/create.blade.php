@extends('sensipay.layout')

@section('title', 'Tambah Parent')
@section('page_title', 'Tambah Parent')

@section('content')
    <div class="max-w-md mx-auto bg-white border rounded p-4 shadow-sm">
        <h1 class="text-lg font-bold mb-3">Tambah Akun Orang Tua</h1>

        @if ($errors->any())
            <div class="mb-3 p-3 rounded bg-red-100 text-red-800 text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('sensipay.parents.store') }}" method="POST" class="space-y-3 text-sm">
            @csrf
            <div>
                <label class="block text-xs font-medium mb-1">Nama</label>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="w-full border rounded px-2 py-1 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       class="w-full border rounded px-2 py-1 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium mb-1">Password</label>
                <input type="text" name="password"
                       class="w-full border rounded px-2 py-1 text-sm">
                <p class="text-[11px] text-slate-500 mt-1">
                    Bisa diisi password sementara yang nanti dikirim ke orang tua.
                </p>
            </div>
            <button type="submit"
                    class="px-3 py-1 rounded bg-slate-900 text-white hover:bg-slate-800 text-xs">
                Simpan
            </button>
        </form>
    </div>
@endsection
