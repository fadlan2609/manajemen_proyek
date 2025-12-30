<?php

namespace App\Http\Controllers\ProjectManager;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class ProjectController extends Controller
{
    /**
     * Constructor untuk authorization
     */
    public function __construct()
    {
        // Middleware untuk memastikan hanya admin dan project manager yang bisa akses
        $this->middleware(function ($request, $next) {
            if (!Auth::check()) {
                return redirect()->route('login');
            }
            
            /** @var \App\Models\User $user */
            $user = Auth::user();
            
            // Cek jika user adalah admin atau project manager
            if (!$user->isAdmin() && !$user->isProjectManager()) {
                abort(403, 'Unauthorized action. You must be a Project Manager or Admin to access this page.');
            }
            
            return $next($request);
        });
    }

    /**
     * Display a listing of the projects.
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Admin bisa lihat semua project
        if ($user->isAdmin()) {
            $projects = Project::with(['manager', 'tasks'])
                              ->latest()
                              ->paginate(10);
        } 
        // Project manager hanya lihat project yang mereka manage atau yang mereka anggota
        else {
            $projects = Project::with(['manager', 'tasks'])
                              ->where(function($query) use ($user) {
                                  $query->where('project_manager_id', $user->id)
                                        ->orWhereHas('members', function($q) use ($user) {
                                            $q->where('user_id', $user->id);
                                        });
                              })
                              ->latest()
                              ->paginate(10);
        }
        
        return view('project-manager.projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new project.
     */
    public function create()
    {
        // Get all users except current user
        $members = User::where('id', '!=', Auth::id())
                      ->whereIn('role', ['project_manager', 'member']) // Hanya ambil project manager dan member
                      ->get();
        
        // Get project managers untuk dipilih sebagai project manager
        $projectManagers = User::where(function($query) {
            $query->where('role', 'project_manager')
                  ->orWhere('role', 'admin');
        })->get();
        
        return view('project-manager.projects.create', compact('members', 'projectManagers'));
    }

    /**
     * Store a newly created project in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|min:3',
            'description' => 'nullable|string|max:1000',
            'deadline' => 'nullable|date|after_or_equal:today',
            'project_manager_id' => 'required|exists:users,id',
            'members' => 'nullable|array',
            'members.*' => 'exists:users,id',
            //'budget' => 'nullable|numeric|min:0',
        ]);

        $project = Project::create([
            'name' => $request->name,
            'description' => $request->description,
            'project_manager_id' => $request->project_manager_id,
            'deadline' => $request->deadline,
            'status' => 'active',
            'progress' => 0.00,
            //'budget' => $request->budget ?? 0,
        ]);

        // Attach members if provided
        if ($request->has('members')) {
            $project->members()->attach($request->members);
            
            // Log activity
            Log::info('Project created', [
                'project_id' => $project->id,
                'project_name' => $project->name,
                'created_by' => Auth::id(),
                'members_count' => count($request->members),
            ]);
        }

        return redirect()->route('project-manager.projects.index')
                         ->with('success', 'Project created successfully!');
    }

    /**
     * Display the specified project.
     */
    public function show(Project $project)
    {
        // Authorization using policy
        $this->authorize('view', $project);
        
        // Load relationships with counts
        $project->load([
            'manager',
            'tasks' => function($query) {
                $query->orderBy('priority', 'desc')
                      ->orderBy('deadline', 'asc');
            },
            'tasks.assignee',
            'members'
        ]);
        
        // Get task statistics
        $taskStats = [
            'total' => $project->tasks->count(),
            'todo' => $project->tasks->where('status', 'todo')->count(),
            'in_progress' => $project->tasks->where('status', 'in_progress')->count(),
            'done' => $project->tasks->where('status', 'done')->count(),
            'overdue' => $project->tasks->filter(function($task) {
                return $task->isOverdue();
            })->count(),
        ];
        
        return view('project-manager.projects.show', compact('project', 'taskStats'));
    }

