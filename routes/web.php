<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProjectManager\ProjectController;
use App\Http\Controllers\ProjectManager\TaskController as ProjectManagerTaskController;
use App\Http\Middleware\CheckRole; // IMPORT MIDDLEWARE
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\TaskLinkHistory;
use App\Models\User; // TAMBAHKAN INI
use Illuminate\Support\Facades\Hash; // TAMBAHKAN INI

// Authentication Routes
Auth::routes();

// ==================== PUBLIC ROUTES ====================
Route::get('/', function () {
    // Jika user sudah login, redirect ke dashboard sesuai role
    if (Auth::check()) {
        $user = Auth::user();
        
        switch ($user->role ?? 'member') {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'project_manager':
                return redirect()->route('project-manager.dashboard');
            case 'member':
            default:
                return redirect()->route('member.dashboard');
        }
    }
    
    // Jika belum login, redirect ke login
    return redirect()->route('login');
})->name('home');

// ==================== AUTHENTICATED ROUTES ====================
Route::middleware(['auth'])->group(function () {

    // ========== REDIRECT BERDASARKAN ROLE ==========
    Route::get('/dashboard', function () {
        $user = Auth::user();

        switch ($user->role ?? 'member') { // tambahkan default value
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'project_manager':
                return redirect()->route('project-manager.dashboard');
            case 'member':
            default:
                return redirect()->route('member.dashboard');
        }
    })->name('dashboard');

    // ========== ADMIN ROUTES ==========
    Route::middleware([CheckRole::class . ':admin'])->prefix('admin')->name('admin.')->group(function () {
        // Dashboard Admin
        Route::get('/dashboard', function () {
            $user = Auth::user();
            return view('admin.dashboard', ['user' => $user]);
        })->name('dashboard');

        // ========== PROFILE ROUTES FOR ADMIN ==========
        Route::get('profile', function () {
            /** @var User $user */
            $user = Auth::user();
            $user->load('tasks');
            
            // Get recent activities
            $recentActivities = collect([]);
            
            // Get task statistics
            $userId = $user->id;
            $stats = [
                'total' => Task::where('assigned_to', $userId)->count(),
                'todo' => Task::where('assigned_to', $userId)->where('status', 'todo')->count(),
                'in_progress' => Task::where('assigned_to', $userId)->where('status', 'in_progress')->count(),
                'done' => Task::where('assigned_to', $userId)->where('status', 'done')->count(),
                'overdue' => Task::where('assigned_to', $userId)
                    ->where('deadline', '<', now())
                    ->where('status', '!=', 'done')
                    ->count(),
                'due_today' => Task::where('assigned_to', $userId)
                    ->whereDate('deadline', today())
                    ->where('status', '!=', 'done')
                    ->count(),
            ];
            
            return view('admin.profile', compact('user', 'recentActivities', 'stats'));
        })->name('profile');

        // Profile update routes
        Route::put('profile/update', function (Request $request) {
            /** @var User $user */
            $user = Auth::user();
            
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'phone' => 'nullable|string|max:20',
                'position' => 'nullable|string|max:100',
                'department' => 'nullable|string|max:100',
            ]);
            
            $user->update($validated);
            
            return redirect()->route('admin.profile')->with('success', 'Profile updated successfully!');
        })->name('profile.update');

        Route::put('profile/password', function (Request $request) {
            /** @var User $user */
            $user = Auth::user();
            
            $validated = $request->validate([
                'current_password' => ['required', 'current_password'],
                'password' => ['required', 'confirmed', 'min:8'],
            ]);
            
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);
            
            return redirect()->route('admin.profile')->with('success', 'Password updated successfully!');
        })->name('profile.password');

        Route::delete('profile/delete', function () {
            /** @var User $user */
            $user = Auth::user();
            
            // You might want to soft delete instead
            $user->delete();
            
            Auth::logout();
            
            return redirect()->route('login')->with('success', 'Your account has been deleted.');
        })->name('profile.delete');

        // User Management
        Route::resource('users', UserController::class);
    });

    // ========== PROJECT MANAGER ROUTES ==========
    Route::middleware([CheckRole::class . ':project_manager'])->prefix('project-manager')->name('project-manager.')->group(function () {
        // Dashboard Project Manager
        Route::get('/dashboard', function () {
            $user = Auth::user();
            return view('project-manager.dashboard', ['user' => $user]);
        })->name('dashboard');

        // ========== PROFILE ROUTES FOR PROJECT MANAGER ==========
        Route::get('profile', function () {
            /** @var User $user */
            $user = Auth::user();
            $user->load('tasks', 'managedProjects');
            
            // Get recent activities
            $recentActivities = collect([]);
            
            // Get task statistics
            $userId = $user->id;
            $stats = [
                'total_tasks' => Task::where('assigned_to', $userId)->count(),
                'managed_projects' => $user->managedProjects()->count(),
                'todo' => Task::where('assigned_to', $userId)->where('status', 'todo')->count(),
                'in_progress' => Task::where('assigned_to', $userId)->where('status', 'in_progress')->count(),
                'done' => Task::where('assigned_to', $userId)->where('status', 'done')->count(),
                'overdue' => Task::where('assigned_to', $userId)
                    ->where('deadline', '<', now())
                    ->where('status', '!=', 'done')
                    ->count(),
            ];
            
            return view('project-manager.profile', compact('user', 'recentActivities', 'stats'));
        })->name('profile');

        // Profile update routes
        Route::put('profile/update', function (Request $request) {
            /** @var User $user */
            $user = Auth::user();
            
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'phone' => 'nullable|string|max:20',
                'position' => 'nullable|string|max:100',
                'department' => 'nullable|string|max:100',
            ]);
            
            $user->update($validated);
            
            return redirect()->route('project-manager.profile')->with('success', 'Profile updated successfully!');
        })->name('profile.update');

        Route::put('profile/password', function (Request $request) {
            /** @var User $user */
            $user = Auth::user();
            
            $validated = $request->validate([
                'current_password' => ['required', 'current_password'],
                'password' => ['required', 'confirmed', 'min:8'],
            ]);
            
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);
            
            return redirect()->route('project-manager.profile')->with('success', 'Password updated successfully!');
        })->name('profile.password');

        Route::delete('profile/delete', function () {
            /** @var User $user */
            $user = Auth::user();
            
            // You might want to soft delete instead
            $user->delete();
            
            Auth::logout();
            
            return redirect()->route('login')->with('success', 'Your account has been deleted.');
        })->name('profile.delete');

        // TRASHED ROUTES
        Route::get('projects/trashed', [ProjectController::class, 'trashed'])->name('projects.trashed');
        Route::put('projects/{id}/restore', [ProjectController::class, 'restore'])->name('projects.restore');
        Route::delete('projects/{id}/force-delete', [ProjectController::class, 'forceDelete'])->name('projects.forceDelete');

        // PROJECT CRUD ROUTES
        Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
        Route::get('projects/create', [ProjectController::class, 'create'])->name('projects.create');
        Route::post('projects', [ProjectController::class, 'store'])->name('projects.store');

        Route::get('projects/{project}', [ProjectController::class, 'show'])
            ->where('project', '[0-9]+')
            ->name('projects.show');

        Route::get('projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
        Route::put('projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
        Route::delete('projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

        // ADDITIONAL PROJECT FEATURES
        Route::post('projects/{project}/update-progress', [ProjectController::class, 'updateProgress'])->name('projects.updateProgress');
        Route::post('projects/{project}/add-member', [ProjectController::class, 'addMember'])->name('projects.addMember');
        Route::delete('projects/{project}/remove-member/{user}', [ProjectController::class, 'removeMember'])->name('projects.removeMember');
        Route::post('projects/{project}/clone', [ProjectController::class, 'cloneProject'])->name('projects.clone');

        // TASK ROUTES
        Route::get('projects/{project}/tasks/create', [ProjectManagerTaskController::class, 'create'])->name('projects.tasks.create');
        Route::post('projects/{project}/tasks', [ProjectManagerTaskController::class, 'store'])->name('projects.tasks.store');
        Route::get('projects/{project}/tasks/{task}/edit', [ProjectManagerTaskController::class, 'edit'])->name('projects.tasks.edit');
        Route::put('projects/{project}/tasks/{task}', [ProjectManagerTaskController::class, 'update'])->name('projects.tasks.update');
        Route::delete('projects/{project}/tasks/{task}', [ProjectManagerTaskController::class, 'destroy'])->name('projects.tasks.destroy');
    });

    // ==================== MEMBER ROUTES ====================
    Route::middleware([CheckRole::class . ':member'])->prefix('member')->name('member.')->group(function () {

        // Dashboard Member
        Route::get('/dashboard', function () {
            $user = Auth::user();
            return view('member.dashboard', ['user' => $user]);
        })->name('dashboard');

        // ========== TASKS ROUTES ==========
        // Tasks - Index
        Route::get('tasks', function () {
            $userId = Auth::id();
            $tasks = Task::where('assigned_to', $userId)
                ->with(['project', 'assignee'])
                ->orderBy('deadline', 'asc')
                ->paginate(15);

            return view('member.tasks.index', compact('tasks'));
        })->name('tasks.index');

        // Tasks - Show
        Route::get('tasks/{task}', function (Task $task) {
            $userId = Auth::id();

            if ($task->assigned_to !== $userId) {
                abort(403, 'You are not authorized to view this task.');
            }

            $task->load(['project', 'assignee', 'comments.user', 'linkHistories.user']);

            // Get similar tasks
            $similarTasks = Task::where('project_id', $task->project_id)
                ->where('id', '!=', $task->id)
                ->where('assigned_to', $userId)
                ->orderBy('deadline', 'asc')
                ->limit(5)
                ->get();

            return view('member.tasks.show', compact('task', 'similarTasks'));
        })->name('tasks.show');

        // Tasks - Edit
        Route::get('tasks/{task}/edit', function (Task $task) {
            $userId = Auth::id();

            if ($task->assigned_to !== $userId) {
                abort(403, 'You are not authorized to edit this task.');
            }

            return view('member.tasks.edit', compact('task'));
        })->name('tasks.edit');

        // Tasks - Update
        Route::put('tasks/{task}', function (Request $request, Task $task) {
            $userId = Auth::id();

            if ($task->assigned_to !== $userId) {
                abort(403, 'You are not authorized to update this task.');
            }

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'priority' => 'required|in:low,medium,high',
                'deadline' => 'required|date',
                'status' => 'required|in:todo,in_progress,done',
                'progress' => 'nullable|integer|min:0|max:100',
            ]);

            $task->update($validated);

            return redirect()->route('member.tasks.show', $task)
                ->with('success', 'Task updated successfully.');
        })->name('tasks.update');

        // Tasks - Create
        Route::get('tasks/create', function () {
            return view('member.tasks.create');
        })->name('tasks.create');

        // Tasks - Store
        Route::post('tasks', function (Request $request) {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'priority' => 'required|in:low,medium,high',
                'deadline' => 'required|date',
            ]);

            $validated['assigned_to'] = Auth::id();
            $validated['status'] = 'todo';
            $validated['progress'] = 0;

            Task::create($validated);

            return redirect()->route('member.tasks.index')
                ->with('success', 'Task created successfully.');
        })->name('tasks.store');

        // Tasks - Destroy
        Route::delete('tasks/{task}', function (Task $task) {
            $userId = Auth::id();

            if ($task->assigned_to !== $userId) {
                abort(403, 'You are not authorized to delete this task.');
            }

            $task->delete();

            return redirect()->route('member.tasks.index')
                ->with('success', 'Task deleted successfully.');
        })->name('tasks.destroy');

        // ========== TASK ACTIONS ==========
        // Complete Task - POST (DIPERBAIKI: dengan completed_at)
        Route::post('tasks/{task}/complete', function (Request $request, Task $task) {
            $userId = Auth::id();

            if ($task->assigned_to !== $userId) {
                abort(403, 'You are not authorized to update this task.');
            }

            // Mark as completed DENGAN completed_at
            $task->update([
                'status' => 'done',
                'progress' => 100,
                'completed_at' => now(), // MENYERTAKAN completed_at
            ]);

            return redirect()->back()
                ->with('success', 'Task marked as completed.');
        })->name('tasks.complete');
        
        // Complete Task - GET (DIPERBAIKI: dengan completed_at)
        Route::get('tasks/{task}/mark-complete', function(Task $task) {
            $userId = Auth::id();
            
            if ($task->assigned_to !== $userId) {
                abort(403, 'You are not authorized to update this task.');
            }

            $task->update([
                'status' => 'done',
                'progress' => 100,
                'completed_at' => now(), // MENYERTAKAN completed_at
            ]);

            return redirect()->back()
                ->with('success', 'Task marked as completed via GET.');
        })->name('tasks.complete.get');

        // Status Update (DIPERBAIKI: dengan completed_at)
        Route::put('tasks/{task}/status', function (Request $request, Task $task) {
            $userId = Auth::id();

            if ($task->assigned_to !== $userId) {
                abort(403, 'You are not authorized to update this task.');
            }

            $validated = $request->validate([
                'status' => 'required|in:todo,in_progress,done',
            ]);

            $updateData = ['status' => $validated['status']];

            // If marking as done, set progress to 100%
            if ($validated['status'] === 'done') {
                $updateData['progress'] = 100;
                $updateData['completed_at'] = now(); // MENYERTAKAN completed_at
            } elseif ($validated['status'] === 'in_progress' && $task->progress == 0) {
                // If starting work, set progress to 10%
                $updateData['progress'] = 10;
                $updateData['completed_at'] = null; // Reset completed_at jika tidak done
            } else {
                $updateData['completed_at'] = null; // Reset completed_at untuk status lainnya
            }

            $task->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Task status updated successfully',
                'task' => $task->fresh(),
            ]);
        })->name('tasks.update-status');

        // Progress Update (DIPERBAIKI: dengan completed_at)
        Route::post('tasks/{task}/progress', function (Request $request, Task $task) {
            $userId = Auth::id();

            if ($task->assigned_to !== $userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $request->validate([
                'progress' => 'required|integer|min:0|max:100'
            ]);

            $updateData = ['progress' => $request->progress];

            // Auto-update status based on progress
            if ($request->progress >= 100) {
                $updateData['status'] = 'done';
                $updateData['completed_at'] = now(); // MENYERTAKAN completed_at
            } elseif ($request->progress > 0 && $task->status === 'todo') {
                $updateData['status'] = 'in_progress';
                $updateData['completed_at'] = null; // Reset completed_at
            } elseif ($request->progress == 0) {
                $updateData['status'] = 'todo';
                $updateData['completed_at'] = null; // Reset completed_at
            }

            $task->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Progress updated successfully',
                'old_progress' => $task->progress,
                'new_progress' => $request->progress,
                'status' => $task->fresh()->status,
            ]);
        })->name('tasks.update-progress');

        // Comments
        Route::post('tasks/{task}/comments', function (Request $request, Task $task) {
            $userId = Auth::id();

            if ($task->assigned_to !== $userId) {
                abort(403, 'You are not authorized to comment on this task.');
            }

            $validated = $request->validate([
                'content' => 'required|string|min:1|max:1000'
            ]);

            $comment = $task->comments()->create([
                'user_id' => $userId,
                'content' => $validated['content'],
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Comment added',
                    'comment' => $comment->load('user')
                ]);
            }

            return redirect()->back()
                ->with('success', 'Comment added.');
        })->name('tasks.comments.store');

        // ========== LINK MANAGEMENT ==========
        // Submit Link (DIPERBAIKI: tambahkan route update-link)
        Route::post('tasks/{task}/submit-link', function (Request $request, Task $task) {
            $userId = Auth::id();

            if ($task->assigned_to !== $userId) {
                abort(403, 'You are not authorized to submit link for this task.');
            }

            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'link' => 'required|url|max:500',
                'notes' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $validated = $validator->validated();

            // Simpan data lama untuk history
            $oldLink = $task->link;

            // Update task
            $updateData = ['link' => $validated['link']];

            // Update status jika masih todo
            if ($task->status === 'todo') {
                $updateData['status'] = 'in_progress';
            }

            // Tambah progress otomatis jika kurang dari 100
            if ($task->progress < 100) {
                $updateData['progress'] = min(100, $task->progress + 25);
            }

            $task->update($updateData);

            // Buat history link
            TaskLinkHistory::create([
                'task_id' => $task->id,
                'user_id' => $userId,
                'old_link' => $oldLink,
                'new_link' => $validated['link'],
                'notes' => $validated['notes'] ?? null,
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Link submitted successfully',
                    'task' => $task->fresh(),
                    'new_link' => $task->link,
                    'new_status' => $task->status,
                    'new_progress' => $task->progress,
                ]);
            }

            return redirect()->back()
                ->with('success', 'Link submitted successfully!');
        })->name('tasks.submit-link');
        
        // Update Link Route
        Route::put('tasks/{task}/update-link', function(Request $request, Task $task) {
            $userId = Auth::id();

            if ($task->assigned_to !== $userId) {
                abort(403, 'You are not authorized to update link for this task.');
            }

            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'link' => 'required|url|max:500',
                'notes' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $validated = $validator->validated();
            $oldLink = $task->link;

            // Update task link
            $task->update(['link' => $validated['link']]);

            // Buat history link
            TaskLinkHistory::create([
                'task_id' => $task->id,
                'user_id' => $userId,
                'old_link' => $oldLink,
                'new_link' => $validated['link'],
                'notes' => $validated['notes'] ?? null,
            ]);

            return redirect()->back()
                ->with('success', 'Link updated successfully!');
        })->name('tasks.update-link');
        
        // Remove Link
        Route::delete('tasks/{task}/remove-link', function(Task $task) {
            $userId = Auth::id();

            if ($task->assigned_to !== $userId) {
                abort(403, 'You are not authorized to remove link for this task.');
            }

            $oldLink = $task->link;
            
            $task->update(['link' => null]);

            // Buat history link
            TaskLinkHistory::create([
                'task_id' => $task->id,
                'user_id' => $userId,
                'old_link' => $oldLink,
                'new_link' => null,
                'notes' => 'Link removed by user',
            ]);

            return redirect()->back()
                ->with('success', 'Link removed successfully!');
        })->name('tasks.remove-link');
        
        // ========== TASK LINKS PAGE ==========
        Route::get('tasks/links', function() {
            $userId = Auth::id();
            
            $tasks = Task::where('assigned_to', $userId)
                ->whereNotNull('link')
                ->with(['project', 'linkHistories' => function($query) {
                    $query->latest()->limit(5);
                }])
                ->orderBy('updated_at', 'desc')
                ->paginate(10);
            
            return view('member.tasks.links', compact('tasks'));
        })->name('tasks.links');

        // ========== API ENDPOINTS ==========
        Route::get('tasks/calendar', function (Request $request) {
            $userId = Auth::id();

            $start = $request->get('start', date('Y-m-01'));
            $end = $request->get('end', date('Y-m-t'));

            $tasks = Task::where('assigned_to', $userId)
                ->whereNotNull('deadline')
                ->whereBetween('deadline', [$start, $end])
                ->with('project')
                ->get();

            $events = $tasks->map(function ($task) {
                $eventColor = match ($task->priority) {
                    'high' => '#dc3545',
                    'medium' => '#ffc107',
                    'low' => '#28a745',
                    default => '#6c757d',
                };

                // Adjust color based on status
                $isOverdue = $task->deadline && $task->deadline < now() && $task->status !== 'done';
                if ($isOverdue) {
                    $eventColor = '#dc3545';
                } elseif ($task->status === 'done') {
                    $eventColor = '#28a745';
                }

                return [
                    'id' => $task->id,
                    'title' => $task->title . ($task->progress ? " ({$task->progress}%)" : ''),
                    'start' => $task->deadline->format('Y-m-d'),
                    'end' => $task->deadline->format('Y-m-d'),
                    'color' => $eventColor,
                    'textColor' => '#ffffff',
                    'url' => route('member.tasks.show', $task),
                    'extendedProps' => [
                        'priority' => $task->priority,
                        'status' => $task->status,
                        'progress' => $task->progress,
                        'project' => $task->project->name ?? 'No Project',
                        'has_link' => !empty($task->link),
                        'description' => substr($task->description ?? '', 0, 100),
                    ],
                ];
            });

            return response()->json($events);
        })->name('tasks.calendar');

        Route::get('tasks/stats', function () {
            $userId = Auth::id();

            $stats = [
                'total' => Task::where('assigned_to', $userId)->count(),
                'todo' => Task::where('assigned_to', $userId)->where('status', 'todo')->count(),
                'in_progress' => Task::where('assigned_to', $userId)->where('status', 'in_progress')->count(),
                'done' => Task::where('assigned_to', $userId)->where('status', 'done')->count(),
                'overdue' => Task::where('assigned_to', $userId)
                    ->where('deadline', '<', now())
                    ->where('status', '!=', 'done')
                    ->count(),
                'due_today' => Task::where('assigned_to', $userId)
                    ->whereDate('deadline', today())
                    ->where('status', '!=', 'done')
                    ->count(),
            ];

            return response()->json($stats);
        })->name('tasks.stats');

        // ========== CALENDAR VIEW ==========
        Route::get('calendar', function () {
            return view('member.tasks.calendar');
        })->name('calendar.view');

        // ========== PROFILE ==========
        Route::get('profile', function () {
            /** @var User $user */
            $user = Auth::user();
            $user->load('tasks');
            
            // Get recent activities
            $recentActivities = collect([]);
            
            // Get task statistics
            $userId = $user->id;
            $stats = [
                'total' => Task::where('assigned_to', $userId)->count(),
                'todo' => Task::where('assigned_to', $userId)->where('status', 'todo')->count(),
                'in_progress' => Task::where('assigned_to', $userId)->where('status', 'in_progress')->count(),
                'done' => Task::where('assigned_to', $userId)->where('status', 'done')->count(),
                'overdue' => Task::where('assigned_to', $userId)
                    ->where('deadline', '<', now())
                    ->where('status', '!=', 'done')
                    ->count(),
                'due_today' => Task::where('assigned_to', $userId)
                    ->whereDate('deadline', today())
                    ->where('status', '!=', 'done')
                    ->count(),
            ];
            
            return view('member.profile', compact('user', 'recentActivities', 'stats'));
        })->name('profile');

        // Profile update routes
        Route::put('profile/update', function (Request $request) {
            /** @var User $user */
            $user = Auth::user();
            
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'phone' => 'nullable|string|max:20',
                'position' => 'nullable|string|max:100',
                'department' => 'nullable|string|max:100',
            ]);
            
            $user->update($validated);
            
            return redirect()->route('member.profile')->with('success', 'Profile updated successfully!');
        })->name('profile.update');

        Route::put('profile/password', function (Request $request) {
            /** @var User $user */
            $user = Auth::user();
            
            $validated = $request->validate([
                'current_password' => ['required', 'current_password'],
                'password' => ['required', 'confirmed', 'min:8'],
            ]);
            
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);
            
            return redirect()->route('member.profile')->with('success', 'Password updated successfully!');
        })->name('profile.password');

        Route::delete('profile/delete', function () {
            /** @var User $user */
            $user = Auth::user();
            
            // You might want to soft delete instead
            $user->delete();
            
            Auth::logout();
            
            return redirect()->route('login')->with('success', 'Your account has been deleted.');
        })->name('profile.delete');

    }); // Penutupan grup route untuk member

    // ========== ROUTE UNTUK MULTIPLE ROLES (CONTOH) ==========
    Route::middleware([CheckRole::class . ':admin,project_manager'])->prefix('reports')->name('reports.')->group(function () {
        Route::get('/', function () {
            return view('reports.index');
        })->name('index');
    });

}); // Penutupan grup route untuk auth middleware

// Fallback route
Route::fallback(function () {
    return redirect()->route('dashboard');
});