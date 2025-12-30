<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Manager Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-color: #3b82f6;
            --secondary-color: #1d4ed8;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #8b5cf6;
            --dark-color: #1f2937;
            --light-color: #f9fafb;
            --sidebar-bg: #111827;
            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --hover-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            color: var(--dark-color);
            min-height: 100vh;
        }

        /* Navigation */
        .pm-navbar {
            background: white;
            padding: 1rem 2rem;
            box-shadow: var(--card-shadow);
            position: sticky;
            top: 0;
            z-index: 1000;
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .navbar-brand i {
            font-size: 1.8rem;
            color: var(--secondary-color);
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .nav-link {
            color: #6b7280;
            font-weight: 500;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--primary-color);
            background: rgba(59, 130, 246, 0.1);
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 3px;
            background: var(--primary-color);
            border-radius: 2px;
            transition: width 0.3s ease;
        }

        .nav-link:hover::after, .nav-link.active::after {
            width: 80%;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-left: auto;
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .notification-badge {
            position: relative;
        }

        .notification-badge::after {
            content: '3';
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--danger-color);
            color: white;
            font-size: 0.7rem;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Main Content */
        .main-content {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .welcome-section {
            margin-bottom: 2rem;
            animation: fadeIn 0.6s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .welcome-card {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2.5rem;
            border-radius: 16px;
            position: relative;
            overflow: hidden;
            box-shadow: var(--hover-shadow);
        }

        .welcome-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .welcome-card h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
        }

        .welcome-card p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 1.5rem;
            position: relative;
            z-index: 1;
        }

        .date-time {
            background: rgba(255, 255, 255, 0.1);
            padding: 0.8rem 1.5rem;
            border-radius: 50px;
            display: inline-block;
            backdrop-filter: blur(10px);
            position: relative;
            z-index: 1;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            border-left: 4px solid;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--hover-shadow);
        }

        .stat-card.projects { border-color: var(--primary-color); }
        .stat-card.tasks { border-color: var(--success-color); }
        .stat-card.team { border-color: var(--warning-color); }
        .stat-card.deadline { border-color: var(--danger-color); }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

        .stat-card.projects .stat-icon { background: var(--primary-color); }
        .stat-card.tasks .stat-icon { background: var(--success-color); }
        .stat-card.team .stat-icon { background: var(--warning-color); }
        .stat-card.deadline .stat-icon { background: var(--danger-color); }

        .stat-number {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #6b7280;
            font-size: 0.9rem;
        }

        .stat-trend {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.85rem;
            margin-top: 0.5rem;
        }

        .trend-up { color: var(--success-color); }
        .trend-down { color: var(--danger-color); }

        /* Project Overview */
        .project-overview {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
            margin-bottom: 2rem;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .section-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--dark-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            color: var(--primary-color);
        }

        .project-list {
            display: grid;
            gap: 1rem;
        }

        .project-item {
            padding: 1rem;
            border-radius: 8px;
            background: #f9fafb;
            border-left: 4px solid;
            transition: all 0.3s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .project-item:hover {
            background: #f3f4f6;
            transform: translateX(5px);
        }

        .project-item.critical { border-color: var(--danger-color); }
        .project-item.high { border-color: var(--warning-color); }
        .project-item.medium { border-color: var(--primary-color); }
        .project-item.low { border-color: var(--success-color); }

        .project-info h5 {
            font-weight: 600;
            margin-bottom: 0.3rem;
        }

        .project-meta {
            display: flex;
            gap: 1rem;
            font-size: 0.85rem;
            color: #6b7280;
        }

        .project-progress {
            width: 150px;
        }

        .progress-bar {
            height: 8px;
            background: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 0.3rem;
        }

        .progress-fill {
            height: 100%;
            border-radius: 4px;
            background: var(--primary-color);
        }

        .progress-text {
            font-size: 0.8rem;
            color: #6b7280;
            text-align: right;
        }

        /* Charts Container */
        .charts-container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        @media (max-width: 1024px) {
            .charts-container {
                grid-template-columns: 1fr;
            }
        }

        .chart-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
        }

        .chart-card h4 {
            margin-bottom: 1rem;
            font-weight: 600;
            color: var(--dark-color);
        }

        /* Quick Actions */
        .quick-actions {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .action-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.5rem 1rem;
            background: #f9fafb;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            text-decoration: none;
            color: var(--dark-color);
            transition: all 0.3s ease;
        }

        .action-btn:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            transform: translateY(-3px);
        }

        .action-icon {
            font-size: 2rem;
            margin-bottom: 0.8rem;
        }

        .action-label {
            font-weight: 500;
            text-align: center;
        }

        /* Activity Feed */
        .activity-feed {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
            margin-top: 2rem;
        }

        .activity-list {
            list-style: none;
        }

        .activity-item {
            padding: 1rem 0;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
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
            color: white;
            flex-shrink: 0;
        }

        .activity-icon.project { background: var(--primary-color); }
        .activity-icon.task { background: var(--success-color); }
        .activity-icon.team { background: var(--warning-color); }
        .activity-icon.deadline { background: var(--danger-color); }

        .activity-content {
            flex: 1;
        }

        .activity-text {
            color: var(--dark-color);
            margin-bottom: 0.3rem;
        }

        .activity-time {
            color: #6b7280;
            font-size: 0.85rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .pm-navbar {
                padding: 1rem;
            }
            
            .nav-links {
                display: none;
            }
            
            .main-content {
                padding: 1rem;
            }
            
            .welcome-card h1 {
                font-size: 2rem;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Custom Utilities */
        .badge {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-critical { background: #fee2e2; color: #dc2626; }
        .badge-high { background: #fef3c7; color: #d97706; }
        .badge-medium { background: #dbeafe; color: #2563eb; }
        .badge-low { background: #d1fae5; color: #059669; }

        .text-gradient {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="pm-navbar">
        <div class="container-fluid d-flex align-items-center">
            <a href="{{ route('project-manager.dashboard') }}" class="navbar-brand">
                <i class="bi bi-kanban-fill"></i>
                ProjectFlow
            </a>
            
            <div class="nav-links">
                <a href="{{ route('project-manager.dashboard') }}" class="nav-link active">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <a href="{{ route('project-manager.projects.index') }}" class="nav-link">
                    <i class="bi bi-kanban"></i> Projects
                </a>
                <a href="#" class="nav-link">
                    <i class="bi bi-people"></i> Team
                </a>
                <a href="#" class="nav-link">
                    <i class="bi bi-calendar-check"></i> Calendar
                </a>
                <a href="#" class="nav-link">
                    <i class="bi bi-graph-up"></i> Reports
                </a>
            </div>
            
            <div class="user-profile">
                <div class="notification-badge">
                    <i class="bi bi-bell" style="font-size: 1.2rem; color: #6b7280;"></i>
                </div>
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div>
                    <strong>{{ Auth::user()->name }}</strong>
                    <p class="text-muted mb-0" style="font-size: 0.85rem;">Project Manager</p>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Welcome Section -->
        <section class="welcome-section">
            <div class="welcome-card">
                <h1>Welcome back, {{ Auth::user()->name }}! 🚀</h1>
                <p>Monitor your projects, track progress, and lead your team to success.</p>
                <div class="date-time">
                    <i class="bi bi-calendar3 me-2"></i>
                    <span id="currentDateTime">Loading...</span>
                </div>
            </div>
        </section>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card projects">
                <div class="stat-header">
                    <div>
                        <div class="stat-number">12</div>
                        <div class="stat-label">Active Projects</div>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-kanban"></i>
                    </div>
                </div>
                <div class="stat-trend trend-up">
                    <i class="bi bi-arrow-up"></i>
                    <span>3 new this month</span>
                </div>
            </div>

            <div class="stat-card tasks">
                <div class="stat-header">
                    <div>
                        <div class="stat-number">156</div>
                        <div class="stat-label">Total Tasks</div>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-list-task"></i>
                    </div>
                </div>
                <div class="stat-trend trend-up">
                    <i class="bi bi-arrow-up"></i>
                    <span>87% completion rate</span>
                </div>
            </div>

            <div class="stat-card team">
                <div class="stat-header">
                    <div>
                        <div class="stat-number">24</div>
                        <div class="stat-label">Team Members</div>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
                <div class="stat-trend trend-up">
                    <i class="bi bi-arrow-up"></i>
                    <span>2 new members</span>
                </div>
            </div>

            <div class="stat-card deadline">
                <div class="stat-header">
                    <div>
                        <div class="stat-number">5</div>
                        <div class="stat-label">Upcoming Deadlines</div>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-clock"></i>
                    </div>
                </div>
                <div class="stat-trend trend-down">
                    <i class="bi bi-exclamation-triangle"></i>
                    <span>2 critical this week</span>
                </div>
            </div>
        </div>

        <!-- Project Overview & Charts -->
        <div class="charts-container">
            <div class="chart-card">
                <h4>Project Progress Overview</h4>
                <canvas id="progressChart" height="250"></canvas>
            </div>
            
            <div class="chart-card">
                <h4>Task Status Distribution</h4>
                <canvas id="taskChart" height="250"></canvas>
            </div>
        </div>

        <!-- Project Overview -->
        <div class="project-overview">
            <div class="section-header">
                <h3 class="section-title">
                    <i class="bi bi-kanban"></i>
                    Active Projects
                </h3>
                <a href="{{ route('project-manager.projects.index') }}" class="btn btn-sm btn-outline-primary">
                    View All <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            
            <div class="project-list">
                <div class="project-item critical">
                    <div class="project-info">
                        <h5>Website Redesign</h5>
                        <div class="project-meta">
                            <span><i class="bi bi-calendar"></i> Due: Dec 15</span>
                            <span><i class="bi bi-people"></i> 5 members</span>
                            <span class="badge badge-critical">Critical</span>
                        </div>
                    </div>
                    <div class="project-progress">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 45%"></div>
                        </div>
                        <div class="progress-text">45% Complete</div>
                    </div>
                </div>

                <div class="project-item high">
                    <div class="project-info">
                        <h5>Mobile App Development</h5>
                        <div class="project-meta">
                            <span><i class="bi bi-calendar"></i> Due: Jan 20</span>
                            <span><i class="bi bi-people"></i> 8 members</span>
                            <span class="badge badge-high">High Priority</span>
                        </div>
                    </div>
                    <div class="project-progress">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 78%"></div>
                        </div>
                        <div class="progress-text">78% Complete</div>
                    </div>
                </div>

                <div class="project-item medium">
                    <div class="project-info">
                        <h5>Marketing Campaign Q1</h5>
                        <div class="project-meta">
                            <span><i class="bi bi-calendar"></i> Due: Feb 10</span>
                            <span><i class="bi bi-people"></i> 4 members</span>
                            <span class="badge badge-medium">Medium Priority</span>
                        </div>
                    </div>
                    <div class="project-progress">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 92%"></div>
                        </div>
                        <div class="progress-text">92% Complete</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <div class="section-header">
                <h3 class="section-title">
                    <i class="bi bi-lightning-charge"></i>
                    Quick Actions
                </h3>
            </div>
            
            <div class="actions-grid">
                <a href="{{ route('project-manager.projects.create') }}" class="action-btn">
                    <div class="action-icon">
                        <i class="bi bi-plus-circle"></i>
                    </div>
                    <div class="action-label">New Project</div>
                </a>
                
                <a href="#" class="action-btn">
                    <div class="action-icon">
                        <i class="bi bi-person-plus"></i>
                    </div>
                    <div class="action-label">Add Team Member</div>
                </a>
                
                <a href="#" class="action-btn">
                    <div class="action-icon">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <div class="action-label">Generate Report</div>
                </a>
                
                <a href="#" class="action-btn">
                    <div class="action-icon">
                        <i class="bi bi-calendar-plus"></i>
                    </div>
                    <div class="action-label">Schedule Meeting</div>
                </a>
            </div>
        </div>

        <!-- Activity Feed -->
        <div class="activity-feed">
            <div class="section-header">
                <h3 class="section-title">
                    <i class="bi bi-clock-history"></i>
                    Recent Activity
                </h3>
            </div>
            
            <ul class="activity-list">
                <li class="activity-item">
                    <div class="activity-icon project">
                        <i class="bi bi-kanban"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-text">
                            <strong>Sarah Johnson</strong> completed task "Design homepage mockup"
                        </div>
                        <div class="activity-time">10 minutes ago</div>
                    </div>
                </li>
                
                <li class="activity-item">
                    <div class="activity-icon task">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-text">
                            <strong>Mike Chen</strong> updated progress on "Mobile App" to 78%
                        </div>
                        <div class="activity-time">1 hour ago</div>
                    </div>
                </li>
                
                <li class="activity-item">
                    <div class="activity-icon team">
                        <i class="bi bi-people"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-text">
                            <strong>Alex Rodriguez</strong> joined the "Website Redesign" project
                        </div>
                        <div class="activity-time">3 hours ago</div>
                    </div>
                </li>
                
                <li class="activity-item">
                    <div class="activity-icon deadline">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-text">
                            Deadline approaching for <strong>Marketing Campaign</strong> (3 days left)
                        </div>
                        <div class="activity-time">5 hours ago</div>
                    </div>
                </li>
            </ul>
        </div>
    </div>

    <script>
        // Update current date and time
        function updateDateTime() {
            const now = new Date();
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            };
            document.getElementById('currentDateTime').textContent = 
                now.toLocaleDateString('en-US', options);
        }
        
        updateDateTime();
        setInterval(updateDateTime, 1000);

        // Initialize Progress Chart
        const progressCtx = document.getElementById('progressChart').getContext('2d');
        const progressChart = new Chart(progressCtx, {
            type: 'bar',
            data: {
                labels: ['Website', 'Mobile App', 'Marketing', 'Research', 'Development', 'Testing'],
                datasets: [{
                    label: 'Progress (%)',
                    data: [45, 78, 92, 60, 85, 30],
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.7)',
                        'rgba(16, 185, 129, 0.7)',
                        'rgba(245, 158, 11, 0.7)',
                        'rgba(139, 92, 246, 0.7)',
                        'rgba(59, 130, 246, 0.7)',
                        'rgba(16, 185, 129, 0.7)'
                    ],
                    borderColor: [
                        'rgb(59, 130, 246)',
                        'rgb(16, 185, 129)',
                        'rgb(245, 158, 11)',
                        'rgb(139, 92, 246)',
                        'rgb(59, 130, 246)',
                        'rgb(16, 185, 129)'
                    ],
                    borderWidth: 1,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Progress: ${context.raw}%`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                }
            }
        });

        // Initialize Task Distribution Chart
        const taskCtx = document.getElementById('taskChart').getContext('2d');
        const taskChart = new Chart(taskCtx, {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'In Progress', 'Pending', 'Overdue'],
                datasets: [{
                    data: [45, 30, 20, 5],
                    backgroundColor: [
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)'
                    ],
                    borderColor: [
                        'rgb(16, 185, 129)',
                        'rgb(59, 130, 246)',
                        'rgb(245, 158, 11)',
                        'rgb(239, 68, 68)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((context.raw / total) * 100);
                                return `${context.label}: ${context.raw} tasks (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Add hover effects to project items
        document.querySelectorAll('.project-item').forEach(item => {
            item.addEventListener('mouseenter', function() {
                this.style.boxShadow = '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)';
            });
            
            item.addEventListener('mouseleave', function() {
                this.style.boxShadow = 'none';
            });
        });

        // Animate stats on scroll
        const observerOptions = {
            threshold: 0.2,
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

        // Animate stat cards
        document.querySelectorAll('.stat-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(card);
        });

        // Simulate real-time updates
        setInterval(() => {
            const tasks = document.querySelector('.stat-card.tasks .stat-number');
            const currentTasks = parseInt(tasks.textContent);
            tasks.textContent = currentTasks + Math.floor(Math.random() * 3);
            
            // Update chart data randomly
            progressChart.data.datasets[0].data = progressChart.data.datasets[0].data.map(
                value => Math.min(100, value + Math.floor(Math.random() * 3))
            );
            progressChart.update();
        }, 10000);
    </script>
</body>
</html>