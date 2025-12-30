@extends('layouts.app')

@section('title', 'My Profile - Project Manager')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">My Profile (Project Manager)</h1>
                <div>
                    <a href="{{ route('project-manager.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column: Profile Info -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <!-- Avatar -->
                    <div class="mb-3">
                        @if($user->avatar_url)
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" 
                                 class="rounded-circle border" width="120" height="120">
                        @else
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-warning text-white" 
                                 style="width: 120px; height: 120px; font-size: 40px;">
                                {{ $user->initials }}
                            </div>
                        @endif
                    </div>
                    
                    <!-- User Info -->
                    <h3 class="h5 mb-1">{{ $user->name }}</h3>
                    <p class="text-muted mb-2">
                        <span class="badge bg-warning">Project Manager</span>
                        <span class="badge bg-{{ $user->status_color }} ms-1">{{ $user->status_name }}</span>
                    </p>
                    <p class="text-muted mb-3">
                        <i class="bi bi-envelope"></i> {{ $user->email }}<br>
                        @if($user->phone)
                            <i class="bi bi-phone"></i> {{ $user->phone }}<br>
                        @endif
                        @if($user->position)
                            <i class="bi bi-briefcase"></i> {{ $user->position }}<br>
                        @endif
                        @if($user->department)
                            <i class="bi bi-building"></i> {{ $user->department }}
                        @endif
                    </p>
                    
                    <!-- Statistics -->
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="h5 mb-0">{{ $stats['total_tasks'] ?? 0 }}</div>
                            <small class="text-muted">My Tasks</small>
                        </div>
                        <div class="col-4">
                            <div class="h5 mb-0">{{ $stats['managed_projects'] ?? 0 }}</div>
                            <small class="text-muted">Projects</small>
                        </div>
                        <div class="col-4">
                            <div class="h5 mb-0">{{ $stats['done'] ?? 0 }}</div>
                            <small class="text-muted">Completed</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Task Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>To Do</span>
                            <span>{{ $stats['todo'] ?? 0 }}</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-secondary" style="width: {{ (($stats['todo'] ?? 0) / max(($stats['total_tasks'] ?? 1), 1)) * 100 }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>In Progress</span>
                            <span>{{ $stats['in_progress'] ?? 0 }}</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary" style="width: {{ (($stats['in_progress'] ?? 0) / max(($stats['total_tasks'] ?? 1), 1)) * 100 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>Completed</span>
                            <span>{{ $stats['done'] ?? 0 }}</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: {{ (($stats['done'] ?? 0) / max(($stats['total_tasks'] ?? 1), 1)) * 100 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Actions -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Profile Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="#" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                            <i class="bi bi-pencil"></i> Edit Profile
                        </a>
                        <a href="#" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                            <i class="bi bi-key"></i> Change Password
                        </a>
                        <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                            <i class="bi bi-trash"></i> Delete Account
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Details and Managed Projects -->
        <div class="col-md-8">
            <!-- Managed Projects -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Managed Projects</h6>
                    <a href="{{ route('project-manager.projects.index') }}" class="btn btn-sm btn-outline-warning">View All</a>
                </div>
                <div class="card-body">
                    @if($user->managedProjects->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Project</th>
                                        <th>Status</th>
                                        <th>Progress</th>
                                        <th>Tasks</th>
                                        <th>Deadline</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($user->managedProjects->take(5) as $project)
                                    <tr>
                                        <td>
                                            <a href="{{ route('project-manager.projects.show', $project) }}">
                                                {{ Str::limit($project->name, 30) }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $project->status === 'completed' ? 'success' : ($project->status === 'in_progress' ? 'primary' : 'secondary') }}">
                                                {{ ucfirst($project->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar bg-success" style="width: {{ $project->progress }}%"></div>
                                            </div>
                                            <small>{{ $project->progress }}%</small>
                                        </td>
                                        <td>{{ $project->tasks_count ?? 0 }}</td>
                                        <td>
                                            @if($project->deadline)
                                                {{ $project->deadline->format('M d, Y') }}
                                            @else
                                                <span class="text-muted">No deadline</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-folder display-4 text-muted"></i>
                            <p class="mt-3 mb-0">No projects managed yet.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Tasks -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">My Recent Tasks</h6>
                    <a href="{{ route('member.tasks.index') }}" class="btn btn-sm btn-outline-warning">View All</a>
                </div>
                <div class="card-body">
                    @if($user->tasks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Task</th>
                                        <th>Project</th>
                                        <th>Status</th>
                                        <th>Priority</th>
                                        <th>Due Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($user->tasks->take(5) as $task)
                                    <tr>
                                        <td>
                                            <a href="{{ route('member.tasks.show', $task) }}">
                                                {{ Str::limit($task->title, 30) }}
                                            </a>
                                        </td>
                                        <td>
                                            @if($task->project)
                                                {{ $task->project->name }}
                                            @else
                                                <span class="text-muted">No Project</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $task->status === 'done' ? 'success' : ($task->status === 'in_progress' ? 'primary' : 'secondary') }}">
                                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $task->priority === 'high' ? 'danger' : ($task->priority === 'medium' ? 'warning' : 'success') }}">
                                                {{ ucfirst($task->priority) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($task->deadline)
                                                {{ $task->deadline->format('M d, Y') }}
                                                @if($task->deadline < now() && $task->status !== 'done')
                                                    <span class="badge bg-danger ms-1">Overdue</span>
                                                @endif
                                            @else
                                                <span class="text-muted">No deadline</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-clipboard-check display-4 text-muted"></i>
                            <p class="mt-3 mb-0">No tasks assigned yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals (sama seperti member profile) -->
<div class="modal fade" id="editProfileModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('project-manager.profile.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="{{ old('name', $user->name) }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="{{ old('email', $user->email) }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="phone" name="phone" 
                               value="{{ old('phone', $user->phone) }}">
                    </div>
                    <div class="mb-3">
                        <label for="position" class="form-label">Position</label>
                        <input type="text" class="form-control" id="position" name="position" 
                               value="{{ old('position', $user->position) }}">
                    </div>
                    <div class="mb-3">
                        <label for="department" class="form-label">Department</label>
                        <input type="text" class="form-control" id="department" name="department" 
                               value="{{ old('department', $user->department) }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="changePasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('project-manager.profile.password') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Change Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="current_password" 
                               name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="password" name="password" 
                               minlength="8" required>
                        <div class="form-text">Must be at least 8 characters long.</div>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="password_confirmation" 
                               name="password_confirmation" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Change Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Account Form -->
<form id="deleteAccountForm" action="{{ route('project-manager.profile.delete') }}" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
function confirmDelete() {
    if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
        document.getElementById('deleteAccountForm').submit();
    }
}
</script>
@endpush
@endsection