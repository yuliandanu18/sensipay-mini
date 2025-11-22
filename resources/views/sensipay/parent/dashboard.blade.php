@extends('sensipay.layout')

@section('title', 'Dashboard Orang Tua')
@section('page_title', 'Dashboard Orang Tua')

@section('content')
    <div class="max-w-5xl mx-auto py-4">
        <h1 class="text-2xl font-bold mb-2">Dashboard Orang Tua Murid</h1>

        <p class="mb-4 text-sm text-slate-600">
            Selamat datang,
            <span class="font-semibold">{{ $user->name }}</span>.
            Berikut ringkasan tagihan yang terhubung dengan akun Anda.
        </p>

        @if ($invoices->isEmpty())
            <div class="p-4 border rounded bg-white">
                Belum ada invoice yang terhubung dengan akun Anda.
            </div>
        @else
            <div class="space-y-3">
                @foreach ($invoices as $invoice)
                    @php
                        $remaining = max(0, ($invoice->total_amount ?? 0) - ($invoice->paid_amount ?? 0));
                    @endphp
                    <div class="p-4 border rounded bg-white shadow-sm">
                        <div class="flex justify-between items-center mb-2">
                            <div>
                                <div class="text-xs text-slate-500">Kode Invoice</div>
                                <div class="font-semibold text-lg">{{ $invoice->invoice_code }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-xs text-slate-500">Status</div>
                                <div class="font-semibold">
                                    @if ($invoice->status === 'paid')
                                        <span class="text-green-600">LUNAS</span>
                                    @elseif ($invoice->status === 'partial')
                                        <span class="text-yellow-600">ANGSUR</span>
                                    @else
                                        <span class="text-red-600">BELUM BAYAR</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm mb-3">
                            <div>
                                <div class="text-slate-500 text-xs">Nama Siswa</div>
                                <div class="font-medium">
                                    {{ optional($invoice->student)->name ?? '-' }}
                                </div>
                            </div>
                            <div>
                                <div class="text-slate-500 text-xs">Total Tagihan</div>
                                <div class="font-medium">
                                    Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                                </div>
                            </div>
                            <div>
                                <div class="text-slate-500 text-xs">Sudah Dibayar</div>
                                <div class="font-medium">
                                    Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>

                        <div class="text-sm">
                            <span class="text-slate-500">Sisa Tagihan:</span>
                            <span class="font-semibold">
                                Rp {{ number_format($remaining, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
