<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    /**
     * Display a listing of the projects for member.
     */
    public function index()
    {
        $userId = Auth::id(); // Menggunakan Auth::id() untuk Laravel 12
        
        // Get projects where the authenticated member is assigned
        $projects = Project::whereHas('tasks', function ($query) use ($userId) {
                $query->where('assigned_to', $userId);
            })
            ->orWhere('created_by', $userId)
            ->withCount(['tasks' => function ($query) use ($userId) {
                $query->where('assigned_to', $userId);
            }])
            ->latest()
            ->get();
            
        return view('member.projects.index', compact('projects'));
    }

    /**
     * Display the specified project.
     */
    public function show(Project $project)
    {
        $userId = Auth::id(); // Menggunakan Auth::id() untuk Laravel 12
        
        // Check if member has access to this project
        $hasAccess = $project->tasks()->where('assigned_to', $userId)->exists() 
                   || $project->created_by == $userId;
        
        if (!$hasAccess) {
            abort(403, 'Unauthorized access to this project.');
        }
        
        // Get tasks assigned to the member in this project
        $tasks = $project->tasks()
            ->where('assigned_to', $userId)
            ->with('assignedUser')
            ->latest()
            ->get();
            
        return view('member.projects.show', compact('project', 'tasks'));
    }
}