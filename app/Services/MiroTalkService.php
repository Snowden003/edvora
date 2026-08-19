<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MiroTalkService
{
    private string $baseUrl;
    private string $apiSecret;

    public function __construct()
    {
        $this->baseUrl   = rtrim(config('services.mirotalk.url'), '/');
        $this->apiSecret = config('services.mirotalk.api_secret', '');
    }

    /**
     * Generate a unique room name for a course session.
     */
    public function generateRoomName(string $courseSlug): string
    {
        $timestamp = now()->format('Ymd-Hi');
        $random    = Str::random(6);
        return 'edvora-' . Str::slug($courseSlug . '-' . $random . '-' . $timestamp);
    }

    /**
     * Get the base room URL (without token), used for storage/display.
     */
    public function getRoomUrl(string $room): string
    {
        return $this->baseUrl . '/join?room=' . urlencode($room);
    }

    /**
     * Build a join URL via API (supports redirect after leave).
     * Falls back to direct URL if API fails.
     *
     * @param  string  $room        Room name
     * @param  string  $username    Display name shown in the meeting
     * @param  bool    $presenter   true = teacher, false = student
     * @param  string|null $redirectUrl  URL to redirect after leaving the meeting
     * @return string  Full join URL
     */
    public function buildJoinUrl(string $room, string $username, bool $presenter = false, ?string $redirectUrl = null): string
    {
        $payload = [
            'room'         => $room,
            'roomPassword' => false,
            'name'         => $username,
            'audio'        => true,
            'video'        => true,
            'screen'       => true,
            'hide'         => false,
            'notify'       => false,
            'duration'     => 'unlimited',
        ];

        if ($redirectUrl) {
            $payload['redirect'] = $redirectUrl;
        }

        try {
            $response = Http::timeout(8)->withHeaders([
                'authorization' => $this->apiSecret,
                'Content-Type'  => 'application/json',
            ])->post($this->baseUrl . '/api/v1/join', $payload);

            if ($response->successful() && isset($response->json()['join'])) {
                return $response->json()['join'];
            }

            Log::warning('MiroTalk API failed, falling back to direct URL', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
        } catch (\Exception $e) {
            Log::warning('MiroTalk API exception, falling back to direct URL', [
                'error' => $e->getMessage(),
            ]);
        }

        $params = [
            'room'        => $room,
            'name'        => $username,
            'notify'      => '0',
            'isPresenter' => $presenter ? 'true' : 'false',
            'audio'       => 'true',
            'video'       => 'true',
            'screen'      => 'true',
        ];

        return $this->baseUrl . '/join?' . http_build_query($params);
    }
}
