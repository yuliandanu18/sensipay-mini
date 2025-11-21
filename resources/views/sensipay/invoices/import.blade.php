@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto mt-8">
    <h1 class="text-2xl font-bold mb-4">Import Invoice dari File</h1>

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
            <ul class="list-disc ml-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('sensipay.invoices.import.preview') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf

        <div>
            <label class="block font-medium mb-1">File Invoice (CSV / Excel)</label>
            <input type="file" name="file" class="border rounded w-full p-2">
            <p class="text-sm text-gray-500 mt-1">
                Pastikan kolom minimal berisi: Nama Siswa, Program, Total.
            </p>
        </div>

        <button type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Upload &amp; Preview
        </button>
    </form>
</div>
@endsection
