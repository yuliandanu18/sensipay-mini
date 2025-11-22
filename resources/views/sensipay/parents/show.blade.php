@extends('sensipay.layout')

@section('title', 'Detail Parent')
@section('page_title', 'Detail Parent & Invoice')

@section('content')
    <div class="max-w-5xl mx-auto space-y-4">

        @if (session('status'))
            <div class="p-3 rounded bg-green-100 text-green-800 text-sm">
                {{ session('status') }}
            </div>
        @endif

        @if (session('error'))
            <div class="p-3 rounded bg-red-100 text-red-800 text-sm">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="p-3 rounded bg-red-100 text-red-800 text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white border rounded p-4 shadow-sm">
            <h1 class="text-xl font-bold mb-2">Detail Orang Tua</h1>
            <div class="text-sm space-y-1">
                <div><span class="font-semibold">Nama:</span> {{ $parent->name }}</div>
                <div><span class="font-semibold">Email:</span> {{ $parent->email }}</div>
                <div><span class="font-semibold">Role:</span> {{ $parent->role }}</div>
            </div>
            <div class="mt-3 text-sm">
                <a href="{{ route('sensipay.parents.edit', $parent) }}"
                   class="inline-block px-3 py-1 text-xs rounded bg-slate-900 text-white hover:bg-slate-800">
                    Edit Data Orang Tua
                </a>
            </div>
        </div>

        <div class="bg-white border rounded p-4 shadow-sm space-y-3">
            <h2 class="text-lg font-semibold mb-2">Daftar Invoice</h2>

            @if ($invoices->isEmpty())
                <div class="text-sm text-slate-500 mb-2">
                    Belum ada invoice yang terhubung dengan akun ini.
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
                                <th class="px-2 py-1 text-center">Aksi</th>
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
                                    <td class="px-2 py-1 text-center">
                                        <form action="{{ route('sensipay.parents.detach-invoice', [$parent, $invoice]) }}"
                                              method="POST"
                                              onsubmit="return confirm('Lepas invoice ini dari orang tua?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="px-2 py-1 text-[11px] rounded border border-slate-300 text-slate-600 hover:bg-slate-50">
                                                Lepas
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <div class="mt-4 border-t border-slate-200 pt-3">
                <h3 class="text-sm font-semibold mb-2">Tambahkan Invoice ke Parent ini</h3>
                <form action="{{ route('sensipay.parents.attach-invoice', $parent) }}"
                      method="POST"
                      class="flex flex-col sm:flex-row gap-2 text-sm max-w-md">
                    @csrf
                    <input type="text"
                           name="invoice_code"
                           placeholder="Masukkan kode invoice, contoh: 25092595"
                           class="flex-1 border rounded px-2 py-1 text-sm"
                           value="{{ old('invoice_code') }}">
                    <button type="submit"
                            class="px-3 py-1 rounded bg-slate-900 text-white hover:bg-slate-800 text-xs">
                        Kaitkan Invoice
                    </button>
                </form>
                <p class="mt-1 text-[11px] text-slate-500">
                    Sistem akan mencari invoice berdasarkan <span class="font-mono">invoice_code</span>
                    dan mengubah <span class="font-mono">parent_user_id</span>-nya menjadi akun ini.
                </p>
            </div>
        </div>
    </div>
@endsection
