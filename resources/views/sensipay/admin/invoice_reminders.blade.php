@extends('sensipay.layout')

@section('title', 'Reminder Tagihan')

@section('content')
<div class="container mx-auto py-6 space-y-4">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Reminder Tagihan Orang Tua</h1>
        <p class="text-xs text-slate-500">
            Halaman ini menampilkan invoice yang statusnya masih <strong>UNPAID</strong> atau <strong>PARTIAL</strong>.
        </p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <h2 class="font-semibold text-sm">Daftar Tagihan Belum Lunas</h2>
            <span class="text-xs text-slate-500">
                {{ $invoices->count() }} invoice
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-xs">
                <thead class="bg-slate-100">
                    <tr>
                        <th class="px-3 py-2 text-left">Siswa / Ortu</th>
                        <th class="px-3 py-2 text-left">Program</th>
                        <th class="px-3 py-2 text-right">Total</th>
                        <th class="px-3 py-2 text-right">Terbayar</th>
                        <th class="px-3 py-2 text-right">Sisa</th>
                        <th class="px-3 py-2 text-center">Jatuh Tempo</th>
                        <th class="px-3 py-2 text-center">Status</th>
                        <th class="px-3 py-2 text-left">Template WA</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($invoices as $inv)
                    @php
                        $total = $inv->total_amount ?? 0;
                        $paid  = $inv->paid_amount ?? 0;
                        $remaining = max(0, $total - $paid);
                        $studentName = $inv->student->name ?? '-';
                        $programName = $inv->program->name ?? '-';
                        $due = optional($inv->due_date)->format('d/m/Y') ?? '-';
                        $msg = "Assalamu'alaikum Ayah/Bunda, ini pengingat tagihan Bimbel JET untuk *{$studentName}* (%0A".
                               "Program: {$programName}%0A".
                               "Total: Rp ".number_format($total,0,',','.')."%0A".
                               "Terbayar: Rp ".number_format($paid,0,',','.')."%0A".
                               "Sisa: Rp ".number_format($remaining,0,',','.')."%0A".
                               "Jatuh tempo: {$due}%0A%0A".
                               "Mohon konfirmasi jika sudah melakukan pembayaran. Terima kasih 🙏";
                    @endphp
                    <tr class="border-t border-slate-100 align-top">
                        <td class="px-3 py-2">
                            <div class="font-medium">{{ $studentName }}</div>
                            <div class="text-[10px] text-slate-500">
                                Invoice: {{ $inv->invoice_code }}
                            </div>
                        </td>
                        <td class="px-3 py-2">
                            <div>{{ $programName }}</div>
                        </td>
                        <td class="px-3 py-2 text-right">
                            Rp {{ number_format($total, 0, ',', '.') }}
                        </td>
                        <td class="px-3 py-2 text-right">
                            Rp {{ number_format($paid, 0, ',', '.') }}
                        </td>
                        <td class="px-3 py-2 text-right">
                            Rp {{ number_format($remaining, 0, ',', '.') }}
                        </td>
                        <td class="px-3 py-2 text-center text-[11px]">
                            {{ $due }}
                        </td>
                        <td class="px-3 py-2 text-center">
                            <span class="inline-flex px-2 py-1 rounded-full text-[10px]
                                @if($inv->status === 'paid') bg-emerald-100 text-emerald-700
                                @elseif($inv->status === 'partial') bg-amber-100 text-amber-700
                                @else bg-rose-100 text-rose-700 @endif">
                                {{ strtoupper($inv->status ?? 'UNPAID') }}
                            </span>
                        </td>
                        <td class="px-3 py-2">
                            <div class="flex flex-col gap-1">
                                <textarea
                                    class="w-full border border-slate-200 rounded-md p-1 text-[11px] font-mono"
                                    rows="4"
                                    readonly
                                >Assalamu'alaikum Ayah/Bunda, ini pengingat tagihan Bimbel JET untuk {{ $studentName }}.
Program: {{ $programName }}
Total: Rp {{ number_format($total,0,',','.') }}
Terbayar: Rp {{ number_format($paid,0,',','.') }}
Sisa: Rp {{ number_format($remaining,0,',','.') }}
Jatuh tempo: {{ $due }}

Mohon konfirmasi jika sudah melakukan pembayaran. Terima kasih 🙏</textarea>

                                <a href="https://wa.me/?text={{ $msg }}"
                                   target="_blank"
                                   class="inline-flex items-center justify-center px-2 py-1 rounded-md text-[11px] bg-emerald-600 text-white hover:bg-emerald-700">
                                    Buka WhatsApp (manual)
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-3 py-6 text-center text-slate-500">
                            Tidak ada invoice yang perlu diingatkan.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
