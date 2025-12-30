<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'project_manager_id',
        'deadline',
        'status',
        'progress',
        //'budget',
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'progress' => 'decimal:2',
        //'budget' => 'decimal:2',
    ];

    // RELATIONSHIPS
    public function manager()
    {
        return $this->belongsTo(User::class, 'project_manager_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'project_members')
                    ->withTimestamps();
    }

    // SCOPES
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeOnHold($query)
    {
        return $query->where('status', 'on_hold');
    }

    public function scopeOverdue($query)
    {
        return $query->where('deadline', '<', now())
                     ->where('status', '!=', 'completed');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('project_manager_id', $userId)
                     ->orWhereHas('members', function($q) use ($userId) {
                         $q->where('user_id', $userId);
                     });
    }

    // HELPERS
    public function updateProgress(): float
    {
        $totalTasks = $this->tasks()->count();
        
        if ($totalTasks === 0) {
            $this->progress = 0;
        } else {
            $completedTasks = $this->tasks()->where('status', 'done')->count();
            $inProgressTasks = $this->tasks()->where('status', 'in_progress')->count() * 0.5;
            $totalProgress = $completedTasks + $inProgressTasks;
            
            $this->progress = round(($totalProgress / $totalTasks) * 100, 2);
        }
        
        // Auto-update status based on progress
        if ($this->progress >= 100) {
            $this->status = 'completed';
        } elseif ($this->progress > 0 && $this->status == 'active') {
            $this->status = 'active';
        }
        
        $this->save();
        
        return $this->progress;
    }

    public function isMember($userId): bool
    {
        return $this->members()->where('user_id', $userId)->exists();
    }

    public function isProjectManager($userId): bool
    {
        return $this->project_manager_id == $userId;
    }

    public function userHasAccess(User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($this->isProjectManager($user->id)) {
            return true;
        }

        return $this->isMember($user->id);
    }

    /**
     * Get status color for display
     */
    public function getStatusColorAttribute(): string
    {
        if ($this->status == 'active') return 'success';
        if ($this->status == 'completed') return 'info';
        if ($this->status == 'on_hold') return 'warning';
        return 'secondary';
    }

    /**
     * Get status text in readable format
     */
    public function getStatusTextAttribute(): string
    {
        if ($this->status == 'active') return 'Active';
        if ($this->status == 'completed') return 'Completed';
        if ($this->status == 'on_hold') return 'On Hold';
        return ucfirst($this->status);
    }

    /**
     * Check if project is overdue
     */
    public function isOverdue(): bool
    {
        if (!$this->deadline || $this->status == 'completed') {
            return false;
        }
        
        return $this->deadline->isPast();
    }

    /**
     * Get days remaining until deadline
     */
    public function getDaysRemainingAttribute(): ?int
    {
        if (!$this->deadline) {
            return null;
        }

        return now()->diffInDays($this->deadline, false);
    }

    /**
     * Get formatted deadline
     */
    public function getFormattedDeadlineAttribute(): ?string
    {
        return $this->deadline ? $this->deadline->format('d/m/Y') : null;
    }

    /**
     * Get deadline status text
     */
    public function getDeadlineStatusAttribute(): string
    {
        if (!$this->deadline) {
            return 'No deadline';
        }

        if ($this->status == 'completed') {
            return 'Completed';
        }

        $daysRemaining = $this->days_remaining;

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

    /**
     * Get progress color based on percentage
     */
    public function getProgressColorAttribute(): string
    {
        $progress = $this->progress;
        
        if ($progress >= 80) {
            return 'success';
        } elseif ($progress >= 50) {
            return 'info';
        } elseif ($progress >= 20) {
            return 'warning';
        } else {
            return 'danger';
        }
    }

    /**
     * Get task statistics
     */
    public function getTaskStatsAttribute(): array
    {
        return [
            'total' => $this->tasks()->count(),
            'todo' => $this->tasks()->where('status', 'todo')->count(),
            'in_progress' => $this->tasks()->where('status', 'in_progress')->count(),
            'done' => $this->tasks()->where('status', 'done')->count(),
            'overdue' => $this->tasks()->where(function($query) {
                $query->where('deadline', '<', now())
                      ->where('status', '!=', 'done');
            })->count(),
        ];
    }

    /**
     * Calculate completion percentage based on tasks
     */
    public function calculateCompletionPercentage(): float
    {
        $tasks = $this->tasks;
        
        if ($tasks->isEmpty()) {
            return 0;
        }
        
        $totalProgress = $tasks->sum('progress');
        $averageProgress = $totalProgress / $tasks->count();
        
        return round($averageProgress, 2);
    }

    /**
     * Check if project has tasks
     */
    public function hasTasks(): bool
    {
        return $this->tasks()->exists();
    }

    /**
     * Check if project has members
     */
    public function hasMembers(): bool
    {
        return $this->members()->exists();
    }

    /**
     * Get total members count including project manager
     */
    public function getTotalMembersCountAttribute(): int
    {
        return $this->members()->count() + 1;
    }
}