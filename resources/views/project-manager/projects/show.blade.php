@extends('layouts.app')

@section('title', $project->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">{{ $project->name }}</h1>
        <p class="text-muted mb-0">{{ $project->description }}</p>
    </div>
    <div class="d-flex">
        <a href="{{ route('project-manager.projects.tasks.create', $project) }}" class="btn btn-primary me-2">
            <i class="bi bi-plus-circle"></i> Add Task
        </a>
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-gear"></i> Actions
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('project-manager.projects.edit', $project) }}">
                    <i class="bi bi-pencil"></i> Edit Project
                </a></li>
                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#updateProgressModal">
                    <i class="bi bi-graph-up"></i> Update Progress
                </a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('project-manager.projects.clone', $project) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            <i class="bi bi-copy"></i> Clone Project
                        </button>
                    </form>
                </li>
                <li>
                    <form action="{{ route('project-manager.projects.destroy', $project) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="dropdown-item text-danger" 
                                onclick="return confirm('Are you sure you want to delete this project?')">
                            <i class="bi bi-trash"></i> Delete Project
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Project Stats -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <h1 class="display-6">{{ $project->progress }}%</h1>
                <p class="text-muted mb-0">Overall Progress</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <h1 class="display-6">{{ $taskStats['todo'] ?? 0 }}</h1>
                <p class="text-muted mb-0">To Do</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <h1 class="display-6">{{ $taskStats['in_progress'] ?? 0 }}</h1>
                <p class="text-muted mb-0">In Progress</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <h1 class="display-6">{{ $taskStats['done'] ?? 0 }}</h1>
                <p class="text-muted mb-0">Completed</p>
            </div>
        </div>
    </div>
</div>

