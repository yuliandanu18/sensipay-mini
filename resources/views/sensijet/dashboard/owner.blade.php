@extends('sensijet.layout')

@section('title','Dashboard Owner')

@section('content')
<h1 class="text-xl font-semibold mb-4">Dashboard Owner</h1>

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 text-xs">
    <div class="bg-white p-4 rounded-xl shadow">
        <div class="text-slate-500">Pemasukan Bulan Ini</div>
        <div class="text-lg font-bold">Rp {{ number_format($income,0,',','.') }}</div>
    </div>
    <div class="bg-white p-4 rounded-xl shadow">
        <div class="text-slate-500">Piutang</div>
        <div class="text-lg font-bold text-red-600">Rp {{ number_format($piutang,0,',','.') }}</div>
    </div>
    <div class="bg-white p-4 rounded-xl shadow">
        <div class="text-slate-500">Total Sesi KBM</div>
        <div class="text-lg font-bold">{{ $sessions }}</div>
    </div>
    <div class="bg-white p-4 rounded-xl shadow">
        <div class="text-slate-500">Estimasi Gaji Guru</div>
        <div class="text-lg font-bold">Rp {{ number_format($payroll,0,',','.') }}</div>
    </div>
</div>

<div class="bg-white p-4 rounded-xl shadow mb-6">
    <h2 class="text-sm font-semibold mb-2">Pergerakan 30 Hari</h2>
    <canvas id="chart30"></canvas>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
    <div class="bg-white p-4 rounded-xl shadow">
        <h2 class="font-semibold mb-2">Top Invoice Belum Lunas</h2>
        <table class="w-full text-xs">
            @foreach($top_unpaid as $inv)
                <tr class="border-b">
                    <td>{{ $inv->invoice_code }}</td>
                    <td class="text-right">Rp {{ number_format($inv->total_amount-$inv->paid_amount,0,',','.') }}</td>
                </tr>
            @endforeach
        </table>
    </div>

    <div class="bg-white p-4 rounded-xl shadow">
        <h2 class="font-semibold mb-2">Guru Dengan Sesi Terbanyak</h2>
        <table class="w-full text-xs">
            @foreach($top_teachers as $t)
                <tr class="border-b">
                    <td>{{ $t->teacher->name ?? '-' }}</td>
                    <td class="text-right">{{ $t->total }} sesi</td>
                </tr>
            @endforeach
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
var ctx = document.getElementById('chart30');
var chart = new Chart(ctx,{
    type:'line',
    data:{
        labels: {!! json_encode(array_keys($chart)) !!},
        datasets:[
            {
                label:'Income',
                data: {!! json_encode(array_column($chart,'income')) !!},
                borderColor:'green',
                tension:0.2
            },
            {
                label:'Sessions',
                data: {!! json_encode(array_column($chart,'sessions')) !!},
                borderColor:'blue',
                tension:0.2
            }
        ]
    }
});
</script>

@endsection
