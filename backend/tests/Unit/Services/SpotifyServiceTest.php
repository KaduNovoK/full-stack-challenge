<?php

namespace Tests\Unit\Services;

use Tests\TestCase;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

use App\Services\SpotifyService;
use App\DTO\Spotify\SpotifyTrackDTO;

class SpotifyServiceTest extends TestCase
{
    protected SpotifyService $spotifyService;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('services.spotify.client_id', 'fake_client_id');
        Config::set('services.spotify.client_secret', 'fake_client_secret');

        $this->spotifyService = new SpotifyService();
    }

    public function test_get_access_token_returns_token_and_caches_it()
    {
        Http::fake([
            'https://accounts.spotify.com/api/token' => Http::response([
                'access_token' => 'fake_token',
                'token_type' => 'Bearer',
                'expires_in' => 3600,
            ], 200)
        ]);

        Cache::flush();

        $token = $this->spotifyService->getAccessToken();
        $this->assertEquals('fake_token', $token);

        Http::fake([
            'https://accounts.spotify.com/api/token' => Http::response([
                'access_token' => 'another_token',
                'token_type' => 'Bearer',
                'expires_in' => 3600,
            ], 200)
        ]);

        $cachedToken = $this->spotifyService->getAccessToken();
        $this->assertEquals('fake_token', $cachedToken);
    }

    public function test_get_access_token_throws_exception_on_failure()
    {
        Http::fake([
            'https://accounts.spotify.com/api/token' => Http::response([], 500)
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Erro ao obter token do Spotify');

        $this->spotifyService->getAccessToken();
    }

    public function test_get_track_by_isrc_returns_dto()
    {
        Http::fake([
            'https://accounts.spotify.com/api/token' => Http::response([
                'access_token' => 'fake_token',
                'token_type' => 'Bearer',
                'expires_in' => 3600,
            ], 200),
            'https://api.spotify.com/v1/search*' => Http::response([
                'tracks' => [
                    'items' => [
                        [
                            'id' => 'track123',
                            'name' => 'Track Test',
                            'duration_ms' => 123000,
                            'explicit' => false,
                            'preview_url' => null,
                            'uri' => 'spotify:track:track123',
                            'track_number' => 1,
                            'disc_number' => 1,
                            'is_playable' => true,
                            'popularity' => 50,
                            'album' => [
                                'album_type' => 'album',
                                'id' => 'album123',
                                'name' => 'Album Test',
                                'release_date' => '2020-01-01',
                                'release_date_precision' => 'day',
                                'total_tracks' => 10,
                                'uri' => 'spotify:album:album123',
                                'is_playable' => true,
                                'available_markets' => ['US'],
                                'artists' => [],
                                'images' => [],
                                'external_urls' => ['spotify' => 'https://spotify.com/album/album123'],
                            ],
                            'artists' => [],
                            'external_ids' => ['isrc' => 'ISRC123'],
                            'external_urls' => ['spotify' => 'https://spotify.com/track/track123'],
                        ],
                    ],
                ],
            ], 200),
        ]);

        Cache::flush();

        $result = $this->spotifyService->getTrackByISRC('ISRC123');
        $this->assertInstanceOf(SpotifyTrackDTO::class, $result);
        $this->assertEquals('track123', $result->id);
        $this->assertEquals('Track Test', $result->name);
    }

    public function test_get_track_by_isrc_returns_null_on_failure()
    {
        Http::fake([
            'https://accounts.spotify.com/api/token' => Http::response([
                'access_token' => 'fake_token',
                'token_type' => 'Bearer',
                'expires_in' => 3600,
            ], 200),
            'https://api.spotify.com/v1/search*' => Http::response([], 500),
        ]);

        Cache::flush();

        $result = $this->spotifyService->getTrackByISRC('ISRC123');
        $this->assertNull($result);
    }
}
