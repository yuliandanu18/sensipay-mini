@extends('sensijet.layout')

@section('title', 'Edit Sesi KBM - Sensijet')

@section('content')
<h1 class="text-xl font-semibold mb-4">Edit Sesi KBM {{ $classRoom->name }}</h1>

<form method="post" action="{{ route('sensijet.sessions.update', $session) }}" class="bg-white rounded-xl shadow-sm p-4 space-y-4">
    @csrf
    @method('put')

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Tanggal</label>
            <input type="date" name="date" value="{{ old('date', optional($session->date)->format('Y-m-d')) }}" class="w-full rounded-md border-slate-300 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Mulai</label>
            <input type="time" name="start_time" value="{{ old('start_time', $session->start_time) }}" class="w-full rounded-md border-slate-300 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Selesai</label>
            <input type="time" name="end_time" value="{{ old('end_time', $session->end_time) }}" class="w-full rounded-md border-slate-300 text-sm">
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Tipe Sesi</label>
            <select name="type" class="w-full rounded-md border-slate-300 text-sm">
                @foreach(['regular' => 'Reguler', 'private' => 'Private', 'makeup' => 'Pengganti', 'bonus' => 'Bonus'] as $val => $label)
                    <option value="{{ $val }}" @selected(old('type', $session->type) == $val)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Durasi (menit)</label>
            <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $session->duration_minutes) }}" class="w-full rounded-md border-slate-300 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Siswa (jika private)</label>
            <select name="student_id" class="w-full rounded-md border-slate-300 text-sm">
                <option value="">-- Pilih Siswa --</option>
                @foreach($students as $student)
                    <option value="{{ $student->id }}" @selected(old('student_id', $session->student_id) == $student->id)>
                        {{ $student->name }} ({{ $student->grade }} - {{ $student->school }})
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="flex items-center gap-2">
            <input type="checkbox" id="is_counted_in_quota" name="is_counted_in_quota" value="1" @checked(old('is_counted_in_quota', $session->is_counted_in_quota)) class="rounded border-slate-300">
            <label for="is_counted_in_quota" class="text-sm">Hitung ke quota sesi</label>
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" id="is_chargeable" name="is_chargeable" value="1" @checked(old('is_chargeable', $session->is_chargeable)) class="rounded border-slate-300">
            <label for="is_chargeable" class="text-sm">Akan dijadikan tagihan extra</label>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Topik / Materi</label>
        <input type="text" name="topic" value="{{ old('topic', $session->topic) }}" class="w-full rounded-md border-slate-300 text-sm">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Catatan</label>
        <textarea name="note" rows="3" class="w-full rounded-md border-slate-300 text-sm">{{ old('note', $session->note) }}</textarea>
    </div>

    <div class="flex justify-between items-center">
        <form action="{{ route('sensijet.sessions.destroy', $session) }}" method="post" onsubmit="return confirm('Hapus sesi ini?')">
            @csrf
            @method('delete')
            <button class="px-3 py-2 rounded-md border border-red-200 text-red-700 text-xs">Hapus Sesi</button>
        </form>

        <div class="flex gap-2">
            <a href="{{ route('sensijet.classes.show', $classRoom) }}" class="px-3 py-2 rounded-md border text-sm">Batal</a>
            <button class="px-3 py-2 rounded-md bg-emerald-600 text-white text-sm">Simpan</button>
        </div>
    </div>
</form>
@endsection
