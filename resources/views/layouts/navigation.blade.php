<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
    <div class="container-fluid">
        <!-- Logo -->
        <a class="navbar-brand fw-bold fs-4 text-primary" href="{{ route('dashboard') }}">
            <i class="bi bi-kanban-fill me-2"></i>
            PT Indonesia Gadai Oke
        </a>
        
        <!-- Toggle button for mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <!-- Navigation Links -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                @auth
                    @switch(Auth::user()->role)
                        @case('admin')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active fw-bold' : '' }}" 
                                   href="{{ route('admin.dashboard') }}">
                                    <i class="bi bi-speedometer2 me-1"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active fw-bold' : '' }}" 
                                   href="{{ route('admin.users.index') }}">
                                    <i class="bi bi-people me-1"></i> Users
                                </a>
                            </li>
                            @break
                            
                        @case('project_manager')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('project-manager.dashboard') ? 'active fw-bold' : '' }}" 
                                   href="{{ route('project-manager.dashboard') }}">
                                    <i class="bi bi-speedometer2 me-1"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('project-manager.projects.*') ? 'active fw-bold' : '' }}" 
                                   href="{{ route('project-manager.projects.index') }}">
                                    <i class="bi bi-folder me-1"></i> Projects
                                </a>
                            </li>
                            @break
                            
                        @case('member')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('member.dashboard') ? 'active fw-bold' : '' }}" 
                                   href="{{ route('member.dashboard') }}">
                                    <i class="bi bi-speedometer2 me-1"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('member.tasks.index') ? 'active fw-bold' : '' }}" 
                                   href="{{ route('member.tasks.index') }}">
                                    <i class="bi bi-list-task me-1"></i> Tasks
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('member.tasks.links') ? 'active fw-bold' : '' }}" 
                                   href="{{ route('member.tasks.links') }}">
                                    <i class="bi bi-link-45deg me-1"></i> Work Links
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('member.calendar.*') ? 'active fw-bold' : '' }}" 
                                   href="{{ route('member.calendar.view') }}">
                                    <i class="bi bi-calendar me-1"></i> Calendar
                                </a>
                            </li>
                            @break
                    @endswitch
                @endauth
            </ul>
            
            <!-- Right side: User dropdown or Auth links -->
            <ul class="navbar-nav">
                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            @if(Auth::user()->avatar)
                                <img src="{{ Auth::user()->avatar }}" class="rounded-circle me-2" width="32" height="32" alt="{{ Auth::user()->name }}">
                            @else
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                            @endif
                            <div class="text-start d-none d-lg-block">
                                <div class="fw-bold">{{ Auth::user()->name }}</div>
                                <small class="text-muted">{{ ucfirst(str_replace('_', ' ', Auth::user()->role)) }}</small>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="{{ route('member.profile') }}">
                                <i class="bi bi-person me-2"></i> Profile
                            </a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                                </button>
                            </form>
                        </div>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">
                            <i class="bi bi-person-plus me-1"></i> Register
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<!-- Mobile Navigation Menu (hidden by default) -->
<div class="d-lg-none">
    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileNav">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Menu</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            @auth
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-3">
                        @if(Auth::user()->avatar)
                            <img src="{{ Auth::user()->avatar }}" class="rounded-circle me-3" width="48" height="48" alt="{{ Auth::user()->name }}">
                        @else
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        @endif
                        <div>
                            <div class="fw-bold">{{ Auth::user()->name }}</div>
                            <small class="text-muted">{{ ucfirst(str_replace('_', ' ', Auth::user()->role)) }}</small>
                        </div>
                    </div>
                    
                    <div class="list-group">
                        @switch(Auth::user()->role)
                            @case('admin')
                                <a href="{{ route('admin.dashboard') }}" 
                                   class="list-group-item list-group-item-action {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                                </a>
                                <a href="{{ route('admin.users.index') }}" 
                                   class="list-group-item list-group-item-action {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                    <i class="bi bi-people me-2"></i> Users
                                </a>
                                @break
                                
                            @case('project_manager')
                                <a href="{{ route('project-manager.dashboard') }}" 
                                   class="list-group-item list-group-item-action {{ request()->routeIs('project-manager.dashboard') ? 'active' : '' }}">
                                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                                </a>
                                <a href="{{ route('project-manager.projects.index') }}" 
                                   class="list-group-item list-group-item-action {{ request()->routeIs('project-manager.projects.*') ? 'active' : '' }}">
                                    <i class="bi bi-folder me-2"></i> Projects
                                </a>
                                @break
                                
                            @case('member')
                                <a href="{{ route('member.dashboard') }}" 
                                   class="list-group-item list-group-item-action {{ request()->routeIs('member.dashboard') ? 'active' : '' }}">
                                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                                </a>
                                <a href="{{ route('member.tasks.index') }}" 
                                   class="list-group-item list-group-item-action {{ request()->routeIs('member.tasks.*') ? 'active' : '' }}">
                                    <i class="bi bi-list-task me-2"></i> Tasks
                                </a>
                                <a href="{{ route('member.tasks.links') }}" 
                                   class="list-group-item list-group-item-action {{ request()->routeIs('member.tasks.links') ? 'active' : '' }}">
                                    <i class="bi bi-link-45deg me-2"></i> Work Links
                                </a>
                                <a href="{{ route('member.calendar.view') }}" 
                                   class="list-group-item list-group-item-action {{ request()->routeIs('member.calendar.*') ? 'active' : '' }}">
                                    <i class="bi bi-calendar me-2"></i> Calendar
                                </a>
                                @break
                        @endswitch
                        
                        <a href="{{ route('member.profile') }}" 
                           class="list-group-item list-group-item-action {{ request()->routeIs('member.profile') ? 'active' : '' }}">
                            <i class="bi bi-person me-2"></i> Profile
                        </a>
                        
                        <form method="POST" action="{{ route('logout') }}" class="list-group-item">
                            @csrf
                            <button type="submit" class="btn btn-link text-decoration-none p-0 text-start w-100">
                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="d-grid gap-2">
                    <a href="{{ route('login') }}" class="btn btn-primary">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Login
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-outline-primary">
                        <i class="bi bi-person-plus me-1"></i> Register
                    </a>
                </div>
            @endauth
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Update active class based on current route
        const currentPath = window.location.pathname;
        document.querySelectorAll('.nav-link').forEach(link => {
            if (link.getAttribute('href') === currentPath) {
                link.classList.add('active', 'fw-bold');
            }
        });
    });
</script>
@endpush