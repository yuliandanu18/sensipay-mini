@extends('sensipay.layout')

@section('title', 'Buat Invoice - Sensipay')

@section('content')
<h1 class="text-xl font-semibold mb-4">Buat Invoice Baru</h1>

<form method="post" action="{{ route('sensipay.invoices.store') }}" class="bg-white rounded-xl shadow-sm p-4 space-y-4">
    @csrf
    <div>
        <label class="block text-sm font-medium mb-1">Siswa</label>
        <select name="student_id" class="w-full rounded-md border-slate-300 text-sm">
            <option value="">-- Pilih Siswa --</option>
            @foreach($students as $student)
                <option value="{{ $student->id }}" @selected(old('student_id') == $student->id)>
                    {{ $student->name }} - {{ $student->grade }} ({{ $student->school }})
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Program</label>
        <select name="program_id" class="w-full rounded-md border-slate-300 text-sm">
            <option value="">-- Pilih Program --</option>
            @foreach($programs as $program)
                <option value="{{ $program->id }}" @selected(old('program_id') == $program->id)>
                    {{ $program->name }} ({{ $program->level }})
                </option>
            @endforeach
        </select>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Total Tagihan (Rp)</label>
            <input type="number" name="total_amount" value="{{ old('total_amount') }}" class="w-full rounded-md border-slate-300 text-sm" min="0">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Jatuh Tempo</label>
            <input type="date" name="due_date" value="{{ old('due_date') }}" class="w-full rounded-md border-slate-300 text-sm">
        </div>
    </div>

    <div class="flex justify-end gap-2">
        <a href="{{ route('sensipay.invoices.index') }}" class="px-3 py-2 rounded-md border text-sm">Batal</a>
        <button class="px-3 py-2 rounded-md bg-sky-600 text-white text-sm">Simpan</button>
    </div>
</form>
@endsection
