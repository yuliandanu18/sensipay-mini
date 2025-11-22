@extends('sensipay.layout')

@section('page_title', 'Parent & OTM')

@section('content')
    <div class="max-w-6xl mx-auto">
        <h1 class="text-2xl font-bold mb-4">Daftar Orang Tua / Akun OTM</h1>

        @if ($parents->isEmpty())
            <div class="p-4 bg-white border rounded">
                Belum ada akun parent yang terdaftar.
            </div>
        @else
            <div class="overflow-x-auto bg-white border rounded shadow-sm">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-100">
                        <tr>
                            <th class="px-3 py-2 border text-left">Nama Ortu</th>
                            <th class="px-3 py-2 border text-left">Email</th>
                            <th class="px-3 py-2 border text-right"># Invoice</th>
                            <th class="px-3 py-2 border text-right">Total Tagihan</th>
                            <th class="px-3 py-2 border text-right">Sudah Dibayar</th>
                            <th class="px-3 py-2 border text-right">Sisa</th>
                            <th class="px-3 py-2 border text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($parents as $parent)
                            @php
                                $total  = $parent->invoices_total_amount ?? 0;
                                $paid   = $parent->invoices_paid_amount ?? 0;
                                $remain = max(0, $total - $paid);
                            @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="px-3 py-2 border">
                                    <div class="font-semibold">{{ $parent->name }}</div>
                                </td>
                                <td class="px-3 py-2 border">
                                    <div class="text-xs text-slate-600">{{ $parent->email }}</div>
                                </td>
                                <td class="px-3 py-2 border text-right">
                                    {{ $parent->invoices_count }}
                                </td>
                                <td class="px-3 py-2 border text-right">
                                    Rp {{ number_format($total, 0, ',', '.') }}
                                </td>
                                <td class="px-3 py-2 border text-right">
                                    Rp {{ number_format($paid, 0, ',', '.') }}
                                </td>
                                <td class="px-3 py-2 border text-right">
                                    Rp {{ number_format($remain, 0, ',', '.') }}
                                </td>
                                <td class="px-3 py-2 border text-center">
                                    <a href="{{ route('sensipay.parents.show', $parent) }}"
                                       class="inline-flex items-center px-3 py-1 rounded text-xs bg-slate-900 text-white hover:bg-slate-700">
                                        Lihat Invoice
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $parents->links() }}
            </div>
        @endif
    </div>
@endsection
