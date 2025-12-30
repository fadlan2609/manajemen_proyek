<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Task Links</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #10b981;
            --secondary-color: #059669;
        }
        
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 20px 20px;
        }
        
        .link-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
            border-left: 4px solid var(--primary-color);
        }
        
        .link-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.12);
        }
        
        .link-url {
            background: #f8f9fa;
            padding: 0.8rem;
            border-radius: 8px;
            font-family: monospace;
            word-break: break-all;
            margin: 1rem 0;
        }
        
        .badge-priority {
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .priority-high { background: #fee2e2; color: #dc2626; }
        .priority-medium { background: #fef3c7; color: #d97706; }
        .priority-low { background: #d1fae5; color: #059669; }
        
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #6b7280;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #d1d5db;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="bi bi-link-45deg"></i> My Task Links</h1>
                    <p class="mb-0">All submitted links for your tasks</p>
                </div>
                <div>
                    <a href="{{ route('member.dashboard') }}" class="btn btn-light">
                        <i class="bi bi-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container">
        <!-- Stats -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title">{{ $tasks->total() }}</h5>
                        <p class="card-text">Total Links</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title">{{ $tasks->where('status', 'done')->count() }}</h5>
                        <p class="card-text">Completed Tasks</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <h5 class="card-title">{{ $tasks->where('status', 'in_progress')->count() }}</h5>
                        <p class="card-text">In Progress</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <h5 class="card-title">{{ $tasks->where('status', 'todo')->count() }}</h5>
                        <p class="card-text">To Do</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Links List -->
        @if($tasks->isEmpty())
            <div class="empty-state">
                <i class="bi bi-link-45deg"></i>
                <h3>No Links Found</h3>
                <p>You haven't submitted any links for your tasks yet.</p>
                <a href="{{ route('member.tasks.index') }}" class="btn btn-primary">
                    <i class="bi bi-list-check"></i> Go to My Tasks
                </a>
            </div>
        @else
            @foreach($tasks as $task)
            <div class="link-card">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h5 class="mb-1">{{ $task->title }}</h5>
                        @if($task->project)
                            <span class="badge bg-secondary">
                                <i class="bi bi-folder"></i> {{ $task->project->name }}
                            </span>
                        @endif
                        <span class="badge-priority priority-{{ $task->priority }}">
                            {{ ucfirst($task->priority) }} Priority
                        </span>
                    </div>
                    <div>
                        <span class="badge bg-{{ $task->status == 'done' ? 'success' : ($task->status == 'in_progress' ? 'warning' : 'secondary') }}">
                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                        </span>
                    </div>
                </div>
                
                @if($task->description)
                    <p class="text-muted mb-2">{{ Str::limit($task->description, 150) }}</p>
                @endif
                
                <div class="link-url">
                    <i class="bi bi-link"></i> 
                    <a href="{{ $task->link }}" target="_blank" class="text-decoration-none">
                        {{ $task->link }}
                    </a>
                    <button class="btn btn-sm btn-outline-secondary ms-2" onclick="copyToClipboard('{{ $task->link }}')">
                        <i class="bi bi-clipboard"></i> Copy
                    </button>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        <small class="text-muted">
                            <i class="bi bi-calendar"></i> 
                            Updated: {{ $task->updated_at->diffForHumans() }}
                        </small>
                        @if($task->deadline)
                            <small class="ms-3 {{ $task->deadline < now() && $task->status != 'done' ? 'text-danger' : 'text-muted' }}">
                                <i class="bi bi-clock"></i> 
                                Deadline: {{ $task->deadline->format('M d, Y') }}
                            </small>
                        @endif
                    </div>
                    <div>
                        <a href="{{ route('member.tasks.show', $task) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i> View Task
                        </a>
                    </div>
                </div>
                
                <!-- Link History (if any) -->
                @if($task->linkHistories->isNotEmpty())
                    <div class="mt-3 pt-3 border-top">
                        <h6><i class="bi bi-clock-history"></i> Link History</h6>
                        <div class="list-group list-group-flush">
                            @foreach($task->linkHistories->take(3) as $history)
                            <div class="list-group-item border-0 px-0 py-1">
                                <small class="d-block">
                                    <strong>{{ $history->user->name ?? 'You' }}</strong> 
                                    @if($history->old_link && $history->new_link)
                                        updated link
                                    @elseif($history->new_link && !$history->old_link)
                                        submitted link
                                    @elseif($history->old_link && !$history->new_link)
                                        removed link
                                    @endif
                                    <span class="text-muted">({{ $history->created_at->diffForHumans() }})</span>
                                </small>
                                @if($history->notes)
                                    <small class="text-muted d-block">{{ $history->notes }}</small>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
            @endforeach
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $tasks->links() }}
            </div>
        @endif
    </div>
    
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                alert('Link copied to clipboard!');
            }, function(err) {
                console.error('Could not copy text: ', err);
            });
        }
        
        // Auto-hide alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);
    </script>
</body>
</html>