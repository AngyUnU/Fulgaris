<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! $user->role) {
            abort(403, 'Acceso no autorizado: No cuentas con un rol asignado.');
        }

        if (! empty($roles) && ! in_array($user->role->name, $roles, true)) {
            abort(403, 'Acceso denegado: Tu rol (' . $user->role->label . ') no tiene permisos para esta acción.');
        }

        return $next($request);
    }
}
