<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $data = [];

        if ($user->isAdmin()) {
            $data = $this->getAdminDashboardData();
        } elseif ($user->isProjectManager()) {
            $data = $this->getProjectManagerDashboardData($user);
        } else {
            $data = $this->getMemberDashboardData($user);
        }

        // Tambahkan data user ke semua view
        $data['user'] = $user;
        
        return view('dashboard', $data);
    }

    private function getAdminDashboardData(): array
    {
        return [
            'totalUsers' => User::count(),
            'totalProjects' => Project::count(),
            'totalTasks' => Task::count(),
            'activeProjects' => Project::active()->count(),
            'recentProjects' => Project::latest()->take(5)->get(),
            'recentUsers' => User::latest()->take(5)->get(),
            'dashboardType' => 'admin',
        ];
    }

    private function getProjectManagerDashboardData(User $user): array
    {
        $managedProjects = $user->managedProjects()->withCount('tasks')->get();
        
        return [
            'managedProjects' => $managedProjects,
            'totalProjects' => $managedProjects->count(),
            'activeProjects' => $managedProjects->where('status', 'active')->count(),
            'totalTasks' => Task::whereIn('project_id', $managedProjects->pluck('id'))->count(),
            'overdueTasks' => Task::whereIn('project_id', $managedProjects->pluck('id'))
                                 ->overdue()
                                 ->count(),
            'dashboardType' => 'project_manager',
        ];
    }

    private function getMemberDashboardData(User $user): array
    {
        $projects = $user->projects()->withCount('tasks')->get();
        $assignedTasks = $user->assignedTasks()->with('project')->get();
        
        return [
            'projects' => $projects,
            'assignedTasks' => $assignedTasks,
            'totalTasks' => $assignedTasks->count(),
            'completedTasks' => $assignedTasks->where('status', 'done')->count(),
            'overdueTasks' => $assignedTasks->filter(function($task) {
                return $task->isOverdue();
            })->count(),
            'dashboardType' => 'member',
        ];
    }
}