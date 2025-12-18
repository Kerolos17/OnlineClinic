<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name_en',
        'name_ar',
        'email',
        'password',
        'role',
        'phone',
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function doctor()
    {
        return $this->hasOne(Doctor::class);
    }

    public function isDoctor()
    {
        return $this->role === 'doctor';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isAdmin();
    }

    /**
     * Get the user's display name for Filament.
     */
    public function getFilamentName(): string
    {
        return $this->getDisplayName();
    }

    /**
     * Get the user's display name as a string.
     */
    public function getDisplayName(): string
    {
        $locale = app()->getLocale();

        if ($locale === 'ar' && !empty($this->name_ar)) {
            return $this->name_ar;
        }

        if (!empty($this->name_en)) {
            return $this->name_en;
        }

        if (!empty($this->name_ar)) {
            return $this->name_ar;
        }

        return $this->email ?? 'User';
    }

    /**
     * Accessor for name attribute (for backward compatibility).
     */
    public function getNameAttribute(): string
    {
        return $this->getDisplayName();
    }

    /**
     * Accessor for display_name attribute.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->getDisplayName();
    }

    /**
     * Convert the model to its string representation.
     */
    public function __toString(): string
    {
        return $this->getDisplayName();
    }
}
