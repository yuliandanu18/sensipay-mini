@extends('sensipay.layout')

@section('content')
    <div class="max-w-xl mx-auto py-8">
        <h1 class="text-2xl font-bold mb-4">Import Cicilan Legacy</h1>

        <p class="mb-4 text-sm text-gray-700">
            Upload file CSV berisi data orang tua, siswa, dan invoice lama.
            Untuk versi awal, gunakan header minimal:
            <code>parent_name, parent_email, student_name, invoice_code, total_amount</code>.
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

        @if ($errors->any())
            <div class="mb-4 p-3 rounded bg-red-100 text-red-800 text-sm">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('sensipay.legacy-installments.import.process') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium mb-1" for="file">
                    File CSV Legacy
                </label>
                <input
                    type="file"
                    name="file"
                    id="file"
                    required
                    class="block w-full text-sm border rounded px-2 py-1"
                >
            </div>

            <button
                type="submit"
                class="px-4 py-2 rounded bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700"
            >
                Import
            </button>
        </form>
    </div>
@endsection
@if (session('new_parents') && is_array(session('new_parents')) && count(session('new_parents')) > 0)
    <div class="mt-6 p-4 border rounded bg-white">
        <h2 class="font-semibold mb-2 text-sm">Akun Orang Tua yang Baru Dibuat</h2>
        <p class="text-xs text-gray-600 mb-2">
            Simpan atau kirimkan password berikut ke orang tua masing-masing. 
            Password ini hanya ditampilkan sekali saat import.
        </p>
        <div class="overflow-x-auto">
            <table class="min-w-full text-xs border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-2 py-1 border">Student</th>
                        <th class="px-2 py-1 border">Nama Ortu</th>
                        <th class="px-2 py-1 border">Email</th>
                        <th class="px-2 py-1 border">WhatsApp</th>
                        <th class="px-2 py-1 border">Invoice</th>
                        <th class="px-2 py-1 border">Password</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (session('new_parents') as $p)
                        <tr>
                            <td class="px-2 py-1 border">{{ $p['student_name'] ?? '-' }}</td>
                            <td class="px-2 py-1 border">{{ $p['name'] ?? '-' }}</td>
                            <td class="px-2 py-1 border">{{ $p['email'] ?? '-' }}</td>
                            <td class="px-2 py-1 border">{{ $p['phone'] ?? '-' }}</td>
                            <td class="px-2 py-1 border">{{ $p['invoice_code'] ?? '-' }}</td>
                            <td class="px-2 py-1 border font-mono">{{ $p['password'] ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
