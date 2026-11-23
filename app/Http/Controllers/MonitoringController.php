<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityComment;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MonitoringController extends Controller
{
    public function index(Request $request): View
    {
        $activitiesQuery = Activity::with(['project', 'assignees', 'comments.user']);
        $commentsQuery = ActivityComment::with(['user.role', 'activity.project']);

        if ($request->filled('project_id')) {
            $activitiesQuery->where('project_id', $request->project_id);
            $commentsQuery->whereHas('activity', function ($q) use ($request) {
                $q->where('project_id', $request->project_id);
            });
        }

        if ($request->filled('status')) {
            $activitiesQuery->where('status', $request->status);
        }

        if ($request->filled('user_id')) {
            $userId = $request->user_id;
            $activitiesQuery->whereHas('assignees', function ($q) use ($userId) {
                $q->where('users.id', $userId);
            });
            $commentsQuery->where('user_id', $userId);
        }

        if ($request->filled('source')) {
            $commentsQuery->where('source', $request->source);
        }

        $activities = $activitiesQuery->latest('due_date')->paginate(10, ['*'], 'activities_page');
        $comments = $commentsQuery->latest()->paginate(15, ['*'], 'comments_page');

        $projects = Project::orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return view('monitoring.index', compact('activities', 'comments', 'projects', 'users'));
    }

    public function storeComment(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'activity_id' => ['required', 'exists:activities,id'],
            'comment' => ['required', 'string'],
            'progress' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $activity = Activity::findOrFail($validated['activity_id']);
        if (isset($validated['progress'])) {
            $isComplete = $validated['progress'] == 100;
            $activity->update([
                'progress' => $validated['progress'],
                'status' => $isComplete
                    ? 'completada'
                    : ($activity->status === 'completada' ? 'en_progreso' : $activity->status),
                'completed_at' => $isComplete ? ($activity->completed_at ?? now()) : null,
            ]);
        }

        ActivityComment::create([
            'activity_id' => $activity->id,
            'user_id' => auth()->id(),
            'comment' => $validated['comment'],
            'progress_reported' => $validated['progress'] ?? $activity->progress,
            'source' => 'web',
        ]);

        return back()->with('status', 'Comentario de seguimiento registrado exitosamente.');
    }
}
