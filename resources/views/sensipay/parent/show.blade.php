@extends('sensipay.layout')

@section('page_title', 'Detail Parent')

@section('content')
    <div class="max-w-5xl mx-auto space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold mb-1">Detail Orang Tua</h1>
                <div class="text-sm text-slate-600">
                    {{ $parent->name }} &middot; {{ $parent->email }}
                </div>
            </div>
            <a href="{{ route('sensipay.parents.index') }}"
               class="text-xs text-slate-600 hover:text-slate-900">
                &larr; Kembali ke daftar parent
            </a>
        </div>

        <div class="bg-white border rounded p-4">
            <h2 class="font-semibold mb-2 text-sm">Ringkasan Invoice</h2>

            @php
                $total  = $invoices->sum('total_amount');
                $paid   = $invoices->sum('paid_amount');
                $remain = max(0, $total - $paid);
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <div class="text-slate-500">Jumlah Invoice</div>
                    <div class="font-semibold">{{ $invoices->count() }}</div>
                </div>
                <div>
                    <div class="text-slate-500">Total Tagihan</div>
                    <div class="font-semibold">
                        Rp {{ number_format($total, 0, ',', '.') }}
                    </div>
                </div>
                <div>
                    <div class="text-slate-500">Sisa Tagihan</div>
                    <div class="font-semibold">
                        Rp {{ number_format($remain, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white border rounded p-4">
            <h2 class="font-semibold mb-2 text-sm">Daftar Invoice</h2>

            @if ($invoices->isEmpty())
                <div class="text-sm text-slate-600">
                    Belum ada invoice untuk parent ini.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-100">
                            <tr>
                                <th class="px-3 py-2 border text-left">Kode</th>
                                <th class="px-3 py-2 border text-left">Siswa</th>
                                <th class="px-3 py-2 border text-right">Total</th>
                                <th class="px-3 py-2 border text-right">Dibayar</th>
                                <th class="px-3 py-2 border text-right">Sisa</th>
                                <th class="px-3 py-2 border text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoices as $invoice)
                                @php
                                    $remain = max(0, ($invoice->total_amount ?? 0) - ($invoice->paid_amount ?? 0));
                                @endphp
                                <tr class="hover:bg-slate-50">
                                    <td class="px-3 py-2 border">
                                        {{ $invoice->invoice_code }}
                                    </td>
                                    <td class="px-3 py-2 border">
                                        {{ optional($invoice->student)->name ?? '-' }}
                                    </td>
                                    <td class="px-3 py-2 border text-right">
                                        Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-3 py-2 border text-right">
                                        Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-3 py-2 border text-right">
                                        Rp {{ number_format($remain, 0, ',', '.') }}
                                    </td>
                                    <td class="px-3 py-2 border text-center">
                                        @if ($invoice->status === 'paid')
                                            <span class="text-green-600 font-semibold">LUNAS</span>
                                        @elseif ($invoice->status === 'partial')
                                            <span class="text-yellow-600 font-semibold">ANGSUR</span>
                                        @else
                                            <span class="text-red-600 font-semibold">BELUM BAYAR</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
