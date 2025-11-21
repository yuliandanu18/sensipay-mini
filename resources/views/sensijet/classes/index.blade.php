@extends('sensijet.layout')

@section('title', 'Daftar Kelas - Sensijet')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-semibold">Daftar Kelas</h1>
    <a href="{{ route('sensijet.classes.create') }}" class="inline-flex items-center px-3 py-2 rounded-md bg-sky-600 text-white text-sm font-medium hover:bg-sky-700">
        + Kelas Baru
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="min-w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-200">
        <tr>
            <th class="px-3 py-2 text-left">Nama Kelas</th>
            <th class="px-3 py-2 text-left">Level</th>
            <th class="px-3 py-2 text-left">Program</th>
            <th class="px-3 py-2 text-left">Guru</th>
            <th class="px-3 py-2 text-center">Quota Sesi</th>
            <th class="px-3 py-2 text-center">Aktif</th>
            <th class="px-3 py-2 text-right">Aksi</th>
        </tr>
        </thead>
        <tbody>
        @forelse($classes as $class)
            <tr class="border-b border-slate-100 hover:bg-slate-50">
                <td class="px-3 py-2">
                    <a href="{{ route('sensijet.classes.show', $class) }}" class="text-sky-700 hover:underline">
                        {{ $class->name }}
                    </a>
                </td>
                <td class="px-3 py-2 text-xs text-slate-600">
                    {{ $class->level }}
                </td>
                <td class="px-3 py-2 text-xs text-slate-600">
                    {{ $class->program?->name }}
                </td>
                <td class="px-3 py-2 text-xs text-slate-600">
                    {{ $class->teacher?->name }}
                </td>
                <td class="px-3 py-2 text-center">
                    {{ $class->sessions_quota }}
                </td>
                <td class="px-3 py-2 text-center">
                    {{ $class->students()->wherePivot('status','active')->count() }}
                </td>
                <td class="px-3 py-2 text-right text-xs space-x-1">
                    <a href="{{ route('sensijet.classes.intake', $class) }}"
                       class="inline-block px-2 py-1 rounded-lg bg-blue-600 text-white">
                        Intake Siswa
                    </a>
                    <a href="{{ route('sensijet.classes.edit', $class) }}"
                       class="inline-block px-2 py-1 rounded-lg bg-slate-200 text-slate-800">
                        Edit
                    </a>
                    <form action="{{ route('sensijet.classes.destroy', $class) }}" method="post" class="inline" onsubmit="return confirm('Hapus kelas ini?')">
                        @csrf
                        @method('delete')
                        <button class="inline-block px-2 py-1 rounded-lg bg-red-100 text-red-700">
                            Hapus
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="px-3 py-4 text-center text-sm text-slate-500">
                    Belum ada kelas.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $classes->links() }}
</div>
@endsection
