@extends('layouts.app')

@section('title', 'Edit Project: ' . $project->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-primary">
                            <i class="bi bi-pencil-square me-2"></i>Edit Project
                        </h5>
                        <div class="badge bg-light text-dark">
                            <i class="bi bi-diagram-3 me-1"></i>ID: {{ $project->id }}
                        </div>
                    </div>
                    <p class="text-muted mb-0 mt-1">
                        <i class="bi bi-info-circle me-1"></i>Update project details below
                    </p>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('project-manager.projects.update', $project) }}" method="POST" id="projectForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- Basic Information Section -->
                        <div class="card mb-4 border-0 shadow-sm">
                            <div class="card-header bg-light py-3">
                                <h6 class="mb-0">
                                    <i class="bi bi-card-checklist me-2"></i>Basic Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="name" class="form-label fw-semibold">
                                                <i class="bi bi-tag me-1"></i>Project Name <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                                   id="name" name="name" value="{{ old('name', $project->name) }}" 
                                                   placeholder="Enter project name" required>
                                            <div class="form-text">Give your project a descriptive name</div>
                                            @error('name')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="description" class="form-label fw-semibold">
                                                <i class="bi bi-text-paragraph me-1"></i>Description
                                            </label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                                      id="description" name="description" rows="4" 
                                                      placeholder="Describe the project objectives and scope">{{ old('description', $project->description) }}</textarea>
                                            <div class="form-text">Detailed description helps team understand the project</div>
                                            @error('description')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Timeline & Status Section -->
                        <div class="card mb-4 border-0 shadow-sm">
                            <div class="card-header bg-light py-3">
                                <h6 class="mb-0">
                                    <i class="bi bi-calendar-check me-2"></i>Timeline & Status
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="deadline" class="form-label fw-semibold">
                                                <i class="bi bi-calendar-date me-1"></i>Deadline
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-light">
                                                    <i class="bi bi-clock"></i>
                                                </span>
                                                <input type="date" class="form-control @error('deadline') is-invalid @enderror" 
                                                       id="deadline" name="deadline" 
                                                       value="{{ old('deadline', $project->deadline ? $project->deadline->format('Y-m-d') : '') }}">
                                            </div>
                                            <div class="form-text">Set the project completion deadline</div>
                                            @error('deadline')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="status" class="form-label fw-semibold">
                                                <i class="bi bi-activity me-1"></i>Status <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select @error('status') is-invalid @enderror" 
                                                    id="status" name="status" required>
                                                <option value="">Select Status</option>
                                                <option value="active" {{ old('status', $project->status) == 'active' ? 'selected' : '' }} class="text-success">
                                                    🟢 Active
                                                </option>
                                                <option value="on_hold" {{ old('status', $project->status) == 'on_hold' ? 'selected' : '' }} class="text-warning">
                                                    🟡 On Hold
                                                </option>
                                                <option value="completed" {{ old('status', $project->status) == 'completed' ? 'selected' : '' }} class="text-primary">
                                                    🔵 Completed
                                                </option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="progress" class="form-label fw-semibold">
                                                <i class="bi bi-graph-up me-1"></i>Progress (%)
                                            </label>
                                            <div class="input-group">
                                                <input type="number" class="form-control @error('progress') is-invalid @enderror" 
                                                       id="progress" name="progress" min="0" max="100" step="1"
                                                       value="{{ old('progress', $project->progress ?? 0) }}">
                                                <span class="input-group-text">%</span>
                                            </div>
                                            <div class="progress mt-2" style="height: 8px;">
                                                <div class="progress-bar bg-success" role="progressbar" 
                                                     style="width: {{ old('progress', $project->progress ?? 0) }}%"
                                                     aria-valuenow="{{ old('progress', $project->progress ?? 0) }}" 
                                                     aria-valuemin="0" aria-valuemax="100">
                                                </div>
                                            </div>
                                            @error('progress')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="project_manager_id" class="form-label fw-semibold">
                                                <i class="bi bi-person-badge me-1"></i>Project Manager
                                            </label>
                                            @php
                                                $currentUser = auth()->user();
                                            @endphp
                                            <select class="form-select @error('project_manager_id') is-invalid @enderror" 
                                                    id="project_manager_id" name="project_manager_id">
                                                <option value="">Assign Project Manager</option>
                                                <option value="{{ $currentUser->id }}" 
                                                        {{ old('project_manager_id', $project->project_manager_id) == $currentUser->id ? 'selected' : '' }}>
                                                    👤 {{ $currentUser->name }} (You)
                                                </option>
                                                <!-- Add other project managers if needed -->
                                            </select>
                                            @error('project_manager_id')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Team Members Section -->
                        <div class="card mb-4 border-0 shadow-sm">
                            <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <i class="bi bi-people me-2"></i>Team Members
                                </h6>
                                <small class="text-muted">
                                    <i class="bi bi-info-circle me-1"></i>Select team members for this project
                                </small>
                            </div>
                            <div class="card-body">
                                @php
                                    // Get all users except current user
                                    $allUsers = App\Models\User::where('id', '!=', auth()->id())->get();
                                    $selectedMemberIds = old('members', $project->members->pluck('id')->toArray() ?? []);
                                @endphp
                                
                                @if($allUsers->count() > 0)
                                <div class="row row-cols-1 row-cols-md-2 g-3">
                                    @foreach($allUsers as $user)
                                    <div class="col">
                                        <div class="border rounded p-3 h-100">
                                            <div class="form-check d-flex align-items-center">
                                                <input class="form-check-input me-3" type="checkbox" 
                                                       name="members[]" value="{{ $user->id }}" 
                                                       id="member{{ $user->id }}"
                                                       {{ in_array($user->id, (array)$selectedMemberIds) ? 'checked' : '' }}>
                                                <div class="d-flex align-items-center w-100">
                                                    <div class="flex-shrink-0">
                                                        <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center">
                                                            <i class="bi bi-person fs-5"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <label class="form-check-label fw-medium mb-1" for="member{{ $user->id }}">
                                                            {{ $user->name }}
                                                        </label>
                                                        <div class="text-muted small">
                                                            <i class="bi bi-envelope me-1"></i>{{ $user->email }}
                                                        </div>
                                                        <span class="badge bg-light text-dark mt-1">
                                                            <i class="bi bi-person-badge me-1"></i>{{ ucfirst($user->role) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @else
                                <div class="text-center py-4">
                                    <i class="bi bi-people fs-1 text-muted mb-3"></i>
                                    <p class="text-muted mb-0">No other users found to add as team members</p>
                                </div>
                                @endif
                                
                                @error('members.*')
                                    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                                        <i class="bi bi-exclamation-triangle me-2"></i>{{ $message }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <div>
                                <a href="{{ route('project-manager.projects.show', $project) }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Back to Project
                                </a>
                                <a href="{{ route('project-manager.projects.index') }}" class="btn btn-light ms-2">
                                    <i class="bi bi-list-ul me-2"></i>All Projects
                                </a>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                    <i class="bi bi-trash me-2"></i>Delete
                                </button>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-save me-2"></i>Update Project
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
@if(auth()->user()->isAdmin() || auth()->id() == $project->project_manager_id)
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Confirm Deletion
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <div class="avatar-lg mx-auto bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center mb-3">
                        <i class="bi bi-exclamation-triangle-fill fs-1"></i>
                    </div>
                    <h5 class="text-danger">Warning: This action cannot be undone!</h5>
                </div>
                
                <div class="alert alert-warning border-0">
                    <div class="d-flex">
                        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                        <div>
                            <strong>Please confirm you want to delete:</strong>
                            <p class="mb-0 mt-1">Project: <strong>"{{ $project->name }}"</strong></p>
                            <p class="mb-0">Total Tasks: <strong>{{ $project->tasks()->count() }}</strong></p>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-danger border-0 mt-3">
                    <i class="bi bi-info-circle me-2"></i>
                    All associated tasks, files, and data will be permanently deleted.
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-2"></i>Cancel
                </button>
                <form action="{{ route('project-manager.projects.destroy', $project) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger px-4">
                        <i class="bi bi-trash me-2"></i>Delete Project
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@section('styles')
<style>
    .form-control:focus, .form-select:focus {
        border-color: #4dabf7;
        box-shadow: 0 0 0 0.25rem rgba(77, 171, 247, 0.25);
    }
    
    .card {
        border-radius: 12px;
        overflow: hidden;
    }
    
    .card-header {
        border-radius: 12px 12px 0 0 !important;
    }
    
    .avatar-sm {
        width: 40px;
        height: 40px;
    }
    
    .avatar-lg {
        width: 80px;
        height: 80px;
    }
    
    .progress {
        border-radius: 10px;
        overflow: hidden;
    }
    
    .progress-bar {
        border-radius: 10px;
    }
    
    .border-0 {
        border: none !important;
    }
    
    .shadow-sm {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
    }
</style>
@endsection

@section('scripts')
<!-- Load Bootstrap JS (pastikan sudah di-load di layout utama) -->
@if(!isset($bootstrapLoaded))
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Update progress bar in real-time
        const progressInput = document.getElementById('progress');
        const progressBar = document.querySelector('.progress-bar');
        
        if (progressInput && progressBar) {
            // Initial progress bar text
            progressBar.textContent = progressBar.style.width;
            
            progressInput.addEventListener('input', function() {
                let value = parseInt(this.value) || 0;
                if (value < 0) value = 0;
                if (value > 100) value = 100;
                
                // Update progress bar width
                progressBar.style.width = value + '%';
                progressBar.setAttribute('aria-valuenow', value);
                progressBar.textContent = value + '%';
                
                // Update color based on progress
                if (value < 30) {
                    progressBar.className = 'progress-bar bg-danger';
                } else if (value < 70) {
                    progressBar.className = 'progress-bar bg-warning';
                } else {
                    progressBar.className = 'progress-bar bg-success';
                }
            });
            
            // Trigger initial update
            setTimeout(() => {
                progressInput.dispatchEvent(new Event('input'));
            }, 100);
        }
        
        // Set minimum date for deadline (today)
        const deadlineInput = document.getElementById('deadline');
        if (deadlineInput) {
            const today = new Date();
            today.setDate(today.getDate() - 1); // Allow yesterday for editing existing projects
            const minDate = today.toISOString().split('T')[0];
            deadlineInput.min = minDate;
        }
        
        // Form validation
        const projectForm = document.getElementById('projectForm');
        if (projectForm) {
            projectForm.addEventListener('submit', function(e) {
                const projectName = document.getElementById('name').value.trim();
                if (!projectName) {
                    e.preventDefault();
                    showToast('Project name is required!', 'warning');
                    return;
                }
                
                // Show loading
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Updating...';
                submitBtn.disabled = true;
                
                // Re-enable button after 5 seconds (in case of error)
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }, 5000);
            });
        }
        
        // Checkbox select all functionality
        const selectAllBtn = document.createElement('button');
        selectAllBtn.type = 'button';
        selectAllBtn.className = 'btn btn-sm btn-outline-primary mb-3';
        selectAllBtn.innerHTML = '<i class="bi bi-check-all me-1"></i>Select All';
        
        const teamMembersSection = document.querySelector('.card-body .row-cols-md-2');
        if (teamMembersSection) {
            const header = teamMembersSection.previousElementSibling;
            if (header && header.classList.contains('card-header')) {
                header.appendChild(selectAllBtn);
            }
            
            selectAllBtn.addEventListener('click', function() {
                const checkboxes = document.querySelectorAll('input[name="members[]"]');
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                
                checkboxes.forEach(cb => {
                    cb.checked = !allChecked;
                });
                
                this.innerHTML = allChecked 
                    ? '<i class="bi bi-check-all me-1"></i>Select All'
                    : '<i class="bi bi-x-square me-1"></i>Deselect All';
            });
        }
        
        // Toast notification function (with fallback)
        function showToast(message, type = 'info') {
            // Check if Bootstrap Toast is available
            if (typeof bootstrap === 'undefined' || typeof bootstrap.Toast === 'undefined') {
                // Fallback to simple alert
                alert(message);
                return;
            }
            
            const toastId = 'toast-' + Date.now();
            const toastHtml = `
                <div id="${toastId}" class="toast align-items-center text-bg-${type} border-0 position-fixed bottom-0 end-0 m-3" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="bi ${type === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle'} me-2"></i>
                            ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            `;
            
            // Add toast to body
            const toastContainer = document.createElement('div');
            toastContainer.innerHTML = toastHtml;
            document.body.appendChild(toastContainer.firstElementChild);
            
            // Show toast
            const toastElement = document.getElementById(toastId);
            const toast = new bootstrap.Toast(toastElement);
            toast.show();
            
            // Remove toast after it's hidden
            toastElement.addEventListener('hidden.bs.toast', function () {
                toastElement.remove();
            });
        }
        
        // Initialize tooltips if available
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    });
</script>
@endsection