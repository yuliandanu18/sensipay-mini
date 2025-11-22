<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Beranda - Bimbel JET & Sensipay</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 flex flex-col">

    <header class="w-full bg-white border-b border-slate-200">
        <div class="max-w-5xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="h-8 w-8 rounded-full bg-slate-900 flex items-center justify-center text-xs font-bold text-white">
                    J
                </div>
                <div>
                    <div class="font-semibold text-sm">Bimbel JET</div>
                    <div class="text-[11px] text-slate-500">Sensipay Mini &amp; Sensijet</div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                @auth
                    <span class="hidden sm:inline text-xs text-slate-600 mr-2">
                        Halo, <span class="font-semibold">{{ auth()->user()->name }}</span>
                    </span>
                    <a href="{{ route('dashboard') }}"
                       class="px-3 py-1 text-xs rounded bg-slate-900 text-white hover:bg-slate-800">
                        Buka Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="px-3 py-1 text-xs rounded bg-slate-900 text-white hover:bg-slate-800">
                        Login
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <main class="flex-1 w-full">
        <div class="max-w-5xl mx-auto px-4 py-6 space-y-6">

            <section class="bg-white rounded-xl border border-slate-200 p-4 sm:p-6 shadow-sm">
                <h1 class="text-xl sm:text-2xl font-bold mb-2">
                    Selamat datang di sistem Bimbel JET
                </h1>
                <p class="text-sm text-slate-600 mb-4 leading-relaxed">
                    Satu pintu untuk mengelola bimbingan belajar, keuangan (Sensipay),
                    dan jadwal belajar (Sensijet). Desainnya sederhana, yang penting
                    orang tua bisa paham dan tim internal bisa kerja tenang.
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                    <div class="p-3 rounded-lg bg-slate-50 border border-slate-200">
                        <div class="font-semibold mb-1">Untuk Orang Tua Murid</div>
                        <p class="text-xs text-slate-600 mb-2">
                            Cek tagihan, angsuran, dan riwayat pembayaran anak Anda secara online.
                        </p>
                        @auth
                            @if (auth()->user()->role === 'parent')
                                <a href="{{ route('sensipay.parent.dashboard') }}"
                                   class="inline-block px-3 py-1 text-xs rounded bg-slate-900 text-white hover:bg-slate-800">
                                    Buka Dashboard Orang Tua
                                </a>
                            @else
                                <span class="inline-block px-3 py-1 text-xs rounded border border-slate-300 text-slate-600">
                                    Login sebagai akun parent untuk melihat menu ini
                                </span>
                            @endif
                        @else
                            <a href="{{ route('login') }}"
                               class="inline-block px-3 py-1 text-xs rounded bg-slate-900 text-white hover:bg-slate-800">
                                Login sebagai Orang Tua
                            </a>
                        @endauth
                    </div>

                    <div class="p-3 rounded-lg bg-slate-50 border border-slate-200">
                        <div class="font-semibold mb-1">Untuk Tim Bimbel JET</div>
                        <p class="text-xs text-slate-600 mb-2">
                            Kelola invoice, cicilan, dan pengingat pembayaran tanpa spreadsheet berantakan.
                        </p>
                        @auth
                            @if (in_array(auth()->user()->role, ['owner','operational_director','academic_director','finance']))
                                <a href="{{ route('sensipay.invoices.index') }}"
                                   class="inline-block px-3 py-1 text-xs rounded bg-slate-900 text-white hover:bg-slate-800">
                                    Buka Dashboard Invoice
                                </a>
                            @else
                                <span class="inline-block px-3 py-1 text-xs rounded border border-slate-300 text-slate-600">
                                    Login sebagai admin / direksi untuk akses penuh
                                </span>
                            @endif
                        @else
                            <a href="{{ route('login') }}"
                               class="inline-block px-3 py-1 text-xs rounded bg-slate-900 text-white hover:bg-slate-800">
                                Login sebagai Admin / Tim
                            </a>
                        @endauth
                    </div>
                </div>
            </section>

            @auth
                <section class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
                    <a href="{{ route('dashboard') }}"
                       class="block bg-white border border-slate-200 rounded-lg p-3 shadow-sm hover:bg-slate-50">
                        <div class="font-semibold mb-1">Dashboard</div>
                        <p class="text-xs text-slate-600">
                            Masuk ke dashboard sesuai peran Anda (parent / admin).
                        </p>
                    </a>
                    <a href="{{ route('profile.edit') }}"
                       class="block bg-white border border-slate-200 rounded-lg p-3 shadow-sm hover:bg-slate-50">
                        <div class="font-semibold mb-1">Profil Akun</div>
                        <p class="text-xs text-slate-600">
                            Ubah nama, email, atau password akun.
                        </p>
                    </a>
                    <form method="POST" action="{{ route('logout') }}"
                          class="block bg-white border border-slate-200 rounded-lg p-3 shadow-sm hover:bg-slate-50">
                        @csrf
                        <button type="submit" class="w-full text-left">
                            <div class="font-semibold mb-1 text-red-600">Logout</div>
                            <p class="text-xs text-slate-600">
                                Keluar dari sistem dengan aman.
                            </p>
                        </button>
                    </form>
                </section>
            @endauth
        </div>
    </main>

    <footer class="w-full border-t border-slate-200 py-3 text-center text-[11px] text-slate-500">
        Bimbel JET &middot; Sensipay Mini &middot; {{ date('Y') }}
    </footer>
</body>
</html>
