<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    use HasFactory;

    // Allowed status choices
    public const STATUS_APPLIED = 'Applied';
    public const STATUS_SHORTLISTED = 'Shortlisted';
    public const STATUS_INTERVIEW = 'Interview';
    public const STATUS_SELECTED = 'Selected';
    public const STATUS_REJECTED = 'Rejected';

    public const STATUSES = [
        self::STATUS_APPLIED,
        self::STATUS_SHORTLISTED,
        self::STATUS_INTERVIEW,
        self::STATUS_SELECTED,
        self::STATUS_REJECTED,
    ];

    // Allowed job type choices
    public const JOB_TYPES = [
        'Full-time',
        'Part-time',
        'Internship',
        'Contract',
    ];

    /**
     * Attributes that are mass assignable.
     * Prevents mass-assignment vulnerabilities.
     */
    protected $fillable = [
        'user_id',
        'company',
        'position',
        'location',
        'job_type',
        'salary',
        'applied_date',
        'follow_up_date',
        'status',
        'job_url',
        'notes',
    ];

    /**
     * Attribute casting.
     * Automatically converts database date strings into Carbon (datetime) instances.
     */
    protected function casts(): array
    {
        return [
            'applied_date' => 'date',
            'follow_up_date' => 'date',
            'salary' => 'decimal:2',
        ];
    }

    /**
     * Get the user that owns this job application.
     * Similar to Django's ForeignKey(User, on_delete=models.CASCADE).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Query scope to fetch pending follow-up reminders.
     * Reminder rule: follow_up_date is today or earlier, and application is not Selected or Rejected.
     */
    public function scopeDueFollowUps(Builder $query): Builder
    {
        return $query->whereNotNull('follow_up_date')
            ->whereDate('follow_up_date', '<=', now()->toDateString())
            ->whereNotIn('status', [self::STATUS_SELECTED, self::STATUS_REJECTED])
            ->orderBy('follow_up_date', 'asc');
    }

    /**
     * Return Bootstrap badge CSS class based on application status.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_APPLIED => 'badge bg-primary',
            self::STATUS_SHORTLISTED => 'badge bg-info text-dark',
            self::STATUS_INTERVIEW => 'badge bg-warning text-dark',
            self::STATUS_SELECTED => 'badge bg-success',
            self::STATUS_REJECTED => 'badge bg-danger',
            default => 'badge bg-secondary',
        };
    }
}
