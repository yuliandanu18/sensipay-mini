@extends('sensipay.layout')

@section('title', 'Edit Invoice - Sensipay')

@section('content')
<h1 class="text-xl font-semibold mb-4">Edit Invoice {{ $invoice->invoice_code }}</h1>

<form method="post" action="{{ route('sensipay.invoices.update', $invoice) }}" class="bg-white rounded-xl shadow-sm p-4 space-y-4">
    @csrf
    @method('put')

    <div class="text-xs text-slate-500 mb-2">
        Dibuat: {{ $invoice->created_at?->format('d/m/Y H:i') }}
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Siswa</label>
        <select name="student_id" class="w-full rounded-md border-slate-300 text-sm">
            @foreach($students as $student)
                <option value="{{ $student->id }}" @selected(old('student_id', $invoice->student_id) == $student->id)>
                    {{ $student->name }} - {{ $student->grade }} ({{ $student->school }})
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Program</label>
        <select name="program_id" class="w-full rounded-md border-slate-300 text-sm">
            <option value="">-- Tidak ada / Custom --</option>
            @foreach($programs as $program)
                <option value="{{ $program->id }}" @selected(old('program_id', $invoice->program_id) == $program->id)>
                    {{ $program->name }} ({{ $program->level }})
                </option>
            @endforeach
        </select>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Total Tagihan (Rp)</label>
            <input type="number" name="total_amount" value="{{ old('total_amount', $invoice->total_amount) }}" class="w-full rounded-md border-slate-300 text-sm" min="0">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Terbayar (Rp)</label>
            <input type="number" value="{{ $invoice->paid_amount }}" disabled class="w-full rounded-md border-slate-300 text-sm bg-slate-50">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Status</label>
            <select name="status" class="w-full rounded-md border-slate-300 text-sm">
                @foreach(['unpaid' => 'Belum Lunas', 'partial' => 'Cicilan', 'paid' => 'Lunas'] as $val => $label)
                    <option value="{{ $val }}" @selected(old('status', $invoice->status) == $val)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Jatuh Tempo</label>
        <input type="date" name="due_date" value="{{ old('due_date', optional($invoice->due_date)->format('Y-m-d')) }}" class="w-full rounded-md border-slate-300 text-sm">
    </div>

    <div class="flex justify-between items-center">
        <button formaction="{{ route('sensipay.invoices.destroy', $invoice) }}" formmethod="post" onclick="return confirm('Hapus invoice ini?')" class="px-3 py-2 rounded-md border border-red-200 text-red-700 text-xs">
            @csrf
            @method('delete')
            Hapus
        </button>

        <div class="flex gap-2">
            <a href="{{ route('sensipay.invoices.show', $invoice) }}" class="px-3 py-2 rounded-md border text-sm">Batal</a>
            <button class="px-3 py-2 rounded-md bg-sky-600 text-white text-sm">Simpan</button>
        </div>
    </div>
</form>
@endsection
