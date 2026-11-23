<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relación con el rol del usuario.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Actividades asignadas al usuario.
     */
    public function assignedActivities(): BelongsToMany
    {
        return $this->belongsToMany(Activity::class, 'activity_user');
    }

    /**
     * Comprueba si el usuario tiene un rol determinado o alguno de una lista.
     */
    public function hasRole(string|array $roles): bool
    {
        if (! $this->role) {
            return false;
        }

        if (is_array($roles)) {
            return in_array($this->role->name, $roles, true);
        }

        return $this->role->name === $roles;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public static function verifiesAdminPassword(string $password): bool
    {
        return static::query()
            ->whereHas('role', fn ($query) => $query->where('name', 'admin'))
            ->get(['password'])
            ->contains(fn (self $admin) => Hash::check($password, $admin->password));
    }

    public function isLeader(): bool
    {
        return $this->hasRole('lider');
    }

    public function isDeveloper(): bool
    {
        return $this->hasRole('desarrollador');
    }

    public function isQa(): bool
    {
        return $this->hasRole('qa');
    }
}
