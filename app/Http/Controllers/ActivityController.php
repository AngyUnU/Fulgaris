<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityComment;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function index(Request $request): View
    {
        $query = Activity::with(['project', 'assignees', 'comments.user']);

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('assignee_id')) {
            $assigneeId = $request->assignee_id;
            $query->whereHas('assignees', function ($q) use ($assigneeId) {
                $q->where('users.id', $assigneeId);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $activities = $query->latest('due_date')->paginate(12)->withQueryString();
        $projects = Project::orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return view('activities.index', compact('activities', 'projects', 'users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['required', 'in:baja,media,alta,urgente'],
            'status' => ['required', 'in:pendiente,en_progreso,completada,atrasada'],
            'progress' => ['required', 'integer', 'min:0', 'max:100'],
            'due_date' => ['nullable', 'date'],
            'assignees' => ['nullable', 'array'],
            'assignees.*' => ['exists:users,id'],
        ]);

        $validated['created_by'] = auth()->id();
        $assignees = $validated['assignees'] ?? [];
        unset($validated['assignees']);

        if ($validated['progress'] == 100) {
            $validated['status'] = 'completada';
            $validated['completed_at'] = now();
        } else {
            $validated['completed_at'] = null;
            if ($validated['status'] === 'completada') {
                $validated['status'] = 'en_progreso';
            }
        }

        $activity = Activity::create($validated);
        $activity->assignees()->sync($assignees);

        return back()->with('status', '¡Actividad creada y asignada con éxito!');
    }

    public function update(Request $request, Activity $activity): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['required', 'in:baja,media,alta,urgente'],
            'status' => ['required', 'in:pendiente,en_progreso,completada,atrasada'],
            'progress' => ['required', 'integer', 'min:0', 'max:100'],
            'due_date' => ['nullable', 'date'],
            'assignees' => ['nullable', 'array'],
            'assignees.*' => ['exists:users,id'],
            'comment' => ['nullable', 'string'],
        ]);

        if ($validated['progress'] == 100) {
            $validated['status'] = 'completada';
            $validated['completed_at'] = $activity->completed_at ?? now();
        } else {
            $validated['completed_at'] = null;
            if ($validated['status'] === 'completada') {
                $validated['status'] = 'en_progreso';
            }
        }

        $assignees = $validated['assignees'] ?? null;
        $comment = $validated['comment'] ?? null;
        unset($validated['assignees'], $validated['comment']);

        $activity->update($validated);

        if ($assignees !== null) {
            $activity->assignees()->sync($assignees);
        }

        if (! empty($comment)) {
            ActivityComment::create([
                'activity_id' => $activity->id,
                'user_id' => auth()->id(),
                'comment' => $comment,
                'progress_reported' => $activity->progress,
                'source' => 'web',
            ]);
        }

        return back()->with('status', 'Actividad actualizada.');
    }

    public function destroy(Request $request, Activity $activity): RedirectResponse
    {
        $validated = $request->validate([
            'admin_password' => ['required', 'string'],
            'activity_id' => ['required', 'integer', 'in:'.$activity->id],
        ]);

        if (! User::verifiesAdminPassword($validated['admin_password'])) {
            throw ValidationException::withMessages([
                'admin_password' => 'La contraseña del administrador no es correcta.',
            ]);
        }

        $activity->delete();

        return back()->with('status', 'Actividad eliminada.');
    }
}
