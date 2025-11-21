
@extends('sensipay.layout')

@section('title','Reminder Tagihan')

@section('content')
<div class="mb-4">
    <h1 class="text-xl font-semibold">Reminder Tagihan Jatuh Tempo</h1>
    <p class="text-xs text-slate-500">Invoice jatuh tempo tanggal 20 bulan ini.</p>
</div>

@if($invoices->isEmpty())
<div class="p-4 bg-white rounded-xl shadow text-xs text-slate-500">
    Tidak ada invoice untuk diingatkan.
</div>
@else
<div class="bg-white rounded-xl shadow p-0 overflow-hidden text-xs">
<table class="min-w-full">
<thead class="bg-slate-100 border-b">
<tr>
<th class="px-3 py-2 text-left">Siswa</th>
<th class="px-3 py-2 text-left">Program</th>
<th class="px-3 py-2 text-left">Invoice</th>
<th class="px-3 py-2 text-right">Total</th>
<th class="px-3 py-2 text-right">Terbayar</th>
<th class="px-3 py-2 text-right">Sisa</th>
<th class="px-3 py-2 text-left">Template WA</th>
</tr>
</thead>
<tbody>
@foreach($invoices as $inv)
@php
$remaining = max(0, ($inv->total_amount ?? 0) - ($inv->paid_amount ?? 0));
$student = $inv->student->name ?? '-';
$program = $inv->program->name ?? '-';
$due = optional($inv->due_date)->format('d M Y');
$msg = "Assalamu'alaikum, ini pengingat tagihan Bimbel JET untuk {$student} program {$program}. Total: Rp ".number_format($inv->total_amount,0,',','.').", Terbayar: Rp ".number_format($inv->paid_amount,0,',','.').", Sisa: Rp ".number_format($remaining,0,',','.').". Jatuh tempo: {$due}. Terima kasih.";
@endphp
<tr class="border-b hover:bg-slate-50">
<td class="px-3 py-2">{{ $student }}</td>
<td class="px-3 py-2">{{ $program }}</td>
<td class="px-3 py-2">{{ $inv->invoice_code }}</td>
<td class="px-3 py-2 text-right">{{ number_format($inv->total_amount,0,',','.') }}</td>
<td class="px-3 py-2 text-right">{{ number_format($inv->paid_amount,0,',','.') }}</td>
<td class="px-3 py-2 text-right">{{ number_format($remaining,0,',','.') }}</td>
<td class="px-3 py-2">
<textarea class="w-full border rounded p-1 text-[11px]" rows="2" onclick="this.select()">{{ $msg }}</textarea>
</td>
</tr>
@endforeach
</tbody>
</table>
</div>
@endif
@endsection
