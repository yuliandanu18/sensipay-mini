@extends('sensipay.layout')

@section('title', 'Parent & OTM')
@section('page_title', 'Parent & OTM')

@section('content')
    <div class="max-w-6xl mx-auto">
        <h1 class="text-2xl font-bold mb-4">Daftar Akun Orang Tua</h1>

        @if ($parents->isEmpty())
            <div class="p-4 bg-white border rounded">
                Belum ada akun orang tua (role = parent) yang terdaftar.
            </div>
        @else
            <div class="bg-white border rounded shadow-sm overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 border-b">
                        <tr>
                            <th class="px-3 py-2 text-left">Nama</th>
                            <th class="px-3 py-2 text-left">Email</th>
                            <th class="px-3 py-2 text-right">Jumlah Invoice</th>
                            <th class="px-3 py-2 text-right">Total Tagihan</th>
                            <th class="px-3 py-2 text-right">Total Terbayar</th>
                            <th class="px-3 py-2 text-right">Sisa</th>
                            <th class="px-3 py-2 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($parents as $parent)
                            @php
                                $total  = $parent->invoices_total_amount ?? 0;
                                $paid   = $parent->invoices_paid_amount ?? 0;
                                $remain = max(0, $total - $paid);
                            @endphp
                            <tr class="border-b last:border-0">
                                <td class="px-3 py-2">
                                    <div class="font-semibold">{{ $parent->name }}</div>
                                </td>
                                <td class="px-3 py-2">
                                    <span class="text-xs text-slate-600">{{ $parent->email }}</span>
                                </td>
                                <td class="px-3 py-2 text-right">
                                    {{ $parent->invoices_count }}
                                </td>
                                <td class="px-3 py-2 text-right">
                                    Rp {{ number_format($total, 0, ',', '.') }}
                                </td>
                                <td class="px-3 py-2 text-right">
                                    Rp {{ number_format($paid, 0, ',', '.') }}
                                </td>
                                <td class="px-3 py-2 text-right">
                                    Rp {{ number_format($remain, 0, ',', '.') }}
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <a href="{{ route('sensipay.parents.show', $parent) }}"
                                       class="inline-block px-2 py-1 text-xs bg-slate-800 text-white rounded hover:bg-slate-700">
                                        Lihat detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $parents->links() }}
            </div>
        @endif
    </div>
@endsection
