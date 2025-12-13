
@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-xl font-bold mb-4">Bukti Pembayaran Pending</h1>

    @if($pending->isEmpty())
        <p>Tidak ada bukti pembayaran yang menunggu verifikasi.</p>
    @else
        <table class="w-full border">
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-2 border">Tanggal</th>
                    <th class="p-2 border">Invoice</th>
                    <th class="p-2 border">Uploader</th>
                    <th class="p-2 border">Nominal (claim)</th>
                    <th class="p-2 border">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pending as $proof)
                    <tr>
                        <td class="p-2 border">{{ $proof->created_at->format('d-m-Y H:i') }}</td>
                        <td class="p-2 border">{{ $proof->invoice->invoice_number ?? $proof->invoice->id }}</td>
                        <td class="p-2 border">{{ $proof->uploader->name ?? 'N/A' }}</td>
                        <td class="p-2 border">
                            @if($proof->amount)
                                Rp {{ number_format($proof->amount, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="p-2 border">
                            <a href="{{ route('sensipay.admin.payment-proofs.show', $proof) }}" class="text-blue-600 underline">
                                Lihat & Verifikasi
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            {{ $pending->links() }}
        </div>
    @endif
</div>
@endsection
