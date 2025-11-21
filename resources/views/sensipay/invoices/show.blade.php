@extends('sensipay.layout')

@section('title', 'Detail Invoice - Sensipay')

@section('content')
<div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4 mb-4">
    <div>
        <h1 class="text-xl font-semibold mb-1">Invoice {{ $invoice->invoice_code }}</h1>
        <div class="text-xs text-slate-500 space-y-1">
            <div>Dibuat: {{ $invoice->created_at?->format('d/m/Y H:i') }}</div>
            <div>Jatuh Tempo: {{ $invoice->due_date?->format('d/m/Y') ?? '-' }}</div>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('sensipay.invoices.edit', $invoice) }}" class="px-3 py-2 rounded-md border text-xs">Edit</a>
        <a href="{{ route('sensipay.invoices.index') }}" class="px-3 py-2 rounded-md border text-xs">Kembali</a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
    <div class="bg-white rounded-xl shadow-sm p-4 space-y-2">
        <h2 class="text-sm font-semibold mb-2">Data Siswa</h2>
        <div class="text-sm">
            <div class="font-medium">{{ $invoice->student?->name }}</div>
            <div class="text-xs text-slate-500">
                {{ $invoice->student?->grade }} - {{ $invoice->student?->school }}
            </div>
            <div class="text-xs text-slate-500 mt-1">
                Ortu: {{ $invoice->student?->parent_name }}<br>
                WA: {{ $invoice->student?->whatsapp }}
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-4 space-y-2">
        <h2 class="text-sm font-semibold mb-2">Program</h2>
        <div class="text-sm">
            <div class="font-medium">{{ $invoice->program?->name ?? '-' }}</div>
            @if($invoice->program)
                <div class="text-xs text-slate-500">
                    Level: {{ $invoice->program->level }}<br>
                    Sesi: {{ $invoice->program->total_sessions }}x @ {{ number_format($invoice->program->price_per_session, 0, ',', '.') }}
                </div>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-4 space-y-2">
        <h2 class="text-sm font-semibold mb-2">Ringkasan Tagihan</h2>
        <dl class="text-sm space-y-1">
            <div class="flex justify-between">
                <dt>Total Tagihan</dt>
                <dd>Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</dd>
            </div>
            <div class="flex justify-between">
                <dt>Terbayar</dt>
                <dd>Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</dd>
            </div>
            <div class="flex justify-between font-semibold">
                <dt>Sisa</dt>
                <dd>Rp {{ number_format($invoice->remaining, 0, ',', '.') }}</dd>
            </div>
            <div class="pt-2">
                @php
                    $color = [
                        'unpaid' => 'bg-red-50 text-red-700 border-red-200',
                        'partial' => 'bg-amber-50 text-amber-700 border-amber-200',
                        'paid' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                    ][$invoice->status] ?? 'bg-slate-50 text-slate-700 border-slate-200';
                @endphp
                <span class="inline-flex items-center px-2 py-1 rounded-full border text-xs font-medium {{ $color }}">
                    Status: {{ strtoupper($invoice->status) }}
                </span>
            </div>
        </dl>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="bg-white rounded-xl shadow-sm p-4">
        <h2 class="text-sm font-semibold mb-3">Riwayat Pembayaran</h2>

        <table class="w-full text-xs">
            <thead>
            <tr class="border-b border-slate-200 text-left">
                <th class="py-1">Tanggal</th>
                <th class="py-1 text-right">Nominal</th>
                <th class="py-1">Metode</th>
                <th class="py-1">Catatan</th>
                <th class="py-1 text-right">Aksi</th>
            </tr>
            </thead>
            <tbody>
            @forelse($invoice->payments as $payment)
                <tr class="border-b border-slate-100">
                    <td class="py-1">
                        {{ $payment->paid_at?->format('d/m/Y') }}
                    </td>
                    <td class="py-1 text-right">
                        Rp {{ number_format($payment->amount, 0, ',', '.') }}
                    </td>
                    <td class="py-1">
                        {{ $payment->method }}
                    </td>
                    <td class="py-1 text-xs">
                        {{ $payment->note }}
                    </td>
                    <td class="py-1 text-right">
                        <form method="post" action="{{ route('sensipay.payments.destroy', $payment) }}" onsubmit="return confirm('Hapus pembayaran ini?')">
                            @csrf
                            @method('delete')
                            <button class="text-red-600 hover:underline">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="py-2 text-center text-slate-500">
                        Belum ada pembayaran.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-4">
        <h2 class="text-sm font-semibold mb-3">Tambah Pembayaran</h2>

        <form method="post" action="{{ route('sensipay.payments.store', $invoice) }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-medium mb-1">Tanggal Bayar</label>
                <input type="date" name="paid_at" value="{{ old('paid_at', now()->format('Y-m-d')) }}" class="w-full rounded-md border-slate-300 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium mb-1">Nominal (Rp)</label>
                <input type="number" name="amount" value="{{ old('amount') }}" min="0" class="w-full rounded-md border-slate-300 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium mb-1">Metode</label>
                <input type="text" name="method" value="{{ old('method', 'cash') }}" class="w-full rounded-md border-slate-300 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium mb-1">Catatan</label>
                <textarea name="note" rows="2" class="w-full rounded-md border-slate-300 text-sm">{{ old('note') }}</textarea>
            </div>
            <div class="flex justify-end">
                <button class="px-3 py-2 rounded-md bg-emerald-600 text-white text-xs">Simpan Pembayaran</button>
            </div>
        </form>
    </div>
</div>
@endsection
