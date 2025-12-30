<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $role
 * @property string|null $phone
 * @property string|null $position
 * @property string|null $department
 * @property string|null $avatar
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $last_login_at
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Task[] $assignedTasks
 * @property-read int|null $assigned_tasks_count
 * @property-read string $activity_level
 * @property-read string $avatar_thumbnail
 * @property-read string $avatar_url
 * @property-read array $current_workload
 * @property-read string $initials
 * @property-read string|null $last_activity
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Project[] $managedProjects
 * @property-read int|null $managed_projects_count
 * @property-read float $performance_score
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Project[] $projects
 * @property-read int|null $projects_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ProjectMember[] $projectMemberships
 * @property-read int|null $project_memberships_count
 * @property-read string $role_color
 * @property-read string $role_name
 * @property-read string $status_color
 * @property-read string $status_name
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User active()
 * @method static \Illuminate\Database\Eloquent\Builder|User admins()
 * @method static \Illuminate\Database\Eloquent\Builder|User byRole($role)
 * @method static \Illuminate\Database\Eloquent\Builder|User inactive()
 * @method static \Illuminate\Database\Eloquent\Builder|User members()
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User projectManagers()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User search($search)
 * @method static \Illuminate\Database\Eloquent\Builder|User suspended()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDepartment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User withoutTrashed()
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'position',
        'department',
        'avatar',
        'status',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'tokens',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_login_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $appends = [
        'role_name',
        'status_name',
        'role_color',
        'status_color',
        'initials',
        'avatar_url',
        'avatar_thumbnail',
        'performance_score',
        'last_activity',
        'current_workload',
        'activity_level',
    ];

    // ========== RELATIONSHIPS ==========

    /**
     * Get projects managed by this user
     */
    public function managedProjects()
    {
        return $this->hasMany(Project::class, 'project_manager_id');
    }

    /**
     * Get tasks assigned to this user
     */
    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    /**
 * Alias for assignedTasks - for backward compatibility
 */
