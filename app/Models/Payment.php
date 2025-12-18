<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'booking_id',
        'payment_provider',
        'transaction_id',
        'amount',
        'currency',
        'status',
        'payment_details',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_details' => 'array',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
