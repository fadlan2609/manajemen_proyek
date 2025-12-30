<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3a0ca3;
            --success-color: #4cc9f0;
            --warning-color: #f72585;
            --info-color: #7209b7;
            --dark-color: #2b2d42;
            --light-color: #f8f9fa;
            --sidebar-width: 260px;
            --header-height: 70px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f5f7fb;
            color: #333;
            overflow-x: hidden;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 20px 0;
            z-index: 1000;
            box-shadow: 5px 0 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .sidebar-brand {
            padding: 0 20px 30px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-brand h2 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .sidebar-brand .brand-icon {
            color: #4cc9f0;
            font-size: 1.8rem;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .nav-item {
            margin-bottom: 5px;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s ease;
            border-radius: 0 30px 30px 0;
            margin: 5px 0;
            position: relative;
            overflow: hidden;
        }

        .nav-link:hover, .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }

        .nav-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: var(--success-color);
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .nav-link:hover::before, .nav-link.active::before {
            transform: scaleY(1);
        }

        .nav-icon {
            font-size: 1.2rem;
            min-width: 24px;
        }

        /* Main Content Styles */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        /* Header Styles */
        .main-header {
            background: white;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
            animation: slideDown 0.5s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .welcome-section h1 {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 5px;
        }

        .welcome-section p {
            color: #6c757d;
            margin: 0;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(45deg, var(--primary-color), var(--info-color));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.2rem;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--success-color));
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 20px;
        }

        .stat-card.users .stat-icon {
            background: rgba(67, 97, 238, 0.1);
            color: var(--primary-color);
        }

        .stat-card.projects .stat-icon {
            background: rgba(76, 201, 240, 0.1);
            color: var(--success-color);
        }

        .stat-card.tasks .stat-icon {
            background: rgba(247, 37, 133, 0.1);
            color: var(--warning-color);
        }

        .stat-card.revenue .stat-icon {
            background: rgba(114, 9, 183, 0.1);
            color: var(--info-color);
        }

        .stat-number {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 5px;
        }

        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }

        .stat-change {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.85rem;
        }

        .stat-change.positive {
            color: #28a745;
        }

        .stat-change.negative {
            color: #dc3545;
        }

        /* Quick Actions */
        .quick-actions {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            color: var(--primary-color);
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .action-btn {
            background: white;
            border: 2px solid #e9ecef;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }

        .action-btn:hover {
            border-color: var(--primary-color);
            background: rgba(67, 97, 238, 0.05);
            transform: translateY(-3px);
            color: var(--primary-color);
        }

        .action-icon {
            font-size: 2rem;
            margin-bottom: 10px;
            color: var(--primary-color);
        }

        .action-label {
            font-weight: 500;
            color: var(--dark-color);
        }

        /* Recent Activity */
        .recent-activity {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .activity-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .activity-item {
            padding: 15px 0;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: white;
        }

        .activity-icon.user {
            background: linear-gradient(45deg, var(--primary-color), var(--info-color));
        }

        .activity-icon.project {
            background: linear-gradient(45deg, var(--success-color), #2a9d8f);
        }

        .activity-icon.task {
            background: linear-gradient(45deg, var(--warning-color), #ff9e00);
        }

        .activity-details {
            flex: 1;
        }

        .activity-text {
            color: var(--dark-color);
            margin-bottom: 5px;
        }

        .activity-time {
            color: #6c757d;
            font-size: 0.85rem;
        }

        /* Mobile Responsive */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .menu-toggle {
                display: block;
            }
        }

        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            body {
                background-color: #1a1d28;
                color: #e9ecef;
            }
            
            .main-header,
            .stat-card,
            .quick-actions,
            .recent-activity {
                background: #2b2d42;
                color: #e9ecef;
            }
            
            .welcome-section h1 {
                color: #e9ecef;
            }
            
            .welcome-section p {
                color: #adb5bd;
            }
            
            .stat-number {
                color: #e9ecef;
            }
            
            .action-btn {
                background: #2b2d42;
                border-color: #495057;
                color: #e9ecef;
            }
            
            .activity-text {
                color: #e9ecef;
            }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(var(--primary-color), var(--secondary-color));
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(var(--secondary-color), var(--primary-color));
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <h2>
                <i class="bi bi-shield-lock-fill brand-icon"></i>
                AdminPro
            </h2>
            <p class="text-muted mb-0" style="color: rgba(255,255,255,0.7) !important;">Control Panel</p>
        </div>
        
        <ul class="nav flex-column sidebar-menu">
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link active">
                    <i class="bi bi-speedometer2 nav-icon"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.users.index') }}" class="nav-link">
                    <i class="bi bi-people nav-icon"></i>
                    <span>User Management</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-briefcase nav-icon"></i>
                    <span>Projects</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-list-task nav-icon"></i>
                    <span>Tasks</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-bar-chart nav-icon"></i>
                    <span>Analytics</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-gear nav-icon"></i>
                    <span>Settings</span>
                </a>
            </li>
            <li class="nav-item mt-4">
                <a href="{{ route('logout') }}" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="bi bi-box-arrow-right nav-icon"></i>
                    <span>Logout</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <header class="main-header">
            <div class="welcome-section">
                <h1>Welcome back, {{ Auth::user()->name }}! 👋</h1>
                <p>Here's what's happening with your system today.</p>
            </div>
            <div class="user-info">
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div>
                    <strong>{{ Auth::user()->name }}</strong>
                    <p class="text-muted mb-0">Administrator</p>
                </div>
            </div>
        </header>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card users">
                <div class="stat-icon">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="stat-number">1,248</div>
                <div class="stat-label">Total Users</div>
                <div class="stat-change positive">
                    <i class="bi bi-arrow-up"></i>
                    12.5% increase
                </div>
            </div>
            
            <div class="stat-card projects">
                <div class="stat-icon">
                    <i class="bi bi-folder-fill"></i>
                </div>
                <div class="stat-number">84</div>
                <div class="stat-label">Active Projects</div>
                <div class="stat-change positive">
                    <i class="bi bi-arrow-up"></i>
                    8.2% increase
                </div>
            </div>
            
            <div class="stat-card tasks">
                <div class="stat-icon">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="stat-number">324</div>
                <div class="stat-label">Completed Tasks</div>
                <div class="stat-change positive">
                    <i class="bi bi-arrow-up"></i>
                    24.7% increase
                </div>
            </div>
            
            <div class="stat-card revenue">
                <div class="stat-icon">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
                <div class="stat-number">$24.5K</div>
                <div class="stat-label">Monthly Revenue</div>
                <div class="stat-change positive">
                    <i class="bi bi-arrow-up"></i>
                    18.3% increase
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <h3 class="section-title">
                <i class="bi bi-lightning-fill"></i>
                Quick Actions
            </h3>
            <div class="actions-grid">
                <a href="{{ route('admin.users.create') }}" class="action-btn">
                    <div class="action-icon">
                        <i class="bi bi-person-plus-fill"></i>
                    </div>
                    <div class="action-label">Add New User</div>
                </a>
                
                <a href="#" class="action-btn">
                    <div class="action-icon">
                        <i class="bi bi-plus-square-fill"></i>
                    </div>
                    <div class="action-label">Create Project</div>
                </a>
                
                <a href="#" class="action-btn">
                    <div class="action-icon">
                        <i class="bi bi-file-earmark-text-fill"></i>
                    </div>
                    <div class="action-label">Generate Report</div>
                </a>
                
                <a href="#" class="action-btn">
                    <div class="action-icon">
                        <i class="bi bi-bell-fill"></i>
                    </div>
                    <div class="action-label">View Notifications</div>
                </a>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="recent-activity">
            <h3 class="section-title">
                <i class="bi bi-clock-history"></i>
                Recent Activity
            </h3>
            <ul class="activity-list">
                <li class="activity-item">
                    <div class="activity-icon user">
                        <i class="bi bi-person-plus"></i>
                    </div>
                    <div class="activity-details">
                        <div class="activity-text">
                            <strong>John Doe</strong> created a new account
                        </div>
                        <div class="activity-time">10 minutes ago</div>
                    </div>
                </li>
                
                <li class="activity-item">
                    <div class="activity-icon project">
                        <i class="bi bi-folder-plus"></i>
                    </div>
                    <div class="activity-details">
                        <div class="activity-text">
                            <strong>Project "Redesign"</strong> was assigned to Sarah
                        </div>
                        <div class="activity-time">45 minutes ago</div>
                    </div>
                </li>
                
                <li class="activity-item">
                    <div class="activity-icon task">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                    <div class="activity-details">
                        <div class="activity-text">
                            <strong>Task #324</strong> was completed by Alex
                        </div>
                        <div class="activity-time">2 hours ago</div>
                    </div>
                </li>
                
                <li class="activity-item">
                    <div class="activity-icon user">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <div class="activity-details">
                        <div class="activity-text">
                            System backup was <strong>successfully completed</strong>
                        </div>
                        <div class="activity-time">5 hours ago</div>
                    </div>
                </li>
            </ul>
        </div>
    </div>

    <script>
        // Add some interactive animations
        document.addEventListener('DOMContentLoaded', function() {
            // Animate stats cards on scroll
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            // Observe all stat cards
            document.querySelectorAll('.stat-card').forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                observer.observe(card);
            });

            // Add hover effect to action buttons
            const actionBtns = document.querySelectorAll('.action-btn');
            actionBtns.forEach(btn => {
                btn.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px) scale(1.02)';
                });
                
                btn.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });

            // Update current time
            function updateTime() {
                const now = new Date();
                const options = { 
                    weekday: 'long', 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                };
                const timeString = now.toLocaleDateString('en-US', options);
                
                // Update if there's a time element
                const timeElement = document.querySelector('.current-time');
                if (timeElement) {
                    timeElement.textContent = timeString;
                }
            }

            // Update time every minute
            updateTime();
            setInterval(updateTime, 60000);
        });

        // Mobile menu toggle
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('active');
        }
    </script>
</body>
</html>