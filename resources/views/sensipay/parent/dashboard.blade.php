@extends('sensipay.layout')

@section('title', 'Dashboard Orang Tua')
@section('page_title', 'Dashboard Orang Tua')

@section('content')
<div class="max-w-5xl mx-auto py-6 space-y-4">

    @if (session('status'))
        <div class="mb-4 p-3 rounded bg-green-100 text-green-800 text-sm">
            {{ session('status') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-3 rounded bg-red-100 text-red-800 text-sm">
            {{ session('error') }}
        </div>
    @endif

    <p class="text-sm text-slate-700 mb-2">
        Halo, <span class="font-semibold">{{ $user->name }}</span>.
        Berikut ringkasan tagihan dan pembayaran siswa yang terhubung dengan akun Anda.
    </p>

    <p class="text-xs text-slate-500 mb-4">
        Aturan pembayaran bulan ini:
        minimal Rp 1.000.000 dan kelipatan Rp 50.000.
        Jika sisa tagihan kurang dari atau sama dengan Rp 1.000.000,
        maka harus dibayar lunas (pelunasan).
    </p>

    @if ($invoices->isEmpty())
        <div class="p-4 border rounded bg-white text-sm">
            Belum ada invoice yang terhubung dengan akun Anda.
        </div>
    @else
        <div class="space-y-4">
            @foreach ($invoices as $invoice)
                @php
                    $remaining = max(0, ($invoice->total_amount ?? 0) - ($invoice->paid_amount ?? 0));
                    $recommended = $remaining > 1000000 ? 1000000 : $remaining;
                @endphp

                <div class="p-4 border rounded bg-white shadow-sm space-y-3">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="text-xs text-gray-500">Kode Invoice</div>
                            <div class="font-semibold text-lg">{{ $invoice->invoice_code }}</div>
                            <div class="text-xs text-gray-500 mt-1">
                                Siswa:
                                <span class="font-medium">
                                    {{ optional($invoice->student)->name ?? '-' }}
                                </span>
                            </div>
                        </div>
                        <div class="text-right text-sm">
                            <div class="text-gray-500">Status</div>
                            <div class="font-semibold">
                                @if ($invoice->status === 'paid')
                                    <span class="text-green-600">LUNAS</span>
                                @elseif ($invoice->status === 'partial')
                                    <span class="text-yellow-600">ANGSUR</span>
                                @else
                                    <span class="text-red-600">BELUM BAYAR</span>
                                @endif
                            </div>
                            <div class="mt-1 text-xs text-gray-500">
                                Total: Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}<br>
                                Terbayar: Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>

                    <div class="text-sm">
                        <span class="text-gray-500">Sisa Tagihan:</span>
                        <span class="font-semibold">
                            Rp {{ number_format($remaining, 0, ',', '.') }}
                        </span>
                    </div>

                    {{-- Detail Angsuran (jika pakai tabel installments) --}}
                    @if ($invoice->installments && $invoice->installments->count())
                        <div class="mt-2">
                            <div class="text-sm font-semibold mb-1">Detail Angsuran (Rencana):</div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-xs border">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="px-2 py-1 border">Termin</th>
                                            <th class="px-2 py-1 border">Jatuh Tempo</th>
                                            <th class="px-2 py-1 border">Jumlah</th>
                                            <th class="px-2 py-1 border">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($invoice->installments as $i => $inst)
                                            <tr>
                                                <td class="px-2 py-1 border text-center">
                                                    {{ $i + 1 }}
                                                </td>
                                                <td class="px-2 py-1 border text-center">
                                                    {{ optional($inst->due_date)->format('d-m-Y') ?? '-' }}
                                                </td>
                                                <td class="px-2 py-1 border text-right">
                                                    Rp {{ number_format($inst->amount, 0, ',', '.') }}
                                                </td>
                                                <td class="px-2 py-1 border text-center">
                                                    @if ($inst->is_paid)
                                                        <span class="text-green-600">LUNAS</span>
                                                    @else
                                                        <span class="text-red-600">BELUM</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    {{-- Riwayat Pembayaran --}}
                    @if ($invoice->payments && $invoice->payments->count())
                        <div class="mt-3">
                            <div class="text-sm font-semibold mb-1">Riwayat Pembayaran:</div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-xs border">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="px-2 py-1 border">Tanggal</th>
                                            <th class="px-2 py-1 border">Jumlah</th>
                                            <th class="px-2 py-1 border">Metode</th>
                                            <th class="px-2 py-1 border">Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($invoice->payments->sortBy('paid_at') as $pay)
                                            <tr>
                                                <td class="px-2 py-1 border text-center">
                                                    {{ optional($pay->paid_at)->format('d-m-Y') ?? '-' }}
                                                </td>
                                                <td class="px-2 py-1 border text-right">
                                                    Rp {{ number_format($pay->amount, 0, ',', '.') }}
                                                </td>
                                                <td class="px-2 py-1 border text-center">
                                                    {{ $pay->method ?? '-' }}
                                                </td>
                                                <td class="px-2 py-1 border">
                                                    {{ $pay->note ?? '-' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    {{-- Form Pembayaran Bulan Ini --}}
                    @if ($remaining > 0)
                        <div class="mt-4 border-t pt-3">
                            <div class="text-sm font-semibold mb-1">Bayar Tagihan Bulan Ini</div>
                            <div class="text-xs text-gray-500 mb-2">
                                Rekomendasi pembayaran: Rp {{ number_format($recommended, 0, ',', '.') }}
                                @if ($remaining <= 1000000)
                                    (pelunasan)
                                @endif
                            </div>

                            <form method="POST"
                                  action="{{ route('sensipay.parent.invoices.pay', $invoice) }}"
                                  enctype="multipart/form-data"
                                  class="space-y-2 max-w-md">
                                @csrf

                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">
                                        Nominal Pembayaran (Rp)
                                    </label>
                                    <input type="number"
                                           name="amount"
                                           inputmode="numeric"
                                           class="w-full border rounded px-2 py-1 text-sm"
                                           value="{{ old('amount', (int) $recommended) }}"
                                           required>
                                </div>

                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">
                                        No. Referensi Transfer (opsional)
                                    </label>
                                    <input type="text"
                                           name="reference"
                                           class="w-full border rounded px-2 py-1 text-sm"
                                           value="{{ old('reference') }}"
                                           placeholder="Misal: BRIS 1234xxxxxxxx">
                                </div>

                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">
                                        Upload Bukti Transfer (opsional)
                                    </label>
                                    <input type="file"
                                           name="evidence"
                                           accept="image/*,application/pdf"
                                           class="w-full text-xs">
                                    <p class="text-[10px] text-gray-400 mt-1">
                                        Format: JPG, PNG, atau PDF. Maksimal 4 MB.
                                    </p>
                                </div>

                                <button type="submit"
                                        class="inline-flex items-center px-3 py-1.5 rounded bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold">
                                    Kirim Konfirmasi Pembayaran
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