    /**
     * Show the form for editing the specified project.
     */
    public function edit(Project $project)
    {
        // Authorization using policy
        $this->authorize('update', $project);
        
        // Get all project managers (admin dan project manager)
        $projectManagers = User::where(function($query) {
            $query->where('role', 'project_manager')
                  ->orWhere('role', 'admin');
        })->get();
        
        // Get all users for members (exclude current user dan users yang sudah jadi member)
        $existingMemberIds = $project->members->pluck('id')->push($project->project_manager_id);
        $availableUsers = User::where('id', '!=', Auth::id())
                            ->whereNotIn('id', $existingMemberIds)
                            ->get();
        
        $project->load('members');
        
        return view('project-manager.projects.edit', compact('project', 'projectManagers', 'availableUsers'));
    }

    /**
     * Update the specified project in storage.
     */
    public function update(Request $request, Project $project)
    {
        // Authorization using policy
        $this->authorize('update', $project);
        
        $request->validate([
            'name' => 'required|string|max:255|min:3',
            'description' => 'nullable|string|max:1000',
            'deadline' => 'nullable|date',
            'status' => 'required|in:active,completed,on_hold',
            'project_manager_id' => 'required|exists:users,id',
            'members' => 'nullable|array',
            'members.*' => 'exists:users,id',
            'progress' => 'nullable|numeric|min:0|max:100',
            //'budget' => 'nullable|numeric|min:0',
        ]);

        // Update project
        $project->update([
            'name' => $request->name,
            'description' => $request->description,
            'deadline' => $request->deadline,
            'status' => $request->status,
            'project_manager_id' => $request->project_manager_id,
            'progress' => $request->progress ?? $project->progress,
            //'budget' => $request->budget ?? $project->budget,
        ]);

        // Sync members if provided
        if ($request->has('members')) {
            $project->members()->sync($request->members);
        }

        // Update progress if status changed to completed
        if ($request->status == 'completed' && $project->progress < 100) {
            $project->update(['progress' => 100]);
        }

        return redirect()->route('project-manager.projects.show', $project)
                         ->with('success', 'Project updated successfully!');
    }

    /**
     * Remove the specified project from storage.
     */
    public function destroy(Project $project)
    {
        // Authorization using policy
        $this->authorize('delete', $project);
        
        $projectName = $project->name;
        
        // Log deletion
        Log::info('Project deleted (soft delete)', [
            'project_id' => $project->id,
            'project_name' => $projectName,
            'deleted_by' => Auth::id(),
        ]);
        
        $project->delete();

        return redirect()->route('project-manager.projects.index')
                         ->with('success', "Project '{$projectName}' has been deleted!");
    }

    /**
     * Show trashed projects.
     */
public function trashed()
{
    /** @var \App\Models\User $user */
    $user = Auth::user();
    
    // Cek authorization - siapa yang bisa melihat trash?
    if (!$user->isAdmin() && !$user->isProjectManager()) {
        abort(403, 'Unauthorized action.');
    }
    
    $projects = Project::onlyTrashed()
                      ->when(!$user->isAdmin(), function($query) use ($user) {
                          return $query->where('project_manager_id', $user->id);
                      })
                      ->with(['manager', 'members'])
                      ->latest('deleted_at')
                      ->paginate(10);
    
    return view('project-manager.projects.trashed', compact('projects'));
}

    /**
     * Restore a trashed project.
     */
    public function restore($id)
    {
        $project = Project::onlyTrashed()->findOrFail($id);
        
        // Authorization
        $this->authorize('restore', $project);
        
        // Log restoration
        Log::info('Project restored', [
            'project_id' => $project->id,
            'project_name' => $project->name,
            'restored_by' => Auth::id(),
        ]);
        
        $project->restore();

        return redirect()->route('project-manager.projects.trashed')
                         ->with('success', 'Project restored successfully!');
    }

