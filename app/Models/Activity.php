<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'priority',
        'status',
        'start_date',
        'due_date',
        'completed_at',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
        'completed_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'activity_user')->withPivot('assigned_at')->withTimestamps();
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ActivityComment::class)->latest();
    }

    public function agreements(): HasMany
    {
        return $this->hasMany(Agreement::class);
    }

    public function getIsOverdueAttribute(): bool
    {
        if ($this->status === 'completada' || ! $this->due_date) {
            return false;
        }

        return $this->due_date->isPast();
    }
}
