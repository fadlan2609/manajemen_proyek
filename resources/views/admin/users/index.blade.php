<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        .badge-role-admin { background-color: #dc3545; }
        .badge-role-project_manager { background-color: #0d6efd; }
        .badge-role-member { background-color: #198754; }
        .badge-status-active { background-color: #198754; }
        .badge-status-inactive { background-color: #6c757d; }
        .badge-status-suspended { background-color: #dc3545; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">Admin Panel</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link active" href="{{ route('admin.users.index') }}">Users</a>
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

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="bi bi-people"></i> User Management
                </h4>
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Add New User
                </a>
            </div>
            <div class="card-body">
                @if($users->isEmpty())
                    <div class="text-center py-5">
                        <i class="bi bi-people display-1 text-muted"></i>
                        <h5 class="mt-3">No Users Found</h5>
                        <p class="text-muted">Get started by adding your first user</p>
                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Add First User
                        </a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>User</th>
                                    <th>Contact</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Joined</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $user->avatar_thumbnail }}" 
                                                 alt="{{ $user->name }}" 
                                                 class="user-avatar me-2">
                                            <div>
                                                <strong>{{ $user->name }}</strong><br>
                                                <small class="text-muted">{{ $user->initials }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <i class="bi bi-envelope me-1"></i> {{ $user->email }}<br>
                                            @if($user->phone)
                                                <i class="bi bi-telephone me-1"></i> {{ $user->phone }}
                                            @else
                                                <span class="text-muted">No phone</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-role-{{ $user->role }}">
                                            {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                        </span>
                                        @if($user->position)
                                            <br><small>{{ $user->position }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-status-{{ $user->status }}">
                                            {{ ucfirst($user->status) }}
                                        </span>
                                        <br>
                                        <small class="text-muted">{{ $user->last_activity ?? 'No activity' }}</small>
                                    </td>
                                    <td>
                                        {{ $user->created_at->format('d M Y') }}<br>
                                        <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.users.show', $user->id) }}" 
                                               class="btn btn-info" 
                                               title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.users.edit', $user->id) }}" 
                                               class="btn btn-warning" 
                                               title="Edit User">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-danger" 
                                                    title="Delete User"
                                                    onclick="confirmDelete({{ $user->id }}, '{{ addslashes($user->name) }}')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} users
                        </div>
                        <div>
                            {{ $users->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete user <strong id="userName"></strong>?</p>
                    <p class="text-danger">This action cannot be undone!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Yes, Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmDelete(userId, userName) {
            document.getElementById('userName').textContent = userName;
            document.getElementById('deleteForm').action = `/admin/users/${userId}`;
            
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }
        
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>