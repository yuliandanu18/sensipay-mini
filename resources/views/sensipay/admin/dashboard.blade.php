@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto mt-8 space-y-6">

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">Dashboard Sensipay</h1>
        <a href="{{ route('sensipay.invoices.index') }}"
        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
            Lihat Semua Invoice
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="p-4 border rounded-lg bg-white shadow-sm">
            <div class="text-sm text-gray-500">Siswa Terdaftar</div>
            <div class="text-2xl font-bold mt-1">{{ $totalStudents }}</div>
        </div>

        <div class="p-4 border rounded-lg bg-white shadow-sm">
            <div class="text-sm text-gray-500">Program Aktif</div>
            <div class="text-2xl font-bold mt-1">{{ $totalPrograms }}</div>
        </div>

        <div class="p-4 border rounded-lg bg-white shadow-sm">
            <div class="text-sm text-gray-500">Total Invoice</div>
            <div class="text-2xl font-bold mt-1">{{ $totalInvoices }}</div>
        </div>

        <div class="p-4 border rounded-lg bg-white shadow-sm">
            <div class="text-sm text-gray-500">Tagihan Outstanding</div>
            <div class="text-xl font-bold mt-1">
                Rp {{ number_format($totalUnpaid, 0, ',', '.') }}
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="p-4 border rounded-lg bg-white shadow-sm">
            <div class="text-sm text-gray-500">Total Tagihan</div>
            <div class="text-xl font-bold mt-1">
                Rp {{ number_format($totalAmount, 0, ',', '.') }}
            </div>
        </div>
        <div class="p-4 border rounded-lg bg-white shadow-sm">
            <div class="text-sm text-gray-500">Total Terbayar</div>
            <div class="text-xl font-bold mt-1 text-green-700">
                Rp {{ number_format($totalPaid, 0, ',', '.') }}
            </div>
        </div>
        <div class="p-4 border rounded-lg bg-white shadow-sm">
            <div class="text-sm text-gray-500">Belum Terbayar</div>
            <div class="text-xl font-bold mt-1 text-red-700">
                Rp {{ number_format($totalUnpaid, 0, ',', '.') }}
            </div>
        </div>
    </div>

    <div class="p-4 border rounded-lg bg-white shadow-sm">
        <h2 class="font-semibold mb-3">Distribusi Status Invoice</h2>
        @if($byStatus->isEmpty())
            <p class="text-sm text-gray-500">Belum ada invoice.</p>
        @else
            <div class="flex flex-wrap gap-3">
                @foreach($byStatus as $row)
                    <div class="px-3 py-2 rounded border text-sm bg-gray-50">
                        <span class="font-semibold">{{ strtoupper($row->status) }}</span>
                        <span class="ml-2 text-gray-500">({{ $row->total }})</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="p-4 border rounded-lg bg-white shadow-sm">
        <h2 class="font-semibold mb-3">5 Invoice Terbaru</h2>

        @if($latestInvoices->isEmpty())
            <p class="text-sm text-gray-500">Belum ada invoice yang tercatat.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-3 py-2 text-left">Tanggal</th>
                            <th class="px-3 py-2 text-left">Siswa</th>
                            <th class="px-3 py-2 text-left">Program</th>
                            <th class="px-3 py-2 text-right">Total</th>
                            <th class="px-3 py-2 text-right">Terbayar</th>
                            <th class="px-3 py-2 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($latestInvoices as $invoice)
                            <tr class="border-b">
                                <td class="px-3 py-2">
                                    {{ $invoice->created_at?->format('d M Y') }}
                                </td>
                                <td class="px-3 py-2">
                                    {{ optional($invoice->student)->name ?? '-' }}
                                </td>
                                <td class="px-3 py-2">
                                    {{ optional($invoice->program)->name ?? '-' }}
                                </td>
                                <td class="px-3 py-2 text-right">
                                    Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                                </td>
                                <td class="px-3 py-2 text-right">
                                    Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <span class="inline-flex px-2 py-1 rounded-full text-xs
                                        @if($invoice->status === 'paid')
                                            bg-green-100 text-green-700
                                        @elseif($invoice->status === 'unpaid')
                                            bg-red-100 text-red-700
                                        @else
                                            bg-yellow-100 text-yellow-700
                                        @endif">
                                        {{ strtoupper($invoice->status) }}
                                    </span>
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
