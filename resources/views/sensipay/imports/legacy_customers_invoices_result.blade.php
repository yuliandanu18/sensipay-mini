@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto mt-8 space-y-6">
    <h1 class="text-2xl font-bold">Hasil Import Customer + Invoice Lama</h1>

    {{-- User baru yang dibuat --}}
    <div class="p-4 border rounded bg-white shadow-sm">
        <h2 class="font-semibold mb-2">User Baru (student_parent) yang Dibuat</h2>
        @if (empty($createdUsers))
            <p class="text-sm text-gray-500">Tidak ada user baru yang dibuat.</p>
        @else
            <p class="text-sm text-gray-600 mb-2">
                Simpan baik-baik email dan password berikut untuk dibagikan ke orang tua.
            </p>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-3 py-2 text-left">Nama</th>
                            <th class="px-3 py-2 text-left">Email</th>
                            <th class="px-3 py-2 text-left">Password</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($createdUsers as $user)
                            <tr class="border-b">
                                <td class="px-3 py-2">{{ $user['name'] }}</td>
                                <td class="px-3 py-2">{{ $user['email'] }}</td>
                                <td class="px-3 py-2 font-mono">{{ $user['password'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- User yang sudah ada --}}
    <div class="p-4 border rounded bg-white shadow-sm">
        <h2 class="font-semibold mb-2">User yang Sudah Ada (tidak dibuat ulang)</h2>
        @if (empty($existingUsers))
            <p class="text-sm text-gray-500">Tidak ada user existing berdasarkan email.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-3 py-2 text-left">Nama</th>
                            <th class="px-3 py-2 text-left">Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($existingUsers as $user)
                            <tr class="border-b">
                                <td class="px-3 py-2">{{ $user['name'] }}</td>
                                <td class="px-3 py-2">{{ $user['email'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Invoice yang dibuat --}}
    <div class="p-4 border rounded bg-white shadow-sm">
        <h2 class="font-semibold mb-2">Invoice Lama yang Berhasil Dibuat</h2>
        @if (empty($createdInvoices))
            <p class="text-sm text-gray-500">Tidak ada invoice yang berhasil dibuat.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-3 py-2 text-left">ID Invoice</th>
                            <th class="px-3 py-2 text-left">Siswa</th>
                            <th class="px-3 py-2 text-left">Program</th>
                            <th class="px-3 py-2 text-right">Total</th>
                            <th class="px-3 py-2 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($createdInvoices as $inv)
                            <tr class="border-b">
                                <td class="px-3 py-2">{{ $inv['invoice_id'] }}</td>
                                <td class="px-3 py-2">{{ $inv['student_name'] }}</td>
                                <td class="px-3 py-2">{{ $inv['program_name'] }}</td>
                                <td class="px-3 py-2 text-right">
                                    Rp {{ number_format($inv['total_amount'], 0, ',', '.') }}
                                </td>
                                <td class="px-3 py-2 text-center">
                                    {{ strtoupper($inv['status']) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Baris gagal --}}
    <div class="p-4 border rounded bg-white shadow-sm">
        <h2 class="font-semibold mb-2">Baris yang Gagal Diproses</h2>
        @if (empty($failedRows))
            <p class="text-sm text-gray-500">Semua baris berhasil diproses.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-xs">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-3 py-2 text-left">Alasan</th>
                            <th class="px-3 py-2 text-left">Data Baris</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($failedRows as $fail)
                            <tr class="border-b align-top">
                                <td class="px-3 py-2 text-red-700 w-1/3">
                                    {{ $fail['reason'] }}
                                </td>
                                <td class="px-3 py-2 font-mono text-[11px]">
                                    {{ json_encode($fail['row']) }}
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
