<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $query = Project::with(['leader', 'activities.assignees', 'agreements']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $projects = $query->latest()->paginate(9)->withQueryString();
        $leaders = User::whereHas('role', function ($q) {
            $q->whereIn('name', ['admin', 'lider']);
        })->get();
        $users = User::with('role')->orderBy('name')->get();

        return view('projects.index', compact('projects', 'leaders', 'users'));
    }

    public function show(Project $project): View
    {
        $project->load([
            'leader',
            'creator',
            'activities.assignees',
            'activities.comments.user',
            'agreements.responsible',
        ]);

        $users = User::with('role')->get();
        $leaders = User::whereHas('role', function ($query) {
            $query->whereIn('name', ['admin', 'lider']);
        })->get();

        return view('projects.show', compact('project', 'users', 'leaders'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:projects,code'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:planificacion,en_progreso,en_pausa,concluido'],
            'start_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'leader_id' => ['nullable', 'exists:users,id'],
            'activities' => ['nullable', 'array'],
            'activities.*.title' => ['required', 'string', 'max:255'],
            'activities.*.description' => ['nullable', 'string'],
            'activities.*.priority' => ['required', 'in:baja,media,alta,urgente'],
            'activities.*.status' => ['required', 'in:pendiente,en_progreso,completada,atrasada'],
            'activities.*.progress' => ['required', 'integer', 'min:0', 'max:100'],
            'activities.*.due_date' => ['nullable', 'date'],
            'activities.*.assignees' => ['nullable', 'array'],
            'activities.*.assignees.*' => ['integer', 'exists:users,id'],
        ], [
            'code.unique' => 'El código del proyecto ya existe.',
            'due_date.after_or_equal' => 'La fecha de compromiso debe ser posterior o igual a la fecha de inicio.',
        ]);

        $activities = $validated['activities'] ?? [];
        unset($validated['activities']);

        $project = DB::transaction(function () use ($validated, $activities) {
            $project = Project::create([
                ...$validated,
                'created_by' => auth()->id(),
            ]);

            foreach ($activities as $activityData) {
                $isComplete = $activityData['progress'] == 100;
                $activity = $project->activities()->create([
                    'title' => $activityData['title'],
                    'description' => $activityData['description'] ?? null,
                    'priority' => $activityData['priority'],
                    'status' => $isComplete
                        ? 'completada'
                        : ($activityData['status'] === 'completada' ? 'en_progreso' : $activityData['status']),
                    'progress' => $activityData['progress'],
                    'completed_at' => $isComplete ? now() : null,
                    'due_date' => $activityData['due_date'] ?? null,
                    'created_by' => auth()->id(),
                ]);
                $activity->assignees()->sync($activityData['assignees'] ?? []);
            }

            return $project;
        });

        return redirect()->route('projects.show', $project)->with('status', 'Proyecto y actividades creados correctamente.');
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:planificacion,en_progreso,en_pausa,concluido'],
            'start_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date'],
            'leader_id' => ['nullable', 'exists:users,id'],
        ]);

        $project->update($validated);

        return back()->with('status', 'Proyecto actualizado correctamente.');
    }

    public function destroy(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'admin_password' => ['required', 'string'],
        ]);

        if (! User::verifiesAdminPassword($validated['admin_password'])) {
            throw ValidationException::withMessages([
                'admin_password' => 'La contraseña del administrador no es correcta.',
            ]);
        }

        $project->delete();

        return redirect()->route('projects.index')->with('status', 'Proyecto eliminado.');
    }
}
