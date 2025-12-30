@extends('layouts.app')

@section('title', 'Edit Project: ' . $project->name)

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Edit Project: {{ $project->name }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('project-manager.projects.update', $project) }}" method="POST">
            @csrf
            @method('PUT')
            
            <!-- Project Name -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="name" class="form-label">Project Name *</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" value="{{ old('name', $project->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <!-- Description -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="3">{{ old('description', $project->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <!-- Deadline & Status - SATU BARIS -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="deadline" class="form-label">Deadline</label>
                    <input type="date" class="form-control @error('deadline') is-invalid @enderror" 
                           id="deadline" name="deadline" 
                           value="{{ old('deadline', $project->deadline ? $project->deadline->format('Y-m-d') : '') }}">
                    @error('deadline')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label for="status" class="form-label">Status *</label>
                    <select class="form-select @error('status') is-invalid @enderror" 
                            id="status" name="status" required>
                        <option value="active" {{ old('status', $project->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="completed" {{ old('status', $project->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="on_hold" {{ old('status', $project->status) == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Project Manager - DI BAWAH STATUS -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="project_manager_id" class="form-label">Project Manager *</label>
                    <select class="form-select @error('project_manager_id') is-invalid @enderror" 
                            id="project_manager_id" name="project_manager_id" required>
                        <option value="">Select Project Manager</option>
                        @foreach($projectManagers as $pm)
                            <option value="{{ $pm->id }}" 
                                    {{ old('project_manager_id', $project->project_manager_id) == $pm->id ? 'selected' : '' }}>
                                {{ $pm->name }} ({{ $pm->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('project_manager_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Progress - SEJAJAR DENGAN PROJECT MANAGER -->
                <div class="col-md-6">
                    <label for="progress" class="form-label">Progress (%)</label>
                    <input type="number" class="form-control @error('progress') is-invalid @enderror" 
                           id="progress" name="progress" min="0" max="100" step="0.01"
                           value="{{ old('progress', $project->progress ?? 0) }}">
                    <div class="form-text">Overall project completion percentage</div>
                    @error('progress')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <!-- Team Members -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <label class="form-label">Team Members</label>
                    <div class="form-control" style="height: 150px; overflow-y: auto;">
                        @php
                            $allUsers = App\Models\User::where('id', '!=', auth()->id())->get();
                            $selectedMemberIds = old('members', $project->members->pluck('id')->toArray());
                        @endphp
                        @foreach($allUsers as $user)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" 
                                   name="members[]" value="{{ $user->id }}" 
                                   id="member{{ $user->id }}"
                                   {{ in_array($user->id, (array)$selectedMemberIds) ? 'checked' : '' }}>
                            <label class="form-check-label" for="member{{ $user->id }}">
                                {{ $user->name }} 
                                <small class="text-muted">({{ $user->email }}) - {{ $user->role_name }}</small>
                            </label>
                        </div>
                        @endforeach
                    </div>
                    @error('members.*')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Danger Zone -->
            @if(auth()->user()->isAdmin() || auth()->id() == $project->project_manager_id)
            <div class="card border-danger mt-4">
                <div class="card-header bg-danger text-white">
                    <h6 class="mb-0">Danger Zone</h6>
                </div>
                <div class="card-body">
                    <p class="text-danger mb-3">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        Actions in this section are irreversible. Proceed with caution.
                    </p>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Delete Project</strong>
                            <p class="text-muted mb-0">
                                Permanently delete this project and all associated tasks.
                            </p>
                        </div>
                        <button type="button" class="btn btn-outline-danger" 
                                data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="bi bi-trash"></i> Delete Project
                        </button>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Action Buttons -->
            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('project-manager.projects.show', $project) }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Cancel
                </a>
                <div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Update Project
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Delete Modal -->
@if(auth()->user()->isAdmin() || auth()->id() == $project->project_manager_id)
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="bi bi-exclamation-triangle"></i> Confirm Deletion
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this project?</p>
                <div class="alert alert-warning">
                    <strong>Warning:</strong> This action cannot be undone. All tasks, files, and data associated with this project will be permanently deleted.
                </div>
                <p><strong>Project:</strong> {{ $project->name }}</p>
                <p><strong>Total Tasks:</strong> {{ $project->tasks_count ?? $project->tasks()->count() }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('project-manager.projects.destroy', $project) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i> Delete Project
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-calculate deadline minimum (today)
        const deadlineInput = document.getElementById('deadline');
        if (deadlineInput) {
            const today = new Date().toISOString().split('T')[0];
            deadlineInput.min = today;
        }

        // Confirm before deleting
        const deleteForm = document.querySelector('#deleteModal form');
        if (deleteForm) {
            deleteForm.addEventListener('submit', function(e) {
                const confirmDelete = confirm('Are you absolutely sure? This cannot be undone!');
                if (!confirmDelete) {
                    e.preventDefault();
                }
            });
        }

        // Progress validation
        const progressInput = document.getElementById('progress');
        if (progressInput) {
            progressInput.addEventListener('change', function() {
                let value = parseFloat(this.value);
                if (isNaN(value)) value = 0;
                if (value < 0) this.value = 0;
                if (value > 100) this.value = 100;
            });
        }
    });
</script>
@endsection
@endsection