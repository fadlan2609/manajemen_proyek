<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Constructor untuk authorization
     */
    public function __construct()
    {
        // Middleware untuk memastikan hanya admin yang bisa akses
        $this->middleware(function ($request, $next) {
            if (!Auth::check()) {
                return redirect()->route('login');
            }
            
            /** @var \App\Models\User $user */
            $user = Auth::user();
            
            // Cek jika user adalah admin
            if (!$user->isAdmin()) {
                abort(403, 'Unauthorized action. You must be an Administrator to access this page.');
            }
            
            return $next($request);
        });
    }

    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        // Filter berdasarkan role jika ada
        $query = User::query();
        
        if ($request->has('role') && $request->role != '') {
            $query->where('role', $request->role);
        }
        
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Sort options
        $sort = $request->get('sort', 'created_at');
        $order = $request->get('order', 'desc');
        
        $query->orderBy($sort, $order);
        
        $users = $query->paginate(15);
        
        // Get user statistics
        $userStats = [
            'total' => User::count(),
            'admins' => User::where('role', 'admin')->count(),
            'project_managers' => User::where('role', 'project_manager')->count(),
            'members' => User::where('role', 'member')->count(),
            'active_today' => User::whereDate('last_login_at', today())->count(),
        ];
        
        return view('admin.users.index', compact('users', 'userStats'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|min:2',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|in:admin,project_manager,member',
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::beginTransaction();
        
        try {
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'phone' => $request->phone,
                'position' => $request->position,
                'department' => $request->department,
                'email_verified_at' => now(), // Auto verify for admin-created users
            ];

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                $path = $request->file('avatar')->store('avatars', 'public');
                $userData['avatar'] = $path;
            }

            $user = User::create($userData);

            DB::commit();

            return redirect()->route('admin.users.show', $user)
                             ->with('success', 'User created successfully!');
                             
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Failed to create user: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        // Load user relationships
        $user->load([
            'managedProjects' => function($query) {
                $query->withCount('tasks')->latest()->take(5);
            },
            'assignedTasks' => function($query) {
                $query->with('project')->latest()->take(10);
            },
            'projects' => function($query) {
                $query->withCount('tasks')->latest()->take(5);
            }
        ]);

        // Get user statistics
        $userStats = [
            'managed_projects' => $user->managedProjects()->count(),
            'assigned_tasks' => $user->assignedTasks()->count(),
            'completed_tasks' => $user->assignedTasks()->where('status', 'done')->count(),
            'active_projects' => $user->projects()->where('status', 'active')->count(),
            'total_projects' => $user->projects()->count(),
            'overdue_tasks' => $user->assignedTasks()->overdue()->count(),
        ];

        // Get recent activities
        $recentActivities = $this->getUserActivities($user);

        return view('admin.users.show', compact('user', 'userStats', 'recentActivities'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255|min:2',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,project_manager,member',
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'nullable|in:active,inactive,suspended',
        ]);

        DB::beginTransaction();
        
        try {
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'phone' => $request->phone,
                'position' => $request->position,
                'department' => $request->department,
                'status' => $request->status ?? 'active',
            ];

            // Handle password update if provided
            if ($request->filled('password')) {
                $request->validate([
                    'password' => ['confirmed', Rules\Password::defaults()],
                ]);
                $userData['password'] = Hash::make($request->password);
            }

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                // Delete old avatar if exists
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }
                
                $path = $request->file('avatar')->store('avatars', 'public');
                $userData['avatar'] = $path;
            }

            // Handle avatar removal
            if ($request->has('remove_avatar')) {
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $userData['avatar'] = null;
            }

            $user->update($userData);

            DB::commit();

            return redirect()->route('admin.users.show', $user)
                             ->with('success', 'User updated successfully!');
                             
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Failed to update user: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')
                             ->with('error', 'You cannot delete your own account.');
        }

        // Check if user has managed projects
        if ($user->managedProjects()->count() > 0) {
            return redirect()->route('admin.users.index')
                             ->with('error', 'Cannot delete user who is managing projects. Please reassign projects first.');
        }

        DB::beginTransaction();
        
        try {
            // Store user info for message
            $userName = $user->name;
            $userEmail = $user->email;

            // Delete avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Delete user
            $user->delete();

            DB::commit();

            return redirect()->route('admin.users.index')
                             ->with('success', "User '{$userName} ({$userEmail})' has been deleted successfully!");
                             
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.users.index')
                             ->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }

    /**
     * Show trashed users.
     */
    public function trashed(Request $request)
    {
        $query = User::onlyTrashed();
        
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        $users = $query->latest()->paginate(15);
        
        return view('admin.users.trashed', compact('users'));
    }

    /**
     * Restore a trashed user.
     */
    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        
        // Check if email is still unique
        if (User::where('email', $user->email)->where('id', '!=', $user->id)->exists()) {
            return redirect()->route('admin.users.trashed')
                             ->with('error', 'Cannot restore user. Email is already taken by another user.');
        }
        
        $user->restore();

        return redirect()->route('admin.users.trashed')
                         ->with('success', 'User restored successfully!');
    }

    /**
     * Permanently delete a user.
     */
    public function forceDelete($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        
        // Prevent deleting yourself
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.trashed')
                             ->with('error', 'You cannot permanently delete your own account.');
        }

        DB::beginTransaction();
        
        try {
            $userName = $user->name;
            $userEmail = $user->email;
            
            // Delete avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            // Permanently delete
            $user->forceDelete();

            DB::commit();

            return redirect()->route('admin.users.trashed')
                             ->with('success', "User '{$userName} ({$userEmail})' has been permanently deleted!");
                             
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.users.trashed')
                             ->with('error', 'Failed to permanently delete user: ' . $e->getMessage());
        }
    }

    /**
     * Impersonate a user (login as that user).
     */
    public function impersonate(User $user)
    {
        // Only allow admin to impersonate
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();
        if (!$authUser->isAdmin()) {
            abort(403, 'Only administrators can impersonate users.');
        }

        // Store original user ID in session
        session()->put('impersonator_id', Auth::id());
        
        // Log in as the target user
        Auth::login($user);

        return redirect()->route('dashboard')
                         ->with('info', "You are now impersonating {$user->name}. <a href='" . route('admin.users.stop-impersonating') . "' class='alert-link'>Stop Impersonating</a>");
    }

    /**
     * Stop impersonating and return to admin account.
     */
    public function stopImpersonating()
    {
        if (!session()->has('impersonator_id')) {
            return redirect()->route('dashboard');
        }

        $impersonatorId = session()->get('impersonator_id');
        $impersonator = User::find($impersonatorId);

        if ($impersonator) {
            Auth::login($impersonator);
            session()->forget('impersonator_id');
        }

        return redirect()->route('admin.users.index')
                         ->with('success', 'You have stopped impersonating.');
    }

    /**
     * Reset user password.
     */
    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.users.show', $user)
                         ->with('success', 'Password reset successfully!');
    }

    /**
     * Bulk actions on users.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,activate,deactivate,change_role',
            'users' => 'required|array',
            'users.*' => 'exists:users,id',
        ]);

        $action = $request->action;
        $userIds = $request->users;
        
        // Prevent including current user in bulk actions
        $userIds = array_diff($userIds, [Auth::id()]);

        DB::beginTransaction();
        
        try {
            switch ($action) {
                case 'delete':
                    // Check for managed projects
                    $usersWithProjects = User::whereIn('id', $userIds)
                                             ->whereHas('managedProjects')
                                             ->count();
                    
                    if ($usersWithProjects > 0) {
                        return redirect()->back()
                                         ->with('error', 'Cannot delete users who are managing projects. Please reassign projects first.');
                    }
                    
                    User::whereIn('id', $userIds)->delete();
                    $message = 'Selected users have been deleted successfully!';
                    break;
                    
                case 'activate':
                    User::whereIn('id', $userIds)->update(['status' => 'active']);
                    $message = 'Selected users have been activated!';
                    break;
                    
                case 'deactivate':
                    User::whereIn('id', $userIds)->update(['status' => 'inactive']);
                    $message = 'Selected users have been deactivated!';
                    break;
                    
                case 'change_role':
                    $request->validate([
                        'new_role' => 'required|in:admin,project_manager,member',
                    ]);
                    
                    User::whereIn('id', $userIds)->update(['role' => $request->new_role]);
                    $message = "Selected users' roles have been changed to " . str_replace('_', ' ', $request->new_role) . '!';
                    break;
            }

            DB::commit();

            return redirect()->route('admin.users.index')
                             ->with('success', $message);
                             
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                             ->with('error', 'Failed to perform bulk action: ' . $e->getMessage());
        }
    }

    /**
     * Export users data.
     */
    public function export($format = 'csv')
    {
        $users = User::with(['managedProjects', 'assignedTasks'])->get();
        
        if ($format == 'json') {
            return response()->json($users);
        }
        
        // CSV Export
        $filename = 'users_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];
        
        $callback = function() use ($users) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, [
                'ID', 'Name', 'Email', 'Role', 'Position', 'Department',
                'Phone', 'Managed Projects', 'Assigned Tasks', 'Status', 'Created At'
            ]);
            
            // Data
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->role,
                    $user->position,
                    $user->department,
                    $user->phone,
                    $user->managedProjects->count(),
                    $user->assignedTasks->count(),
                    $user->status,
                    $user->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get user activities for timeline.
     */
    private function getUserActivities(User $user)
    {
        $activities = [];
        
        // Recent managed projects
        $recentProjects = $user->managedProjects()
                              ->latest()
                              ->take(5)
                              ->get()
                              ->map(function($project) {
                                  return [
                                      'type' => 'project_created',
                                      'title' => 'Created Project: ' . $project->name,
                                      'date' => $project->created_at,
                                      'icon' => 'bi-folder-plus',
                                      'color' => 'primary',
                                  ];
                              });
        
        // Recent completed tasks
        $completedTasks = $user->assignedTasks()
                              ->where('status', 'done')
                              ->latest()
                              ->take(5)
                              ->get()
                              ->map(function($task) {
                                  return [
                                      'type' => 'task_completed',
                                      'title' => 'Completed Task: ' . $task->title,
                                      'date' => $task->updated_at,
                                      'icon' => 'bi-check-circle',
                                      'color' => 'success',
                                  ];
                              });
        
        // Merge activities
        $activities = $recentProjects->merge($completedTasks)
                                     ->sortByDesc('date')
                                     ->take(10)
                                     ->values();
        
        return $activities;
    }

    /**
     * Show user's activity log.
     */
    public function activities(User $user)
    {
        $activities = $this->getUserActivities($user);
        
        return view('admin.users.activities', compact('user', 'activities'));
    }

    /**
     * Generate API token for user.
     */
    public function generateApiToken(Request $request, User $user)
    {
        $request->validate([
            'token_name' => 'required|string|max:255',
        ]);

        $token = $user->createToken($request->token_name)->plainTextToken;

        return redirect()->route('admin.users.show', $user)
                         ->with('success', 'API token created successfully!')
                         ->with('api_token', $token); // Flash token to show once
    }

    /**
     * Revoke API token.
     */
    public function revokeApiToken(Request $request, User $user, $tokenId)
    {
        $user->tokens()->where('id', $tokenId)->delete();

        return redirect()->route('admin.users.show', $user)
                         ->with('success', 'API token revoked successfully!');
    }
}