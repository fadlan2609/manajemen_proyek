<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard - Team Member</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-color: #10b981;
            --secondary-color: #059669;
            --accent-color: #3b82f6;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --light-color: #f9fafb;
            --dark-color: #1f2937;
            --gray-color: #6b7280;
            --sidebar-bg: #111827;
            --card-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            --hover-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            color: var(--dark-color);
            min-height: 100vh;
        }

        /* Header */
        .member-header {
            background: white;
            padding: 1rem 2rem;
            box-shadow: var(--card-shadow);
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.98);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1400px;
            margin: 0 auto;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .brand-logo {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }

        .brand-text h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark-color);
            margin: 0;
        }

        .brand-text span {
            font-size: 0.85rem;
            color: var(--gray-color);
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .notification-bell {
            position: relative;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 50%;
            transition: background 0.3s ease;
        }

        .notification-bell:hover {
            background: var(--light-color);
        }

        .notification-badge {
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

        .user-profile {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.5rem;
            border-radius: 50px;
            background: var(--light-color);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .user-profile:hover {
            background: #e5e7eb;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .user-info h4 {
            font-size: 1rem;
            font-weight: 600;
            margin: 0;
        }

        .user-info p {
            font-size: 0.85rem;
            color: var(--gray-color);
            margin: 0;
        }

        /* Main Content */
        .main-content {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 2rem;
            animation: fadeIn 0.6s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Welcome Card */
        .welcome-card {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2.5rem;
            border-radius: 20px;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
            box-shadow: var(--hover-shadow);
        }

        .welcome-card::before {
            content: '';
            position: absolute;
            top: -100px;
            right: -100px;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .welcome-content {
            position: relative;
            z-index: 1;
        }

        .welcome-title {
            font-family: 'Poppins', sans-serif;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .welcome-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 1.5rem;
        }

        .date-display {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            padding: 0.8rem 1.5rem;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        /* Stats Overview */
        .stats-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--hover-shadow);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .stat-card.todo .stat-icon { background: #fef3c7; color: #d97706; }
        .stat-card.progress .stat-icon { background: #dbeafe; color: #3b82f6; }
        .stat-card.completed .stat-icon { background: #d1fae5; color: #059669; }
        .stat-card.overdue .stat-icon { background: #fee2e2; color: #dc2626; }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            font-family: 'Poppins', sans-serif;
        }

        .stat-label {
            color: var(--gray-color);
            font-size: 0.9rem;
            font-weight: 500;
        }

        .stat-trend {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.85rem;
            margin-top: 0.5rem;
        }

        .trend-up { color: #059669; }
        .trend-down { color: #dc2626; }

        /* Tasks & Calendar Section */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        @media (max-width: 1024px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Recent Tasks */
        .tasks-section {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .section-title {
            font-family: 'Poppins', sans-serif;
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

        .task-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .task-item {
            padding: 1rem;
            border-radius: 10px;
            background: var(--light-color);
            border-left: 4px solid;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .task-item:hover {
            transform: translateX(5px);
            background: #f3f4f6;
        }

        .task-item.high { border-color: var(--danger-color); }
        .task-item.medium { border-color: var(--warning-color); }
        .task-item.low { border-color: var(--primary-color); }

        .task-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.5rem;
        }

        .task-title {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.3rem;
        }

        .task-project {
            font-size: 0.85rem;
            color: var(--gray-color);
        }

        .task-priority {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
        }

        .priority-high { background: #fee2e2; color: #dc2626; }
        .priority-medium { background: #fef3c7; color: #d97706; }
        .priority-low { background: #d1fae5; color: #059669; }

        .task-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 0.8rem;
        }

        .task-deadline {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.85rem;
            color: var(--gray-color);
        }

        .deadline-near { color: var(--danger-color); font-weight: 500; }

        .task-actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn-sm {
            padding: 0.3rem 0.8rem;
            font-size: 0.8rem;
            border-radius: 6px;
        }

        /* Quick Stats */
        .quick-stats {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
        }

        .stats-chart {
            height: 200px;
            margin: 1.5rem 0;
        }

        .performance-meter {
            text-align: center;
            padding: 1rem;
            background: var(--light-color);
            border-radius: 10px;
            margin-top: 1.5rem;
        }

        .performance-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
            font-family: 'Poppins', sans-serif;
        }

        .performance-label {
            color: var(--gray-color);
            font-size: 0.9rem;
        }

        /* Quick Actions */
        .quick-actions-section {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
            margin-bottom: 2rem;
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .action-card {
            background: var(--light-color);
            border-radius: 12px;
            padding: 1.5rem 1rem;
            text-align: center;
            text-decoration: none;
            color: var(--dark-color);
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .action-card:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            transform: translateY(-3px);
        }

        .action-card:hover .action-icon {
            color: white;
        }

        .action-icon {
            font-size: 2rem;
            margin-bottom: 0.8rem;
            color: var(--primary-color);
            transition: color 0.3s ease;
        }

        .action-label {
            font-weight: 500;
            font-size: 0.9rem;
        }

        /* Upcoming Deadlines */
        .deadlines-section {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
        }

        .deadline-list {
            list-style: none;
            padding: 0;
            margin: 1.5rem 0 0;
        }

        .deadline-item {
            padding: 1rem;
            border-radius: 10px;
            background: var(--light-color);
            margin-bottom: 0.8rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all 0.3s ease;
        }

        .deadline-item:hover {
            background: #f3f4f6;
        }

        .deadline-item.critical {
            border-left: 4px solid var(--danger-color);
        }

        .deadline-item.warning {
            border-left: 4px solid var(--warning-color);
        }

        .deadline-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }

        .deadline-item.critical .deadline-icon {
            background: var(--danger-color);
        }

        .deadline-item.warning .deadline-icon {
            background: var(--warning-color);
        }

        .deadline-content h5 {
            font-weight: 600;
            margin-bottom: 0.3rem;
        }

        .deadline-content p {
            font-size: 0.85rem;
            color: var(--gray-color);
            margin: 0;
        }

        /* Footer */
        .member-footer {
            text-align: center;
            padding: 2rem;
            color: var(--gray-color);
            font-size: 0.9rem;
            border-top: 1px solid #e5e7eb;
            margin-top: 3rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .member-header {
                padding: 1rem;
            }
            
            .header-content {
                flex-direction: column;
                gap: 1rem;
            }
            
            .main-content {
                padding: 1rem;
            }
            
            .welcome-title {
                font-size: 2rem;
            }
            
            .stats-overview {
                grid-template-columns: 1fr;
            }
            
            .user-info {
                display: none;
            }
        }

        /* Custom Utilities */
        .text-gradient {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .progress-ring {
            position: relative;
            width: 100px;
            height: 100px;
            margin: 0 auto;
        }

        .progress-ring circle {
            fill: none;
            stroke-width: 8;
            stroke-linecap: round;
            transform: rotate(-90deg);
            transform-origin: 50% 50%;
        }

        .progress-ring-bg {
            stroke: #e5e7eb;
        }

        .progress-ring-fill {
            stroke: var(--primary-color);
            stroke-dasharray: 283;
            transition: stroke-dashoffset 0.35s;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="member-header">
        <div class="header-content">
            <a href="{{ route('member.dashboard') }}" class="brand">
                <div class="brand-logo">
                    <i class="bi bi-person-workspace"></i>
                </div>
                <div class="brand-text">
                    <h1>WorkSpace</h1>
                    <span>Team Member Portal</span>
                </div>
            </a>
            
            <div class="header-actions">
                <div class="notification-bell">
                    <i class="bi bi-bell" style="font-size: 1.2rem; color: var(--gray-color);"></i>
                    <div class="notification-badge">3</div>
                </div>
                
                <div class="user-profile">
                    <div class="user-avatar">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="user-info">
                        <h4>{{ Auth::user()->name }}</h4>
                        <p>Team Member</p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Welcome Section -->
        <section class="welcome-card">
            <div class="welcome-content">
                <h1 class="welcome-title">Hello, {{ Auth::user()->name }}! 👋</h1>
                <p class="welcome-subtitle">Welcome to your personal workspace. Here's what you need to focus on today.</p>
                <div class="date-display">
                    <i class="bi bi-calendar3"></i>
                    <span id="currentDateTime">Loading...</span>
                </div>
            </div>
        </section>

        <!-- Stats Overview -->
        <div class="stats-overview">
            <div class="stat-card todo">
                <div class="stat-header">
                    <div>
                        <div class="stat-number">8</div>
                        <div class="stat-label">To Do</div>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-list-check"></i>
                    </div>
                </div>
                <div class="stat-trend trend-up">
                    <i class="bi bi-arrow-up"></i>
                    <span>2 new today</span>
                </div>
            </div>

            <div class="stat-card progress">
                <div class="stat-header">
                    <div>
                        <div class="stat-number">5</div>
                        <div class="stat-label">In Progress</div>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-arrow-clockwise"></i>
                    </div>
                </div>
                <div class="stat-trend trend-up">
                    <i class="bi bi-arrow-up"></i>
                    <span>3 active</span>
                </div>
            </div>

            <div class="stat-card completed">
                <div class="stat-header">
                    <div>
                        <div class="stat-number">24</div>
                        <div class="stat-label">Completed</div>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-check-circle"></i>
                    </div>
                </div>
                <div class="stat-trend trend-up">
                    <i class="bi bi-arrow-up"></i>
                    <span>87% success rate</span>
                </div>
            </div>

            <div class="stat-card overdue">
                <div class="stat-header">
                    <div>
                        <div class="stat-number">2</div>
                        <div class="stat-label">Overdue</div>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                </div>
                <div class="stat-trend trend-down">
                    <i class="bi bi-arrow-down"></i>
                    <span>Need attention</span>
                </div>
            </div>
        </div>

        <!-- Tasks & Quick Stats Grid -->
        <div class="dashboard-grid">
            <!-- Recent Tasks -->
            <section class="tasks-section">
                <div class="section-header">
                    <h3 class="section-title">
                        <i class="bi bi-list-task"></i>
                        Recent Tasks
                    </h3>
                    <a href="{{ route('member.tasks.index') }}" class="btn btn-sm btn-outline-primary">
                        View All <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                
                <div class="task-list">
                    <div class="task-item high">
                        <div class="task-header">
                            <div>
                                <h4 class="task-title">Design Homepage Mockup</h4>
                                <p class="task-project">Website Redesign Project</p>
                            </div>
                            <span class="task-priority priority-high">High</span>
                        </div>
                        <p>Create responsive design mockups for the new homepage layout.</p>
                        <div class="task-footer">
                            <div class="task-deadline deadline-near">
                                <i class="bi bi-clock"></i>
                                Due Today, 5:00 PM
                            </div>
                            <div class="task-actions">
                                <button class="btn btn-sm btn-success">Mark Done</button>
                                <button class="btn btn-sm btn-outline-primary">Update</button>
                            </div>
                        </div>
                    </div>

                    <div class="task-item medium">
                        <div class="task-header">
                            <div>
                                <h4 class="task-title">API Integration Testing</h4>
                                <p class="task-project">Mobile App Development</p>
                            </div>
                            <span class="task-priority priority-medium">Medium</span>
                        </div>
                        <p>Test integration with payment gateway API and document results.</p>
                        <div class="task-footer">
                            <div class="task-deadline">
                                <i class="bi bi-clock"></i>
                                Due: Jan 15, 2024
                            </div>
                            <div class="task-actions">
                                <button class="btn btn-sm btn-warning">Start</button>
                                <button class="btn btn-sm btn-outline-primary">Details</button>
                            </div>
                        </div>
                    </div>

                    <div class="task-item low">
                        <div class="task-header">
                            <div>
                                <h4 class="task-title">Documentation Update</h4>
                                <p class="task-project">Internal Tools</p>
                            </div>
                            <span class="task-priority priority-low">Low</span>
                        </div>
                        <p>Update user documentation for the new reporting features.</p>
                        <div class="task-footer">
                            <div class="task-deadline">
                                <i class="bi bi-clock"></i>
                                Due: Jan 20, 2024
                            </div>
                            <div class="task-actions">
                                <button class="btn btn-sm btn-primary">View</button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Quick Stats -->
            <section class="quick-stats">
                <h3 class="section-title">
                    <i class="bi bi-graph-up"></i>
                    My Performance
                </h3>
                
                <div class="stats-chart">
                    <canvas id="performanceChart"></canvas>
                </div>
                
                <div class="performance-meter">
                    <div class="performance-value">87%</div>
                    <div class="performance-label">Completion Rate</div>
                </div>
                
                <div class="mt-4">
                    <h6><i class="bi bi-lightning-charge text-warning"></i> Quick Stats</h6>
                    <div class="d-flex justify-content-between mt-2">
                        <span>On Time Tasks:</span>
                        <strong>92%</strong>
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                        <span>Avg. Completion:</span>
                        <strong>2.3 days</strong>
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                        <span>Quality Score:</span>
                        <strong>4.8/5</strong>
                    </div>
                </div>
            </section>
        </div>

        <!-- Quick Actions -->
        <section class="quick-actions-section">
            <h3 class="section-title">
                <i class="bi bi-lightning-charge"></i>
                Quick Actions
            </h3>
            
            <div class="actions-grid">
                <a href="{{ route('member.tasks.index') }}" class="action-card">
                    <div class="action-icon">
                        <i class="bi bi-list-check"></i>
                    </div>
                    <div class="action-label">My Tasks</div>
                </a>
                
                <a href="{{ route('member.tasks.create') }}" class="action-card">
                    <div class="action-icon">
                        <i class="bi bi-plus-circle"></i>
                    </div>
                    <div class="action-label">New Task</div>
                </a>
                
                <a href="{{ route('member.calendar.view') }}" class="action-card">
                    <div class="action-icon">
                        <i class="bi bi-calendar-week"></i>
                    </div>
                    <div class="action-label">Calendar</div>
                </a>
                
                <a href="{{ route('member.tasks.links') }}" class="action-card">
                    <div class="action-icon">
                        <i class="bi bi-link-45deg"></i>
                    </div>
                    <div class="action-label">My Links</div>
                </a>
                
                <a href="{{ route('member.profile') }}" class="action-card">
                    <div class="action-icon">
                        <i class="bi bi-person-circle"></i>
                    </div>
                    <div class="action-label">Profile</div>
                </a>
                
                <form action="{{ route('logout') }}" method="POST" class="action-card" style="cursor: pointer;" 
                      onclick="event.preventDefault(); this.submit();">
                    @csrf
                    <div class="action-icon">
                        <i class="bi bi-box-arrow-right"></i>
                    </div>
                    <div class="action-label">Logout</div>
                </form>
            </div>
        </section>

        <!-- Upcoming Deadlines -->
        <section class="deadlines-section">
            <div class="section-header">
                <h3 class="section-title">
                    <i class="bi bi-calendar-exclamation"></i>
                    Upcoming Deadlines
                </h3>
            </div>
            
            <ul class="deadline-list">
                <li class="deadline-item critical">
                    <div class="deadline-icon">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <div class="deadline-content">
                        <h5>Design Homepage Mockup</h5>
                        <p>Due Today • 5:00 PM • High Priority</p>
                    </div>
                </li>
                
                <li class="deadline-item warning">
                    <div class="deadline-icon">
                        <i class="bi bi-clock"></i>
                    </div>
                    <div class="deadline-content">
                        <h5>API Documentation</h5>
                        <p>Due Tomorrow • 10:00 AM • Medium Priority</p>
                    </div>
                </li>
                
                <li class="deadline-item">
                    <div class="deadline-icon" style="background: var(--accent-color);">
                        <i class="bi bi-file-text"></i>
                    </div>
                    <div class="deadline-content">
                        <h5>Monthly Report</h5>
                        <p>Due in 3 days • 9:00 AM • Low Priority</p>
                    </div>
                </li>
            </ul>
        </section>
    </main>

    <!-- Footer -->
    <footer class="member-footer">
        <p>© 2024 WorkSpace Dashboard. Welcome, {{ Auth::user()->name }}! Keep up the great work! 💪</p>
        <p class="text-muted" style="font-size: 0.8rem;">
            <i class="bi bi-shield-check"></i> Last login: Today at 9:15 AM | 
            <i class="bi bi-cpu"></i> System Status: Operational
        </p>
    </footer>

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
                minute: '2-digit'
            };
            document.getElementById('currentDateTime').textContent = 
                now.toLocaleDateString('en-US', options);
        }
        
        updateDateTime();
        setInterval(updateDateTime, 60000);

        // Initialize Performance Chart
        const perfCtx = document.getElementById('performanceChart').getContext('2d');
        const performanceChart = new Chart(perfCtx, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Tasks Completed',
                    data: [5, 8, 6, 10, 7, 4, 3],
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.parsed.y} tasks completed`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            stepSize: 2
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Add hover effects to task items
        document.querySelectorAll('.task-item').forEach(item => {
            item.addEventListener('mouseenter', function() {
                this.style.boxShadow = '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)';
            });
            
            item.addEventListener('mouseleave', function() {
                this.style.boxShadow = 'none';
            });
            
            // Click to view task details
            item.addEventListener('click', function(e) {
                if (!e.target.closest('.btn')) {
                    // Navigate to task details
                    console.log('View task details');
                }
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

        // Simulate real-time task updates
        setInterval(() => {
            const todoCount = document.querySelector('.stat-card.todo .stat-number');
            const progressCount = document.querySelector('.stat-card.progress .stat-number');
            
            // Randomly update counts (simulating task changes)
            if (Math.random() > 0.7) {
                const currentTodo = parseInt(todoCount.textContent);
                const currentProgress = parseInt(progressCount.textContent);
                
                if (currentTodo > 0 && Math.random() > 0.5) {
                    todoCount.textContent = currentTodo - 1;
                    progressCount.textContent = currentProgress + 1;
                }
            }
            
            // Update chart with new data
            const newData = performanceChart.data.datasets[0].data.map(
                value => Math.max(0, value + Math.floor(Math.random() * 3) - 1)
            );
            performanceChart.data.datasets[0].data = newData;
            performanceChart.update('none');
            
        }, 10000);

        // Notification bell click
        document.querySelector('.notification-bell').addEventListener('click', function() {
            alert('You have 3 unread notifications');
            this.querySelector('.notification-badge').textContent = '0';
            this.querySelector('.notification-badge').style.background = '#6b7280';
        });

        // Task action buttons
        document.querySelectorAll('.btn-success').forEach(btn => {
            btn.addEventListener('click', function() {
                const taskItem = this.closest('.task-item');
                taskItem.style.opacity = '0.6';
                setTimeout(() => {
                    taskItem.remove();
                    // Update stats
                    const todoCount = document.querySelector('.stat-card.todo .stat-number');
                    const completedCount = document.querySelector('.stat-card.completed .stat-number');
                    todoCount.textContent = parseInt(todoCount.textContent) - 1;
                    completedCount.textContent = parseInt(completedCount.textContent)