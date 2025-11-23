@extends('sensipay.layout')

@section('title', 'Dashboard Orang Tua')
@section('page_title', 'Dashboard Orang Tua')

@section('content')
    <div class="max-w-5xl mx-auto">
        <h1 class="text-2xl font-bold mb-4">Dashboard Orang Tua Murid</h1>

        @php
            /** @var \App\Models\User $user */
            $user = auth()->user();
        @endphp

        <p class="mb-6 text-sm">
            Selamat datang,
            <span class="font-semibold">{{ $user->name ?? '-' }}</span>.<br>
            Berikut adalah ringkasan tagihan (invoice) yang terhubung dengan akun Anda.
        </p>

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

        @if ($invoices->isEmpty())
            <div class="p-4 border rounded bg-white text-sm">
                Belum ada invoice yang terhubung dengan akun Anda.
            </div>
        @else
            <div class="space-y-4">
                @foreach ($invoices as $invoice)
                    @php
                        $total     = $invoice->total_amount ?? 0;
                        $paid      = $invoice->paid_amount ?? 0;
                        $remaining = max(0, $total - $paid);
                        $pendingExists = $invoice->payments->where('status', 'pending')->isNotEmpty();
                    @endphp

                    <div class="p-4 border rounded bg-white shadow-sm">
                        {{-- Banner aturan nominal --}}
                        @if ($remaining <= 1_000_000 && $remaining > 0)
                            <div class="mb-3 px-3 py-2 rounded bg-red-50 text-red-700 text-xs">
                                Untuk sisa di bawah atau sama dengan Rp 1.000.000, wajib pelunasan penuh.
                            </div>
                        @elseif ($remaining > 1_000_000)
                            <div class="mb-3 px-3 py-2 rounded bg-amber-50 text-amber-700 text-xs">
                                Sisa &gt; Rp 1.000.000: minimal pembayaran Rp 1.000.000 dan kelipatan Rp 50.000.
                            </div>
                        @endif

                        {{-- Info kalau ada payment pending --}}
                        @if ($pendingExists)
                            <div class="mb-3 px-3 py-2 rounded bg-sky-50 text-sky-700 text-xs">
                                Ada konfirmasi pembayaran yang masih menunggu verifikasi admin. Form pembayaran sementara dinonaktifkan.
                            </div>
                        @endif

                        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-2 gap-2">
                            <div>
                                <div class="text-xs text-gray-500">Kode Invoice</div>
                                <div class="font-semibold text-lg">{{ $invoice->invoice_code }}</div>
                            </div>
                            <div class="text-right text-sm">
                                <div class="text-xs text-gray-500">Status</div>
                                <div class="font-semibold">
                                    @if ($invoice->status === 'paid')
                                        <span class="text-green-600">LUNAS</span>
                                    @elseif ($invoice->status === 'partial')
                                        <span class="text-yellow-600">ANGSUR</span>
                                    @else
                                        <span class="text-red-600">BELUM BAYAR</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm mb-3">
                            <div>
                                <div class="text-gray-500 text-xs">Nama Siswa</div>
                                <div class="font-medium">
                                    {{ optional($invoice->student)->name ?? '-' }}
                                </div>
                            </div>
                            <div>
                                <div class="text-gray-500 text-xs">Total Tagihan</div>
                                <div class="font-medium">
                                    Rp {{ number_format($total, 0, ',', '.') }}
                                </div>
                            </div>
                            <div>
                                <div class="text-gray-500 text-xs">Sudah Dibayar</div>
                                <div class="font-medium">
                                    Rp {{ number_format($paid, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>

                        <div class="mb-2 text-sm">
                            <span class="text-gray-500">Sisa Tagihan:</span>
                            <span class="font-semibold">
                                Rp {{ number_format($remaining, 0, ',', '.') }}
                            </span>
                        </div>

                        {{-- Riwayat pembayaran --}}
                        @if ($invoice->payments->isNotEmpty())
                            <div class="mt-3 mb-4">
                                <div class="text-sm font-semibold mb-1">Riwayat Pembayaran:</div>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full text-xs border">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th class="px-2 py-1 border text-left">Tanggal</th>
                                                <th class="px-2 py-1 border text-right">Nominal</th>
                                                <th class="px-2 py-1 border text-left">Metode</th>
                                                <th class="px-2 py-1 border text-left">Status</th>
                                                <th class="px-2 py-1 border text-left">Bukti</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($invoice->payments->sortByDesc('created_at') as $pay)
                                                <tr>
                                                    <td class="px-2 py-1 border">
                                                        {{ optional($pay->paid_at)->format('d-m-Y H:i') ?? '-' }}
                                                    </td>
                                                    <td class="px-2 py-1 border text-right">
                                                        Rp {{ number_format($pay->amount ?? 0, 0, ',', '.') }}
                                                    </td>
                                                    <td class="px-2 py-1 border">
                                                        {{ $pay->method ?? '-' }}
                                                    </td>
                                                    <td class="px-2 py-1 border">
                                                        @if ($pay->status === 'approved')
                                                            <span class="text-green-600 font-semibold">APPROVED</span>
                                                        @elseif ($pay->status === 'rejected')
                                                            <span class="text-red-600 font-semibold">REJECTED</span>
                                                        @else
                                                            <span class="text-amber-600 font-semibold">PENDING</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-2 py-1 border">
                                                        @if ($pay->proof_path)
                                                            <a href="{{ asset('storage/' . $pay->proof_path) }}"
                                                               target="_blank"
                                                               class="text-blue-600 hover:underline">
                                                                Lihat
                                                            </a>
                                                        @else
                                                            <span class="text-[11px] text-slate-400">Tidak ada</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        {{-- FORM BAYAR TAGIHAN BULAN INI + UPLOAD BUKTI --}}
                        @if ($remaining > 0 && ! $pendingExists)
                            <form method="POST"
                                  action="{{ route('sensipay.parent.invoices.pay', $invoice) }}"
                                  enctype="multipart/form-data"
                                  class="mt-4 space-y-2 border-t pt-3">
                                @csrf

                                <div class="text-sm font-semibold">
                                    Bayar Tagihan Bulan Ini
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">
                                            Nominal Bayar (Rp)
                                        </label>
                                        <input type="number"
                                               name="amount"
                                               class="w-full border rounded px-2 py-1 text-right"
                                               placeholder="contoh: 1000000">
                                        <p class="text-[11px] text-gray-400 mt-1">
                                            Sisa: Rp {{ number_format($remaining, 0, ',', '.') }}.<br>
                                            Jika sisa &gt; Rp 1.000.000:
                                            minimal Rp 1.000.000 dan kelipatan Rp 50.000.<br>
                                            Jika sisa ≤ Rp 1.000.000: wajib pelunasan penuh.
                                        </p>
                                    </div>

                                    <div class="md:col-span-2 space-y-2">
                                        <div>
                                            <label class="block text-xs text-gray-500 mb-1">
                                                Catatan / No. Referensi Transfer (opsional)
                                            </label>
                                            <input type="text"
                                                   name="note"
                                                   class="w-full border rounded px-2 py-1"
                                                   placeholder="contoh: BCA 24/11, Ref 123ABC">
                                        </div>

                                        <div>
                                            <label class="block text-xs text-gray-500 mb-1">
                                                Upload Bukti Transfer (opsional, jpg/png, max 2MB)
                                            </label>
                                            <input type="file"
                                                   name="proof"
                                                   accept="image/*"
                                                   class="w-full border rounded px-2 py-1 bg-white">
                                        </div>
                                    </div>
                                </div>

                                <div class="flex justify-end">
                                    <button type="submit"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded bg-emerald-600 text-white hover:bg-emerald-700">
                                        Kirim Konfirmasi Pembayaran
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