<!-- Project Info -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Tasks</h5>
                <a href="{{ route('project-manager.projects.tasks.create', $project) }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-circle"></i> Add Task
                </a>
            </div>
            <div class="card-body">
                @if($project->tasks->isEmpty())
                <div class="text-center py-4">
                    <i class="bi bi-list-task" style="font-size: 3rem; color: #6c757d;"></i>
                    <p class="mt-3 text-muted">No tasks yet. Create your first task!</p>
                    <a href="{{ route('project-manager.projects.tasks.create', $project) }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Add Task
                    </a>
                </div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Assignee</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Deadline</th>
                                <th>Progress</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($project->tasks as $task)
                            <tr class="priority-{{ $task->priority }}">
                                <td>{{ $task->title }}</td>
                                <td>
                                    @if($task->assignee)
                                    <span class="badge bg-info">{{ $task->assignee->name }}</span>
                                    @else
                                    <span class="badge bg-secondary">Unassigned</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $task->priority == 'high' ? 'danger' : ($task->priority == 'medium' ? 'warning' : 'success') }}">
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $task->status == 'todo' ? 'secondary' : ($task->status == 'in_progress' ? 'info' : 'success') }}">
                                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                    </span>
                                </td>
                                <td>
                                    @if($task->deadline)
                                        {{ $task->deadline->format('d/m/Y') }}
                                        @if($task->isOverdue())
                                            <span class="badge bg-danger">Overdue</span>
                                        @endif
                                    @else
                                        No deadline
                                    @endif
                                </td>
                                <td>
                                    <div class="progress" style="height: 6px; width: 80px;">
                                        <div class="progress-bar bg-{{ $task->progress >= 80 ? 'success' : ($task->progress >= 50 ? 'info' : 'warning') }}" 
                                             role="progressbar" style="width: {{ $task->progress }}%"></div>
                                    </div>
                                    <small>{{ $task->progress }}%</small>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <!-- Edit Button dengan teks -->
                                        <a href="{{ route('project-manager.projects.tasks.edit', ['project' => $project, 'task' => $task]) }}" 
                                           class="btn btn-sm btn-outline-primary d-flex align-items-center">
                                            <i class="bi bi-pencil me-1"></i> Edit
                                        </a>
                                        
                                        <!-- Delete Button dengan teks -->
                                        <form action="{{ route('project-manager.projects.tasks.destroy', ['project' => $project, 'task' => $task]) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger d-flex align-items-center"
                                                    onclick="return confirm('Are you sure you want to delete this task?')">
                                                <i class="bi bi-trash me-1"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Project Details -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Project Details</h5>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Manager</dt>
                    <dd class="col-sm-8">{{ $project->manager->name ?? 'N/A' }}</dd>
                    
                    <dt class="col-sm-4">Status</dt>
                    <dd class="col-sm-8">
                        <span class="badge bg-{{ $project->status == 'active' ? 'success' : ($project->status == 'completed' ? 'info' : 'warning') }}">
                            {{ ucfirst($project->status) }}
                        </span>
                    </dd>
                    
                    <dt class="col-sm-4">Deadline</dt>
                    <dd class="col-sm-8">
                        {{ $project->deadline ? $project->deadline->format('d F Y') : 'No deadline' }}
                    </dd>
                    
                    <dt class="col-sm-4">Created</dt>
                    <dd class="col-sm-8">{{ $project->created_at->format('d/m/Y') }}</dd>
                    
                    <dt class="col-sm-4">Last Updated</dt>
                    <dd class="col-sm-8">{{ $project->updated_at->diffForHumans() }}</dd>
                </dl>
            </div>
        </div>
        
        <!-- Team Members -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Team Members</h5>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                    <i class="bi bi-plus-circle"></i> Add
                </button>
            </div>
            <div class="card-body">
                @if($project->members->isEmpty())
                <p class="text-muted text-center py-3">No team members assigned yet.</p>
                @else
                <ul class="list-group list-group-flush">
                    <!-- Project Manager -->
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $project->manager->name ?? 'No Manager' }}</strong>
                            <br>
                            <small class="text-muted">Project Manager</small>
                        </div>
                        <span class="badge bg-primary">Manager</span>
                    </li>
                    
                    <!-- Team Members -->
                    @foreach($project->members as $member)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $member->name }}</strong>
                            <br>
                            <small class="text-muted">{{ $member->email }}</small>
                        </div>
                        <div>
                            <span class="badge bg-{{ $member->role == 'admin' ? 'danger' : ($member->role == 'project_manager' ? 'warning' : 'info') }}">
                                {{ ucfirst(str_replace('_', ' ', $member->role)) }}
                            </span>
                            @can('removeMember', $project)
                            <form action="{{ route('project-manager.projects.removeMember', ['project' => $project, 'user' => $member]) }}" 
                                  method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger ms-1"
                                        onclick="return confirm('Remove {{ $member->name }} from project?')">
                                    <i class="bi bi-person-dash"></i>Delete
                                </button>
                            </form>
                            @endcan
                        </div>
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Update Progress Modal -->
<div class="modal fade" id="updateProgressModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('project-manager.projects.updateProgress', $project) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Update Project Progress</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="progress" class="form-label">Progress Percentage</label>
                        <input type="range" class="form-range" id="progress" name="progress" 
                               min="0" max="100" step="5" value="{{ $project->progress }}"
                               oninput="document.getElementById('progressValue').textContent = this.value + '%'">
                        <div class="d-flex justify-content-between">
                            <small>0%</small>
                            <span id="progressValue" class="fw-bold">{{ $project->progress }}%</span>
                            <small>100%</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Progress</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Member Modal -->
<div class="modal fade" id="addMemberModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('project-manager.projects.addMember', $project) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Team Member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="user_id" class="form-label">Select Member</label>
                        <select class="form-select" id="user_id" name="user_id" required>
                            <option value="">Choose a member...</option>
                            @php
                                $existingMemberIds = $project->members->pluck('id')->push($project->project_manager_id);
                                $availableMembers = App\Models\User::whereNotIn('id', $existingMemberIds)->get();
                            @endphp
                            @foreach($availableMembers as $user)
                            <option value="{{ $user->id }}">
                                {{ $user->name }} ({{ $user->email }}) - {{ $user->role_name }}
                            </option>
                            @endforeach
                        </select>
                        @if($availableMembers->isEmpty())
                        <div class="alert alert-info mt-2 mb-0">
                            <i class="bi bi-info-circle"></i> All users are already members of this project.
                        </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" {{ $availableMembers->isEmpty() ? 'disabled' : '' }}>
                        Add Member
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .priority-high {
        background-color: rgba(220, 53, 69, 0.05);
    }
    .priority-medium {
        background-color: rgba(255, 193, 7, 0.05);
    }
    .priority-low {
        background-color: rgba(25, 135, 84, 0.05);
    }
</style>
@endsection