@extends('sensipay.layout')

@section('title', 'Detail Parent')
@section('page_title', 'Detail Parent')

@section('content')
    <div class="max-w-5xl mx-auto space-y-4">
        <div class="bg-white border rounded p-4 shadow-sm">
            <h1 class="text-xl font-bold mb-2">Detail Orang Tua</h1>
            <div class="text-sm">
                <div><span class="font-semibold">Nama:</span> {{ $parent->name }}</div>
                <div><span class="font-semibold">Email:</span> {{ $parent->email }}</div>
                <div><span class="font-semibold">Role:</span> {{ $parent->role }}</div>
            </div>
        </div>

        <div class="bg-white border rounded p-4 shadow-sm">
            <h2 class="text-lg font-semibold mb-3">Daftar Invoice</h2>

            @if ($invoices->isEmpty())
                <div class="text-sm text-slate-500">
                    Belum ada invoice untuk akun ini.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 border-b">
                            <tr>
                                <th class="px-2 py-1 text-left">Kode</th>
                                <th class="px-2 py-1 text-left">Siswa</th>
                                <th class="px-2 py-1 text-right">Total</th>
                                <th class="px-2 py-1 text-right">Terbayar</th>
                                <th class="px-2 py-1 text-right">Sisa</th>
                                <th class="px-2 py-1 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoices as $invoice)
                                @php
                                    $total  = $invoice->total_amount ?? 0;
                                    $paid   = $invoice->paid_amount ?? 0;
                                    $remain = max(0, $total - $paid);
                                @endphp
                                <tr class="border-b last:border-0">
                                    <td class="px-2 py-1">
                                        {{ $invoice->invoice_code }}
                                    </td>
                                    <td class="px-2 py-1">
                                        {{ optional($invoice->student)->name ?? '-' }}
                                    </td>
                                    <td class="px-2 py-1 text-right">
                                        Rp {{ number_format($total, 0, ',', '.') }}
                                    </td>
                                    <td class="px-2 py-1 text-right">
                                        Rp {{ number_format($paid, 0, ',', '.') }}
                                    </td>
                                    <td class="px-2 py-1 text-right">
                                        Rp {{ number_format($remain, 0, ',', '.') }}
                                    </td>
                                    <td class="px-2 py-1 text-center">
                                        @if ($invoice->status === 'paid')
                                            <span class="text-green-600 font-semibold">LUNAS</span>
                                        @elseif ($invoice->status === 'partial')
                                            <span class="text-yellow-600 font-semibold">ANGSUR</span>
                                        @else
                                            <span class="text-red-600 font-semibold">BELUM</span>
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
