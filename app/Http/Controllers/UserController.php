<?php

namespace App\Http\Controllers;

use App\Models\ActivityComment;
use App\Models\Agreement;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::with(['role', 'assignedActivities.project']);

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('name')->paginate(12)->withQueryString();
        $roles = Role::all();

        return view('users.index', compact('users', 'roles'));
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role_id' => ['required', 'exists:roles,id'],
            'current_password' => ['required', 'current_password'],
        ]);

        $user->update(['role_id' => $validated['role_id']]);

        return back()->with('status', "Rol de {$user->name} actualizado correctamente.");
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'role_id' => ['required', 'exists:roles,id'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $validated['role_id'],
            'password' => $validated['password'],
        ]);

        return redirect()->route('users.index')->with('status', 'La cuenta se creó correctamente.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            throw ValidationException::withMessages([
                'admin_password' => 'No puedes eliminar tu propia cuenta de administrador.',
            ]);
        }

        $validated = $request->validate([
            'admin_password' => ['required', 'string'],
        ]);

        if (! User::verifiesAdminPassword($validated['admin_password'])) {
            throw ValidationException::withMessages([
                'admin_password' => 'La contraseña del administrador no es correcta.',
            ]);
        }

        $userName = $user->name;

        DB::transaction(function () use ($user) {
            // Eliminar evidencias en disco de comentarios hechos por este usuario
            $evidencePaths = ActivityComment::where('user_id', $user->id)
                ->whereNotNull('evidence_path')
                ->pluck('evidence_path');
            if ($evidencePaths->isNotEmpty()) {
                Storage::disk('local')->delete($evidencePaths->all());
            }

            // Desvincular de todas las actividades asignadas
            $user->assignedActivities()->detach();

            // Si es líder o creador de proyectos, desvincular para mantener la integridad del proyecto
            Project::where('leader_id', $user->id)->update(['leader_id' => null]);
            Project::where('created_by', $user->id)->update(['created_by' => null]);

            // Si es responsable de acuerdos, desvincular
            Agreement::where('responsible_id', $user->id)->update(['responsible_id' => null]);
            Agreement::where('created_by', $user->id)->update(['created_by' => null]);

            // Revocar tokens API
            $user->tokens()->delete();

            // Eliminar cuenta
            $user->delete();
        });

        return redirect()->route('users.index')->with('status', "La cuenta de {$userName} fue eliminada permanentemente y desvinculada de todas las actividades y proyectos.");
    }
}
