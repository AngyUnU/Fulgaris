<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Módulo de Administración</span>
                <h1 class="text-2xl font-extrabold text-[#2F295B] tracking-tight">Gestión de Proyectos</h1>
            </div>

            @if(Auth::user()->hasRole(['admin', 'lider']))
                <button type="button" onclick="document.getElementById('modal-nuevo-proyecto').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 rounded-lg bg-[#1573F7] hover:bg-[#0d62d9] text-white text-xs font-bold shadow-sm transition">
                    <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nuevo Proyecto
                </button>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Filters Bar -->
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs flex flex-col md:flex-row items-center justify-between gap-4">
                <form method="GET" action="{{ route('projects.index') }}" class="w-full flex flex-col sm:flex-row items-center gap-3">
                    <div class="relative w-full sm:w-72">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre o código..." class="w-full text-xs rounded-lg border-slate-200 ps-9 focus:border-[#1573F7] focus:ring-[#1573F7]">
                        <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>

                    <div class="w-full sm:w-48">
                        <select name="status" onchange="this.form.submit()" class="w-full text-xs rounded-lg border-slate-200 focus:border-[#1573F7] focus:ring-[#1573F7]">
                            <option value="">Todos los estados</option>
                            <option value="en_progreso" {{ request('status') === 'en_progreso' ? 'selected' : '' }}>En Progreso</option>
                            <option value="planificacion" {{ request('status') === 'planificacion' ? 'selected' : '' }}>Planificación</option>
                            <option value="en_pausa" {{ request('status') === 'en_pausa' ? 'selected' : '' }}>En Pausa</option>
                            <option value="concluido" {{ request('status') === 'concluido' ? 'selected' : '' }}>Concluido</option>
                        </select>
                    </div>

                    <button type="submit" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition">
                        Filtrar
                    </button>

                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('projects.index') }}" class="text-xs text-slate-500 hover:text-slate-800 underline">
                            Limpiar
                        </a>
                    @endif
                </form>

                <div class="text-xs text-slate-500 shrink-0">
                    Mostrando <strong class="text-slate-800">{{ $projects->total() }}</strong> proyectos
                </div>
            </div>

            <!-- Projects Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($projects as $project)
                    @php
                        $progress = $project->calculated_progress;
                        $statusStyles = [
                            'en_progreso' => ['bg' => 'bg-blue-50 text-blue-700 border-blue-200', 'label' => 'En Progreso'],
                            'planificacion' => ['bg' => 'bg-amber-50 text-amber-700 border-amber-200', 'label' => 'Planificación'],
                            'en_pausa' => ['bg' => 'bg-slate-100 text-slate-700 border-slate-200', 'label' => 'En Pausa'],
                            'concluido' => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'label' => 'Concluido'],
                        ];
                        $style = $statusStyles[$project->status] ?? ['bg' => 'bg-slate-100 text-slate-700 border-slate-200', 'label' => ucfirst($project->status)];
                    @endphp
                    <div class="bg-white rounded-xl border border-slate-200/90 p-5 shadow-xs hover:shadow-md transition flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="font-mono text-xs font-bold px-2 py-0.5 rounded bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $project->code }}
                                </span>
                                <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full border {{ $style['bg'] }}">
                                    {{ $style['label'] }}
                                </span>
                            </div>

                            <h3 class="font-bold text-base text-[#2F295B] hover:text-[#1573F7] transition mb-2">
                                <a href="{{ route('projects.show', $project) }}">{{ $project->name }}</a>
                            </h3>

                            <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed mb-4">
                                {{ $project->description ?? 'Sin descripción especificada.' }}
                            </p>
                        </div>

                        <div>
                            <!-- Progress Bar -->
                            <div class="space-y-1 mb-4">
                                <div class="flex justify-between text-xs">
                                    <span class="text-slate-500 text-[11px]">Avance global</span>
                                    <span class="font-bold text-slate-800">{{ $progress }}%</span>
                                </div>
                                <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-[#1573F7] to-[#5F3AD7] rounded-full" style="width: {{ $progress }}%"></div>
                                </div>
                            </div>

                            <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                                <div>
                                    <span class="block text-[10px] text-slate-400">Responsable:</span>
                                    <span class="font-semibold text-slate-700 truncate max-w-[140px] inline-block">
                                        {{ $project->leader?->name ?? 'Sin asignar' }}
                                    </span>
                                </div>
                                <div class="text-right">
                                    <span class="block text-[10px] text-slate-400">Compromiso:</span>
                                    <span class="font-semibold text-slate-700">
                                        {{ $project->due_date ? $project->due_date->format('d/m/Y') : 'N/D' }}
                                    </span>
                                </div>
                            </div>

                            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                                <span class="text-xs text-slate-500">
                                    <strong>{{ $project->activities->count() }}</strong> actividades
                                </span>
                                <a href="{{ route('projects.show', $project) }}" class="text-xs font-bold text-[#1573F7] hover:underline flex items-center gap-1">
                                    Ver Proyecto →
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white rounded-xl border border-slate-200 p-12 text-center">
                        <div class="w-12 h-12 rounded-full bg-blue-50 text-[#1573F7] mx-auto flex items-center justify-center mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h4 class="font-bold text-slate-800 text-sm mb-1">No se encontraron proyectos</h4>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto">
                            Comienza registrando tu primer proyecto para organizar actividades y acuerdos en ArkanA.
                        </p>
                    </div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $projects->links() }}
            </div>

        </div>
    </div>

    <!-- Modal Registrar Proyecto -->
    <div id="modal-nuevo-proyecto" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-xl border border-slate-200 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="font-bold text-lg text-[#2F295B]">Registrar Nuevo Proyecto</h3>
                <button type="button" onclick="document.getElementById('modal-nuevo-proyecto').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <form method="POST" action="{{ route('projects.store') }}" class="mt-4 space-y-4">
                @csrf

                <div class="grid grid-cols-3 gap-3">
                    <div class="col-span-1">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Código *</label>
                        <input type="text" name="code" value="{{ old('code', 'BP-0'.(rand(4, 99))) }}" required class="w-full text-xs rounded-lg border-slate-200 font-mono">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nombre del Proyecto *</label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Ej. Portal de Servicios" required class="w-full text-xs rounded-lg border-slate-200">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Descripción</label>
                    <textarea name="description" rows="3" placeholder="Objetivos y alcance del proyecto..." class="w-full text-xs rounded-lg border-slate-200">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Líder o Responsable</label>
                        <select name="leader_id" class="w-full text-xs rounded-lg border-slate-200">
                            <option value="">-- Seleccionar --</option>
                            @foreach ($leaders as $leader)
                                <option value="{{ $leader->id }}">{{ $leader->name }} ({{ $leader->role?->label }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Estado Inicial</label>
                        <select name="status" class="w-full text-xs rounded-lg border-slate-200" required>
                            <option value="planificacion">Planificación</option>
                            <option value="en_progreso" selected>En Progreso</option>
                            <option value="en_pausa">En Pausa</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Fecha de Inicio</label>
                        <input type="date" name="start_date" value="{{ date('Y-m-d') }}" class="w-full text-xs rounded-lg border-slate-200">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Fecha de Compromiso</label>
                        <input type="date" name="due_date" class="w-full text-xs rounded-lg border-slate-200">
                    </div>
                </div>

                <section class="pt-4 border-t border-slate-100 space-y-3">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h4 class="text-sm font-bold text-[#2F295B]">Actividades iniciales</h4>
                            <p class="text-xs text-slate-500">Puedes agregar actividades para iniciar el plan de trabajo.</p>
                        </div>
                        <button type="button" onclick="addProjectActivity()" class="shrink-0 px-3 py-2 rounded-lg border border-slate-200 text-xs font-semibold text-[#1573F7] hover:bg-blue-50">
                            + Agregar actividad
                        </button>
                    </div>
                    <div id="project-activities-list" class="space-y-3"></div>
                </section>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modal-nuevo-proyecto').classList.add('hidden')" class="px-4 py-2 border border-slate-200 rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-50">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2 bg-[#1573F7] hover:bg-[#0d62d9] text-white rounded-lg text-xs font-bold shadow-sm">
                        Crear proyecto y actividades
                    </button>
                </div>
            </form>
        </div>
    </div>

    <template id="project-activity-template">
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 space-y-3" data-project-activity data-index="__INDEX__">
            <div class="flex items-center justify-between">
                <h5 class="text-xs font-bold text-slate-700">Nueva actividad</h5>
                <button type="button" onclick="this.closest('[data-project-activity]').remove()" class="text-xs text-red-600 hover:underline">Quitar actividad</button>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Título *</label>
                    <input type="text" name="activities[__INDEX__][title]" required maxlength="255" class="w-full text-xs rounded-lg border-slate-200">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Prioridad</label>
                    <select name="activities[__INDEX__][priority]" required class="w-full text-xs rounded-lg border-slate-200">
                        <option value="media" selected>Media</option>
                        <option value="alta">Alta</option>
                        <option value="urgente">Urgente</option>
                        <option value="baja">Baja</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Descripción</label>
                <textarea name="activities[__INDEX__][description]" rows="2" class="w-full text-xs rounded-lg border-slate-200"></textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Estado</label>
                <select name="activities[__INDEX__][status]" required class="w-full text-xs rounded-lg border-slate-200">
                    <option value="pendiente">Por empezar</option>
                    <option value="en_progreso">En progreso</option>
                    <option value="en_revision">En revisión</option>
                    <option value="completada">Completada</option>
                    <option value="atrasada">Atrasada</option>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Fecha de compromiso</label>
                    <input type="date" name="activities[__INDEX__][due_date]" class="w-full text-xs rounded-lg border-slate-200">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Responsables</label>
                    <select name="activities[__INDEX__][assignees][]" multiple class="w-full text-xs rounded-lg border-slate-200 h-20">
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role?->label }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </template>

    <script>
        let projectActivityIndex = 0;

        function addProjectActivity() {
            const template = document.getElementById('project-activity-template').innerHTML;
            const activity = document.createElement('div');
            activity.innerHTML = template.replaceAll('__INDEX__', projectActivityIndex++);
            document.getElementById('project-activities-list').append(activity.firstElementChild);
        }

    </script>
</x-app-layout>
