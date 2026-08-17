<?php

namespace App\Services;

use App\Models\Booking;

/**
 * Handles payment processing for bookings.
 *
 * Currently uses a simulated payment flow (no real gateway). The design keeps
 * the payment logic isolated so a real gateway can be plugged in later without
 * touching the booking flow.
 */
class PaymentService
{
    /**
     * Simulated payment charge.
     *
     * @return array{success: bool, transaction_id: string, provider: string, currency: string, status: string}
     */
    public function charge(Booking $booking): array
    {
        // Simulate a successful gateway charge. Replace this body with a real
        // gateway integration when ready.
        return [
            'success' => true,
            'transaction_id' => 'TXN_'.strtoupper(uniqid()),
            'provider' => 'simulated',
            'currency' => 'EGP',
            'status' => 'completed',
        ];
    }
}
