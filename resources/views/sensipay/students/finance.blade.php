@extends('sensipay.layout')

@section('title', 'Keuangan Siswa - ' . $student->name)

@section('content')
<div class="mb-4">
    <h1 class="text-xl font-semibold">Keuangan Siswa</h1>
    <p class="text-xs text-slate-500">
        {{ $student->name }} @if($student->school ?? null) - {{ $student->school }} @endif<br>
        ID: {{ $student->id }}
    </p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 text-xs">
    <div class="bg-white rounded-xl shadow p-4">
        <div class="text-slate-500 mb-1">Total Tagihan</div>
        <div class="text-lg font-bold">
            Rp {{ number_format($total_invoice, 0, ',', '.') }}
        </div>
    </div>
    <div class="bg-white rounded-xl shadow p-4">
        <div class="text-slate-500 mb-1">Total Pembayaran</div>
        <div class="text-lg font-bold text-emerald-600">
            Rp {{ number_format($total_paid, 0, ',', '.') }}
        </div>
    </div>
    <div class="bg-white rounded-xl shadow p-4">
        <div class="text-slate-500 mb-1">Sisa Tagihan</div>
        <div class="text-lg font-bold text-red-600">
            Rp {{ number_format($remaining, 0, ',', '.') }}
        </div>
    </div>
</div>

{{-- DAFTAR INVOICE --}}
<div class="bg-white rounded-xl shadow mb-6 overflow-hidden text-xs">
    <div class="border-b px-4 py-2 bg-slate-50 font-semibold">
        Daftar Invoice
    </div>
    <table class="w-full">
        <thead class="bg-slate-50 border-b">
            <tr>
                <th class="px-3 py-2 text-left">Invoice</th>
                <th class="px-3 py-2 text-left">Program</th>
                <th class="px-3 py-2 text-center">Jatuh Tempo</th>
                <th class="px-3 py-2 text-right">Total</th>
                <th class="px-3 py-2 text-right">Terbayar</th>
                <th class="px-3 py-2 text-right">Sisa</th>
                <th class="px-3 py-2 text-center">Status</th>
            </tr>
        </thead>
        <tbody>
        @forelse($invoices as $inv)
            <tr class="border-b hover:bg-slate-50">
                <td class="px-3 py-2">{{ $inv->invoice_code }}</td>
                <td class="px-3 py-2">{{ $inv->program->name ?? '-' }}</td>
                <td class="px-3 py-2 text-center">
                    {{ $inv->due_date?->format('d/m/Y') ?? '-' }}
                </td>
                <td class="px-3 py-2 text-right">
                    Rp {{ number_format($inv->total_amount, 0, ',', '.') }}
                </td>
                <td class="px-3 py-2 text-right">
                    Rp {{ number_format($inv->paid_amount, 0, ',', '.') }}
                </td>
                <td class="px-3 py-2 text-right">
                    Rp {{ number_format($inv->remaining, 0, ',', '.') }}
                </td>
                <td class="px-3 py-2 text-center">
                    @if($inv->status === 'paid')
                        <span class="px-2 py-1 rounded-full bg-emerald-100 text-emerald-700 text-[11px] font-semibold">PAID</span>
                    @elseif($inv->status === 'partial')
                        <span class="px-2 py-1 rounded-full bg-amber-100 text-amber-700 text-[11px] font-semibold">PARTIAL</span>
                    @else
                        <span class="px-2 py-1 rounded-full bg-rose-100 text-rose-700 text-[11px] font-semibold">UNPAID</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="px-3 py-4 text-center text-slate-500">
                    Belum ada invoice untuk siswa ini.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

{{-- RIWAYAT PEMBAYARAN --}}
<div class="bg-white rounded-xl shadow mb-6 overflow-hidden text-xs">
    <div class="border-b px-4 py-2 bg-slate-50 font-semibold">
        Riwayat Pembayaran
    </div>
    <table class="w-full">
        <thead class="bg-slate-50 border-b">
            <tr>
                <th class="px-3 py-2 text-left">Tanggal</th>
                <th class="px-3 py-2 text-left">Invoice</th>
                <th class="px-3 py-2 text-right">Nominal</th>
                <th class="px-3 py-2 text-left">Metode</th>
                <th class="px-3 py-2 text-left">Catatan</th>
            </tr>
        </thead>
        <tbody>
        @forelse($payments as $pay)
            <tr class="border-b hover:bg-slate-50">
                <td class="px-3 py-2">
                    {{ $pay->paid_at?->format('d/m/Y') ?? $pay->created_at->format('d/m/Y') }}
                </td>
                <td class="px-3 py-2">
                    {{ $pay->invoice->invoice_code ?? '-' }}
                </td>
                <td class="px-3 py-2 text-right">
                    Rp {{ number_format($pay->amount, 0, ',', '.') }}
                </td>
                <td class="px-3 py-2">
                    {{ $pay->method ?? '-' }}
                </td>
                <td class="px-3 py-2">
                    {{ $pay->note ?? '-' }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="px-3 py-4 text-center text-slate-500">
                    Belum ada pembayaran tercatat untuk siswa ini.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
