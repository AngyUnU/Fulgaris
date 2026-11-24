<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Agreement;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $projectsQuery = Project::with(['leader', 'activities']);
        $totalProjects = $projectsQuery->count();
        $activeProjects = (clone $projectsQuery)->where('status', 'en_progreso')->count();

        $activities = Activity::with(['project', 'assignees'])->get();
        $totalActivities = $activities->count();
        $inProgressActivities = $activities->where('status', 'en_progreso')->count();
        $completedActivities = $activities->where('status', 'completada')->count();

        $today = now()->toDateString();
        $overdueActivities = $activities->filter(function ($act) use ($today) {
            return $act->status !== 'completada' && $act->due_date && $act->due_date->toDateString() < $today;
        })->count();

        $overallProgress = $totalActivities > 0
            ? (int) round(($completedActivities / $totalActivities) * 100)
            : 0;
        $totalAgreements = Agreement::count();
        $pendingAgreements = Agreement::whereIn('status', ['pendiente', 'en_cumplimiento'])->count();

        $recentProjects = Project::with(['leader', 'activities'])
            ->latest()
            ->take(4)
            ->get();

        $urgentActivities = Activity::with(['project', 'assignees'])
            ->where('status', '!=', 'completada')
            ->orderByRaw('CASE WHEN due_date < ? THEN 0 ELSE 1 END, due_date ASC', [$today])
            ->take(5)
            ->get();

        $recentAgreements = Agreement::with(['project', 'responsible'])
            ->latest('agreement_date')
            ->take(4)
            ->get();

        return view('dashboard', compact(
            'totalProjects',
            'activeProjects',
            'totalActivities',
            'inProgressActivities',
            'completedActivities',
            'overdueActivities',
            'overallProgress',
            'totalAgreements',
            'pendingAgreements',
            'recentProjects',
            'urgentActivities',
            'recentAgreements'
        ));
    }
}
