@extends('sensijet.layout')

@section('title', 'Jadwal Saya - Sensijet')

@section('content')
<h1 class="text-xl font-semibold mb-4">Jadwal Mengajar Saya</h1>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="min-w-full text-xs">
        <thead class="bg-slate-50 border-b border-slate-200">
        <tr>
            <th class="px-3 py-2 text-left">Tanggal</th>
            <th class="px-3 py-2 text-left">Waktu</th>
            <th class="px-3 py-2 text-left">Kelas</th>
            <th class="px-3 py-2 text-left">Tipe</th>
            <th class="px-3 py-2 text-left">Siswa (jika private)</th>
            <th class="px-3 py-2 text-left">Topik</th>
            <th class="px-3 py-2 text-right">Absensi</th>
        </tr>
        </thead>
        <tbody>
        @forelse($sessions as $session)
            <tr class="border-b border-slate-100">
                <td class="px-3 py-2">
                    {{ $session->date?->format('d/m/Y') }}
                </td>
                <td class="px-3 py-2">
                    {{ $session->start_time }} - {{ $session->end_time }}
                </td>
                <td class="px-3 py-2">
                    {{ $session->classRoom?->name }}
                </td>
                <td class="px-3 py-2">
                    {{ strtoupper($session->type) }}
                </td>
                <td class="px-3 py-2">
                    {{ $session->student?->name }}
                </td>
                <td class="px-3 py-2">
                    {{ $session->topic }}
                </td>
                <td class="px-3 py-2 text-right">
                    <form action="{{ route('sensijet.sessions.attendance.store', $session) }}" method="post" class="inline-flex items-center gap-2">
                        @csrf
                        <select name="student_id" class="rounded-md border-slate-300 text-xs">
                            <option value="">Siswa</option>
                            @php
                                $students = \App\Models\Student::orderBy('name')->get();
                            @endphp
                            @foreach($students as $student)
                                <option value="{{ $student->id }}">{{ $student->name }}</option>
                            @endforeach
                        </select>
                        <select name="status" class="rounded-md border-slate-300 text-xs">
                            <option value="present">Hadir</option>
                            <option value="absent">Alpa</option>
                            <option value="sick">Sakit</option>
                            <option value="leave">Izin</option>
                        </select>
                        <input type="text" name="note" placeholder="Catatan" class="rounded-md border-slate-300 text-xs">
                        <button class="px-2 py-1 rounded-md bg-emerald-600 text-white text-xs">Simpan</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="px-3 py-4 text-center text-slate-500">
                    Jadwal mengajar belum ada.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $sessions->links() }}
</div>
@endsection
