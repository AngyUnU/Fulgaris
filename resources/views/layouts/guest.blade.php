<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ArkanA') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('images/arkana-logo.png') }}">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-[#202124] antialiased bg-[#F8F9FC] min-h-screen flex flex-col justify-center items-center py-10 px-4 selection:bg-[#1573F7] selection:text-white">
        <div class="mb-4">
            <a href="/" class="flex flex-col items-center gap-1.5">
                <img src="{{ asset('images/arkana-logo.png') }}" alt="ArkanA" class="h-14 w-auto object-contain rounded-xl shadow-xs">
                <span class="text-base font-extrabold text-[#2F295B] tracking-tight">Arkan<span class="text-[#1573F7]">A</span></span>
                <span class="text-[10px] text-slate-400">Agenda • Acuerdos • Análisis</span>
            </a>
        </div>

        <div class="w-full sm:max-w-md bg-white border border-slate-200 rounded-lg p-6 sm:p-8">
            {{ $slot }}
        </div>

        <div class="mt-6 text-center text-xs text-slate-400">
            Agenda • Acuerdos • Análisis
        </div>
    </body>
</html>
