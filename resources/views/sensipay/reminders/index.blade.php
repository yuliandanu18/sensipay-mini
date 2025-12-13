@extends('sensipay.layout')

@section('title','Reminder Tagihan')

@section('content')
<div class="mb-4 flex items-center justify-between">
    <div>
        <h1 class="text-xl font-semibold">Reminder Tagihan Jatuh Tempo</h1>
        <p class="text-xs text-slate-500">
            Daftar invoice yang sudah jatuh tempo dan belum lunas.
        </p>
    </div>

    @if(! $invoices->isEmpty())
        <form method="POST" action="{{ route('sensipay.reminders.send-bulk') }}">
            @csrf
            <button
                type="submit"
                class="px-3 py-1.5 text-xs rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold shadow">
                Kirim Reminder Semua
            </button>
        </form>
    @endif
</div>

@if (session('success'))
    <div class="mb-3 p-3 text-xs rounded bg-emerald-50 text-emerald-700">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="mb-3 p-3 text-xs rounded bg-red-50 text-red-700">
        {{ session('error') }}
    </div>
@endif

@if($invoices->isEmpty())
    <div class="p-4 bg-white rounded-xl shadow text-xs text-slate-500">
        Tidak ada invoice untuk diingatkan.
    </div>
@else
    <div class="bg-white rounded-xl shadow overflow-x-auto">
        <table class="min-w-full text-[11px]">
            <thead class="bg-slate-100 text-slate-600 uppercase">
    <tr>
        <th class="px-3 py-2 text-left">Siswa</th>
        <th class="px-3 py-2 text-left">Program</th>
        <th class="px-3 py-2 text-left">Kode</th>
        <th class="px-3 py-2 text-left">WA Ortu</th>   {{-- baru --}}
        <th class="px-3 py-2 text-right">Total</th>
        <th class="px-3 py-2 text-right">Terbayar</th>
        <th class="px-3 py-2 text-right">Sisa</th>
        <th class="px-3 py-2 text-left">Template Pesan</th>
        <th class="px-3 py-2 text-center">Aksi</th>
    </tr>
</thead>

            <tbody class="divide-y">
                @foreach($invoices as $inv)
                    @php
                        $remaining = max(0, ($inv->total_amount ?? 0) - ($inv->paid_amount ?? 0));
                        $student   = $inv->student->name ?? '-';
                        $program   = $inv->program->name ?? '-';
                        $due       = optional($inv->due_date)->format('d M Y');
                        $amountFmt = number_format($remaining, 0, ',', '.');

                        $msg = "Assalamu'alaikum Ayah/Bunda, ini pengingat tagihan Bimbel JET untuk "
                            . "{$student} (program {$program}). "
                            . "Sisa tagihan: Rp {$amountFmt}. Jatuh tempo: {$due}. "
                            . "Mohon kesediaannya untuk melakukan pembayaran. Terima kasih. 🙏";
                    @endphp
                    <tr class="hover:bg-slate-50">
    <td class="px-3 py-2">{{ $student }}</td>
    <td class="px-3 py-2">{{ $program }}</td>
    <td class="px-3 py-2">{{ $inv->invoice_code }}</td>
    <td class="px-3 py-2">
        {{ $inv->parent->whatsapp_number ?? '-' }}
    </td>
    <td class="px-3 py-2 text-right">
        {{ number_format($inv->total_amount, 0, ',', '.') }}
    </td>


                        <td class="px-3 py-2 text-right">
                            {{ number_format($inv->paid_amount, 0, ',', '.') }}
                        </td>
                        <td class="px-3 py-2 text-right">
                            {{ number_format($remaining, 0, ',', '.') }}
                        </td>
                        <td class="px-3 py-2">
                            <textarea
                                class="w-full border rounded p-1 text-[11px]"
                                rows="2"
                                onclick="this.select()">{{ $msg }}</textarea>
                        </td>
                        <td class="px-3 py-2 text-center">
                           <form method="POST"
      action="{{ route('sensipay.reminders.send-single', $inv) }}">

                                @csrf
                                <button
                                    type="submit"
                                    class="px-2 py-1 rounded bg-sky-600 hover:bg-sky-700 text-white text-[11px]">
                                    Kirim WA
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $invoices->links() }}
    </div>
@endif
@endsection
