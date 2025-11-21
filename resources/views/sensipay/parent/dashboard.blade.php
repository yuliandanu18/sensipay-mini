@extends('sensipay.layout')

@section('title', 'Dashboard Orang Tua')

@section('content')
<div class="container mx-auto py-6 space-y-6">
    <h1 class="text-xl font-semibold">
        Halo, {{ $user->name }} 👋
    </h1>

    {{-- Ringkasan Tagihan Bulan Ini --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="p-4 rounded-xl border border-slate-200 bg-sky-50">
            <div class="text-xs text-slate-500">Total Tagihan Bulan Ini</div>
            <div class="text-lg font-semibold">
                Rp {{ number_format($monthlyTotal ?? 0, 0, ',', '.') }}
            </div>
        </div>
        <div class="p-4 rounded-xl border border-slate-200 bg-emerald-50">
            <div class="text-xs text-slate-500">Total Pembayaran Masuk Bulan Ini</div>
            <div class="text-lg font-semibold">
                Rp {{ number_format($monthlyPaid ?? 0, 0, ',', '.') }}
            </div>
        </div>
        <div class="p-4 rounded-xl border border-slate-200 bg-amber-50">
            <div class="text-xs text-slate-500">Sisa Tagihan Bulan Ini</div>
            <div class="text-lg font-semibold">
                Rp {{ number_format($monthlyRemaining ?? 0, 0, ',', '.') }}
            </div>
        </div>
    </div>

    {{-- Tabel Semua Tagihan --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <h2 class="font-semibold text-sm">Daftar Tagihan</h2>
            <span class="text-xs text-slate-500">
                Menampilkan {{ $invoices->count() }} invoice
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-100">
                    <tr>
                        <th class="px-3 py-2 text-left">Invoice</th>
                        <th class="px-3 py-2 text-left">Siswa / Program</th>
                        <th class="px-3 py-2 text-right">Total</th>
                        <th class="px-3 py-2 text-right">Terbayar</th>
                        <th class="px-3 py-2 text-right">Sisa</th>
                        <th class="px-3 py-2 text-center">Status</th>
                        <th class="px-3 py-2 text-center">Jatuh Tempo</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($invoices as $inv)
                    @php
                        $total = $inv->total_amount ?? 0;
                        $paid  = $inv->paid_amount ?? 0;
                        $remaining = max(0, $total - $paid);
                    @endphp
                    <tr class="border-t border-slate-100">
                        <td class="px-3 py-2 align-top">
                            <div class="font-mono text-xs">
                                {{ $inv->invoice_code }}
                            </div>
                        </td>
                        <td class="px-3 py-2 align-top">
                            <div class="font-medium">
                                {{ $inv->student->name ?? '-' }}
                            </div>
                            <div class="text-xs text-slate-500">
                                {{ $inv->program->name ?? '-' }}
                            </div>
                        </td>
                        <td class="px-3 py-2 text-right align-top">
                            Rp {{ number_format($total, 0, ',', '.') }}
                        </td>
                        <td class="px-3 py-2 text-right align-top">
                            Rp {{ number_format($paid, 0, ',', '.') }}
                        </td>
                        <td class="px-3 py-2 text-right align-top">
                            Rp {{ number_format($remaining, 0, ',', '.') }}
                        </td>
                        <td class="px-3 py-2 text-center align-top">
                            <span class="inline-flex px-2 py-1 rounded-full text-xs
                                @if($inv->status === 'paid') bg-emerald-100 text-emerald-700
                                @elseif($inv->status === 'partial') bg-amber-100 text-amber-700
                                @else bg-rose-100 text-rose-700 @endif">
                                {{ strtoupper($inv->status ?? 'UNPAID') }}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-center text-xs align-top">
                            {{ optional($inv->due_date)->format('d/m/Y') ?? '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-3 py-6 text-center text-slate-500">
                            Belum ada tagihan yang tercatat.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
