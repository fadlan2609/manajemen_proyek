<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'PT Indonesia Gadai Oke') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom Styles -->
    <style>
        :root {
            --primary-color: #1a56db;
            --secondary-color: #046c4e;
            --success-color: #057a55;
            --warning-color: #f59e0b;
            --danger-color: #dc3545;
            --info-color: #0dcaf0;
            --dark-color: #1f2937;
            --light-color: #f8f9fa;
        }
        
        body {
            font-family: 'Figtree', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: var(--light-color);
            line-height: 1.6;
            color: var(--dark-color);
        }
        
        /* Navigation */
        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            color: var(--primary-color) !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .navbar-brand:hover {
            color: var(--secondary-color) !important;
        }
        
        .navbar-brand img {
            height: 40px;
            margin-right: 10px;
        }
        
        /* Buttons */
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            font-weight: 500;
            padding: 0.5rem 1.5rem;
        }
        
        .btn-primary:hover, .btn-primary:focus {
            background-color: #1546b8;
            border-color: #1546b8;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(26, 86, 219, 0.25);
        }
        
        .btn-success {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            font-weight: 500;
        }
        
        .btn-success:hover, .btn-success:focus {
            background-color: #035c44;
            border-color: #035c44;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(4, 108, 78, 0.25);
        }
        
        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        /* Cards */
        .card {
            border-radius: 12px;
            border: 1px solid rgba(0,0,0,0.08);
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .card:hover {
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        
        .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0,0,0,0.1);
            font-weight: 600;
            padding: 1.25rem 1.5rem;
            font-size: 1.1rem;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        /* Badges */
        .badge {
            font-weight: 500;
            padding: 0.35em 0.65em;
            font-size: 0.85em;
            border-radius: 6px;
        }
        
        .badge.bg-primary {
            background-color: var(--primary-color) !important;
        }
        
        .badge.bg-success {
            background-color: var(--secondary-color) !important;
        }
        
        .badge.bg-warning {
            background-color: var(--warning-color) !important;
            color: #000;
        }
        
        .badge.bg-danger {
            background-color: var(--danger-color) !important;
        }
        
        /* Forms */
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #d1d5db;
            padding: 0.625rem 0.875rem;
            font-size: 0.95rem;
            transition: all 0.2s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(26, 86, 219, 0.15);
            outline: none;
        }
        
        .form-label {
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
        }
        
        /* Progress bars */
        .progress {
            height: 10px;
            border-radius: 5px;
            background-color: #e5e7eb;
            overflow: hidden;
        }
        
        .progress-bar {
            border-radius: 5px;
            transition: width 0.6s ease;
        }
        
        /* Tables */
        .table {
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .table th {
            background-color: #f9fafb;
            font-weight: 600;
            color: #374151;
            border-top: none;
            padding: 0.75rem 1rem;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .table td {
            padding: 0.75rem 1rem;
            vertical-align: middle;
            border-top: 1px solid #f3f4f6;
        }
        
        .table tbody tr:hover {
            background-color: #f8fafc;
        }
        
        /* Alerts */
        .alert {
            border-radius: 8px;
            border: none;
            padding: 1rem 1.25rem;
        }
        
        .alert-success {
            background-color: #d1fae5;
            color: var(--secondary-color);
        }
        
        .alert-danger {
            background-color: #fee2e2;
            color: var(--danger-color);
        }
        
        .alert-warning {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .alert-info {
            background-color: #dbeafe;
            color: var(--primary-color);
        }
        
        /* Modals */
        .modal-content {
            border-radius: 12px;
            border: none;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
        }
        
        .modal-header {
            border-bottom: 1px solid #e5e7eb;
            background-color: #f9fafb;
            border-radius: 12px 12px 0 0;
            padding: 1.25rem 1.5rem;
        }
        
        .modal-footer {
            border-top: 1px solid #e5e7eb;
            padding: 1.25rem 1.5rem;
        }
        
        /* Avatar placeholder */
        .avatar-placeholder {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            font-weight: bold;
            font-size: 16px;
        }
        
        /* Status indicators */
        .status-indicator {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 5px;
        }
        
        .status-active { background-color: #10b981; }
        .status-inactive { background-color: #ef4444; }
        .status-pending { background-color: #f59e0b; }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #a1a1a1;
        }
        
        /* Footer */
        footer {
            background-color: white;
            border-top: 1px solid #e5e7eb;
            padding: 1.5rem 0;
            margin-top: 3rem;
        }
        
        /* Login page specific */
        .login-page {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 2rem;
        }
        
        .login-card {
            border-radius: 16px;
            border: none;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .container-fluid {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }
            
            .card-body {
                padding: 1rem;
            }
            
            .btn {
                padding: 0.375rem 0.75rem;
                font-size: 0.875rem;
            }
            
            .navbar-brand {
                font-size: 1.25rem;
            }
        }
        
        /* Animation for notifications */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .alert, .toast {
            animation: fadeIn 0.3s ease;
        }
        
        /* Task status colors */
        .status-todo { color: #6b7280; }
        .status-in_progress { color: var(--primary-color); }
        .status-done { color: var(--secondary-color); }
        
        /* Priority colors */
        .priority-high { color: var(--danger-color); }
        .priority-medium { color: var(--warning-color); }
        .priority-low { color: var(--success-color); }
    </style>
    
    <!-- Additional page-specific styles -->
    @stack('styles')
</head>
<body class="font-sans antialiased">
    <div id="app">
        <!-- Navigation -->
        @if(!request()->is('login') && !request()->is('register') && !request()->is('password/*'))
            @include('layouts.navigation')
        @endif

        <!-- Page Content -->
        <main>
            @yield('content')
        </main>
        
        <!-- Footer (optional) -->
        @if(!request()->is('login') && !request()->is('register') && !request()->is('password/*'))
        <footer class="mt-auto">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-0 text-muted">
                            &copy; {{ date('Y') }} {{ config('app.name', 'PT Indonesia Gadai Oke') }}. All rights reserved.
                        </p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <p class="mb-0 text-muted">
                            Task Management System v1.0
                        </p>
                    </div>
                </div>
            </div>
        </footer>
        @endif
    </div>

    <!-- Bootstrap JS with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Additional Scripts -->
    @stack('scripts')
    
    <script>
        // Initialize Bootstrap components
        document.addEventListener('DOMContentLoaded', function() {
            // Tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Popovers
            var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl);
            });
            
            // Auto-dismiss alerts after 5 seconds
            setTimeout(function() {
                var alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
                alerts.forEach(function(alert) {
                    var bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
            
            // Confirm before submitting forms
            var confirmForms = document.querySelectorAll('form[data-confirm]');
            confirmForms.forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    var message = this.getAttribute('data-confirm') || 'Are you sure?';
                    if (!confirm(message)) {
                        e.preventDefault();
                        return false;
                    }
                });
            });
            
            // Auto-focus first input in modals
            document.addEventListener('shown.bs.modal', function(event) {
                var modal = event.target;
                var input = modal.querySelector('input, textarea, select');
                if (input && !input.disabled) {
                    input.focus();
                }
            });
        });
        
        // Helper functions
        window.showToast = function(message, type = 'info') {
            // Create toast container if not exists
            var toastContainer = document.getElementById('toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.id = 'toast-container';
                toastContainer.className = 'position-fixed top-0 end-0 p-3';
                toastContainer.style.zIndex = '1060';
                document.body.appendChild(toastContainer);
            }
            
            // Create toast
            var toastId = 'toast-' + Date.now();
            var toastHtml = `
                <div id="${toastId}" class="toast align-items-center text-bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="bi ${type === 'success' ? 'bi-check-circle' : type === 'error' ? 'bi-exclamation-triangle' : 'bi-info-circle'} me-2"></i>
                            ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            `;
            
            toastContainer.insertAdjacentHTML('beforeend', toastHtml);
            
            // Show toast
            var toastEl = document.getElementById(toastId);
            var toast = new bootstrap.Toast(toastEl);
            toast.show();
            
            // Remove toast after hidden
            toastEl.addEventListener('hidden.bs.toast', function () {
                this.remove();
            });
        };
        
        // Copy to clipboard helper
        window.copyToClipboard = function(text) {
            navigator.clipboard.writeText(text).then(function() {
                showToast('Copied to clipboard!', 'success');
            }).catch(function() {
                showToast('Failed to copy', 'error');
            });
        };
        
        // Form validation helper
        window.validateForm = function(formId) {
            var form = document.getElementById(formId);
            if (!form) return true;
            
            var isValid = true;
            var inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
            
            inputs.forEach(function(input) {
                if (!input.value.trim()) {
                    isValid = false;
                    input.classList.add('is-invalid');
                    
                    // Add error message if not exists
                    if (!input.nextElementSibling || !input.nextElementSibling.classList.contains('invalid-feedback')) {
                        var errorDiv = document.createElement('div');
                        errorDiv.className = 'invalid-feedback';
                        errorDiv.textContent = 'This field is required';
                        input.parentNode.appendChild(errorDiv);
                    }
                } else {
                    input.classList.remove('is-invalid');
                }
            });
            
            return isValid;
        };
    </script>
</body>
</html>