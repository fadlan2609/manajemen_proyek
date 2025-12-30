<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskLinkHistory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'task_id',
        'user_id',
        'old_link',
        'new_link',
        'notes'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the task that owns the link history.
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Get the user who changed the link.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include recent histories.
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Get the formatted created at date.
     */
    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->format('d M Y, H:i');
    }

    /**
     * Check if this is a link addition (old link null, new link not null).
     */
    public function isLinkAddition(): bool
    {
        return is_null($this->old_link) && !is_null($this->new_link);
    }

    /**
     * Check if this is a link update (both old and new links exist).
     */
    public function isLinkUpdate(): bool
    {
        return !is_null($this->old_link) && !is_null($this->new_link);
    }

    /**
     * Check if this is a link removal (old link not null, new link null).
     */
    public function isLinkRemoval(): bool
    {
        return !is_null($this->old_link) && is_null($this->new_link);
    }

    /**
     * Get the action type.
     */
    public function getActionTypeAttribute(): string
    {
        if ($this->isLinkAddition()) {
            return 'added';
        } elseif ($this->isLinkUpdate()) {
            return 'updated';
        } elseif ($this->isLinkRemoval()) {
            return 'removed';
        }
        
        return 'modified';
    }

    /**
     * Get the action icon.
     */
    public function getActionIconAttribute(): string
    {
        return match($this->action_type) {
            'added' => '➕',
            'updated' => '🔄',
            'removed' => '🗑️',
            default => '📝',
        };
    }
}