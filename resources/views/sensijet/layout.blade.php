<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Sensijet Mini')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-800">
<div class="min-h-screen">
    <nav class="bg-white border-b border-slate-200">
        <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="font-semibold text-slate-800">
                Sensijet Mini
            </div>
            <div class="flex gap-4 text-sm">
                <a href="{{ route('sensijet.classes.index') }}" class="text-slate-600 hover:text-slate-900">Kelas</a>
                @if(auth()->user()?->role === 'teacher')
                    <a href="{{ route('sensijet.sessions.my') }}" class="text-slate-600 hover:text-slate-900">Jadwal Saya</a>
                @endif
                @php
                    $role = auth()->user()->role ?? null;
                @endphp
                @if(in_array($role, ['owner', 'operational_director', 'academic_director']))
                    <a href="{{ route('sensijet.payroll.teachers.index') }}" class="text-slate-600 hover:text-slate-900">Payroll Guru</a>
                @endif
            </div>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-4 py-6">
        @if(session('success'))
            <div class="mb-4 rounded-md bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</div>
</body>
</html>
