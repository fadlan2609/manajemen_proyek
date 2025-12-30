@extends('layouts.app')

@section('title', 'My Tasks')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">My Tasks</h1>
                <div class="d-flex align-items-center">
                    <!-- Calendar View -->
                    <a href="#" class="btn btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#calendarModal">
                        <i class="bi bi-calendar-week"></i> Calendar
                    </a>
                    
                    <!-- Export -->
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-download"></i> Export
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-filetype-pdf"></i> PDF</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-file-excel"></i> Excel</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-printer"></i> Print</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-start border-primary border-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title text-muted mb-1">Total Tasks</h5>
                            <h2 class="mb-0">{{ $stats['total'] ?? 0 }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-list-task text-primary" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-start border-info border-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title text-muted mb-1">To Do</h5>
                            <h2 class="mb-0">{{ $stats['todo'] ?? 0 }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-clock text-info" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-start border-warning border-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title text-muted mb-1">In Progress</h5>
                            <h2 class="mb-0">{{ $stats['in_progress'] ?? 0 }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-gear text-warning" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-start border-success border-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title text-muted mb-1">Completed</h5>
                            <h2 class="mb-0">{{ $stats['done'] ?? 0 }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-start border-danger border-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title text-muted mb-1">Overdue</h5>
                            <h2 class="mb-0">{{ $stats['overdue'] ?? 0 }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-exclamation-triangle text-danger" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-start border-warning border-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title text-muted mb-1">Due Today</h5>
                            <h2 class="mb-0">{{ $stats['due_today'] ?? 0 }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-calendar-day text-warning" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('member.tasks.index') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select" onchange="this.form.submit()">
                                    <option value="all" {{ request('status') == 'all' || !request('status') ? 'selected' : '' }}>All Status</option>
                                    <option value="todo" {{ request('status') == 'todo' ? 'selected' : '' }}>To Do</option>
                                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="done" {{ request('status') == 'done' ? 'selected' : '' }}>Completed</option>
                                </select>
                            </div>
                            
                            <div class="col-md-3">
                                <label for="priority" class="form-label">Priority</label>
                                <select name="priority" id="priority" class="form-select" onchange="this.form.submit()">
                                    <option value="all" {{ request('priority') == 'all' || !request('priority') ? 'selected' : '' }}>All Priorities</option>
                                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                                    <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                </select>
                            </div>
                            
                            <div class="col-md-3">
                                <label for="project" class="form-label">Project</label>
                                <select name="project_id" id="project" class="form-select" onchange="this.form.submit()">
                                    <option value="">All Projects</option>
                                    @foreach($projects ?? [] as $project)
                                    <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                        {{ $project->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-3">
                                <label for="sort" class="form-label">Sort by</label>
                                <div class="input-group">
                                    <select name="sort" id="sort" class="form-select" onchange="this.form.submit()">
                                        <option value="deadline" {{ request('sort') == 'deadline' ? 'selected' : '' }}>Deadline</option>
                                        <option value="priority" {{ request('sort') == 'priority' ? 'selected' : '' }}>Priority</option>
                                        <option value="status" {{ request('sort') == 'status' ? 'selected' : '' }}>Status</option>
                                        <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Title</option>
                                        <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Created Date</option>
                                    </select>
                                    <button class="btn btn-outline-secondary" type="button" onclick="toggleSortDirection()">
                                        <i class="bi bi-arrow-{{ request('direction', 'asc') == 'asc' ? 'up' : 'down' }}"></i>
                                    </button>
                                    <input type="hidden" name="direction" id="direction" value="{{ request('direction', 'asc') }}">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="search" class="form-label">Search</label>
                                <div class="input-group">
                                    <input type="text" name="search" id="search" class="form-control" 
                                           placeholder="Search tasks..." value="{{ request('search') }}">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-search"></i>
                                    </button>
                                    @if(request('search'))
                                    <a href="{{ route('member.tasks.index') }}" class="btn btn-outline-secondary">
                                        Clear
                                    </a>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <label for="due_date" class="form-label">Due Date</label>
                                <input type="date" name="due_date" id="due_date" class="form-control" 
                                       value="{{ request('due_date') }}" onchange="this.form.submit()">
                            </div>
                            
                            <div class="col-md-3">
                                <label for="show" class="form-label">Show</label>
                                <select name="show" id="show" class="form-select" onchange="this.form.submit()">
                                    <option value="all" {{ request('show') == 'all' ? 'selected' : '' }}>All Tasks</option>
                                    <option value="active" {{ request('show') == 'active' ? 'selected' : '' }}>Active Only</option>
                                    <option value="completed" {{ request('show') == 'completed' ? 'selected' : '' }}>Completed Only</option>
                                    <option value="overdue" {{ request('show') == 'overdue' ? 'selected' : '' }}>Overdue Only</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tasks Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if($tasks->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="30%">Task</th>
                                    <th>Project</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Deadline</th>
                                    <th>Progress</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tasks as $task)
                                <tr class="task-row {{ $task->is_overdue ? 'table-danger' : '' }}">
                                    <td>
                                        <div class="d-flex align-items-start">
                                            <div class="flex-shrink-0 me-3">
                                                <input type="checkbox" class="form-check-input task-checkbox" 
                                                       data-task-id="{{ $task->id }}"
                                                       {{ $task->status == 'done' ? 'checked' : '' }}>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">
                                                    <a href="{{ route('member.tasks.show', $task) }}" class="text-decoration-none">
                                                        {{ $task->title }}
                                                    </a>
                                                    @if($task->link)
                                                        <i class="fas fa-link text-success ms-2" 
                                                           title="Has link: {{ $task->link }}"
                                                           data-bs-toggle="tooltip"></i>
                                                    @endif
                                                </h6>
                                                <small class="text-muted">{{ Str::limit($task->description ?? '', 50) }}</small>
                                                @if($task->is_overdue)
                                                <div class="mt-1">
                                                    <span class="badge bg-danger">
                                                        <i class="bi bi-exclamation-triangle"></i> Overdue
                                                    </span>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        {{-- PERBAIKAN DI SINI (LINE 258) --}}
                                        @if($task->project && $task->project->exists)
                                            <span class="badge bg-info">
                                                {{ $task->project->name }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                No Project
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $task->priority_color ?? 'secondary' }}">
                                            <i class="bi bi-flag"></i> {{ $task->priority_text ?? 'Medium' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $task->status_color ?? 'secondary' }}">
                                            {{ $task->status_text ?? 'To Do' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($task->deadline)
                                            <div class="d-flex flex-column">
                                                <span>{{ $task->deadline->format('d/m/Y') }}</span>
                                                @php
                                                    $daysDiff = \Carbon\Carbon::parse($task->deadline)->diffInDays(now(), false);
                                                @endphp
                                                @if($daysDiff > 0)
                                                    <small class="text-danger">Overdue {{ $daysDiff }} days</small>
                                                @elseif($daysDiff == 0)
                                                    <small class="text-warning">Due today</small>
                                                @else
                                                    <small class="text-success">Due in {{ abs($daysDiff) }} days</small>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">No deadline</span>
                                        @endif
                                    </td>
                                    <td width="150">
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                <div class="progress-bar bg-{{ $task->progress_color ?? 'secondary' }}" 
                                                     style="width: {{ $task->progress ?? 0 }}%"></div>
                                            </div>
                                            <small>{{ $task->progress ?? 0 }}%</small>
                                        </div>
                                        <div class="mt-1">
                                            <input type="range" class="form-range progress-slider" 
                                                   min="0" max="100" step="5" 
                                                   value="{{ $task->progress ?? 0 }}"
                                                   data-task-id="{{ $task->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('member.tasks.show', $task) }}" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="View Details">
                                                <i class="bi bi-eye"></i>View
                                            </a>
                                            
                                            @if($task->status != 'done')
                                            <button class="btn btn-sm btn-outline-success mark-complete-btn"
                                                    data-task-id="{{ $task->id }}"
                                                    title="Mark as Complete">
                                                <i class="bi bi-check-circle"></i> Complete
                                            </button>
                                            @endif
                                            
                                            <!-- Quick Status Update -->
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-info dropdown-toggle" 
                                                        type="button" data-bs-toggle="dropdown">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item status-change" href="#" 
                                                           data-task-id="{{ $task->id }}" data-status="todo">
                                                            <i class="bi bi-circle text-secondary me-1"></i> To Do
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item status-change" href="#" 
                                                           data-task-id="{{ $task->id }}" data-status="in_progress">
                                                            <i class="bi bi-arrow-clockwise text-info me-1"></i> In Progress
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item status-change" href="#" 
                                                           data-task-id="{{ $task->id }}" data-status="done">
                                                            <i class="bi bi-check-circle text-success me-1"></i> Complete
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                            
                                            <!-- Quick Link Button -->
                                            @if($task->link)
                                            <a href="{{ $task->link }}" target="_blank" 
                                               class="btn btn-sm btn-outline-warning"
                                               title="Open Link">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            Showing {{ $tasks->firstItem() }} to {{ $tasks->lastItem() }} of {{ $tasks->total() }} tasks
                        </div>
                        <div>
                            {{ $tasks->links() }}
                        </div>
                    </div>
                    @else
                    <!-- Empty State -->
                    <div class="text-center py-5">
                        <i class="bi bi-check-circle" style="font-size: 4rem; color: #28a745;"></i>
                        <h3 class="mt-4">No Tasks Found</h3>
                        <p class="text-muted">No tasks match your search criteria.</p>
                        <a href="{{ route('member.tasks.index') }}" class="btn btn-primary mt-2">
                            <i class="bi bi-arrow-clockwise"></i> Reset Filters
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Calendar Modal -->
<div class="modal fade" id="calendarModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tasks Calendar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .task-row:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }
    
    .progress-slider {
        height: 5px;
        cursor: pointer;
    }
    
    .progress-slider::-webkit-slider-thumb {
        width: 15px;
        height: 15px;
        background: #007bff;
        border-radius: 50%;
    }
    
    .fc-event {
        cursor: pointer;
    }
    
    .table-danger {
        background-color: rgba(220, 53, 69, 0.05) !important;
    }
    
    .task-checkbox:checked {
        background-color: #28a745;
        border-color: #28a745;
    }
    
    .dropdown-item.active {
        background-color: #f8f9fa;
    }
</style>
@endpush

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script>
    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Event delegation for status change
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('status-change') || 
                e.target.closest('.status-change')) {
                e.preventDefault();
                
                const target = e.target.classList.contains('status-change') 
                    ? e.target 
                    : e.target.closest('.status-change');
                
                const taskId = target.dataset.taskId;
                const status = target.dataset.status;
                
                updateTaskStatus(taskId, status);
            }
        });
        
        // Update task status
        window.updateTaskStatus = function(taskId, status) {
            if (!confirm('Are you sure you want to update task status?')) {
                return;
            }
            
            fetch(`/member/tasks/${taskId}/status`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: status })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Show success message
                    showToast('Task status updated successfully!', 'success');
                    
                    // Reload after 1 second
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    showToast(data.message || 'Error updating task', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error updating task status', 'error');
            });
        };
        
        // Update progress via slider
        document.querySelectorAll('.progress-slider').forEach(slider => {
            slider.addEventListener('change', function() {
                const taskId = this.dataset.taskId;
                const progress = this.value;
                
                if (confirm('Update task progress to ' + progress + '%?')) {
                    fetch(`/member/tasks/${taskId}/progress`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ progress: parseInt(progress) })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update UI
                            const row = this.closest('tr');
                            const progressBar = row.querySelector('.progress-bar');
                            const progressText = row.querySelector('small');
                            
                            if (progressBar) {
                                progressBar.style.width = `${data.new_progress}%`;
                                progressBar.className = `progress-bar bg-${data.progress_color || 'secondary'}`;
                            }
                            
                            if (progressText) {
                                progressText.textContent = `${data.new_progress}%`;
                            }
                            
                            // Update slider value
                            this.value = data.new_progress;
                            
                            // Show success message
                            showToast('Progress updated successfully!', 'success');
                            
                            // Auto update status based on progress
                            if (data.new_progress >= 100) {
                                updateTaskStatus(taskId, 'done');
                            } else if (data.new_progress > 0) {
                                updateTaskStatus(taskId, 'in_progress');
                            }
                        } else {
                            showToast(data.message || 'Error updating progress', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('Error updating progress', 'error');
                    });
                }
            });
        });
        
        // Mark as complete buttons
        document.querySelectorAll('.mark-complete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const taskId = this.dataset.taskId;
                updateTaskStatus(taskId, 'done');
            });
        });
        
        // Task checkboxes
        document.querySelectorAll('.task-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const taskId = this.dataset.taskId;
                const status = this.checked ? 'done' : 'todo';
                updateTaskStatus(taskId, status);
            });
        });
        
        // Calendar
        const calendarEl = document.getElementById('calendar');
        if (calendarEl) {
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: '{{ route("member.tasks.calendar") }}',
                eventClick: function(info) {
                    window.location.href = info.event.url;
                },
                eventDisplay: 'block',
                eventColor: '#3788d8'
            });
            calendar.render();
        }
    });
    
    function toggleSortDirection() {
        const directionInput = document.getElementById('direction');
        const currentDirection = directionInput.value;
        directionInput.value = currentDirection === 'asc' ? 'desc' : 'asc';
        document.forms[0].submit();
    }
    
    function showToast(message, type = 'info') {
        // You can implement a toast notification here
        // For now using alert
        alert(message);
    }
</script>
@endpush
@endsection