    /**
     * Permanently delete a project.
     */
    public function forceDelete($id)
    {
        $project = Project::onlyTrashed()->findOrFail($id);
        
        // Authorization - hanya admin yang bisa permanent delete
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403, 'Only administrators can permanently delete projects.');
        }
        
        $projectName = $project->name;
        
        // Log permanent deletion
        Log::warning('Project permanently deleted', [
            'project_id' => $project->id,
            'project_name' => $projectName,
            'deleted_by' => Auth::id(),
        ]);
        
        $project->forceDelete();

        return redirect()->route('project-manager.projects.trashed')
                         ->with('success', "Project '{$projectName}' has been permanently deleted!");
    }

    /**
     * Update project progress manually.
     */
    public function updateProgress(Request $request, Project $project)
    {
        $this->authorize('update', $project);
        
        $request->validate([
            'progress' => 'required|numeric|min:0|max:100',
        ]);

        $oldProgress = $project->progress;
        
        $project->update([
            'progress' => $request->progress,
            'status' => $request->progress == 100 ? 'completed' : $project->status,
        ]);

        // Log progress update
        Log::info('Project progress updated', [
            'project_id' => $project->id,
            'project_name' => $project->name,
            'old_progress' => $oldProgress,
            'new_progress' => $request->progress,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->back()
                         ->with('success', 'Project progress updated!');
    }

    /**
     * Add member to project.
     */
    public function addMember(Request $request, Project $project)
    {
        $this->authorize('update', $project);
        
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        // Check if user is already a member
        if ($project->members()->where('user_id', $request->user_id)->exists()) {
            return redirect()->back()
                             ->with('error', 'User is already a member of this project.');
        }

        $user = User::findOrFail($request->user_id);
        $project->members()->attach($request->user_id);

        // Log member addition
        Log::info('Member added to project', [
            'project_id' => $project->id,
            'project_name' => $project->name,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'added_by' => Auth::id(),
        ]);

        return redirect()->back()
                         ->with('success', 'Member added to project!');
    }

    /**
     * Remove member from project.
     */
    public function removeMember(Request $request, Project $project, User $user)
    {
        $this->authorize('update', $project);
        
        // Prevent removing project manager
        if ($project->project_manager_id == $user->id) {
            return redirect()->back()
                             ->with('error', 'Cannot remove project manager from project.');
        }

        $project->members()->detach($user->id);

        // Log member removal
        Log::info('Member removed from project', [
            'project_id' => $project->id,
            'project_name' => $project->name,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'removed_by' => Auth::id(),
        ]);

        return redirect()->back()
                         ->with('success', 'Member removed from project!');
    }

    /**
     * Get project statistics for dashboard.
     */
    public function statistics(Project $project)
    {
        $this->authorize('view', $project);
        
        $statistics = [
            'tasks_by_status' => [
                'todo' => $project->tasks()->where('status', 'todo')->count(),
                'in_progress' => $project->tasks()->where('status', 'in_progress')->count(),
                'done' => $project->tasks()->where('status', 'done')->count(),
            ],
            'tasks_by_priority' => [
                'high' => $project->tasks()->where('priority', 'high')->count(),
                'medium' => $project->tasks()->where('priority', 'medium')->count(),
                'low' => $project->tasks()->where('priority', 'low')->count(),
            ],
            'overdue_tasks' => $project->tasks()->overdue()->count(),
            'total_members' => $project->members()->count() + 1, // +1 for project manager
            'days_remaining' => $project->deadline ? now()->diffInDays($project->deadline, false) : null,
        ];

        return response()->json($statistics);
    }

    /**
     * Export project data.
     */
    public function export(Project $project, $format = 'pdf')
    {
        $this->authorize('view', $project);
        
        // Load data
        $project->load('tasks.assignee', 'members', 'manager');
        
        // Return based on format
        if ($format == 'json') {
            return response()->json($project);
        }
        
        // For PDF and other formats, you would use a PDF library
        // This is just a placeholder
        return redirect()->back()
                         ->with('info', 'Export feature coming soon!');
    }

    /**
     * Clone/Copy a project.
     */
    public function cloneProject(Project $project)
    {
        $this->authorize('create', Project::class);
        
        // Create new project with same data
        $newProject = $project->replicate();
        $newProject->name = $project->name . ' (Copy)';
        $newProject->status = 'active';
        $newProject->progress = 0;
        $newProject->project_manager_id = Auth::id();
        $newProject->save();
        
        // Clone tasks
        foreach ($project->tasks as $task) {
            $newTask = $task->replicate();
            $newTask->project_id = $newProject->id;
            $newTask->status = 'todo';
            $newTask->progress = 0;
            $newTask->save();
        }
        
        // Clone members
        $newProject->members()->attach($project->members->pluck('id'));

        // Log cloning
        Log::info('Project cloned', [
            'original_project_id' => $project->id,
            'original_project_name' => $project->name,
            'new_project_id' => $newProject->id,
            'new_project_name' => $newProject->name,
            'cloned_by' => Auth::id(),
        ]);

        return redirect()->route('project-manager.projects.show', $newProject)
                         ->with('success', 'Project cloned successfully!');
    }
}