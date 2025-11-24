{{-- resources/views/sensipay/parent/invoice-show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-lg text-gray-800 leading-tight">
                    Detail Tagihan & Konfirmasi Pembayaran
                </h2>
                <p class="text-xs text-gray-500 mt-1">
                    Halaman ini dioptimalkan untuk HP. Scroll ke bawah untuk melihat rincian & form pembayaran.
                </p>
            </div>
            <a href="{{ route('sensipay.parent.dashboard') }}"
               class="hidden sm:inline-flex items-center rounded-full border border-gray-300 px-3 py-1 text-[11px] text-gray-600 hover:bg-gray-100">
                ← Kembali ke Dashboard
            </a>
        </div>
    </x-slot>

    @php
        $user      = auth()->user();
        $total     = (int) ($invoice->total_amount ?? 0);
        $paid      = (int) ($invoice->paid_amount ?? 0);
        $remaining = max(0, $total - $paid);
        $dueDate   = $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('d-m-Y') : '-';
        $status    = $invoice->status ?? 'unpaid';

        $adminWa       = env('JET_ADMIN_WA');
        $adminWaDigits = $adminWa ? preg_replace('/\D+/', '', $adminWa) : null;
    @endphp

    <div class="py-4">
        <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            {{-- FLASH MESSAGE --}}
            @if(session('status'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            @if(session('error'))
                <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-xs text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            {{-- KARTU RINGKASAN INVOICE --}}
            <div class="bg-white rounded-2xl shadow-sm p-4 sm:p-5 space-y-3">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-[11px] uppercase tracking-wide text-gray-400">
                            Invoice
                        </p>
                        <p class="text-sm font-semibold text-gray-900">
                            {{ $invoice->invoice_code ?? 'Tanpa Kode' }}
                        </p>
                        <p class="mt-1 text-xs text-gray-500">
                            Atas nama:
                            <span class="font-semibold text-gray-800">
                                {{ $invoice->student->name ?? 'Siswa' }}
                            </span>
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-[11px] text-gray-400">
                            Jatuh Tempo
                        </p>
                        <p class="text-xs font-semibold text-gray-800">
                            {{ $dueDate }}
                        </p>
                        <span class="mt-2 inline-flex items-center rounded-full px-2.5 py-0.5 text-[10px] font-semibold
                            @if($status === 'paid')
                                bg-emerald-50 text-emerald-700
                            @elseif($status === 'partial')
                                bg-amber-50 text-amber-700
                            @else
                                bg-red-50 text-red-700
                            @endif
                        ">
                            Status: {{ strtoupper($status) }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3 text-[11px] mt-2">
                    <div class="rounded-xl bg-slate-50 px-3 py-2">
                        <p class="text-slate-500">Total Tagihan</p>
                        <p class="mt-1 font-semibold text-slate-900">
                            Rp {{ number_format($total, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="rounded-xl bg-emerald-50 px-3 py-2">
                        <p class="text-emerald-600">Sudah Dibayar</p>
                        <p class="mt-1 font-semibold text-emerald-800">
                            Rp {{ number_format($paid, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="rounded-xl bg-red-50 px-3 py-2">
                        <p class="text-red-600">Sisa</p>
                        <p class="mt-1 font-semibold text-red-700">
                            Rp {{ number_format($remaining, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                @if($remaining <= 0)
                    <p class="mt-2 text-[11px] text-emerald-700">
                        Terima kasih, tagihan ini sudah <span class="font-semibold">lunas</span>.
                    </p>
                @else
                    <p class="mt-2 text-[11px] text-gray-500">
                        Silakan isi nominal yang akan dibayarkan dan unggah bukti transfer. Pembayaran akan dicek oleh admin JET terlebih dahulu.
                    </p>
                @endif
            </div>

            {{-- ATURAN PEMBAYARAN --}}
            @if($remaining > 0)
                <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-[11px] text-amber-800">
                    <p class="font-semibold mb-1">
                        📌 Aturan Pembayaran
                    </p>
                    <ul class="list-disc pl-4 space-y-1">
                        <li>
                            Jika sisa tagihan <span class="font-semibold">&gt; Rp 1.000.000</span>:
                            minimal pembayaran <span class="font-semibold">Rp 1.000.000</span>
                            dan <span class="font-semibold">harus kelipatan Rp 50.000</span>.
                        </li>
                        <li>
                            Jika sisa tagihan <span class="font-semibold">&le; Rp 1.000.000</span>:
                            wajib <span class="font-semibold">pelunasan penuh</span>.
                        </li>
                        <li>
                            Admin akan menyetujui pembayaran terlebih dahulu sebelum status di sistem berubah.
                        </li>
                    </ul>
                </div>
            @endif

            {{-- FORM KONFIRMASI PEMBAYARAN --}}
            @if($remaining > 0)
                <div class="bg-white rounded-2xl shadow-sm p-4 sm:p-5">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">
                        Form Konfirmasi Pembayaran
                    </h3>

                    <form action="{{ route('sensipay.parent.invoices.pay', $invoice) }}"
                          method="POST"
                          enctype="multipart/form-data"
                          class="space-y-3">
                        @csrf

                        {{-- NOMINAL BAYAR --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">
                                Nominal yang Dibayarkan
                            </label>
                            <input
                                type="text"
                                name="amount"
                                id="amount-input"
                                inputmode="numeric"
                                autocomplete="off"
                                placeholder="Contoh: 1.000.000"
                                class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                                value="{{ old('amount') }}"
                                required
                            >
                            @error('amount')
                                <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                            @else
                                <p class="mt-1 text-[11px] text-gray-400">
                                    Angka saja, tanpa "Rp". Sistem akan otomatis membaca sebagai rupiah.
                                </p>
                            @enderror
                        </div>

                        {{-- CATATAN OPTIONAL --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">
                                Catatan (opsional)
                            </label>
                            <textarea
                                name="note"
                                rows="2"
                                class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                                placeholder="Misal: Transfer via BCA a.n. Ayah, jam 19.30">{{ old('note') }}</textarea>
                            @error('note')
                                <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- BUKTI TRANSFER --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">
                                Foto Bukti Transfer (opsional, tapi sangat disarankan)
                            </label>
                            <input
                                type="file"
                                name="proof"
                                accept="image/*"
                                class="block w-full text-xs text-gray-500
                                       file:mr-3 file:rounded-full file:border-0
                                       file:bg-emerald-50 file:px-3 file:py-1.5
                                       file:text-xs file:font-semibold file:text-emerald-700
                                       hover:file:bg-emerald-100"
                            >
                            @error('proof')
                                <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                            @else
                                <p class="mt-1 text-[11px] text-gray-400">
                                    Format gambar (JPG/PNG), maksimal sekitar 2 MB.
                                </p>
                            @enderror
                        </div>

                        <div class="pt-2">
                            <button type="submit"
                                    class="inline-flex w-full items-center justify-center rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1">
                                Kirim Konfirmasi Pembayaran
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            {{-- CARD INFO BANTUAN --}}
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-[11px] text-slate-700">
                Jika ada salah nominal, upload ganda, atau membutuhkan bantuan,
                silakan hubungi admin Bimbel JET melalui WhatsApp.
            </div>
        </div>
    </div>

    {{-- SATU-SAJA: FLOATING BUTTON WA ADMIN UNTUK INVOICE INI --}}
    @if($adminWaDigits)
        @php
            $defaultText = 'Halo Admin Bimbel JET, saya ingin konfirmasi pembayaran untuk invoice '
                . ($invoice->invoice_code ?? '-')
                . ' atas nama '
                . ($invoice->student->name ?? 'siswa') . '.';
            $waUrl = 'https://wa.me/' . $adminWaDigits . '?text=' . urlencode($defaultText);
        @endphp

        <a href="{{ $waUrl }}"
           target="_blank"
           rel="noopener"
           class="fixed bottom-5 right-4 z-40 inline-flex items-center rounded-full bg-emerald-600 px-4 py-2 shadow-lg text-xs font-semibold text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1">
            <span class="mr-2 text-lg">🟢</span>
            WA Admin (Invoice Ini)
        </a>
    @endif

    {{-- JS: FORMAT INPUT NOMINAL JADI 1.000.000 --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const input = document.getElementById('amount-input');
            if (!input) return;

            input.addEventListener('input', function (e) {
                let value = e.target.value || '';
                value = value.replace(/\D/g, ''); // buang non-digit
                if (value === '') {
                    e.target.value = '';
                    return;
                }
                e.target.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            });
        });
    </script>
</x-app-layout>
