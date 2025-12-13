
@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-xl font-bold mb-4">
        Verifikasi Bukti Pembayaran #{{ $proof->id }}
    </h1>

    <div class="mb-4">
        <p><strong>Invoice:</strong> {{ $invoice->invoice_number ?? $invoice->id }}</p>
        <p><strong>Siswa:</strong> {{ $invoice->student->name ?? '-' }}</p>
        <p><strong>Nominal Claim:</strong>
            @if($proof->amount)
                Rp {{ number_format($proof->amount, 0, ',', '.') }}
            @else
                -
            @endif
        </p>
        <p><strong>Sisa Tagihan Saat Ini:</strong>
            Rp {{ number_format($invoice->remaining_amount ?? 0, 0, ',', '.') }}
        </p>
        <p><strong>Tanggal Transfer:</strong>
            {{ $proof->transfer_date?->format('d-m-Y') ?? '-' }}
        </p>
        <p><strong>Bank/Channel:</strong> {{ $proof->bank_name ?? '-' }}</p>
    </div>

    <div class="mb-4">
        <p class="font-semibold mb-1">File Bukti Pembayaran:</p>
        <a href="{{ asset('storage/' . $proof->file_path) }}" target="_blank" class="text-blue-600 underline">
            Lihat Bukti
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
        {{-- Form Approve --}}
        <div>
            <h2 class="font-semibold mb-2">Setujui & Catat Pembayaran</h2>
            <form action="{{ route('sensipay.admin.payment-proofs.approve', $proof) }}" method="POST">
                @csrf
                <label class="block mb-1 text-sm font-semibold">
                    Nominal yang Diakui
                </label>
                <input type="number" name="amount" class="w-full border rounded p-2 mb-2"
                       value="{{ old('amount', $proof->amount ?? $invoice->remaining_amount) }}">

                @error('amount')
                    <p class="text-red-600 text-sm mb-2">{{ $message }}</p>
                @enderror

                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">
                    Setujui & Catat Pembayaran
                </button>
            </form>
        </div>

        {{-- Form Reject --}}
        <div>
            <h2 class="font-semibold mb-2">Tolak Bukti Pembayaran</h2>
            <form action="{{ route('sensipay.admin.payment-proofs.reject', $proof) }}" method="POST">
                @csrf
                <label class="block mb-1 text-sm font-semibold">
                    Alasan Penolakan
                </label>
                <textarea name="reason" class="w-full border rounded p-2 mb-2" rows="3">{{ old('reason') }}</textarea>

                @error('reason')
                    <p class="text-red-600 text-sm mb-2">{{ $message }}</p>
                @enderror

                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">
                    Tolak Bukti Pembayaran
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
