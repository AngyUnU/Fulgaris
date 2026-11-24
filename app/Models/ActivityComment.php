<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\UploadedFile;
use RuntimeException;

class ActivityComment extends Model
{
    use HasFactory;

    protected $hidden = [
        'progress_reported',
    ];

    public static function createWithEvidence(array $attributes, ?UploadedFile $evidence = null): self
    {
        if ($evidence) {
            $path = $evidence->store('activity-evidence', 'local');
            if (! is_string($path)) {
                throw new RuntimeException('No se pudo guardar la evidencia de la actividad.');
            }

            $attributes['evidence_path'] = $path;
            $attributes['evidence_name'] = basename(str_replace('\\', '/', $evidence->getClientOriginalName()));
        }

        return self::create($attributes);
    }

    protected $fillable = [
        'activity_id',
        'user_id',
        'comment',
        'evidence_path',
        'evidence_name',
        'source',
    ];

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
