<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read int $id
 * @property-read int $user_id
 * @property-read string $title
 * @property-read string $message
 * @property-read string $type
 * @property-read string|null $link
 * @property-read array|null $data
 * @property-read string|null $action_url
 * @property-read string|null $action_text
 * @property-read string|null $icon
 * @property-read \Illuminate\Support\Carbon|null $read_at
 * @property-read \Illuminate\Support\Carbon $created_at
 * @property-read \Illuminate\Support\Carbon $updated_at
 * @property-read \App\Models\User $user
 * @property-read bool $is_read
 * @property-read string $time_ago
 * @property-read string $icon_class
 * @property-read string $type_color
 * @property-read string $badge_color
 */
class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'link',
        'data',
        'read_at',
        'action_url',
        'action_text',
        'icon',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'data' => 'array',
    ];

    protected $appends = [
        'is_read',
        'time_ago',
    ];

    // RELATIONSHIPS
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // SCOPES
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    public function scopeRecent($query, $limit = 50)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeImportant($query)
    {
        return $query->where('type', 'important');
    }

    public function scopeInfo($query)
    {
        return $query->where('type', 'info');
    }

    public function scopeWarning($query)
    {
        return $query->where('type', 'warning');
    }

    public function scopeSuccess($query)
    {
        return $query->where('type', 'success');
    }

    public function scopeError($query)
    {
        return $query->where('type', 'error');
    }

    // ATTRIBUTES
    public function getIsReadAttribute(): bool
    {
        return !is_null($this->read_at);
    }

    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    public function getIconClassAttribute(): string
    {
        return match($this->type) {
            'important' => 'bi-exclamation-circle',
            'warning' => 'bi-exclamation-triangle',
            'success' => 'bi-check-circle',
            'error' => 'bi-x-circle',
            default => 'bi-info-circle',
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            'important' => 'warning',
            'warning' => 'warning',
            'success' => 'success',
            'error' => 'danger',
            default => 'info',
        };
    }

    public function getBadgeColorAttribute(): string
    {
        return match($this->type) {
            'important' => 'bg-warning',
            'warning' => 'bg-warning text-dark',
            'success' => 'bg-success',
            'error' => 'bg-danger',
            default => 'bg-info',
        };
    }

    // HELPER METHODS
    public function markAsRead(): bool
    {
        return $this->update(['read_at' => now()]);
    }

    public function markAsUnread(): bool
    {
        return $this->update(['read_at' => null]);
    }

    public static function notifyUser($userId, $title, $message, $type = 'info', $data = [], $link = null): self
    {
        return self::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'data' => $data,
            'link' => $link,
            'icon' => match($type) {
                'important' => 'exclamation-circle',
                'warning' => 'exclamation-triangle',
                'success' => 'check-circle',
                'error' => 'x-circle',
                default => 'info-circle',
            },
        ]);
    }

    public static function notifyTaskAssigned($userId, $task): self
    {
        return self::notifyUser(
            $userId,
            'New Task Assigned',
            "You have been assigned to task: {$task->title}",
            'info',
            ['task_id' => $task->id, 'project_id' => $task->project_id],
            route('member.tasks.show', $task)
        );
    }

    public static function notifyTaskUpdated($userId, $task, $changes = []): self
    {
        return self::notifyUser(
            $userId,
            'Task Updated',
            "Task '{$task->title}' has been updated",
            'info',
            ['task_id' => $task->id, 'changes' => $changes],
            route('member.tasks.show', $task)
        );
    }

    public static function notifyTaskCompleted($userId, $task): self
    {
        return self::notifyUser(
            $userId,
            'Task Completed',
            "Task '{$task->title}' has been marked as completed",
            'success',
            ['task_id' => $task->id],
            route('member.tasks.show', $task)
        );
    }

    public static function notifyProjectUpdated($userId, $project): self
    {
        return self::notifyUser(
            $userId,
            'Project Updated',
            "Project '{$project->name}' has been updated",
            'info',
            ['project_id' => $project->id],
            route('project-manager.projects.show', $project)
        );
    }

    public static function notifyDeadlineReminder($userId, $task): self
    {
        return self::notifyUser(
            $userId,
            'Deadline Approaching',
            "Task '{$task->title}' is due on " . ($task->deadline ? $task->deadline->format('M d, Y') : 'N/A'),
            'warning',
            ['task_id' => $task->id, 'deadline' => $task->deadline],
            route('member.tasks.show', $task)
        );
    }

    public static function notifyOverdueTask($userId, $task): self
    {
        return self::notifyUser(
            $userId,
            'Task Overdue',
            "Task '{$task->title}' is overdue!",
            'error',
            ['task_id' => $task->id],
            route('member.tasks.show', $task)
        );
    }

    public function getActionUrlAttribute(): ?string
    {
        if ($this->link) {
            return $this->link;
        }

        $data = $this->data ?? [];

        if (isset($data['task_id'])) {
            return route('member.tasks.show', $data['task_id']);
        }

        if (isset($data['project_id'])) {
            return route('project-manager.projects.show', $data['project_id']);
        }

        return null;
    }

    public function getActionTextAttribute(): string
    {
        $data = $this->data ?? [];

        if (isset($data['task_id'])) {
            return 'View Task';
        }

        if (isset($data['project_id'])) {
            return 'View Project';
        }

        return 'View Details';
    }

    public function getNotificationData(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
            'type_color' => $this->type_color,
            'icon_class' => $this->icon_class,
            'is_read' => $this->is_read,
            'time_ago' => $this->time_ago,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'action_url' => $this->action_url,
            'action_text' => $this->action_text,
            'badge_color' => $this->badge_color,
        ];
    }

    public static function cleanupOldNotifications($days = 30): void
    {
        self::where('created_at', '<', now()->subDays($days))->delete();
    }

    public static function getUnreadCount($userId): int
    {
        return self::where('user_id', $userId)->unread()->count();
    }

    public static function markAllAsRead($userId): int
    {
        return self::where('user_id', $userId)
            ->unread()
            ->update(['read_at' => now()]);
    }

    public static function getRecentNotifications($userId, $limit = 10)
    {
        return self::where('user_id', $userId)
            ->recent($limit)
            ->get()
            ->map(function($notification) {
                /** @var self $notification */
                return $notification->getNotificationData();
            });
    }
}