@extends('sensijet.layout')

@section('title', 'Detail Kelas - Sensijet')

@section('content')
<div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4 mb-4">
    <div>
        <h1 class="text-xl font-semibold mb-1">{{ $classRoom->name }}</h1>
        <div class="text-xs text-slate-500 space-y-1">
            <div>Level: {{ $classRoom->level }}</div>
            <div>Program: {{ $classRoom->program?->name }}</div>
            <div>Guru: {{ $classRoom->teacher?->name }}</div>
            <div>Periode: {{ $classRoom->start_date?->format('d/m/Y') }} - {{ $classRoom->end_date?->format('d/m/Y') }}</div>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('sensijet.classes.edit', $classRoom) }}" class="px-3 py-2 rounded-md border text-xs">Edit</a>
        <a href="{{ route('sensijet.classes.index') }}" class="px-3 py-2 rounded-md border text-xs">Kembali</a>
    </div>
</div>

<div class="mb-4">
    <h2 class="text-sm font-semibold mb-2">Ringkasan Sesi</h2>
    <div class="bg-white rounded-xl shadow-sm p-4 text-sm grid grid-cols-2 md:grid-cols-4 gap-4">
        <div>
            <div class="text-xs text-slate-500">Quota Sesi</div>
            <div class="font-semibold">{{ $classRoom->sessions_quota }}</div>
        </div>
        <div>
            <div class="text-xs text-slate-500">Total Sesi Terjadwal</div>
            <div class="font-semibold">{{ $classRoom->sessions->count() }}</div>
        </div>
        <div>
            <div class="text-xs text-slate-500">Status</div>
            <div class="font-semibold">
                @if($classRoom->is_active)
                    Aktif
                @else
                    Nonaktif
                @endif
            </div>
        </div>
    </div>
</div>

<div class="flex items-center justify-between mb-2">
    <h2 class="text-sm font-semibold">Jadwal & Sesi KBM</h2>
    <a href="{{ route('sensijet.classes.sessions.create', $classRoom) }}" class="px-3 py-2 rounded-md bg-emerald-600 text-white text-xs">+ Tambah Sesi</a>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="min-w-full text-xs">
        <thead class="bg-slate-50 border-b border-slate-200">
        <tr>
            <th class="px-3 py-2 text-left">Tanggal</th>
            <th class="px-3 py-2 text-left">Waktu</th>
            <th class="px-3 py-2 text-left">Tipe</th>
            <th class="px-3 py-2 text-left">Siswa (jika private)</th>
            <th class="px-3 py-2 text-center">Durasi</th>
            <th class="px-3 py-2 text-center">Hitung Quota</th>
            <th class="px-3 py-2 text-center">Tagih</th>
            <th class="px-3 py-2 text-right">Aksi</th>
        </tr>
        </thead>
        <tbody>
        @forelse($classRoom->sessions as $session)
            <tr class="border-b border-slate-100">
                <td class="px-3 py-2">
                    {{ $session->date?->format('d/m/Y') }}
                </td>
                <td class="px-3 py-2">
                    {{ $session->start_time }} - {{ $session->end_time }}
                </td>
                <td class="px-3 py-2">
                    {{ strtoupper($session->type) }}
                </td>
                <td class="px-3 py-2">
                    {{ $session->student?->name }}
                </td>
                <td class="px-3 py-2 text-center">
                    {{ $session->duration_minutes }} menit
                </td>
                <td class="px-3 py-2 text-center">
                    @if($session->is_counted_in_quota)
                        ✔
                    @else
                        -
                    @endif
                </td>
                <td class="px-3 py-2 text-center">
                    @if($session->is_chargeable)
                        ✔
                    @else
                        -
                    @endif
                </td>
                <td class="px-3 py-2 text-right">
                    <a href="{{ route('sensijet.sessions.edit', $session) }}" class="text-sky-700 hover:underline mr-2">Edit</a>
                    <form action="{{ route('sensijet.sessions.destroy', $session) }}" method="post" class="inline" onsubmit="return confirm('Hapus sesi ini?')">
                        @csrf
                        @method('delete')
                        <button class="text-red-600 hover:underline">Hapus</button>
                            {{-- Absen / Kehadiran guru & siswa --}}
                            <form action="{{ route('sensijet.sessions.attendance.store', $session) }}"
                                  method="post" class="inline">
                                @csrf
                                <button class="inline-block px-2 py-1 rounded bg-emerald-100 text-emerald-700">
                                    Absen
                                </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="px-3 py-4 text-center text-slate-500">
                    Belum ada sesi KBM untuk kelas ini.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
