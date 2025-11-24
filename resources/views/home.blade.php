{{-- resources/views/home.blade.php --}}
@php
    $user = auth()->user();
@endphp

{{-- Jika GUEST (belum login) --}}
@if(!$user)
    <x-guest-layout>
        <div class="p-6 text-center">
            <h1 class="text-2xl font-bold text-gray-800">Selamat datang di Bimbel JET</h1>
            <p class="text-gray-600 mt-2 text-sm">
                Kelola pembayaran, lihat tagihan, dan akses dashboard belajar dalam satu aplikasi.
            </p>

            <a href="{{ route('login') }}"
               class="mt-6 inline-block bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm font-semibold">
                Login untuk melanjutkan
            </a>
        </div>
    </x-guest-layout>

@else
    {{-- Jika SUDAH LOGIN → dashboard lengkap --}}
    @php
        $role = $user->role ?? null;
    @endphp

    <x-app-layout>
        <x-slot name="header">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-semibold text-lg text-gray-800 leading-tight">
                        Dashboard Bimbel JET
                    </h2>
                    <p class="text-xs text-gray-500 mt-1">
                        Tampilan ini sudah disesuaikan untuk HP. Cukup scroll dan tap kartu yang dibutuhkan.
                    </p>
                </div>
            </div>
        </x-slot>

        <div class="py-4">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

                {{-- Salam utama --}}
                <div class="bg-white rounded-2xl shadow-sm p-4 sm:p-5">
                    <p class="text-xs text-gray-500 mb-1">
                        Selamat datang di
                    </p>
                    <h1 class="text-xl font-semibold text-gray-900">
                        Bimbel JET Control Panel
                    </h1>
                    <p class="mt-2 text-sm text-gray-600">
                        Hai,
                        <span class="font-semibold text-gray-800">
                            {{ $user->name ?? 'Pengguna' }}
                        </span>.
                        Silakan pilih menu utama di bawah sesuai kebutuhan Anda.
                    </p>
                </div>

                {{-- Jika ORANG TUA --}}
                @if($role === 'parent')
                    <div class="space-y-3">

                        {{-- Kartu utama: Tagihan Anak --}}
                        <div class="bg-white rounded-2xl shadow-sm p-4 sm:p-5 flex flex-col gap-3">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <h2 class="text-sm font-semibold text-gray-900">
                                        Tagihan Bimbingan Belajar Anak
                                    </h2>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Lihat ringkasan tagihan, sisa pembayaran, dan kirim bukti transfer dengan mudah.
                                    </p>
                                </div>
                                <div class="hidden sm:flex items-center justify-center rounded-full bg-emerald-50 p-2">
                                    <span class="text-emerald-600 text-xs font-semibold">
                                        Parent
                                    </span>
                                </div>
                            </div>

                            <a href="{{ route('sensipay.parent.dashboard') }}"
                               class="mt-2 inline-flex w-full items-center justify-center rounded-full bg-emerald-600 px-4 py-2.5 text-xs font-semibold text-white
       hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1
       transition transform duration-100 active:scale-[0.98]">
                                Lihat Tagihan & Konfirmasi Pembayaran
                            </a>

                            <p class="mt-2 text-[11px] text-gray-500">
                                Tip: gunakan WhatsApp & galeri HP untuk foto bukti transfer, lalu unggah langsung dari
                                tombol di halaman tagihan.
                            </p>
                        </div>

                        {{-- Info bantuan --}}
                        <div class="bg-amber-50 border border-amber-100 rounded-2xl p-3 text-[11px] text-amber-800">
                            Jika ada kendala pembayaran atau salah nominal,
                            silakan hubungi admin Bimbel JET melalui WhatsApp yang tertera di brosur atau grup kelas.
                        </div>
                    </div>

                {{-- Jika INTERNAL (owner / director / finance / dll) --}}
                @else
                    <div class="space-y-3">

                        {{-- Quick status bar --}}
                        <div class="bg-slate-900 rounded-2xl p-4 text-xs text-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                            <div>
                                <p class="uppercase tracking-wide text-[10px] text-slate-400">
                                    Mode Admin / Internal
                                </p>
                                <p class="text-sm font-semibold">
                                    Kontrol keuangan & operasional Bimbel JET dalam satu tempat.
                                </p>
                            </div>
                            <div class="flex flex-wrap gap-2 text-[11px]">
                                <span class="inline-flex items-center rounded-full bg-slate-800/70 px-3 py-1">
                                    🔐 {{ $role ?? 'No Role' }}
                                </span>
                            </div>
                        </div>

                        {{-- GRID KARTU MENU UTAMA --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">

                            {{-- Sensipay: Approval Pembayaran --}}
                            <a href="{{ route('sensipay.payments.index') }}"
                               class="group bg-white rounded-2xl shadow-sm p-4 flex flex-col justify-between border border-slate-100
       transition transform duration-150 ease-out
       hover:-translate-y-0.5 hover:shadow-lg hover:border-emerald-400 active:translate-y-0"
>
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-sm font-semibold text-gray-900">
                                        Sensipay – Pembayaran
                                    </h3>
                                    <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-semibold text-emerald-700">
                                        Admin
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 mb-3">
                                    Lihat konfirmasi pembayaran orang tua, setujui / tolak, dan perbarui status invoice.
                                </p>
                                <div class="mt-auto flex items-center justify-between">
                                    <span class="text-[11px] text-emerald-700 font-semibold">
                                        Buka halaman approval
                                    </span>
                                    <span class="text-[16px] text-emerald-500 group-hover:translate-x-1 transition-transform">
                                        →
                                    </span>
                                </div>
                            </a>

                            {{-- Sensipay: Invoice --}}
                            <a href="{{ route('sensipay.invoices.index') }}"
                               class="group bg-white rounded-2xl shadow-sm p-4 flex flex-col justify-between border border-slate-100 hover:border-indigo-400 hover:shadow-md transition">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-sm font-semibold text-gray-900">
                                        Sensipay – Invoice
                                    </h3>
                                    <span class="rounded-full bg-indigo-50 px-2 py-0.5 text-[10px] font-semibold text-indigo-700">
                                        Finance
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 mb-3">
                                    Kelola tagihan siswa, cek total, sisa pembayaran, dan riwayat invoice per program.
                                </p>
                                <div class="mt-auto flex items-center justify-between">
                                    <span class="text-[11px] text-indigo-700 font-semibold">
                                        Kelola invoice
                                    </span>
                                    <span class="text-[16px] text-indigo-500 group-hover:translate-x-1 transition-transform">
                                        →
                                    </span>
                                </div>
                            </a>

                            {{-- Sensipay: Parents Management --}}
                            <a href="{{ route('sensipay.parents.index') }}"
                               class="group bg-white rounded-2xl shadow-sm p-4 flex flex-col justify-between border border-slate-100 hover:border-amber-400 hover:shadow-md transition">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-sm font-semibold text-gray-900">
                                        Data Orang Tua & Siswa
                                    </h3>
                                    <span class="rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-semibold text-amber-700">
                                        Relasi
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 mb-3">
                                    Atur keterkaitan akun orang tua dengan invoice dan siswa. Cocok untuk validasi & bantuan WA.
                                </p>
                                <div class="mt-auto flex items-center justify-between">
                                    <span class="text-[11px] text-amber-700 font-semibold">
                                        Kelola data parent
                                    </span>
                                    <span class="text-[16px] text-amber-500 group-hover:translate-x-1 transition-transform">
                                        →
                                    </span>
                                </div>
                            </a>

                            {{-- SLOT KARTU KOSONG UNTUK PROJECT LAIN --}}
                            <div class="bg-slate-50 rounded-2xl border border-dashed border-slate-200 p-4 flex flex-col justify-between text-xs text-slate-500">
                                <div class="mb-2">
                                    <h3 class="text-sm font-semibold text-slate-700">
                                        Slot Fitur Berikutnya
                                    </h3>
                                    <p class="mt-1">
                                        Bisa diisi link ke Sensijet, Jet Kilas, atau modul lain Bimbel JET.
                                    </p>
                                </div>
                                <p class="mt-auto text-[11px] text-slate-400">
                                    Nanti kita isi bareng: statistik kelas, absensi, atau kilas performa siswa.
                                </p>
                            </div>

                        </div>

                    </div>
                @endif

            </div>
        </div>
    </x-app-layout>
@endif
