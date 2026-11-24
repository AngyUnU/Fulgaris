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
    <body class="font-sans antialiased bg-[#F8F9FC] text-[#202124] min-h-screen flex flex-col selection:bg-[#1573F7] selection:text-white">
        <div class="flex-1 flex flex-col">
            @include('layouts.navigation')

            <!-- Flash Status Messages -->
            @if (session('status'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 w-full">
                    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium flex items-center justify-between shadow-xs">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ session('status') }}</span>
                        </div>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 w-full">
                    <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm font-medium shadow-xs">
                        <p class="font-bold mb-1">Por favor corrige los siguientes errores:</p>
                        <ul class="list-disc list-inside text-xs space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white border-b border-slate-200/80">
                    <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="flex-1">
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-slate-200/80 py-4 mt-12 text-center text-xs text-slate-500">
                <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-[#2F295B]">ArkanA</span>
                        <span>•</span>
                        <span>Gestión de proyectos</span>
                    </div>
                    <div class="text-slate-400">
                        Agenda • Acuerdos • Análisis
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
