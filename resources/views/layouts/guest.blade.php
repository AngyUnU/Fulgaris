<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'BeTrack') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('images/betrack-logo.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-[#202124] antialiased bg-[#F8F9FC] min-h-screen flex flex-col justify-center items-center py-10 px-4 selection:bg-[#1573F7] selection:text-white">
        <div class="mb-4">
            <a href="/" class="flex flex-col items-center gap-2">
                <img src="{{ asset('images/betrack-logo.png') }}" alt="BeTrack" class="h-16 w-auto object-contain drop-shadow-sm">
                <span class="text-xs font-semibold uppercase tracking-widest text-[#2F295B]">Gestión de proyectos</span>
            </a>
        </div>

        <div class="w-full sm:max-w-md bg-white border border-slate-200/90 shadow-sm rounded-2xl p-6 sm:p-8">
            {{ $slot }}
        </div>

        <div class="mt-6 text-center text-xs text-slate-400">
            Planifica • Avanza • Logra
        </div>
    </body>
</html>
