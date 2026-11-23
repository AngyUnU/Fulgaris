<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Agreement;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AgreementController extends Controller
{
    public function index(Request $request): View
    {
        $query = Agreement::with(['project', 'activity', 'responsible', 'creator']);

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $agreements = $query->latest('agreement_date')->paginate(10)->withQueryString();
        $projects = Project::orderBy('name')->get();
        $activities = Activity::orderBy('title')->get();
        $users = User::orderBy('name')->get();

        return view('agreements.index', compact('agreements', 'projects', 'activities', 'users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'activity_id' => ['nullable', 'exists:activities,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'agreement_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date'],
            'status' => ['required', 'in:pendiente,en_cumplimiento,cumplido,vencido'],
            'responsible_id' => ['nullable', 'exists:users,id'],
        ]);

        $validated['created_by'] = auth()->id();

        Agreement::create($validated);

        return back()->with('status', '¡Acuerdo registrado con éxito!');
    }

    public function update(Request $request, Agreement $agreement): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'due_date' => ['nullable', 'date'],
            'status' => ['required', 'in:pendiente,en_cumplimiento,cumplido,vencido'],
            'responsible_id' => ['nullable', 'exists:users,id'],
        ]);

        $agreement->update($validated);

        return back()->with('status', 'Acuerdo actualizado.');
    }

    public function destroy(Agreement $agreement): RedirectResponse
    {
        $agreement->delete();

        return back()->with('status', 'Acuerdo eliminado.');
    }
}

