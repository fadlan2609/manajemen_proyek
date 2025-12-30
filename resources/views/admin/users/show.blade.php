<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Details - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">Admin Panel</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="{{ route('admin.users.index') }}">Users</a>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-danger">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-person-badge"></i> User Profile</h5>
                    </div>
                    <div class="card-body text-center">
                        <img src="{{ $user->avatar_url }}" 
                             alt="{{ $user->name }}" 
                             class="rounded-circle mb-3" 
                             width="120" 
                             height="120">
                        <h4>{{ $user->name }}</h4>
                        <p class="text-muted">{{ $user->email }}</p>
                        
                        <div class="d-flex justify-content-center mb-3">
                            <span class="badge bg-{{ $user->role_color }} fs-6">
                                {{ $user->role_name }}
                            </span>
                            <span class="badge bg-{{ $user->status_color }} fs-6 ms-2">
                                {{ $user->status_name }}
                            </span>
                        </div>
                        
                        <div class="list-group list-group-flush text-start">
                            <div class="list-group-item">
                                <i class="bi bi-telephone me-2"></i>
                                <strong>Phone:</strong> {{ $user->phone ?? 'Not set' }}
                            </div>
                            <div class="list-group-item">
                                <i class="bi bi-briefcase me-2"></i>
                                <strong>Position:</strong> {{ $user->position ?? 'Not set' }}
                            </div>
                            <div class="list-group-item">
                                <i class="bi bi-building me-2"></i>
                                <strong>Department:</strong> {{ $user->department ?? 'Not set' }}
                            </div>
                            <div class="list-group-item">
                                <i class="bi bi-calendar me-2"></i>
                                <strong>Joined:</strong> {{ $user->created_at->format('d M Y') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-info-circle"></i> User Information</h5>
                        <div>
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning btn-sm">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm ms-2">
                                <i class="bi bi-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6><i class="bi bi-activity"></i> Activity Level</h6>
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar bg-{{ $user->activity_level == 'High' ? 'success' : ($user->activity_level == 'Medium' ? 'warning' : 'secondary') }}" 
                                         role="progressbar" 
                                         style="width: {{ $user->activity_level == 'High' ? '80%' : ($user->activity_level == 'Medium' ? '50%' : '20%') }}"
                                         aria-valuenow="{{ $user->activity_level == 'High' ? '80' : ($user->activity_level == 'Medium' ? '50' : '20') }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        {{ $user->activity_level }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6><i class="bi bi-graph-up"></i> Performance Score</h6>
                                <div class="display-4 text-center">
                                    {{ $user->performance_score }}%
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0"><i class="bi bi-list-task"></i> Workload Summary</h6>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Total Tasks:</strong> {{ $user->current_workload['total'] ?? 0 }}</p>
                                        <p><strong>High Priority:</strong> {{ $user->current_workload['high_priority'] ?? 0 }}</p>
                                        <p><strong>Medium Priority:</strong> {{ $user->current_workload['medium_priority'] ?? 0 }}</p>
                                        <p><strong>Low Priority:</strong> {{ $user->current_workload['low_priority'] ?? 0 }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0"><i class="bi bi-clock-history"></i> Recent Activity</h6>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Last Activity:</strong> {{ $user->last_activity ?? 'No recent activity' }}</p>
                                        <p><strong>Last Login:</strong> {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never logged in' }}</p>
                                        <p><strong>Email Verified:</strong> {{ $user->email_verified_at ? 'Yes' : 'No' }}</p>
                                        <p><strong>Account Status:</strong> 
                                            <span class="badge bg-{{ $user->status_color }}">
                                                {{ $user->status_name }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h6><i class="bi bi-exclamation-triangle"></i> Danger Zone</h6>
                            <div class="alert alert-danger">
                                <h6 class="alert-heading">Delete User Account</h6>
                                <p class="mb-2">Once you delete a user account, there is no going back. Please be certain.</p>
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-danger" 
                                            onclick="return confirm('Are you sure you want to delete this user?')">
                                        <i class="bi bi-trash"></i> Delete This User
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>