@extends('sensipay.layout')

@section('title', 'Daftar Invoice - Sensipay')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-semibold">Daftar Invoice</h1>
    <a href="{{ route('sensipay.invoices.create') }}" class="inline-flex items-center px-3 py-2 rounded-md bg-sky-600 text-white text-sm font-medium hover:bg-sky-700">
        + Buat Invoice
    </a>
</div>

<form method="get" class="mb-4">
    <div class="flex gap-2">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari kode invoice / nama siswa" class="flex-1 rounded-md border-slate-300 text-sm">
        <button class="px-3 py-2 rounded-md bg-slate-800 text-white text-sm">Cari</button>
    </div>
</form>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="min-w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-200">
        <tr>
            <th class="px-3 py-2 text-left">Tanggal</th>
            <th class="px-3 py-2 text-left">Kode</th>
            <th class="px-3 py-2 text-left">Siswa</th>
            <th class="px-3 py-2 text-left">Program</th>
            <th class="px-3 py-2 text-right">Total</th>
            <th class="px-3 py-2 text-right">Terbayar</th>
            <th class="px-3 py-2 text-center">Status</th>
            <th class="px-3 py-2 text-right">Aksi</th>
        </tr>
        </thead>
        <tbody>
        @forelse($invoices as $invoice)
            <tr class="border-b border-slate-100 hover:bg-slate-50">
                <td class="px-3 py-2 text-xs text-slate-500">
                    {{ $invoice->created_at?->format('d/m/Y') }}
                </td>
                <td class="px-3 py-2 font-mono text-xs">
                    {{ $invoice->invoice_code }}
                </td>
                <td class="px-3 py-2">
                    {{ $invoice->student?->name }}
                </td>
                <td class="px-3 py-2 text-xs">
                    {{ $invoice->program?->name }}
                </td>
                <td class="px-3 py-2 text-right">
                    {{ number_format($invoice->total_amount, 0, ',', '.') }}
                </td>
                <td class="px-3 py-2 text-right">
                    {{ number_format($invoice->paid_amount, 0, ',', '.') }}
                </td>
                <td class="px-3 py-2 text-center">
                    @php
                        $color = [
                            'unpaid' => 'bg-red-50 text-red-700 border-red-200',
                            'partial' => 'bg-amber-50 text-amber-700 border-amber-200',
                            'paid' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        ][$invoice->status] ?? 'bg-slate-50 text-slate-700 border-slate-200';
                    @endphp
                    <span class="inline-flex items-center px-2 py-1 rounded-full border text-xs font-medium {{ $color }}">
                        {{ strtoupper($invoice->status) }}
                    </span>
                </td>
                <td class="px-3 py-2 text-right">
                    <a href="{{ route('sensipay.invoices.show', $invoice) }}" class="text-xs text-sky-700 hover:underline">Detail</a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="px-3 py-4 text-center text-sm text-slate-500">
                    Belum ada invoice.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $invoices->withQueryString()->links() }}
</div>
@endsection
