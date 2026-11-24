<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-xl font-black text-[#2F295B] tracking-tight">Iniciar Sesión</h2>
        <p class="text-xs text-slate-500 mt-1">Plataforma Web de Gestión • <strong>ArkanA</strong></p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Correo Electrónico" />
            <x-text-input id="email" class="block mt-1 w-full text-xs" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="correo@ejemplo.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" value="Contraseña" />

            <x-text-input id="password" class="block mt-1 w-full text-xs"
                            type="password"
                            name="password"
                            required autocomplete="current-password"
                            placeholder="••••••••" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-[#1573F7] shadow-xs focus:ring-[#1573F7]" name="remember" checked>
                <span class="ms-2 text-xs text-slate-600">Mantener sesión iniciada</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-6">
            <button type="submit" class="px-5 py-2.5 rounded-lg bg-[#1573F7] hover:bg-[#0d62d9] text-white text-xs font-bold shadow-sm transition">
                Ingresar a ArkanA
            </button>
        </div>

        <div class="mt-6 pt-4 border-t border-slate-100 text-center text-xs text-slate-500">
            Las cuentas y los roles son administrados por el administrador de ArkanA.
        </div>
    </form>
</x-guest-layout>
