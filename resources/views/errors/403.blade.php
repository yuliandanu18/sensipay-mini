@extends('sensipay.layout')

@section('title', '403 - Tidak Punya Akses')
@section('page_title', 'Akses Ditolak')

@section('content')
    <div class="max-w-xl mx-auto mt-10 p-6 bg-white border rounded shadow-sm">
        <h1 class="text-2xl font-bold mb-4">403 | Tidak Punya Akses</h1>

        @auth
            <p class="mb-4">
                Maaf, akun <span class="font-semibold">{{ auth()->user()->name }}</span>
                tidak memiliki izin untuk membuka halaman ini.
            </p>

            @php
                $role = auth()->user()->role ?? null;
            @endphp

            @if ($role === 'parent')
                <p class="mb-4 text-sm text-slate-600">
                    Untuk orang tua murid, silakan kembali ke dashboard tagihan.
                </p>
                <a href="{{ route('sensipay.parent.dashboard') }}"
                   class="inline-block px-4 py-2 text-sm bg-slate-800 text-white rounded hover:bg-slate-700">
                    Kembali ke Dashboard Orang Tua
                </a>
            @else
                <p class="mb-4 text-sm text-slate-600">
                    Silakan kembali ke dashboard utama Sensipay.
                </p>
                <a href="{{ route('sensipay.invoices.index') }}"
                   class="inline-block px-4 py-2 text-sm bg-slate-800 text-white rounded hover:bg-slate-700">
                    Kembali ke Dashboard Invoice
                </a>
            @endif
        @else
            <p class="mb-4 text-sm text-slate-600">
                Anda belum login. Silakan masuk terlebih dahulu.
            </p>
            <a href="{{ route('login') }}"
               class="inline-block px-4 py-2 text-sm bg-slate-800 text-white rounded hover:bg-slate-700">
                Login
            </a>
        @endauth
    </div>
@endsection
