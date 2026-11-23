<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Control de Acceso y Cuentas</span>
                <h1 class="text-2xl font-extrabold text-[#2F295B] tracking-tight">Administración de Usuarios</h1>
            </div>

            <div class="flex items-center gap-3">
                <p class="text-xs text-slate-500">
                    Los integrantes utilizan estas credenciales para acceder a la <strong>aplicación móvil</strong>.
                </p>
                <button type="button" onclick="document.getElementById('modal-nuevo-usuario').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 rounded-lg bg-[#1573F7] hover:bg-[#0d62d9] text-white text-xs font-bold shadow-sm transition">
                    Crear cuenta
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Filter Bar -->
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
                <form method="GET" action="{{ route('users.index') }}" class="w-full flex flex-col sm:flex-row items-center gap-3">
                    <div class="w-full sm:w-80">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre o correo..." class="w-full text-xs rounded-lg border-slate-200 focus:border-[#1573F7] focus:ring-[#1573F7]">
                    </div>

                    <div class="w-full sm:w-60">
                        <select name="role_id" onchange="this.form.submit()" class="w-full text-xs rounded-lg border-slate-200 focus:border-[#1573F7] focus:ring-[#1573F7]">
                            <option value="">Todos los roles</option>
                            @foreach ($roles as $r)
                                <option value="{{ $r->id }}" {{ request('role_id') == $r->id ? 'selected' : '' }}>
                                    {{ $r->label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition">
                        Filtrar
                    </button>

                    @if(request()->hasAny(['search', 'role_id']))
                        <a href="{{ route('users.index') }}" class="text-xs text-slate-500 hover:text-slate-800 underline">
                            Limpiar
                        </a>
                    @endif
                </form>

                <div class="text-xs text-slate-500 shrink-0">
                    Total: <strong class="text-slate-800">{{ $users->total() }}</strong> cuentas registradas
                </div>
            </div>

            <!-- Users Table -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 uppercase text-[10px] tracking-wider font-bold">
                                <th class="p-4">Usuario</th>
                                <th class="p-4">Correo Electrónico</th>
                                <th class="p-4">Rol Asignado</th>
                                <th class="p-4">Actividades Asignadas</th>
                                <th class="p-4">Fecha de Registro</th>
                                @if(Auth::user()->isAdmin())
                                    <th class="p-4 text-right">Modificar Rol</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse ($users as $user)
                                @php
                                    $roleBadge = match($user->role?->name) {
                                        'admin' => 'bg-purple-100 text-purple-800 border-purple-300',
                                        'lider' => 'bg-blue-100 text-blue-800 border-blue-300',
                                        'desarrollador' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                                        'qa' => 'bg-amber-100 text-amber-800 border-amber-300',
                                        default => 'bg-slate-100 text-slate-700 border-slate-200',
                                    };
                                @endphp
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="p-4 font-semibold text-slate-900">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-full bg-[#1573F7] text-white flex items-center justify-center font-bold text-xs shrink-0">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="text-xs font-bold text-slate-800">{{ $user->name }}</div>
                                                <div class="text-[10px] text-slate-400">ID: #{{ $user->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 font-mono text-slate-600">
                                        {{ $user->email }}
                                    </td>
                                    <td class="p-4">
                                        <span class="text-[11px] font-semibold px-2.5 py-0.5 rounded-full border {{ $roleBadge }}">
                                            {{ $user->role?->label ?? 'Sin Rol' }}
                                        </span>
                                    </td>
                                    <td class="p-4 font-medium">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-bold">
                                            {{ $user->assignedActivities->count() }} tareas
                                        </span>
                                    </td>
                                    <td class="p-4 text-slate-500">
                                        {{ $user->created_at->format('d/m/Y') }}
                                    </td>
                                    @if(Auth::user()->isAdmin())
                                        <td class="p-4">
                                            <form method="POST" action="{{ route('users.role.update', $user) }}" class="flex flex-col items-end gap-2">
                                                @csrf
                                                @method('PATCH')
                                                <select name="role_id" class="text-[11px] py-1 px-2 rounded-lg border-slate-200 focus:border-[#1573F7]">
                                                    @foreach ($roles as $r)
                                                        <option value="{{ $r->id }}" {{ $user->role_id == $r->id ? 'selected' : '' }}>
                                                            {{ $r->label }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <input type="password" name="current_password" required autocomplete="current-password" aria-label="Contraseña actual del administrador" placeholder="Confirma con tu contraseña" class="w-48 text-[11px] py-1.5 rounded-lg border-slate-200 focus:border-[#1573F7]">
                                                <button type="submit" class="px-3 py-1.5 rounded-md bg-[#1573F7] text-white text-[11px] font-semibold hover:bg-[#0d62d9] transition">
                                                    Confirmar cambio
                                                </button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-slate-400">
                                        No se encontraron usuarios.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-slate-100">
                    {{ $users->links() }}
                </div>
            </div>

            <!-- Role Definitions Explanatory Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach ($roles as $r)
                    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs text-xs space-y-1">
                        <span class="font-bold text-slate-800 text-sm block">{{ $r->label }}</span>
                        <p class="text-slate-500 leading-relaxed text-[11px]">{{ $r->description }}</p>
                    </div>
                @endforeach
            </div>

        </div>
    </div>

    <div id="modal-nuevo-usuario" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-200">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="font-bold text-lg text-[#2F295B]">Crear cuenta de usuario</h3>
                    <p class="text-xs text-slate-500 mt-1">Asigna el rol según las responsabilidades de la persona.</p>
                </div>
                <button type="button" onclick="document.getElementById('modal-nuevo-usuario').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <form method="POST" action="{{ route('users.store') }}" class="mt-4 space-y-4">
                @csrf

                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">Nombre completo *</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required maxlength="255" autocomplete="name" class="w-full text-xs rounded-lg border-slate-200">
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Correo electrónico *</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required maxlength="255" autocomplete="email" class="w-full text-xs rounded-lg border-slate-200">
                </div>

                <div>
                    <label for="role_id" class="block text-xs font-semibold text-slate-700 mb-1">Rol *</label>
                    <select id="role_id" name="role_id" required class="w-full text-xs rounded-lg border-slate-200">
                        <option value="">Selecciona un rol</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">Contraseña inicial *</label>
                        <input id="password" type="password" name="password" required autocomplete="new-password" class="w-full text-xs rounded-lg border-slate-200">
                        <p class="mt-1 text-[10px] text-slate-500">Mínimo 8 caracteres, una mayúscula, un número y un símbolo.</p>
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1">Confirmar contraseña *</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="w-full text-xs rounded-lg border-slate-200">
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modal-nuevo-usuario').classList.add('hidden')" class="px-4 py-2 border border-slate-200 rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-50">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2 bg-[#1573F7] hover:bg-[#0d62d9] text-white rounded-lg text-xs font-bold shadow-sm">
                        Crear cuenta
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
