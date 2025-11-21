@extends('sensijet.layout')

@section('title', 'Buat Kelas - Sensijet')

@section('content')
<h1 class="text-xl font-semibold mb-4">Buat Kelas Baru</h1>

<form method="post" action="{{ route('sensijet.classes.store') }}" class="bg-white rounded-xl shadow-sm p-4 space-y-4">
    @csrf

    <div>
        <label class="block text-sm font-medium mb-1">Nama Kelas</label>
        <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-md border-slate-300 text-sm">
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Level</label>
            <input type="text" name="level" value="{{ old('level') }}" placeholder="SD6 / SMP9 / dll" class="w-full rounded-md border-slate-300 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Quota Sesi</label>
            <input type="number" name="sessions_quota" value="{{ old('sessions_quota', 40) }}" min="1" class="w-full rounded-md border-slate-300 text-sm">
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
        <div>
            <label class="block text-sm font-medium mb-1">Guru</label>
            <select name="teacher_id" class="w-full rounded-md border-slate-300 text-sm">
                <option value="">-- Pilih Guru --</option>
                @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}" @selected(old('teacher_id') == $teacher->id)>
                        {{ $teacher->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Mulai</label>
            <input type="date" name="start_date" value="{{ old('start_date') }}" class="w-full rounded-md border-slate-300 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Selesai</label>
            <input type="date" name="end_date" value="{{ old('end_date') }}" class="w-full rounded-md border-slate-300 text-sm">
        </div>
    </div>

    <div class="flex items-center gap-2">
        <input type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', true)) class="rounded border-slate-300">
        <label for="is_active" class="text-sm">Kelas aktif</label>
    </div>

    <div class="flex justify-end gap-2">
        <a href="{{ route('sensijet.classes.index') }}" class="px-3 py-2 rounded-md border text-sm">Batal</a>
        <button class="px-3 py-2 rounded-md bg-sky-600 text-white text-sm">Simpan</button>
    </div>
</form>
@endsection
