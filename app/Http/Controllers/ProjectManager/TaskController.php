<?php

namespace App\Http\Controllers\ProjectManager;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function create(Project $project)
    {
        $this->authorize('update', $project);
        
        $members = $project->members;
        return view('project-manager.tasks.create', compact('project', 'members'));
    }

    public function store(Request $request, Project $project)
    {
        $this->authorize('update', $project);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'priority' => 'required|in:low,medium,high',
            'deadline' => 'nullable|date',
        ]);

        Task::create([
            'project_id' => $project->id,
            'title' => $request->title,
            'description' => $request->description,
            'assigned_to' => $request->assigned_to,
            'priority' => $request->priority,
            'deadline' => $request->deadline,
            'status' => 'todo',
        ]);

        // Update project progress
        $project->updateProgress();

        return redirect()->route('project-manager.projects.show', $project)
                         ->with('success', 'Task created successfully.');
    }

    public function edit(Project $project, Task $task)
    {
        $this->authorize('update', $project);
        
        $members = $project->members;
        return view('project-manager.tasks.edit', compact('project', 'task', 'members'));
    }

    public function update(Request $request, Project $project, Task $task)
    {
        $this->authorize('update', $project);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'priority' => 'required|in:low,medium,high',
            'deadline' => 'nullable|date',
            'status' => 'required|in:todo,in_progress,done',
            'progress' => 'required|integer|min:0|max:100',
        ]);

        $task->update([
            'title' => $request->title,
            'description' => $request->description,
            'assigned_to' => $request->assigned_to,
            'priority' => $request->priority,
            'deadline' => $request->deadline,
            'status' => $request->status,
            'progress' => $request->progress,
        ]);

        // Update project progress
        $project->updateProgress();

        return redirect()->route('project-manager.projects.show', $project)
                         ->with('success', 'Task updated successfully.');
    }

    public function destroy(Project $project, Task $task)
    {
        $this->authorize('update', $project);
        
        $task->delete();

        // Update project progress
        $project->updateProgress();

        return redirect()->route('project-manager.projects.show', $project)
                         ->with('success', 'Task deleted successfully.');
    }
}