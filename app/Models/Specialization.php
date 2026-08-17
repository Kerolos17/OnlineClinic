<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Specialization extends Model
{
    use HasFactory;

    protected $fillable = ['name_en', 'name_ar', 'description_en', 'description_ar', 'icon', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the specialization name based on locale.
     */
    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();

        return $locale === 'ar' && ! empty($this->name_ar)
            ? $this->name_ar
            : ($this->name_en ?? $this->name_ar ?? '');
    }

    /**
     * Get the specialization description based on locale.
     */
    public function getDescriptionAttribute(): ?string
    {
        $locale = app()->getLocale();

        return $locale === 'ar' && ! empty($this->description_ar)
            ? $this->description_ar
            : ($this->description_en ?? $this->description_ar);
    }

    /** @return HasMany<Doctor, $this> */
    public function doctors(): HasMany
    {
        return $this->hasMany(Doctor::class);
    }

    /** @return HasMany<Doctor, $this> */
    public function activeDoctors(): HasMany
    {
        return $this->hasMany(Doctor::class)->where('is_active', true);
    }

    // Query Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWithDoctorsCount($query)
    {
        return $query->withCount(['activeDoctors as doctors_count']);
    }
}
