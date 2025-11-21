
@extends('sensipay.layout')

@section('title', 'Manajemen Orang Tua & Siswa')

@section('content')
<div class="mb-4">
    <h1 class="text-xl font-semibold">Manajemen Orang Tua & Siswa</h1>
    <p class="text-xs text-slate-500">
        Halaman ini digunakan admin JET untuk menghubungkan akun orang tua (user role <code>parent</code>) dengan data siswa.
    </p>
</div>

<div class="mb-3">
    <form method="get" class="flex items-center gap-2 text-xs">
        <input type="text"
               name="q"
               value="{{ request('q') }}"
               placeholder="Cari nama siswa / sekolah..."
               class="flex-1 border rounded-lg px-2 py-1 text-xs">
        <button class="px-3 py-1 rounded-lg bg-sky-600 text-white text-xs">
            Cari
        </button>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden text-xs">
    <table class="min-w-full">
        <thead class="bg-slate-100 border-b border-slate-200">
            <tr>
                <th class="px-3 py-2 text-left">Siswa</th>
                <th class="px-3 py-2 text-left">Sekolah</th>
                <th class="px-3 py-2 text-left">Orang Tua (User)</th>
                <th class="px-3 py-2 text-left">Email</th>
                <th class="px-3 py-2 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $student)
                <tr class="border-b border-slate-100 hover:bg-slate-50">
                    <td class="px-3 py-2">
                        {{ $student->name }}
                    </td>
                    <td class="px-3 py-2">
                        {{ $student->school_name ?? '-' }}
                    </td>
                    <td class="px-3 py-2">
                        {{ $student->parentUser?->name ?? '-' }}
                    </td>
                    <td class="px-3 py-2">
                        {{ $student->parentUser?->email ?? '-' }}
                    </td>
                    <td class="px-3 py-2 text-right">
                        <a href="{{ route('sensipay.parents.edit', $student) }}"
                           class="inline-flex items-center px-2 py-1 rounded-lg bg-sky-600 text-white text-[11px]">
                            Atur Orang Tua
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-3 py-4 text-center text-slate-500">
                        Belum ada data siswa.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3">
    {{ $students->links() }}
</div>
@endsection
