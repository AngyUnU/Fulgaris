<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityComment;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
            'status' => ['required', 'in:pendiente,en_progreso,en_revision,completada,atrasada'],
            'due_date' => ['nullable', 'date'],
            'assignees' => ['required', 'array', 'min:1'],
            'assignees.*' => ['exists:users,id'],
        ], [
            'assignees.required' => 'Debe asignar al menos un encargado a la actividad.',
            'assignees.min' => 'Debe asignar al menos un encargado a la actividad.',
            'assignees.*.exists' => 'Uno de los encargados seleccionados no es válido.',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['completed_at'] = $validated['status'] === 'completada' ? now() : null;
        $assignees = $validated['assignees'];
        unset($validated['assignees']);

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
            'status' => ['required', 'in:pendiente,en_progreso,en_revision,completada,atrasada'],
            'due_date' => ['nullable', 'date'],
            'assignees' => ['required', 'array', 'min:1'],
            'assignees.*' => ['exists:users,id'],
            'comment' => ['nullable', 'string', 'max:2000'],
            'evidence' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'assignees.required' => 'Debe haber al menos un encargado asignado a la actividad.',
            'assignees.min' => 'Debe haber al menos un encargado asignado a la actividad.',
            'assignees.*.exists' => 'Uno de los encargados seleccionados no es válido.',
        ]);

        $validated['completed_at'] = $validated['status'] === 'completada'
            ? ($activity->completed_at ?? now())
            : null;

        $assignees = $validated['assignees'];
        $comment = $validated['comment'] ?? null;
        unset($validated['assignees'], $validated['comment'], $validated['evidence']);

        $activity->update($validated);
        $activity->assignees()->sync($assignees);

        if ($comment || $request->hasFile('evidence')) {
            ActivityComment::createWithEvidence([
                'activity_id' => $activity->id,
                'user_id' => auth()->id(),
                'comment' => $comment ?: 'Evidencia adjunta.',
                'source' => 'web',
            ], $request->file('evidence'));
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

        $evidencePaths = ActivityComment::where('activity_id', $activity->id)
            ->whereNotNull('evidence_path')
            ->pluck('evidence_path');
        Storage::disk('local')->delete($evidencePaths->all());
        $activity->delete();

        return back()->with('status', 'Actividad eliminada.');
    }

    public function evidence(ActivityComment $comment): StreamedResponse
    {
        abort_unless($comment->evidence_path, 404);

        return Storage::disk('local')->response(
            $comment->evidence_path,
            $comment->evidence_name,
            ['X-Content-Type-Options' => 'nosniff'],
            'inline'
        );
    }
}
