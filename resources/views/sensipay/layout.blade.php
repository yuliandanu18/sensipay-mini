<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Sensipay Mini')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-100 text-slate-900">

@php
    $user = auth()->user();
    $role = $user->role ?? null;

    // Menu untuk orang tua
    $parentMenu = [
        ['label' => 'Ringkasan Tagihan', 'route' => 'sensipay.parent.dashboard'],
    ];

    // Menu admin/direksi
    $adminMenu = [
        ['label' => 'Dashboard Invoice', 'route' => 'sensipay.invoices.index'],
        ['label' => 'Parent & OTM', 'route' => 'sensipay.parents.index'],
        ['label' => 'Import Invoice', 'route' => 'sensipay.invoices.import.form'],
        ['label' => 'Import Legacy', 'route' => 'sensipay.legacy-installments.import.form'],
        ['label' => 'Reminders', 'route' => 'sensipay.reminders.index'],
    ];

    $menuItems = $role === 'parent' ? $parentMenu : $adminMenu;
@endphp

<div class="min-h-screen flex">

    {{-- SIDEBAR --}}
    <aside class="hidden md:flex md:flex-col w-60 bg-slate-900 text-slate-100">
        <div class="h-16 flex items-center px-4 border-b border-slate-800">
            <div class="flex flex-col leading-tight">
                <span class="font-bold text-lg">Sensipay Mini</span>
                <span class="text-xs text-slate-400">Bimbel JET</span>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto py-3">
            @foreach ($menuItems as $item)
                @php
                    $isActive = request()->routeIs($item['route']);
                @endphp

                <a href="{{ route($item['route']) }}"
                   class="block px-4 py-2 text-sm
                          {{ $isActive ? 'bg-slate-700 text-white font-semibold' : 'text-slate-200 hover:bg-slate-800' }}">
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="border-t border-slate-800 p-4 text-xs text-slate-400">
            <div class="font-semibold text-slate-200">
                {{ $user->name ?? 'Guest' }}
            </div>
            <div class="text-[10px] uppercase tracking-wide">
                {{ $role ?? '-' }}
            </div>

            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf
                <button class="text-xs text-red-300 hover:text-red-200" type="submit">
                    Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- CONTENT AREA --}}
    <div class="flex-1 flex flex-col">

        {{-- TOP NAVBAR --}}
        <header class="h-14 bg-white border-b border-slate-200 flex items-center justify-between px-4">
            <div class="flex items-center gap-2">
                <span class="md:hidden text-slate-500 text-lg">☰</span>
                <div class="text-sm uppercase tracking-wide text-slate-500">
                    @yield('page_title', 'Dashboard')
                </div>
            </div>

            <div class="text-xs text-slate-500">
                @if ($role === 'parent')
                    Login sebagai <span class="font-semibold text-slate-700">Orang Tua</span>
                @else
                    Login sebagai <span class="font-semibold text-slate-700">{{ $role ?? 'User' }}</span>
                @endif
            </div>
        </header>

        {{-- MAIN CONTENT --}}
        <main class="flex-1 p-4 md:p-6">
            @yield('content')
        </main>

        <footer class="border-t border-slate-200 text-center text-[11px] text-slate-400 py-2">
            Sensipay Mini · Bimbel JET
        </footer>
    </div>
</div>

</body>
</html>
