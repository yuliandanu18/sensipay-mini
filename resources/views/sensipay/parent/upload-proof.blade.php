@extends('layouts.app')

@section('content')
<div class="max-w-lg mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-xl font-bold mb-4">
        Upload Bukti Pembayaran - Invoice {{ $invoice->invoice_number ?? $invoice->id }}
    </h1>

    <form action="{{ route('sensipay.parent.invoices.upload-proof.store', $invoice) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-4">
            <label class="block mb-1 font-semibold">Nominal Transfer (opsional)</label>
            <input type="number" name="amount" class="w-full border rounded p-2" value="{{ old('amount') }}">
            @error('amount')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block mb-1 font-semibold">Tanggal Transfer (opsional)</label>
            <input type="date" name="transfer_date" class="w-full border rounded p-2" value="{{ old('transfer_date') }}">
            @error('transfer_date')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block mb-1 font-semibold">Bank / Channel (opsional)</label>
            <input type="text" name="bank_name" class="w-full border rounded p-2" value="{{ old('bank_name') }}">
            @error('bank_name')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block mb-1 font-semibold">File Bukti Transfer</label>
            <input type="file" name="proof" class="w-full">
            <p class="text-xs text-gray-500 mt-1">
                Format: JPG, JPEG, PNG, atau PDF. Maks 4 MB.
            </p>
            @error('proof')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
            Upload
        </button>
    </form>
</div>
@endsection