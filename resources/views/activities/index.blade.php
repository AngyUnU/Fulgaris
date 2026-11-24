<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Planificación y Tareas</span>
                <h1 class="text-2xl font-extrabold text-[#2F295B] tracking-tight">Gestión de Actividades</h1>
            </div>

            @if(Auth::user()->hasRole(['admin', 'lider', 'desarrollador', 'qa']))
                <button type="button" onclick="document.getElementById('modal-actividad').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 rounded-lg bg-[#1573F7] hover:bg-[#0d62d9] text-white text-xs font-bold shadow-sm transition">
                    <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nueva Actividad
                </button>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Filter Controls -->
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs">
                <form method="GET" action="{{ route('activities.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                    <div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por título..." class="w-full text-xs rounded-lg border-slate-200 focus:border-[#1573F7] focus:ring-[#1573F7]">
                    </div>

                    <div>
                        <select name="project_id" onchange="this.form.submit()" class="w-full text-xs rounded-lg border-slate-200 focus:border-[#1573F7] focus:ring-[#1573F7]">
                            <option value="">Todos los proyectos</option>
                            @foreach ($projects as $p)
                                <option value="{{ $p->id }}" {{ request('project_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->code }} - {{ $p->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <select name="status" onchange="this.form.submit()" class="w-full text-xs rounded-lg border-slate-200 focus:border-[#1573F7] focus:ring-[#1573F7]">
                            <option value="">Todos los estados</option>
                            <option value="pendiente" {{ request('status') === 'pendiente' ? 'selected' : '' }}>Por empezar</option>
                            <option value="en_progreso" {{ request('status') === 'en_progreso' ? 'selected' : '' }}>En Progreso</option>
                            <option value="en_revision" {{ request('status') === 'en_revision' ? 'selected' : '' }}>En revisión</option>
                            <option value="completada" {{ request('status') === 'completada' ? 'selected' : '' }}>Completada</option>
                            <option value="atrasada" {{ request('status') === 'atrasada' ? 'selected' : '' }}>Atrasada</option>
                        </select>
                    </div>

                    <div>
                        <select name="priority" onchange="this.form.submit()" class="w-full text-xs rounded-lg border-slate-200 focus:border-[#1573F7] focus:ring-[#1573F7]">
                            <option value="">Todas las prioridades</option>
                            <option value="urgente" {{ request('priority') === 'urgente' ? 'selected' : '' }}>Urgente</option>
                            <option value="alta" {{ request('priority') === 'alta' ? 'selected' : '' }}>Alta</option>
                            <option value="media" {{ request('priority') === 'media' ? 'selected' : '' }}>Media</option>
                            <option value="baja" {{ request('priority') === 'baja' ? 'selected' : '' }}>Baja</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit" class="w-full py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition">
                            Filtrar
                        </button>
                        @if(request()->hasAny(['search', 'project_id', 'status', 'priority', 'assignee_id']))
                            <a href="{{ route('activities.index') }}" class="px-3 py-2 text-xs text-slate-500 hover:text-slate-800 underline">
                                Limpiar
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Activities Table -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <div>
                        Total de actividades encontradas: <strong class="text-slate-800">{{ $activities->total() }}</strong>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 uppercase text-[10px] tracking-wider font-bold">
                                <th class="p-4">Actividad y Proyecto</th>
                                <th class="p-4">Integrantes Asignados</th>
                                <th class="p-4">Prioridad</th>
                                <th class="p-4">Compromiso</th>
                                <th class="p-4">Estado</th>
                                <th class="p-4">Evidencia</th>
                                <th class="p-4 text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse ($activities as $act)
                                @php
                                    $prioBadge = match($act->priority) {
                                        'urgente' => 'bg-red-50 text-red-700 border-red-200',
                                        'alta' => 'bg-orange-50 text-orange-700 border-orange-200',
                                        'media' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        default => 'bg-slate-100 text-slate-600 border-slate-200',
                                    };
                                    $statBadge = match($act->status) {
                                        'completada' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'en_progreso' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'en_revision' => 'bg-purple-50 text-purple-700 border-purple-200',
                                        'pendiente' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        default => 'bg-red-50 text-red-700 border-red-200',
                                    };
                                    $evidenceComments = $act->comments->whereNotNull('evidence_path');
                                    $isOverdue = $act->is_overdue;
                                @endphp
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="p-4 max-w-sm">
                                        <div class="font-bold text-slate-900 text-xs">{{ $act->title }}</div>
                                        <div class="text-[11px] text-slate-400 mt-0.5 line-clamp-1">{{ $act->description ?? 'Sin descripción' }}</div>
                                        <div class="mt-1">
                                            <a href="{{ route('projects.show', $act->project) }}" class="inline-flex items-center gap-1 text-[11px] font-semibold text-[#1573F7] hover:underline">
                                                <span>{{ $act->project?->code }}</span> • {{ $act->project?->name }}
                                            </a>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            @forelse ($act->assignees as $assignee)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 text-slate-800 border border-slate-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-[#1573F7]"></span>
                                                    {{ $assignee->name }}
                                                </span>
                                            @empty
                                                <span class="text-slate-400 italic text-[11px]">Sin asignar</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border {{ $prioBadge }}">
                                            {{ ucfirst($act->priority) }}
                                        </span>
                                    </td>
                                    <td class="p-4 whitespace-nowrap">
                                        <span class="font-medium {{ $isOverdue ? 'text-red-600 font-bold' : 'text-slate-700' }}">
                                            {{ $act->due_date ? $act->due_date->format('d/m/Y') : 'N/D' }}
                                        </span>
                                        @if($isOverdue)
                                            <span class="block text-[10px] text-red-500 font-semibold">¡Atrasada!</span>
                                        @endif
                                    </td>
                                    <td class="p-4 whitespace-nowrap">
                                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full border {{ $statBadge }}">
                                            {{ $act->status === 'pendiente' ? 'Por empezar' : ucfirst(str_replace('_', ' ', $act->status)) }}
                                        </span>
                                    </td>
                                    <td class="p-4 min-w-32">
                                        @forelse ($evidenceComments as $evidenceComment)
                                            <a href="{{ route('activities.comments.evidence', $evidenceComment) }}" target="_blank" rel="noopener" class="block text-[11px] text-[#1573F7] hover:underline truncate max-w-36" title="{{ $evidenceComment->evidence_name }}">
                                                {{ $evidenceComment->evidence_name }}
                                            </a>
                                        @empty
                                            <span class="text-[11px] text-slate-400">Sin archivos</span>
                                        @endforelse
                                    </td>
                                    <td class="p-4 text-right whitespace-nowrap">
                                        <button type="button"
                                                onclick="openEditModal({{ $act->id }}, @js($act->title), @js($act->description), @js($act->priority), @js($act->due_date ? $act->due_date->format('Y-m-d') : ''), @js($act->status), @js($act->assignees->pluck('id')))"
                                                class="px-2.5 py-1 rounded-md text-xs font-bold text-[#1573F7] hover:bg-blue-50 transition">
                                            Actualizar
                                        </button>
                                        <button type="button" onclick="openActivityDeleteModal(@js(route('activities.destroy', $act)), @js($act->title), {{ $act->id }})" class="px-2.5 py-1 rounded-md text-xs font-bold text-red-600 hover:bg-red-50 transition">
                                            Eliminar
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-10 text-center text-slate-400">
                                        No se encontraron actividades con los filtros seleccionados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-slate-100">
                    {{ $activities->links() }}
                </div>
            </div>

        </div>
    </div>

    @php
        $deleteActivity = $activities->firstWhere('id', old('activity_id'));
    @endphp
    <div id="modal-eliminar-actividad" class="{{ $errors->has('admin_password') && $deleteActivity ? '' : 'hidden' }} fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="delete-activity-title">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-red-100">
            <div class="flex items-start gap-4">
                <div class="shrink-0 flex items-center justify-center w-11 h-11 rounded-full bg-amber-100 text-amber-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.3 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.7 3.86a2 2 0 0 0-3.4 0Z"/>
                    </svg>
                </div>
                <div>
                    <h3 id="delete-activity-title" class="font-bold text-lg text-slate-900">Confirmar eliminación</h3>
                    <p class="mt-1 text-sm text-slate-600">Se eliminará la actividad <strong id="delete-activity-name">{{ $deleteActivity?->title }}</strong>. Esta acción no se puede deshacer.</p>
                </div>
            </div>

            <form id="form-eliminar-actividad" method="POST" action="{{ $deleteActivity ? route('activities.destroy', $deleteActivity) : '' }}" class="mt-5 space-y-4">
                @csrf
                @method('DELETE')
                <input id="delete-activity-id" type="hidden" name="activity_id" value="{{ old('activity_id', $deleteActivity?->id) }}">
                <div>
                    <label for="delete-activity-admin-password" class="block text-xs font-semibold text-slate-700 mb-1">Contraseña de un administrador</label>
                    <input id="delete-activity-admin-password" type="password" name="admin_password" required autocomplete="current-password" class="w-full text-sm rounded-lg border-slate-300 focus:border-red-500 focus:ring-red-500">
                    @error('admin_password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('modal-eliminar-actividad').classList.add('hidden')" class="px-4 py-2 rounded-lg border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50">Cancelar</button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white text-xs font-bold">Eliminar actividad</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Crear Actividad -->
    <div id="modal-actividad" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-200">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="font-bold text-lg text-[#2F295B]">Registrar Nueva Actividad</h3>
                <button type="button" onclick="document.getElementById('modal-actividad').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <form method="POST" action="{{ route('activities.store') }}" class="mt-4 space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Proyecto *</label>
                    <select name="project_id" class="w-full text-xs rounded-lg border-slate-200" required>
                        <option value="">-- Seleccionar Proyecto --</option>
                        @foreach ($projects as $p)
                            <option value="{{ $p->id }}">{{ $p->code }} - {{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Título de la Actividad *</label>
                    <input type="text" name="title" placeholder="Ej. Diseñar interfaz de usuario" required class="w-full text-xs rounded-lg border-slate-200">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Descripción</label>
                    <textarea name="description" rows="3" placeholder="Detalles de la tarea..." class="w-full text-xs rounded-lg border-slate-200"></textarea>
                </div>

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
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Estado</label>
                    <select name="status" required class="w-full text-xs rounded-lg border-slate-200">
                        <option value="pendiente">Por empezar</option>
                        <option value="en_progreso">En progreso</option>
                        <option value="en_revision">En revisión</option>
                        <option value="completada">Completada</option>
                        <option value="atrasada">Atrasada</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Fecha de Compromiso</label>
                    <input type="date" name="due_date" class="w-full text-xs rounded-lg border-slate-200">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Encargados de la actividad * <span class="text-red-500 font-bold">(Mínimo 1 obligatorio)</span>
                    </label>
                    <select name="assignees[]" id="create-assignees" multiple required class="w-full text-xs rounded-lg border-slate-200 h-28 focus:border-[#1573F7] focus:ring-[#1573F7]">
                        @foreach ($users as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->role?->label }})</option>
                        @endforeach
                    </select>
                    <p class="text-[10px] text-slate-400 mt-1">Mantén presionado Ctrl (o Cmd) para seleccionar uno o varios integrantes.</p>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modal-actividad').classList.add('hidden')" class="px-4 py-2 border border-slate-200 rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-50">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2 bg-[#1573F7] hover:bg-[#0d62d9] text-white rounded-lg text-xs font-bold shadow-sm">
                        Crear Actividad
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Actualizar Actividad -->
    <div id="modal-update-actividad" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="font-bold text-base text-[#2F295B]" id="update-modal-title">Actualizar Actividad</h3>
                <button type="button" onclick="document.getElementById('modal-update-actividad').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <form id="form-update-actividad" method="POST" action="" enctype="multipart/form-data" class="mt-4 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Título *</label>
                    <input type="text" name="title" id="edit-title" required class="w-full text-xs rounded-lg border-slate-200">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Descripción</label>
                    <textarea name="description" id="edit-description" rows="3" class="w-full text-xs rounded-lg border-slate-200"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Prioridad</label>
                    <select name="priority" id="edit-priority" required class="w-full text-xs rounded-lg border-slate-200">
                        <option value="baja">Baja</option>
                        <option value="media">Media</option>
                        <option value="alta">Alta</option>
                        <option value="urgente">Urgente</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Estado</label>
                    <select name="status" id="edit-status" required class="w-full text-xs rounded-lg border-slate-200">
                        <option value="pendiente">Por empezar</option>
                        <option value="en_progreso">En progreso</option>
                        <option value="en_revision">En revisión</option>
                        <option value="completada">Completada</option>
                        <option value="atrasada">Atrasada</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Fecha de Compromiso</label>
                    <input type="date" name="due_date" id="edit-due-date" class="w-full text-xs rounded-lg border-slate-200">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Encargados de la actividad * <span class="text-red-500 font-bold">(Mínimo 1 obligatorio)</span>
                    </label>
                    <select name="assignees[]" id="edit-assignees" multiple required class="w-full text-xs rounded-lg border-slate-200 h-28 focus:border-[#1573F7] focus:ring-[#1573F7]">
                        @foreach ($users as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->role?->label }})</option>
                        @endforeach
                    </select>
                    <p class="text-[10px] text-slate-400 mt-1">Selecciona uno o más encargados. No se puede dejar el campo sin encargados.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Comentario o nota</label>
                    <textarea name="comment" rows="3" placeholder="Escribe una nota sobre esta actividad..." class="w-full text-xs rounded-lg border-slate-200"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Imagen de evidencia (opcional)</label>
                    <input type="file" name="evidence" accept="image/jpeg,image/png,image/webp" class="w-full text-xs rounded-lg border border-slate-200 p-2">
                    <p class="mt-1 text-[10px] text-slate-400">JPG, PNG o WebP; máximo 5 MB.</p>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modal-update-actividad').classList.add('hidden')" class="px-4 py-2 border border-slate-200 rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-50">
                        Cerrar
                    </button>
                    <button type="submit" class="px-5 py-2 bg-[#1573F7] hover:bg-[#0d62d9] text-white rounded-lg text-xs font-bold shadow-sm">
                        Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openActivityDeleteModal(action, title, activityId) {
            document.getElementById('form-eliminar-actividad').action = action;
            document.getElementById('delete-activity-name').innerText = title;
            document.getElementById('delete-activity-id').value = activityId;
            document.getElementById('delete-activity-admin-password').value = '';
            document.getElementById('modal-eliminar-actividad').classList.remove('hidden');
            document.getElementById('delete-activity-admin-password').focus();
        }

        function openEditModal(id, title, description, priority, dueDate, status, assigneeIds = []) {
            document.getElementById('form-update-actividad').action = '/activities/' + id;
            document.getElementById('update-modal-title').innerText = title;
            document.getElementById('edit-title').value = title;
            document.getElementById('edit-description').value = description || '';
            document.getElementById('edit-priority').value = priority;
            document.getElementById('edit-due-date').value = dueDate;
            document.getElementById('edit-status').value = status;

            const select = document.getElementById('edit-assignees');
            if (select) {
                const selectedIds = Array.isArray(assigneeIds) ? assigneeIds.map(Number) : [];
                Array.from(select.options).forEach(opt => {
                    opt.selected = selectedIds.includes(Number(opt.value));
                });
            }

            document.getElementById('modal-update-actividad').classList.remove('hidden');
        }

        document.getElementById('form-update-actividad')?.addEventListener('submit', function(e) {
            const select = document.getElementById('edit-assignees');
            if (select && select.selectedOptions.length === 0) {
                e.preventDefault();
                alert('Debe asignar al menos un encargado a la actividad.');
            }
        });

        document.querySelector('#modal-actividad form')?.addEventListener('submit', function(e) {
            const select = document.getElementById('create-assignees');
            if (select && select.selectedOptions.length === 0) {
                e.preventDefault();
                alert('Debe asignar al menos un encargado a la actividad.');
            }
        });
    </script>
</x-app-layout>
