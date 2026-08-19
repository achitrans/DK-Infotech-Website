<?php

namespace App\Services;

use App\Models\GoogleToken;
use App\Models\Meeting;
use Carbon\Carbon;
use Google\Client as GoogleClient;
use Google\Service\Calendar as GoogleCalendar;
use Google\Service\Calendar\ConferenceData;
use Google\Service\Calendar\CreateConferenceRequest;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventDateTime;
use Google\Service\Oauth2;
use Illuminate\Support\Facades\Log;

class GoogleCalendarService
{
    protected GoogleClient $client;

    public function __construct()
    {
        $this->client = new GoogleClient;
        $this->client->setClientId(env('GOOGLE_CLIENT_ID'));
        $this->client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));

        // Allow explicit GOOGLE_REDIRECT_URI from env or fallback to dynamic Laravel route
        $redirectUri = env('GOOGLE_REDIRECT_URI');
        if (empty($redirectUri)) {
            try {
                $redirectUri = route('google.callback');
            } catch (\Exception $e) {
                $redirectUri = 'http://localhost:8002/dashboard/google/callback';
            }
        }
        $this->client->setRedirectUri($redirectUri);

        $this->client->addScope(GoogleCalendar::CALENDAR);
        $this->client->addScope(GoogleCalendar::CALENDAR_EVENTS);
        $this->client->addScope(Oauth2::USERINFO_EMAIL);
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent');
    }

    /**
     * Get Google OAuth Authorization URL.
     */
    public function getAuthUrl(): string
    {
        $authUrl = $this->client->createAuthUrl();

        Log::info('Google OAuth Init Request', [
            'redirect_uri' => $this->client->getRedirectUri(),
            'client_id' => env('GOOGLE_CLIENT_ID'),
            'auth_url' => $authUrl,
        ]);

        return $authUrl;
    }

    /**
     * Handle OAuth Callback Code and store refresh/access token for user.
     */
    public function handleCallback(string $code, int $userId): GoogleToken
    {
        Log::info('Google OAuth Callback Received', [
            'redirect_uri' => $this->client->getRedirectUri(),
            'code' => substr($code, 0, 15).'...',
            'user_id' => $userId,
        ]);

        $tokenData = $this->client->fetchAccessTokenWithAuthCode($code);

        Log::info('Google OAuth Token Response', [
            'has_error' => isset($tokenData['error']),
            'error_details' => $tokenData['error_description'] ?? ($tokenData['error'] ?? null),
            'has_refresh' => isset($tokenData['refresh_token']),
        ]);

        if (isset($tokenData['error'])) {
            throw new \Exception('Google OAuth Error: '.($tokenData['error_description'] ?? $tokenData['error']));
        }

        $this->client->setAccessToken($tokenData);
        $googleEmail = null;
        try {
            $oauth2 = new Oauth2($this->client);
            $googleUser = $oauth2->userinfo->get();
            $googleEmail = $googleUser->getEmail();
        } catch (\Exception $e) {
            Log::warning('Could not fetch Google User Email: '.$e->getMessage());
        }

        $existingToken = GoogleToken::first();
        if ($existingToken && $existingToken->google_email && $googleEmail && $existingToken->google_email !== $googleEmail) {
            Log::info("Google Account switched from {$existingToken->google_email} to {$googleEmail}");
        }

        $refreshToken = $tokenData['refresh_token'] ?? ($existingToken->refresh_token ?? null);

        return GoogleToken::updateOrCreate(
            ['user_id' => $userId],
            [
                'google_email' => $googleEmail,
                'access_token' => json_encode($tokenData),
                'refresh_token' => $refreshToken,
                'expires_in' => $tokenData['expires_in'] ?? 3600,
            ]
        );
    }

    /**
     * Authorize Google Client with stored token for given user or system admin.
     */
    public function authorizeClient(?int $userId = null): bool
    {
        $query = GoogleToken::query();
        if ($userId) {
            $query->where('user_id', $userId);
        }

        $googleTokenRecord = $query->first();

        // Fallback to any available admin token if specific user token is not present
        if (! $googleTokenRecord) {
            $googleTokenRecord = GoogleToken::first();
        }

        if (! $googleTokenRecord) {
            return false;
        }

        $tokenData = json_decode($googleTokenRecord->access_token, true);
        $this->client->setAccessToken($tokenData);

        if ($this->client->isAccessTokenExpired()) {
            if ($googleTokenRecord->refresh_token || isset($tokenData['refresh_token'])) {
                $refreshToken = $googleTokenRecord->refresh_token ?? $tokenData['refresh_token'];
                $newToken = $this->client->fetchAccessTokenWithRefreshToken($refreshToken);

                if (! isset($newToken['error'])) {
                    if (! isset($newToken['refresh_token'])) {
                        $newToken['refresh_token'] = $refreshToken;
                    }
                    $googleTokenRecord->update([
                        'access_token' => json_encode($newToken),
                        'refresh_token' => $newToken['refresh_token'],
                        'expires_in' => $newToken['expires_in'] ?? 3600,
                    ]);
                } else {
                    Log::warning("Google token revoked or invalid ({$newToken['error']}). Deleting token record.");
                    $googleTokenRecord->delete();

                    return false;
                }
            } else {
                $googleTokenRecord->delete();

                return false;
            }
        }

        return true;
    }

    /**
     * Create a Google Calendar event with auto-generated Google Meet video conference link.
     */
    public function createMeetEvent(Meeting $meeting, bool $sendNotifications = true): Meeting
    {
        if (! $this->authorizeClient($meeting->created_by)) {
            Log::warning("Google authorization missing when creating meeting #{$meeting->id}");
            throw new \Exception('Google is not connected. Please connect your Google account first.');
        }

        $service = new GoogleCalendar($this->client);

        $tz = config('app.timezone', 'Asia/Kolkata');
        $startCarbon = Carbon::parse($meeting->start_time)->setTimezone($tz);
        $endCarbon = Carbon::parse($meeting->end_time)->setTimezone($tz);

        $event = new Event([
            'summary' => $meeting->title,
            'description' => $meeting->description ?? 'Scheduled Meeting',
            'start' => new EventDateTime([
                'dateTime' => $startCarbon->toRfc3339String(),
                'timeZone' => $tz,
            ]),
            'end' => new EventDateTime([
                'dateTime' => $endCarbon->toRfc3339String(),
                'timeZone' => $tz,
            ]),
        ]);

        // Build attendees array
        $attendees = $this->getMeetingAttendeeEmails($meeting);
        if (! empty($attendees)) {
            $event->setAttendees($attendees);
        }

        // Request Google Meet Conference Generation
        $conferenceRequest = new CreateConferenceRequest([
            'requestId' => 'meet_'.$meeting->id.'_'.time(),
        ]);
        $conferenceData = new ConferenceData([
            'createRequest' => $conferenceRequest,
        ]);
        $event->setConferenceData($conferenceData);

        try {
            $createdEvent = $service->events->insert('primary', $event, [
                'conferenceDataVersion' => 1,
                'sendUpdates' => $sendNotifications ? 'all' : 'none',
            ]);

            $meetLink = $createdEvent->getHangoutLink();
            $eventId = $createdEvent->getId();

            $meeting->update([
                'google_event_id' => $eventId,
                'meet_link' => $meetLink,
            ]);

            Log::info("Google Meet generated successfully for meeting #{$meeting->id}: {$meetLink} (sendUpdates: ".($sendNotifications ? 'all' : 'none').')');
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            if ($e->getCode() == 401 || str_contains($msg, '401') || str_contains($msg, 'UNAUTHENTICATED') || str_contains($msg, 'Invalid Credentials')) {
                Log::error('HTTP 401 UNAUTHENTICATED error encountered during meeting creation. Purging stale Google tokens.');
                GoogleToken::truncate();
                throw new \Exception('Google Calendar session expired or authentication failed (401). Please connect Google Calendar again.');
            }
            Log::error('Failed to create Google Meet event: '.$msg);
            throw $e;
        }

        return $meeting;
    }

    /**
     * Update an existing Google Calendar event.
     */
    public function updateMeetEvent(Meeting $meeting, bool $sendNotifications = false): bool
    {
        if (! $meeting->google_event_id || ! $this->authorizeClient($meeting->created_by)) {
            return false;
        }

        try {
            $service = new GoogleCalendar($this->client);
            $event = $service->events->get('primary', $meeting->google_event_id);

            $tz = config('app.timezone', 'Asia/Kolkata');
            $startCarbon = Carbon::parse($meeting->start_time)->setTimezone($tz);
            $endCarbon = Carbon::parse($meeting->end_time)->setTimezone($tz);

            $event->setSummary($meeting->title);
            $event->setDescription($meeting->description ?? '');
            $event->setStart(new EventDateTime([
                'dateTime' => $startCarbon->toRfc3339String(),
                'timeZone' => $tz,
            ]));
            $event->setEnd(new EventDateTime([
                'dateTime' => $endCarbon->toRfc3339String(),
                'timeZone' => $tz,
            ]));

            $attendees = $this->getMeetingAttendeeEmails($meeting);
            if (! empty($attendees)) {
                $event->setAttendees($attendees);
            }

            $service->events->update('primary', $meeting->google_event_id, $event, [
                'sendUpdates' => $sendNotifications ? 'all' : 'none',
            ]);

            return true;
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            if ($e->getCode() == 401 || str_contains($msg, '401') || str_contains($msg, 'UNAUTHENTICATED') || str_contains($msg, 'Invalid Credentials')) {
                Log::error('HTTP 401 UNAUTHENTICATED error encountered during meeting update. Purging stale Google tokens.');
                GoogleToken::truncate();
                throw new \Exception('Google Calendar session expired or authentication failed (401). Please connect Google Calendar again.');
            }
            Log::error("Failed to update Google Calendar event #{$meeting->google_event_id}: ".$msg);

            return false;
        }
    }

    /**
     * Get unique list of attendee emails for a meeting.
     * Checks client, creator, attached project (client, user, associate), and additional attendees.
     */
    protected function getMeetingAttendeeEmails(Meeting $meeting): array
    {
        $emails = [];

        if ($meeting->client && ! empty($meeting->client->email)) {
            $emails[] = strtolower(trim($meeting->client->email));
        }

        if ($meeting->creator && ! empty($meeting->creator->email)) {
            $emails[] = strtolower(trim($meeting->creator->email));
        }

        if ($meeting->project) {
            $project = $meeting->project;
            if ($project->client && ! empty($project->client->email)) {
                $emails[] = strtolower(trim($project->client->email));
            }
            if ($project->user && ! empty($project->user->email)) {
                $emails[] = strtolower(trim($project->user->email));
            }
            if ($project->associate && ! empty($project->associate->email)) {
                $emails[] = strtolower(trim($project->associate->email));
            }
        }

        if ($meeting->inquiry && ! empty($meeting->inquiry->email)) {
            $emails[] = strtolower(trim($meeting->inquiry->email));
        }

        if ($meeting->interview && ! empty($meeting->interview->email)) {
            $emails[] = strtolower(trim($meeting->interview->email));
        }

        if (is_array($meeting->attendees)) {
            foreach ($meeting->attendees as $email) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $emails[] = strtolower(trim($email));
                }
            }
        }

        $uniqueEmails = array_values(array_unique(array_filter($emails)));

        return array_map(fn ($email) => ['email' => $email], $uniqueEmails);
    }

    /**
     * Delete a Google Calendar event.
     */
    public function deleteMeetEvent(Meeting $meeting, bool $sendNotifications = true): bool
    {
        if (! $meeting->google_event_id || ! $this->authorizeClient($meeting->created_by)) {
            return false;
        }

        try {
            $service = new GoogleCalendar($this->client);
            $service->events->delete('primary', $meeting->google_event_id, [
                'sendUpdates' => $sendNotifications ? 'all' : 'none',
            ]);

            return true;
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            if ($e->getCode() == 401 || str_contains($msg, '401') || str_contains($msg, 'UNAUTHENTICATED') || str_contains($msg, 'Invalid Credentials')) {
                Log::error('HTTP 401 UNAUTHENTICATED error encountered during meeting delete. Purging stale Google tokens.');
                GoogleToken::truncate();
            }
            Log::error("Failed to delete Google Calendar event #{$meeting->google_event_id}: ".$msg);

            return false;
        }
    }
}
