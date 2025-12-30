@extends('layouts.app')

@section('title', 'Create New Project')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Create New Project</h4>
                        <a href="{{ route('project-manager.projects.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Projects
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('project-manager.projects.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Project Name *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" 
                                           placeholder="Enter project name" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="4" 
                                              placeholder="Describe the project...">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="project_manager_id" class="form-label">Project Manager *</label>
                                    <select class="form-select @error('project_manager_id') is-invalid @enderror" 
                                            id="project_manager_id" name="project_manager_id" required>
                                        <option value="">Select Project Manager</option>
                                        @foreach($projectManagers as $manager)
                                            <option value="{{ $manager->id }}" {{ old('project_manager_id') == $manager->id ? 'selected' : '' }}>
                                                {{ $manager->name }} ({{ $manager->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('project_manager_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="deadline" class="form-label">Deadline</label>
                                    <input type="date" class="form-control @error('deadline') is-invalid @enderror" 
                                           id="deadline" name="deadline" value="{{ old('deadline') }}">
                                    @error('deadline')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-12">
                                <label class="form-label">Team Members (Optional)</label>
                                <div class="form-control" style="height: 150px; overflow-y: auto;">
                                    @foreach($members as $member)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               name="members[]" value="{{ $member->id }}" 
                                               id="member{{ $member->id }}"
                                               {{ is_array(old('members')) && in_array($member->id, old('members')) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="member{{ $member->id }}">
                                            {{ $member->name }} 
                                            <small class="text-muted">({{ $member->email }})</small>
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                                @error('members.*')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Create Project
                            </button>
                            <a href="{{ route('project-manager.projects.index') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Set minimum date to today for deadline
        const today = new Date().toISOString().split('T')[0];
        const deadlineInput = document.getElementById('deadline');
        if (deadlineInput) {
            deadlineInput.min = today;
        }
    });
</script>
@endsection