@extends('layouts.app')

@section('title', 'Task: ' . ($task->title ?? 'Untitled Task'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">{{ $task->title ?? 'Untitled Task' }}</h5>
                            <p class="text-muted mb-0 small">Project: {{ $task->project->name ?? 'No Project' }}</p>
                        </div>
                        @if(isset($task->status))
                        <span class="badge bg-{{ $task->status === 'done' ? 'success' : ($task->status === 'in_progress' ? 'primary' : 'secondary') }}">
                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                        </span>
                        @endif
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Action Buttons -->
                    @if($task->assigned_to === auth()->id())
                    <div class="mb-4">
                        <div class="btn-group flex-wrap" role="group">
                            <a href="{{ route('member.tasks.edit', $task) }}" 
                               class="btn btn-outline-primary mb-2 me-2">
                                <i class="bi bi-pencil"></i> Edit Task
                            </a>
                            
                            @if(!$task->link)
                            <button type="button" 
                                    class="btn btn-success mb-2 me-2" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#submitLinkModal">
                                <i class="bi bi-send"></i> Submit Link
                            </button>
                            @else
                            <button type="button" 
                                    class="btn btn-warning mb-2 me-2" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#updateLinkModal">
                                <i class="bi bi-link"></i> Update Link
                            </button>
                            @endif
                            
                            <button type="button" 
                                    class="btn btn-info mb-2 me-2" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#addCommentModal">
                                <i class="bi bi-chat-left"></i> Add Comment
                            </button>
                            
                            @if($task->status !== 'done')
                            <form action="{{ route('member.tasks.complete', $task) }}" method="POST" class="d-inline mb-2">
                                @csrf
                                <button type="submit" 
                                        class="btn btn-success"
                                        onclick="return confirm('Mark this task as complete?\nProgress will be set to 100% and status to Done.')">
                                    <i class="bi bi-check-circle"></i> Mark Complete
                                </button>
                            </form>
                            @else
                            <button type="button" class="btn btn-success mb-2 disabled">
                                <i class="bi bi-check-circle"></i> Completed
                            </button>
                            @endif
                        </div>
                    </div>
                    @endif
                    
                    <!-- Tampilkan Link jika ada -->
                    @if($task->link)
                    <div class="mb-4 p-3 border rounded bg-light">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <strong>Task Link:</strong>
                            <span class="badge bg-success">
                                <i class="bi bi-check-circle"></i> Submitted
                            </span>
                        </div>
                        <div class="mt-2 d-flex align-items-center gap-2">
                            <a href="{{ $task->link }}" 
                               target="_blank" 
                               class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-box-arrow-up-right me-1"></i> Open Link
                            </a>
                            <button type="button" 
                                    class="btn btn-outline-secondary btn-sm"
                                    onclick="copyToClipboard('{{ $task->link }}')">
                                <i class="bi bi-clipboard me-1"></i> Copy
                            </button>
                            @if($task->assigned_to === auth()->id())
                            <button type="button" 
                                    class="btn btn-outline-warning btn-sm" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#updateLinkModal">
                                <i class="bi bi-pencil"></i> Edit
                            </button>
                            <form action="{{ route('member.tasks.remove-link', $task) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="btn btn-outline-danger btn-sm"
                                        onclick="return confirm('Are you sure you want to remove this link?')">
                                    <i class="bi bi-trash"></i> Remove
                                </button>
                            </form>
                            @endif
                        </div>
                        <small class="text-muted d-block mt-2">
                            <i class="bi bi-link-45deg me-1"></i>
                            {{ $task->link }}
                        </small>
                        @php
                            $parsedUrl = parse_url($task->link);
                            $domain = $parsedUrl['host'] ?? null;
                        @endphp
                        @if($domain)
                        <small class="text-muted">
                            <i class="bi bi-globe me-1"></i>
                            Domain: {{ $domain }}
                        </small>
                        @endif
                    </div>
                    @endif
                    
                    <div class="mb-4">
                        <h6>Description</h6>
                        <div class="p-3 bg-light rounded">
                            {!! nl2br(e($task->description ?? 'No description provided.')) !!}
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Task Details</h6>
                            <div class="p-3 bg-light rounded">
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-2">
                                        <i class="bi bi-person me-2 text-primary"></i>
                                        <strong>Assigned To:</strong> 
                                        {{ $task->assignee->name ?? 'Not Assigned' }}
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-flag me-2 text-warning"></i>
                                        <strong>Priority:</strong> 
                                        @php
                                            $priorityColors = [
                                                'high' => 'danger',
                                                'medium' => 'warning',
                                                'low' => 'success'
                                            ];
                                            $priorityText = ucfirst($task->priority ?? 'medium');
                                            $priorityColor = $priorityColors[$task->priority ?? 'medium'] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $priorityColor }}">
                                            {{ $priorityText }}
                                        </span>
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-calendar me-2 text-info"></i>
                                        <strong>Deadline:</strong> 
                                        @if($task->deadline)
                                            {{ \Carbon\Carbon::parse($task->deadline)->format('M d, Y H:i') }}
                                        @else
                                            <span class="text-muted">No deadline</span>
                                        @endif
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-clock me-2 text-secondary"></i>
                                        <strong>Created:</strong> 
                                        {{ $task->created_at->format('M d, Y H:i') }}
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-pencil me-2 text-secondary"></i>
                                        <strong>Last Updated:</strong> 
                                        {{ $task->updated_at->format('M d, Y H:i') }}
                                    </li>
                                    {{-- 
                                    <!-- Hapus atau komentari jika kolom completed_at tidak ada -->
                                    @if($task->completed_at)
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle me-2 text-success"></i>
                                        <strong>Completed:</strong> 
                                        {{ $task->completed_at->format('M d, Y H:i') }}
                                    </li>
                                    @endif
                                    --}}
                                </ul>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h6>Progress & Timeline</h6>
                            <div class="p-3 bg-light rounded">
                                <div class="progress mb-3" style="height: 25px;">
                                    @php
                                        $progress = $task->progress ?? 0;
                                        $progressColor = $progress >= 100 ? 'success' : 
                                                         ($progress >= 50 ? 'primary' : 
                                                         ($progress >= 25 ? 'warning' : 'danger'));
                                    @endphp
                                    <div class="progress-bar bg-{{ $progressColor }}" 
                                         role="progressbar" 
                                         style="width: {{ $progress }}%"
                                         aria-valuenow="{{ $progress }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        {{ $progress }}%
                                    </div>
                                </div>
                                
                                @if($task->deadline)
                                    @php
                                        $deadline = \Carbon\Carbon::parse($task->deadline);
                                        $now = \Carbon\Carbon::now();
                                        $daysRemaining = $now->diffInDays($deadline, false);
                                        $isOverdue = $deadline < $now && $task->status !== 'done';
                                    @endphp
                                    <div class="timeline-status mb-3">
                                        <p class="mb-1">
                                            @if($isOverdue)
                                                <span class="badge bg-danger">
                                                    <i class="bi bi-exclamation-triangle"></i> Overdue
                                                </span>
                                            @elseif($task->status === 'done')
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle"></i> Completed
                                                </span>
                                            @elseif($daysRemaining === 0)
                                                <span class="badge bg-warning">
                                                    <i class="bi bi-clock"></i> Due today
                                                </span>
                                            @elseif($daysRemaining > 0 && $daysRemaining <= 3)
                                                <span class="badge bg-warning">
                                                    <i class="bi bi-clock"></i> Due in {{ $daysRemaining }} days
                                                </span>
                                            @elseif($daysRemaining > 3)
                                                <span class="badge bg-info">
                                                    <i class="bi bi-clock"></i> {{ $daysRemaining }} days remaining
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i class="bi bi-clock"></i> Time elapsed
                                                </span>
                                            @endif
                                        </p>
                                        
                                        @if($task->deadline && $task->status !== 'done')
                                        <div class="progress timeline-progress" style="height: 8px;">
                                            @php
                                                $totalDays = max(1, $now->diffInDays($deadline, false) + 30); // 30 days buffer
                                                $elapsedDays = min($totalDays, max(0, $now->diffInDays($deadline->copy()->subDays($totalDays))));
                                                $timelinePercent = min(100, ($elapsedDays / $totalDays) * 100);
                                            @endphp
                                            <div class="progress-bar bg-{{ $isOverdue ? 'danger' : 'info' }}" 
                                                 style="width: {{ $timelinePercent }}%">
                                            </div>
                                        </div>
                                        <small class="text-muted">
                                            @if($isOverdue)
                                                {{ abs($daysRemaining) }} days overdue
                                            @else
                                                {{ $daysRemaining }} days remaining
                                            @endif
                                        </small>
                                        @endif
                                    </div>
                                @endif
                                
                                <!-- Quick Status Update -->
                                @if($task->assigned_to === auth()->id() && $task->status !== 'done')
                                <div class="mt-3">
                                    <h6 class="small">Quick Status Update</h6>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <!-- To Do Button -->
                                        @if($task->status !== 'todo')
                                        <form action="{{ route('member.tasks.update-status', $task) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="todo">
                                            <button type="submit" 
                                                    class="btn btn-outline-secondary"
                                                    onclick="return confirm('Change status to "To Do"?')">
                                                To Do
                                            </button>
                                        </form>
                                        @else
                                        <button type="button" class="btn btn-secondary disabled">To Do</button>
                                        @endif
                                        
                                        <!-- In Progress Button -->
                                        @if($task->status !== 'in_progress')
                                        <form action="{{ route('member.tasks.update-status', $task) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="in_progress">
                                            <button type="submit" 
                                                    class="btn btn-outline-primary"
                                                    onclick="return confirm('Change status to "In Progress"?')">
                                                In Progress
                                            </button>
                                        </form>
                                        @else
                                        <button type="button" class="btn btn-primary disabled">In Progress</button>
                                        @endif
                                        
                                        <!-- Done Button -->
                                        @if($task->status !== 'done')
                                        <form action="{{ route('member.tasks.complete', $task) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="btn btn-outline-success"
                                                    onclick="return confirm('Mark this task as complete?')">
                                                Done
                                            </button>
                                        </form>
                                        @else
                                        <button type="button" class="btn btn-success disabled">Done</button>
                                        @endif
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Link History -->
                    @if(isset($linkHistory) && $linkHistory->count() > 0)
                    <div class="mb-4">
                        <h6>Link History</h6>
                        <div class="list-group">
                            @foreach($linkHistory as $history)
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <strong>{{ $history->user->name ?? 'User' }}</strong>
                                    <small class="text-muted">{{ $history->created_at->format('M d, Y H:i') }}</small>
                                </div>
                                @if($history->old_link)
                                <div class="mt-1">
                                    <small class="text-muted">From: {{ $history->old_link }}</small>
                                </div>
                                @endif
                                @if($history->new_link)
                                <div class="mt-1">
                                    <small>To: <a href="{{ $history->new_link }}" target="_blank">{{ $history->new_link }}</a></small>
                                </div>
                                @endif
                                @if($history->notes)
                                <div class="mt-2">
                                    <small class="text-muted">Notes: {{ $history->notes }}</small>
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    <!-- Similar Tasks -->
                    @if(isset($similarTasks) && $similarTasks->count() > 0)
                    <div class="mb-4">
                        <h6>Related Tasks</h6>
                        <div class="list-group">
                            @foreach($similarTasks as $similarTask)
                            <a href="{{ route('member.tasks.show', $similarTask) }}" 
                               class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <strong>{{ $similarTask->title }}</strong>
                                    <span class="badge bg-{{ $similarTask->status === 'done' ? 'success' : ($similarTask->status === 'in_progress' ? 'primary' : 'secondary') }}">
                                        {{ ucfirst($similarTask->status) }}
                                    </span>
                                </div>
                                <small class="text-muted">
                                    <i class="bi bi-calendar"></i> 
                                    @if($similarTask->deadline)
                                        Due: {{ \Carbon\Carbon::parse($similarTask->deadline)->format('M d, Y') }}
                                    @endif
                                    | 
                                    <i class="bi bi-percent"></i> {{ $similarTask->progress ?? 0 }}%
                                </small>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    <!-- Comments Section -->
                    @if(isset($task->comments) && $task->comments->count() > 0)
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0">Comments ({{ $task->comments->count() }})</h6>
                            <button type="button" 
                                    class="btn btn-sm btn-outline-primary" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#addCommentModal">
                                <i class="bi bi-plus"></i> Add Comment
                            </button>
                        </div>
                        <div class="list-group">
                            @foreach($task->comments->sortByDesc('created_at') as $comment)
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-placeholder bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                            {{ substr($comment->user->name ?? 'U', 0, 1) }}
                                        </div>
                                        <strong>{{ $comment->user->name ?? 'Unknown User' }}</strong>
                                    </div>
                                    <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                                </div>
                                <div class="mt-2 p-2 bg-white rounded border">
                                    <p class="mb-0">{{ $comment->content }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('member.tasks.index') }}" 
                           class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Tasks
                        </a>
                        <div class="d-flex gap-2">
                            @if($task->assigned_to === auth()->id() && !$task->link)
                            <button type="button" 
                                    class="btn btn-success" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#submitLinkModal">
                                <i class="bi bi-send"></i> Submit Work Link
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk submit link -->
<div class="modal fade" id="submitLinkModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Submit Task Work Link</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('member.tasks.submit-link', $task) }}" method="POST" id="submitLinkForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="link" class="form-label">Work Link *</label>
                        <input type="url" 
                               class="form-control @error('link') is-invalid @enderror" 
                               id="link" 
                               name="link" 
                               value="{{ old('link') }}"
                               placeholder="https://drive.google.com/... or https://github.com/..."
                               required>
                        <div class="form-text">
                            Link to your work (Google Drive, GitHub, Figma, Website, etc.)
                        </div>
                        @error('link')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" 
                                  name="notes" 
                                  rows="3"
                                  placeholder="Brief description of what you've done...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitLinkBtn">
                        <i class="bi bi-send"></i> Submit Link
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal untuk update link -->
<div class="modal fade" id="updateLinkModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Task Link</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('member.tasks.update-link', $task) }}" method="POST" id="updateLinkForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="update_link" class="form-label">New Link *</label>
                        <input type="url" 
                               class="form-control @error('link') is-invalid @enderror" 
                               id="update_link" 
                               name="link" 
                               value="{{ old('link', $task->link) }}"
                               placeholder="https://drive.google.com/... or https://github.com/..."
                               required>
                        <div class="form-text">
                            Update your work link (Google Drive, GitHub, Figma, Website, etc.)
                        </div>
                        @error('link')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="update_notes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" 
                                  id="update_notes" 
                                  name="notes" 
                                  rows="3"
                                  placeholder="Reason for update...">{{ old('notes') }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="updateLinkBtn">
                        <i class="bi bi-check-circle"></i> Update Link
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal untuk add comment -->
<div class="modal fade" id="addCommentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Comment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('member.tasks.comments.store', $task) }}" method="POST" id="addCommentForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="comment_content" class="form-label">Comment *</label>
                        <textarea class="form-control @error('content') is-invalid @enderror" 
                                  id="comment_content" 
                                  name="content" 
                                  rows="4"
                                  placeholder="Add your comment here..."
                                  required>{{ old('content') }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="addCommentBtn">
                        <i class="bi bi-chat-left"></i> Add Comment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Task show page loaded for task #{{ $task->id }}');
        
        // Copy to clipboard
        window.copyToClipboard = function(text) {
            navigator.clipboard.writeText(text).then(function() {
                showToast('Link copied to clipboard!', 'success');
            }).catch(function() {
                alert('Failed to copy link. Please copy manually.');
            });
        }
        
        // Helper functions
        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-bg-${type === 'error' ? 'danger' : type} border-0 position-fixed bottom-0 end-0 m-3`;
            toast.style.zIndex = '1060';
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi ${type === 'success' ? 'bi-check-circle' : type === 'error' ? 'bi-exclamation-triangle' : 'bi-info-circle'} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;
            document.body.appendChild(toast);
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
            
            toast.addEventListener('hidden.bs.toast', function () {
                toast.remove();
            });
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.remove();
                }
            }, 5000);
        }
        
        // Form validation
        const forms = document.querySelectorAll('form[id$="Form"]');
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Processing...';
                }
                
                // Validate URL in link forms
                if (this.id.includes('Link')) {
                    const urlInput = this.querySelector('input[type="url"]');
                    if (urlInput && !isValidUrl(urlInput.value.trim())) {
                        e.preventDefault();
                        showToast('Please enter a valid URL (starting with http:// or https://)', 'error');
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = this.id.includes('submit') 
                                ? '<i class="bi bi-send"></i> Submit Link' 
                                : '<i class="bi bi-check-circle"></i> Update Link';
                        }
                        urlInput.focus();
                    }
                }
            });
        });
        
        // Helper function untuk validasi URL
        function isValidUrl(string) {
            try {
                new URL(string);
                return true;
            } catch (_) {
                return false;
            }
        }
        
        // Initialize Bootstrap tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Auto-focus first input in modals
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            modal.addEventListener('shown.bs.modal', function() {
                const input = this.querySelector('input, textarea');
                if (input) input.focus();
            });
        });
        
        // Enable form submission for status update buttons
        const statusForms = document.querySelectorAll('.btn-group-sm form');
        statusForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                const button = this.querySelector('button[type="submit"]');
                if (button) {
                    button.disabled = true;
                    button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
                }
            });
        });
    });
</script>

<style>
    .btn-group.flex-wrap {
        flex-wrap: wrap;
    }
    .btn-group.flex-wrap .btn,
    .btn-group.flex-wrap .form {
        margin-bottom: 0.5rem;
    }
    .avatar-placeholder {
        font-size: 14px;
        font-weight: bold;
    }
    .timeline-progress {
        background-color: #e9ecef;
    }
    .list-group-item {
        transition: all 0.2s;
    }
    .list-group-item:hover {
        background-color: #f8f9fa;
    }
    .progress-bar {
        transition: width 0.6s ease;
    }
    .btn-group .form {
        display: inline;
    }
    /* Tambahkan spacing untuk form di dalam button group */
    .btn-group-sm .form {
        margin: 0 2px;
    }
    .btn-group-sm button[type="submit"] {
        border-radius: 4px !important;
    }
    /* Style untuk disabled buttons dalam button group */
    .btn-group-sm button.disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    /* Toast styling */
    .toast {
        opacity: 1;
        transition: opacity 0.3s;
    }
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .btn-group.flex-wrap {
            justify-content: flex-start;
        }
        .btn-group.flex-wrap .btn,
        .btn-group.flex-wrap .form {
            flex: 0 0 auto;
            margin-right: 0.25rem;
        }
        .d-flex.gap-2 {
            gap: 0.5rem !important;
        }
    }
</style>
@endpush
@endsection