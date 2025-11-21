@extends('sensijet.layout')

@section('title', 'Detail Gaji Guru - Sensijet')

@section('content')
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
    <div>
        <h1 class="text-xl font-semibold">Detail Gaji Guru</h1>
        <p class="text-xs text-slate-500">
            Guru: <span class="font-semibold">{{ $teacher->name }}</span><br>
            Periode: {{ $start->format('d/m/Y') }} - {{ $end->format('d/m/Y') }}
        </p>
    </div>
    <a href="{{ route('sensijet.payroll.index', ['month' => $month]) }}" class="px-3 py-2 rounded-md border text-xs">
        &larr; Kembali ke Rekap
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-4 text-xs">
    <div class="bg-white rounded-xl shadow-sm p-3">
        <div class="text-slate-500">Total Sesi</div>
        <div class="text-lg font-semibold">{{ $summary['total_sessions'] }}</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-3">
        <div class="text-slate-500">Reguler / Private / Lain</div>
        <div class="text-sm font-semibold">
            {{ $summary['regular_sessions'] }} / {{ $summary['private_sessions'] }} / {{ $summary['other_sessions'] }}
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-3">
        <div class="text-slate-500">Total Menit</div>
        <div class="text-lg font-semibold">{{ $summary['total_minutes'] }}</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-3">
        <div class="text-slate-500">Total Fee</div>
        <div class="text-lg font-semibold">Rp {{ number_format($summary['total_fee'], 0, ',', '.') }}</div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="min-w-full text-xs">
        <thead class="bg-slate-50 border-b border-slate-200">
        <tr>
            <th class="px-3 py-2 text-left">Tanggal</th>
            <th class="px-3 py-2 text-left">Waktu</th>
            <th class="px-3 py-2 text-left">Kelas</th>
            <th class="px-3 py-2 text-left">Tipe</th>
            <th class="px-3 py-2 text-center">Durasi (menit)</th>
            <th class="px-3 py-2 text-right">Fee</th>
            <th class="px-3 py-2 text-left">Topik</th>
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
                <td class="px-3 py-2 text-center">
                    {{ $session->duration_minutes }}
                </td>
                <td class="px-3 py-2 text-right">
                    Rp {{ number_format($session->teacher_fee, 0, ',', '.') }}
                </td>
                <td class="px-3 py-2">
                    {{ $session->topic }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="px-3 py-4 text-center text-slate-500">
                    Belum ada sesi untuk guru ini pada periode ini.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
