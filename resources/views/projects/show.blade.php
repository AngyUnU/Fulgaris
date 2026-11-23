<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('projects.index') }}" class="p-2 rounded-lg border border-slate-200 text-slate-500 hover:text-slate-800 hover:bg-white transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-mono text-xs font-bold px-2 py-0.5 rounded bg-slate-100 text-slate-700 border border-slate-200">
                            {{ $project->code }}
                        </span>
                        <span class="text-xs text-slate-400">• Proyecto</span>
                    </div>
                    <h1 class="text-2xl font-extrabold text-[#2F295B] tracking-tight mt-0.5">{{ $project->name }}</h1>
                </div>
            </div>

            <div class="flex items-center gap-2">
                @if(Auth::user()->hasRole(['admin', 'lider']))
                    <button type="button" onclick="document.getElementById('modal-editar-proyecto').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 rounded-lg border border-slate-300 bg-white text-slate-700 text-xs font-semibold hover:bg-slate-50 transition">
                        Editar proyecto
                    </button>
                    <button type="button" onclick="document.getElementById('modal-eliminar-proyecto').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 rounded-lg border border-red-200 bg-white text-red-600 text-xs font-semibold hover:bg-red-50 transition">
                        Eliminar proyecto
                    </button>
                @endif
                @if(Auth::user()->hasRole(['admin', 'lider', 'desarrollador', 'qa']))
                    <button type="button" onclick="document.getElementById('modal-nueva-actividad').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 rounded-lg bg-[#1573F7] hover:bg-[#0d62d9] text-white text-xs font-bold shadow-sm transition">
                        <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Añadir Actividad
                    </button>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Project Details Card -->
            <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-xs">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="md:col-span-3 space-y-3">
                        <h3 class="font-bold text-sm text-slate-700">Descripción y Alcance</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            {{ $project->description ?? 'No se ha proporcionado una descripción detallada.' }}
                        </p>
                        
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-3 border-t border-slate-100 text-xs">
                            <div>
                                <span class="text-slate-400 block text-[10px]">Líder del Proyecto</span>
                                <span class="font-bold text-slate-800">{{ $project->leader?->name ?? 'Sin asignar' }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px]">Estado</span>
                                <span class="font-semibold text-[#1573F7]">{{ ucfirst(str_replace('_', ' ', $project->status)) }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px]">Fecha de Inicio</span>
                                <span class="font-semibold text-slate-700">{{ $project->start_date ? $project->start_date->format('d/m/Y') : 'N/D' }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px]">Fecha de Compromiso</span>
                                <span class="font-bold text-slate-800">{{ $project->due_date ? $project->due_date->format('d/m/Y') : 'N/D' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Gauge Box -->
                    <div class="bg-slate-50 rounded-xl p-5 border border-slate-100 flex flex-col justify-center text-center">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Avance del Proyecto</span>
                        <div class="text-4xl font-black text-[#1573F7] my-2">
                            {{ $project->calculated_progress }}%
                        </div>
                        <div class="w-full h-2 bg-slate-200 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-[#1573F7] to-[#5F3AD7] rounded-full" style="width: {{ $project->calculated_progress }}%"></div>
                        </div>
                        <span class="text-[10px] text-slate-400 mt-2">
                            Basado en {{ $project->activities->count() }} actividades
                        </span>
                    </div>
                </div>
            </div>

            <!-- Activities Section -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-base text-[#2F295B]">Actividades del Proyecto</h3>
                        <p class="text-xs text-slate-500">Plan de trabajo, integrantes asignados y porcentaje de avance</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 uppercase text-[10px] tracking-wider font-bold">
                                <th class="p-4">Actividad</th>
                                <th class="p-4">Responsables</th>
                                <th class="p-4">Prioridad</th>
                                <th class="p-4">Compromiso</th>
                                <th class="p-4">Progreso</th>
                                <th class="p-4">Estado</th>
                                <th class="p-4 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse ($project->activities as $act)
                                @php
                                    $prioColors = [
                                        'urgente' => 'text-red-700 bg-red-50 border-red-200',
                                        'alta' => 'text-orange-700 bg-orange-50 border-orange-200',
                                        'media' => 'text-blue-700 bg-blue-50 border-blue-200',
                                        'baja' => 'text-slate-600 bg-slate-100 border-slate-200',
                                    ];
                                    $statColors = [
                                        'completada' => 'text-emerald-700 bg-emerald-50 border-emerald-200',
                                        'en_progreso' => 'text-blue-700 bg-blue-50 border-blue-200',
                                        'pendiente' => 'text-amber-700 bg-amber-50 border-amber-200',
                                        'atrasada' => 'text-red-700 bg-red-50 border-red-200',
                                    ];
                                @endphp
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="p-4 font-semibold text-slate-900 max-w-xs">
                                        <div class="line-clamp-1">{{ $act->title }}</div>
                                        <div class="text-[11px] text-slate-400 font-normal line-clamp-1 mt-0.5">{{ $act->description }}</div>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            @forelse($act->assignees as $assignee)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                                    {{ $assignee->name }}
                                                </span>
                                            @empty
                                                <span class="text-slate-400 italic text-[11px]">Sin asignar</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border {{ $prioColors[$act->priority] ?? 'bg-slate-100 text-slate-600' }}">
                                            {{ ucfirst($act->priority) }}
                                        </span>
                                    </td>
                                    <td class="p-4 font-medium {{ $act->is_overdue ? 'text-red-600 font-bold' : '' }}">
                                        {{ $act->due_date ? $act->due_date->format('d/m/Y') : 'N/D' }}
                                    </td>
                                    <td class="p-4 w-36">
                                        <div class="flex items-center gap-2">
                                            <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                                                <div class="h-full bg-[#1573F7] rounded-full" style="width: {{ $act->progress }}%"></div>
                                            </div>
                                            <span class="font-bold text-[11px] w-8 text-right">{{ $act->progress }}%</span>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full border {{ $statColors[$act->status] ?? 'bg-slate-100' }}">
                                            {{ ucfirst(str_replace('_', ' ', $act->status)) }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-right">
                                        <a href="{{ route('activities.index', ['project_id' => $project->id]) }}" class="text-[#1573F7] hover:underline font-bold text-xs">
                                            Detalle
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-8 text-center text-slate-400">
                                        No hay actividades registradas en este proyecto aún.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Agreements Section -->
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <h3 class="font-bold text-base text-[#2F295B]">Acuerdos Registrados en el Proyecto</h3>
                    <a href="{{ route('agreements.index', ['project_id' => $project->id]) }}" class="text-xs font-bold text-[#1573F7] hover:underline">
                        Ver todos los acuerdos →
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse ($project->agreements as $agr)
                        <div class="p-4 rounded-xl border border-slate-100 bg-slate-50/50 space-y-2">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-slate-800">{{ $agr->title }}</span>
                                <span class="text-[10px] px-2 py-0.5 rounded font-bold uppercase bg-white border border-slate-200">
                                    {{ $agr->status }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-600">{{ $agr->description }}</p>
                            <div class="flex items-center justify-between text-[11px] text-slate-400 pt-2 border-t border-slate-100">
                                <span>Responsable: <strong class="text-slate-700">{{ $agr->responsible?->name ?? 'N/D' }}</strong></span>
                                <span>Límite: <strong class="text-slate-700">{{ $agr->due_date ? $agr->due_date->format('d/m/Y') : 'N/D' }}</strong></span>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-4 text-xs text-slate-400">
                            No hay acuerdos vinculados a este proyecto.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    @if(Auth::user()->hasRole(['admin', 'lider']))
        <div id="modal-eliminar-proyecto" class="{{ $errors->has('admin_password') ? '' : 'hidden' }} fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="delete-project-title">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-red-100">
                <div class="flex items-start gap-4">
                    <div class="shrink-0 flex items-center justify-center w-11 h-11 rounded-full bg-amber-100 text-amber-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.3 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.7 3.86a2 2 0 0 0-3.4 0Z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 id="delete-project-title" class="font-bold text-lg text-slate-900">Confirmar eliminación</h3>
                        <p class="mt-1 text-sm text-slate-600">Se eliminará el proyecto <strong>{{ $project->name }}</strong>, junto con sus actividades y acuerdos. Esta acción no se puede deshacer.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('projects.destroy', $project) }}" class="mt-5 space-y-4">
                    @csrf
                    @method('DELETE')
                    <div>
                        <label for="delete-project-admin-password" class="block text-xs font-semibold text-slate-700 mb-1">Contraseña de un administrador</label>
                        <input id="delete-project-admin-password" type="password" name="admin_password" required autocomplete="current-password" class="w-full text-sm rounded-lg border-slate-300 focus:border-red-500 focus:ring-red-500">
                        @error('admin_password')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" onclick="document.getElementById('modal-eliminar-proyecto').classList.add('hidden')" class="px-4 py-2 rounded-lg border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50">Cancelar</button>
                        <button type="submit" class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white text-xs font-bold">Eliminar proyecto</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="modal-editar-proyecto" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-200">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="font-bold text-lg text-[#2F295B]">Editar proyecto</h3>
                    <button type="button" onclick="document.getElementById('modal-editar-proyecto').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
                </div>

                <form method="POST" action="{{ route('projects.update', $project) }}" class="mt-4 space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="project-name" class="block text-xs font-semibold text-slate-700 mb-1">Nombre *</label>
                        <input id="project-name" type="text" name="name" value="{{ $project->name }}" required class="w-full text-xs rounded-lg border-slate-200">
                    </div>

                    <div>
                        <label for="project-description" class="block text-xs font-semibold text-slate-700 mb-1">Descripción</label>
                        <textarea id="project-description" name="description" rows="3" class="w-full text-xs rounded-lg border-slate-200">{{ $project->description }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="project-leader" class="block text-xs font-semibold text-slate-700 mb-1">Líder</label>
                            <select id="project-leader" name="leader_id" class="w-full text-xs rounded-lg border-slate-200">
                                <option value="">Sin asignar</option>
                                @foreach ($leaders as $leader)
                                    <option value="{{ $leader->id }}" {{ $project->leader_id == $leader->id ? 'selected' : '' }}>{{ $leader->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="project-status" class="block text-xs font-semibold text-slate-700 mb-1">Estado *</label>
                            <select id="project-status" name="status" required class="w-full text-xs rounded-lg border-slate-200">
                                <option value="planificacion" {{ $project->status === 'planificacion' ? 'selected' : '' }}>Planificación</option>
                                <option value="en_progreso" {{ $project->status === 'en_progreso' ? 'selected' : '' }}>En progreso</option>
                                <option value="en_pausa" {{ $project->status === 'en_pausa' ? 'selected' : '' }}>En pausa</option>
                                <option value="concluido" {{ $project->status === 'concluido' ? 'selected' : '' }}>Concluido</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="project-start-date" class="block text-xs font-semibold text-slate-700 mb-1">Fecha de inicio</label>
                            <input id="project-start-date" type="date" name="start_date" value="{{ $project->start_date?->format('Y-m-d') }}" class="w-full text-xs rounded-lg border-slate-200">
                        </div>
                        <div>
                            <label for="project-due-date" class="block text-xs font-semibold text-slate-700 mb-1">Fecha de compromiso</label>
                            <input id="project-due-date" type="date" name="due_date" value="{{ $project->due_date?->format('Y-m-d') }}" class="w-full text-xs rounded-lg border-slate-200">
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button" onclick="document.getElementById('modal-editar-proyecto').classList.add('hidden')" class="px-4 py-2 border border-slate-200 rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-50">
                            Cancelar
                        </button>
                        <button type="submit" class="px-5 py-2 bg-[#1573F7] hover:bg-[#0d62d9] text-white rounded-lg text-xs font-bold shadow-sm">
                            Guardar cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal Registrar Actividad para este proyecto -->
    <div id="modal-nueva-actividad" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-200">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="font-bold text-lg text-[#2F295B]">Nueva Actividad en {{ $project->code }}</h3>
                <button type="button" onclick="document.getElementById('modal-nueva-actividad').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <form method="POST" action="{{ route('activities.store') }}" class="mt-4 space-y-4">
                @csrf
                <input type="hidden" name="project_id" value="{{ $project->id }}">

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Título de la Actividad *</label>
                    <input type="text" name="title" placeholder="Ej. Diseñar interfaz de usuario" required class="w-full text-xs rounded-lg border-slate-200">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Descripción</label>
                    <textarea name="description" rows="3" placeholder="Detalles de la tarea a realizar..." class="w-full text-xs rounded-lg border-slate-200"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Prioridad</label>
                        <select name="priority" class="w-full text-xs rounded-lg border-slate-200" required>
                            <option value="media" selected>Media</option>
                            <option value="alta">Alta</option>
                            <option value="urgente">Urgente</option>
                            <option value="baja">Baja</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Fecha de compromiso</label>
                        <input type="date" name="due_date" class="w-full text-xs rounded-lg border-slate-200">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Progreso (%)</label>
                        <input type="number" name="progress" min="0" max="100" value="0" required class="w-full text-xs rounded-lg border-slate-200">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Estado</label>
                    <select name="status" required class="w-full text-xs rounded-lg border-slate-200">
                        <option value="pendiente">Pendiente</option>
                        <option value="en_progreso">En progreso</option>
                        <option value="completada">Completada</option>
                        <option value="atrasada">Atrasada</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Responsables asignados (Integrantes)</label>
                    <select name="assignees[]" multiple class="w-full text-xs rounded-lg border-slate-200 h-24">
                        @foreach ($users as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->role?->label }})</option>
                        @endforeach
                    </select>
                    <p class="text-[10px] text-slate-400 mt-1">Mantén presionado Ctrl (o Cmd) para seleccionar varios integrantes.</p>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modal-nueva-actividad').classList.add('hidden')" class="px-4 py-2 border border-slate-200 rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-50">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2 bg-[#1573F7] hover:bg-[#0d62d9] text-white rounded-lg text-xs font-bold shadow-sm">
                        Crear Actividad
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
