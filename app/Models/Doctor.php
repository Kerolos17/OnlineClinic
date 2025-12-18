<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $fillable = [
        'user_id',
        'specialization_id',
        'bio',
        'experience_years',
        'languages',
        'consultation_price',
        'image',
        'rating',
        'total_reviews',
        'is_active',
    ];

    protected $casts = [
        'bio' => 'array',
        'languages' => 'array',
        'consultation_price' => 'decimal:2',
        'rating' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function specialization()
    {
        return $this->belongsTo(Specialization::class);
    }

    public function slots()
    {
        return $this->hasMany(Slot::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews()
    {
        return $this->hasMany(Review::class)->where('status', 'approved');
    }

    // Query Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWithRelations($query)
    {
        return $query->with(['user', 'specialization', 'approvedReviews']);
    }

    public function scopeBySpecialization($query, $specializationId)
    {
        return $query->where('specialization_id', $specializationId);
    }

    public function scopeTopRated($query, $limit = 10)
    {
        return $query->orderBy('rating', 'desc')
            ->orderBy('total_reviews', 'desc')
            ->limit($limit);
    }

    public function scopeWithMinRating($query, $minRating = 4.0)
    {
        return $query->where('rating', '>=', $minRating);
    }

    public function scopeByLanguage($query, $language)
    {
        return $query->whereJsonContains('languages', $language);
    }

    public function scopeByPriceRange($query, $min = null, $max = null)
    {
        if ($min !== null) {
            $query->where('consultation_price', '>=', $min);
        }
        if ($max !== null) {
            $query->where('consultation_price', '<=', $max);
        }
        return $query;
    }

    public function scopeWithAvailableSlots($query, $date = null)
    {
        $date = $date ?? now()->format('Y-m-d');

        return $query->whereHas('slots', function ($q) use ($date) {
            $q->where('date', $date)
              ->where('status', 'available');
        });
    }

    public function scopeSearch($query, $search)
    {
        return $query->whereHas('user', function ($q) use ($search) {
            $q->where('name_en', 'like', "%{$search}%")
              ->orWhere('name_ar', 'like', "%{$search}%");
        });
    }

    // Accessors
    public function getAvatarUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return null;
    }

    public function getFullNameAttribute()
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? $this->user->name_ar : $this->user->name_en;
    }
}
