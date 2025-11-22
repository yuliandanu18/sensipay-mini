@extends('sensipay.layout')

@section('content')
@section('page_title', 'Dashboard Orang Tua')

    <div class="max-w-5xl mx-auto py-8">
        <h1 class="text-2xl font-bold mb-4">Dashboard Orang Tua Murid</h1>

        <p class="mb-6">
            Selamat datang,
            <span class="font-semibold">{{ $user->name }}</span>.
            Berikut adalah ringkasan tagihan (invoice) yang terhubung dengan akun Anda.
        </p>

        @if (session('status'))
            <div class="mb-4 p-3 rounded bg-green-100 text-green-800">
                {{ session('status') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-3 rounded bg-red-100 text-red-800">
                {{ session('error') }}
            </div>
        @endif

        @if ($invoices->isEmpty())
            <div class="p-4 border rounded bg-white">
                Belum ada invoice yang terhubung dengan akun Anda.
            </div>
        @else
            <div class="space-y-4">
                @foreach ($invoices as $invoice)
                    <div class="p-4 border rounded bg-white shadow-sm">
                        <div class="flex justify-between items-center mb-2">
                            <div>
                                <div class="text-sm text-gray-500">Kode Invoice</div>
                                <div class="font-semibold text-lg">{{ $invoice->invoice_code }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-gray-500">Status</div>
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
                                <div class="text-gray-500">Nama Siswa</div>
                                <div class="font-medium">
                                    {{ optional($invoice->student)->name ?? '-' }}
                                </div>
                            </div>
                            <div>
                                <div class="text-gray-500">Total Tagihan</div>
                                <div class="font-medium">
                                    Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                                </div>
                            </div>
                            <div>
                                <div class="text-gray-500">Sudah Dibayar</div>
                                <div class="font-medium">
                                    Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>

                        @php
                            $remaining = max(0, $invoice->total_amount - $invoice->paid_amount);
                        @endphp

                        <div class="mb-2 text-sm">
                            <span class="text-gray-500">Sisa Tagihan:</span>
                            <span class="font-semibold">
                                Rp {{ number_format($remaining, 0, ',', '.') }}
                            </span>
                        </div>

                        {{-- Bagian detail angsuran dimatikan dulu
                        @if ($invoice->installments && $invoice->installments->count())
                            ...
                        @endif
                        --}}
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