public function tasks()
{
    return $this->assignedTasks();
}

    /**
     * Get project memberships
     */
    public function projectMemberships()
    {
        return $this->hasMany(ProjectMember::class);
    }

    /**
     * Get projects this user is a member of
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_members')
            ->withTimestamps();
    }

    /**
     * Get comments made by this user
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get notifications for this user
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get activity logs for this user
     */
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Get API tokens
     */
    public function tokens()
    {
        return $this->hasMany(PersonalAccessToken::class, 'tokenable_id');
    }

    // ========== SCOPES ==========

    /**
     * Scope for admin users
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * Scope for project manager users
     */
    public function scopeProjectManagers($query)
    {
        return $query->where('role', 'project_manager');
    }

    /**
     * Scope for member users
     */
    public function scopeMembers($query)
    {
        return $query->where('role', 'member');
    }

    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for inactive users
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope for suspended users
     */
    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }

    /**
     * Scope for users with specific role
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope for searching users
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('position', 'like', "%{$search}%")
              ->orWhere('department', 'like', "%{$search}%");
        });
    }

    /**
     * Scope for recently active users (last 7 days)
     */
    public function scopeRecentlyActive($query)
    {
        return $query->where('last_login_at', '>=', now()->subDays(7));
    }

    // ========== HELPER METHODS ==========

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is project manager
     */
    public function isProjectManager()
    {
        return $this->role === 'project_manager';
    }

    /**
     * Check if user is member
     */
    public function isMember()
    {
        return $this->role === 'member';
    }

    /**
     * Check if user is active
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Check if user is inactive
     */
    public function isInactive()
    {
        return $this->status === 'inactive';
    }

    /**
     * Check if user is suspended
     */
    public function isSuspended()
    {
        return $this->status === 'suspended';
    }

    /**
     * Check if user can manage projects
     */
    public function canManageProjects()
    {
        return $this->isAdmin() || $this->isProjectManager();
    }

    /**
     * Check if user can be deleted (no managed projects)
     */
    public function canBeDeleted()
    {
        return $this->managedProjects()->count() === 0;
    }

    /**
     * Check if user can be restored
     */
    public function canBeRestored()
    {
        // Check if email is still unique
        $existingUser = self::where('email', $this->email)
            ->where('id', '!=', $this->id)
            ->first();
        
        return !$existingUser;
    }

    /**
     * Get user's role name formatted
     */
    public function getRoleNameAttribute()
    {
        return match ($this->role) {
            'admin' => 'Administrator',
            'project_manager' => 'Project Manager',
            'member' => 'Team Member',
            default => 'User',
        };
    }

    /**
     * Get user's status name formatted
     */
    public function getStatusNameAttribute()
    {
        return match ($this->status) {
            'active' => 'Active',
            'inactive' => 'Inactive',
            'suspended' => 'Suspended',
            default => 'Unknown',
        };
    }

    /**
     * Get user's status color for display
     */
    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'active' => 'success',
            'inactive' => 'warning',
            'suspended' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get user's role color for display
     */
    public function getRoleColorAttribute()
    {
        return match ($this->role) {
            'admin' => 'danger',
            'project_manager' => 'warning',
            'member' => 'info',
            default => 'secondary',
        };
    }

    /**
     * Get user's initials for avatar
     */
    public function getInitialsAttribute()
    {
        $names = explode(' ', $this->name);
        $initials = '';

        foreach ($names as $name) {
            $initials .= strtoupper(substr($name, 0, 1));
        }

        return substr($initials, 0, 2);
    }

    /**
     * Get avatar URL
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            return asset('storage/' . $this->avatar);
        }

        // Generate default avatar using UI Avatars
        $params = http_build_query([
            'name' => $this->initials,
            'background' => $this->getDefaultAvatarColor(),
            'color' => 'fff',
            'size' => '128',
            'bold' => 'true',
            'format' => 'png',
        ]);

        return "https://ui-avatars.com/api/?{$params}";
    }

    /**
     * Get avatar thumbnail URL
     */
    public function getAvatarThumbnailAttribute()
    {
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            return asset('storage/' . $this->avatar);
        }

        // Generate smaller default avatar
        $params = http_build_query([
            'name' => $this->initials,
            'background' => $this->getDefaultAvatarColor(),
            'color' => 'fff',
            'size' => '64',
            'bold' => 'true',
        ]);

        return "https://ui-avatars.com/api/?{$params}";
    }

    /**
     * Get default avatar color based on user ID
     */
    private function getDefaultAvatarColor()
    {
        $colors = [
            '0D6EFD', // Blue
            '6610F2', // Indigo
            '6F42C1', // Purple
            'D63384', // Pink
            'DC3545', // Red
            'FD7E14', // Orange
            'FFC107', // Yellow
            '198754', // Green
            '20C997', // Teal
            '0DCAF0', // Cyan
        ];

        $index = $this->id % count($colors);
        return $colors[$index];
    }

    /**
     * Get active projects count
     */
    public function activeProjectsCount()
    {
        if ($this->isAdmin()) {
            return Project::active()->count();
        } elseif ($this->isProjectManager()) {
            return $this->managedProjects()->active()->count();
        } else {
            return $this->projects()->active()->count();
        }
    }

    /**
     * Get overdue tasks count
     */
    public function overdueTasksCount()
    {
        if ($this->isAdmin()) {
            return Task::overdue()->count();
        } elseif ($this->isProjectManager()) {
            $projectIds = $this->managedProjects()->pluck('id');
            return Task::whereIn('project_id', $projectIds)->overdue()->count();
        } else {
            return $this->assignedTasks()->overdue()->count();
        }
    }

    /**
     * Get completed tasks count
     */
    public function completedTasksCount()
    {
        return $this->assignedTasks()->where('status', 'done')->count();
    }

    /**
     * Get user performance score (0-100)
     */
    public function getPerformanceScoreAttribute()
    {
        $totalTasks = $this->assignedTasks()->count();
        
        if ($totalTasks === 0) {
            return 0;
        }

        $completedOnTime = $this->assignedTasks()
            ->where('status', 'done')
            ->where(function($query) {
                $query->whereNull('deadline')
                      ->orWhereColumn('updated_at', '<=', 'deadline');
            })
            ->count();

        return round(($completedOnTime / $totalTasks) * 100, 1);
    }

    /**
     * Get user's last activity
     */
    public function getLastActivityAttribute()
    {
        $lastTask = $this->assignedTasks()->latest('updated_at')->first();
        $lastProject = $this->managedProjects()->latest('updated_at')->first();

        if (!$lastTask && !$lastProject) {
            return null;
        }

        $taskDate = $lastTask ? $lastTask->updated_at : null;
        $projectDate = $lastProject ? $lastProject->updated_at : null;

        if ($taskDate && $projectDate) {
            $latest = $taskDate->greaterThan($projectDate) ? $taskDate : $projectDate;
        } elseif ($taskDate) {
            $latest = $taskDate;
        } else {
            $latest = $projectDate;
        }

        return $latest->diffForHumans();
    }

    /**
     * Get user's activity timeline
     */
    public function getActivityTimeline($limit = 10)
    {
        $activities = [];

        // Recent managed projects
        $recentProjects = $this->managedProjects()
            ->latest()
            ->take(5)
            ->get()
            ->map(function($project) {
                return [
                    'type' => 'project_created',
                    'title' => "Created project: {$project->name}",
                    'description' => "Project with {$project->tasks()->count()} tasks",
                    'date' => $project->created_at,
                    'icon' => 'bi-folder-plus',
                    'color' => 'primary',
                    'link' => route('project-manager.projects.show', $project),
                ];
            });

        // Recent completed tasks
        $completedTasks = $this->assignedTasks()
            ->where('status', 'done')
            ->with('project')
            ->latest('updated_at')
            ->take(5)
            ->get()
            ->map(function($task) {
                return [
                    'type' => 'task_completed',
                    'title' => "Completed task: {$task->title}",
                    'description' => $task->project ? "Project: {$task->project->name}" : 'No project',
                    'date' => $task->updated_at,
                    'icon' => 'bi-check-circle',
                    'color' => 'success',
                    'link' => $task->project ? route('project-manager.projects.show', $task->project) : null,
                ];
            });

        // Merge and sort activities
        $activities = $recentProjects->merge($completedTasks)
            ->sortByDesc('date')
            ->take($limit)
            ->values()
            ->toArray();

        return $activities;
    }

    /**
     * Get user's current workload (tasks in progress)
     */
    public function getCurrentWorkloadAttribute()
    {
        $inProgressTasks = $this->assignedTasks()
            ->where('status', 'in_progress')
            ->with('project')
            ->get();

        $highPriority = $inProgressTasks->where('priority', 'high')->count();
        $mediumPriority = $inProgressTasks->where('priority', 'medium')->count();
        $lowPriority = $inProgressTasks->where('priority', 'low')->count();

        return [
            'total' => $inProgressTasks->count(),
            'high_priority' => $highPriority,
            'medium_priority' => $mediumPriority,
            'low_priority' => $lowPriority,
            'tasks' => $inProgressTasks->take(5),
        ];
    }

    /**
     * Update last login timestamp
     */
    public function updateLastLogin()
    {
        $this->update(['last_login_at' => now()]);
    }

    /**
     * Check if user can be impersonated
     */
    public function canBeImpersonated()
    {
        // Cannot impersonate self or inactive/suspended users
        if ($this->id === Auth::id() || !$this->isActive()) {
            return false;
        }

        // Additional rules if needed
        return true;
    }

    /**
     * Get user's API tokens
     */
    public function getApiTokens()
    {
        return $this->tokens()->get()->map(function($token) {
            return [
                'id' => $token->id,
                'name' => $token->name,
                'last_used' => $token->last_used_at ? $token->last_used_at->diffForHumans() : 'Never',
                'created_at' => $token->created_at->format('Y-m-d H:i'),
            ];
        });
    }

    /**
     * Create API token for user
     */
    public function createApiToken($name)
    {
        return $this->createToken($name)->plainTextToken;
    }

    /**
     * Revoke API token
     */
    public function revokeApiToken($tokenId)
    {
        $token = $this->tokens()->find($tokenId);
        
        if ($token) {
            $token->delete();
            return true;
        }

        return false;
    }

    /**
     * Revoke all API tokens
     */
    public function revokeAllApiTokens()
    {
        $this->tokens()->delete();
    }

    /**
     * Check if user has avatar
     */
    public function hasAvatar()
    {
        return !empty($this->avatar) && Storage::disk('public')->exists($this->avatar);
    }

    /**
     * Delete avatar file
     */
    public function deleteAvatar()
    {
        if ($this->hasAvatar()) {
            Storage::disk('public')->delete($this->avatar);
            $this->avatar = null;
            return $this->save();
        }

        return false;
    }

    /**
     * Get user's full profile data
     */
    public function getProfileData()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'role_name' => $this->role_name,
            'position' => $this->position,
            'department' => $this->department,
            'phone' => $this->phone,
            'status' => $this->status,
            'status_name' => $this->status_name,
            'avatar_url' => $this->avatar_url,
            'avatar_thumbnail' => $this->avatar_thumbnail,
            'initials' => $this->initials,
            'created_at' => $this->created_at->format('Y-m-d H:i'),
            'last_login' => $this->last_login_at ? $this->last_login_at->diffForHumans() : 'Never',
            'performance_score' => $this->performance_score,
            'last_activity' => $this->last_activity,
            'deleted_at' => $this->deleted_at ? $this->deleted_at->format('Y-m-d H:i') : null,
        ];
    }

    /**
     * Get user's statistics
     */
    public function getStatistics()
    {
        return [
            'managed_projects' => $this->managedProjects()->count(),
            'assigned_tasks' => $this->assignedTasks()->count(),
            'completed_tasks' => $this->completedTasksCount(),
            'overdue_tasks' => $this->overdueTasksCount(),
            'active_projects' => $this->activeProjectsCount(),
            'total_projects' => $this->projects()->count(),
            'performance_score' => $this->performance_score,
            'current_workload' => $this->current_workload['total'],
            'api_tokens' => $this->tokens()->count(),
            'comments_count' => $this->comments()->count(),
            'notifications_count' => $this->notifications()->count(),
        ];
    }

    /**
     * Check if user is recently active (within 24 hours)
     */
    public function isRecentlyActive()
    {
        if (!$this->last_login_at) {
            return false;
        }

        return $this->last_login_at->gt(now()->subDay());
    }

    /**
     * Get user's activity level (Low, Medium, High)
     */
    public function getActivityLevelAttribute()
    {
        $taskCount = $this->assignedTasks()->count();
        $projectCount = $this->projects()->count();

        $score = ($taskCount * 0.7) + ($projectCount * 0.3);

        if ($score >= 15) return 'High';
        if ($score >= 5) return 'Medium';
        return 'Low';
    }

    /**
     * Get trashed projects for user
     */
    public function trashedProjects()
    {
        if ($this->isAdmin()) {
            return Project::onlyTrashed()
                ->with(['manager', 'members'])
                ->latest('deleted_at')
                ->paginate(10);
        } else {
            return Project::onlyTrashed()
                ->where('project_manager_id', $this->id)
                ->with(['manager', 'members'])
                ->latest('deleted_at')
                ->paginate(10);
        }
    }

    /**
     * Get trashed tasks for user
     */
    public function trashedTasks()
    {
        return Task::onlyTrashed()
            ->where('assigned_to', $this->id)
            ->with(['project', 'assignee'])
            ->latest('deleted_at')
            ->paginate(10);
    }

    /**
     * Get user's notification preferences
     */
    public function getNotificationPreferences()
    {
        $defaults = [
            'email_notifications' => true,
            'task_assigned' => true,
            'task_updated' => true,
            'project_updated' => true,
            'deadline_reminder' => true,
            'weekly_digest' => true,
        ];

        $preferences = json_decode($this->notification_preferences ?? '{}', true);
        
        return array_merge($defaults, $preferences);
    }

    /**
     * Update notification preferences
     */
    public function updateNotificationPreferences(array $preferences)
    {
        $current = $this->getNotificationPreferences();
        $updated = array_merge($current, $preferences);
        
        $this->notification_preferences = json_encode($updated);
        return $this->save();
    }

    /**
     * Check if user should receive specific notification
     */
    public function shouldReceiveNotification($type)
    {
        $preferences = $this->getNotificationPreferences();
        return $preferences['email_notifications'] && ($preferences[$type] ?? false);
    }

    /**
     * Send notification to user
     */
    public function sendNotification($title, $message, $type = 'info', $link = null)
    {
        return Notification::notifyUser($this->id, $title, $message, $type, [], $link);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsAsRead()
    {
        return Notification::markAllAsRead($this->id);
    }

    /**
     * Get unread notifications count
     */
    public function unreadNotificationsCount()
    {
        return Notification::getUnreadCount($this->id);
    }

    /**
     * Get recent notifications
     */
    public function recentNotifications($limit = 10)
    {
        return Notification::getRecentNotifications($this->id, $limit);
    }

    /**
     * Get user's permissions based on role
     */
    public function getPermissions()
    {
        $basePermissions = [
            'view_dashboard' => true,
            'edit_profile' => true,
            'change_password' => true,
        ];

        $rolePermissions = match($this->role) {
            'admin' => [
                'manage_users' => true,
                'manage_all_projects' => true,
                'manage_all_tasks' => true,
                'view_reports' => true,
                'manage_settings' => true,
                'impersonate_users' => true,
            ],
            'project_manager' => [
                'manage_projects' => true,
                'manage_tasks' => true,
                'assign_tasks' => true,
                'view_reports' => true,
            ],
            'member' => [
                'view_projects' => true,
                'update_task_progress' => true,
                'add_comments' => true,
            ],
            default => [],
        };

        return array_merge($basePermissions, $rolePermissions);
    }

    /**
     * Check if user has specific permission
     */
    public function hasPermission($permission)
    {
        $permissions = $this->getPermissions();
        return $permissions[$permission] ?? false;
    }

    /**
     * Soft delete user with checks
     */
    public function softDelete()
    {
        if ($this->canBeDeleted()) {
            return $this->delete();
        }
        
        return false;
    }

    /**
     * Restore soft deleted user
     */
    public function restoreUser()
    {
        if ($this->canBeRestored()) {
            return $this->restore();
        }
        
        return false;
    }

    /**
     * Force delete user
     */
    public function forceDeleteUser()
    {
        // Delete related records
        $this->assignedTasks()->update(['assigned_to' => null]);
        $this->managedProjects()->update(['project_manager_id' => null]);
        $this->projectMemberships()->delete();
        $this->comments()->delete();
        
        // Delete notifications
        Notification::where('user_id', $this->id)->delete();
        
        // Delete activity logs
        ActivityLog::where('user_id', $this->id)->delete();
        
        $this->tokens()->delete();
        
        // Delete avatar file
        $this->deleteAvatar();
        
        // Force delete user
        return $this->forceDelete();
    }

    /**
     * Get user's activity summary for current month
     */
    public function getMonthlyActivitySummary()
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $completedTasks = $this->assignedTasks()
            ->where('status', 'done')
            ->whereBetween('updated_at', [$startOfMonth, $endOfMonth])
            ->count();

        $createdComments = $this->comments()
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->count();

        $projectActivity = $this->projects()
            ->whereBetween('project_members.created_at', [$startOfMonth, $endOfMonth])
            ->count();

        return [
            'completed_tasks' => $completedTasks,
            'comments' => $createdComments,
            'project_activity' => $projectActivity,
            'login_count' => $this->login_count ?? 0,
        ];
    }

    /**
     * Check if user has 2FA enabled
     */
    public function hasTwoFactorEnabled()
    {
        return !empty($this->two_factor_secret);
    }

    /**
     * Enable 2FA for user
     */
    public function enableTwoFactor($secret)
    {
        $this->two_factor_secret = encrypt($secret);
        $this->two_factor_enabled = true;
        return $this->save();
    }

    /**
     * Disable 2FA for user
     */
    public function disableTwoFactor()
    {
        $this->two_factor_secret = null;
        $this->two_factor_enabled = false;
        return $this->save();
    }

    /**
     * Get user's dashboard widgets configuration
     */
    public function getDashboardWidgets()
    {
        $defaultWidgets = [
            'stats_overview' => ['enabled' => true, 'position' => 1],
            'recent_tasks' => ['enabled' => true, 'position' => 2],
            'upcoming_deadlines' => ['enabled' => true, 'position' => 3],
            'project_progress' => ['enabled' => true, 'position' => 4],
            'activity_feed' => ['enabled' => true, 'position' => 5],
        ];

        $userWidgets = json_decode($this->dashboard_widgets ?? '{}', true);
        
        return array_merge($defaultWidgets, $userWidgets);
    }

    /**
     * Update dashboard widgets configuration
     */
    public function updateDashboardWidgets(array $widgets)
    {
        $this->dashboard_widgets = json_encode($widgets);
        return $this->save();
    }

    /**
     * Record user activity
     */
    public function recordActivity($action, $description = null, $model = null)
    {
        $activity = [
            'user_id' => $this->id,
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ];

        if ($model) {
            $activity['subject_type'] = get_class($model);
            $activity['subject_id'] = $model->id;
            $activity['subject_name'] = $model->name ?? $model->title ?? $model->id;
        }

        // Gunakan fully qualified class name
        return \App\Models\ActivityLog::create($activity);
    }

    /**
     * Get user's recent activities
     */
    public function getRecentActivities($limit = 20)
    {
        return $this->activityLogs()
            ->latest()
            ->limit($limit)
            ->get();
    }
}