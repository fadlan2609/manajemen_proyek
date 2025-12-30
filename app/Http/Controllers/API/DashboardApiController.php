<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardApiController extends Controller
{
    /**
     * Get dashboard statistics
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function stats()
    {
        /** @var User $user */
        $user = Auth::user();
        
        $data = [
            'total_projects' => 0,
            'active_projects' => 0,
            'total_tasks' => 0,
            'completed_tasks' => 0,
            'overdue_tasks' => 0,
        ];

        if ($user->isAdmin()) {
            $data['total_projects'] = Project::count();
            $data['active_projects'] = Project::active()->count();
            $data['total_tasks'] = Task::count();
            $data['completed_tasks'] = Task::done()->count();
            $data['overdue_tasks'] = Task::overdue()->count();
        } elseif ($user->isProjectManager()) {
            $managedProjects = $user->managedProjects()->pluck('id');
            $data['total_projects'] = $managedProjects->count();
            $data['active_projects'] = $user->managedProjects()->active()->count();
            $data['total_tasks'] = Task::whereIn('project_id', $managedProjects)->count();
            $data['completed_tasks'] = Task::whereIn('project_id', $managedProjects)->done()->count();
            $data['overdue_tasks'] = Task::whereIn('project_id', $managedProjects)->overdue()->count();
        } else {
            $data['total_tasks'] = $user->assignedTasks()->count();
            $data['completed_tasks'] = $user->assignedTasks()->done()->count();
            $data['overdue_tasks'] = $user->assignedTasks()->overdue()->count();
        }

        return response()->json($data);
    }

    /**
     * Get recent activities
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function recentActivities()
    {
        /** @var User $user */
        $user = Auth::user();
        $activities = [];

        if ($user->isAdmin()) {
            $activities = Project::latest()->take(5)->get()
                ->map(function($project) {
                    return [
                        'type' => 'project',
                        'title' => $project->name,
                        'description' => 'Project ' . $project->status,
                        'date' => $project->created_at->diffForHumans(),
                    ];
                });
        } elseif ($user->isProjectManager()) {
            $activities = $user->managedProjects()->latest()->take(5)->get()
                ->map(function($project) {
                    return [
                        'type' => 'project',
                        'title' => $project->name,
                        'description' => 'Progress: ' . $project->progress . '%',
                        'date' => $project->updated_at->diffForHumans(),
                    ];
                });
        } else {
            $activities = $user->assignedTasks()->with('project')->latest()->take(5)->get()
                ->map(function($task) {
                    return [
                        'type' => 'task',
                        'title' => $task->title,
                        'description' => 'Status: ' . $task->status,
                        'date' => $task->updated_at->diffForHumans(),
                    ];
                });
        }

        return response()->json($activities);
    }

    /**
     * Get projects progress data
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function projectsProgress()
    {
        /** @var User $user */
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            $projects = Project::select('name', 'progress')
                ->orderBy('progress', 'desc')
                ->limit(10)
                ->get();
        } elseif ($user->isProjectManager()) {
            $projects = $user->managedProjects()
                ->select('name', 'progress')
                ->orderBy('progress', 'desc')
                ->get();
        } else {
            $projects = $user->projects()
                ->select('name', 'progress')
                ->orderBy('progress', 'desc')
                ->get();
        }

        return response()->json($projects);
    }
}