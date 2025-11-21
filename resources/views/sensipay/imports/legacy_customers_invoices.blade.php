    @extends('layouts.app')

    @section('content')
    <div class="max-w-3xl mx-auto mt-8 space-y-4">
        <h1 class="text-2xl font-bold">Import Customer + Invoice Lama</h1>
        <p class="text-sm text-gray-600">
            Fitur ini akan:
        </p>
        <ul class="list-disc text-sm text-gray-700 ml-5">
            <li>Membuat akun <strong>user</strong> dengan role <code>student_parent</code> berdasarkan email orang tua.</li>
            <li>Membuat invoice lama dan mengaitkan ke siswa dan program yang sudah ada.</li>
        </ul>

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
                <ul class="list-disc ml-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="p-4 border rounded bg-white shadow-sm">
            <p class="text-sm font-semibold mb-2">Format CSV (delimiter ; ) yang disarankan:</p>
            <pre class="bg-gray-100 p-2 rounded text-xs overflow-x-auto">
parent_name;parent_email;student_name;program_name;total_amount;paid_amount;status;invoice_code
Bunda A; bunda.a@example.com; Andi; JET INTENSIF LABSCHOOL; 5000000; 2500000; unpaid; INV-001
Bunda B; bunda.b@example.com; Sinta; JET REGULER 7 SMP; 3000000; 3000000; paid; INV-002
            </pre>
        </div>

        <form action="{{ route('sensipay.legacy-import.process') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label class="block font-medium mb-1">File CSV</label>
                <input type="file" name="file" class="border rounded w-full p-2">
            </div>

            <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Import Sekarang
            </button>
        </form>
    </div>
    @endsection
