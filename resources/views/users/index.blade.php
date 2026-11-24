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
                                    <th class="p-4 text-right">Gestión de Cuenta</th>
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
                                            <div class="flex flex-col items-end gap-2.5">
                                                <form method="POST" action="{{ route('users.role.update', $user) }}" class="flex flex-col items-end gap-1.5">
                                                    @csrf
                                                    @method('PATCH')
                                                    <div class="flex items-center gap-1.5">
                                                        <select name="role_id" class="text-[11px] py-1 px-2 rounded-lg border-slate-200 focus:border-[#1573F7]">
                                                            @foreach ($roles as $r)
                                                                <option value="{{ $r->id }}" {{ $user->role_id == $r->id ? 'selected' : '' }}>
                                                                    {{ $r->label }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <input type="password" name="current_password" required autocomplete="current-password" aria-label="Contraseña actual del administrador" placeholder="Contraseña admin..." class="w-36 text-[11px] py-1 px-2 rounded-lg border-slate-200 focus:border-[#1573F7]">
                                                        <button type="submit" class="px-2.5 py-1 rounded-md bg-[#1573F7] text-white text-[11px] font-semibold hover:bg-[#0d62d9] transition">
                                                            Cambiar
                                                        </button>
                                                    </div>
                                                </form>

                                                @if($user->id !== Auth::id())
                                                    <button type="button"
                                                            onclick="openUserDeleteModal(@js(route('users.destroy', $user)), @js($user->name), @js($user->email))"
                                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-bold text-red-600 hover:bg-red-50 hover:text-red-700 transition">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                        Eliminar cuenta
                                                    </button>
                                                @else
                                                    <span class="text-[10px] text-slate-400 italic">Cuenta activa actual</span>
                                                @endif
                                            </div>
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

    <!-- Modal Eliminar Cuenta de Usuario -->
    <div id="modal-eliminar-usuario" class="{{ $errors->has('admin_password') ? '' : 'hidden' }} fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="delete-user-title">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-red-100">
            <div class="flex items-start gap-4">
                <div class="shrink-0 flex items-center justify-center w-11 h-11 rounded-full bg-red-100 text-red-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.3 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.7 3.86a2 2 0 0 0-3.4 0Z"/>
                    </svg>
                </div>
                <div>
                    <h3 id="delete-user-title" class="font-bold text-lg text-slate-900">Eliminar cuenta de usuario</h3>
                    <p class="mt-1 text-xs text-slate-500">
                        Usuario a eliminar: <strong id="delete-user-name" class="text-slate-800"></strong>
                    </p>
                </div>
            </div>

            <!-- Alerta Destacada Requerida -->
            <div class="mt-4 p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs space-y-1.5">
                <div class="font-bold flex items-center gap-1.5 text-amber-950">
                    <svg class="w-4 h-4 text-amber-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span>⚠️ Advertencia de eliminación irreversible</span>
                </div>
                <p class="leading-relaxed">
                    Si este usuario es eliminado, <strong>no se podrá restaurar esta acción</strong>. Además, la cuenta <strong>será desvinculada y eliminada de todas las actividades asignadas y proyectos</strong> en los que participa.
                </p>
            </div>

            <form id="form-eliminar-usuario" method="POST" action="" class="mt-5 space-y-4">
                @csrf
                @method('DELETE')

                <div>
                    <label for="delete-user-admin-password" class="block text-xs font-semibold text-slate-700 mb-1">
                        Ingresa tu contraseña de administrador para confirmar:
                    </label>
                    <input id="delete-user-admin-password" type="password" name="admin_password" required autocomplete="current-password" placeholder="Tu contraseña de administrador..." class="w-full text-xs rounded-lg border-slate-300 focus:border-red-500 focus:ring-red-500">
                    @error('admin_password')
                        <p class="mt-1 text-xs text-red-600 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('modal-eliminar-usuario').classList.add('hidden')" class="px-4 py-2 rounded-lg border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white text-xs font-bold transition shadow-xs">
                        Eliminar cuenta permanentemente
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openUserDeleteModal(action, name, email) {
            document.getElementById('form-eliminar-usuario').action = action;
            document.getElementById('delete-user-name').innerText = name + ' (' + email + ')';
            document.getElementById('delete-user-admin-password').value = '';
            document.getElementById('modal-eliminar-usuario').classList.remove('hidden');
            document.getElementById('delete-user-admin-password').focus();
        }
    </script>
</x-app-layout>
