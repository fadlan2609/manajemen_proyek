{{-- resources/views/project-manager/projects/trashed.blade.php --}}
@extends('layouts.app')

@section('title', 'Trashed Projects')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="bi bi-trash"></i> Trashed Projects
        </h1>
        <a href="{{ route('project-manager.projects.index') }}" class="btn btn-primary">
            <i class="bi bi-arrow-left"></i> Back to Projects
        </a>
    </div>

    @if($projects->isEmpty())
    <div class="text-center py-5">
        <i class="bi bi-trash" style="font-size: 3rem; color: #6c757d;"></i>
        <h4 class="mt-3">No Trashed Projects</h4>
        <p class="text-muted">Projects you delete will appear here</p>
    </div>
    @else
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Manager</th>
                            <th>Status</th>
                            <th>Deleted At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($projects as $project)
                        <tr>
                            <td>
                                <strong>{{ $project->name }}</strong>
                                <br>
                                <small class="text-muted">{{ Str::limit($project->description, 50) }}</small>
                            </td>
                            <td>
                                @if($project->manager)
                                    {{ $project->manager->name }}
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $project->status_color }}">
                                    {{ ucfirst($project->status) }}
                                </span>
                            </td>
                            <td>
                                {{ $project->deleted_at->format('d/m/Y H:i') }}
                                <br>
                                <small class="text-muted">{{ $project->deleted_at->diffForHumans() }}</small>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <!-- Restore Button -->
                                    <form action="{{ route('project-manager.projects.restore', $project->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-outline-success" 
                                                onclick="return confirm('Restore this project?')">
                                            <i class="bi bi-arrow-counterclockwise"></i> Restore
                                        </button>
                                    </form>
                                    
                                    <!-- Permanent Delete Button (Admin only) -->
                                    @if(auth()->user()->isAdmin())
                                    <form action="{{ route('project-manager.projects.forceDelete', $project->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Permanently delete this project? This cannot be undone.')">
                                            <i class="bi bi-trash-fill"></i> Delete Permanently
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $projects->links() }}
            </div>
        </div>
    </div>
    @endif
</div>
@endsection