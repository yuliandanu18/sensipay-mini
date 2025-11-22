@extends('sensipay.layout')

@section('title', 'Import Legacy Cicilan')
@section('page_title', 'Import Legacy Cicilan')

@section('content')
    <div class="max-w-xl mx-auto py-4">
        <h1 class="text-2xl font-bold mb-4">Import BAHANIMPORT (Cicilan Legacy)</h1>

        @if (session('status'))
            <div class="mb-4 p-3 rounded bg-green-100 text-green-800 text-sm">
                {{ session('status') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-3 rounded bg-red-100 text-red-800 text-sm">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-3 rounded bg-red-100 text-red-800 text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white border rounded p-4 shadow-sm">
            <p class="text-sm text-slate-600 mb-3">
                Upload file <span class="font-semibold">CSV</span> hasil konversi dari
                <span class="font-mono">BAHANIMPORT.xlsx</span> dengan kolom minimal:
                <span class="font-mono">invoice_code</span> dan
                <span class="font-mono">item_total_amount</span>.
            </p>

            <form action="{{ route('sensipay.legacy-installments.import.process') }}"
                  method="POST"
                  enctype="multipart/form-data"
                  class="space-y-3">
                @csrf

                <div>
                    <label class="block text-sm font-medium mb-1">
                        File CSV BAHANIMPORT
                    </label>
                    <input type="file"
                           name="file"
                           accept=".csv,text/csv"
                           class="block w-full text-sm border rounded px-2 py-1">
                </div>

                <button type="submit"
                        class="px-4 py-2 text-sm bg-slate-800 text-white rounded hover:bg-slate-700">
                    Proses Import & Update Invoice
                </button>
            </form>
        </div>
    </div>
@endsection
