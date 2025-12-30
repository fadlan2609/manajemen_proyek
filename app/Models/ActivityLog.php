<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'description',
        'subject_type',
        'subject_id',
        'subject_name',
        'properties',
        'ip_address',
        'user_agent',
        'browser',
        'platform',
        'device',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    protected $appends = [
        'time_ago',
        'formatted_action',
        'icon_class',
    ];

    // RELATIONSHIPS
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    // SCOPES
    public function scopeRecent($query, $limit = 50)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // ATTRIBUTES
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    public function getFormattedActionAttribute(): string
    {
        return ucfirst(str_replace('_', ' ', $this->action));
    }

    public function getIconClassAttribute(): string
    {
        return match($this->action) {
            'created' => 'bi-plus-circle',
            'updated' => 'bi-pencil',
            'deleted' => 'bi-trash',
            'restored' => 'bi-arrow-clockwise',
            'login' => 'bi-box-arrow-in-right',
            'logout' => 'bi-box-arrow-right',
            'completed' => 'bi-check-circle',
            'assigned' => 'bi-person-plus',
            default => 'bi-activity',
        };
    }

    // STATIC METHODS
    public static function log($action, $description = null, $subject = null, $properties = []): self
    {
        $user = Auth::user();
        
        $log = new self([
            'user_id' => $user ? $user->id : null,
            'action' => $action,
            'description' => $description,
            'properties' => $properties,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);

        if ($subject) {
            $log->subject_type = get_class($subject);
            $log->subject_id = $subject->id;
            $log->subject_name = $subject->name ?? $subject->title ?? class_basename($subject);
        }

        $log->save();

        return $log;
    }

    public static function getActivityFeed($userId = null, $limit = 20)
    {
        $query = self::with(['user', 'subject'])->recent($limit);

        if ($userId) {
            $query->byUser($userId);
        }

        return $query->get()->map(function($log) {
            $user = $log->user;
            
            return [
                'id' => $log->id,
                'user' => $user ? $user->name : 'System',
                'user_avatar' => $user ? self::generateAvatarUrl($user) : null,
                'action' => $log->action,
                'formatted_action' => $log->formatted_action,
                'description' => $log->description,
                'subject_type' => $log->subject_type,
                'subject_name' => $log->subject_name,
                'subject_id' => $log->subject_id,
                'time_ago' => $log->time_ago,
                'icon_class' => $log->icon_class,
            ];
        });
    }

    private static function generateAvatarUrl(?User $user): ?string
    {
        if (!$user) return null;
        
        // Gunakan Gravatar sebagai fallback
        $hash = md5(strtolower(trim($user->email)));
        return "https://www.gravatar.com/avatar/{$hash}?s=200&d=identicon";
    }

    public function getActivityDetails(): array
    {
        $user = $this->user;
        
        $details = [
            'id' => $this->id,
            'user' => $user ? [
                'id' => $user->id,
                'name' => $user->name,
                'avatar' => self::generateAvatarUrl($user),
            ] : null,
            'action' => $this->action,
            'formatted_action' => $this->formatted_action,
            'description' => $this->description,
            'subject_type' => $this->subject_type,
            'subject_id' => $this->subject_id,
            'subject_name' => $this->subject_name,
            'time_ago' => $this->time_ago,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];

        return $details;
    }
}