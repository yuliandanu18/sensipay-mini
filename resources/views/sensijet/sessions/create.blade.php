@extends('sensijet.layout')

@section('title', 'Tambah Sesi KBM - ' . $classRoom->name)

@section('content')
<div class="max-w-xl">
    <h1 class="text-xl font-semibold mb-2">
        Tambah Sesi KBM
    </h1>
    <p class="text-xs text-slate-500 mb-4">
        Kelas: {{ $classRoom->name }}<br>
        Program: {{ $classRoom->program->name ?? '-' }}<br>
        Guru: {{ $teacher->name ?? ($classRoom->teacher->name ?? '-') }}
    </p>

    @if($errors->any())
        <div class="mb-3 text-xs bg-rose-100 text-rose-700 px-3 py-2 rounded">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('sensijet.classes.sessions.store', $classRoom) }}" method="POST" class="space-y-3 text-sm">
        @csrf

        <div>
            <label class="block text-xs text-slate-600 mb-1">Tanggal</label>
            <input type="date" name="date" value="{{ old('date', now()->toDateString()) }}"
                   class="w-full border rounded-lg px-3 py-2 text-sm">
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs text-slate-600 mb-1">Mulai</label>
                <input type="time" name="start_time" value="{{ old('start_time') }}"
                       class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs text-slate-600 mb-1">Selesai</label>
                <input type="time" name="end_time" value="{{ old('end_time') }}"
                       class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
        </div>

        <div>
            <label class="block text-xs text-slate-600 mb-1">Topik / Materi</label>
            <input type="text" name="topic" value="{{ old('topic') }}"
                   placeholder="Misal: FPB & KPK, Latihan Numerasi"
                   class="w-full border rounded-lg px-3 py-2 text-sm">
        </div>

        {{-- Optional: pilih guru kalau mau override --}}
        @if($teacher)
            <input type="hidden" name="teacher_id" value="{{ $teacher->id }}">
        @endif

        <div class="pt-2 flex items-center justify-between">
            <a href="{{ route('sensijet.classes.show', $classRoom) }}"
               class="text-xs text-slate-500 hover:underline">
                ← Kembali ke detail kelas
            </a>
            <button class="px-4 py-2 rounded-lg bg-sky-600 text-white text-xs font-medium hover:bg-sky-700">
                Simpan Sesi
            </button>
        </div>
    </form>

    {{-- Info siswa aktif (optional, cuma info visual) --}}
    @if($students->count())
        <div class="mt-6">
            <h2 class="text-xs font-semibold text-slate-600 mb-1">Siswa aktif di kelas ini</h2>
            <ul class="text-xs text-slate-600 list-disc list-inside">
                @foreach($students as $s)
                    <li>{{ $s->name }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
@endsection
