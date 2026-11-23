<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold uppercase tracking-wider bg-blue-50 text-[#1573F7] border border-blue-200">
                        Plataforma Web
                    </span>
                    <span class="text-xs text-slate-400">• BeTrack</span>
                </div>
                <h1 class="font-extrabold text-2xl text-[#2F295B] mt-1 tracking-tight">
                    Resumen de trabajo
                </h1>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('activities.index') }}" class="inline-flex items-center px-3.5 py-2 rounded-lg border border-slate-300 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 shadow-xs transition">
                    Ver Actividades
                </a>
                <a href="{{ route('projects.index') }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-[#1573F7] text-white text-xs font-bold hover:bg-[#0d62d9] shadow-sm transition">
                    <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Gestionar Proyectos
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Executive KPI Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Proyectos -->
                <div class="bg-white rounded-xl border border-slate-200/90 p-5 shadow-xs">
                    <div class="flex items-center justify-between text-slate-500 mb-2">
                        <span class="text-xs font-semibold uppercase tracking-wider">Proyectos</span>
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-[#1573F7] flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-black text-[#2F295B]">{{ $totalProjects }}</div>
                    <div class="text-[11px] text-slate-500 mt-1 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
                        <span>{{ $activeProjects }} en desarrollo activo</span>
                    </div>
                </div>

                <!-- Actividades en progreso -->
                <div class="bg-white rounded-xl border border-slate-200/90 p-5 shadow-xs">
                    <div class="flex items-center justify-between text-slate-500 mb-2">
                        <span class="text-xs font-semibold uppercase tracking-wider">En Progreso</span>
                        <div class="w-8 h-8 rounded-lg bg-sky-50 text-[#029DFF] flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-black text-[#2F295B]">{{ $inProgressActivities }}</div>
                    <div class="text-[11px] text-slate-500 mt-1">
                        de <strong class="text-slate-700">{{ $totalActivities }}</strong> actividades totales
                    </div>
                </div>

                <!-- Actividades Completadas -->
                <div class="bg-white rounded-xl border border-slate-200/90 p-5 shadow-xs">
                    <div class="flex items-center justify-between text-slate-500 mb-2">
                        <span class="text-xs font-semibold uppercase tracking-wider">Completadas</span>
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-black text-emerald-600">{{ $completedActivities }}</div>
                    <div class="text-[11px] text-slate-500 mt-1">
                        Tareas terminadas al 100%
                    </div>
                </div>

                <!-- Tareas con Atraso (Alerta) -->
                <div class="bg-white rounded-xl border {{ $overdueActivities > 0 ? 'border-red-300 bg-red-50/20' : 'border-slate-200/90' }} p-5 shadow-xs">
                    <div class="flex items-center justify-between text-slate-500 mb-2">
                        <span class="text-xs font-semibold uppercase tracking-wider {{ $overdueActivities > 0 ? 'text-red-700' : '' }}">Atrasadas</span>
                        <div class="w-8 h-8 rounded-lg {{ $overdueActivities > 0 ? 'bg-red-100 text-red-600' : 'bg-slate-100 text-slate-500' }} flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-black {{ $overdueActivities > 0 ? 'text-red-600' : 'text-slate-800' }}">{{ $overdueActivities }}</div>
                    <div class="text-[11px] {{ $overdueActivities > 0 ? 'text-red-600 font-medium' : 'text-slate-500' }} mt-1">
                        {{ $overdueActivities > 0 ? 'Requieren atención urgente' : 'Sin retrasos registrados' }}
                    </div>
                </div>

                <!-- Acuerdos y Cumplimiento -->
                <div class="bg-white rounded-xl border border-slate-200/90 p-5 shadow-xs">
                    <div class="flex items-center justify-between text-slate-500 mb-2">
                        <span class="text-xs font-semibold uppercase tracking-wider">Acuerdos</span>
                        <div class="w-8 h-8 rounded-lg bg-purple-50 text-[#5F3AD7] flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-black text-[#2F295B]">{{ $totalAgreements }}</div>
                    <div class="text-[11px] text-slate-500 mt-1">
                        <strong class="text-[#5F3AD7]">{{ $pendingAgreements }}</strong> en seguimiento
                    </div>
                </div>
            </div>

            <!-- Global Progress Bar banner -->
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-[#1573F7] to-[#5F3AD7] text-white flex items-center justify-center font-bold text-sm shrink-0">
                        {{ $overallProgress }}%
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-[#2F295B]">Avance Global Consolidado</h3>
                        <p class="text-xs text-slate-500">Promedio de avance registrado en todas las actividades de los proyectos.</p>
                    </div>
                </div>
                <div class="w-full md:w-1/2">
                    <div class="flex justify-between text-xs font-medium text-slate-600 mb-1">
                        <span>Progreso general</span>
                        <span class="font-bold text-[#1573F7]">{{ $overallProgress }}%</span>
                    </div>
                    <div class="w-full h-3 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-[#1573F7] to-[#5F3AD7] rounded-full transition-all duration-500" style="width: {{ $overallProgress }}%"></div>
                    </div>
                </div>
            </div>

            <!-- 2-Column Grid: Proyectos Activos & Actividades con Próximo Vencimiento -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Left 2 Cols: Proyectos Recientes -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                            <div>
                                <h3 class="font-bold text-[#2F295B] text-base">Proyectos en Supervisión</h3>
                                <p class="text-xs text-slate-500">Seguimiento de cumplimiento y responsables asignados</p>
                            </div>
                            <a href="{{ route('projects.index') }}" class="text-xs font-bold text-[#1573F7] hover:underline">
                                Ver todos ({{ $totalProjects }}) →
                            </a>
                        </div>

                        <div class="divide-y divide-slate-100">
                            @forelse ($recentProjects as $project)
                                @php
                                    $pProgress = $project->calculated_progress;
                                    $statusBadges = [
                                        'en_progreso' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'planificacion' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'en_pausa' => 'bg-slate-100 text-slate-700 border-slate-200',
                                        'concluido' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    ];
                                @endphp
                                <div class="p-5 hover:bg-slate-50/60 transition">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-2">
                                                <span class="font-mono text-xs font-bold px-2 py-0.5 rounded bg-slate-100 text-slate-700">
                                                    {{ $project->code }}
                                                </span>
                                                <span class="text-[11px] px-2 py-0.5 rounded-full font-semibold border {{ $statusBadges[$project->status] ?? 'bg-slate-100 text-slate-600' }}">
                                                    {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                                </span>
                                            </div>
                                            <h4 class="font-bold text-slate-800 text-sm hover:text-[#1573F7] transition">
                                                <a href="{{ route('projects.show', $project) }}">{{ $project->name }}</a>
                                            </h4>
                                            <p class="text-xs text-slate-500 line-clamp-1">{{ $project->description }}</p>
                                        </div>

                                        <div class="text-right shrink-0">
                                            <div class="text-sm font-black text-[#2F295B]">{{ $pProgress }}%</div>
                                            <span class="text-[10px] text-slate-400">Avance</span>
                                        </div>
                                    </div>

                                    <!-- Progress line and meta -->
                                    <div class="mt-3">
                                        <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                            <div class="h-full bg-[#1573F7] rounded-full" style="width: {{ $pProgress }}%"></div>
                                        </div>
                                    </div>

                                    <div class="mt-3 flex flex-wrap items-center justify-between text-xs text-slate-500 gap-2">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            <span>Responsable: <strong class="text-slate-700">{{ $project->leader?->name ?? 'No asignado' }}</strong></span>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <span>Actividades: <strong>{{ $project->activities->count() }}</strong></span>
                                            @if($project->due_date)
                                                <span>Compromiso: <strong class="text-slate-700">{{ $project->due_date->format('d/m/Y') }}</strong></span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 text-center text-sm text-slate-500">
                                    No hay proyectos registrados en este momento.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Acuerdos Recientes -->
                    <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                            <div>
                                <h3 class="font-bold text-[#2F295B] text-base">Acuerdos y Minutas de Reunión</h3>
                                <p class="text-xs text-slate-500">Compromisos formales vinculados a proyectos</p>
                            </div>
                            <a href="{{ route('agreements.index') }}" class="text-xs font-bold text-[#1573F7] hover:underline">
                                Gestionar Acuerdos →
                            </a>
                        </div>

                        <div class="divide-y divide-slate-100">
                            @forelse ($recentAgreements as $agr)
                                @php
                                    $agrBadges = [
                                        'pendiente' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'en_cumplimiento' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'cumplido' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'vencido' => 'bg-red-50 text-red-700 border-red-200',
                                    ];
                                @endphp
                                <div class="p-4 flex items-start justify-between gap-3 hover:bg-slate-50/50 transition">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full border {{ $agrBadges[$agr->status] ?? 'bg-slate-100 text-slate-700' }}">
                                                {{ ucfirst(str_replace('_', ' ', $agr->status)) }}
                                            </span>
                                            <span class="text-xs font-semibold text-slate-500">{{ $agr->project?->code }}</span>
                                        </div>
                                        <h5 class="text-sm font-semibold text-slate-800">{{ $agr->title }}</h5>
                                        <p class="text-xs text-slate-500 line-clamp-1">{{ $agr->description }}</p>
                                    </div>
                                    <div class="text-right shrink-0 text-xs">
                                        <span class="text-slate-400 block text-[10px]">Límite:</span>
                                        <span class="font-semibold text-slate-700">{{ $agr->due_date ? $agr->due_date->format('d/m/Y') : 'Sin fecha' }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="p-6 text-center text-sm text-slate-500">
                                    No hay acuerdos registrados.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Right Col: Actividades Prioritarias & Feed de Seguimiento Móvil -->
                <div class="space-y-6">

                    <!-- Actividades con Atención Requerida -->
                    <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                            <div>
                                <h3 class="font-bold text-[#2F295B] text-base">Fechas de Compromiso</h3>
                                <p class="text-xs text-slate-500">Actividades próximas o atrasadas</p>
                            </div>
                            <a href="{{ route('activities.index') }}" class="text-xs font-bold text-[#1573F7] hover:underline">
                                Ver todas →
                            </a>
                        </div>

                        <div class="divide-y divide-slate-100">
                            @forelse ($urgentActivities as $act)
                                @php
                                    $isOverdue = $act->is_overdue;
                                @endphp
                                <div class="p-4 hover:bg-slate-50/50 transition {{ $isOverdue ? 'bg-red-50/30' : '' }}">
                                    <div class="flex items-start justify-between gap-2">
                                        <h5 class="font-bold text-xs text-slate-800 line-clamp-1">{{ $act->title }}</h5>
                                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded {{ $isOverdue ? 'bg-red-100 text-red-700' : 'bg-slate-100 text-slate-600' }}">
                                            {{ $isOverdue ? 'Atrasada' : $act->progress.'%' }}
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-0.5 truncate">{{ $act->project?->name }}</p>
                                    
                                    <div class="mt-2 flex items-center justify-between text-[11px]">
                                        <span class="text-slate-500">
                                            Compromiso: <strong class="{{ $isOverdue ? 'text-red-600' : 'text-slate-700' }}">{{ $act->due_date ? $act->due_date->format('d/m/Y') : 'N/D' }}</strong>
                                        </span>
                                        <div class="flex -space-x-1 overflow-hidden">
                                            @foreach ($act->assignees->take(2) as $assignee)
                                                <div class="inline-block h-5 w-5 rounded-full ring-2 ring-white bg-[#1573F7] text-white text-[9px] font-bold flex items-center justify-center" title="{{ $assignee->name }}">
                                                    {{ strtoupper(substr($assignee->name, 0, 1)) }}
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="p-6 text-center text-sm text-slate-500">
                                    No hay tareas urgentes.
                                </div>
                            @endforelse
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>
</x-app-layout>
