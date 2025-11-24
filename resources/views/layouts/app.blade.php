<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
            @php
        $user = auth()->user();
        $isParent = $user && $user->role === 'parent';
        $waNumber = config('services.jet.admin_wa');
        $waText   = urlencode(config('services.jet.admin_wa_text'));
    @endphp

    <!-- @if($isParent && $waNumber)
        <a href="https://wa.me/{{ $waNumber }}?text={{ $waText }}"
           class="fixed bottom-4 right-4 z-40 inline-flex items-center justify-center rounded-full bg-emerald-600 px-4 py-3 text-white shadow-lg shadow-emerald-400/40 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
            <span class="mr-2 text-lg">💬</span>
            <span class="text-xs font-semibold hidden sm:inline">
                Hubungi Admin
            </span>
        </a>
    @endif -->
</body>
</html>

    </body>
</html>