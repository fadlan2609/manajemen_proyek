<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'link',
        'assigned_to',
        'status',
        'priority',
        'deadline',
        'progress',
        'completed_at', // Tambahkan ini
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'completed_at' => 'datetime',
        'progress' => 'integer',
    ];

    protected $appends = [
        'status_text',
        'status_color',
        'priority_text',
        'priority_color',
        'progress_color',
        'is_overdue',
        'days_remaining',
        'deadline_status',
        'assignee_name',
        'assignee_email',
        'formatted_deadline',
        'formatted_link',
        'link_domain',
        'link_type',
        'has_link', // Tambahkan ini
    ];

    // RELATIONSHIPS
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function linkHistories(): HasMany
    {
        return $this->hasMany(TaskLinkHistory::class);
    }

    // ATTRIBUTES (Accessors)
    /**
     * Get formatted deadline
     */
    public function getFormattedDeadlineAttribute(): ?string
    {
        if (!$this->deadline) {
            return null;
        }
        
        try {
            return Carbon::parse($this->deadline)->format('M d, Y H:i');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get status in readable format
     */
    public function getStatusTextAttribute(): string
    {
        $status = $this->status ?? 'todo';
        return match ($status) {
            'pending' => 'Pending',
            'in_progress' => 'In Progress',
            'done' => 'Completed',
            'todo' => 'To Do',
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    }

    /**
     * Get priority in readable format
     */
    public function getPriorityTextAttribute(): string
    {
        return ucfirst($this->priority ?? 'medium');
    }

    /**
     * Get status color for display
     */
    public function getStatusColorAttribute(): string
    {
        $status = $this->status ?? 'todo';
        return match ($status) {
            'pending', 'todo' => 'secondary',
            'in_progress' => 'primary',
            'done' => 'success',
            default => 'secondary',
        };
    }

    /**
     * Get priority color for display
     */
    public function getPriorityColorAttribute(): string
    {
        $priority = $this->priority ?? 'medium';
        return match ($priority) {
            'high' => 'danger',
            'medium' => 'warning',
            'low' => 'success',
            default => 'secondary',
        };
    }

    /**
     * Get progress color based on percentage
     */
    public function getProgressColorAttribute(): string
    {
        $progress = $this->progress ?? 0;
        return match (true) {
            $progress >= 80 => 'success',
            $progress >= 50 => 'primary',
            $progress >= 20 => 'warning',
            default => 'danger',
        };
    }

    /**
     * Check if task is overdue
     */
    public function getIsOverdueAttribute(): bool
    {
        if (!$this->deadline) {
            return false;
        }
        
        try {
            $deadline = Carbon::parse($this->deadline);
            return $deadline < now() && $this->status !== 'done';
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get assignee name or fallback text
     */
    public function getAssigneeNameAttribute(): string
    {
        if (!$this->assigned_to) {
            return 'Unassigned';
        }

        return $this->assignee?->name ?? 'Unknown';
    }

    /**
     * Get assignee email
     */
    public function getAssigneeEmailAttribute(): ?string
    {
        return $this->assignee?->email;
    }

    /**
     * Calculate days remaining until deadline
     */
    public function getDaysRemainingAttribute(): ?int
    {
        if (!$this->deadline) {
            return null;
        }

        try {
            $deadline = Carbon::parse($this->deadline);
            return now()->diffInDays($deadline, false);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get deadline status text
     */
    public function getDeadlineStatusAttribute(): string
    {
        if (!$this->deadline) {
            return 'No deadline';
        }

        if ($this->status === 'done') {
            return 'Completed';
        }

        $daysRemaining = $this->days_remaining;

        if ($daysRemaining === null) {
            return 'No deadline';
        }

        if ($daysRemaining < 0) {
            return 'Overdue';
        } elseif ($daysRemaining === 0) {
            return 'Due today';
        } elseif ($daysRemaining <= 3) {
            return 'Due soon';
        } else {
            return 'On track';
        }
    }

    // LINK-RELATED METHODS
    /**
     * Get formatted link with protocol
     */
    public function getFormattedLinkAttribute(): ?string
    {
        if (!$this->link) {
            return null;
        }

        // Ensure link has protocol
        $link = $this->link;
        if (!preg_match("~^(?:f|ht)tps?://~i", $link)) {
            return 'https://' . $link;
        }

        return $link;
    }

    /**
     * Get domain from link
     */
    public function getLinkDomainAttribute(): ?string
    {
        if (!$this->link) {
            return null;
        }

        try {
            $parsed = parse_url($this->formatted_link);
            return $parsed['host'] ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get link type/icon
     */
    public function getLinkTypeAttribute(): string
    {
        if (!$this->link) {
            return 'none';
        }

        $link = strtolower($this->link);
        if (str_contains($link, 'github.com')) {
            return 'github';
        } elseif (str_contains($link, 'figma.com')) {
            return 'figma';
        } elseif (str_contains($link, 'docs.google.com')) {
            return 'google-docs';
        } elseif (str_contains($link, 'drive.google.com')) {
            return 'google-drive';
        } elseif (str_contains($link, 'notion.so')) {
            return 'notion';
        } elseif (str_contains($link, 'trello.com')) {
            return 'trello';
        } elseif (str_contains($link, 'youtube.com')) {
            return 'youtube';
        } elseif (str_contains($link, 'vimeo.com')) {
            return 'vimeo';
        } elseif (str_contains($link, 'loom.com')) {
            return 'loom';
        } else {
            return 'link';
        }
    }

    /**
     * Check if task has link
     */
    public function getHasLinkAttribute(): bool
    {
        return !empty($this->link);
    }

    /**
     * Validate link URL
     */
    public static function isValidUrl(string $url): bool
    {
        if (empty($url)) {
            return false;
        }

        // Basic URL validation
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    // SCOPES
    public function scopeTodo($query)
    {
        return $query->whereIn('status', ['todo', 'pending']);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeDone($query)
    {
        return $query->where('status', 'done');
    }

    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }

    public function scopeMediumPriority($query)
    {
        return $query->where('priority', 'medium');
    }

    public function scopeLowPriority($query)
    {
        return $query->where('priority', 'low');
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeUnassigned($query)
    {
        return $query->whereNull('assigned_to');
    }

    public function scopeOverdue($query)
    {
        return $query->where('deadline', '<', now())
            ->where('status', '!=', 'done');
    }

    public function scopeDueThisWeek($query)
    {
        return $query->whereBetween('deadline', [now(), now()->addWeek()])
            ->where('status', '!=', 'done');
    }

    // Scope untuk tasks dengan link
    public function scopeWithLink($query)
    {
        return $query->whereNotNull('link')->where('link', '!=', '');
    }

    public function scopeWithoutLink($query)
    {
        return $query->whereNull('link')->orWhere('link', '');
    }

    // Filter Scope untuk menerima multiple filters
    public function scopeFilter($query, array $filters = [])
    {
        return $query
            ->when(isset($filters['status']) && !empty($filters['status']), function ($q) use ($filters) {
                $q->where('status', $filters['status']);
            })
            ->when(isset($filters['priority']) && !empty($filters['priority']), function ($q) use ($filters) {
                $q->where('priority', $filters['priority']);
            })
            ->when(isset($filters['assigned_to']) && !empty($filters['assigned_to']), function ($q) use ($filters) {
                $q->where('assigned_to', $filters['assigned_to']);
            })
            ->when(isset($filters['project_id']) && !empty($filters['project_id']), function ($q) use ($filters) {
                $q->where('project_id', $filters['project_id']);
            })
            ->when(isset($filters['search']) && !empty($filters['search']), function ($q) use ($filters) {
                $q->where(function ($query) use ($filters) {
                    $query->where('title', 'like', '%' . $filters['search'] . '%')
                        ->orWhere('description', 'like', '%' . $filters['search'] . '%')
                        ->orWhere('link', 'like', '%' . $filters['search'] . '%');
                });
            })
            ->when(isset($filters['has_link']) && $filters['has_link'], function ($q) {
                $q->withLink();
            })
            ->when(isset($filters['only_overdue']) && $filters['only_overdue'], function ($q) {
                $q->overdue();
            });
    }

    // New scope untuk advanced filtering
    public function scopeAdvancedFilter($query, $request)
    {
        // Filter by status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->has('priority') && $request->priority != 'all') {
            $query->where('priority', $request->priority);
        }

        // Filter by has link
        if ($request->has('has_link') && $request->has_link) {
            $query->withLink();
        }

        // Search
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%')
                    ->orWhere('link', 'like', '%' . $request->search . '%');
            });
        }

        // Sort
        if ($request->has('sort_by')) {
            $direction = $request->get('sort_direction', 'asc');
            $allowedSorts = ['title', 'status', 'priority', 'deadline', 'progress', 'created_at'];
            
            if (in_array($request->sort_by, $allowedSorts)) {
                $query->orderBy($request->sort_by, $direction);
            }
        }

        return $query;
    }

    // HELPERS
    public function isOverdue(): bool
    {
        return $this->getIsOverdueAttribute();
    }

    public function isAssigned(): bool
    {
        return !is_null($this->assigned_to);
    }

    public function isAssignedTo(User $user): bool
    {
        return $this->assigned_to === $user->id;
    }

    public function isCompleted(): bool
    {
        return $this->status === 'done';
    }

    /**
     * Update task progress and status if needed
     */
    public function updateProgress(int $progress): self
    {
        $this->progress = $progress;

        // Auto-update status based on progress
        if ($progress >= 100) {
            $this->status = 'done';
            $this->completed_at = now();
        } elseif ($progress > 0) {
            $this->status = 'in_progress';
        } else {
            $this->status = 'todo';
        }

        $this->save();

        // Update project progress
        if ($this->project && method_exists($this->project, 'updateProgress')) {
            $this->project->updateProgress();
        }

        return $this;
    }

    /**
     * Mark task as completed
     */
    public function markAsCompleted(): self
    {
        $this->update([
            'status' => 'done',
            'progress' => 100,
            'completed_at' => now(),
        ]);

        if ($this->project && method_exists($this->project, 'updateProgress')) {
            $this->project->updateProgress();
        }

        return $this;
    }

    /**
     * Add comment to task
     */
    public function addComment(string $content, User $user): Comment
    {
        return $this->comments()->create([
            'user_id' => $user->id,
            'content' => $content,
        ]);
    }

    /**
     * Add link history
     */
    public function addLinkHistory(string $oldLink, string $newLink, User $user, ?string $notes = null): TaskLinkHistory
    {
        return $this->linkHistories()->create([
            'user_id' => $user->id,
            'old_link' => $oldLink,
            'new_link' => $newLink,
            'notes' => $notes,
        ]);
    }

    // New methods untuk member-specific queries
    public function scopeForMember($query, User $user)
    {
        return $query->where('assigned_to', $user->id);
    }

    // STATIC METHODS
    /**
     * Get task statistics for a user
     */
    public static function getStatisticsForUser(User $user): array
    {
        $query = self::where('assigned_to', $user->id);

        return [
            'total' => $query->count(),
            'todo' => $query->clone()->todo()->count(),
            'in_progress' => $query->clone()->inProgress()->count(),
            'done' => $query->clone()->done()->count(),
            'overdue' => $query->clone()->overdue()->count(),
            'high_priority' => $query->clone()->highPriority()->count(),
            'with_link' => $query->clone()->withLink()->count(),
        ];
    }

    /**
     * Get upcoming deadlines for a user
     */
    public static function getUpcomingDeadlines(User $user, int $limit = 5)
    {
        return self::where('assigned_to', $user->id)
            ->where('status', '!=', 'done')
            ->where('deadline', '>=', now())
            ->orderBy('deadline', 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get overdue tasks for a user
     */
    public static function getOverdueTasks(User $user)
    {
        return self::where('assigned_to', $user->id)
            ->overdue()
            ->orderBy('deadline', 'asc')
            ->get();
    }

    /**
     * Get tasks with links for a user
     */
    public static function getTasksWithLinks(User $user, int $limit = 10)
    {
        return self::where('assigned_to', $user->id)
            ->withLink()
            ->latest()
            ->limit($limit)
            ->get();
    }

    // VALIDATION RULES
    /**
     * Get validation rules for task
     */
    public static function rules($id = null): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'link' => 'nullable|url|max:500',
            'assigned_to' => 'required|exists:users,id',
            'priority' => 'required|in:high,medium,low',
            'deadline' => 'nullable|date',
            'progress' => 'nullable|integer|min:0|max:100',
            'status' => 'required|in:todo,pending,in_progress,done',
        ];
    }

    /**
     * Get custom validation messages
     */
    public static function messages(): array
    {
        return [
            'link.url' => 'Please enter a valid URL (e.g., https://example.com)',
            'link.max' => 'Link must not exceed 500 characters',
            'assigned_to.exists' => 'Selected user does not exist',
            'priority.in' => 'Priority must be high, medium, or low',
            'progress.min' => 'Progress cannot be less than 0%',
            'progress.max' => 'Progress cannot exceed 100%',
            'status.in' => 'Status must be todo, pending, in progress, or done',
        ];
    }

    /**
     * Get validation attributes
     */
    public static function attributes(): array
    {
        return [
            'title' => 'task title',
            'description' => 'description',
            'link' => 'link',
            'assigned_to' => 'assigned user',
            'priority' => 'priority',
            'deadline' => 'deadline',
            'progress' => 'progress',
            'status' => 'status',
        ];
    }
}