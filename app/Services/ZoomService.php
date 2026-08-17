<?php
namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
                'Authorization' => 'Basic ' . base64_encode(
                    config('services.zoom.client_id') . ':' . config('services.zoom.client_secret')
                ),
            ])->asForm()->post('https://zoom.us/oauth/token', [
                'grant_type' => 'account_credentials',
                'account_id' => config('services.zoom.account_id'),
            ]);

            if ($response->failed()) {
                Log::error('Failed to get Zoom access token', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                throw new \Exception('Failed to get Zoom access token: ' . $response->body());
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
            ->post($this->baseUrl . "/users/{$userId}/meetings", [
                'topic'      => $topic,
                'type'       => 2, // Scheduled meeting
                'start_time' => $startTime,
                'duration'   => $duration,
                'timezone'   => 'Africa/Cairo',
                'settings'   => [
                    'join_before_host' => true,
                    'approval_type'    => 0,
                    'audio'            => 'both',
                    'waiting_room'     => false,
                ],
            ]);

        if ($response->failed()) {
            Log::error('Zoom meeting creation failed', [
                'status'     => $response->status(),
                'body'       => $response->body(),
                'topic'      => $topic,
                'start_time' => $startTime,
            ]);
            throw new \Exception('Zoom meeting creation failed: ' . $response->body());
        }

        // ✅ Log meeting info for debugging
        Log::info('Zoom meeting created successfully', [
            'user'     => $userId,
            'topic'    => $topic,
            'response' => $response->json(),
        ]);

        return $response->json();
    }

    /**
     * Create a meeting specifically for an appointment slot with doctor settings
     */
    public function createMeetingForAppointment(string $doctorEmail, string $topic, string $startTime, int $duration, $slot = null): array
    {
        $token = $this->getAccessToken();

        // Get doctor-specific settings if available
        $doctorSettings = $this->getDoctorZoomSettings($slot?->doctor_id);

        $meetingData = [
            'topic'      => $topic,
            'type'       => 2, // Scheduled meeting
            'start_time' => $startTime,
            'duration'   => $duration,
            'timezone'   => 'Africa/Cairo',
            'settings'   => array_merge([
                'join_before_host'               => true,
                'approval_type'                  => 0, // Automatically approve
                'audio'                          => 'both',
                'video'                          => true,
                'waiting_room'                   => true, // Enable waiting room for appointments
                'mute_upon_entry'                => false,
                'allow_multiple_devices'         => true,
                'encryption_type'                => 'enhanced_encryption',
                'auto_recording'                 => 'none',
                'registrants_email_notification' => false,
            ], $doctorSettings),
        ];

        // Add appointment-specific settings
        if ($slot) {
            $meetingData['agenda']                        = "Medical consultation appointment";
            $meetingData['settings']['alternative_hosts'] = $doctorEmail;
        }

        $response = Http::withToken($token)
            ->post($this->baseUrl . "/users/me/meetings", $meetingData);

        if ($response->failed()) {
            Log::error('Zoom appointment meeting creation failed', [
                'status'       => $response->status(),
                'body'         => $response->body(),
                'doctor_email' => $doctorEmail,
                'topic'        => $topic,
                'start_time'   => $startTime,
                'slot_id'      => $slot?->id,
            ]);
            throw new \Exception('Zoom appointment meeting creation failed: ' . $response->body());
        }

        $meetingData = $response->json();

        Log::info('Zoom appointment meeting created successfully', [
            'meeting_id'       => $meetingData['id'],
            'doctor_email'     => $doctorEmail,
            'topic'            => $topic,
            'slot_id'          => $slot?->id,
            'settings_applied' => $doctorSettings,
        ]);

        return $meetingData;
    }

    /**
     * Get doctor-specific Zoom settings from cache
     */
    protected function getDoctorZoomSettings(?int $doctorId): array
    {
        if (! $doctorId) {
            return [];
        }

        $cacheKey = 'zoom_settings_doctor_' . $doctorId;
        $settings = \Cache::get($cacheKey, []);

        // Convert UI settings to Zoom API format
        $zoomSettings = [];

        if (isset($settings['waiting_room'])) {
            $zoomSettings['waiting_room'] = $settings['waiting_room'];
        }

        if (isset($settings['password_protection'])) {
            $zoomSettings['password'] = $settings['password_protection'] ? \Str::random(8) : null;
        }

        if (isset($settings['encryption'])) {
            $zoomSettings['encryption_type'] = $settings['encryption'] ? 'enhanced_encryption' : 'e2ee';
        }

        if (isset($settings['approval_type'])) {
            $zoomSettings['approval_type'] = $settings['approval_type'];
        }

        if (isset($settings['auto_recording'])) {
            $zoomSettings['auto_recording'] = $settings['auto_recording'];
        }

        if (isset($settings['video_host'])) {
            $zoomSettings['host_video'] = $settings['video_host'];
        }

        if (isset($settings['video_participants'])) {
            $zoomSettings['participant_video'] = $settings['video_participants'];
        }

        if (isset($settings['mute_upon_entry'])) {
            $zoomSettings['mute_upon_entry'] = $settings['mute_upon_entry'];
        }

        if (isset($settings['join_before_host'])) {
            $zoomSettings['join_before_host'] = $settings['join_before_host'];
        }

        return $zoomSettings;
    }

    /**
     * Update an existing meeting
     */
    public function updateMeeting(string $meetingId, string $topic, string $startTime, int $duration): array
    {
        $token = $this->getAccessToken();

        $updateData = [
            'topic'      => $topic,
            'start_time' => $startTime,
            'duration'   => $duration,
            'timezone'   => 'Africa/Cairo',
        ];

        $response = Http::withToken($token)
            ->patch($this->baseUrl . "/meetings/{$meetingId}", $updateData);

        if ($response->failed()) {
            Log::error('Zoom meeting update failed', [
                'status'     => $response->status(),
                'body'       => $response->body(),
                'meeting_id' => $meetingId,
                'topic'      => $topic,
                'start_time' => $startTime,
            ]);
            throw new \Exception('Zoom meeting update failed: ' . $response->body());
        }

        Log::info('Zoom meeting updated successfully', [
            'meeting_id' => $meetingId,
            'topic'      => $topic,
        ]);

        return $response->json();
    }

    /**
     * Delete a meeting
     */
    public function deleteMeeting(string $meetingId): bool
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)
            ->delete($this->baseUrl . "/meetings/{$meetingId}");

        if ($response->failed()) {
            Log::error('Zoom meeting deletion failed', [
                'status'     => $response->status(),
                'body'       => $response->body(),
                'meeting_id' => $meetingId,
            ]);
            throw new \Exception('Zoom meeting deletion failed: ' . $response->body());
        }

        Log::info('Zoom meeting deleted successfully', [
            'meeting_id' => $meetingId,
        ]);

        return true;
    }

    /**
     * Get meeting details
     */
    public function getMeeting(string $meetingId): array
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)
            ->get($this->baseUrl . "/meetings/{$meetingId}");

        if ($response->failed()) {
            Log::error('Failed to get Zoom meeting details', [
                'status'     => $response->status(),
                'body'       => $response->body(),
                'meeting_id' => $meetingId,
            ]);
            throw new \Exception('Failed to get Zoom meeting details: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Update meeting settings for appointments with enhanced configuration
     */
    public function updateMeetingSettings(string $meetingId, array $settings): array
    {
        $token = $this->getAccessToken();

        // Merge with default appointment settings
        $defaultSettings = [
            'join_before_host'               => true,
            'approval_type'                  => 0,
            'audio'                          => 'both',
            'video'                          => true,
            'waiting_room'                   => true,
            'mute_upon_entry'                => false,
            'allow_multiple_devices'         => true,
            'encryption_type'                => 'enhanced_encryption',
            'auto_recording'                 => 'none',
            'registrants_email_notification' => false,
        ];

        $updateData = [
            'settings' => array_merge($defaultSettings, $settings),
        ];

        $response = Http::withToken($token)
            ->patch($this->baseUrl . "/meetings/{$meetingId}", $updateData);

        if ($response->failed()) {
            Log::error('Zoom meeting settings update failed', [
                'status'     => $response->status(),
                'body'       => $response->body(),
                'meeting_id' => $meetingId,
                'settings'   => $settings,
            ]);
            throw new \Exception('Zoom meeting settings update failed: ' . $response->body());
        }

        Log::info('Zoom meeting settings updated successfully', [
            'meeting_id' => $meetingId,
            'settings'   => $settings,
        ]);

        return $response->json();
    }

    /**
     * Get meeting status and validate if it's active
     */
    public function getMeetingStatus(string $meetingId): array
    {
        try {
            $meeting = $this->getMeeting($meetingId);

            return [
                'exists'     => true,
                'status'     => $meeting['status'] ?? 'unknown',
                'start_time' => $meeting['start_time'] ?? null,
                'join_url'   => $meeting['join_url'] ?? null,
                'start_url'  => $meeting['start_url'] ?? null,
            ];
        } catch (\Exception $e) {
            return [
                'exists' => false,
                'status' => 'not_found',
                'error'  => $e->getMessage(),
            ];
        }
    }

    /**
     * Batch create meetings for multiple slots
     */
    public function createBulkMeetings(array $slots): array
    {
        $results = [];
        $errors  = [];

        foreach ($slots as $slot) {
            try {
                $topic     = "Consultation with Dr. {$slot->doctor->user->name}";
                $startTime = $slot->date->format('Y-m-d') . 'T' . $slot->start_time . ':00';
                $duration  = (int) \Carbon\Carbon::parse($slot->end_time)->diffInMinutes(\Carbon\Carbon::parse($slot->start_time));

                $meeting = $this->createMeetingForAppointment(
                    $slot->doctor->user->email,
                    $topic,
                    $startTime,
                    $duration,
                    $slot
                );

                $results[] = [
                    'slot_id'    => $slot->id,
                    'meeting_id' => $meeting['id'],
                    'join_url'   => $meeting['join_url'],
                    'start_url'  => $meeting['start_url'],
                    'success'    => true,
                ];

            } catch (\Exception $e) {
                $errors[] = [
                    'slot_id' => $slot->id,
                    'error'   => $e->getMessage(),
                    'success' => false,
                ];

                Log::error('Bulk meeting creation failed for slot', [
                    'slot_id' => $slot->id,
                    'error'   => $e->getMessage(),
                ]);
            }
        }

        return [
            'successful'      => $results,
            'failed'          => $errors,
            'total_processed' => count($slots),
            'success_count'   => count($results),
            'error_count'     => count($errors),
        ];
    }
}
