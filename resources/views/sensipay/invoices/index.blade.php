@extends('sensipay.layout')

@section('title', 'Daftar Invoice',)

@section('content')

<h1 class="text-xl font-semibold mb-4">Daftar Invoice</h1>

{{-- FILTER --}}
<form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3 mb-5 text-xs">

    <input type="text" name="search" value="{{ $search }}"
        placeholder="Cari siswa / invoice"
        class="border rounded-lg p-2">

    <select name="status" class="border rounded-lg p-2">
        <option value="">Status</option>
        <option value="unpaid"  @selected($status=='unpaid')>Unpaid</option>
        <option value="partial" @selected($status=='partial')>Partial</option>
        <option value="paid"    @selected($status=='paid')>Paid</option>
    </select>

    <select name="program" class="border rounded-lg p-2">
        <option value="">Program</option>
        @foreach($programs as $p)
            <option value="{{ $p->id }}" @selected($program_id==$p->id)>
                {{ $p->name }}
            </option>
        @endforeach
    </select>

    <input type="month" name="month" value="{{ $month }}"
        class="border rounded-lg p-2">

    <button class="bg-blue-600 text-white rounded-lg p-2">
        Filter
    </button>

</form>

{{-- TABLE --}}
<div class="bg-white rounded-xl shadow text-xs overflow-hidden">
    <table class="w-full">
        <thead class="bg-slate-100">
            <tr class="border-b">
                <th class="p-2 text-left">Invoice</th>
                <th class="p-2 text-left">Siswa</th>
                <th class="p-2 text-left">Program</th>
                <th class="p-2 text-right">Total</th>
                <th class="p-2 text-right">Bayar</th>
                <th class="p-2 text-right">Sisa</th>
                <th class="p-2 text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $inv)
            <tr class="border-b hover:bg-slate-50">
                <td class="p-2">{{ $inv->invoice_code }}</td>
                <td class="p-2">{{ $inv->student->name }}</td>
                <td class="p-2">{{ $inv->program->name ?? '-' }}</td>

                <td class="p-2 text-right">
                    Rp {{ number_format($inv->total_amount,0,',','.') }}
                </td>

                <td class="p-2 text-right">
                    Rp {{ number_format($inv->paid_amount,0,',','.') }}
                </td>

                <td class="p-2 text-right font-semibold
                    @if($inv->remaining > 0) text-red-600 @else text-green-600 @endif">
                    Rp {{ number_format($inv->remaining,0,',','.') }}
                </td>

                <td class="p-2 text-center">
                    @if($inv->status=='paid')
                        <span class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded-full">PAID</span>
                    @elseif($inv->status=='partial')
                        <span class="px-2 py-1 bg-amber-100 text-amber-700 rounded-full">PARTIAL</span>
                    @else
                        <span class="px-2 py-1 bg-rose-100 text-rose-700 rounded-full">UNPAID</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $invoices->withQueryString()->links() }}
</div>

@endsection
