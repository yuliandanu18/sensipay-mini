@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto mt-8">
    <h1 class="text-2xl font-bold mb-4">Preview Import Invoice</h1>

    @if (!empty($failed))
        <div class="mb-4 p-3 bg-yellow-100 text-yellow-800 rounded">
            <p class="font-semibold mb-1">Beberapa baris bermasalah:</p>
            <p class="text-sm">
                Cek kembali nama siswa / program di file sumber. Baris yang merah tidak akan di-import.
            </p>
        </div>
    @endif

    <div class="overflow-x-auto border rounded mb-6">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-3 py-2 text-left">Nama Siswa (File)</th>
                    <th class="px-3 py-2 text-left">Siswa Match di DB</th>
                    <th class="px-3 py-2 text-left">Program (File)</th>
                    <th class="px-3 py-2 text-left">Program Match di DB</th>
                    <th class="px-3 py-2 text-right">Total</th>
                    <th class="px-3 py-2 text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($preview as $row)
                    <tr class="{{ $row['ok'] ? 'bg-green-50' : 'bg-red-50' }}">
                        <td class="px-3 py-2">{{ $row['nama_siswa'] }}</td>
                        <td class="px-3 py-2">
                            @if($row['student'])
                                {{ $row['student']->name }} (ID: {{ $row['student']->id }})
                            @else
                                <span class="text-red-700 italic">Tidak ditemukan</span>
                            @endif
                        </td>
                        <td class="px-3 py-2">{{ $row['nama_program'] }}</td>
                        <td class="px-3 py-2">
                            @if($row['program'])
                                {{ $row['program']->name }} (ID: {{ $row['program']->id }})
                            @else
                                <span class="text-red-700 italic">Tidak ditemukan</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-right">
                            {{ $row['total'] }}
                        </td>
                        <td class="px-3 py-2 text-center">
                            @if($row['ok'])
                                <span class="text-green-700 font-semibold">Akan di-import</span>
                            @else
                                <span class="text-red-700 font-semibold">Dilewati</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-3 py-4 text-center text-gray-500">
                            Tidak ada data yang terbaca dari file.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <form action="{{ route('sensipay.invoices.import.process') }}" method="POST" class="flex items-center gap-3">
        @csrf

        <a href="{{ route('sensipay.invoices.import.form') }}"
           class="px-4 py-2 border rounded hover:bg-gray-50">
            Kembali &amp; Ganti File
        </a>

        <button type="submit"
            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
            @if(empty($preview) || count($preview) === count($failed)) disabled @endif>
            Import Sekarang
        </button>
    </form>
</div>
@endsection
