<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Minutas y Compromisos</span>
                <h1 class="text-2xl font-extrabold text-[#2F295B] tracking-tight">Gestión de Acuerdos</h1>
            </div>

            @if(Auth::user()->hasRole(['admin', 'lider']))
                <button type="button" onclick="document.getElementById('modal-acuerdo').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 rounded-lg bg-[#1573F7] hover:bg-[#0d62d9] text-white text-xs font-bold shadow-sm transition">
                    <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Registrar Acuerdo
                </button>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Filter Bar -->
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs">
                <form method="GET" action="{{ route('agreements.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                    <div class="sm:col-span-2">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar en título o descripción del acuerdo..." class="w-full text-xs rounded-lg border-slate-200 focus:border-[#1573F7] focus:ring-[#1573F7]">
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

                    <div class="flex items-center gap-2">
                        <select name="status" onchange="this.form.submit()" class="w-full text-xs rounded-lg border-slate-200 focus:border-[#1573F7] focus:ring-[#1573F7]">
                            <option value="">Todos los estados</option>
                            <option value="pendiente" {{ request('status') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="en_cumplimiento" {{ request('status') === 'en_cumplimiento' ? 'selected' : '' }}>En Cumplimiento</option>
                            <option value="cumplido" {{ request('status') === 'cumplido' ? 'selected' : '' }}>Cumplido</option>
                            <option value="vencido" {{ request('status') === 'vencido' ? 'selected' : '' }}>Vencido</option>
                        </select>
                        @if(request()->hasAny(['search', 'project_id', 'status']))
                            <a href="{{ route('agreements.index') }}" class="text-xs text-slate-500 hover:text-slate-800 underline px-2">
                                Limpiar
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Agreements Cards List -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse ($agreements as $agr)
                    @php
                        $statusStyle = match($agr->status) {
                            'cumplido' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'en_cumplimiento' => 'bg-blue-50 text-blue-700 border-blue-200',
                            'pendiente' => 'bg-amber-50 text-amber-700 border-amber-200',
                            default => 'bg-red-50 text-red-700 border-red-200',
                        };
                    @endphp
                    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs hover:shadow-md transition flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <span class="font-mono text-[11px] font-bold px-2 py-0.5 rounded bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $agr->project?->code }}
                                </span>
                                <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full border {{ $statusStyle }}">
                                    {{ ucfirst(str_replace('_', ' ', $agr->status)) }}
                                </span>
                            </div>

                            <h3 class="font-bold text-sm text-[#2F295B] mb-2">{{ $agr->title }}</h3>
                            <p class="text-xs text-slate-600 leading-relaxed mb-4">
                                {{ $agr->description }}
                            </p>

                            @if($agr->activity)
                                <div class="mb-3 px-3 py-1.5 rounded-lg bg-blue-50/50 border border-blue-100 text-[11px] text-[#1573F7]">
                                    <strong>Actividad vinculada:</strong> {{ $agr->activity->title }}
                                </div>
                            @endif
                        </div>

                        <div class="pt-3 border-t border-slate-100 text-xs text-slate-500 space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px]">Responsable:</span>
                                <span class="font-bold text-slate-800">{{ $agr->responsible?->name ?? 'Sin asignar' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[11px]">Fecha del acuerdo:</span>
                                <span class="text-slate-600">{{ $agr->agreement_date->format('d/m/Y') }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[11px]">Fecha compromiso:</span>
                                <span class="font-bold text-slate-700">{{ $agr->due_date ? $agr->due_date->format('d/m/Y') : 'Sin fecha límite' }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white rounded-xl border border-slate-200 p-12 text-center text-slate-400">
                        No hay acuerdos registrados que coincidan con la búsqueda.
                    </div>
                @endforelse
            </div>

            <div>
                {{ $agreements->links() }}
            </div>

        </div>
    </div>

    <!-- Modal Registrar Acuerdo -->
    <div id="modal-acuerdo" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-200">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="font-bold text-lg text-[#2F295B]">Registrar Nuevo Acuerdo</h3>
                <button type="button" onclick="document.getElementById('modal-acuerdo').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <form method="POST" action="{{ route('agreements.store') }}" class="mt-4 space-y-4">
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
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Título del Acuerdo *</label>
                    <input type="text" name="title" placeholder="Ej. Aprobación de arquitectura de software" required class="w-full text-xs rounded-lg border-slate-200">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Descripción del Compromiso *</label>
                    <textarea name="description" rows="3" placeholder="Puntos acordados en la reunión o minuta..." required class="w-full text-xs rounded-lg border-slate-200"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Vincular a Actividad (Opcional)</label>
                    <select name="activity_id" class="w-full text-xs rounded-lg border-slate-200">
                        <option value="">-- Sin actividad específica (Acuerdo general) --</option>
                        @foreach ($activities as $act)
                            <option value="{{ $act->id }}">{{ $act->project?->code }}: {{ $act->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Fecha del Acuerdo *</label>
                        <input type="date" name="agreement_date" value="{{ date('Y-m-d') }}" required class="w-full text-xs rounded-lg border-slate-200">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Fecha Límite</label>
                        <input type="date" name="due_date" class="w-full text-xs rounded-lg border-slate-200">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Responsable de Seguimiento</label>
                        <select name="responsible_id" class="w-full text-xs rounded-lg border-slate-200">
                            <option value="">-- Seleccionar --</option>
                            @foreach ($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Estado</label>
                        <select name="status" class="w-full text-xs rounded-lg border-slate-200" required>
                            <option value="pendiente">Pendiente</option>
                            <option value="en_cumplimiento" selected>En Cumplimiento</option>
                            <option value="cumplido">Cumplido</option>
                        </select>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modal-acuerdo').classList.add('hidden')" class="px-4 py-2 border border-slate-200 rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-50">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2 bg-[#1573F7] hover:bg-[#0d62d9] text-white rounded-lg text-xs font-bold shadow-sm">
                        Registrar Acuerdo
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

