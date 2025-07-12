<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

use App\DTO\Spotify\SpotifyTrackDTO;

class SpotifyService
{
    protected string $clientId;
    protected string $clientSecret;

    public function __construct()
    {
        $this->clientId = config('services.spotify.client_id');
        $this->clientSecret = config('services.spotify.client_secret');
    }

    public function getAccessToken(): string
    {
        return Cache::remember('spotify_access_token', 3300, function () {
            $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
                ->asForm()
                ->post('https://accounts.spotify.com/api/token', [
                    'grant_type' => 'client_credentials',
                ])
            ;

            if ($response->failed()) {
                throw new \Exception('Erro ao obter token do Spotify');
            }

            return $response->json()['access_token'];
        });
    }

    public function getTrackByISRC(string $isrc): ?SpotifyTrackDTO
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)
            ->get('https://api.spotify.com/v1/search', [
                'q' => "isrc:$isrc",
                'type' => 'track',
                'limit' => 1,
            ]);

        if ($response->failed()) {
            return null;
        }

        $data = $response->json()['tracks']['items'][0] ?? null;
        if (!$data) {
            return null;
        }

        return new SpotifyTrackDTO($data);
    }
}
