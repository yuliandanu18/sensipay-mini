@extends('sensipay.layout')

@section('title', 'Keuangan: ' . $student->name)

@section('content')

<h1 class="text-xl font-bold mb-4">Keuangan: {{ $student->name }}</h1>

<div class="grid grid-cols-3 gap-4 mb-6 text-sm">
    <div class="bg-white p-4 rounded-lg shadow">
        <div class="text-gray-500">Total Tagihan</div>
        <div class="font-bold">Rp {{ number_format($total_invoice,0,',','.') }}</div>
    </div>

    <div class="bg-white p-4 rounded-lg shadow">
        <div class="text-gray-500">Total Pembayaran</div>
        <div class="font-bold text-green-600">Rp {{ number_format($total_paid,0,',','.') }}</div>
    </div>

    <div class="bg-white p-4 rounded-lg shadow">
        <div class="text-gray-500">Sisa</div>
        <div class="font-bold text-red-600">Rp {{ number_format($remaining,0,',','.') }}</div>
    </div>
</div>

<h2 class="text-lg font-semibold mt-6 mb-2">Daftar Invoice</h2>
<table class="w-full text-sm bg-white rounded-lg shadow">
    <tr class="border-b bg-gray-100">
        <th class="p-2">Invoice</th>
        <th class="p-2">Program</th>
        <th class="p-2 text-right">Total</th>
        <th class="p-2 text-right">Terbayar</th>
        <th class="p-2 text-right">Sisa</th>
        <th class="p-2">Status</th>
    </tr>

    @foreach($invoices as $inv)
    <tr class="border-b">
        <td class="p-2">{{ $inv->invoice_code }}</td>
        <td class="p-2">{{ $inv->program->name ?? '-' }}</td>
        <td class="p-2 text-right">Rp {{ number_format($inv->total_amount,0,',','.') }}</td>
        <td class="p-2 text-right">Rp {{ number_format($inv->paid_amount,0,',','.') }}</td>
        <td class="p-2 text-right">Rp {{ number_format($inv->remaining,0,',','.') }}</td>
        <td class="p-2">
            @if($inv->status=='paid')
                <span class="text-green-600 font-bold">PAID</span>
            @elseif($inv->status=='partial')
                <span class="text-yellow-600 font-bold">PARTIAL</span>
            @else
                <span class="text-red-600 font-bold">UNPAID</span>
            @endif
        </td>
    </tr>
    @endforeach
</table>

<h2 class="text-lg font-semibold mt-6 mb-2">Riwayat Pembayaran</h2>
<table class="w-full text-sm bg-white rounded-lg shadow mb-10">
    <tr class="border-b bg-gray-100">
        <th class="p-2">Tanggal</th>
        <th class="p-2">Invoice</th>
        <th class="p-2 text-right">Nominal</th>
        <th class="p-2">Metode</th>
    </tr>

    @foreach($payments as $pay)
    <tr class="border-b">
        <td class="p-2">{{ $pay->created_at->format('d/m/Y') }}</td>
        <td class="p-2">{{ $pay->invoice->invoice_code }}</td>
        <td class="p-2 text-right">Rp {{ number_format($pay->amount,0,',','.') }}</td>
        <td class="p-2">{{ $pay->method }}</td>
    </tr>
    @endforeach
</table>

@endsection
