@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        <i class="bi bi-speedometer2"></i> Dashboard
        <small class="text-muted fs-6">
            @if(isset($dashboardType))
                ({{ ucfirst(str_replace('_', ' ', $dashboardType)) }})
            @endif
        </small>
    </h1>
    <div class="d-flex">
        <span class="badge bg-secondary me-2">Last updated: {{ now()->format('H:i') }}</span>
        <button class="btn btn-sm btn-outline-primary" onclick="refreshDashboard()">
            <i class="bi bi-arrow-clockwise"></i> Refresh
        </button>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    @if($user->isAdmin())
        <!-- Admin Stats -->
        <div class="col-md-3 mb-3">
            <div class="card border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted">Total Users</h6>
                            <h3 id="totalUsers">{{ $totalUsers ?? 0 }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-people text-primary" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted">Total Projects</h6>
                            <h3 id="totalProjects">{{ $totalProjects ?? 0 }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-folder text-success" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted">Active Projects</h6>
                            <h3 id="activeProjects">{{ $activeProjects ?? 0 }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-activity text-warning" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted">Total Tasks</h6>
                            <h3 id="totalTasks">{{ $totalTasks ?? 0 }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-list-task text-info" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    @elseif($user->isProjectManager())
        <!-- Project Manager Stats -->
        <div class="col-md-3 mb-3">
            <div class="card border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted">Managed Projects</h6>
                            <h3 id="totalProjects">{{ $totalProjects ?? 0 }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-folder text-primary" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted">Active Projects</h6>
                            <h3 id="activeProjects">{{ $activeProjects ?? 0 }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-activity text-success" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted">Total Tasks</h6>
                            <h3 id="totalTasks">{{ $totalTasks ?? 0 }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-list-task text-warning" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted">Overdue Tasks</h6>
                            <h3 id="overdueTasks">{{ $overdueTasks ?? 0 }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-exclamation-triangle text-danger" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    @else
        <!-- Member Stats -->
        <div class="col-md-3 mb-3">
            <div class="card border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted">My Projects</h6>
                            <h3 id="totalProjects">{{ $projects->count() ?? 0 }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-folder text-primary" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted">Assigned Tasks</h6>
                            <h3 id="totalTasks">{{ $totalTasks ?? 0 }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-list-task text-success" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted">Completed Tasks</h6>
                            <h3 id="completedTasks">{{ $completedTasks ?? 0 }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-check-circle text-info" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted">Overdue Tasks</h6>
                            <h3 id="overdueTasks">{{ $overdueTasks ?? 0 }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-exclamation-triangle text-danger" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Recent Items Section -->
<div class="row">
    @if($user->isAdmin())
        <!-- Admin Recent Items -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-folder"></i> Recent Projects
                    </h5>
                </div>
                <div class="card-body">
                    @if(isset($recentProjects) && $recentProjects->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentProjects as $project)
                            <a href="{{ route('project-manager.projects.show', $project) }}" 
                               class="list-group-item list-group-item-action">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $project->name }}</strong>
                                        <div class="small text-muted">
                                            {{ Str::limit($project->description, 50) }}
                                        </div>
                                    </div>
                                    <div>
                                        <span class="badge bg-{{ $project->status == 'active' ? 'success' : ($project->status == 'completed' ? 'info' : 'warning') }}">
                                            {{ ucfirst($project->status) }}
                                        </span>
                                        <span class="badge bg-primary ms-1">
                                            {{ $project->progress }}%
                                        </span>
                                    </div>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center py-3">No recent projects</p>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-people"></i> Recent Users
                    </h5>
                </div>
                <div class="card-body">
                    @if(isset($recentUsers) && $recentUsers->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentUsers as $userItem)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $userItem->name }}</strong>
                                        <div class="small text-muted">{{ $userItem->email }}</div>
                                    </div>
                                    <div>
                                        <span class="badge bg-{{ $userItem->role == 'admin' ? 'danger' : ($userItem->role == 'project_manager' ? 'warning' : 'info') }}">
                                            {{ ucfirst(str_replace('_', ' ', $userItem->role)) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center py-3">No recent users</p>
                    @endif
                </div>
            </div>
        </div>
        
    @elseif($user->isProjectManager())
        <!-- Project Manager Recent Projects -->
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-folder"></i> Managed Projects
                    </h5>
                    <a href="{{ route('project-manager.projects.index') }}" class="btn btn-sm btn-outline-primary">
                        View All
                    </a>
                </div>
                <div class="card-body">
                    @if(isset($managedProjects) && $managedProjects->count() > 0)
                        <div class="row">
                            @foreach($managedProjects as $project)
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h6 class="card-title">{{ Str::limit($project->name, 30) }}</h6>
                                        <p class="card-text small text-muted">
                                            {{ Str::limit($project->description, 60) }}
                                        </p>
                                        
                                        <div class="mb-2">
                                            <div class="d-flex justify-content-between mb-1">
                                                <small>Progress</small>
                                                <small>{{ $project->progress }}%</small>
                                            </div>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-{{ $project->progress >= 80 ? 'success' : ($project->progress >= 50 ? 'info' : 'warning') }}" 
                                                     role="progressbar" style="width: {{ $project->progress }}%"></div>
                                            </div>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-{{ $project->status == 'active' ? 'success' : ($project->status == 'completed' ? 'info' : 'warning') }}">
                                                {{ ucfirst($project->status) }}
                                            </span>
                                            <span class="badge bg-secondary">
                                                <i class="bi bi-list-task"></i> {{ $project->tasks_count }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-transparent">
                                        <a href="{{ route('project-manager.projects.show', $project) }}" 
                                           class="btn btn-sm btn-outline-primary w-100">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-folder-x" style="font-size: 3rem; color: #6c757d;"></i>
                            <p class="mt-3 text-muted">No projects managed yet</p>
                            <a href="{{ route('project-manager.projects.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Create Project
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
    @else
        <!-- Member Recent Tasks -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-list-task"></i> My Tasks
                    </h5>
                    <a href="{{ route('member.tasks.index') }}" class="btn btn-sm btn-outline-primary">
                        View All
                    </a>
                </div>
                <div class="card-body">
                    @if(isset($assignedTasks) && $assignedTasks->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($assignedTasks->take(10) as $task)
                            <div class="list-group-item priority-{{ $task->priority }}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ Str::limit($task->title, 40) }}</strong>
                                        <div class="small text-muted">
                                            Project: {{ $task->project->name ?? 'No Project' }}
                                        </div>
                                    </div>
                                    <div>
                                        <span class="badge bg-{{ $task->status == 'todo' ? 'secondary' : ($task->status == 'in_progress' ? 'info' : 'success') }}">
                                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                        </span>
                                        @if($task->isOverdue())
                                            <span class="badge bg-danger ms-1">Overdue</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center py-3">No assigned tasks</p>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Member Recent Projects -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-folder"></i> My Projects
                    </h5>
                </div>
                <div class="card-body">
                    @if(isset($projects) && $projects->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($projects as $project)
                            <a href="{{ route('project-manager.projects.show', $project) }}" 
                               class="list-group-item list-group-item-action">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ Str::limit($project->name, 40) }}</strong>
                                        <div class="small text-muted">
                                            {{ Str::limit($project->description, 50) }}
                                        </div>
                                    </div>
                                    <div>
                                        <span class="badge bg-{{ $project->status == 'active' ? 'success' : ($project->status == 'completed' ? 'info' : 'warning') }}">
                                            {{ ucfirst($project->status) }}
                                        </span>
                                        <span class="badge bg-primary ms-1">
                                            {{ $project->progress }}%
                                        </span>
                                    </div>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center py-3">No projects assigned</p>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    // Dashboard Charts (hanya untuk admin dan project manager)
    @if($user->isAdmin() || $user->isProjectManager())
    let progressChart = null;

    function loadDashboardData() {
        // Load stats via API
        fetch('/api/dashboard/stats')
            .then(response => response.json())
            .then(data => {
                // Update stats cards jika elemen ada
                if(document.getElementById('totalProjects')) {
                    document.getElementById('totalProjects').textContent = data.total_projects || 0;
                }
                if(document.getElementById('activeProjects')) {
                    document.getElementById('activeProjects').textContent = data.active_projects || 0;
                }
                if(document.getElementById('totalTasks')) {
                    document.getElementById('totalTasks').textContent = data.total_tasks || 0;
                }
                if(document.getElementById('overdueTasks')) {
                    document.getElementById('overdueTasks').textContent = data.overdue_tasks || 0;
                }
            });

        // Load projects progress via API
        fetch('/api/dashboard/projects-progress')
            .then(response => response.json())
            .then(data => {
                if(data.length > 0) {
                    updateProgressChart(data);
                }
            });
    }

    function updateProgressChart(projects) {
        const ctx = document.getElementById('progressChart');
        if(!ctx) return;
        
        const chartCtx = ctx.getContext('2d');
        
        if (progressChart) {
            progressChart.destroy();
        }
        
        const labels = projects.map(p => p.name.length > 20 ? p.name.substring(0, 20) + '...' : p.name);
        const progress = projects.map(p => p.progress);
        
        progressChart = new Chart(chartCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Progress %',
                    data: progress,
                    backgroundColor: progress.map(p => 
                        p >= 80 ? '#28a745' : 
                        p >= 50 ? '#17a2b8' : 
                        '#ffc107'
                    ),
                    borderColor: '#2c3e50',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                }
            }
        });
    }

    function refreshDashboard() {
        loadDashboardData();
        // Show loading indicator
        const refreshBtn = document.querySelector('button[onclick="refreshDashboard()"]');
        const originalHtml = refreshBtn.innerHTML;
        refreshBtn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Refreshing...';
        refreshBtn.disabled = true;
        
        setTimeout(() => {
            refreshBtn.innerHTML = originalHtml;
            refreshBtn.disabled = false;
        }, 1000);
    }

    // Load data on page load
    document.addEventListener('DOMContentLoaded', loadDashboardData);
    
    // Auto-refresh every 60 seconds
    setInterval(loadDashboardData, 60000);
    @endif
</script>
@endpush

<!-- Add Progress Chart for Admin and Project Manager -->
@if($user->isAdmin() || $user->isProjectManager())
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Projects Progress Overview</h5>
            </div>
            <div class="card-body">
                <canvas id="progressChart" height="100"></canvas>
            </div>
        </div>
    </div>
</div>
@endif
@endsection