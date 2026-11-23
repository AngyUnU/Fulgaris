<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'status',
        'start_date',
        'due_date',
        'leader_id',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
    ];

    public function leader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function agreements(): HasMany
    {
        return $this->hasMany(Agreement::class);
    }

    /**
     * Calcula el progreso ponderado del proyecto en base a sus actividades.
     */
    public function getCalculatedProgressAttribute(): int
    {
        $count = $this->activities()->count();
        if ($count === 0) {
            return 0;
        }

        $avg = $this->activities()->avg('progress');
        return (int) round($avg ?? 0);
    }

    /**
     * Devuelve el conteo de actividades atrasadas.
     */
    public function getOverdueActivitiesCountAttribute(): int
    {
        return $this->activities()
            ->where('status', '!=', 'completada')
            ->whereNotNull('due_date')
            ->where('due_date', '<', now()->toDateString())
            ->count();
    }
}
