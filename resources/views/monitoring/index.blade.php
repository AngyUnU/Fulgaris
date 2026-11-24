<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Centro de Control</span>
                <h1 class="text-2xl font-extrabold text-[#2F295B] tracking-tight">Supervisión y Seguimiento de Avances</h1>
            </div>

            <div class="flex items-center gap-2">
                <span class="text-xs px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-semibold flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Sincronización Web & Móvil activa
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Information Callout -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200/80 rounded-xl p-5 shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#1573F7] text-white flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-[#2F295B]">Centro de Sincronización ArkanA</h4>
                        <p class="text-xs text-slate-600 mt-0.5 leading-relaxed">
                            Aquí se centralizan los cambios de estado, comentarios y evidencias enviados desde la <strong>aplicación móvil</strong> (Ionic / Angular) y la <strong>plataforma web</strong>.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('monitoring.index', ['source' => 'mobile']) }}" class="px-3 py-1.5 rounded-lg border {{ request('source') === 'mobile' ? 'bg-[#5F3AD7] text-white border-transparent' : 'bg-white border-slate-200 text-slate-700' }} text-xs font-bold transition">
                        Solo Móvil
                    </a>
                    <a href="{{ route('monitoring.index', ['source' => 'web']) }}" class="px-3 py-1.5 rounded-lg border {{ request('source') === 'web' ? 'bg-[#1573F7] text-white border-transparent' : 'bg-white border-slate-200 text-slate-700' }} text-xs font-bold transition">
                        Solo Web
                    </a>
                    @if(request()->filled('source'))
                        <a href="{{ route('monitoring.index') }}" class="text-xs text-slate-500 hover:underline">Todos</a>
                    @endif
                </div>
            </div>

            <!-- 2 Columns: Feed of Updates (Left) & Quick Supervision Matrix (Right) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Left 2 cols: Live Feed of Reported Comments -->
                <div class="lg:col-span-2 space-y-4">
                    <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                            <div>
                                <h3 class="font-bold text-base text-[#2F295B]">Historial de Actividades y Notas</h3>
                                <p class="text-xs text-slate-500">Comentarios de seguimiento registrados por el equipo</p>
                            </div>
                            <span class="text-xs font-semibold text-slate-400">Total: {{ $comments->total() }}</span>
                        </div>

                        <div class="p-5 space-y-4">
                            @forelse ($comments as $comm)
                                @php
                                    $isMobile = $comm->source === 'mobile';
                                @endphp
                                <div class="p-4 rounded-xl border {{ $isMobile ? 'border-purple-200/80 bg-purple-50/20' : 'border-slate-200 bg-white' }} space-y-2 hover:shadow-xs transition">
                                    <div class="flex items-center justify-between gap-3 text-xs">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-full {{ $isMobile ? 'bg-[#5F3AD7] text-white' : 'bg-[#1573F7] text-white' }} flex items-center justify-center font-bold text-xs shrink-0">
                                                {{ strtoupper(substr($comm->user?->name ?? 'U', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-900 leading-tight">{{ $comm->user?->name }}</div>
                                                <div class="text-[10px] text-slate-400">{{ $comm->user?->role?->label ?? 'Integrante' }}</div>
                                            </div>
                                        </div>

                                        <div class="text-right">
                                            <div class="flex items-center gap-1.5 justify-end">
                                                @if($isMobile)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-[#5F3AD7] border border-purple-200">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                                        App Móvil
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-[#1573F7] border border-blue-200">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                                        Web
                                                    </span>
                                                @endif
                                            </div>
                                            <span class="text-[10px] text-slate-400 block mt-0.5">{{ $comm->created_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                    </div>

                                    <!-- Comment body -->
                                    <div class="text-xs text-slate-700 bg-white/80 p-3 rounded-lg border border-slate-100 leading-relaxed font-normal">
                                        {{ $comm->comment }}
                                    </div>

                                    @if($comm->evidence_path)
                                        <a href="{{ route('activities.comments.evidence', $comm) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 text-xs font-semibold text-[#1573F7] hover:underline">
                                            <span aria-hidden="true">↗</span>
                                            Abrir evidencia: {{ $comm->evidence_name }}
                                        </a>
                                    @endif

                                    <!-- Activity context link -->
                                    @if($comm->activity)
                                        <div class="text-[11px] text-slate-500 pt-1 flex items-center justify-between">
                                            <span>
                                                Actividad: <strong class="text-slate-800">{{ $comm->activity->title }}</strong>
                                            </span>
                                            <span class="font-mono text-[10px] text-slate-400">
                                                {{ $comm->activity->project?->code }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="p-8 text-center text-slate-400 text-xs">
                                    No hay comentarios registrados.
                                </div>
                            @endforelse

                            <div>
                                {{ $comments->links() }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Col: Add feedback comment & Quick Status Filter -->
                <div class="space-y-6">

                    <!-- Form Agregar Nota de Seguimiento -->
                    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs">
                        <h4 class="font-bold text-sm text-[#2F295B] mb-1">Registrar Observación</h4>
                        <p class="text-xs text-slate-500 mb-4">Añadir una nota de retroalimentación o actualización a una actividad.</p>

                        <form method="POST" action="{{ route('monitoring.comments.store') }}" enctype="multipart/form-data" class="space-y-3">
                            @csrf

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Actividad *</label>
                                <select name="activity_id" required class="w-full text-xs rounded-lg border-slate-200">
                                    <option value="">-- Seleccionar Actividad --</option>
                                    @foreach ($activities as $act)
                                        <option value="{{ $act->id }}">{{ $act->project?->code }}: {{ $act->title }} ({{ $act->status === 'pendiente' ? 'Por empezar' : ucfirst(str_replace('_', ' ', $act->status)) }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Comentario *</label>
                                <textarea name="comment" rows="3" placeholder="Escribe una nota sobre la actividad..." required class="w-full text-xs rounded-lg border-slate-200"></textarea>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Imagen de evidencia (opcional)</label>
                                <input type="file" name="evidence" accept="image/jpeg,image/png,image/webp" class="w-full text-xs rounded-lg border border-slate-200 p-2">
                                <p class="mt-1 text-[10px] text-slate-400">JPG, PNG o WebP; máximo 5 MB.</p>
                            </div>

                            <button type="submit" class="w-full py-2 bg-[#1573F7] hover:bg-[#0d62d9] text-white text-xs font-bold rounded-lg shadow-sm transition">
                                Publicar Seguimiento
                            </button>
                        </form>
                    </div>

                    <!-- Filter by Assignee quick list -->
                    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs">
                        <h4 class="font-bold text-sm text-[#2F295B] mb-2">Integrantes del Equipo</h4>
                        <p class="text-xs text-slate-500 mb-3">Filtrar actividades y avances por integrante</p>

                        <div class="space-y-2">
                            @foreach ($users as $u)
                                <a href="{{ route('monitoring.index', ['user_id' => $u->id]) }}" class="flex items-center justify-between p-2 rounded-lg hover:bg-slate-50 transition text-xs {{ request('user_id') == $u->id ? 'bg-blue-50 text-[#1573F7] font-bold' : 'text-slate-700' }}">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-slate-200 text-slate-700 text-[10px] font-bold flex items-center justify-center">
                                            {{ strtoupper(substr($u->name, 0, 1)) }}
                                        </div>
                                        <span>{{ $u->name }}</span>
                                    </div>
                                    <span class="text-[10px] text-slate-400">{{ $u->role?->label }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>
</x-app-layout>
