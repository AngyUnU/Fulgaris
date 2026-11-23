<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>BeTrack - Gestión de Proyectos</title>
        <link rel="icon" type="image/png" href="{{ asset('images/betrack-logo.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-[#F8F9FC] text-[#202124] min-h-screen flex flex-col font-sans selection:bg-[#1573F7] selection:text-white">
        <!-- Navigation Bar -->
        <header class="border-b border-slate-200/80 bg-white/95 backdrop-blur-md sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/betrack-logo.png') }}" alt="BeTrack" class="h-11 w-auto object-contain">
                    <div class="flex flex-col">
                        <span class="text-2xl font-black tracking-tight text-[#2F295B] leading-none">Be<span class="text-[#1573F7]">Track</span></span>
                        <span class="text-[9px] font-semibold tracking-wider text-slate-500 uppercase mt-0.5">Planifica • Avanza • Logra</span>
                    </div>
                </div>

                <nav class="flex items-center gap-3">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-5 py-2.5 rounded-lg bg-[#1573F7] hover:bg-[#0d62d9] font-bold text-white shadow-xs transition text-xs">
                                Ir al Panel Principal
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-xs font-semibold text-slate-700 hover:text-[#1573F7] transition px-4 py-2">
                                Iniciar Sesión
                            </a>

                        @endauth
                    @endif
                </nav>
            </div>
        </header>

        <!-- Hero Section -->
        <main class="flex-1 flex flex-col justify-center">
            <div class="py-16 sm:py-20">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    
                    <div class="inline-flex items-center px-3.5 py-1.5 rounded-full bg-blue-50 border border-blue-200/80 text-[#1573F7] text-xs font-semibold mb-6">
                        Organización y seguimiento de proyectos
                    </div>

                    <h1 class="text-4xl sm:text-6xl font-black text-[#2F295B] tracking-tight max-w-4xl mx-auto leading-tight">
                        El trabajo de tu equipo, más fácil de seguir con <span class="text-[#1573F7]">BeTrack</span>
                    </h1>

                    <p class="mt-6 text-base sm:text-lg text-slate-600 max-w-2xl mx-auto leading-relaxed">
                        Organiza proyectos y actividades, asigna responsables y consulta los avances desde la web o la aplicación móvil.
                    </p>

                    <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="px-7 py-3 rounded-xl bg-[#1573F7] hover:bg-[#0d62d9] font-bold text-white text-sm shadow-md shadow-blue-500/20 transition">
                                Acceder al Panel
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="px-7 py-3 rounded-xl bg-[#1573F7] hover:bg-[#0d62d9] font-bold text-white text-sm shadow-md shadow-blue-500/20 transition">
                                Iniciar Sesión en BeTrack
                            </a>
                        @endauth
                    </div>

                    <!-- BeTrack features -->
                    <div class="mt-16 max-w-4xl mx-auto bg-white rounded-2xl border border-slate-200/90 p-8 shadow-xs text-left">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="text-xs font-bold uppercase tracking-wider text-[#1573F7]">Qué puedes hacer con BeTrack</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Col 1 -->
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                                <div class="w-8 h-8 rounded-lg bg-blue-100 text-[#1573F7] flex items-center justify-center font-bold text-xs mb-3">
                                    WEB
                                </div>
                                <h4 class="font-bold text-sm text-[#2F295B] mb-1">Planifica proyectos</h4>
                                <p class="text-xs text-slate-500 leading-relaxed">
                                    Define responsables y fechas, organiza actividades y deja por escrito los acuerdos del equipo.
                                </p>
                            </div>

                            <!-- Col 2 -->
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                                <div class="w-8 h-8 rounded-lg bg-purple-100 text-[#5F3AD7] flex items-center justify-center font-bold text-xs mb-3">
                                    APP
                                </div>
                                <h4 class="font-bold text-sm text-[#2F295B] mb-1">Actualiza desde el móvil</h4>
                                <p class="text-xs text-slate-500 leading-relaxed">
                                    Consulta tus actividades asignadas, informa cuánto has avanzado y comparte notas de trabajo.
                                </p>
                            </div>

                            <!-- Col 3 -->
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs mb-3">
                                    API
                                </div>
                                <h4 class="font-bold text-sm text-[#2F295B] mb-1">Da seguimiento</h4>
                                <p class="text-xs text-slate-500 leading-relaxed">
                                    Revisa el estado de las actividades, las fechas próximas y los avances reportados por el equipo.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Roles Grid -->
                    <div class="mt-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-left max-w-5xl mx-auto">
                        <div class="p-4 rounded-xl bg-white border border-slate-200/90 shadow-xs">
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-purple-50 text-purple-700 border border-purple-200">
                                ADMINISTRADOR
                            </span>
                            <h4 class="font-bold text-sm text-[#2F295B] mt-2 mb-1">Control Global</h4>
                            <p class="text-xs text-slate-500 leading-relaxed">Supervisa proyectos, usuarios, permisos y métricas de desempeño.</p>
                        </div>

                        <div class="p-4 rounded-xl bg-white border border-slate-200/90 shadow-xs">
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200">
                                LÍDER DE PROYECTO
                            </span>
                            <h4 class="font-bold text-sm text-[#2F295B] mt-2 mb-1">Gestión y Tiempos</h4>
                            <p class="text-xs text-slate-500 leading-relaxed">Crea actividades, asigna responsables, define compromisos y acuerdos.</p>
                        </div>

                        <div class="p-4 rounded-xl bg-white border border-slate-200/90 shadow-xs">
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
                                DESARROLLADOR
                            </span>
                            <h4 class="font-bold text-sm text-[#2F295B] mt-2 mb-1">Ejecución y Avances</h4>
                            <p class="text-xs text-slate-500 leading-relaxed">Consulta sus actividades en la app móvil y reporta avances y notas.</p>
                        </div>

                        <div class="p-4 rounded-xl bg-white border border-slate-200/90 shadow-xs">
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-200">
                                ANALISTA DE QA
                            </span>
                            <h4 class="font-bold text-sm text-[#2F295B] mt-2 mb-1">Calidad y Acuerdos</h4>
                            <p class="text-xs text-slate-500 leading-relaxed">Valida cumplimiento de especificaciones y minutas de proyecto.</p>
                        </div>
                    </div>

                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="border-t border-slate-200 bg-white py-6 text-center text-xs text-slate-500">
            <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('images/betrack-logo.png') }}" alt="BeTrack" class="h-6 w-auto">
                    <strong class="text-[#2F295B]">BeTrack</strong>
                    <span class="text-slate-400">· Planifica • Avanza • Logra</span>
                </div>
                <div>
                    © {{ date('Y') }} BeTrack
                </div>
            </div>
        </footer>
    </body>
</html>
