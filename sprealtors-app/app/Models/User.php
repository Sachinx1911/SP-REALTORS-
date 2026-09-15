<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';

    public const ROLE_MANAGER = 'manager';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    // 'role' is deliberately not fillable — it must be set explicitly (see
    // AdminUserController and DatabaseSeeder) so a stray mass-assignment can
    // never grant panel access or admin rights.
    protected $fillable = [
        'name',
        'email',
        'password',
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
     * Roles that may log into the admin panel, in order of access level.
     *
     * @return array<string, string>
     */
    public static function roleOptions(): array
    {
        return [
            self::ROLE_ADMIN => 'Admin — full access, including Team & Settings',
            self::ROLE_MANAGER => 'Manager — properties, projects, locations, testimonials & enquiries',
        ];
    }

    public function roleLabel(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_MANAGER => 'Manager',
            default => 'No access',
        };
    }

    /**
     * Full admin: can also manage Team and Settings.
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isManager(): bool
    {
        return $this->role === self::ROLE_MANAGER;
    }

    /**
     * Any role that may sign in and use the admin panel at all.
     */
    public function hasAccess(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_MANAGER], true);
    }
}
