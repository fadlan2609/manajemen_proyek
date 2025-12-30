@extends('layouts.app')

@section('title', 'Edit Task: ' . $task->title)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Edit Task: {{ $task->title }}</h5>
                        <a href="{{ route('member.tasks.show', $task) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('member.tasks.update', $task) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Task Title *</label>
                            <input type="text" 
                                   class="form-control @error('title') is-invalid @enderror" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title', $task->title) }}"
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
                                      rows="4">{{ old('description', $task->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="priority" class="form-label">Priority *</label>
                                    <select class="form-control @error('priority') is-invalid @enderror" 
                                            id="priority" 
                                            name="priority" 
                                            required>
                                        <option value="low" {{ old('priority', $task->priority) == 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="medium" {{ old('priority', $task->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high" {{ old('priority', $task->priority) == 'high' ? 'selected' : '' }}>High</option>
                                    </select>
                                    @error('priority')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="deadline" class="form-label">Deadline *</label>
                                    <input type="datetime-local" 
                                           class="form-control @error('deadline') is-invalid @enderror" 
                                           id="deadline" 
                                           name="deadline" 
                                           value="{{ old('deadline', $task->deadline ? $task->deadline->format('Y-m-d\TH:i') : '') }}"
                                           required>
                                    @error('deadline')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status *</label>
                                    <select class="form-control @error('status') is-invalid @enderror" 
                                            id="status" 
                                            name="status" 
                                            required>
                                        <option value="todo" {{ old('status', $task->status) == 'todo' ? 'selected' : '' }}>To Do</option>
                                        <option value="in_progress" {{ old('status', $task->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="done" {{ old('status', $task->status) == 'done' ? 'selected' : '' }}>Completed</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="progress" class="form-label">Progress (%)</label>
                                    <input type="number" 
                                           class="form-control @error('progress') is-invalid @enderror" 
                                           id="progress" 
                                           name="progress" 
                                           min="0" 
                                           max="100" 
                                           value="{{ old('progress', $task->progress) }}">
                                    @error('progress')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        @if(isset($projects) && $projects->count() > 0)
                        <div class="mb-3">
                            <label for="project_id" class="form-label">Project (Optional)</label>
                            <select class="form-control @error('project_id') is-invalid @enderror" 
                                    id="project_id" 
                                    name="project_id">
                                <option value="">No Project</option>
                                @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ old('project_id', $task->project_id) == $project->id ? 'selected' : '' }}>
                                    {{ $project->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('project_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @endif
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('member.tasks.show', $task) }}" class="btn btn-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Update Task
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection