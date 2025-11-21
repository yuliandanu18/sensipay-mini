@extends('sensijet.layout')

@section('title', 'Rekap Gaji Guru - Sensijet')

@section('content')
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
    <div>
        <h1 class="text-xl font-semibold">Rekap Gaji Guru</h1>
        <p class="text-xs text-slate-500">
            Periode: {{ $start->format('d/m/Y') }} - {{ $end->format('d/m/Y') }}
        </p>
    </div>
    <form method="get" class="flex items-center gap-2 text-xs">
        <label for="month" class="text-slate-600">Bulan</label>
        <input type="month" id="month" name="month" value="{{ $month }}" class="rounded-md border-slate-300 text-xs">
        <button class="px-3 py-2 rounded-md bg-sky-600 text-white">Terapkan</button>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="min-w-full text-xs">
        <thead class="bg-slate-50 border-b border-slate-200">
        <tr>
            <th class="px-3 py-2 text-left">Guru</th>
            <th class="px-3 py-2 text-center">Sesi Reguler</th>
            <th class="px-3 py-2 text-center">Sesi Private</th>
            <th class="px-3 py-2 text-center">Sesi Lain</th>
            <th class="px-3 py-2 text-center">Total Sesi</th>
            <th class="px-3 py-2 text-center">Total Menit</th>
            <th class="px-3 py-2 text-right">Total Fee</th>
            <th class="px-3 py-2 text-right"></th>
        </tr>
        </thead>
        <tbody>
        @forelse($summaries as $summary)
            <tr class="border-b border-slate-100 hover:bg-slate-50">
                <td class="px-3 py-2">
                    {{ $summary['teacher']->name }}
                </td>
                <td class="px-3 py-2 text-center">
                    {{ $summary['regular_sessions'] }}
                </td>
                <td class="px-3 py-2 text-center">
                    {{ $summary['private_sessions'] }}
                </td>
                <td class="px-3 py-2 text-center">
                    {{ $summary['other_sessions'] }}
                </td>
                <td class="px-3 py-2 text-center font-semibold">
                    {{ $summary['total_sessions'] }}
                </td>
                <td class="px-3 py-2 text-center">
                    {{ $summary['total_minutes'] }}
                </td>
                <td class="px-3 py-2 text-right font-semibold">
                    Rp {{ number_format($summary['total_fee'], 0, ',', '.') }}
                </td>
                <td class="px-3 py-2 text-right">
                    <a href="{{ route('sensijet.payroll.show', ['teacher' => $summary['teacher']->id, 'month' => $month]) }}" class="text-sky-700 hover:underline">
                        Detail
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="px-3 py-4 text-center text-slate-500">
                    Belum ada sesi KBM pada periode ini.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
