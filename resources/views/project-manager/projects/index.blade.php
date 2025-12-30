@extends('layouts.app')

@section('title', 'My Projects')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        <i class="bi bi-folder"></i> My Projects
    </h1>
    <div class="d-flex">
        <a href="{{ route('project-manager.projects.trashed') }}" class="btn btn-outline-secondary me-2">
            <i class="bi bi-trash"></i> Trashed Projects
        </a>
        <a href="{{ route('project-manager.projects.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> New Project
        </a>
    </div>
</div>

@if($projects->isEmpty())
<div class="text-center py-5">
    <i class="bi bi-folder-x" style="font-size: 3rem; color: #6c757d;"></i>
    <h4 class="mt-3">No Projects Found</h4>
    <p class="text-muted">Create your first project to get started</p>
    <a href="{{ route('project-manager.projects.create') }}" class="btn btn-primary mt-2">
        <i class="bi bi-plus-circle"></i> Create Project
    </a>
</div>
@else
<div class="row">
    @foreach($projects as $project)
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">{{ Str::limit($project->name, 25) }}</h6>
                <span class="badge bg-{{ $project->status == 'active' ? 'success' : ($project->status == 'completed' ? 'info' : 'warning') }}">
                    {{ ucfirst($project->status) }}
                </span>
            </div>
            <div class="card-body">
                <p class="card-text text-muted small">{{ Str::limit($project->description, 100) }}</p>
                
                <div class="mb-3">
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
                    <div>
                        <small class="text-muted">
                            <i class="bi bi-calendar"></i> 
                            {{ $project->deadline ? $project->deadline->format('d/m/Y') : 'No deadline' }}
                        </small>
                    </div>
                    <div>
                        <span class="badge bg-secondary">
                            <i class="bi bi-list-task"></i> {{ $project->tasks_count ?? 0 }}
                        </span>
                    </div>
                </div>
                
                <div class="mt-3">
                    <small class="text-muted">
                        <i class="bi bi-person"></i> 
                        Manager: {{ $project->manager->name ?? 'N/A' }}
                    </small>
                </div>
            </div>
            <div class="card-footer bg-transparent">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('project-manager.projects.show', $project) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye"></i> View
                    </a>
                    @can('update', $project)
                    <a href="{{ route('project-manager.projects.edit', $project) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="d-flex justify-content-center">
    {{ $projects->links() }}
</div>
@endif
@endsection