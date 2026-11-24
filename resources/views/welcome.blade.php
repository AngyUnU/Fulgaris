<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>ArkanA - Agenda • Acuerdos • Análisis</title>
        <link rel="icon" type="image/png" href="{{ asset('images/arkana-logo.png') }}">

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-[#F8F9FC] text-[#202124] min-h-screen flex flex-col font-sans selection:bg-[#1573F7] selection:text-white">
        <!-- Navigation Bar -->
        <header class="border-b border-slate-200 bg-white">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/arkana-logo.png') }}" alt="ArkanA" class="h-10 w-auto object-contain rounded-lg shadow-xs">
                    <div class="flex flex-col">
                        <span class="text-2xl font-black tracking-tight text-[#2F295B] leading-none">Arkan<span class="text-[#1573F7]">A</span></span>
                        <span class="text-[10px] text-slate-500 mt-0.5">Agenda • Acuerdos • Análisis</span>
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
            <div class="py-12 sm:py-16">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-left">
                    
                    <div class="text-sm font-semibold text-[#1573F7] mb-3">
                        Agenda • Acuerdos • Análisis
                    </div>

                    <h1 class="text-3xl sm:text-5xl font-bold text-[#2F295B] max-w-3xl leading-tight">
                        Organiza el trabajo de tu equipo con <span class="text-[#1573F7]">ArkanA</span>
                    </h1>

                    <p class="mt-5 text-base text-slate-600 max-w-2xl leading-relaxed">
                        Centraliza la <strong>agenda</strong> de actividades, asegura el cumplimiento de <strong>acuerdos</strong> de equipo y obtén un <strong>análisis</strong> objetivo del avance en tiempo real desde la web o la aplicación móvil.
                    </p>

                    <div class="mt-6 flex flex-wrap items-center gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}" class="px-5 py-2.5 rounded-lg bg-[#1573F7] hover:bg-[#0d62d9] font-semibold text-white text-sm transition">
                                Acceder al Panel
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-lg bg-[#1573F7] hover:bg-[#0d62d9] font-semibold text-white text-sm transition">
                                Iniciar Sesión en ArkanA
                            </a>
                        @endauth
                    </div>

                    <!-- ArkanA features -->
                    <div class="mt-12 max-w-5xl bg-white rounded-lg border border-slate-200 p-6 sm:p-7 text-left">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="text-sm font-bold text-[#2F295B]">Qué puedes hacer</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Col 1 -->
                            <div class="p-4 rounded-lg bg-slate-50 border border-slate-200">
                                <div class="w-8 h-8 rounded-lg bg-blue-100 text-[#1573F7] flex items-center justify-center font-bold text-xs mb-3">
                                    📅
                                </div>
                                <h4 class="font-bold text-sm text-[#2F295B] mb-1">Agenda de Proyectos</h4>
                                <p class="text-xs text-slate-500 leading-relaxed">
                                    Planifica actividades, define encargados obligatorios y establece fechas de compromiso para mantener el rumbo claro.
                                </p>
                            </div>

                            <!-- Col 2 -->
                            <div class="p-4 rounded-lg bg-slate-50 border border-slate-200">
                                <div class="w-8 h-8 rounded-lg bg-purple-100 text-[#5F3AD7] flex items-center justify-center font-bold text-xs mb-3">
                                    🤝
                                </div>
                                <h4 class="font-bold text-sm text-[#2F295B] mb-1">Gestión de Acuerdos</h4>
                                <p class="text-xs text-slate-500 leading-relaxed">
                                    Formaliza minutas y compromisos vinculados a tareas para asegurar que cada decisión del equipo se ejecute.
                                </p>
                            </div>

                            <!-- Col 3 -->
                            <div class="p-4 rounded-lg bg-slate-50 border border-slate-200">
                                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs mb-3">
                                    📊
                                </div>
                                <h4 class="font-bold text-sm text-[#2F295B] mb-1">Análisis y Seguimiento</h4>
                                <p class="text-xs text-slate-500 leading-relaxed">
                                    Supervisa avances verificados con fotos de evidencia y métricas automáticas calculadas según el cumplimiento real.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Roles Grid -->
                    <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-left max-w-5xl">
                        <div class="p-4 rounded-lg bg-white border border-slate-200">
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-purple-50 text-purple-700 border border-purple-200">
                                ADMINISTRADOR
                            </span>
                            <h4 class="font-bold text-sm text-[#2F295B] mt-2 mb-1">Control Global</h4>
                            <p class="text-xs text-slate-500 leading-relaxed">Supervisa proyectos, usuarios, permisos y métricas de desempeño.</p>
                        </div>

                        <div class="p-4 rounded-lg bg-white border border-slate-200">
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200">
                                LÍDER DE PROYECTO
                            </span>
                            <h4 class="font-bold text-sm text-[#2F295B] mt-2 mb-1">Gestión y Tiempos</h4>
                            <p class="text-xs text-slate-500 leading-relaxed">Crea actividades, asigna responsables, define compromisos y acuerdos.</p>
                        </div>

                        <div class="p-4 rounded-lg bg-white border border-slate-200">
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
                                DESARROLLADOR
                            </span>
                            <h4 class="font-bold text-sm text-[#2F295B] mt-2 mb-1">Ejecución y Avances</h4>
                            <p class="text-xs text-slate-500 leading-relaxed">Actualiza desde el móvil o web, sube evidencias y comparte notas de trabajo.</p>
                        </div>

                        <div class="p-4 rounded-lg bg-white border border-slate-200">
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-200">
                                ANALISTA DE QA
                            </span>
                            <h4 class="font-bold text-sm text-[#2F295B] mt-2 mb-1">Calidad y Acuerdos</h4>
                            <p class="text-xs text-slate-500 leading-relaxed">Valida cumplimiento de especificaciones y minutas de proyecto.</p>
                        </div>
                    </div>

                    <!-- Sección Sobre Nosotros / Nuestra Empresa -->
                    <div class="mt-14 pt-10 border-t border-slate-200 max-w-5xl text-left">
                        <div class="mb-6">
                            <span class="text-xs font-bold text-[#1573F7] tracking-wider uppercase">Nuestra Empresa</span>
                            <h2 class="text-2xl sm:text-3xl font-extrabold text-[#2F295B] mt-1">Sobre Nosotros</h2>
                            <p class="mt-2 text-sm text-slate-600 max-w-3xl leading-relaxed">
                                En <strong>Fulgaris</strong> nos apasiona diseñar herramientas que transforman la gestión operativa y el trabajo colaborativo. Desarrollamos <strong>ArkanA</strong> como una respuesta integral a la dispersión de información, consolidando la <strong>Agenda</strong> de actividades, el cumplimiento formal de <strong>Acuerdos</strong> y el <strong>Análisis</strong> de resultados en un ecosistema sincronizado entre la web y dispositivos móviles.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                            <div class="p-5 rounded-xl bg-white border border-slate-200 shadow-xs">
                                <div class="w-8 h-8 rounded-lg bg-blue-100 text-[#1573F7] flex items-center justify-center font-bold text-sm mb-3">
                                    🎯
                                </div>
                                <h3 class="font-bold text-sm text-[#2F295B] mb-1">Misión</h3>
                                <p class="text-xs text-slate-500 leading-relaxed">
                                    Impulsar el éxito de los equipos de trabajo a través de soluciones tecnológicas ágiles, accesibles y orientadas a resultados tangibles.
                                </p>
                            </div>

                            <div class="p-5 rounded-xl bg-white border border-slate-200 shadow-xs">
                                <div class="w-8 h-8 rounded-lg bg-purple-100 text-[#5F3AD7] flex items-center justify-center font-bold text-sm mb-3">
                                    🚀
                                </div>
                                <h3 class="font-bold text-sm text-[#2F295B] mb-1">Visión</h3>
                                <p class="text-xs text-slate-500 leading-relaxed">
                                    Ser la plataforma de referencia para la coordinación y auditoría de proyectos tecnológicos en entornos universitarios y empresariales.
                                </p>
                            </div>

                            <div class="p-5 rounded-xl bg-white border border-slate-200 shadow-xs">
                                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm mb-3">
                                    💎
                                </div>
                                <h3 class="font-bold text-sm text-[#2F295B] mb-1">Valores</h3>
                                <p class="text-xs text-slate-500 leading-relaxed">
                                    Transparencia, colaboración continua, puntualidad en compromisos y excelencia en la ejecución de cada entrega.
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="border-t border-slate-200 bg-white py-6 text-center text-xs text-slate-500">
            <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('images/arkana-logo.png') }}" alt="ArkanA" class="h-6 w-auto rounded">
                    <strong class="text-[#2F295B]">ArkanA</strong>
                    <span class="text-slate-400">· Agenda • Acuerdos • Análisis</span>
                </div>
                <div>
                    © {{ date('Y') }} ArkanA · Fulgaris
                </div>
            </div>
        </footer>
    </body>
</html>
