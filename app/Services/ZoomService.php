<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ZoomService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.zoom.base_url', 'https://api.zoom.us/v2');
    }

    /**
     * Get or refresh Zoom access token (Server-to-Server OAuth recommended)
     */
    protected function getAccessToken(): string
    {
        return Cache::remember('zoom_access_token', 3500, function () {
            $response = Http::withHeaders([
                'Authorization' => 'Basic '.base64_encode(
                    config('services.zoom.client_id').':'.config('services.zoom.client_secret')
                ),
            ])->asForm()->post('https://zoom.us/oauth/token', [
                'grant_type' => 'account_credentials',
                'account_id' => config('services.zoom.account_id'),
            ]);

            if ($response->failed()) {
                \Log::error('Failed to get Zoom access token', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new \Exception('Failed to get Zoom access token: '.$response->body());
            }

            return $response->json('access_token');
        });
    }

    /**
     * Create a meeting for a user
     */
    public function createMeeting(string $userId, string $topic, string $startTime, int $duration = 30)
    {
        $token = $this->getAccessToken();

        // Always use 'me' for Server-to-Server OAuth
        $userId = 'me';

        $response = Http::withToken($token)
            ->post($this->baseUrl."/users/{$userId}/meetings", [
                'topic' => $topic,
                'type' => 2, // Scheduled meeting
                'start_time' => $startTime,
                'duration' => $duration,
                'timezone' => 'Africa/Cairo',
                'settings' => [
                    'join_before_host' => true,
                    'approval_type' => 0,
                    'audio' => 'both',
                    'waiting_room' => false,
                ],
            ]);

        if ($response->failed()) {
            \Log::error('Zoom meeting creation failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'topic' => $topic,
                'start_time' => $startTime,
            ]);
            throw new \Exception('Zoom meeting creation failed: '.$response->body());
        }

        // ✅ Log meeting info for debugging
        \Log::info('Zoom meeting created successfully', [
            'user' => $userId,
            'topic' => $topic,
            'response' => $response->json(),
        ]);

        return $response->json();
    }
}
