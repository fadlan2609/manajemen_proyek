@extends('layouts.app')

@section('title', 'Create New Task')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-plus-circle me-2"></i>Create New Task
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('project-manager.projects.tasks.store', $project) }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Task Title *</label>
                            <input type="text" 
                                   class="form-control @error('title') is-invalid @enderror" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title') }}"
                                   required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- TAMBAHKAN FIELD LINK DI SINI -->
                        <div class="mb-3">
                            <label for="link" class="form-label">Link/URL (Optional)</label>
                            <input type="url" 
                                   class="form-control @error('link') is-invalid @enderror" 
                                   id="link" 
                                   name="link" 
                                   value="{{ old('link') }}"
                                   placeholder="https://example.com atau link referensi">
                            @error('link')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                Masukkan link untuk referensi, dokumen, atau website terkait task
                            </small>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="assigned_to" class="form-label">Assign To *</label>
                                <select class="form-select @error('assigned_to') is-invalid @enderror" 
                                        id="assigned_to" 
                                        name="assigned_to" 
                                        required>
                                    <option value="">Select Team Member</option>
                                    @foreach($project->members as $member)
                                        <option value="{{ $member->id }}" {{ old('assigned_to') == $member->id ? 'selected' : '' }}>
                                            {{ $member->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('assigned_to')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="priority" class="form-label">Priority *</label>
                                <select class="form-select @error('priority') is-invalid @enderror" 
                                        id="priority" 
                                        name="priority" 
                                        required>
                                    <option value="">Select Priority</option>
                                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="deadline" class="form-label">Deadline</label>
                                <input type="date" 
                                       class="form-control @error('deadline') is-invalid @enderror" 
                                       id="deadline" 
                                       name="deadline" 
                                       value="{{ old('deadline') }}">
                                @error('deadline')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status *</label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" 
                                        name="status" 
                                        required>
                                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="done" {{ old('status') == 'done' ? 'selected' : '' }}>Done</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('project-manager.projects.show', $project) }}" 
                               class="btn btn-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Create Task
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection