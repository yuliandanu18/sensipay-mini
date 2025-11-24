{{-- resources/views/sensipay/parent/dashboard.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg text-gray-800 leading-tight">
            Dashboard Orang Tua
        </h2>
        <p class="text-xs text-gray-500 mt-1">
            Halaman ini sudah disesuaikan untuk tampilan HP. Scroll ke bawah untuk melihat semua tagihan.
        </p>
    </x-slot>

    @php
        // Nomor WA admin dari env
        $adminWa = env('JET_ADMIN_WA'); // contoh: 6281234567890
        $adminWaDigits = $adminWa ? preg_replace('/\D+/', '', $adminWa) : null;
    @endphp

    <div class="py-4">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            {{-- Kartu sapaan singkat --}}
            <div class="bg-white rounded-xl shadow-sm p-4">
                <p class="text-sm text-gray-700">
                    Halo, ini halaman dashboard parent JET. Kalau kamu melihat teks ini, berarti view sudah kepanggil.
                </p>
            </div>

            {{-- NOTIFIKASI JATUH TEMPO (LEWAT TEMPO) --}}
            @if(($overdue ?? collect())->count() > 0)
                <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-xs text-red-800">
                    <div class="font-semibold text-[13px] mb-1">
                        ⚠️ Ada tagihan yang sudah lewat jatuh tempo
                    </div>
                    <ul class="list-disc pl-4 space-y-1">
                        @foreach($overdue->take(3) as $inv)
                            <li>
                                Invoice {{ $inv->invoice_code ?? '-' }} –
                                {{ $inv->student->name ?? 'Siswa' }} –
                                Jatuh tempo:
                                {{ optional($inv->due_date)->format('d-m-Y') }}
                            </li>
                        @endforeach
                    </ul>
                    @if($overdue->count() > 3)
                        <p class="mt-1 text-[11px] text-red-700">
                            Dan {{ $overdue->count() - 3 }} tagihan lainnya...
                        </p>
                    @endif
                </div>
            @endif

            {{-- NOTIFIKASI AKAN JATUH TEMPO --}}
            @if(($dueSoon ?? collect())->count() > 0)
                <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs text-amber-800">
                    <div class="font-semibold text-[13px] mb-1">
                        🔔 Tagihan akan jatuh tempo dalam 7 hari
                    </div>
                    <ul class="list-disc pl-4 space-y-1">
                        @foreach($dueSoon->take(3) as $inv)
                            <li>
                                Invoice {{ $inv->invoice_code ?? '-' }} –
                                {{ $inv->student->name ?? 'Siswa' }} –
                                Jatuh tempo:
                                {{ optional($inv->due_date)->format('d-m-Y') }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- LIST INVOICE --}}
            <div class="bg-white rounded-xl shadow-sm p-4">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">
                    Daftar Invoice
                </h3>

                @forelse($invoices as $inv)
                    @php
                        $total     = (int) ($inv->total_amount ?? 0);
                        $paid      = (int) ($inv->paid_amount ?? 0);
                        $remaining = max(0, $total - $paid);
                    @endphp

                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 py-3 border-b last:border-b-0 text-xs">
                        <div>
                            {{-- Judul invoice bisa diklik --}}
                            <a href="{{ $remaining > 0 ? route('sensipay.parent.invoices.show', $inv) : '#' }}"
                               class="{{ $remaining > 0 ? 'text-emerald-700 hover:underline' : 'text-gray-800' }} font-semibold">
                                {{ $inv->invoice_code ?? '-' }}
                            </a>
                            <div class="text-gray-500">
                                {{ $inv->student->name ?? 'Siswa' }}
                            </div>
                            <div class="text-[11px] text-gray-400">
                                Jatuh tempo:
                                {{ $inv->due_date ? \Carbon\Carbon::parse($inv->due_date)->format('d-m-Y') : '-' }}
                            </div>
                        </div>

                        <div class="text-right">
                            <div class="text-[11px] text-gray-500">
                                Total:
                                <span class="font-semibold text-gray-800">
                                    Rp {{ number_format($total, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="text-[11px] text-gray-500">
                                Terbayar:
                                <span class="font-semibold text-emerald-700">
                                    Rp {{ number_format($paid, 0, ',', '.') }}
                                </span>
                            </div>

                            @if($remaining > 0)
                                <div class="mt-1 text-[11px] text-red-600">
                                    Sisa:
                                    <span class="font-semibold">
                                        Rp {{ number_format($remaining, 0, ',', '.') }}
                                    </span>
                                </div>

                                {{-- TOMBOL UTAMA PARENT --}}
                                <a href="{{ route('sensipay.parent.invoices.show', $inv) }}"
                                   class="mt-2 inline-flex w-full sm:w-auto items-center justify-center rounded-full bg-emerald-600 px-3 py-1.5 text-[11px] font-semibold text-white hover:bg-emerald-700">
                                    Lihat Tagihan & Konfirmasi
                                </a>
                            @else
                                <span class="mt-2 inline-block text-[11px] text-emerald-700 font-semibold">
                                    Lunas
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-gray-500 py-3">
                        Belum ada invoice yang terhubung dengan akun ini.
                    </p>
                @endforelse
            </div>

        </div>
    </div>

    {{-- FLOATING BUTTON WA ADMIN (pakai env JET_ADMIN_WA) --}}
    @if($adminWaDigits)
        @php
            $defaultText = 'Halo Admin Bimbel JET, saya ingin menanyakan tagihan bimbel anak.';
            $waUrl = 'https://wa.me/' . $adminWaDigits . '?text=' . urlencode($defaultText);
        @endphp

        <a href="{{ $waUrl }}"
           target="_blank"
           rel="noopener"
           class="fixed bottom-5 right-4 z-40 inline-flex items-center rounded-full bg-emerald-600 px-4 py-2 shadow-lg text-xs font-semibold text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1">
            <span class="mr-2 text-lg">🟢</span>
            WA Admin JET
        </a>
    @endif
</x-app-layout>
